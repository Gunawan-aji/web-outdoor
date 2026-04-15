/**
 * Outdoor Adventure - Main JavaScript
 */

document.addEventListener("DOMContentLoaded", function () {
  // ============================================
  // Header Scroll Effect
  // ============================================
  const header = document.querySelector(".header");

  window.addEventListener("scroll", function () {
    if (window.scrollY > 50) {
      header.classList.add("scrolled");
    } else {
      header.classList.remove("scrolled");
    }
  });

  // ============================================
  // Mobile Navigation Toggle
  // ============================================
  const navToggle = document.querySelector(".nav-toggle");
  const navMenu = document.querySelector(".nav-menu");

  if (navToggle && navMenu) {
    navToggle.addEventListener("click", function () {
      navMenu.classList.toggle("active");

      // Animate hamburger
      this.classList.toggle("active");
    });

    // Close menu when clicking outside
    document.addEventListener("click", function (e) {
      if (!navToggle.contains(e.target) && !navMenu.contains(e.target)) {
        navMenu.classList.remove("active");
        navToggle.classList.remove("active");
      }
    });
  }

  // ============================================
  // Smooth Scroll for Navigation Links
  // ============================================
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      const href = this.getAttribute("href");
      if (href !== "#") {
        e.preventDefault();
        const target = document.querySelector(href);
        if (target) {
          target.scrollIntoView({
            behavior: "smooth",
            block: "start",
          });
        }
      }
    });
  });

  // ============================================
  // Menu Category Filter
  // ============================================
  const categoryBtns = document.querySelectorAll(".category-btn");
  const menuCards = document.querySelectorAll(".menu-card");

  if (categoryBtns.length > 0) {
    categoryBtns.forEach((btn) => {
      btn.addEventListener("click", function () {
        // Remove active class from all buttons
        categoryBtns.forEach((b) => b.classList.remove("active"));
        // Add active class to clicked button
        this.classList.add("active");

        const category = this.dataset.category;

        menuCards.forEach((card) => {
          if (category === "all" || card.dataset.category === category) {
            card.style.display = "block";
            card.style.animation = "fadeIn 0.5s ease";
          } else {
            card.style.display = "none";
          }
        });
      });
    });
  }

  // ============================================
  // Contact Form Submission
  // ============================================
  const contactForm = document.getElementById("contactForm");

  if (contactForm) {
    contactForm.addEventListener("submit", async function (e) {
      e.preventDefault();

      const formData = new FormData(this);
      const submitBtn = this.querySelector('button[type="submit"]');
      const successMsg = document.getElementById("formSuccess");

      // Show loading state
      submitBtn.disabled = true;
      submitBtn.textContent = "Mengirim...";

      try {
        const response = await fetch("pages/send-message.php", {
          method: "POST",
          body: formData,
        });

        const result = await response.json();

        if (result.success) {
          successMsg.style.display = "block";
          this.reset();

          // Hide success message after 5 seconds
          setTimeout(() => {
            successMsg.style.display = "none";
          }, 5000);
        } else {
          alert("Gagal mengirim pesan: " + result.message);
        }
      } catch (error) {
        alert("Terjadi kesalahan. Silakan coba lagi.");
      } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = "Kirim Pesan";
      }
    });
  }

  // ============================================
  // Scroll Animations (Intersection Observer)
  // ============================================
  const observerOptions = {
    threshold: 0.1,
    rootMargin: "0px 0px -50px 0px",
  };

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add("animate-fade-in");
        observer.unobserve(entry.target);
      }
    });
  }, observerOptions);

  document
    .querySelectorAll(".menu-card, .feature-card, .gallery-item")
    .forEach((el) => {
      observer.observe(el);
    });

  // ============================================
  // Navbar Active State on Scroll
  // ============================================
  const sections = document.querySelectorAll("section[id]");

  window.addEventListener("scroll", function () {
    let current = "";

    sections.forEach((section) => {
      const sectionTop = section.offsetTop;
      const sectionHeight = section.clientHeight;

      if (scrollY >= sectionTop - 200) {
        current = section.getAttribute("id");
      }
    });

    document.querySelectorAll(".nav-link").forEach((link) => {
      link.classList.remove("active");
      if (link.getAttribute("href") === "#" + current) {
        link.classList.add("active");
      }
    });
  });

  // ============================================
  // Quantity Selector (Product Detail)
  // ============================================
  const quantityBtns = document.querySelectorAll(".qty-btn");
  const qtyInput = document.getElementById("quantity");

  if (quantityBtns.length > 0 && qtyInput) {
    quantityBtns.forEach((btn) => {
      btn.addEventListener("click", function () {
        let currentQty = parseInt(qtyInput.value);

        if (this.classList.contains("qty-minus") && currentQty > 1) {
          qtyInput.value = currentQty - 1;
        } else if (this.classList.contains("qty-plus")) {
          qtyInput.value = currentQty + 1;
        }
      });
    });
  }

  // ============================================
  // Image Lazy Loading
  // ============================================
  if ("IntersectionObserver" in window) {
    const imgObserver = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const img = entry.target;
          if (img.dataset.src) {
            img.src = img.dataset.src;
            img.removeAttribute("data-src");
          }
          imgObserver.unobserve(img);
        }
      });
    });

    document.querySelectorAll("img[data-src]").forEach((img) => {
      imgObserver.observe(img);
    });
  }

  // ============================================
  // Admin Panel - Delete Confirmation
  // ============================================
  const deleteBtns = document.querySelectorAll(".delete-btn");

  deleteBtns.forEach((btn) => {
    btn.addEventListener("click", function (e) {
      if (!confirm("Apakah Anda yakin ingin menghapus data ini?")) {
        e.preventDefault();
      }
    });
  });

  // ============================================
  // Admin Panel - Toggle Sidebar
  // ============================================
  const sidebarToggle = document.getElementById("sidebarToggle");
  const sidebar = document.querySelector(".sidebar");

  if (sidebarToggle && sidebar) {
    sidebarToggle.addEventListener("click", function () {
      sidebar.classList.toggle("collapsed");
    });
  }

  // ============================================
  // Search Functionality (Admin)
  // ============================================
  const searchInput = document.getElementById("searchInput");
  const tableBody = document.querySelector(".data-table tbody");

  if (searchInput && tableBody) {
    searchInput.addEventListener("input", function () {
      const searchTerm = this.value.toLowerCase();
      const rows = tableBody.querySelectorAll("tr");

      rows.forEach((row) => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? "" : "none";
      });
    });
  }
});

// ============================================
// Utility Functions
// ============================================

/**
 * Format currency to Indonesian Rupiah
 */
function formatRupiah(amount) {
  return "Rp " + amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

/**
 * Show notification
 */
function showNotification(message, type = "success") {
  const notification = document.createElement("div");
  notification.className = `notification notification-${type}`;
  notification.textContent = message;
  notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 25px;
        background: ${type === "success" ? "#28a745" : "#dc3545"};
        color: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 9999;
        animation: slideIn 0.3s ease;
    `;

  document.body.appendChild(notification);

  setTimeout(() => {
    notification.style.animation = "slideOut 0.3s ease";
    setTimeout(() => notification.remove(), 300);
  }, 3000);
}

/**
 * Add animation keyframes dynamically
 */
const style = document.createElement("style");
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
