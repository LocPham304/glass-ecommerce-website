function initReturnRefundPage() {
  const menuButton = document.querySelector(".menu-toggle");
  const nav = document.querySelector(".site-nav");

  menuButton?.addEventListener("click", () => {
    const isOpen = nav?.classList.toggle("is-open");
    menuButton.setAttribute("aria-expanded", String(Boolean(isOpen)));
  });

  document.querySelectorAll(".faq-trigger").forEach((trigger) => {
    trigger.addEventListener("click", () => {
      const item = trigger.closest(".faq-item");
      if (!item) return;

      const isOpen = item.classList.contains("is-open");
      document.querySelectorAll(".faq-item").forEach((entry) => {
        entry.classList.remove("is-open");
        const icon = entry.querySelector(".faq-trigger i");
        if (icon) {
          icon.classList.remove("fa-chevron-up");
          icon.classList.add("fa-chevron-down");
        }
        const btn = entry.querySelector(".faq-trigger");
        btn?.setAttribute("aria-expanded", "false");
      });

      if (!isOpen) {
        item.classList.add("is-open");
        trigger.setAttribute("aria-expanded", "true");
        const icon = trigger.querySelector("i");
        if (icon) {
          icon.classList.remove("fa-chevron-down");
          icon.classList.add("fa-chevron-up");
        }
      }
    });
  });

  document.querySelector(".refund-form")?.addEventListener("submit", (event) => {
    event.preventDefault();
  });
}

document.addEventListener("DOMContentLoaded", initReturnRefundPage);

