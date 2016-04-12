<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Models\TicketModel;
use App\Models\TicketFilesModel;
use App\Models\TicketCommentsModel;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Input, Redirect;


class TicketsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(\Illuminate\Foundation\Application $app)
    {
        $this->middleware('auth');
    }

    public function getTickets(Request $request){
        $limit=$request->query('limit',10);
        $offset=$request->query('offset',0);
        $search=$request->query('search');
        $sort=$request->query('sort');
        $order=$request->query('order');
        if(!$sort){
            $sort='id';
            $order='desc';
        }
        $model=new TicketModel();
        $tickets=$model->newQuery();

        $user_ids=array();
        if(Auth::user()->role!='engineer') {
            $tickets->select(['id','subject','description','status','importance','created_at']);
            $tickets = $tickets->where('user_id', Auth::user()->id);
        }else{//if engineer
            $tickets->select('tickets.*','u.name as user_name','u.email as user_email','a.name as assigned_name','a.email as assigned_email');
            $tickets->join('users as u','u.id','=','tickets.user_id');
            $tickets->leftjoin('users as a','a.id','=','tickets.assigned_to');
            if($search){
                if(strpos($search,'@')===false) {
                    $users=User::where('name', 'like', '%' . $search . '%');
                }else{
                    $users=User::where('email','like','%'.$search.'%');
                }
                $user_ids=$users->select('id')->get();
                $user_ids=array_map(function($u){return $u->id;},$user_ids->all());

            }

        }
        if($status=$request->query('status')){
            $tickets->where('status',$status);
        }

        if($importance=$request->query('importance')){
            $tickets->where('importance',$importance);
        }
        if($search) {
            $tickets->where(function($q) use ($search,$user_ids){
                $q->orwhere('tickets.subject','LIKE','%'.$search.'%');
                $q->orwhere('tickets.description','LIKE','%'.$search.'%');
                $q->orwhere('tickets.status','LIKE',$search.'%');
                $q->orwhere('tickets.importance','LIKE',$search.'%');
                $q->orwhere('tickets.created_at','LIKE','%'.$search.'%');
                if($user_ids){
                    $q->orwherein('user_id',$user_ids);
                    $q->orwherein('assigned_to',$user_ids);
                }
            });

        }
        $total=$tickets->count();
        $tickets=$tickets->orderBy($sort,$order);
        $items=$tickets->skip($offset)->take($limit)->get();

        foreach ($items as $i){

        }
        $results=[
            'total'=>$total,
            'rows'=>$items,
        ];
        //$this->createSampleTickets();
        return response()->json($results);
    }
    function deleteTickets(Request $request){
        $tickets=$request->query('tickets',array());
        if(Auth::user()->role=='engineer'){
            TicketModel::destroy($tickets);
        }else {
            foreach ((array)$tickets as $ticket_id) {
                $ticket = TicketModel::find($ticket_id);
                if ($ticket->user_id == Auth::user()->id) {
                    $ticket->delete();
                }
            }
        }

    }
    function assignTicket(){
        if(Auth::user()->role!='engineer'){
            response()->json(['success'=>0]);
        }
        $ticket_id=request('ticket');
        $user_id=request('user');
        $user=User::find($user_id);
        $ticket=TicketModel::find($ticket_id);
        if($user&&$ticket&&$user->role=='engineer') {
            $ticket->assigned_to = $user->id;
            $ticket->save();
            response()->json(['success'=>1]);
        }
        response()->json(['success'=>0]);
    }
    function createSampleTickets($user_id=1,$num=100){
        for($i=0;$i<$num;$i++) {
            $data = array(
                'subject' => 'Ticket  ' . rand(),
                'description' => 'Ticket description ' . rand(),
                'status' => TicketModel::$allStatus[array_rand(TicketModel::$allStatus)],
                'importance' => TicketModel::$allImportances[array_rand(TicketModel::$allImportances)],
                'assigned_to' => 0,
            );
            (new TicketModel($data))->save();
        }
    }
    public function addTicket()
    {
        $id = Input::get('id');
        if (empty($id)){
            $ticket = new TicketModel();
        }
        else{
            $ticket = TicketModel::find($id);
        }
        $ticket->subject = Input::get('subject');
        $ticket->description = Input::get('description');
        $ticket->importance = Input::get('importance');
        $ticket->status = Input::get('status');
        $ticket->user_id = Auth::user()->id;
        $ticket->save();

        $file_ids = Input::get("qty");
        if (!empty($file_ids)){
            foreach ($file_ids as $key => $value) {
                $file_object = TicketFilesModel::find($value);
                $file_object->ticket_id = $ticket->id;
                $file_object->save();
            }
        }
        $comment_ids = Input::get("comments");
        if (!empty($comment_ids)){
            foreach ($comment_ids as $key => $value) {
                $comment_object = TicketCommentsModel::find($value);
                $comment_object->ticket_id = $ticket->id;
                $comment_object->save();
            }
        }

        return Redirect::back();

    }
    public function upload()
    {
        $file = Input::file('file');

        if($file) {
            $destinationPath = public_path().'/uploads/';
            $filename = $file->getClientOriginalName();

            $upload_success = $file->move($destinationPath, $filename);
            $ticket_files = new TicketFilesModel();
            $ticket_files->file_path = $destinationPath;
            $ticket_files->file_name = $filename;
            $ticket_files->save();
        };
        return response()->json(array(
            'success' => true,
            'id'   => $ticket_files->id
        ));
    }
    public function post_comment(){
        $comment = Input::get('comment');
        $user_id = Auth::user()->id;
        $ticket_comment = new TicketCommentsModel();
        $ticket_comment->comment = $comment;
        $ticket_comment->user_id = $user_id;
        $ticket_comment->save();
        return response()->json(array(
            'success' => true,
            'id'   => $ticket_comment->id
        ));
    }
    public function get_file_and_comment(){
        $id = Input::get('id');
        $ticket_file_count = TicketFilesModel::where("ticket_id", $id)->count();
        $comment_count = TicketCommentsModel::where("ticket_id", $id)->count();
        $files = array();
        $comments = array();
        if ($ticket_file_count > 0){
            $ticket_file_list = TicketFilesModel::where("ticket_id", $id)->get();
            foreach($ticket_file_list as $file){
                $file_item = array(
                    'id'        => $file->id,
                    'file_name' => $file->file_name,
                    'file_path' => $file->file_path
                );
                $files[] = $file_item;
            }
        }
        if ($comment_count > 0){
            $comment_list = TicketCommentsModel::where("ticket_id", $id)->orderBy('created_at', 'desc')->get();
            foreach($comment_list as $comment){
                $user = User::find($comment->user_id);
                $comment_item = array(
                    'id'            => $comment->id,
                    'comment'       => $comment->comment,
                    'created_at'    => $comment->created_at,
                    'username'      => $user->name
                );
                $comments[] = $comment_item;
            }
        }
        return response()->json(array(
            'files'     => $files,
            'comments'  => $comments
        ));
    }
}
