<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    //index
    public function index($email) {

        $user = User::where('email', $email)->first();
        return response()->json([
            'success' => true,
            'data' => $user,
        ]);
    }

    //update googleid

    public function updateGoogleId(Request $request, $id)

    {
        $request->validate([
            "google_id" => 'required',
        ]);

        $user = User::find($id);

        if ($user) {
            $user->google_id = $request->google_id;
            $user->save();
            return response()->json([
                'success' => "success",
                'data' => $user,
            ]);
        }else {
            return response()->json([
                'success' => "error",
                'message' => 'User not found',
            ], 404);
        }
    }
   //update user
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'address' => 'required',
            'google_id' => 'required',
            'ktp_number' => 'required',
            'birth_date' => 'required',
            'gender' => 'required',
            'phone_number' => 'required',
        ]);

        $data = $request->all();
        $user = User::find($id);
        $user->update($data);

        return response()->json([
            'status' => 'success',
            'data' => $user
        ]);
    }

    //check email
    public function checkEmail(Request $request) {

        request()->validate([
            'email' => 'required|email',
        ]);

        $email = $request->email;
        $user = User::where('email', $email)->first();

        if ($user) {
            return response()->json([
                'success' => "success",
                'data' => "Email already Registered",
                'valid' => false
            ]);
        }else {
            return response()->json([
                'success' => "error",
                'message' => 'Email not Registered',
                'valid' => true
            ], 404);
        }
    }

    //check login
    public function login(Request $request) {

        request()->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $email = $request->email;
        $password = $request->password;
        $user = User::where('email', $email)->first();

        if(!$user || !Hash::check($password, $user->password)) {
            return response()->json([
                'success' => "error",
                'message' => 'Invalid Credentials',

            ], 401);
        }

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'success' => "success",
            'data' => [
            'user' => $user,
            'token' => $token
            ]
        ], 200);
    }

    //logout
    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => "success",
            'message' => 'Token Deleted',
        ], 200);
    }

    //store
    public function store(Request $request) {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'role' => 'required',
        ]);

        $data = $request->all();
        $name = $request->name;
        $email = $request->email;
        $role = $request->role;

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'role' => $role,
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $user
        ], 201);
    }

}
