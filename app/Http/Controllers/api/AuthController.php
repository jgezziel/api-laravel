<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Helpers\JwtAuth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    
    public function login(Request $request)
    {
        $dataJson = $request->input('data', null);

        if ($dataJson == null) {
            $data = [
                'status' => 'error',
                'code' => 404,
                'message' => 'No data received'
            ];
            return response()->json($data, $data['code']);
        }

        $rules = [
            'email' => 'required|email',
            'password' => 'required'
        ];

        $messages = [
            'email.required' => 'The email field is required',
            'email.email' => 'The email field must be a valid email',
            'password.required' => 'The password field is required'
        ];

        $validator = Validator::make($dataJson, $rules, $messages);

        if ($validator->fails()) {
            $data = [
                'status' => 'error',
                'code' => 404,
                'message' => 'Login failed, validation failed',
                'errors' => $validator->errors()
            ];
            return response()->json($data, $data['code']);
        }

        $jwtAuth = new JwtAuth();

        $pwd = hash('sha256', $dataJson['password']);
        $dataJson['password'] = $pwd;

        $signup = $jwtAuth->login($dataJson);

        if($signup['status'] == 'success') {
            $duration = 60 * 24;
            $token = $signup['token'];
           return response()->json($signup, 200)->withCookie(cookie('token', $token, $duration));
        }

        return response()->json($signup, 200);
    }
}
