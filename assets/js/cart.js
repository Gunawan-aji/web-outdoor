/**
 * Shopping Cart System using localStorage
 * For Outdoor Adventure Website
 */

const Cart = {
  // Get cart from localStorage
  getCart: function () {
    const cart = localStorage.getItem("outdoor_adventure_cart");
    return cart ? JSON.parse(cart) : [];
  },

  // Save cart to localStorage
  saveCart: function (cart) {
    localStorage.setItem("outdoor_adventure_cart", JSON.stringify(cart));
    this.updateCartCount();
  },

  // Add item to cart
  addItem: function (
    productId,
    productName,
    price,
    quantity = 1,
    image = null,
  ) {
    let cart = this.getCart();

    // Check if product already in cart
    const existingItem = cart.find((item) => item.productId === productId);

    if (existingItem) {
      existingItem.quantity += quantity;
    } else {
      cart.push({
        productId: productId,
        productName: productName,
        price: price,
        quantity: quantity,
        image: image,
      });
    }

    this.saveCart(cart);
    this.showNotification("Alat ditambahkan ke keranjang!", "success");
  },

  // Remove item from cart
  removeItem: function (productId) {
    let cart = this.getCart();
    cart = cart.filter((item) => item.productId !== productId);
    this.saveCart(cart);
    this.updateCartModal();
  },

  // Update item quantity
  updateQuantity: function (productId, quantity) {
    let cart = this.getCart();
    const item = cart.find((item) => item.productId === productId);

    if (item) {
      if (quantity <= 0) {
        this.removeItem(productId);
        return;
      }
      item.quantity = quantity;
      this.saveCart(cart);
      this.updateCartModal();
    }
  },

  // Get cart total items
  getTotalItems: function () {
    const cart = this.getCart();
    return cart.reduce((total, item) => total + item.quantity, 0);
  },

  // Get cart total price
  getTotalPrice: function () {
    const cart = this.getCart();
    return cart.reduce((total, item) => total + item.price * item.quantity, 0);
  },

  // Update cart count in header
  updateCartCount: function () {
    const cartCount = document.getElementById("cart-count");
    if (cartCount) {
      const total = this.getTotalItems();
      cartCount.textContent = total;
      cartCount.style.display = total > 0 ? "flex" : "none";
    }
  },

  // Show notification
  showNotification: function (message, type = "success") {
    const notification = document.createElement("div");
    notification.className = `cart-notification cart-notification-${type}`;
    notification.innerHTML = `
            <i class="fas fa-${type === "success" ? "check-circle" : "exclamation-circle"}"></i>
            <span>${message}</span>
        `;

    // Add styles dynamically
    notification.style.cssText = `
            position: fixed;
            top: 100px;
            right: 20px;
            padding: 15px 25px;
            background: ${type === "success" ? "#28a745" : "#dc3545"};
            color: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 10000;
            animation: slideIn 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
        `;

    document.body.appendChild(notification);

    setTimeout(() => {
      notification.style.animation = "slideOut 0.3s ease";
      setTimeout(() => notification.remove(), 300);
    }, 3000);
  },

  // Open cart modal
  openCartModal: function () {
    const modal = document.getElementById("cart-modal");
    if (modal) {
      this.updateCartModal();
      modal.classList.add("active");
      document.body.style.overflow = "hidden";
    }
  },

  // Close cart modal
  closeCartModal: function () {
    const modal = document.getElementById("cart-modal");
    if (modal) {
      modal.classList.remove("active");
      document.body.style.overflow = "";
    }
  },

  // Update cart modal content
  updateCartModal: function () {
    const cartItemsContainer = document.getElementById("cart-items");
    const cartTotal = document.getElementById("cart-total");
    const checkoutForm = document.getElementById("checkout-form");

    if (!cartItemsContainer) return;

    const cart = this.getCart();

    if (cart.length === 0) {
      cartItemsContainer.innerHTML = `
                <div class="cart-empty">
                    <i class="fas fa-shopping-basket"></i>
                    <p>Keranjang Anda kosong</p>
                    <button class="btn btn-primary" onclick="Cart.closeCartModal()">Mulai Sewa</button>
                </div>
            `;
      if (checkoutForm) checkoutForm.style.display = "none";
      if (cartTotal) cartTotal.style.display = "none";
      return;
    }

    if (checkoutForm) checkoutForm.style.display = "block";
    if (cartTotal) cartTotal.style.display = "block";

    let itemsHTML = "";
    cart.forEach((item) => {
      const itemTotal = item.price * item.quantity;
      itemsHTML += `
                <div class="cart-item">
                    <div class="cart-item-info">
                        <h4>${item.productName}</h4>
                        <p class="cart-item-price">Rp ${item.price.toLocaleString("id-ID")}</p>
                    </div>
                    <div class="cart-item-quantity">
                        <button class="qty-btn" onclick="Cart.updateQuantity(${item.productId}, ${item.quantity - 1})">
                            <i class="fas fa-minus"></i>
                        </button>
                        <span>${item.quantity}</span>
                        <button class="qty-btn" onclick="Cart.updateQuantity(${item.productId}, ${item.quantity + 1})">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <div class="cart-item-total">
                        Rp ${itemTotal.toLocaleString("id-ID")}
                    </div>
                    <button class="cart-item-remove" onclick="Cart.removeItem(${item.productId})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
    });

    cartItemsContainer.innerHTML = itemsHTML;

    if (cartTotal) {
      cartTotal.innerHTML = `
                <div class="cart-total-row">
                    <span>Total Pembayaran</span>
                    <span class="cart-total-amount">Rp ${this.getTotalPrice().toLocaleString("id-ID")}</span>
                </div>
            `;
    }
  },

  // Clear cart
  clearCart: function () {
    localStorage.removeItem("outdoor_adventure_cart");
    this.updateCartCount();
    this.updateCartModal();
  },

  // Initialize cart
  init: function () {
    this.updateCartCount();

    // Add event listeners for cart modal
    const cartBtn = document.getElementById("cart-btn");
    const closeCartBtn = document.getElementById("close-cart");
    const modal = document.getElementById("cart-modal");

    if (cartBtn) {
      cartBtn.addEventListener("click", () => this.openCartModal());
    }

    if (closeCartBtn) {
      closeCartBtn.addEventListener("click", () => this.closeCartModal());
    }

    if (modal) {
      modal.addEventListener("click", (e) => {
        if (e.target === modal) {
          this.closeCartModal();
        }
      });
    }

    // Add animation keyframes
    if (!document.getElementById("cart-animation-styles")) {
      const style = document.createElement("style");
      style.id = "cart-animation-styles";
      style.textContent = `
                @keyframes slideIn {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
                @keyframes slideOut {
                    from { transform: translateX(0); opacity: 1; }
                    to { transform: translateX(100%); opacity: 0; }
                }
            `;
      document.head.appendChild(style);
    }
  },
};

// Initialize cart when DOM is ready
document.addEventListener("DOMContentLoaded", function () {
  Cart.init();
});

// Function to add product to cart (used in HTML)
function addToCart(productId, productName, price) {
  Cart.addItem(productId, productName, price, 1);
}

// Function to quick add (from menu cards)
function quickAddToCart(productId, productName, price) {
  event.preventDefault();
  event.stopPropagation();
  Cart.addItem(productId, productName, price, 1);
}
