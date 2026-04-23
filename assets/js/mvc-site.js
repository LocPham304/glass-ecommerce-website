document.addEventListener("DOMContentLoaded", () => {
  const menuButton = document.querySelector(".menu-toggle");
  const nav = document.querySelector(".site-nav");

  menuButton?.addEventListener("click", () => {
    const isOpen = nav?.classList.toggle("is-open");
    menuButton.setAttribute("aria-expanded", String(Boolean(isOpen)));
  });

  document.querySelectorAll(".nav-dropdown").forEach((dropdown) => {
    const toggle = dropdown.querySelector(".nav-dropdown__toggle");
    if (!toggle) {
      return;
    }

    toggle.addEventListener("click", () => {
      const isOpen = dropdown.classList.toggle("is-open");
      toggle.setAttribute("aria-expanded", String(isOpen));
    });

    document.addEventListener("click", (event) => {
      if (!dropdown.contains(event.target)) {
        dropdown.classList.remove("is-open");
        toggle.setAttribute("aria-expanded", "false");
      }
    });
  });

  document.querySelectorAll('form[action*="/cart/add"]').forEach((form) => {
    form.addEventListener("submit", async (event) => {
      event.preventDefault();

      const submitButton = form.querySelector('[type="submit"]');
      const originalDisabled = submitButton?.disabled ?? false;

      if (submitButton) {
        submitButton.disabled = true;
      }

      try {
        const formData = new FormData(form);
        const quantity = Number(formData.get("quantity") || 1);
        const requestUrl = new URL(form.action, window.location.href);

        requestUrl.searchParams.set("ajax", "1");
        formData.set("ajax", "1");

        const response = await fetch(requestUrl.toString(), {
          method: "POST",
          body: formData,
          headers: {
            Accept: "application/json",
            "X-Requested-With": "XMLHttpRequest",
          },
        });
        const data = await parseCartResponse(response, quantity);

        if (typeof data.cart_count !== "undefined") {
          document.querySelectorAll(".cart-count").forEach((counter) => {
            counter.textContent = String(data.cart_count);
          });
        }

        window.AppToast?.show(
          data.message || (data.success ? "Đã thêm sản phẩm vào giỏ hàng." : "Không thể thêm sản phẩm vào giỏ hàng."),
          data.success ? "success" : "error",
        );
      } catch (error) {
        window.AppToast?.show("Không thể thêm sản phẩm vào giỏ hàng. Vui lòng thử lại.", "error");
      } finally {
        if (submitButton) {
          submitButton.disabled = originalDisabled;
        }
      }
    });
  });
});

async function parseCartResponse(response, quantity) {
  const contentType = response.headers.get("content-type") || "";
  const responseText = await response.text();

  if (contentType.includes("application/json") && responseText.trim() !== "") {
    try {
      return JSON.parse(responseText);
    } catch (error) {
      return buildCartFallbackResponse(response, quantity);
    }
  }

  return buildCartFallbackResponse(response, quantity);
}

function buildCartFallbackResponse(response, quantity) {
  const redirectedToLogin = response.redirected && response.url.includes("/login");

  if (redirectedToLogin) {
    return {
      success: false,
      message: "Vui lòng đăng nhập để tiếp tục.",
    };
  }

  if (response.ok) {
    return {
      success: true,
      message: "Đã thêm sản phẩm vào giỏ hàng.",
      cart_count: getFallbackCartCount(quantity),
    };
  }

  return {
    success: false,
    message: "Không thể thêm sản phẩm vào giỏ hàng. Vui lòng thử lại.",
  };
}

function getFallbackCartCount(quantity) {
  const cartCount = document.querySelector(".cart-count");
  const currentCount = Number(cartCount?.textContent?.trim() || 0);

  return currentCount + Math.max(1, Number.isFinite(quantity) ? quantity : 1);
}