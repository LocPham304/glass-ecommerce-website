function initReturnRefundPage() {
  const form = document.querySelector(".refund-form");
  const orderCodeInput = form?.querySelector('input[name="order_code"]');
  const orderIdInput = form?.querySelector('input[name="order_id"]');
  const orderCodeMap = window.afterSalesOrderCodeMap || {};

  const syncOrderId = () => {
    if (!orderCodeInput || !orderIdInput) {
      return;
    }

    const orderCode = orderCodeInput.value.trim().toUpperCase();
    orderIdInput.value = orderCodeMap[orderCode] || "";
  };

  orderCodeInput?.addEventListener("input", syncOrderId);
  orderCodeInput?.addEventListener("change", syncOrderId);

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

  syncOrderId();
}

document.addEventListener("DOMContentLoaded", initReturnRefundPage);