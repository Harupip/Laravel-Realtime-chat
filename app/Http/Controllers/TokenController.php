<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;


use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;
use App\Models\User;
//use JWTAuth;

class TokenController extends Controller
{
    public function token(Request $request)
    {
      $id = Auth::id();
    	  //$user = Auth::loginUsingId($request->id);
      $user = User::find($request->user()->id);

      // $user = Auth::user()
      $token = JWTAuth::fromUser($user);
        
      return response()->json(['token' => $token, 'id' => $id]);
    }
}
