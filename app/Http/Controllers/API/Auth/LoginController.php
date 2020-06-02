<?php

namespace App\Http\Controllers\API\Auth;

use App\Models\User;

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
            
            if($user->status != 1) {
                return response()->json(['message' => 'Il tuo utente è disabilitato.'], 403);
            }

            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken('Laravel Password Grant Client')->accessToken;
                
                $currentRole = $user->roles->pluck('name')[0];
                if (setting('logging')) {
                    activity()
                        ->causedBy($user)
                        ->withProperties(['type' => 'login'])
                        ->log('Ha effettuato l\'accesso.');
                }

                $response = ['token' => $token, 'user' => $user, 'role' => $currentRole];
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
        $roles = $user->getRoleNames();
        $permissions = $user->getAllPermissions();
        return response()->json([
            'code' => 200,
            'message' => 'Profilo utente.',
            'data' => [
                'user' => $user,
                'roles' => $roles,
                'permissions' => $permissions
            ],
            'error' => null
        ], 200);
    }

    public function logout ()
    {
        $user = Auth::user();
        $token = $user->token();
        $token->revoke();

        if (setting('logging'))
        {
            activity()
                ->causedBy($user)
                ->withProperties(['type' => 'logout'])
                ->log('Ha effettuato il logout.');
        }

        return response()->json(['code' => 200, 'message' => 'Logout con successo.', 'data' => null, 'error' => null], 200);
    
    }
}
