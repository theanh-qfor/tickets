<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Input;

class IndexController extends Controller
{
    public function addTicket()
    {
        $user = new User;

        $user->username = Input::get('username');
        $user->email = Input::get('email');
        $user->password = Hash::make(Input::get('password'));
        $user->save();

        return Redirect::back();
    }
}
