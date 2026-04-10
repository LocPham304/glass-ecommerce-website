const relatedProducts = [
  {
    name: "Classic Matte Black",
    price: "1.650.000đ",
    image:
      "https://images.unsplash.com/photo-1572635196237-14b3f281503f?auto=format&fit=crop&w=720&q=80",
  },
  {
    name: "Aviator Gold Edition",
    price: "2.450.000đ",
    image:
      "https://www.figma.com/api/mcp/asset/a388cb74-5124-4c04-93e2-ea6731a3e569",
  },
  {
    name: "Round Vintage Tortoise",
    price: "1.200.000đ",
    image:
      "https://images.unsplash.com/photo-1511499767150-a48a237f0083?auto=format&fit=crop&w=720&q=80",
  },
  {
    name: "Crystal Clear Tech",
    price: "2.000.000đ",
    image:
      "https://images.unsplash.com/photo-1591076482161-42ce6da69f67?auto=format&fit=crop&w=720&q=80",
  },
];

function renderRelatedProducts() {
  const grid = document.querySelector("#related-grid");
  if (!grid) {
    return;
  }

  grid.innerHTML = relatedProducts
    .map(
      (product) => `
        <article class="related-card">
          <div class="related-card__media">
            <img src="${product.image}" alt="${product.name}">
          </div>
          <div class="related-card__body">
            <h3 class="related-card__name">${product.name}</h3>
            <p class="related-card__price">${product.price}</p>
          </div>
        </article>
      `,
    )
    .join("");
}

function bindGallery() {
  const mainImage = document.querySelector("#main-product-image");
  const thumbs = document.querySelectorAll(".gallery-thumb[data-image]");
  if (!mainImage || thumbs.length === 0) {
    return;
  }

  thumbs.forEach((thumb) => {
    thumb.addEventListener("click", () => {
      thumbs.forEach((item) => item.classList.remove("is-active"));
      thumb.classList.add("is-active");
      mainImage.src = thumb.dataset.image || mainImage.src;
    });
  });
}

function bindVariants() {
  document.querySelectorAll(".color-option").forEach((button) => {
    button.addEventListener("click", () => {
      document
        .querySelectorAll(".color-option")
        .forEach((item) => item.classList.remove("is-active"));
      button.classList.add("is-active");
    });
  });

  document.querySelectorAll(".size-option").forEach((button) => {
    button.addEventListener("click", () => {
      document
        .querySelectorAll(".size-option")
        .forEach((item) => item.classList.remove("is-active"));
      button.classList.add("is-active");
    });
  });
}

function bindQuantity() {
  const value = document.querySelector("#quantity-value");
  if (!value) {
    return;
  }

  document.querySelectorAll("[data-qty]").forEach((button) => {
    button.addEventListener("click", () => {
      const current = Number(value.textContent) || 1;
      const next =
        button.dataset.qty === "increase"
          ? current + 1
          : Math.max(1, current - 1);
      value.textContent = String(next);
    });
  });
}

function bindMobileMenu() {
  const button = document.querySelector(".menu-toggle");
  const nav = document.querySelector(".site-nav");
  if (!button || !nav) {
    return;
  }

  button.addEventListener("click", () => {
    const isOpen = nav.classList.toggle("is-open");
    button.setAttribute("aria-expanded", String(isOpen));
  });
}

document.addEventListener("DOMContentLoaded", () => {
  renderRelatedProducts();
  bindGallery();
  bindVariants();
  bindQuantity();
  bindMobileMenu();
});
