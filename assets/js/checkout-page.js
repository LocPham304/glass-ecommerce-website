const PROVINCES_API_BASE_URL = "https://provinces.open-api.vn/api/v2";
const DEFAULT_ADDRESS_HELPER =
  "Dữ liệu địa giới hành chính được tải từ Provinces Open API theo bản sau sáp nhập.";

function normalizeAddressValue(value) {
  return String(value || "")
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "")
    .toLowerCase()
    .replace(/\b(tinh|thanh pho|quan|huyen|thi xa|thi tran|phuong|xa|dac khu)\b/g, "")
    .replace(/[^a-z0-9]/g, "");
}

function formatCurrency(value) {
  return `${Number(value || 0).toLocaleString("vi-VN")}đ`;
}

async function fetchJson(url, options = {}) {
  const response = await fetch(url, {
    headers: {
      Accept: "application/json",
      ...(options.headers || {}),
    },
    ...options,
  });

  const payload = await response.json().catch(() => ({}));

  if (!response.ok) {
    throw new Error(payload.message || `Request failed with status ${response.status}`);
  }

  return payload;
}

function fillSelectOptions(select, options, placeholder, selectedValue = "") {
  const selectedKey = normalizeAddressValue(selectedValue);
  const optionMarkup = options
    .map(
      (option) =>
        `<option value="${option.name}" data-code="${option.code ?? ""}">${option.name}</option>`
    )
    .join("");

  select.innerHTML = `<option value="">${placeholder}</option>${optionMarkup}`;

  if (!selectedKey) {
    select.value = "";
    return null;
  }

  const matchedOption = options.find(
    (option) => normalizeAddressValue(option.name) === selectedKey
  );

  if (!matchedOption) {
    select.value = "";
    return null;
  }

  select.value = matchedOption.name;
  return matchedOption;
}

function initPaymentOptions() {
  document.querySelectorAll('.payment-option input[type="radio"]').forEach((input) => {
    input.addEventListener("change", () => {
      const groupName = input.name;

      document
        .querySelectorAll(`.payment-option input[type="radio"][name="${groupName}"]`)
        .forEach((groupInput) => {
          groupInput.closest(".payment-option")?.classList.toggle("is-active", groupInput.checked);
        });
    });
  });
}

async function initShippingAddress() {
  const provinceSelect = document.querySelector("[data-address-province]");
  const wardSelect = document.querySelector("[data-address-ward]");
  const districtInput = document.querySelector("[data-address-district]");
  const helperText = document.querySelector("[data-address-helper]");

  if (!provinceSelect || !wardSelect || !districtInput) {
    return;
  }

  const selectedProvinceName = provinceSelect.dataset.selected || "";
  const selectedWardName = wardSelect.dataset.selected || "";

  let provinces = [];
  let legacyWardMatch = null;

  const updateHelper = (message, isError = false) => {
    if (!helperText) {
      return;
    }

    helperText.textContent = message;
    helperText.classList.toggle("is-error", isError);
  };

  const loadWards = async (provinceCode, preferredWardName = "") => {
    wardSelect.disabled = true;
    wardSelect.innerHTML = '<option value="">Đang tải phường / xã...</option>';

    try {
      const province = await fetchJson(`${PROVINCES_API_BASE_URL}/p/${provinceCode}?depth=2`);
      const wards = Array.isArray(province.wards) ? province.wards : [];
      const matchedWard = fillSelectOptions(
        wardSelect,
        wards,
        "Chọn phường / xã",
        preferredWardName
      );

      wardSelect.disabled = false;
      districtInput.value = "";

      if (!matchedWard && preferredWardName) {
        updateHelper(
          "Địa chỉ cũ đã được nạp một phần. Vui lòng chọn lại phường / xã theo địa giới mới."
        );
      } else {
        updateHelper(DEFAULT_ADDRESS_HELPER);
      }
    } catch (error) {
      wardSelect.innerHTML = '<option value="">Không tải được phường / xã</option>';
      updateHelper(
        "Không thể tải danh sách phường / xã từ Provinces Open API. Vui lòng thử lại sau.",
        true
      );
    }
  };

  try {
    provinces = await fetchJson(`${PROVINCES_API_BASE_URL}/p`);
  } catch (error) {
    provinceSelect.innerHTML = '<option value="">Không tải được tỉnh / thành phố</option>';
    wardSelect.innerHTML = '<option value="">Không tải được phường / xã</option>';
    updateHelper(
      "Không thể tải dữ liệu địa chỉ từ Provinces Open API. Vui lòng thử lại sau.",
      true
    );
    return;
  }

  let matchedProvince = fillSelectOptions(
    provinceSelect,
    provinces,
    "Chọn tỉnh / thành phố",
    selectedProvinceName
  );

  if (!matchedProvince && selectedWardName) {
    try {
      const legacyMatches = await fetchJson(
        `${PROVINCES_API_BASE_URL}/w/from-legacy/?legacy_name=${encodeURIComponent(selectedWardName)}`
      );

      legacyWardMatch = Array.isArray(legacyMatches) ? legacyMatches[0] : null;

      if (legacyWardMatch?.ward?.province_code) {
        matchedProvince =
          provinces.find(
            (province) => Number(province.code) === Number(legacyWardMatch.ward.province_code)
          ) || null;

        if (matchedProvince) {
          provinceSelect.value = matchedProvince.name;
        }
      }
    } catch (error) {
      legacyWardMatch = null;
    }
  }

  districtInput.value = "";

  if (matchedProvince) {
    await loadWards(matchedProvince.code, legacyWardMatch?.ward?.name || selectedWardName);
  } else {
    wardSelect.disabled = true;
    wardSelect.innerHTML = '<option value="">Vui lòng chọn tỉnh / thành phố trước</option>';
  }

  provinceSelect.addEventListener("change", async () => {
    const selectedOption = provinceSelect.options[provinceSelect.selectedIndex];
    const provinceCode = selectedOption?.dataset.code;

    districtInput.value = "";

    if (!provinceCode) {
      wardSelect.disabled = true;
      wardSelect.innerHTML = '<option value="">Vui lòng chọn tỉnh / thành phố trước</option>';
      return;
    }

    await loadWards(provinceCode);
  });
}

function initVoucherPreview() {
  const applyButton = document.querySelector("[data-apply-voucher]");
  const voucherInput = document.querySelector("[data-voucher-code]");
  const feedback = document.querySelector("[data-voucher-feedback]");
  const subtotalSource = document.querySelector("[data-checkout-subtotal]");
  const discountRow = document.querySelector("[data-summary-discount-row]");
  const discountValue = document.querySelector("[data-summary-discount]");
  const totalValue = document.querySelector("[data-summary-total]");

  if (!applyButton || !voucherInput || !subtotalSource || !discountRow || !discountValue || !totalValue) {
    return;
  }

  const previewUrl = subtotalSource.dataset.voucherPreviewUrl;
  const subtotal = Number(subtotalSource.dataset.checkoutSubtotal || 0);

  const setFeedback = (message, isError = false) => {
    if (!feedback) {
      return;
    }

    feedback.hidden = !message;
    feedback.textContent = message;
    feedback.classList.toggle("is-error", isError);
    feedback.classList.toggle("is-success", !isError && Boolean(message));
  };

  const resetSummary = () => {
    discountRow.hidden = true;
    discountValue.textContent = "-0đ";
    totalValue.textContent = formatCurrency(subtotal);
  };

  applyButton.addEventListener("click", async () => {
    const voucherCode = voucherInput.value.trim();

    if (!voucherCode) {
      resetSummary();
      setFeedback("Vui lòng nhập mã giảm giá.", true);
      return;
    }

    applyButton.disabled = true;

    try {
      const body = new URLSearchParams({
        voucher_code: voucherCode,
        subtotal: String(subtotal),
      });

      const result = await fetchJson(previewUrl, {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
        },
        body: body.toString(),
      });

      discountRow.hidden = false;
      discountValue.textContent = `-${formatCurrency(result.data.discount_amount)}`;
      totalValue.textContent = formatCurrency(result.data.total_amount);
      voucherInput.value = result.data.code || voucherCode;
      setFeedback(result.message || "Áp dụng mã giảm giá thành công.");
    } catch (error) {
      resetSummary();
      setFeedback(error.message || "Không thể áp dụng mã giảm giá.", true);
    } finally {
      applyButton.disabled = false;
    }
  });
}

function initPrescriptionUpload() {
  const fileInput = document.querySelector("[data-prescription-input]");
  const triggerButton = document.querySelector("[data-prescription-trigger]");
  const fileNameLabel = document.querySelector("[data-prescription-filename]");

  if (!fileInput || !triggerButton || !fileNameLabel) {
    return;
  }

  triggerButton.addEventListener("click", () => {
    fileInput.click();
  });

  fileInput.addEventListener("change", () => {
    const file = fileInput.files?.[0];
    fileNameLabel.textContent = file ? `Đã chọn: ${file.name}` : "Chưa chọn tệp nào.";
  });
}

function initCheckoutPage() {
  initPaymentOptions();
  initShippingAddress();
  initVoucherPreview();
  initPrescriptionUpload();
}

document.addEventListener("DOMContentLoaded", initCheckoutPage);