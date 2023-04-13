<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends BaseController
{

    public function getToken(Request $request)
    {
        $request->validate(
            [
                'username' => 'required',
                'password' => 'required'
            ]
        );
        $username = $request->get('username');
        $password = $request->get('password');
        /** @var  $user  User */
        $user = User::query()->where('username', $username)->first();
        if(!$user || !Hash::check($password, $user->password)){
            return response("Invalide username or password", 401);
        }
        $token = $user->createToken('Customer Token');
        $expiresIn = config('sanctum.sanctum');
        return response()->json(['access_token' => $token, 'token_type' => 'Bearer', 'expires_in' => $expiresIn]);
    }
}
