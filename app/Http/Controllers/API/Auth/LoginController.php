<?php

namespace App\Http\Controllers\API\Auth;

use App\User;

use Hash;
use Validator;
use Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class LoginController extends Controller
{
    public function login (Request $request)
    {
        $validator = Validator::make(['email' => $request->email],[
            'email' => 'required|email'
        ]);
        
        $user = null;
        if($validator->passes()){
            $user = User::where('email', $request->email)->first();
        }

        if ($user) {

            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken('Laravel Password Grant Client')->accessToken;

                $response = ['token' => $token, 'user' => $user];
                return response()->json(['code' => 200, 'message' => 'Login effettuato.', 'data' => $response, 'error' => null], 200);
            } else {
                return response()->json(['code' => 422, 'message' => 'Password errata.', 'data' => null, 'error' => null], 422);
            }
    
        } else {
            return response()->json(['code' => 422, 'message' => 'Combinazione utente password non trovata.', 'data' => null, 'error' => null], 422);
        }
    }

    public function me ()
    {
        $user = Auth::user();
        return response()->json([
            'code' => 200,
            'message' => 'Profilo utente.',
            'data' => [
                'user' => $user
            ],
            'error' => null
        ], 200);
    }

    public function logout ()
    {
        $user = Auth::user();
        $token = $user->token();
        $token->revoke();

        return response()->json(['code' => 200, 'message' => 'Logout con successo.', 'data' => null, 'error' => null], 200);
    
    }
}
