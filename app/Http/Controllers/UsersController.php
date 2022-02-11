<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use \App\Models\User;
use Illuminate\Validation\ValidationException;

class UsersController extends Controller{
    /**
     * @throws ValidationException
     */
    public function register(Request  $request) {
        $this->validate($request, [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|unique:users|email',
            'password' => 'required|string',
            'age' => 'string'
        ]);

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);

        return response(['message' => 'Account created successfully!',
                         'status' => true]);
    }

    public function getUserDetails(String $user_id) {
        $user = Auth::user();
        if ($user['id'] == $user_id){
            return response([
                'status' => true,
                'message' => 'User details',
                'data' => $user
            ]);
        }

        return response([
            'status' => false,
            'message' => 'Invalid route'
        ], 401);
    }

    /**
     * @throws ValidationException
     */
    public function login(Request  $request) {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request['email'])->first();

        // check if user exists
        if (!isset($user)) {
            return response([
                'message' => 'User does not exist',
                'status' => false
            ], 401);
        }

        // check if correct password
        if (!Hash::check($request->password, $user['password'])) {
            return response()->json([
                'message' => 'Invalid email address or password',
                'status' => false
            ], 401);
        }

        // generate auth token and save to database
        $token = $user->createToken('AuthToken')->accessToken;
        User::where('email', $request['email'])->update(
            array('api_token' => $token)
        );

        return response([
            'message' => 'User login successfully',
            'data' => [
                'user' => $user,
                'api_token' => $token
            ],
            'status' => true
        ]);

    }
}
