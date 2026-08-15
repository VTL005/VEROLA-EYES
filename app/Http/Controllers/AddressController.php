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
        DB::transaction(function () use ($request) {

            $userId = auth()->id();

            /*
             * Nếu Customer chưa có địa chỉ nào,
             * địa chỉ đầu tiên tự động là mặc định.
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
             * Nếu địa chỉ mới được đặt mặc định,
             * bỏ mặc định ở các địa chỉ cũ.
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


            Address::create([
                'user_id' =>
                    $userId,

                'recipient_name' =>
                    $request->recipient_name,

                'phone' =>
                    $request->phone,

                'province' =>
                    $request->province,

                'district' =>
                    $request->district,

                'ward' =>
                    $request->ward,

                'detail_address' =>
                    $request->detail_address,

                'label' =>
                    $request->label,

                'is_default' =>
                    $isDefault,
            ]);
        });


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
                 * bỏ mặc định ở các địa chỉ còn lại.
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
                            'is_default' =>
                                false,
                        ]);
                }


                /*
                 * Nếu địa chỉ hiện đang mặc định
                 * mà user bỏ checkbox,
                 * ta vẫn giữ nó là mặc định
                 * để luôn có 1 địa chỉ mặc định.
                 */
                if (
                    $address->is_default
                    && !$isDefault
                ) {
                    $isDefault = true;
                }


                $address->update([
                    'recipient_name' =>
                        $request->recipient_name,

                    'phone' =>
                        $request->phone,

                    'province' =>
                        $request->province,

                    'district' =>
                        $request->district,

                    'ward' =>
                        $request->ward,

                    'detail_address' =>
                        $request->detail_address,

                    'label' =>
                        $request->label,

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
                 * làm mặc định.
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
                            'is_default' =>
                                true,
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