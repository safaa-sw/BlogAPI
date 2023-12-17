<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
//use Validator;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends BaseController
{
    /**
     * Registration
     */
    public function register(Request $request)
    {
        /*$this->validate($request, [
            'name' => 'required|min:4',
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);
 
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);
       
        $token = $user->createToken('LaravelAuthApp')->accessToken;
 
        return response()->json(['token' => $token], 200);*/
        $validator=Validator::make($request->all(),[
            'name' => 'required|min:4',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'c_password'  => 'required|same:password',
        ]);
        if($validator->fails())
        {
            return $this->sendError('Validate Error',$validator->errors());
        }
        $input= $request->all();
        $input['password']=Hash::make($input['password']);
        $user = User::create($input);
        $user->save();
        $success['token']=$user->createToken('LaravelAuthApp')->accessToken;
        $success['name']=$user->name;
        return $this->sendResponse($success,"user Registerd Successfully");

    }
 
    /**
     * Login
     */
    public function login(Request $request)
    {
        /*$data = [
            'email' => $request->email,
            'password' => $request->password
        ];
 
        if (auth()->attempt($data)) {
            $token = auth()->user()->createToken('LaravelAuthApp')->accessToken;
            return response()->json(['token' => $token], 200);
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }*/

        if (Auth::attempt(['email'=>$request->email,'password'=>$request->password])) {
            $id=Auth::user()->id;
            $user=User::find($id);
            $success['token']=$user->createToken('LaravelAuthApp')->accessToken;
            $success['name']=$user->name;
            return $this->sendResponse($success,"user login Successfully");
        }
        else{
            return $this->sendError('UnAuthorized',['error','Unauthorized']); 
        }
        
    }   
}
