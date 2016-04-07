<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Models\TicketModel;
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
        return response()->json($results);
    }
    public function addTicket()
    {
        $ticket = new TicketModel();

        $ticket->subject = Input::get('subject');
        $ticket->description = Input::get('description');
        $ticket->importance = Input::get('importance');
        $ticket->status = Input::get('status');
        $ticket->save();
        return Redirect::back();

    }
}
