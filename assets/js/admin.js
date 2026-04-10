function initAdminOverview() {
  const menuButton = document.querySelector(".admin-menu-toggle");
  const nav = document.querySelector(".admin-nav");
  const toast = document.querySelector("[data-admin-toast]");
  const toastText = toast?.querySelector("span");
  const comingSoonLinks = document.querySelectorAll("[data-coming-soon]");

  let toastTimeout;

  const showToast = (message) => {
    if (!toast || !toastText) return;
    toastText.textContent = message;
    toast.hidden = false;

    window.clearTimeout(toastTimeout);
    toastTimeout = window.setTimeout(() => {
      toast.hidden = true;
    }, 2200);
  };

  menuButton?.addEventListener("click", () => {
    const isOpen = nav?.classList.toggle("is-open");
    menuButton.setAttribute("aria-expanded", String(Boolean(isOpen)));
  });

  comingSoonLinks.forEach((link) => {
    link.addEventListener("click", (event) => {
      event.preventDefault();
      const label = link.getAttribute("data-coming-soon") || "Mục này";
      showToast(`${label} sẽ được triển khai ở bước tiếp theo.`);
    });
  });
}

document.addEventListener("DOMContentLoaded", initAdminOverview);

