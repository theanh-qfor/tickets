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
        return response()->json($results);
    }
}
