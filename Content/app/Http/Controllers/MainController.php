<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Models\Plan;
use DB;

class MainController extends Controller
{


    public function main(Request $request){

        $userid = Session::get('user')['name'];
        $role = Session::get('user')['role'];

        if($role == 'admin'){
            $insuData =
            DB::table('transactions')
            ->select('transactions.*')
            ->orderby('created_at', 'desc')
            ->get();
        }else{
            $insuData =
            DB::table('transactions')
            ->select('transactions.*')
            ->where('created_by', $userid)
            ->orderby('created_at', 'desc')
            ->get();
        }
        

            
        return view('main', compact('insuData'));
    }
    
  


}
