function initCheckoutPage() {
  const menuButton = document.querySelector(".menu-toggle");
  const nav = document.querySelector(".site-nav");

  menuButton?.addEventListener("click", () => {
    const isOpen = nav?.classList.toggle("is-open");
    menuButton.setAttribute("aria-expanded", String(Boolean(isOpen)));
  });

  document.querySelectorAll(".payment-option").forEach((option) => {
    option.addEventListener("click", () => {
      document.querySelectorAll(".payment-option").forEach((item) => item.classList.remove("is-active"));
      option.classList.add("is-active");
      const input = option.querySelector('input[type="radio"]');
      if (input) {
        input.checked = true;
      }
    });
  });
}

document.addEventListener("DOMContentLoaded", initCheckoutPage);

