function initAdminProductCreatePage() {
  const imageInput = document.querySelector("[data-product-image]");
  const preview = document.querySelector("[data-image-preview]");
  const form = document.querySelector("[data-product-create-form]");
  const draftButton = document.querySelector("[data-save-draft]");
  const toast = document.querySelector("[data-admin-toast]");
  const toastText = toast?.querySelector("span");

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

  imageInput?.addEventListener("change", () => {
    const file = imageInput.files?.[0];

    if (!file || !preview) return;

    const imageUrl = URL.createObjectURL(file);
    preview.classList.add("has-image");
    preview.style.backgroundImage = `url("${imageUrl}")`;
    preview.innerHTML = `
      <i class="fa-regular fa-image" aria-hidden="true"></i>
      <strong>${file.name}</strong>
      <span>Nhấn để thay đổi ảnh bìa sản phẩm</span>
    `;
  });

  draftButton?.addEventListener("click", () => {
    showToast("Đã lưu sản phẩm ở trạng thái nháp.");
  });

  form?.addEventListener("submit", (event) => {
    event.preventDefault();
    showToast("San pham moi da duoc xuat ban thanh cong.");
  });
}

document.addEventListener("DOMContentLoaded", initAdminProductCreatePage);


