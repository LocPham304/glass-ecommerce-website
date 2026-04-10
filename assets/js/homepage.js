const featuredProducts = [
  {
    name: "Kính Gọng Tròn Retro",
    color: "Xám Gunmetal",
    price: "120.000d",
    image:
      "https://www.figma.com/api/mcp/asset/6659aab7-c6ab-40e9-9684-94e4019dd789",
  },
  {
    name: "Kính Phi Công Cổ Điển",
    color: "Gọng Vàng",
    price: "145.000d",
    image:
      "https://www.figma.com/api/mcp/asset/a388cb74-5124-4c04-93e2-ea6731a3e569",
  },
  {
    name: "Kính Wayfarer Hiện Đại",
    color: "Đen Nhám",
    price: "110.000d",
    image:
      "https://www.figma.com/api/mcp/asset/b3bf9a9a-feb3-4332-b191-632fca4ec7b7",
  },
  {
    name: "Kính Mắt Mèo Sang Trọng",
    color: "Đồi mồi",
    price: "160.000d",
    image:
      "https://www.figma.com/api/mcp/asset/4f4614be-1fc7-44c0-a193-d1de62bbbf93",
  },
];

const customerReviews = [
  {
    author: "Nguyễn Văn A",
    quote:
      '"Cặp kính tốt nhất tôi từng sở hữu. Gọng kính rất chắc chắn và giao hàng siêu nhanh!"',
  },
  {
    author: "Marcus Chen",
    quote:
      '"Công cụ thử kính ảo rất chuẩn. Kính phi công vừa vặn và tôi nhận được lời khen ở khắp mọi nơi."',
  },
  {
    author: "Elena Rodriguez",
    quote:
      '"Cuối cùng cũng tìm được loại kính áp tròng không bị khô mắt. Đội ngũ hỗ trợ khách hàng rất nhiệt tình."',
  },
];

function renderProducts() {
  const grid = document.querySelector("#products-grid");
  if (!grid) {
    return;
  }

  grid.innerHTML = featuredProducts
    .map(
      (product) => `
        <article class="product-card">
          <div class="product-card__media">
            <div class="product-card__media-inner">
              <img src="${product.image}" alt="${product.name}">
            </div>
          </div>

          <div class="product-card__body">
            <h3 class="product-card__name">${product.name}</h3>
            <p class="product-card__meta">${product.color}</p>
            <div class="product-card__footer">
              <p class="product-card__price">${product.price}</p>
              <button class="product-card__add" type="button" aria-label="Thêm ${product.name} vào giỏ">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                  <path d="M11 5h2v14h-2zM5 11h14v2H5z"></path>
                </svg>
              </button>
            </div>
          </div>
        </article>
      `,
    )
    .join("");
}

function renderReviews() {
  const grid = document.querySelector("#reviews-grid");
  if (!grid) {
    return;
  }

  grid.innerHTML = customerReviews
    .map(
      (item) => `
        <article class="review-card">
          <p class="review-card__stars">&#9733;&#9733;&#9733;&#9733;&#9734;</p>
          <p class="review-card__quote">${item.quote}</p>
          <div class="review-card__author">
            <span class="review-card__avatar" aria-hidden="true"></span>
            <div>
              <p class="review-card__name">${item.author}</p>
              <p class="review-card__meta">Khách hàng đã xác thực</p>
            </div>
          </div>
        </article>
      `,
    )
    .join("");
}

function initMobileMenu() {
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
  renderProducts();
  renderReviews();
  initMobileMenu();
});
