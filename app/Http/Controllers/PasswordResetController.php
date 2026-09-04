<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;


class PasswordResetController extends Controller
{
    /**
     * Hiển thị form nhập email quên mật khẩu.
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Gửi link đặt lại mật khẩu.
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => [
                'required',
                'email',
            ],
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không hợp lệ.',
        ]);

        $email = strtolower(
            trim($request->email)
        );

        /*
         * Chỉ cho Customer và Staff sử dụng
         * chức năng quên mật khẩu.
         */
        $user = User::query()
            ->where('email', $email)
            ->whereHas('role', function ($query) {
                $query->whereIn(
                    'name',
                    [
                        'customer',
                        'staff',
                    ]
                );
            })
            ->first();

        /*
         * Không tiết lộ email có tồn tại hay không.
         */
        if (!$user) {
            return back()->with(
                'status',
                'Nếu email tồn tại trong hệ thống, liên kết đặt lại mật khẩu sẽ được gửi đến email của bạn.'
            );
        }

        /*
         * Không gửi reset password
         * cho tài khoản đã bị khóa.
         */
        if (!$user->is_active) {
            return back()
                ->withErrors([
                    'email' => 'Tài khoản hiện đang bị khóa. Vui lòng liên hệ quản trị viên.',
                ])
                ->withInput(
                    $request->only('email')
                );
        }

        $status = Password::sendResetLink([
            'email' => $email,
        ]);

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with(
                'status',
                'Liên kết đặt lại mật khẩu đã được gửi đến email của bạn.'
            );
        }

        return back()
            ->withErrors([
                'email' => __($status),
            ])
            ->withInput(
                $request->only('email')
            );
    }

    /**
     * Hiển thị form đặt mật khẩu mới.
     */
    public function showResetPasswordForm(
        Request $request,
        string $token
    ) {
        return view(
            'auth.reset-password',
            [
                'token' => $token,
                'email' => $request->query('email'),
            ]
        );
    }

    /**
     * Xử lý đặt mật khẩu mới.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => [
                'required',
            ],

            'email' => [
                'required',
                'email',
            ],

                        'password' => [
                        'required',
                        'confirmed',
                        'min:8',
                        'regex:/[a-z]/',
                        'regex:/[A-Z]/',
                        'regex:/[0-9]/',
                            ],
        ], [
            'token.required' => 'Token đặt lại mật khẩu không hợp lệ.',

            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không hợp lệ.',

            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
        ]);

        /*
         * Đảm bảo email thuộc Customer hoặc Staff
         * và tài khoản đang hoạt động.
         */
        $user = User::query()
            ->where(
                'email',
                strtolower(trim($request->email))
            )
            ->where('is_active', true)
            ->whereHas('role', function ($query) {
                $query->whereIn(
                    'name',
                    [
                        'customer',
                        'staff',
                    ]
                );
            })
            ->first();

        if (!$user) {
            return back()
                ->withErrors([
                    'email' => 'Không thể đặt lại mật khẩu cho tài khoản này.',
                ])
                ->withInput(
                    $request->only('email')
                );
        }

        $status = Password::reset(
            [
                'email' => strtolower(
                    trim($request->email)
                ),

                'password' => $request->password,

                'password_confirmation' =>
                    $request->password_confirmation,

                'token' => $request->token,
            ],

            function (User $user, string $password) {

                /*
                 * User model đã có:
                 *
                 * 'password' => 'hashed'
                 *
                 * nên Laravel tự hash mật khẩu.
                 */
                $user->forceFill([
                    'password' => $password,

                    'remember_token' =>
                        Str::random(60),
                ])->save();

                /*
                 * Phát sự kiện chuẩn của Laravel
                 * sau khi reset thành công.
                 */
                event(
                    new PasswordReset($user)
                );
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()
                ->route('login')
                ->with(
                    'success',
                    'Đặt lại mật khẩu thành công. Bạn có thể đăng nhập bằng mật khẩu mới.'
                );
        }

        return back()
            ->withErrors([
                'email' => __($status),
            ])
            ->withInput(
                $request->only('email')
            );
    }
}