<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AuthRequest;
use App\Http\Resources\UserAuth\UserAuthResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(AuthRequest $request)
    {
        try {
            $dataUser = $request->validated();

            $dataUser = [
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ];

            $user = User::firstOrCreate($dataUser);

            $response = [
                'message' => 'User successfully registered.',
                'token'   => $user->createToken('MyApp')->plainTextToken,
                'data'    => UserAuthResource::make($user),
            ];

            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => "Something went really wrong!"
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $credentials = [
                'email'    => $request->email,
                'password' => $request->password,
            ];

            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $success['token'] = $user->createToken('MyApp')->plainTextToken;
                $success['email'] = $user->email;

                return response()->json([
                    'message' => "Authorised",
                    'data'    => $success
                ], 200);
            }

            return response()->json([
                'message' => "Unauthorised"
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => "Something went really wrong!"
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();

            return response()->json([
                'message' => "User successfully logout."
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => "Something went really wrong!"
            ], 500);
        }
    }
}
