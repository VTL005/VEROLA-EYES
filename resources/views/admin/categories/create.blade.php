@extends('layouts.admin')


@section(
    'title',
    'Thêm danh mục - VELORA Eyes'
)


@section(
    'page-title',
    'Thêm danh mục'
)


@section('content')


<div class="admin-page-header">

    <div>

        <span class="admin-page-kicker">
            CREATE CATEGORY
        </span>

        <h1>
            Thêm danh mục
        </h1>

        <p>
            Tạo nhóm sản phẩm mới
            cho VELORA Eyes.
        </p>

    </div>


    <a
        href="{{ route(
            'admin.categories.index'
        ) }}"
        class="admin-btn admin-btn-secondary"
    >
        <i class="bi bi-arrow-left"></i>

        Danh sách
    </a>

</div>



<form
    action="{{ route(
        'admin.categories.store'
    ) }}"
    method="POST"
    enctype="multipart/form-data"
    class="admin-category-form-layout"
>

    @csrf


    <div class="admin-category-form-main">

        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>

                    <h2>
                        Thông tin danh mục
                    </h2>

                    <p>
                        Slug sẽ được sinh tự động.
                    </p>

                </div>

            </div>


            <div class="admin-form-body">

                <div class="admin-form-group">

                    <label for="name">
                        Tên danh mục
                        <span>*</span>
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        maxlength="100"
                        class="admin-form-control
                            @error('name')
                                admin-input-error
                            @enderror"
                        placeholder="Ví dụ: Kính cận"
                        required
                    >

                    @error('name')
                        <div class="admin-field-error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                <div class="admin-form-group admin-category-description">

                    <label for="description">
                        Mô tả
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="6"
                        maxlength="1000"
                        class="admin-form-control
                            @error('description')
                                admin-input-error
                            @enderror"
                        placeholder="Mô tả ngắn về danh mục..."
                    >{{ old('description') }}</textarea>

                    @error('description')
                        <div class="admin-field-error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

            </div>

        </section>

    </div>



    <aside class="admin-category-form-sidebar">

        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>
                    <h2>Ảnh danh mục</h2>
                </div>

            </div>


            <div class="admin-category-upload">

                <div>
                    <i class="bi bi-image"></i>
                </div>

                <label for="image">
                    Chọn hình ảnh
                </label>

                <input
                    type="file"
                    id="image"
                    name="image"
                    accept=".jpg,.jpeg,.png,.webp"
                >

                <small>
                    JPG, PNG, WEBP. Tối đa 2MB.
                </small>

                @error('image')

                    <div class="admin-field-error">
                        {{ $message }}
                    </div>

                @enderror

            </div>

        </section>



        <section class="admin-panel">
            <div class="admin-panel-header">
                <div>
                    <h2>Cấu hình Trang chủ</h2>
                </div>
            </div>

            <div class="admin-toggle-switch" style="margin-bottom: 24px;">
                <input type="checkbox" id="is_home_featured" name="is_home_featured" value="1" {{ old('is_home_featured') ? 'checked' : '' }}>
                <label for="is_home_featured">
                    <span></span>
                    <div>
                        <strong>Danh mục chủ đạo</strong>
                        <small>Chỉ một danh mục chủ đạo. Sử dụng ảnh 9:16 ô lớn.</small>
                    </div>
                </label>
            </div>

            <div class="admin-category-upload">
                <div>
                    <i class="bi bi-image"></i>
                </div>
                <label for="homepage_image">Ảnh hiển thị trên trang chủ</label>
                <input type="file" id="homepage_image" name="homepage_image" accept=".jpg,.jpeg,.png,.webp">
                <small id="homepage_image_guide">
                    Ảnh bắt buộc theo tỷ lệ 1:1.<br>
                    Kích thước khuyến nghị: 1200 × 1200 px.<br>
                    Dung lượng tối đa: 5MB.
                </small>
                <div id="homepage_image_preview" style="display: none; margin-top: 10px; font-size: 0.85rem; padding: 10px; background: #f8f9fa; border-radius: 6px;"></div>
                @error('homepage_image')
                    <div class="admin-field-error">{{ $message }}</div>
                @enderror
            </div>
        </section>



        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>
                    <h2>Trạng thái</h2>
                </div>

            </div>


            <div class="admin-toggle-switch">

                <input
                    type="checkbox"
                    id="is_active"
                    name="is_active"
                    value="1"
                    {{
                        old(
                            'is_active',
                            true
                        )
                            ? 'checked'
                            : ''
                    }}
                >

                <label for="is_active">

                    <span></span>

                    <div>

                        <strong>
                            Đang hoạt động
                        </strong>

                        <small>
                            Cho phép sử dụng danh mục này.
                        </small>

                    </div>

                </label>

            </div>

        </section>



        <section class="admin-panel admin-form-actions">

            <button
                type="submit"
                class="admin-btn admin-btn-primary admin-btn-full"
            >
                <i class="bi bi-plus-lg"></i>

                Thêm danh mục
            </button>


            <a
                href="{{ route(
                    'admin.categories.index'
                ) }}"
                class="admin-btn admin-btn-secondary admin-btn-full"
            >
                Hủy
            </a>

        </section>

    </aside>

</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const isFeaturedCheckbox = document.getElementById('is_home_featured');
    const guideText = document.getElementById('homepage_image_guide');
    const fileInput = document.getElementById('homepage_image');
    const previewBox = document.getElementById('homepage_image_preview');

    function updateGuide() {
        if (isFeaturedCheckbox.checked) {
            guideText.innerHTML = 'Ảnh bắt buộc theo tỷ lệ 9:16.<br>Kích thước khuyến nghị: 1080 × 1920 px.<br>Dung lượng tối đa: 5MB.';
        } else {
            guideText.innerHTML = 'Ảnh bắt buộc theo tỷ lệ 1:1.<br>Kích thước khuyến nghị: 1200 × 1200 px.<br>Dung lượng tối đa: 5MB.';
        }

        // Re-trigger preview validation if an image is already selected
        if (fileInput.files && fileInput.files[0]) {
            fileInput.dispatchEvent(new Event('change'));
        }
    }

    isFeaturedCheckbox.addEventListener('change', updateGuide);
    updateGuide();

    fileInput.addEventListener('change', function(e) {
        if (!e.target.files || !e.target.files[0]) {
            previewBox.style.display = 'none';
            return;
        }

        const file = e.target.files[0];
        const img = new Image();
        const objectUrl = URL.createObjectURL(file);

        img.onload = function() {
            const width = img.naturalWidth;
            const height = img.naturalHeight;
            const ratio = width / height;

            let statusHtml = `<div style="margin-bottom: 8px;">Kích thước file: <strong>${width} × ${height} px</strong></div>`;

            const isFeatured = isFeaturedCheckbox.checked;
            const targetRatio = isFeatured ? (9 / 16) : (1 / 1);
            const diff = Math.abs(ratio - targetRatio) / targetRatio;

            if (diff <= 0.01) {
                statusHtml += `<div style="color: #10B981; margin-bottom: 12px;"><i class="bi bi-check-circle"></i> <strong>Phù hợp tỷ lệ ${isFeatured ? '9:16' : '1:1'}</strong></div>`;
            } else {
                statusHtml += `<div style="color: #EF4444; margin-bottom: 12px;"><i class="bi bi-exclamation-circle"></i> <strong>Lỗi:</strong> Ảnh sai tỷ lệ. Yêu cầu tải đúng tỷ lệ ${isFeatured ? '9:16' : '1:1'} trước khi lưu.</div>`;
            }

            statusHtml += `<div><img src="${objectUrl}" style="max-width: 100%; height: auto; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);" alt="Preview"></div>`;

            previewBox.innerHTML = statusHtml;
            previewBox.style.display = 'block';

            URL.revokeObjectURL(objectUrl);
        };
        img.src = objectUrl;
    });
});
</script>
@endpush

@endsection
