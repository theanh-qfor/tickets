<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Models\TicketModel;
use App\Models\TicketFilesModel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
        $all=TicketModel::all();
        $total=TicketModel::count();
        $results=[
            'total'=>$total,
            'rows'=>$all,
        ];
        //$this->createSampleTickets();
        return response()->json($results);
    }
    function createSampleTickets($user_id=1,$num=100){
        for($i=0;$i<$num;$i++) {
            $data = array(
                'subject' => 'Ticket  ' . rand(),
                'description' => 'Ticket description ' . rand(),
                'status' => TicketModel::$allStatus[array_rand(TicketModel::$allStatus)],
                'importance' => TicketModel::$allStatus[array_rand(TicketModel::$allImportances)],
                'assigned_to' => 0,
            );
            (new TicketModel($data))->save();
        }
    }
    public function addTicket()
    {
        $ticket = new TicketModel();

        $ticket->subject = Input::get('subject');
        $ticket->description = Input::get('description');
        $ticket->importance = Input::get('importance');
        $ticket->status = Input::get('status');
        $ticket->save();

        $file_ids = Input::get("qty");
        foreach ($file_ids as $key => $value) {
            $file_object = TicketFilesModel::find($value);
            $file_object->ticket_id = $ticket->id;
            $file_object->save();
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
