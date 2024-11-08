<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginUserRequest;
use App\Models\User;
use App\Permissions\V1\Abilities;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{

    use ApiResponses;

    /**
     * Login
     * 
     * Authenticates the user and returns the user's API Token
     * 
     * @unauthenticated
     * @group Authentication
     * @response 200 {
     *  "data": {
     *      "token": "{YOUR_AUTH_TOKEN}"
     *  },
     *  "message": "Authenticated",
     *  "statusCode": 200
     * }
     * 
     */
    public function login(LoginUserRequest $request)
    {
        $request->validated($request->all());

        if(!Auth::attempt($request->only('email', 'password'))){
            return $this->error('Invalid Credentials', Response::HTTP_UNAUTHORIZED);
        }

        $user = User::firstWhere('email', $request->email);

        return $this->ok(
            'Authenticated', 
            [
                'token' => $user->createToken(
                    'API Token for ' . $user->email, 
                    Abilities::getAbilities($user),
                    now()->addMonth()
                )->plainTextToken
            ]
        );
    }

    /**
     * Logout
     * 
     * Logout the authenticated user
     * 
     * @group Authentication
     * @response 200 {}
     * 
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->ok('Logout');
    }


    public function register()
    {
        return $this->ok('Register Ok', []);
    }
}
