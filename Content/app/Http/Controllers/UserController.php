<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Brand;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use DB;
use DateTime;


class UserController extends Controller
{

    public function makeHash(){
        $password = Hash::make('Kar@2026');
        //Tot@2404
        return $password;
    }
	
	 public function testQuery(){
        DB::select(" ");
        return "done";
        
    }

    public function login(Request $request){
        
        $passvalue = "login";
        if($request->isMethod("post")){
        $userData = User::where(["name"=>$request->username])->first();
        if(!empty($userData)){

            if (Hash::check($request->password, $userData->password)) {
                $request->session()->put('user', $userData);
                $userDate =  $userData->updated_at;
                if (is_null($userDate)) {
                    $days = true;
                }else{
                $givenDate = new DateTime($userDate);
                $currentDate = new DateTime();
                $difference = $currentDate->diff($givenDate);
                $days = $difference->days >= 30 && $difference->invert == 1;
                }
                if($days){
                    return redirect('changePassword');
                }else{
                    return redirect('/');
                }
            }else{
                echo "<script>";
                echo "alert('Incorrect Username or Password.');";
                echo "</script>";
            }

            
        }else{
            echo "<script>";
                echo "alert('Incorrect Username or Password.');";
                echo "</script>";
        }
    }
    return view("login", compact('passvalue')); 
}


public function changePassword(Request $request){

    $userpass = Session::get('user')['password'];  
    $userid = Session::get('user')['id']; 
    

    // else if(Hash::check($request->fcur, $userpass)){}

    $prev_pass = DB::select('select prev1, prev2, prev3, prev4, prev5 from users where id = ?', [$userid]);
    $prev1 = $prev_pass[0]->prev1;
    $prev2 = $prev_pass[0]->prev2;
    $prev3 = $prev_pass[0]->prev3;
    $prev4 = $prev_pass[0]->prev4;
    $prev5 = $prev_pass[0]->prev5;


    if($request->isMethod("post")){

        if (Hash::check($request->fcur, $userpass)) {
            $passwordInHash = Hash::make($request->fnew);


        if(Hash::check($request->fnew, $userpass) || Hash::check($request->fnew, $prev1) || Hash::check($request->fnew, $prev2) 
            || Hash::check($request->fnew, $prev3) || Hash::check($request->fnew, $prev4) || Hash::check($request->fnew, $prev5)){

            echo "<script>";
            echo "alert('Password Exist in History. Try New Combinations.');";
            echo "</script>";

        }else{
            User::where(['id'=>$userid])->update(['password'=>$passwordInHash,
            'prev1'=>$userpass,
            'prev2'=>$prev1,
            'prev3'=>$prev2,
            'prev4'=>$prev3,
            'prev5'=>$prev4]);
            $message = 'Password Change!';

            return redirect('/logout')->with('status', 'Password Change. Login with new credentials');
        }

            
        }else{
            echo "<script>";
            echo "alert('Current password is incorrect');";
            echo "</script>";
        }
    }
    return view("changepassword", compact('userpass')); 
}





public function editUsers(Request $request, $id=null){


    if($request->isMethod('post')){
        $data = $request->all();
        
        User::where(['id'=>$id])->update(['name'=>$data['name'],
            'code'=>$data['code'],
            'email'=>$data['email'],
            'password'=>$data['pass'],
            'status'=>$data['status'],
            'role'=>$data['role'],
            'expiry_pass'=>$data['edays'],
            'time_from'=>$data['time_from'],
            'time_to'=>$data['time_to'],
            'cash_code'=>$data['cash_code'],
            ]);  

        return redirect('/UserInformation');
        
    }
    
    $user = User::where(['id'=>$id])->first();
    return view('Utilities.userupdate', compact('user'));
}





// New Controllers
public function userList(Request $request){

    $userData =
        DB::table('users')
        ->select('users.*','brands.brand_name')
        ->join('brands', 'brands.id', '=', 'users.brand')->get();
    return view('User.user_list', compact('userData'));
}



public function createUser(Request $request){

    if($request->isMethod('post')){

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:8',
            'email' => 'required|email|max:255',
            'city' => 'required|string|max:255',
            'loc_zone' => 'required|string|max:1',
            'dealer_code' => 'required|string|max:255',
            'abb' => 'required|string|max:255',
            'loc_code' => 'required|numeric',
            'loc_code_tak' => 'required|numeric',
            'agency' => 'required|numeric',
            'con_per' => 'required|string|max:255',
            'con_no' => 'required|numeric',
            'brand' => 'required|numeric',
            'intg_tag' => 'required|string|max:1',
            'status' => 'required|string|max:1',
        ]);

        $user = new User;
        $passwordInHash = Hash::make($data['password']);


        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = $passwordInHash;
        $user->city = $data['city'];
        $user->loc_zone = $data['loc_zone'];
        $user->dealer_code = $data['dealer_code'];
        $user->abb = $data['abb'];
        $user->loc_code = $data['loc_code'];
        $user->loc_code_tak = $data['loc_code_tak'];

        $user->agency = $data['agency'];
        $user->con_per = $data['con_per'];

        $user->con_no = $data['con_no'];
        $user->brand = $data['brand'];
        $user->intg_tag = $data['intg_tag'];
        $user->status = $data['status'];

        if($user->save()){
            $current_user = $user->id;
            return redirect('/users');
        }else{
            return "User not created";
        }
        

    }
    $brandData = Brand::where('status', 'Y')->get();
    return view('User.userform', compact('brandData'));

}



}
