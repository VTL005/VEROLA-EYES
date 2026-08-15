<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    /**
     * Hiển thị form đổi mật khẩu.
     */
    public function edit()
    {
        return view(
            'profile.change-password'
        );
    }


    /**
     * Cập nhật mật khẩu mới.
     */
    public function update(
        ChangePasswordRequest $request
    ) {
        $user = auth()->user();


        /*
         * ChangePasswordRequest đã kiểm tra:
         *
         * - mật khẩu hiện tại
         * - mật khẩu mới >= 8 ký tự
         * - xác nhận mật khẩu mới
         */
        $user->update([
            'password' => Hash::make(
                $request->password
            ),
        ]);


        return redirect()
            ->route('profile.show')
            ->with(
                'success',
                'Đổi mật khẩu thành công.'
            );
    }
}