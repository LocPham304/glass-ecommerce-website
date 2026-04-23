function getAppToastContainer() {
  let container = document.querySelector("[data-app-toast-container]");

  if (!container) {
    container = document.createElement("div");
    container.className = "toast-container position-fixed top-0 end-0 p-3";
    container.setAttribute("aria-live", "polite");
    container.setAttribute("aria-atomic", "true");
    container.setAttribute("data-app-toast-container", "");
    document.body.appendChild(container);
  }

  return container;
}

function showAppToast(message, type = "success") {
  if (!window.bootstrap?.Toast || !message) {
    return;
  }

  const isError = type === "error";
  const toastElement = document.createElement("div");
  const toastContent = document.createElement("div");
  const toastBody = document.createElement("div");
  const closeButton = document.createElement("button");

  toastElement.className = `toast align-items-center border-0 ${isError ? "text-bg-danger" : "text-bg-success"}`;
  toastElement.setAttribute("role", isError ? "alert" : "status");
  toastElement.setAttribute("aria-live", isError ? "assertive" : "polite");
  toastElement.setAttribute("aria-atomic", "true");
  toastElement.setAttribute("data-bs-delay", "3500");

  toastContent.className = "d-flex";
  toastBody.className = "toast-body";
  toastBody.textContent = message;
  closeButton.type = "button";
  closeButton.className = "btn-close btn-close-white me-2 m-auto";
  closeButton.setAttribute("data-bs-dismiss", "toast");
  closeButton.setAttribute("aria-label", "Đóng");

  toastContent.append(toastBody, closeButton);
  toastElement.appendChild(toastContent);

  getAppToastContainer().appendChild(toastElement);

  toastElement.addEventListener("hidden.bs.toast", () => {
    toastElement.remove();
  });

  bootstrap.Toast.getOrCreateInstance(toastElement).show();
}

window.AppToast = {
  show: showAppToast,
};

document.addEventListener("DOMContentLoaded", () => {
  if (!window.bootstrap?.Toast) {
    return;
  }

  document.querySelectorAll("[data-app-toast]").forEach((toastElement) => {
    bootstrap.Toast.getOrCreateInstance(toastElement).show();
  });
});