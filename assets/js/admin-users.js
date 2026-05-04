function initAdminUsersPage() {
  const searchInput = document.querySelector("[data-user-search]");
  const roleFilter = document.querySelector("[data-role-filter]");
  const statusFilter = document.querySelector("[data-status-filter]");
  const tabs = document.querySelectorAll(".user-tab");
  const rows = Array.from(document.querySelectorAll(".user-row"));
  const summary = document.querySelector("[data-user-summary]");
  const emptyState = document.querySelector("[data-user-empty]");
  const deleteForms = document.querySelectorAll("[data-delete-user-form]");
  const overviewStaff = document.querySelector("[data-overview-staff]");
  const overviewCustomer = document.querySelector("[data-overview-customer]");
  const overviewActive = document.querySelector("[data-overview-active]");
  const tabCounts = document.querySelectorAll("[data-tab-count]");

  let activeKind = "all";

  const normalize = (value) =>
    String(value || "")
      .trim()
      .toLowerCase()
      .normalize("NFD")
      .replace(/[\u0300-\u036f]/g, "");
  const totalRows = rows.length;
  const staffRows = rows.filter((row) => row.dataset.kind === "staff");
  const customerRows = rows.filter((row) => row.dataset.kind === "customer");
  const activeRows = rows.filter((row) => row.dataset.status === "active");

  const updateStaticCounts = () => {
    if (overviewStaff) overviewStaff.textContent = `${staffRows.length}`;
    if (overviewCustomer) overviewCustomer.textContent = `${customerRows.length}`;
    if (overviewActive) overviewActive.textContent = `${activeRows.length}`;

    tabCounts.forEach((count) => {
      const key = count.getAttribute("data-tab-count");
      const value = key === "staff" ? staffRows.length : key === "customer" ? customerRows.length : totalRows;
      count.textContent = `${value}`;
    });
  };

  const applyFilters = () => {
    const query = normalize(searchInput?.value || "");
    const role = roleFilter?.value || "all";
    const status = statusFilter?.value || "all";

    let visibleCount = 0;

    rows.forEach((row) => {
      const rowKind = row.dataset.kind || "all";
      const rowRole = row.dataset.role || "";
      const rowStatus = row.dataset.status || "";
      const rowKeyword = normalize(row.dataset.keyword || "");

      const matchesKind = activeKind === "all" || rowKind === activeKind;
      const matchesRole = role === "all" || rowRole === role;
      const matchesStatus = status === "all" || rowStatus === status;
      const matchesQuery = !query || rowKeyword.includes(query);
      const isVisible = matchesKind && matchesRole && matchesStatus && matchesQuery;

      row.hidden = !isVisible;

      if (isVisible) {
        visibleCount += 1;
      }
    });

    const scopeTotal = activeKind === "staff" ? staffRows.length : activeKind === "customer" ? customerRows.length : totalRows;
    const scopeLabel = activeKind === "staff" ? "nhân viên" : activeKind === "customer" ? "khách hàng" : "người dùng";

    if (summary) {
      summary.textContent =
        visibleCount === 0
          ? `Không tìm thấy ${scopeLabel} phù hợp`
          : `Hiển thị 1 - ${visibleCount} trong số ${scopeTotal} ${scopeLabel}`;
    }

    if (emptyState) {
      emptyState.hidden = visibleCount > 0;
    }
  };

  tabs.forEach((tab) => {
    tab.addEventListener("click", () => {
      tabs.forEach((item) => item.classList.remove("is-active"));
      tab.classList.add("is-active");
      activeKind = tab.dataset.userKind || "all";
      applyFilters();
    });
  });

  [searchInput, roleFilter, statusFilter].forEach((field) => {
    field?.addEventListener("input", applyFilters);
    field?.addEventListener("change", applyFilters);
  });

  deleteForms.forEach((form) => {
    form.addEventListener("submit", (event) => {
      const userName = form.getAttribute("data-user-name") || "người dùng này";
      const confirmed = window.confirm(
        `Bạn có chắc muốn xóa "${userName}"? Tài khoản có dữ liệu nghiệp vụ liên quan sẽ không thể xóa.`
      );

      if (!confirmed) {
        event.preventDefault();
      }
    });
  });

  updateStaticCounts();
  applyFilters();
}

document.addEventListener("DOMContentLoaded", initAdminUsersPage);
