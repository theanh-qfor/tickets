<?php
namespace App\Http\Controllers;
use App\User;
class SuggestController extends Controller{
    function userSuggest(){
        $result=array();
        $search=request('q');
        if(strpos($search,'@')===false) {
            $users=User::where('name', 'like', '%' . $search . '%');
        }else{
            $users=User::where('email','like','%'.$search.'%');
        }
        $users->where('role','engineer');
        $users=$users->select('id','name','email')->get();
        if($users){
            foreach($users as $u){
                $item=array('text'=>$u->name,'id'=>$u->id);
                $result[]=$item;
            }
        }
        return response()->json($result);
    }
}