<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponser;

    public function register(Request $request)
    {
        $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', Rules\Password::defaults()],
            'alamat' => ['required', 'string', 'max:255'],
            'no_telp' => ['required', 'numeric']
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            UserProfile::create([
                'user_id' => $user->id,
                'nama' => $request->nama,
                'alamat' => $request->alamat,
                'no_telp' => $request->no_telp
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
        DB::commit();
        return $this->successResponse('User registered');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($request->toArray())) {
            $user = Auth::user();
            $umkm = $user->umkm ? true : false;
            $role = $umkm ? UserRole::UMKM : UserRole::BUYER;
            $createToken = $user->createToken('auth-token-umkm', [$role]);
            return $this->successResponse([
                'access_token' => $createToken->plainTextToken,
                'umkm' => $umkm
            ]);
        } else {
            return $this->errorResponse('Email or password is wrong', 401);
        }
    }

    public function logout()
    {
        Auth::user()->tokens()->delete();
        return $this->successResponse();
    }
}
