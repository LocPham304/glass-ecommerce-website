const PRODUCTS = [
  {
    name: "Kính Gọng Tròn Retro",
    price: 1200000,
    brand: "Ray-Ban",
    color: "Đen",
    material: "Nhựa Acetate",
    rating: 4.5,
    image:
      "https://images.unsplash.com/photo-1511499767150-a48a237f0083?auto=format&fit=crop&w=640&q=80",
  },
  {
    name: "Classic Aviator",
    price: 2450000,
    brand: "Ray-Ban",
    color: "Nâu",
    material: "Kim loại",
    rating: 5.0,
    image:
      "https://www.figma.com/api/mcp/asset/a388cb74-5124-4c04-93e2-ea6731a3e569",
  },
  {
    name: "Gọng Vuông Modern",
    price: 850000,
    brand: "Oakley",
    color: "Xám",
    material: "Nhựa Acetate",
    rating: 4.0,
    image:
      "https://images.unsplash.com/photo-1577803645773-f96470509666?auto=format&fit=crop&w=640&q=80",
  },
  {
    name: "Titanium Slim Frame",
    price: 3100000,
    brand: "Prada",
    color: "Đen",
    material: "Titanium",
    rating: 4.8,
    image:
      "https://images.unsplash.com/photo-1591076482161-42ce6da69f67?auto=format&fit=crop&w=640&q=80",
  },
  {
    name: "Wayfarer Urban",
    price: 1450000,
    brand: "Ray-Ban",
    color: "Đen",
    material: "Nhựa Acetate",
    rating: 4.6,
    image:
      "https://www.figma.com/api/mcp/asset/b3bf9a9a-feb3-4332-b191-632fca4ec7b7",
  },
  {
    name: "Minimal Round",
    price: 1680000,
    brand: "Gucci",
    color: "Xanh",
    material: "Kim loại",
    rating: 4.4,
    image:
      "https://www.figma.com/api/mcp/asset/6659aab7-c6ab-40e9-9684-94e4019dd789",
  },
  {
    name: "Bold Cat Eye",
    price: 2250000,
    brand: "Prada",
    color: "Hồng",
    material: "Nhựa Acetate",
    rating: 4.7,
    image:
      "https://www.figma.com/api/mcp/asset/4f4614be-1fc7-44c0-a193-d1de62bbbf93",
  },
  {
    name: "Office Square",
    price: 980000,
    brand: "Oakley",
    color: "Xám",
    material: "Kim loại",
    rating: 4.1,
    image:
      "https://images.unsplash.com/photo-1572635196237-14b3f281503f?auto=format&fit=crop&w=640&q=80",
  },
  {
    name: "Slim Gold Wire",
    price: 1890000,
    brand: "Ray-Ban",
    color: "Nâu",
    material: "Kim loại",
    rating: 4.3,
    image:
      "https://images.unsplash.com/photo-1612817159949-195b6eb9e31a?auto=format&fit=crop&w=640&q=80",
  },
  {
    name: "Soft Rectangle",
    price: 1320000,
    brand: "Gucci",
    color: "Đen",
    material: "Nhựa Acetate",
    rating: 4.2,
    image:
      "https://images.unsplash.com/photo-1583394838336-acd977736f90?auto=format&fit=crop&w=640&q=80",
  },
  {
    name: "Metro Titanium",
    price: 3520000,
    brand: "Prada",
    color: "Xám",
    material: "Titanium",
    rating: 4.9,
    image:
      "https://images.unsplash.com/photo-1574258495973-f010dfbb5371?auto=format&fit=crop&w=640&q=80",
  },
  {
    name: "Classic Reader",
    price: 760000,
    brand: "Oakley",
    color: "Nâu",
    material: "Nhựa Acetate",
    rating: 4.0,
    image:
      "https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=640&q=80",
  },
];

PRODUCTS.push(
  {
    name: "Tròng Chống Ánh Xanh Pro",
    price: 680000,
    brand: "Ray-Ban",
    color: "Trong suốt",
    material: "Kim loại",
    rating: 4.7,
    category: "trong-kinh",
    image:
      "https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=640&q=80",
  },
  {
    name: "Tròng Đổi Màu Smart Light",
    price: 1250000,
    brand: "Prada",
    color: "Xám",
    material: "Titanium",
    rating: 4.8,
    category: "trong-kinh",
    image:
      "https://images.unsplash.com/photo-1591076482161-42ce6da69f67?auto=format&fit=crop&w=640&q=80",
  },
  {
    name: "Combo Gọng + Tròng Everyday",
    price: 2190000,
    brand: "Oakley",
    color: "Đen",
    material: "Nhựa Acetate",
    rating: 4.6,
    category: "combo",
    image:
      "https://images.unsplash.com/photo-1577803645773-f96470509666?auto=format&fit=crop&w=640&q=80",
  },
  {
    name: "Combo Slim Frame Premium",
    price: 3890000,
    brand: "Gucci",
    color: "Nâu",
    material: "Kim loại",
    rating: 4.9,
    category: "combo",
    image:
      "https://images.unsplash.com/photo-1574258495973-f010dfbb5371?auto=format&fit=crop&w=640&q=80",
  },
  {
    name: "Hộp Đựng Kính Classic",
    price: 320000,
    brand: "Ray-Ban",
    color: "Nâu",
    material: "Nhựa Acetate",
    rating: 4.4,
    category: "phu-kien",
    image:
      "https://images.unsplash.com/photo-1511499767150-a48a237f0083?auto=format&fit=crop&w=640&q=80",
  },
  {
    name: "Khăn Lau Kính Microfiber",
    price: 90000,
    brand: "Oakley",
    color: "Xanh",
    material: "Nhựa Acetate",
    rating: 4.3,
    category: "phu-kien",
    image:
      "https://images.unsplash.com/photo-1572635196237-14b3f281503f?auto=format&fit=crop&w=640&q=80",
  },
);

PRODUCTS.forEach((product) => {
  if (!product.category) {
    product.category = "gong-kinh-can";
  }
});

const CATEGORY_CONFIG = {
  "gong-kinh-can": {
    title: "Tất cả Gọng kính cận",
    breadcrumb: "Gọng kính cận",
  },
  "trong-kinh": {
    title: "Tất cả Tròng kính",
    breadcrumb: "Tròng kính",
  },
  combo: {
    title: "Tất cả Combo",
    breadcrumb: "Combo",
  },
  "phu-kien": {
    title: "Tất cả Phụ kiện",
    breadcrumb: "Phụ kiện",
  },
  all: {
    title: "Tất cả Sản phẩm",
    breadcrumb: "Sản phẩm",
  },
};

const state = {
  currentPage: 1,
  pageSize: 4,
  search: "",
  sort: "default",
  priceMin: 0,
  priceMax: 5000000,
  activeColor: "",
  category: "",
};

function formatCurrency(value) {
  return `${value.toLocaleString("vi-VN")}d`;
}

function getCheckedValues(selector) {
  return Array.from(document.querySelectorAll(selector))
    .filter((input) => input.checked)
    .map((input) => input.value);
}

function getActiveCategoryConfig() {
  return CATEGORY_CONFIG[state.category] || CATEGORY_CONFIG.all;
}

function syncCategoryContent() {
  const title = document.querySelector(".shop-title");
  const breadcrumbCurrent = document.querySelector(
    ".breadcrumb span:last-child",
  );
  const categoryConfig = getActiveCategoryConfig();

  if (title) {
    title.textContent = categoryConfig.title;
  }

  if (breadcrumbCurrent) {
    breadcrumbCurrent.textContent = categoryConfig.breadcrumb;
  }
}

function applyCategoryFromQuery() {
  const params = new URLSearchParams(window.location.search);
  const category = params.get("category") || "";
  state.category = CATEGORY_CONFIG[category] ? category : "";
  syncCategoryContent();
}

function getFilteredProducts() {
  const checkedBrands = getCheckedValues(
    '.filter-group input[type="checkbox"][value="Ray-Ban"], .filter-group input[type="checkbox"][value="Oakley"], .filter-group input[type="checkbox"][value="Gucci"], .filter-group input[type="checkbox"][value="Prada"]',
  );
  const checkedMaterials = getCheckedValues(
    '.filter-group input[type="checkbox"][value="Nhựa Acetate"], .filter-group input[type="checkbox"][value="Kim loại"], .filter-group input[type="checkbox"][value="Titanium"]',
  );

  let items = PRODUCTS.filter((product) => {
    const matchesSearch =
      !state.search ||
      product.name.toLowerCase().includes(state.search) ||
      product.brand.toLowerCase().includes(state.search);

    const matchesBrand =
      checkedBrands.length === 0 || checkedBrands.includes(product.brand);
    const matchesMaterial =
      checkedMaterials.length === 0 ||
      checkedMaterials.includes(product.material);
    const matchesColor =
      !state.activeColor || product.color === state.activeColor;
    const matchesPrice =
      product.price >= state.priceMin && product.price <= state.priceMax;
    const matchesCategory =
      !state.category || product.category === state.category;

    return (
      matchesSearch &&
      matchesBrand &&
      matchesMaterial &&
      matchesColor &&
      matchesPrice &&
      matchesCategory
    );
  });

  switch (state.sort) {
    case "price-asc":
      items = items.sort((a, b) => a.price - b.price);
      break;
    case "price-desc":
      items = items.sort((a, b) => b.price - a.price);
      break;
    case "name-asc":
      items = items.sort((a, b) => a.name.localeCompare(b.name, "vi"));
      break;
    default:
      break;
  }

  return items;
}

function renderProducts() {
  const grid = document.querySelector("#shop-products-grid");
  const resultsText = document.querySelector("#results-text");
  if (!grid || !resultsText) {
    return;
  }

  const items = getFilteredProducts();
  const start = (state.currentPage - 1) * state.pageSize;
  const pageItems = items.slice(start, start + state.pageSize);

  resultsText.textContent = `Hiển thị ${items.length} sản phẩm`;

  grid.innerHTML = pageItems
    .map(
      (product) => `
        <article class="shop-product-card">
          <div class="shop-product-card__media">
            <img src="${product.image}" alt="${product.name}">
          </div>
          <div class="shop-product-card__body">
            <h3 class="shop-product-card__name">${product.name}</h3>
            <p class="shop-product-card__price">${formatCurrency(product.price)}</p>
          </div>
        </article>
      `,
    )
    .join("");

  renderPagination(items.length);
}

function renderPagination(totalItems) {
  const container = document.querySelector("#pagination");
  if (!container) {
    return;
  }

  const totalPages = Math.max(1, Math.ceil(totalItems / state.pageSize));
  state.currentPage = Math.min(state.currentPage, totalPages);

  const start = Math.max(1, state.currentPage - 1);
  const end = Math.min(totalPages, start + 2);
  const pages = [];
  for (let page = start; page <= end; page += 1) {
    pages.push(page);
  }

  container.innerHTML = `
    <button class="pagination__button" type="button" data-page="${state.currentPage - 1}" ${state.currentPage === 1 ? "disabled" : ""}>&lsaquo;</button>
    ${pages
      .map(
        (page) =>
          `<button class="pagination__button ${page === state.currentPage ? "is-active" : ""}" type="button" data-page="${page}">${page}</button>`,
      )
      .join("")}
    <button class="pagination__button" type="button" data-page="${state.currentPage + 1}" ${state.currentPage === totalPages ? "disabled" : ""}>&rsaquo;</button>
  `;
}

function updatePriceLabels() {
  const minLabel = document.querySelector("#price-min-label");
  const maxLabel = document.querySelector("#price-max-label");
  const progress = document.querySelector("#price-progress");
  const minHandle = document.querySelector("#price-min-handle");
  const maxHandle = document.querySelector("#price-max-handle");
  if (!minLabel || !maxLabel || !progress) {
    return;
  }

  minLabel.textContent = formatCurrency(state.priceMin);
  maxLabel.textContent = formatCurrency(state.priceMax);

  const minPercent = (state.priceMin / 5000000) * 100;
  const maxPercent = (state.priceMax / 5000000) * 100;
  progress.style.left = `${minPercent}%`;
  progress.style.right = `${100 - maxPercent}%`;

  if (minHandle) {
    minHandle.style.left = `${minPercent}%`;
  }

  if (maxHandle) {
    maxHandle.style.left = `${maxPercent}%`;
  }
}

function initPriceSlider() {
  const slider = document.querySelector("[data-price-slider]");
  const minHandle = document.querySelector("#price-min-handle");
  const maxHandle = document.querySelector("#price-max-handle");

  if (!slider || !minHandle || !maxHandle) {
    return;
  }

  const minGap = 50000;
  const minValue = 0;
  const maxValue = 5000000;
  const step = 50000;

  const valueFromClientX = (clientX) => {
    const rect = slider.getBoundingClientRect();
    const ratio = Math.min(Math.max((clientX - rect.left) / rect.width, 0), 1);
    const rawValue = minValue + ratio * (maxValue - minValue);
    return Math.round(rawValue / step) * step;
  };

  const startDrag = (handleType) => (event) => {
    event.preventDefault();

    const onMove = (moveEvent) => {
      const nextValue = valueFromClientX(moveEvent.clientX);

      if (handleType === "min") {
        state.priceMin = Math.min(nextValue, state.priceMax - minGap);
      } else {
        state.priceMax = Math.max(nextValue, state.priceMin + minGap);
      }

      state.currentPage = 1;
      updatePriceLabels();
      renderProducts();
    };

    const onUp = () => {
      window.removeEventListener("pointermove", onMove);
      window.removeEventListener("pointerup", onUp);
    };

    window.addEventListener("pointermove", onMove);
    window.addEventListener("pointerup", onUp);
  };

  minHandle.addEventListener("pointerdown", startDrag("min"));
  maxHandle.addEventListener("pointerdown", startDrag("max"));

  slider.addEventListener("click", (event) => {
    if (event.target.closest(".price-slider__handle")) {
      return;
    }

    const nextValue = valueFromClientX(event.clientX);
    const distanceToMin = Math.abs(nextValue - state.priceMin);
    const distanceToMax = Math.abs(nextValue - state.priceMax);

    if (distanceToMin <= distanceToMax) {
      state.priceMin = Math.min(nextValue, state.priceMax - minGap);
    } else {
      state.priceMax = Math.max(nextValue, state.priceMin + minGap);
    }

    state.currentPage = 1;
    updatePriceLabels();
    renderProducts();
  });
}

function bindEvents() {
  const search = document.querySelector("#product-search");
  const sortSelect = document.querySelector("#sort-select");

  search?.addEventListener("input", (event) => {
    state.search = event.target.value.trim().toLowerCase();
    state.currentPage = 1;
    renderProducts();
  });

  sortSelect?.addEventListener("change", (event) => {
    state.sort = event.target.value;
    renderProducts();
  });

  document
    .querySelectorAll('.filter-check input[type="checkbox"]')
    .forEach((input) => {
      input.addEventListener("change", () => {
        state.currentPage = 1;
        renderProducts();
      });
    });

  document.querySelectorAll(".color-swatch").forEach((button) => {
    button.addEventListener("click", () => {
      const willActivate = !button.classList.contains("is-active");
      document
        .querySelectorAll(".color-swatch")
        .forEach((item) => item.classList.remove("is-active"));
      if (willActivate) {
        button.classList.add("is-active");
      }
      state.activeColor = willActivate ? button.dataset.color || "" : "";
      state.currentPage = 1;
      renderProducts();
    });
  });

  document.querySelector("#pagination")?.addEventListener("click", (event) => {
    const button = event.target.closest("[data-page]");
    if (!button || button.disabled) {
      return;
    }
    state.currentPage = Number(button.dataset.page);
    renderProducts();
  });

  const menuButton = document.querySelector(".menu-toggle");
  const nav = document.querySelector(".site-nav");
  menuButton?.addEventListener("click", () => {
    const isOpen = nav?.classList.toggle("is-open");
    menuButton.setAttribute("aria-expanded", String(Boolean(isOpen)));
  });
}

document.addEventListener("DOMContentLoaded", () => {
  applyCategoryFromQuery();
  updatePriceLabels();
  initPriceSlider();
  bindEvents();
  renderProducts();
});
