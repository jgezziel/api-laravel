<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Validator;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;


class userController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('api.auth', except: ['index', 'store'])
        ];
    }

    public function index()
    {
        $user = User::all();

        $data = $user->isEmpty() ? [
            'status' => 'error',
            'code' => 404,
            'message' => 'No users found'
        ] : [
            'status' => 'success',
            'code' => 200,
            'message' => 'List of users',
            'users' => $user
        ];

        return response()->json($data, $data['code']);
    }

    public function show($id)
    {
        $user = User::find($id);

        $data = $user ? [
            'status' => 'success',
            'code' => 200,
            'message' => 'User found',
            'user' => $user
        ] : [
            'status' => 'error',
            'code' => 404,
            'message' => 'User not found'
        ];

        return response()->json($data, $data['code']);
    }

    public function store(Request $request)
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
            'name' => 'required|alpha',
            'firstName' => 'required|alpha',
            'lastName' => 'required|alpha',
            'age' => 'required|numeric',
            'email' => 'required|email|unique:user',
            'password' => 'required|min:12'
        ];

        $messages = [
            'required' => 'The :attribute is required',
            'name.alpha' => 'Name must be a string',
            'firstName.alpha' => 'First name must be a string',
            'lastName.alpha' => 'Last name must be a string',
            'age.numeric' => 'Age must be a number',
            'email.email' => 'Email must be a valid email',
            'email.unique' => 'Email already exists',
            'password.min' => 'Password must be at least 12 characters'
        ];

        $validator = Validator::make($dataJson, $rules, $messages);

        if ($validator->fails()) {
            $data = [
                'status' => 'error',
                'code' => 404,
                'message' => 'User not created, validation failed',
                'errors' => $validator->errors()
            ];
            return response()->json($data, $data['code']);
        }

        $pwd = hash('sha256', $dataJson['password']);

        $user = new User($dataJson);
        $user->password = $pwd;

        $user->save();

        $data = [
            'status' => 'success',
            'code' => 200,
            'message' => 'User created successfully',
            'user' => $user
        ];

        return response()->json($data, $data['code']);
    }

    public function update(Request $request, $id)
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
            'name' => 'alpha',
            'firstName' => 'alpha',
            'lastName' => 'alpha',
            'age' => 'numeric',
            'email' => 'email|unique:user,email,' . $id,
            'password' => 'min:12'
        ];

        $messages = [
            'name.alpha' => 'Name must be a string',
            'firstName.alpha' => 'First name must be a string',
            'lastName.alpha' => 'Last name must be a string',
            'age.numeric' => 'Age must be a number',
            'email.email' => 'Email must be a valid email',
            'email.unique' => 'Email already exists',
            'password.min' => 'Password must be at least 12 characters'
        ];

        $validator = Validator::make($dataJson, $rules, $messages);

        if ($validator->fails()) {
            $data = [
                'status' => 'error',
                'code' => 404,
                'message' => 'User not updated, validation failed',
                'errors' => $validator->errors()
            ];
            return response()->json($data, $data['code']);
        }

        $user = User::find($id);

        if (!$user) {
            $data = [
                'status' => 'error',
                'code' => 404,
                'message' => 'User not found'
            ];
            return response()->json($data, $data['code']);
        }

        $pwd = hash('sha256', $dataJson['password']);

        $user->name = $dataJson['name'];
        $user->firstName = $dataJson['firstName'];
        $user->lastName = $dataJson['lastName'];
        $user->age = $dataJson['age'];
        $user->email = $dataJson['email'];
        $user->password = $pwd;

        $user->save();

        $data = [
            'status' => 'success',
            'code' => 200,
            'message' => 'User updated successfully',
            'user' => $user
        ];

        return response()->json($data, $data['code']);
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            $data = [
                'status' => 'error',
                'code' => 404,
                'message' => 'User not found'
            ];

            return response()->json($data, $data['code']);
        }

        $user->delete();
        $data = [
            'status' => 'success',
            'code' => 200,
            'message' => 'User deleted successfully',
            'user' => $user
        ];

        return response()->json($data, $data['code']);
    }

    public function disabled()
    {
        $user = User::onlyTrashed()->get();

        $data = $user->isEmpty() ? [
            'status' => 'error',
            'code' => 404,
            'message' => 'No users found in the trash'
        ] : [
            'status' => 'success',
            'code' => 200,
            'message' => 'List of deleted users',
            'users' => $user
        ];

        return response()->json($data, $data['code']);
    }
}
