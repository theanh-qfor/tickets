<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Models\TicketModel;
use App\Models\TicketFilesModel;
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
        $tickets=TicketModel::orderBy($sort,$order);
        if(Auth::user()->role!='engineer') {
            $tickets = $tickets->where('user_id', Auth::user()->id);
        }else{

        }
        if($status=$request->query('status')){
            $tickets->where('status',$status);
        }

        if($importance=$request->query('importance')){
            $tickets->where('importance',$importance);
        }
        if($search) {
            $tickets->orwhere('subject','LIKE','%'.$search.'%');
            $tickets->orwhere('description','LIKE','%'.$search.'%');
            $tickets->orwhere('status','LIKE',$search.'%');
            $tickets->orwhere('importance','LIKE',$search.'%');
            $tickets->orwhere('created_at','LIKE','%'.$search.'%');
        }
        $total=$tickets->count();
        $items=$tickets->skip($offset)->take($limit)->get();
        foreach ($items as $i){
            $i->assigned_name=$i->user_name='';
            if($i->user_id) {
                $user=User::find($i->user_id);
                $i->user_name=$user->exists()?$user->name:'';
            }
            if($i->assigned_to){
                $user=User::find($i->assigned_to);
                $i->assigned_name=$user->exists()?$user->name:'';
            }
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
        $ticket->save();

        $file_ids = Input::get("qty");
        if (!empty($file_ids)){
            foreach ($file_ids as $key => $value) {
                $file_object = TicketFilesModel::find($value);
                $file_object->ticket_id = $ticket->id;
                $file_object->save();
            }
        }

        return Redirect::back();

    }
    public function upload()
    {
        $file = Input::file('file');

        if($file) {
            $destinationPath = public_path().'/uploads/';
            $filename = $file->getClientOriginalName().'.'.$file->getClientOriginalExtension();

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
}
