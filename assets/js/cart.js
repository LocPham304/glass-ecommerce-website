const cartItems = [
  {
    id: 1,
    name: "Titanium Slim Frame - B?c Thanh L?ch",
    meta: "Chất liệu: Titanium • Màu sắc: Bạc",
    price: 3100000,
    quantity: 1,
    image: "https://images.unsplash.com/photo-1591076482161-42ce6da69f67?auto=format&fit=crop&w=320&q=80"
  },
  {
    id: 2,
    name: "Kính râm Aviator Classic",
    meta: "Mắt kính: Polarized • Gọng: Kim loại",
    price: 2500000,
    quantity: 1,
    image: "https://images.unsplash.com/photo-1577803645773-f96470509666?auto=format&fit=crop&w=320&q=80"
  }
];

function formatCurrency(value) {
  return `${value.toLocaleString("vi-VN")}d`;
}

function getSubtotal() {
  return cartItems.reduce((sum, item) => sum + item.price * item.quantity, 0);
}

function updateSummary() {
  const subtotal = getSubtotal();
  const count = cartItems.reduce((sum, item) => sum + item.quantity, 0);

  const subtotalEl = document.querySelector("#summary-subtotal");
  const totalEl = document.querySelector("#summary-total");
  const itemsCountEl = document.querySelector("#cart-items-count");
  const cartCountEl = document.querySelector("#cart-count");

  if (subtotalEl) subtotalEl.textContent = formatCurrency(subtotal);
  if (totalEl) totalEl.textContent = formatCurrency(subtotal);
  if (itemsCountEl) itemsCountEl.textContent = `${count} sản phẩm`;
  if (cartCountEl) cartCountEl.textContent = String(count);
}

function renderCart() {
  const container = document.querySelector("#cart-items");
  if (!container) {
    return;
  }

  container.innerHTML = cartItems
    .map(
      (item) => `
        <article class="cart-item" data-id="${item.id}">
          <img class="cart-item__image" src="${item.image}" alt="${item.name}">
          <div class="cart-item__content">
            <h2 class="cart-item__title">${item.name}</h2>
            <p class="cart-item__meta">${item.meta}</p>
            <p class="cart-item__price">${formatCurrency(item.price)}</p>
          </div>
          <div class="cart-item__actions">
            <div class="cart-qty" aria-label="S? lu?ng">
              <button class="cart-qty__button" type="button" data-action="decrease">-</button>
              <span class="cart-qty__value">${item.quantity}</span>
              <button class="cart-qty__button" type="button" data-action="increase">+</button>
            </div>
            <button class="cart-item__remove" type="button" aria-label="Xóa sản phẩm">
              <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M7 21a2 2 0 0 1-2-2V7h14v12a2 2 0 0 1-2 2Zm10-12H7v10h10ZM9 4h6l1 1h4v2H4V5h4Z"></path>
              </svg>
            </button>
          </div>
        </article>
      `
    )
    .join("");

  updateSummary();
}

function bindCartEvents() {
  document.querySelector("#cart-items")?.addEventListener("click", (event) => {
    const card = event.target.closest(".cart-item");
    if (!card) {
      return;
    }

    const id = Number(card.dataset.id);
    const item = cartItems.find((entry) => entry.id === id);
    if (!item) {
      return;
    }

    if (event.target.closest(".cart-item__remove")) {
      const index = cartItems.findIndex((entry) => entry.id === id);
      if (index >= 0) {
        cartItems.splice(index, 1);
        renderCart();
      }
      return;
    }

    const actionButton = event.target.closest("[data-action]");
    if (!actionButton) {
      return;
    }

    if (actionButton.dataset.action === "increase") {
      item.quantity += 1;
    } else {
      item.quantity = Math.max(1, item.quantity - 1);
    }

    renderCart();
  });

  const menuButton = document.querySelector(".menu-toggle");
  const nav = document.querySelector(".site-nav");
  menuButton?.addEventListener("click", () => {
    const isOpen = nav?.classList.toggle("is-open");
    menuButton.setAttribute("aria-expanded", String(Boolean(isOpen)));
  });

  document.querySelector(".checkout-button")?.addEventListener("click", () => {
    window.location.href = "./checkout.html";
  });
}

document.addEventListener("DOMContentLoaded", () => {
  renderCart();
  bindCartEvents();
});




