<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\DB;
use App\Models\User;

use UnexpectedValueException;

class JwtAuth
{
    public $key;

    public function __construct()
    {
        $this->key = env('JWT_SECRET');
    }

    public function login($data)
    {
        $email = $data['email'];
        $password = $data['password'];
        $getToken = $data['getToken'] ?? null;

        $user = User::where([
            'email' => $email,
            'password' => $password
        ])->first();

        if ($user == null) {
            $data = [
                'status' => 'error',
                'code' => 404,
                'message' => 'Login failed, incorrect credentials'
            ];
        } else {
            $token = [
                'id' => $user->id,
                'email' => $user->email,
                'nameComplete' => $user->name . ' ' . $user->firstName . ' ' . $user->lastName,
                'iat' => time(),
                'exp' => time() + (1 * 24 * 60 * 60)
            ];

            $jwt = JWT::encode($token, $this->key, 'HS256');

            $decoded = JWT::decode($jwt, new Key($this->key, 'HS256'));

            $data = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Login successful',
                'token' => $jwt
            ];

            $getToken ? $data = $decoded : $data;
        }

        return $data;
    }

    public function checkToken($jwt, $getIdentity = false)
    {
        $jwt = str_replace('"', '', $jwt);
        try {
            $decoded = JWT::decode($jwt, new Key($this->key, 'HS256'));
        } catch (\LogicException $e) {
            // errors having to do with environmental setup or malformed JWT Keys
            $auth = false;
        } catch (UnexpectedValueException $e) {
            // errors having to do with JWT signature and claims
            $auth = false;
        }

        $isDecoded = !empty($decoded) && is_object($decoded) && isset($decoded->id);

        $isDecoded ? $auth = true : $auth = false;

        if ($getIdentity && $isDecoded) {
            return $decoded;
        }

        return $auth;
    }
}
