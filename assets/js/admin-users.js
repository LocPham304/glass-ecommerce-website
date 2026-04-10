function initAdminUsersPage() {
  const searchInput = document.querySelector("[data-user-search]");
  const roleFilter = document.querySelector("[data-role-filter]");
  const statusFilter = document.querySelector("[data-status-filter]");
  const tabs = document.querySelectorAll(".user-tab");
  const rows = document.querySelectorAll(".user-row");
  const summary = document.querySelector("[data-user-summary]");
  const emptyState = document.querySelector("[data-user-empty]");
  const overviewStaff = document.querySelector("[data-overview-staff]");
  const overviewCustomer = document.querySelector("[data-overview-customer]");
  const overviewActive = document.querySelector("[data-overview-active]");
  const tabCounts = document.querySelectorAll("[data-tab-count]");

  let activeKind = "all";

  const normalize = (value) => value.trim().toLowerCase();
  const totalRows = rows.length;
  const staffRows = [...rows].filter((row) => row.dataset.kind === "staff");
  const customerRows = [...rows].filter((row) => row.dataset.kind === "customer");
  const activeRows = [...rows].filter((row) => row.dataset.status === "Hoạt động");

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
    const scopeLabel = activeKind === "staff" ? "nhan vien" : activeKind === "customer" ? "khach hang" : "nguoi dung";

    if (summary) {
      summary.textContent = `Hien thi ${visibleCount ? 1 : 0} - ${visibleCount} trong so ${scopeTotal} ${scopeLabel}`;
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

  updateStaticCounts();
  applyFilters();
}

document.addEventListener("DOMContentLoaded", initAdminUsersPage);


