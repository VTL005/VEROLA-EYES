<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Xem hồ sơ Customer.
     */
    public function show()
    {
        $user = auth()->user();


        /*
         * Load Role và địa chỉ.
         */
        $user->load([
            'role',

            'addresses' => function ($query) {

                $query
                    ->orderByDesc('is_default')
                    ->latest();
            },
        ]);


        /*
         * Đếm nhanh các dữ liệu liên quan
         * để hiển thị trên Dashboard Profile.
         */
        $user->loadCount([
            'addresses',
            'orders',
            'appointments',
            'eyePrescriptions',
            'warranties',
        ]);


        return view(
            'profile.show',
            compact('user')
        );
    }


    /**
     * Form sửa hồ sơ.
     */
    public function edit()
    {
        $user = auth()->user();


        return view(
            'profile.edit',
            compact('user')
        );
    }


    /**
     * Cập nhật hồ sơ.
     */
    public function update(
        ProfileUpdateRequest $request
    ) {
        $user = auth()->user();

        $validated = $request->validated();


        /*
         * Email không có trong ProfileUpdateRequest
         * nên Customer không thể tự thay đổi email.
         */


        /*
         * Nếu Customer tải Avatar mới.
         */
        if ($request->hasFile('avatar')) {

            /*
             * Lưu lại Avatar cũ.
             */
            $oldAvatar = $user->avatar;


            /*
             * Lưu Avatar mới.
             */
            $validated['avatar'] = $request
                ->file('avatar')
                ->store(
                    'avatars',
                    'public'
                );


            /*
             * Update User trước.
             */
            $user->update(
                $validated
            );


            /*
             * Sau khi update thành công
             * mới xóa Avatar cũ.
             */
            if (
                $oldAvatar
                && $oldAvatar !== $user->avatar
                && Storage::disk('public')
                    ->exists($oldAvatar)
            ) {
                Storage::disk('public')
                    ->delete($oldAvatar);
            }

        } else {

            /*
             * Không có Avatar mới
             * thì không đụng đến Avatar hiện tại.
             */
            unset(
                $validated['avatar']
            );


            $user->update(
                $validated
            );
        }


        return redirect()
            ->route('profile.show')
            ->with(
                'success',
                'Cập nhật hồ sơ thành công.'
            );
    }
}