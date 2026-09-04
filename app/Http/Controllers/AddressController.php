<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Models\Address;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    /**
     * Danh sách địa chỉ của Customer.
     */
    public function index()
    {
        $addresses = Address::query()
            ->where(
                'user_id',
                auth()->id()
            )
            ->orderByDesc('is_default')
            ->latest()
            ->get();

        return view(
            'addresses.index',
            compact('addresses')
        );
    }


    /**
     * Form thêm địa chỉ.
     */
    public function create()
    {
        return view(
            'addresses.create'
        );
    }


    /**
     * Lưu địa chỉ mới.
     */
    public function store(
        AddressRequest $request
    ) {
        DB::transaction(
            function () use ($request) {

                $userId = auth()->id();


                /*
                |--------------------------------------------------------------------------
                | ĐỊA CHỈ MẶC ĐỊNH
                |--------------------------------------------------------------------------
                |
                | Nếu Customer chưa có địa chỉ nào,
                | địa chỉ đầu tiên tự động là mặc định.
                |
                */

                $hasAddress = Address::query()
                    ->where(
                        'user_id',
                        $userId
                    )
                    ->exists();


                $isDefault =
                    !$hasAddress
                    || $request->boolean(
                        'is_default'
                    );


                /*
                 * Nếu địa chỉ mới được đặt làm mặc định,
                 * bỏ trạng thái mặc định ở các địa chỉ cũ.
                 */
                if ($isDefault) {

                    Address::query()
                        ->where(
                            'user_id',
                            $userId
                        )
                        ->update([
                            'is_default' => false,
                        ]);
                }


                /*
                |--------------------------------------------------------------------------
                | LƯU ĐỊA CHỈ
                |--------------------------------------------------------------------------
                |
                | Địa chỉ mới sử dụng:
                |
                | province_code + province
                | ward_code + ward
                |
                | district chỉ giữ để tương thích
                | với dữ liệu địa chỉ cũ.
                |
                */

                Address::create([
                    'user_id' =>
                        $userId,

                    'recipient_name' =>
                        trim(
                            $request->recipient_name
                        ),

                    'phone' =>
                        trim(
                            $request->phone
                        ),


                    /*
                     * Tỉnh / Thành phố.
                     */
                    'province' =>
                        trim(
                            $request->province
                        ),

                    'province_code' =>
                        $request->filled(
                            'province_code'
                        )
                            ? trim(
                                $request->province_code
                            )
                            : null,


                    /*
                     * Quận / Huyện.
                     *
                     * Chỉ sử dụng cho địa chỉ cũ.
                     * Địa chỉ mới có thể để NULL.
                     */
                    'district' =>
                        $request->filled(
                            'district'
                        )
                            ? trim(
                                $request->district
                            )
                            : null,


                    /*
                     * Phường / Xã / Đặc khu.
                     */
                    'ward' =>
                        trim(
                            $request->ward
                        ),

                    'ward_code' =>
                        $request->filled(
                            'ward_code'
                        )
                            ? trim(
                                $request->ward_code
                            )
                            : null,


                    'detail_address' =>
                        trim(
                            $request->detail_address
                        ),

                    'label' =>
                        $request->filled(
                            'label'
                        )
                            ? trim(
                                $request->label
                            )
                            : null,

                    'is_default' =>
                        $isDefault,
                ]);
            }
        );


        return redirect()
            ->route('addresses.index')
            ->with(
                'success',
                'Thêm địa chỉ thành công.'
            );
    }


    /**
     * Form sửa địa chỉ.
     */
    public function edit(
        Address $address
    ) {
        $this->ensureOwnership(
            $address
        );

        return view(
            'addresses.edit',
            compact('address')
        );
    }


    /**
     * Cập nhật địa chỉ.
     */
    public function update(
        AddressRequest $request,
        Address $address
    ) {
        $this->ensureOwnership(
            $address
        );


        DB::transaction(
            function () use (
                $request,
                $address
            ) {

                $isDefault =
                    $request->boolean(
                        'is_default'
                    );


                /*
                 * Nếu chọn địa chỉ này làm mặc định,
                 * bỏ trạng thái mặc định
                 * ở các địa chỉ còn lại.
                 */
                if ($isDefault) {

                    Address::query()
                        ->where(
                            'user_id',
                            auth()->id()
                        )
                        ->where(
                            'id',
                            '!=',
                            $address->id
                        )
                        ->update([
                            'is_default' => false,
                        ]);
                }


                /*
                 * Nếu địa chỉ hiện tại đang là mặc định
                 * mà Customer bỏ checkbox,
                 * vẫn giữ nó là mặc định.
                 *
                 * Nhờ vậy Customer luôn có
                 * một địa chỉ mặc định
                 * nếu còn ít nhất một địa chỉ.
                 */
                if (
                    $address->is_default
                    && !$isDefault
                ) {
                    $isDefault = true;
                }


                /*
                |--------------------------------------------------------------------------
                | CẬP NHẬT ĐỊA CHỈ
                |--------------------------------------------------------------------------
                */

                $address->update([
                    'recipient_name' =>
                        trim(
                            $request->recipient_name
                        ),

                    'phone' =>
                        trim(
                            $request->phone
                        ),


                    /*
                     * Tỉnh / Thành phố.
                     */
                    'province' =>
                        trim(
                            $request->province
                        ),

                    'province_code' =>
                        $request->filled(
                            'province_code'
                        )
                            ? trim(
                                $request->province_code
                            )
                            : null,


                    /*
                     * Quận / Huyện.
                     *
                     * Không còn bắt buộc
                     * đối với địa chỉ mới.
                     */
                    'district' =>
                        $request->filled(
                            'district'
                        )
                            ? trim(
                                $request->district
                            )
                            : null,


                    /*
                     * Phường / Xã / Đặc khu.
                     */
                    'ward' =>
                        trim(
                            $request->ward
                        ),

                    'ward_code' =>
                        $request->filled(
                            'ward_code'
                        )
                            ? trim(
                                $request->ward_code
                            )
                            : null,


                    'detail_address' =>
                        trim(
                            $request->detail_address
                        ),

                    'label' =>
                        $request->filled(
                            'label'
                        )
                            ? trim(
                                $request->label
                            )
                            : null,

                    'is_default' =>
                        $isDefault,
                ]);
            }
        );


        return redirect()
            ->route('addresses.index')
            ->with(
                'success',
                'Cập nhật địa chỉ thành công.'
            );
    }


    /**
     * Xóa địa chỉ.
     */
    public function destroy(
        Address $address
    ) {
        $this->ensureOwnership(
            $address
        );


        DB::transaction(
            function () use ($address) {

                $wasDefault =
                    $address->is_default;


                $address->delete();


                /*
                 * Nếu vừa xóa địa chỉ mặc định,
                 * lấy một địa chỉ còn lại
                 * làm địa chỉ mặc định mới.
                 */
                if ($wasDefault) {

                    $nextAddress =
                        Address::query()
                            ->where(
                                'user_id',
                                auth()->id()
                            )
                            ->latest()
                            ->first();


                    if ($nextAddress) {

                        $nextAddress->update([
                            'is_default' => true,
                        ]);
                    }
                }
            }
        );


        return redirect()
            ->route('addresses.index')
            ->with(
                'success',
                'Đã xóa địa chỉ.'
            );
    }


    /**
     * Không cho Customer sửa/xóa
     * địa chỉ của Customer khác.
     */
    private function ensureOwnership(
        Address $address
    ): void {
        abort_if(
            $address->user_id
                !== auth()->id(),
            403
        );
    }
}