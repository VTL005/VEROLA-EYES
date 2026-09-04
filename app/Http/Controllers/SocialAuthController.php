<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class SocialAuthController extends Controller
{
    /**
     * Chuyển người dùng sang Google OAuth.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->redirect();
    }

    /**
     * Google callback.
     */
    public function handleGoogleCallback(Request $request)
    {
        try {

            $googleUser = Socialite::driver('google')
                ->user();

        } catch (Throwable $exception) {

            report($exception);

            return redirect()
                ->route('login')
                ->withErrors([
                    'social' =>
                        'Không thể đăng nhập bằng Google. Vui lòng thử lại.',
                ]);
        }


        $googleId = $googleUser->getId();

        $email = strtolower(
            trim(
                (string) $googleUser->getEmail()
            )
        );

        $name = trim(
            (string) $googleUser->getName()
        );


        /*
        |--------------------------------------------------------------------------
        | KIỂM TRA DỮ LIỆU GOOGLE
        |--------------------------------------------------------------------------
        */

        if (!$googleId || !$email) {

            return redirect()
                ->route('login')
                ->withErrors([
                    'social' =>
                        'Google không cung cấp đủ thông tin tài khoản.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | TÌM USER ĐÃ CÓ
        |--------------------------------------------------------------------------
        |
        | Ưu tiên tìm theo google_id.
        | Nếu chưa liên kết thì tìm tiếp bằng email.
        |
        */

        $user = User::with('role')
            ->where(
                'google_id',
                $googleId
            )
            ->first();


        if (!$user) {

            $user = User::with('role')
                ->where(
                    'email',
                    $email
                )
                ->first();
        }


        /*
        |--------------------------------------------------------------------------
        | USER ĐÃ TỒN TẠI
        |--------------------------------------------------------------------------
        */

        if ($user) {

            /*
             * Google Login chỉ dành cho Customer.
             */
            if (!$user->isCustomer()) {

                return redirect()
                    ->route('login')
                    ->withErrors([
                        'social' =>
                            'Đăng nhập Google chỉ áp dụng cho tài khoản khách hàng.',
                    ]);
            }


            /*
             * Tài khoản bị khóa.
             */
            if (!$user->is_active) {

                return redirect()
                    ->route('login')
                    ->withErrors([
                        'social' =>
                            'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.',
                    ]);
            }


            /*
             * Nếu tài khoản Customer trước đây đăng ký
             * bằng email/password thì liên kết Google.
             */
            if (!$user->google_id) {

                $user->google_id = $googleId;

                /*
                 * Google đã xác thực email.
                 */
                if (!$user->email_verified_at) {
                    $user->email_verified_at = now();
                }

                $user->save();
            }


            /*
             * Nếu google_id hiện tại khác với
             * tài khoản Google vừa callback.
             */
            if (
                $user->google_id !==
                $googleId
            ) {

                return redirect()
                    ->route('login')
                    ->withErrors([
                        'social' =>
                            'Tài khoản Google không khớp với tài khoản đã liên kết.',
                    ]);
            }


            /*
             * Đăng nhập.
             */
            Auth::login($user);


            /*
             * Chống Session Fixation.
             */
            $request->session()
                ->regenerate();


            return redirect()
                ->route('home')
                ->with(
                    'success',
                    'Đăng nhập bằng Google thành công.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | GOOGLE USER MỚI
        |--------------------------------------------------------------------------
        |
        | Database hiện tại bắt buộc phone.
        | Google không cung cấp phone ổn định.
        |
        | Vì vậy lưu tạm dữ liệu Google vào session
        | rồi yêu cầu Customer nhập số điện thoại.
        |
        */

        $request->session()->put(
            'google_pending_user',
            [
                'google_id' =>
                    $googleId,

                'name' =>
                    $name !== ''
                        ? $name
                        : 'Khách hàng VELORA',

                'email' =>
                    $email,
            ]
        );


        return redirect()
            ->route(
                'social.google.complete'
            );
    }

    /**
     * Form bổ sung số điện thoại
     * cho Google User mới.
     */
    public function showGoogleCompleteProfile(
        Request $request
    ) {

        $googleUser =
            $request->session()
                ->get(
                    'google_pending_user'
                );


        if (!$googleUser) {

            return redirect()
                ->route('login')
                ->withErrors([
                    'social' =>
                        'Phiên đăng nhập Google đã hết hạn. Vui lòng thử lại.',
                ]);
        }


        return view(
            'auth.google-complete-profile',
            [
                'googleUser' =>
                    $googleUser,
            ]
        );
    }

    /**
     * Tạo Customer sau khi
     * bổ sung số điện thoại.
     */
    public function completeGoogleProfile(
        Request $request
    ) {

        $googleUser =
            $request->session()
                ->get(
                    'google_pending_user'
                );


        if (!$googleUser) {

            return redirect()
                ->route('login')
                ->withErrors([
                    'social' =>
                        'Phiên đăng nhập Google đã hết hạn. Vui lòng thử lại.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDATE PHONE
        |--------------------------------------------------------------------------
        */

        $validated =
            $request->validate(
                [
                    'phone' => [
                        'required',
                        'regex:/^0[0-9]{9}$/',
                        'unique:users,phone',
                    ],
                ],
                [
                    'phone.required' =>
                        'Vui lòng nhập số điện thoại.',

                    'phone.regex' =>
                        'Số điện thoại phải gồm 10 chữ số và bắt đầu bằng số 0.',

                    'phone.unique' =>
                        'Số điện thoại này đã được sử dụng.',
                ]
            );


        /*
        |--------------------------------------------------------------------------
        | KIỂM TRA LẠI EMAIL
        |--------------------------------------------------------------------------
        |
        | Tránh trường hợp trong thời gian hoàn thiện hồ sơ,
        | một tài khoản khác đã được tạo với cùng email.
        |
        */

        if (
            User::where(
                'email',
                $googleUser['email']
            )->exists()
        ) {

            $request->session()
                ->forget(
                    'google_pending_user'
                );


            return redirect()
                ->route('login')
                ->withErrors([
                    'social' =>
                        'Email này đã được sử dụng. Vui lòng đăng nhập lại bằng Google.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | CUSTOMER ROLE
        |--------------------------------------------------------------------------
        */

        $customerRole =
            Role::where(
                'name',
                'customer'
            )
            ->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | CREATE CUSTOMER
        |--------------------------------------------------------------------------
        |
        | password vẫn NOT NULL trong database.
        |
        | User model có:
        | 'password' => 'hashed'
        |
        | nên mật khẩu random sẽ tự được hash.
        |
        */

        $user = User::create([
            'role_id' =>
                $customerRole->id,

            'name' =>
                $googleUser['name'],

            'email' =>
                $googleUser['email'],

            'google_id' =>
                $googleUser['google_id'],

            'facebook_id' =>
                null,

            'phone' =>
                $validated['phone'],

            'avatar' =>
                null,

            'position' =>
                null,

            'is_active' =>
                true,

            'email_verified_at' =>
                now(),

            'password' =>
                Str::random(40),
        ]);


        /*
         * Xóa dữ liệu Google tạm.
         */
        $request->session()
            ->forget(
                'google_pending_user'
            );


        /*
         * Login Customer.
         */
        Auth::login($user);


        /*
         * Chống Session Fixation.
         */
        $request->session()
            ->regenerate();


        return redirect()
            ->route('home')
            ->with(
                'success',
                'Đăng ký và đăng nhập bằng Google thành công.'
            );
    }
}