<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApiLoginRequest;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    use ApiResponses;

    public function login(ApiLoginRequest $apiLoginRequest)
    {

        $email = $apiLoginRequest->get('email');

        return $this->ok($email);
    }


    public function register()
    {
        return $this->ok('Register Ok');
    }
}
