document.addEventListener("DOMContentLoaded", () => {
  const mainImage = document.querySelector("#main-product-image");
  const thumbs = Array.from(document.querySelectorAll(".gallery-thumb[data-image]"));
  const thumbsTrack = document.querySelector("[data-gallery-thumbs-track]");
  const galleryNavButtons = document.querySelectorAll("[data-gallery-nav]");
  const thumbNavButtons = document.querySelectorAll("[data-thumb-nav]");
  const imageZoomOpenButtons = document.querySelectorAll("[data-image-zoom-open]");
  const imageZoomModal = document.querySelector("[data-image-zoom-modal]");
  const imageZoomTarget = document.querySelector("[data-image-zoom-target]");
  const imageZoomCloseButtons = document.querySelectorAll("[data-image-zoom-close]");
  const imageZoomActionButtons = document.querySelectorAll("[data-image-zoom-action]");
  const quantityValue = document.querySelector("#quantity-value");
  const quantityInput = document.querySelector("#quantity-input");
  const productPrice = document.querySelector("#product-price");
  const productOriginalPrice = document.querySelector("#product-original-price");
  const variantSku = document.querySelector("#variant-sku");
  const variantStock = document.querySelector("#variant-stock");
  const productTypeLabel = document.querySelector("#product-type-label");
  const addToCartButton = document.querySelector("#add-to-cart-button");
  const purchaseForm = document.querySelector(".purchase-row[data-variants]");
  const variantInput = purchaseForm?.querySelector('input[name="variant_id"]');
  const selectedColorInput = purchaseForm?.querySelector('input[name="selected_color"]');
  const selectedSizeInput = purchaseForm?.querySelector('input[name="selected_size"]');
  const colorButtons = Array.from(document.querySelectorAll(".color-option[data-color]"));
  const sizeButtons = Array.from(document.querySelectorAll(".size-option[data-size]"));
  const currencyFormatter = new Intl.NumberFormat("vi-VN");
  const currencySuffix = "\u0111";
  const orderTypeLabels = {
    ready_stock: "\u0110\u01a1n c\u00f3 s\u1eb5n",
    pre_order: "Pre-order",
    prescription: "\u0110\u01a1n c\u1eaft k\u00ednh theo toa",
  };
  const visibleThumbs = 4;
  let thumbWindowStart = 0;
  let imageZoomScale = 1;

  const variants = (() => {
    if (!purchaseForm?.dataset.variants) {
      return [];
    }

    try {
      return JSON.parse(purchaseForm.dataset.variants);
    } catch (error) {
      console.warn("Cannot parse variant data", error);
      return [];
    }
  })();

  const variantSupportsOption = (variant, type, value) => {
    if (!value) {
      return true;
    }

    const optionsKey = `${type}_options`;
    const options = Array.isArray(variant?.[optionsKey]) ? variant[optionsKey] : [];
    if (options.length > 0) {
      return options.includes(value);
    }

    return (variant?.[type] || "") === value;
  };

  const getActiveColor = () => document.querySelector(".color-option.is-active")?.dataset.color || "";
  const getActiveSize = () => document.querySelector(".size-option.is-active")?.dataset.size || "";

  const findVariant = () => {
    if (variants.length === 0) {
      return null;
    }

    const activeColor = getActiveColor();
    const activeSize = getActiveSize();

    const variant =
      variants.find((item) => {
        const colorMatched = variantSupportsOption(item, "color", activeColor);
        const sizeMatched = variantSupportsOption(item, "size", activeSize);
        return colorMatched && sizeMatched;
      }) ||
      variants.find((item) => variantSupportsOption(item, "color", activeColor)) ||
      variants.find((item) => variantSupportsOption(item, "size", activeSize)) ||
      variants[0];

    return variant || null;
  };

  const getThumbStepWidth = () => {
    if (!thumbsTrack || thumbs.length === 0) {
      return 0;
    }

    const trackStyles = window.getComputedStyle(thumbsTrack);
    const gap = Number.parseFloat(trackStyles.columnGap || trackStyles.gap || "0") || 0;
    return thumbs[0].getBoundingClientRect().width + gap;
  };

  const updateThumbTrack = () => {
    if (!thumbsTrack) {
      return;
    }

    const maxStart = Math.max(0, thumbs.length - visibleThumbs);
    thumbWindowStart = Math.min(Math.max(0, thumbWindowStart), maxStart);
    thumbsTrack.style.transform = `translateX(-${thumbWindowStart * getThumbStepWidth()}px)`;

    thumbNavButtons.forEach((button) => {
      button.disabled =
        thumbs.length <= visibleThumbs ||
        (button.dataset.thumbNav === "prev" && thumbWindowStart <= 0) ||
        (button.dataset.thumbNav === "next" && thumbWindowStart >= maxStart);
    });
  };

  const ensureThumbVisible = (index) => {
    if (index < 0 || thumbs.length <= visibleThumbs) {
      updateThumbTrack();
      return;
    }

    if (index < thumbWindowStart) {
      thumbWindowStart = index;
    } else if (index >= thumbWindowStart + visibleThumbs) {
      thumbWindowStart = index - visibleThumbs + 1;
    }

    updateThumbTrack();
  };

  const setImageZoomScale = (scale) => {
    imageZoomScale = Math.min(Math.max(scale, 1), 3);

    if (imageZoomTarget) {
      imageZoomTarget.style.transform = `scale(${imageZoomScale})`;
      imageZoomTarget.style.cursor = imageZoomScale >= 3 ? "zoom-out" : "zoom-in";
    }
  };

  const openImageZoom = () => {
    if (!imageZoomModal || !imageZoomTarget || !mainImage) {
      return;
    }

    imageZoomTarget.src = mainImage.currentSrc || mainImage.src;
    imageZoomTarget.alt = mainImage.alt || "";
    imageZoomModal.hidden = false;
    document.body.classList.add("image-zoom-open");
    setImageZoomScale(1);
  };

  const closeImageZoom = () => {
    if (!imageZoomModal) {
      return;
    }

    imageZoomModal.hidden = true;
    document.body.classList.remove("image-zoom-open");
    setImageZoomScale(1);
  };

  const syncVariant = () => {
    const activeColor = getActiveColor();
    const activeSize = getActiveSize();
    const variant = findVariant();
    if (!variant) {
      return;
    }

    if (variantInput) {
      variantInput.value = variant.id;
    }

    if (selectedColorInput) {
      selectedColorInput.value = activeColor;
    }

    if (selectedSizeInput) {
      selectedSizeInput.value = activeSize;
    }

    if (productPrice) {
      productPrice.textContent = `${currencyFormatter.format(Number(variant.price) || 0)}${currencySuffix}`;
    }

    if (productOriginalPrice) {
      const originalPrice = Number(variant.original_price) || 0;
      const currentPrice = Number(variant.price) || 0;
      const hasSalePrice = originalPrice > currentPrice;

      productOriginalPrice.textContent = hasSalePrice
        ? `${currencyFormatter.format(originalPrice)}${currencySuffix}`
        : "";
      productOriginalPrice.classList.toggle("is-hidden", !hasSalePrice);
    }

    if (variantSku) {
      variantSku.textContent = variant.sku || "-";
    }

    if (variantStock) {
      variantStock.textContent = String(variant.stock_quantity ?? 0);
    }

    if (productTypeLabel) {
      productTypeLabel.textContent =
        orderTypeLabels[variant.product_type] || variant.product_type || "Ch\u01b0a x\u00e1c \u0111\u1ecbnh";
    }

    if (mainImage && variant.image_url) {
      mainImage.src = variant.image_url;
    }

    thumbs.forEach((thumb) => {
      thumb.classList.toggle(
        "is-active",
        thumb.dataset.variantId === variant.id ||
          (!thumb.dataset.variantId && thumb.dataset.image === variant.image_url)
      );
    });

    ensureThumbVisible(thumbs.findIndex((thumb) => thumb.classList.contains("is-active")));

    if (addToCartButton) {
      const outOfStock =
        variant.product_type === "ready_stock" &&
        Number(variant.stock_quantity || 0) <= 0;
      addToCartButton.disabled = outOfStock;
      addToCartButton.textContent = outOfStock
        ? "T\u1ea1m h\u1ebft h\u00e0ng"
        : "Th\u00eam v\u00e0o gi\u1ecf h\u00e0ng";
    }
  };

  const showGalleryImage = (thumb) => {
    if (!thumb) {
      return;
    }

    thumbs.forEach((item) => item.classList.remove("is-active"));
    thumb.classList.add("is-active");
    ensureThumbVisible(thumbs.indexOf(thumb));

    if (mainImage) {
      mainImage.src = thumb.dataset.image || mainImage.src;
    }
  };

  thumbs.forEach((thumb) => {
    thumb.addEventListener("click", () => {
      showGalleryImage(thumb);
    });
  });

  mainImage?.addEventListener("click", openImageZoom);

  imageZoomOpenButtons.forEach((button) => {
    button.addEventListener("click", openImageZoom);
  });

  imageZoomCloseButtons.forEach((button) => {
    button.addEventListener("click", closeImageZoom);
  });

  imageZoomActionButtons.forEach((button) => {
    button.addEventListener("click", () => {
      const action = button.dataset.imageZoomAction;

      if (action === "in") {
        setImageZoomScale(imageZoomScale + 0.25);
      } else if (action === "out") {
        setImageZoomScale(imageZoomScale - 0.25);
      } else {
        setImageZoomScale(1);
      }
    });
  });

  imageZoomTarget?.addEventListener("click", () => {
    setImageZoomScale(imageZoomScale >= 3 ? 1 : imageZoomScale + 0.5);
  });

  galleryNavButtons.forEach((button) => {
    button.disabled = thumbs.length <= 1;

    button.addEventListener("click", () => {
      if (thumbs.length <= 1) {
        return;
      }

      const activeIndex = Math.max(
        0,
        thumbs.findIndex((thumb) => thumb.classList.contains("is-active"))
      );
      const step = button.dataset.galleryNav === "next" ? 1 : -1;
      const nextIndex = (activeIndex + step + thumbs.length) % thumbs.length;
      showGalleryImage(thumbs[nextIndex]);
    });
  });

  thumbNavButtons.forEach((button) => {
    button.addEventListener("click", () => {
      const maxStart = Math.max(0, thumbs.length - visibleThumbs);
      const step = button.dataset.thumbNav === "next" ? 1 : -1;
      thumbWindowStart = Math.min(Math.max(0, thumbWindowStart + step), maxStart);
      updateThumbTrack();
    });
  });

  window.addEventListener("resize", updateThumbTrack);
  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape" && imageZoomModal && !imageZoomModal.hidden) {
      closeImageZoom();
    }
  });

  document.querySelectorAll(".color-option").forEach((button) => {
    button.addEventListener("click", () => {
      colorButtons.forEach((item) => item.classList.remove("is-active"));
      button.classList.add("is-active");
      syncVariant();
    });
  });

  document.querySelectorAll(".size-option").forEach((button) => {
    button.addEventListener("click", () => {
      sizeButtons.forEach((item) => item.classList.remove("is-active"));
      button.classList.add("is-active");
      syncVariant();
    });
  });

  document.querySelectorAll("[data-qty]").forEach((button) => {
    button.addEventListener("click", () => {
      if (!quantityValue) {
        return;
      }

      const current = Number(quantityValue.textContent) || 1;
      const nextValue =
        button.dataset.qty === "increase"
          ? current + 1
          : Math.max(1, current - 1);

      quantityValue.textContent = String(nextValue);
      if (quantityInput) {
        quantityInput.value = String(nextValue);
      }
    });
  });

  syncVariant();
  updateThumbTrack();
});
