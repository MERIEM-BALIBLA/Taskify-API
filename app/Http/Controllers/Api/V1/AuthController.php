<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{


    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

     /**
     * Login
     *
     * Log in the user
     *
     * @bodyParam email string required The email address of the user.
     * @bodyParam password string required The password of the user.
     *
     * @response 200 {
     *     "status": true,
     *     "message": "Login successful",
     *     "user": {
     *         "name": "John Doe",
     *         "email": "john@example.com"
     *     },
     *     "token": "Bearer {token}"
     * }
     * @response 401 {
     *     "status": false,
     *     "error": "Invalid credentials"
     * }
     */
    public function login(Request $request)
    {
        $inputs = $request->only('email', 'password');

        $user = $this->userRepository->findByEmail($inputs['email']);

        if (!$user || !password_verify($inputs['password'], $user->password)) {
            return response()->json([
                'status' => false,
                'error' => 'Invalid inputs'
            ], 401);
        }

        Auth::login($user);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token
        ]);
    }

/**
     * Register
     *
     * Register a new user
     *
     * @bodyParam name string required The name of the user.
     * @bodyParam email string required The email address of the user.
     * @bodyParam password string required The password of the user.
     *
     * @response 200 {
     *     "name": "John Doe",
     *     "email": "john@example.com",
     *     "password": "password",
     *     "token": "Bearer {token}"
     * }
     * @response 400 {
     *     "status": false,
     *     "error": {
     *         "email": [
     *             "The email has already been taken."
     *         ]
     *     }
     * }
     */
    public function register(Request $request)
    {
        $rules = [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ];

        $validator = validator($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                'error' => $validator->errors()
            ], 400);
        }

        $user = $this->userRepository->create([
            "name" => $request->input('name'),
            "email" => $request->input('email'),
            "password" => Hash::make($request->input('password')),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            "name" => $user->name,
            "email" => $user->email,
            "password" => $request->input('password'),
            "token" => $token
        ]);
    }

    /**
 * Logout
 *
 * Log out the authenticated user
 *
 * @response 200 {}
 */
    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    /**
 * Show Forgot Password Form
 *
 * Show the form for requesting a password reset link
 *
 * @response 200 {}
 */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
 * Send Reset Link Email
 *
 * Send the password reset link to the given email address
 *
 * @bodyParam email string required The email address of the user.
 *
 * @response 302 {}
 */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $status = Password::sendResetLink($request->only('email'));
        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => $status])
            : back()->withErrors(['email' => $status]);
    }

/**
 * Show Reset Password Form
 *
 * Show the form for resetting the user's password
 *
 * @urlParam token string required The token provided in the password reset link email.
 *
 * @response 200 {}
 */
    public function showResetPasswordForm($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    /**
 * Reset Password
 *
 * Reset the user's password
 *
 * @bodyParam token string required The token provided in the password reset link email.
 * @bodyParam email string required The email address of the user.
 * @bodyParam password string required The new password for the user.
 * @bodyParam password_confirmation string required The confirmation of the new password.
 *
 * @response 302 {}
 */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => bcrypt($password),
                ])->save();
            }
        );
        return $status == Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', $status)
            : back()->withErrors(['email' => $status]);
    }
}
