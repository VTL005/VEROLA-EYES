<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Form đăng ký.
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Xử lý đăng ký Customer.
     */
    public function register(RegisterRequest $request)
    {
        $customerRole = Role::where(
            'name',
            'customer'
        )->firstOrFail();

        $user = User::create([
            'role_id' => $customerRole->id,

            'name' => trim($request->name),
            'email' => strtolower($request->email),
            'phone' => $request->phone,

            'avatar' => null,
            'position' => null,

            'is_active' => true,

            'password' => $request->password,
        ]);

        /*
         * Đăng nhập ngay sau khi đăng ký.
         */
        Auth::login($user);

        /*
         * Chống Session Fixation.
         */
        $request->session()->regenerate();

        return redirect()
            ->route('home')
            ->with(
                'success',
                'Đăng ký tài khoản thành công.'
            );
    }

    /**
     * Form đăng nhập.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Xử lý Login.
     */
    public function login(LoginRequest $request)
    {
        $user = User::with('role')
            ->where(
                'email',
                strtolower($request->email)
            )
            ->first();

        /*
         * Không tìm thấy User
         * hoặc Password không chính xác.
         */
        if (
            !$user
            || !Hash::check(
                $request->password,
                $user->password
            )
        ) {
            return back()
                ->withErrors([
                    'email' => 'Email hoặc mật khẩu không chính xác.',
                ])
                ->withInput(
                    $request->only('email')
                );
        }

        /*
         * Tài khoản bị Admin khóa.
         */
        if (!$user->is_active) {
            return back()
                ->withErrors([
                    'email' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.',
                ])
                ->withInput(
                    $request->only('email')
                );
        }

        /*
         * Login.
         */
        Auth::login(
            $user,
            $request->boolean('remember')
        );

        /*
         * Tạo Session ID mới.
         */
        $request->session()->regenerate();

        /*
         * Redirect theo Role.
         */
        return $this->redirectByRole($user);
    }

    /**
     * Logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with(
                'success',
                'Đăng xuất thành công.'
            );
    }

    /**
     * Điều hướng theo Role.
     */
    private function redirectByRole(User $user)
    {
        return match ($user->role->name) {

    'admin' =>
    redirect()->route(
        'admin.dashboard'
    ),

    'staff' => redirect()
        ->route('staff.orders.index'),

    default => redirect()
        ->route('home'),
    };
    }
}