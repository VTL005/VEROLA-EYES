document.addEventListener("DOMContentLoaded", () => {
    const root = document.querySelector(".address-form-section");

    if (!root) {
        return;
    }

    const statusElement = document.getElementById("address-location-status");

    const controls = {
        province: {
            type: "province",
            input: document.getElementById("province_picker"),
            dropdown: document.getElementById("province_dropdown"),
            nameField: document.getElementById("province"),
            codeField: document.getElementById("ghn_province_id"),
            valueKey: "id",
            items: [],
            activeIndex: -1,
        },

        district: {
            type: "district",
            input: document.getElementById("district_picker"),
            dropdown: document.getElementById("district_dropdown"),
            nameField: document.getElementById("district"),
            codeField: document.getElementById("ghn_district_id"),
            valueKey: "id",
            items: [],
            activeIndex: -1,
        },

        ward: {
            type: "ward",
            input: document.getElementById("ward_picker"),
            dropdown: document.getElementById("ward_dropdown"),
            nameField: document.getElementById("ward"),
            codeField: document.getElementById("ghn_ward_code"),
            valueKey: "code",
            items: [],
            activeIndex: -1,
        },
    };

    const initialValues = {
        provinceId:
            controls.province.codeField?.dataset.selectedId ||
            controls.province.codeField?.value ||
            "",

        districtId:
            controls.district.codeField?.dataset.selectedId ||
            controls.district.codeField?.value ||
            "",

        wardCode:
            controls.ward.codeField?.dataset.selectedCode ||
            controls.ward.codeField?.value ||
            "",
    };

    const normalizeText = (value) => {
        return String(value ?? "")
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .replace(/đ/g, "d")
            .replace(/Đ/g, "D")
            .toLowerCase()
            .trim();
    };

    const setStatus = (message = "", state = "") => {
        if (!statusElement) {
            return;
        }

        statusElement.textContent = message;

        if (state) {
            statusElement.dataset.state = state;
        } else {
            delete statusElement.dataset.state;
        }
    };

    const fetchData = async (url, parameters = {}) => {
        const requestUrl = new URL(url, window.location.origin);

        Object.entries(parameters).forEach(([key, value]) => {
            requestUrl.searchParams.set(key, value);
        });

        const response = await fetch(requestUrl.toString(), {
            method: "GET",
            headers: {
                Accept: "application/json",
                "X-Requested-With": "XMLHttpRequest",
            },
            credentials: "same-origin",
        });

        const result = await response.json().catch(() => null);

        if (!response.ok) {
            throw new Error(
                result?.message || "Không thể tải dữ liệu địa chỉ GHN.",
            );
        }

        if (!Array.isArray(result?.data)) {
            throw new Error("Dữ liệu địa chỉ GHN không hợp lệ.");
        }

        return result.data;
    };

    const closeDropdown = (control) => {
        if (!control?.dropdown || !control?.input) {
            return;
        }

        control.dropdown.hidden = true;
        control.input.setAttribute("aria-expanded", "false");
        control.input
            .closest(".address-location-combobox")
            ?.classList.remove("is-open");

        control.activeIndex = -1;
    };

    const closeAllDropdowns = (exceptControl = null) => {
        Object.values(controls).forEach((control) => {
            if (control !== exceptControl) {
                closeDropdown(control);
            }
        });
    };

    const setDisabled = (control, disabled, placeholder = "") => {
        if (!control?.input) {
            return;
        }

        control.input.disabled = disabled;

        if (placeholder) {
            control.input.placeholder = placeholder;
        }

        if (disabled) {
            closeDropdown(control);
        }
    };

    const clearControl = (control, placeholder, disabled = true) => {
        control.items = [];
        control.activeIndex = -1;

        if (control.input) {
            control.input.value = "";
            control.input.setCustomValidity("");
        }

        if (control.nameField) {
            control.nameField.value = "";
        }

        if (control.codeField) {
            control.codeField.value = "";
        }

        if (control.dropdown) {
            control.dropdown.replaceChildren();
        }

        setDisabled(control, disabled, placeholder);
    };

    const getFilteredItems = (control) => {
        const keyword = normalizeText(control.input.value);

        if (!keyword) {
            return control.items;
        }

        return control.items.filter((item) => {
            return normalizeText(item.name).includes(keyword);
        });
    };

    const updateActiveOption = (control) => {
        const options = Array.from(
            control.dropdown.querySelectorAll(".address-location-option"),
        );

        options.forEach((option, index) => {
            const isActive = index === control.activeIndex;

            option.classList.toggle("is-active", isActive);
            option.setAttribute("aria-selected", isActive ? "true" : "false");
        });

        const activeOption = options[control.activeIndex];

        if (activeOption) {
            activeOption.scrollIntoView({
                block: "nearest",
            });
        }
    };

    const openDropdown = (control) => {
        if (!control?.input || !control?.dropdown || control.input.disabled) {
            return;
        }

        closeAllDropdowns(control);

        control.dropdown.hidden = false;
        control.input.setAttribute("aria-expanded", "true");
        control.input
            .closest(".address-location-combobox")
            ?.classList.add("is-open");
    };

    const applySelection = (control, item) => {
        const selectedValue = item[control.valueKey];

        control.input.value = item.name;
        control.nameField.value = item.name;
        control.codeField.value = selectedValue;
        control.input.setCustomValidity("");

        closeDropdown(control);
    };

    const loadWards = async (districtId, selectedWardCode = "") => {
        clearControl(controls.ward, "Đang tải Phường/Xã...", true);

        setStatus("Đang tải danh sách Phường/Xã...");

        try {
            const wards = await fetchData(root.dataset.ghnWardsUrl, {
                district_id: districtId,
            });

            controls.ward.items = wards.sort((first, second) => {
                return first.name.localeCompare(second.name, "vi");
            });

            setDisabled(controls.ward, false, "Chọn hoặc nhập Phường/Xã");

            if (selectedWardCode) {
                const selectedWard = controls.ward.items.find(
                    (item) => String(item.code) === String(selectedWardCode),
                );

                if (selectedWard) {
                    applySelection(controls.ward, selectedWard);
                }
            }

            setStatus("Dữ liệu địa chỉ GHN đã sẵn sàng.", "success");
        } catch (error) {
            clearControl(controls.ward, "Không thể tải Phường/Xã", true);

            setStatus(error.message, "error");
        }
    };

    const loadDistricts = async (
        provinceId,
        selectedDistrictId = "",
        selectedWardCode = "",
    ) => {
        clearControl(controls.district, "Đang tải Quận/Huyện...", true);

        clearControl(controls.ward, "Chọn Quận/Huyện trước", true);

        setStatus("Đang tải danh sách Quận/Huyện...");

        try {
            const districts = await fetchData(root.dataset.ghnDistrictsUrl, {
                province_id: provinceId,
            });

            controls.district.items = districts.sort((first, second) => {
                return first.name.localeCompare(second.name, "vi");
            });

            setDisabled(controls.district, false, "Chọn hoặc nhập Quận/Huyện");

            if (selectedDistrictId) {
                const selectedDistrict = controls.district.items.find(
                    (item) => String(item.id) === String(selectedDistrictId),
                );

                if (selectedDistrict) {
                    applySelection(controls.district, selectedDistrict);

                    await loadWards(selectedDistrict.id, selectedWardCode);

                    return;
                }
            }

            setStatus("Hãy chọn Quận/Huyện.", "success");
        } catch (error) {
            clearControl(controls.district, "Không thể tải Quận/Huyện", true);

            clearControl(controls.ward, "Chọn Quận/Huyện trước", true);

            setStatus(error.message, "error");
        }
    };

    const selectItem = async (control, item) => {
        applySelection(control, item);

        if (control.type === "province") {
            await loadDistricts(item.id);
        }

        if (control.type === "district") {
            await loadWards(item.id);
        }

        if (control.type === "ward") {
            setStatus("Đã chọn đầy đủ địa chỉ giao hàng.", "success");
        }
    };

    const renderDropdown = (control) => {
        const filteredItems = getFilteredItems(control);

        control.dropdown.replaceChildren();
        control.activeIndex = -1;

        if (filteredItems.length === 0) {
            const emptyElement = document.createElement("div");

            emptyElement.className = "address-location-empty";

            emptyElement.textContent = "Không tìm thấy địa chỉ phù hợp.";

            control.dropdown.appendChild(emptyElement);
            openDropdown(control);

            return;
        }

        filteredItems.forEach((item) => {
            const option = document.createElement("button");

            option.type = "button";
            option.className = "address-location-option";
            option.setAttribute("role", "option");
            option.setAttribute("aria-selected", "false");
            option.textContent = item.name;

            option.addEventListener("mousedown", (event) => {
                event.preventDefault();
            });

            option.addEventListener("click", async () => {
                await selectItem(control, item);
            });

            control.dropdown.appendChild(option);
        });

        openDropdown(control);
    };

    const findExactItem = (control) => {
        const keyword = normalizeText(control.input.value);

        if (!keyword) {
            return null;
        }

        return (
            control.items.find((item) => {
                return normalizeText(item.name) === keyword;
            }) ?? null
        );
    };

    const validateControl = async (control) => {
        if (!control.input.value.trim()) {
            control.input.setCustomValidity("Vui lòng chọn một địa chỉ.");

            return;
        }

        const exactItem = findExactItem(control);

        if (!exactItem) {
            control.input.setCustomValidity(
                "Vui lòng chọn một địa chỉ trong danh sách GHN.",
            );

            return;
        }

        if (
            String(control.codeField.value) !==
            String(exactItem[control.valueKey])
        ) {
            await selectItem(control, exactItem);
        }

        control.input.setCustomValidity("");
    };

    const registerControlEvents = (control) => {
        control.input.addEventListener("focus", () => {
            renderDropdown(control);
        });

        control.input.addEventListener("click", () => {
            renderDropdown(control);
        });

        control.input.addEventListener("input", () => {
            control.nameField.value = "";
            control.codeField.value = "";
            control.input.setCustomValidity("");

            if (control.type === "province") {
                clearControl(
                    controls.district,
                    "Chọn Tỉnh/Thành phố trước",
                    true,
                );

                clearControl(controls.ward, "Chọn Quận/Huyện trước", true);
            }

            if (control.type === "district") {
                clearControl(controls.ward, "Chọn Quận/Huyện trước", true);
            }

            renderDropdown(control);
        });

        control.input.addEventListener("keydown", async (event) => {
            const options = Array.from(
                control.dropdown.querySelectorAll(".address-location-option"),
            );

            if (event.key === "ArrowDown") {
                event.preventDefault();

                if (control.dropdown.hidden) {
                    renderDropdown(control);
                }

                const updatedOptions = Array.from(
                    control.dropdown.querySelectorAll(
                        ".address-location-option",
                    ),
                );

                if (updatedOptions.length === 0) {
                    return;
                }

                control.activeIndex = Math.min(
                    control.activeIndex + 1,
                    updatedOptions.length - 1,
                );

                updateActiveOption(control);
            }

            if (event.key === "ArrowUp") {
                event.preventDefault();

                if (options.length === 0) {
                    return;
                }

                control.activeIndex = Math.max(control.activeIndex - 1, 0);

                updateActiveOption(control);
            }

            if (event.key === "Enter") {
                const activeOption = control.dropdown.querySelectorAll(
                    ".address-location-option",
                )[control.activeIndex];

                if (activeOption) {
                    event.preventDefault();
                    activeOption.click();

                    return;
                }

                const exactItem = findExactItem(control);

                if (exactItem) {
                    event.preventDefault();
                    await selectItem(control, exactItem);
                }
            }

            if (event.key === "Escape") {
                closeDropdown(control);
            }
        });

        control.input.addEventListener("blur", () => {
            window.setTimeout(async () => {
                closeDropdown(control);
                await validateControl(control);
            }, 120);
        });
    };

    const loadProvinces = async () => {
        setDisabled(controls.province, true, "Đang tải Tỉnh/Thành phố...");

        setStatus("Đang tải dữ liệu địa chỉ GHN...");

        try {
            const provinces = await fetchData(root.dataset.ghnProvincesUrl);

            controls.province.items = provinces
                .filter((province) => {
                    const name = normalizeText(province.name);

                    return (
                        String(province.id) !== "2002" &&
                        !name.startsWith("test -")
                    );
                })
                .sort((first, second) => {
                    return first.name.localeCompare(second.name, "vi");
                });

            setDisabled(
                controls.province,
                false,
                "Chọn hoặc nhập Tỉnh/Thành phố",
            );

            if (initialValues.provinceId) {
                const selectedProvince = controls.province.items.find(
                    (item) =>
                        String(item.id) === String(initialValues.provinceId),
                );

                if (selectedProvince) {
                    applySelection(controls.province, selectedProvince);

                    await loadDistricts(
                        selectedProvince.id,
                        initialValues.districtId,
                        initialValues.wardCode,
                    );

                    return;
                }
            }

            setStatus("Dữ liệu địa chỉ GHN đã sẵn sàng.", "success");
        } catch (error) {
            setDisabled(
                controls.province,
                true,
                "Không thể tải Tỉnh/Thành phố",
            );

            setStatus(error.message, "error");
        }
    };

    Object.values(controls).forEach((control) => {
        if (
            control.input &&
            control.dropdown &&
            control.nameField &&
            control.codeField
        ) {
            registerControlEvents(control);
        }
    });

    document.addEventListener("mousedown", (event) => {
        if (!event.target.closest(".address-location-combobox")) {
            closeAllDropdowns();
        }
    });

    loadProvinces();
});
