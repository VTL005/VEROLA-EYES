document.addEventListener("DOMContentLoaded", () => {
    const provinceSelect = document.getElementById("province_code");

    const wardSelect = document.getElementById("ward_code");

    const provinceInput = document.getElementById("province");

    const wardInput = document.getElementById("ward");

    const statusElement = document.getElementById("address-location-status");

    /*
    |--------------------------------------------------------------------------
    | CHỈ CHẠY TRÊN FORM ĐỊA CHỈ
    |--------------------------------------------------------------------------
    */

    if (!provinceSelect || !wardSelect || !provinceInput || !wardInput) {
        return;
    }

    /*
    |--------------------------------------------------------------------------
    | PROVINCE OPEN API V2
    |--------------------------------------------------------------------------
    */

    const API_BASE = "https://provinces.open-api.vn/api/v2";

    /*
    |--------------------------------------------------------------------------
    | GIÁ TRỊ CŨ / GIÁ TRỊ SAU VALIDATION
    |--------------------------------------------------------------------------
    |
    | Code:
    | dùng cho địa chỉ mới.
    |
    | Name:
    | dùng fallback cho địa chỉ cũ
    | chưa có province_code / ward_code.
    |
    */

    const selectedProvinceCode = provinceSelect.dataset.selectedCode || "";

    const selectedProvinceName = provinceSelect.dataset.selectedName || "";

    const selectedWardCode = wardSelect.dataset.selectedCode || "";

    const selectedWardName = wardSelect.dataset.selectedName || "";

    /**
     * Hiển thị trạng thái tải dữ liệu.
     */
    const showStatus = (message = "", isError = false) => {
        if (!statusElement) {
            return;
        }

        statusElement.textContent = message;

        statusElement.dataset.state = isError ? "error" : "normal";
    };

    /**
     * Tạo option.
     */
    const createOption = (value, label, name = "") => {
        const option = document.createElement("option");

        option.value = value;
        option.textContent = label;

        if (name) {
            option.dataset.name = name;
        }

        return option;
    };

    /**
     * Gọi API.
     */
    const fetchJson = async (url) => {
        const response = await fetch(url, {
            headers: {
                Accept: "application/json",
            },
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        return response.json();
    };

    /**
     * Tìm option theo tên.
     *
     * Dùng cho địa chỉ cũ chưa lưu code.
     */
    const findOptionByName = (select, name) => {
        if (!name) {
            return null;
        }

        const normalizedName = name.trim().toLocaleLowerCase("vi");

        return (
            [...select.options].find((option) => {
                const optionName = (
                    option.dataset.name ||
                    option.textContent ||
                    ""
                )
                    .trim()
                    .toLocaleLowerCase("vi");

                return optionName === normalizedName;
            }) || null
        );
    };

    /**
     * Reset Phường/Xã.
     */
    const resetWards = (message = "Chọn Tỉnh/Thành phố trước") => {
        wardSelect.innerHTML = "";

        wardSelect.appendChild(createOption("", message));

        wardSelect.disabled = true;

        wardInput.value = "";
    };

    /**
     * Load Phường/Xã/Đặc khu.
     */
    const loadWards = async (
        provinceCode,
        preferredWardCode = "",
        preferredWardName = "",
    ) => {
        if (!provinceCode) {
            resetWards();

            return;
        }

        wardSelect.disabled = true;

        wardSelect.innerHTML = "";

        wardSelect.appendChild(createOption("", "Đang tải Phường/Xã..."));

        showStatus("Đang tải danh sách Phường/Xã/Đặc khu...");

        try {
            const wards = await fetchJson(
                `${API_BASE}/w/?province=${encodeURIComponent(provinceCode)}`,
            );

            wardSelect.innerHTML = "";

            wardSelect.appendChild(createOption("", "Chọn Phường/Xã/Đặc khu"));

            const sortedWards = Array.isArray(wards)
                ? [...wards].sort((a, b) => a.name.localeCompare(b.name, "vi"))
                : [];

            sortedWards.forEach((ward) => {
                wardSelect.appendChild(
                    createOption(String(ward.code), ward.name, ward.name),
                );
            });

            /*
            |--------------------------------------------------------------------------
            | KHÔI PHỤC WARD
            |--------------------------------------------------------------------------
            */

            let selectedOption = null;

            /*
             * Ưu tiên code.
             */
            if (preferredWardCode) {
                selectedOption =
                    [...wardSelect.options].find(
                        (option) => option.value === String(preferredWardCode),
                    ) || null;
            }

            /*
             * Nếu địa chỉ cũ chưa có code,
             * thử tìm theo tên.
             */
            if (!selectedOption && preferredWardName) {
                selectedOption = findOptionByName(
                    wardSelect,
                    preferredWardName,
                );
            }

            if (selectedOption) {
                wardSelect.value = selectedOption.value;

                wardInput.value =
                    selectedOption.dataset.name || selectedOption.textContent;
            }

            wardSelect.disabled = false;

            showStatus("");
        } catch (error) {
            console.error("Không tải được Phường/Xã:", error);

            wardSelect.innerHTML = "";

            wardSelect.appendChild(createOption("", "Không tải được dữ liệu"));

            wardSelect.disabled = true;

            showStatus(
                "Không thể tải danh sách Phường/Xã. Vui lòng thử lại.",
                true,
            );
        }
    };

    /**
     * Load danh sách Tỉnh/Thành phố.
     */
    const loadProvinces = async () => {
        provinceSelect.disabled = true;

        provinceSelect.innerHTML = "";

        provinceSelect.appendChild(
            createOption("", "Đang tải Tỉnh/Thành phố..."),
        );

        resetWards();

        showStatus("Đang tải danh sách Tỉnh/Thành phố...");

        try {
            const provinces = await fetchJson(`${API_BASE}/p/`);

            provinceSelect.innerHTML = "";

            provinceSelect.appendChild(createOption("", "Chọn Tỉnh/Thành phố"));

            const sortedProvinces = Array.isArray(provinces)
                ? [...provinces].sort((a, b) =>
                      a.name.localeCompare(b.name, "vi"),
                  )
                : [];

            sortedProvinces.forEach((province) => {
                provinceSelect.appendChild(
                    createOption(
                        String(province.code),
                        province.name,
                        province.name,
                    ),
                );
            });

            provinceSelect.disabled = false;

            /*
            |--------------------------------------------------------------------------
            | KHÔI PHỤC PROVINCE
            |--------------------------------------------------------------------------
            */

            let selectedOption = null;

            /*
             * Địa chỉ mới:
             * ưu tiên province_code.
             */
            if (selectedProvinceCode) {
                selectedOption =
                    [...provinceSelect.options].find(
                        (option) =>
                            option.value === String(selectedProvinceCode),
                    ) || null;
            }

            /*
             * Địa chỉ cũ:
             * chưa có province_code.
             * Tìm theo tên province.
             */
            if (!selectedOption && selectedProvinceName) {
                selectedOption = findOptionByName(
                    provinceSelect,
                    selectedProvinceName,
                );
            }

            if (selectedOption) {
                provinceSelect.value = selectedOption.value;

                provinceInput.value =
                    selectedOption.dataset.name || selectedOption.textContent;

                await loadWards(
                    selectedOption.value,
                    selectedWardCode,
                    selectedWardName,
                );
            }

            showStatus("");
        } catch (error) {
            console.error("Không tải được Tỉnh/Thành phố:", error);

            provinceSelect.innerHTML = "";

            provinceSelect.appendChild(
                createOption("", "Không tải được dữ liệu"),
            );

            provinceSelect.disabled = true;

            showStatus(
                "Không thể tải danh sách địa chỉ. Vui lòng kiểm tra kết nối Internet và thử lại.",
                true,
            );
        }
    };

    /*
    |--------------------------------------------------------------------------
    | THAY ĐỔI TỈNH / THÀNH
    |--------------------------------------------------------------------------
    */

    provinceSelect.addEventListener("change", async () => {
        const option = provinceSelect.selectedOptions[0];

        provinceInput.value = option?.dataset?.name || "";

        wardInput.value = "";

        await loadWards(provinceSelect.value);
    });

    /*
    |--------------------------------------------------------------------------
    | THAY ĐỔI PHƯỜNG / XÃ
    |--------------------------------------------------------------------------
    */

    wardSelect.addEventListener("change", () => {
        const option = wardSelect.selectedOptions[0];

        wardInput.value = option?.dataset?.name || "";
    });

    /*
    |--------------------------------------------------------------------------
    | INIT
    |--------------------------------------------------------------------------
    */

    loadProvinces();
});
