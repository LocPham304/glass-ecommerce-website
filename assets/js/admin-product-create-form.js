function initAdminProductCreateForm() {
  const form = document.querySelector("[data-product-create-form]");
  const draftButton = document.querySelector("[data-save-draft]");
  const submitModeInput = document.querySelector("[data-submit-mode]");
  const imageInput = document.querySelector("[data-product-image]");
  const imageUrlInput = null;
  const preview = document.querySelector("[data-image-preview]");
  const gallery = document.querySelector("[data-image-gallery]");

  if (!form || !preview || !submitModeInput) {
    return;
  }

  const resetPreview = (title, subtitle) => {
    preview.classList.remove("has-image");
    preview.style.backgroundImage = "";
    preview.innerHTML = `
      <i class="fa-regular fa-image" aria-hidden="true"></i>
      <strong>${title}</strong>
      <span>${subtitle}</span>
    `;
  };

  const setImagePreview = (imageUrl, title, subtitle) => {
    preview.classList.add("has-image");
    preview.style.backgroundImage = `url("${imageUrl}")`;
    preview.innerHTML = `
      <i class="fa-regular fa-image" aria-hidden="true"></i>
      <strong>${title}</strong>
      <span>${subtitle}</span>
    `;
  };

  const renderGallery = (items) => {
    if (!gallery) {
      return;
    }

    if (!items.length) {
      gallery.innerHTML = "";
      return;
    }

    gallery.innerHTML = items
      .map(
        (item, index) => `
          <figure class="product-image-gallery__item">
            <img src="${item.src}" alt="Anh san pham ${index + 1}" />
            <figcaption>${index === 0 ? "Anh bia" : `Anh ${index + 1}`}</figcaption>
          </figure>
        `
      )
      .join("");
  };

  imageInput?.addEventListener("change", () => {
    const files = Array.from(imageInput.files || []);
    if (files.length > 10) {
      window.alert("Moi san pham chi duoc tai toi da 10 anh.");
      imageInput.value = "";
      resetPreview("Chon anh san pham", "Keo tha hoac nhan de tai len toi da 10 anh");
      renderGallery([]);
      return;
    }

    if (!files.length) {
      resetPreview("Chon anh san pham", "Keo tha hoac nhan de tai len toi da 10 anh");
      renderGallery([]);
      return;
      if (imageUrlInput?.value.trim()) {
        setImagePreview(imageUrlInput.value.trim(), "Ảnh từ URL", "Đang dùng ảnh bìa từ đường dẫn bạn nhập");
      } else {
        resetPreview("Chọn ảnh sản phẩm", "Kéo thả hoặc nhấn để tải ảnh lên");
      }
      return;
    }

    const previewItems = files.map((file) => ({
      src: URL.createObjectURL(file),
      name: file.name,
    }));
    setImagePreview(previewItems[0].src, `${files.length} anh duoc chon`, "Anh dau tien se duoc dung lam anh bia");
    renderGallery(previewItems);
    return;

    const fileUrl = URL.createObjectURL(file);
    setImagePreview(fileUrl, file.name, "Nhấn để thay đổi ảnh bìa sản phẩm");
  });

  imageUrlInput?.addEventListener("input", () => {
    const imageUrl = imageUrlInput.value.trim();
    if (!imageUrl || imageInput?.files?.length) {
      return;
    }

    setImagePreview(imageUrl, "Ảnh từ URL", "Đang dùng ảnh bìa từ đường dẫn bạn nhập");
  });

  draftButton?.addEventListener("click", () => {
    submitModeInput.value = "draft";
    form.requestSubmit();
  });

  form.addEventListener("submit", (event) => {
    const submitter = event.submitter;
    if (submitter !== draftButton) {
      submitModeInput.value = "publish";
    }
  });
}

document.addEventListener("DOMContentLoaded", initAdminProductCreateForm);