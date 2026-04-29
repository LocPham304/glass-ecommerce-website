function initAdminPromotionCreatePage() {
  const form = document.querySelector("[data-promotion-create-form]");
  const draftButton = document.querySelector("[data-save-promo-draft]");
  const typeSelect = document.querySelector("[data-discount-type]");
  const valueInput = document.querySelector("[data-discount-value]");
  const maxInput = document.querySelector("[data-max-discount]");
  const toast = document.querySelector("[data-admin-toast]");
  const toastText = toast?.querySelector("span");
  const codePreview = document.querySelector("[data-preview-code]");
  const titlePreview = document.querySelector("[data-preview-title]");
  const valuePreview = document.querySelector("[data-preview-value]");
  const maxPreview = document.querySelector("[data-preview-max]");
  const codeInput = form?.querySelector('input[name="code"]');
  const titleInput = form?.querySelector('input[name="name"]');

  let toastTimeout;

  const showToast = (message) => {
    if (!toast || !toastText) return;
    toastText.textContent = message;
    toast.hidden = false;

    window.clearTimeout(toastTimeout);
    toastTimeout = window.setTimeout(() => {
      toast.hidden = true;
    }, 2200);
  };

  const updatePreview = () => {
    const code = codeInput?.value.trim() || "COMBO20";
    const title = titleInput?.value.trim() || "Giảm 20% cho combo gọng + tròng";
    const type = typeSelect?.value || "percent";
    const value = valueInput?.value.trim() || "20";
    const max = maxInput?.value.trim() || "150000";

    if (codePreview) codePreview.textContent = code.toUpperCase();
    if (titlePreview) titlePreview.textContent = title;

    if (valuePreview) {
      if (type === "cash") {
        valuePreview.textContent = `Giảm giá: ${Number(value || 0).toLocaleString("vi-VN")}đ`;
      } else if (type === "ship") {
        valuePreview.textContent = "Giảm giá: Miễn phí vận chuyển";
      } else {
        valuePreview.textContent = `Giảm giá: ${value}%`;
      }
    }

    if (maxPreview) {
      maxPreview.textContent =
        type === "percent"
          ? `Tối đa: ${Number(max || 0).toLocaleString("vi-VN")}đ`
          : "Áp dụng tại trang thanh toán";
    }
  };

  [codeInput, titleInput, typeSelect, valueInput, maxInput].forEach((field) => {
    field?.addEventListener("input", updatePreview);
    field?.addEventListener("change", updatePreview);
  });

  draftButton?.addEventListener("click", () => {
    showToast("Đã lưu voucher ở trạng thái nháp.");
  });

  form?.addEventListener("submit", () => {
    showToast("Voucher mới đã được phát hành thành công.");
  });

  updatePreview();
}

document.addEventListener("DOMContentLoaded", initAdminPromotionCreatePage);