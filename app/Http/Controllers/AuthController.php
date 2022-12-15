<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use App\Models\User;
use App\Models\UserProfile;
use App\Traits\AuthTrait;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponser, AuthTrait;

    public function register(Request $request)
    {
        try {
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
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password)
                ]);

                UserProfile::create([
                    'user_id' => $user->id,
                    'nama' => $request->nama,
                    'alamat' => $request->alamat,
                    'no_telp' => $request->no_telp
                ]);
                event(new Registered($user));
            } catch (\Exception $e) {
                DB::rollBack();
                return $this->errorResponse($e->getMessage());
            }
            DB::commit();
            return $this->successResponse('email-sent');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => ['required', 'string', 'email'],
                'password' => ['required', 'string'],
            ]);

            if (Auth::attempt($request->toArray())) {
                $user = User::find(Auth::id());

                if (!$user->email_verified_at)
                    return $this->errorResponse('email-not-verified');

                $createToken = $user->createToken('auth-token-umkm');
                return $this->successResponse([
                    'access_token' => $createToken->plainTextToken,
                    'umkm' => false
                ]);
            } else {
                return $this->errorResponse('Email or password is wrong', 401);
            }
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        // Auth::logout();
        // User::find(Auth::id())->tokens()->delete();
        // $request->session()

        // $request->session()->invalidate();

        // $request->session()->regenerateToken();

        return $this->successResponse();
    }

    public function manualVerify($id, $hash)
    {
        try {
            $getUser = User::where('id', $id)->first();

            if (!$getUser)
                return redirect($this->urlFailVerified());

            Auth::loginUsingId($id);

            $user = Auth::user();

            if (!hash_equals($hash, sha1($user->email))) {
                return redirect($this->urlFailVerified());
            }

            if (!$getUser->email_verified_at) {
                $getUser->markEmailAsVerified();

                event(new Verified($getUser));
            }

            return redirect($this->urlSuccessVerified());
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    public function resendVerification(Request $request)
    {
        try {
            $request->validate([
                'email' => ['required', 'email']
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return $this->errorResponse('user-not-found', 404);
            }

            if ($user->email_verified_at) {
                return $this->errorResponse('user-already-verified', 404);
            }

            $user->sendEmailVerificationNotification();

            return $this->successResponse('email-sent');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }
}
