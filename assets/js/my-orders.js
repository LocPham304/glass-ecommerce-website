function initOrdersHistoryPage() {
  const menuButton = document.querySelector(".menu-toggle");
  const nav = document.querySelector(".site-nav");
  const tabs = document.querySelectorAll(".order-tab");
  const searchInput = document.querySelector("[data-order-search]");
  const orderCards = document.querySelectorAll(".order-card");
  const emptyState = document.querySelector("[data-empty-state]");
  const statusStore = window.OrderStatusStore;

  menuButton?.addEventListener("click", () => {
    const isOpen = nav?.classList.toggle("is-open");
    menuButton.setAttribute("aria-expanded", String(Boolean(isOpen)));
  });

  let activeFilter = "all";

  const renderProgress = (statusKey) => {
    if (!statusStore) return "";

    const { FLOW_STEPS, STATUS_META } = statusStore;
    const meta = STATUS_META[statusKey];

    if (!meta) return "";

    if (statusKey === "cancelled") {
      return `
        <div class="order-progress--cancelled">
          <i class="fa-solid fa-circle-xmark" aria-hidden="true"></i>
          <span>Đơn hàng này đã bị hủy và sẽ không tiếp tục xử lý.</span>
        </div>
      `;
    }

    return FLOW_STEPS.map((step, index) => {
      const currentIndex = meta.progressIndex;
      const stateClass =
        index < currentIndex
          ? "is-done"
          : index === currentIndex
            ? "is-current"
            : "";

      return `
        <div class="order-progress__step ${stateClass}">
          <div class="order-progress__dot">${index + 1}</div>
          <span>${step}</span>
        </div>
      `;
    }).join("");
  };

  const renderOrderCard = (card) => {
    if (!statusStore) return;

    const { STATUS_META, getStatus } = statusStore;
    const orderCode = card.getAttribute("data-order-code");
    const statusKey = getStatus(orderCode);
    const meta = STATUS_META[statusKey];
    const badge = card.querySelector("[data-order-badge]");
    const progress = card.querySelector("[data-order-progress]");
    const note = card.querySelector("[data-order-note]");
    const primaryButton = card.querySelector("[data-order-primary]");

    if (!meta || !badge || !progress || !note || !primaryButton) return;

    card.dataset.status = meta.filterKey;
    badge.className = `history-status ${meta.badgeClass}`;
    badge.textContent = meta.badgeText;
    progress.innerHTML = renderProgress(statusKey);
    note.textContent = meta.note;
    primaryButton.textContent = meta.primaryAction;
    primaryButton.dataset.action = statusKey;
  };

  const applyFilters = () => {
    const query = searchInput?.value.trim().toLowerCase() ?? "";
    let visibleCount = 0;

    orderCards.forEach((card) => {
      const status = card.getAttribute("data-status");
      const keywords = (card.getAttribute("data-keywords") || "").toLowerCase();
      const matchesFilter = activeFilter === "all" || status === activeFilter;
      const matchesQuery = !query || keywords.includes(query);
      const isVisible = matchesFilter && matchesQuery;

      card.hidden = !isVisible;

      if (isVisible) {
        visibleCount += 1;
      }
    });

    if (emptyState) {
      emptyState.hidden = visibleCount !== 0;
    }
  };

  tabs.forEach((tab) => {
    tab.addEventListener("click", () => {
      tabs.forEach((item) => item.classList.remove("is-active"));
      tab.classList.add("is-active");
      activeFilter = tab.dataset.filter || "all";
      applyFilters();
    });
  });

  orderCards.forEach((card) => {
    renderOrderCard(card);

    const primaryButton = card.querySelector("[data-order-primary]");
    const orderCode = card.getAttribute("data-order-code");

    primaryButton?.addEventListener("click", () => {
      if (!statusStore || !orderCode) return;

      const action = primaryButton.dataset.action;

      if (action === "shipping") {
        statusStore.setStatus(orderCode, "delivered");
        renderOrderCard(card);
        applyFilters();
        return;
      }

      if (action === "delivered") {
        window.location.href = "./return-request.html";
        return;
      }

      window.location.hash = "footer";
    });
  });

  searchInput?.addEventListener("input", applyFilters);

  window.addEventListener("storage", (event) => {
    if (event.key !== "clearvision-order-statuses-v1") return;
    orderCards.forEach(renderOrderCard);
    applyFilters();
  });

  applyFilters();
}

document.addEventListener("DOMContentLoaded", initOrdersHistoryPage);

