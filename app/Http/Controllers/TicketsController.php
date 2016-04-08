<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Models\TicketModel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


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
}
