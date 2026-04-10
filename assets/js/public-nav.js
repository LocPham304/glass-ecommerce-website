function legacyInitPublicProductDropdown() {
  const nav = document.querySelector(".site-nav");
  if (!nav || nav.querySelector(".nav-dropdown")) {
    return;
  }

  const productLink = [...nav.querySelectorAll("a")].find((link) => link.textContent.trim() === "Sản phẩm");
  if (!productLink) {
    return;
  }

  const dropdown = document.createElement("div");
  dropdown.className = "nav-dropdown";

  const toggle = document.createElement("button");
  toggle.type = "button";
  toggle.className = "nav-dropdown__toggle";
  toggle.setAttribute("aria-expanded", "false");
  toggle.innerHTML = `
    <span>Sản phẩm</span>
    <i class="fa-solid fa-angle-down" aria-hidden="true"></i>
  `;

  const menu = document.createElement("div");
  menu.className = "nav-dropdown__menu";
  menu.innerHTML = `
    <a href="./shop.html?category=gong-kinh-can">Gọng kính cận</a>
    <a href="./shop.html?category=trong-kinh">Tròng kính</a>
    <a href="./shop.html?category=combo">Combo</a>
    <a href="./shop.html?category=phu-kien">Phụ kiện</a>
  `;

  const closeDropdown = () => {
    dropdown.classList.remove("is-open");
    toggle.setAttribute("aria-expanded", "false");
  };

  const openDropdown = () => {
    dropdown.classList.add("is-open");
    toggle.setAttribute("aria-expanded", "true");
  };

  toggle.addEventListener("click", () => {
    const isOpen = dropdown.classList.toggle("is-open");
    toggle.setAttribute("aria-expanded", String(isOpen));
  });

  dropdown.addEventListener("mouseenter", () => {
    if (window.innerWidth > 991) {
      openDropdown();
    }
  });

  dropdown.addEventListener("mouseleave", () => {
    if (window.innerWidth > 991) {
      closeDropdown();
    }
  });

  document.addEventListener("click", (event) => {
    if (!dropdown.contains(event.target)) {
      closeDropdown();
    }
  });

  dropdown.append(toggle, menu);
  productLink.replaceWith(dropdown);
}

const PRODUCT_NAV_ITEMS = [
  { label: "Gọng kính cận", href: "./shop.html?category=gong-kinh-can" },
  { label: "Tròng kính", href: "./shop.html?category=trong-kinh" },
  { label: "Combo", href: "./shop.html?category=combo" },
  { label: "Phụ kiện", href: "./shop.html?category=phu-kien" }
];

const PROFILE_MENU_ITEMS = [
  {
    label: "Trang quản trị",
    href: "../admin/dashboard.html",
    icon: "fa-solid fa-screwdriver-wrench",
    adminOnly: true,
  },
  {
    label: "Hồ sơ",
    href: "./profile.html",
    icon: "fa-regular fa-user",
  },
  {
    label: "Đăng xuất",
    href: "./login.html",
    icon: "fa-solid fa-arrow-right-from-bracket",
    action: "logout",
  },
];

function buildProductDropdown() {
  const dropdown = document.createElement("div");
  dropdown.className = "nav-dropdown";

  const toggle = document.createElement("button");
  toggle.type = "button";
  toggle.className = "nav-dropdown__toggle";
  toggle.setAttribute("aria-expanded", "false");
  toggle.setAttribute("aria-haspopup", "true");
  toggle.innerHTML = `
    <span>Sản phẩm</span>
    <i class="fa-solid fa-angle-down" aria-hidden="true"></i>
  `;

  const menu = document.createElement("div");
  menu.className = "nav-dropdown__menu";
  menu.setAttribute("role", "menu");
  menu.innerHTML = PRODUCT_NAV_ITEMS.map(
    (item) => `<a href="${item.href}" role="menuitem">${item.label}</a>`
  ).join("");

  dropdown.append(toggle, menu);
  return dropdown;
}

function findProductNavEntry(nav) {
  const directChildren = [...nav.children];

  return (
    directChildren.find(
      (item) =>
        item.tagName === "A" &&
        (item.getAttribute("href")?.includes("shop.html") || item.getAttribute("href") === "#featured-products")
    ) ||
    directChildren.find((item) => item.tagName === "A") ||
    null
  );
}

function bindDropdownBehavior(dropdown) {
  const toggle = dropdown.querySelector(".nav-dropdown__toggle");
  if (!toggle) {
    return;
  }

  const closeDropdown = () => {
    dropdown.classList.remove("is-open");
    toggle.setAttribute("aria-expanded", "false");
  };

  const openDropdown = () => {
    dropdown.classList.add("is-open");
    toggle.setAttribute("aria-expanded", "true");
  };

  toggle.addEventListener("click", (event) => {
    event.preventDefault();
    const isOpen = dropdown.classList.toggle("is-open");
    toggle.setAttribute("aria-expanded", String(isOpen));
  });

  dropdown.addEventListener("mouseenter", () => {
    if (window.innerWidth > 991) {
      openDropdown();
    }
  });

  dropdown.addEventListener("mouseleave", () => {
    if (window.innerWidth > 991) {
      closeDropdown();
    }
  });

  document.addEventListener("click", (event) => {
    if (!dropdown.contains(event.target)) {
      closeDropdown();
    }
  });

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") {
      closeDropdown();
    }
  });
}

function initPublicProductDropdown() {
  const nav = document.querySelector(".site-nav");
  if (!nav) {
    return;
  }

  const existingDropdown = nav.querySelector(".nav-dropdown");
  if (existingDropdown) {
    bindDropdownBehavior(existingDropdown);
    return;
  }

  const productEntry = findProductNavEntry(nav);
  if (!productEntry) {
    return;
  }

  const dropdown = buildProductDropdown();
  productEntry.replaceWith(dropdown);
  bindDropdownBehavior(dropdown);
}

function closeProfileDropdown(dropdown) {
  const toggle = dropdown?.querySelector(".profile-dropdown__toggle");
  if (!dropdown || !toggle) {
    return;
  }

  dropdown.classList.remove("is-open");
  toggle.setAttribute("aria-expanded", "false");
}

function bindProfileDropdown(dropdown) {
  const toggle = dropdown.querySelector(".profile-dropdown__toggle");
  if (!toggle) {
    return;
  }

  toggle.addEventListener("click", (event) => {
    event.preventDefault();
    const isOpen = dropdown.classList.toggle("is-open");
    toggle.setAttribute("aria-expanded", String(isOpen));
  });

  document.addEventListener("click", (event) => {
    if (!dropdown.contains(event.target)) {
      closeProfileDropdown(dropdown);
    }
  });

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") {
      closeProfileDropdown(dropdown);
    }
  });

  dropdown.querySelectorAll("[data-profile-action]").forEach((link) => {
    link.addEventListener("click", () => {
      if (link.dataset.profileAction === "logout") {
        window.localStorage.removeItem("clearvision-user-role");
      }
      closeProfileDropdown(dropdown);
    });
  });
}

function initProfileDropdown() {
  const profileButton = document.querySelector(".profile-button");
  if (!profileButton || profileButton.closest(".profile-dropdown")) {
    return;
  }

  const role =
    document.body.dataset.userRole ||
    window.localStorage.getItem("clearvision-user-role") ||
    "admin";

  const dropdown = document.createElement("div");
  dropdown.className = "profile-dropdown";

  const toggle = profileButton.cloneNode(true);
  toggle.classList.add("profile-dropdown__toggle");
  toggle.setAttribute("aria-expanded", "false");
  toggle.setAttribute("aria-haspopup", "true");

  const menu = document.createElement("div");
  menu.className = "profile-dropdown__menu";
  menu.setAttribute("role", "menu");

  menu.innerHTML = PROFILE_MENU_ITEMS.filter((item) => !item.adminOnly || role === "admin")
    .map(
      (item) => `
        <a href="${item.href}" class="profile-dropdown__item" role="menuitem" ${item.action ? `data-profile-action="${item.action}"` : ""}>
          <i class="${item.icon}" aria-hidden="true"></i>
          <span>${item.label}</span>
        </a>
      `
    )
    .join("");

  profileButton.replaceWith(dropdown);
  dropdown.append(toggle, menu);
  bindProfileDropdown(dropdown);
}

document.addEventListener("DOMContentLoaded", () => {
  initPublicProductDropdown();
  initProfileDropdown();
});

