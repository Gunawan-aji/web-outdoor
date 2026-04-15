<?php
require_once '../config/functions.php';

$settings = getSettings();

// Get product ID from URL
$product_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$product = getProductById($product_id);

// If product not found, redirect to menu
if (!$product) {
    redirect('menu.php');
}

$related_products = getProductsByCategory($product['kategori_id']);
// Remove current product from related
$related_products = array_filter($related_products, function ($p) use ($product_id) {
    return $p['id'] != $product_id;
});
// Limit to 3
$related_products = array_slice($related_products, 0, 3);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $product['nama_produk'] ?> -
        <?= $settings['site_name'] ?>
    </title>
    <meta name="description" content="<?= $product['deskripsi'] ?>">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Styles -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .page-header {
            background: linear-gradient(rgba(44, 24, 16, 0.8), rgba(44, 24, 16, 0.8)),
                url('https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=1920') center/cover;
            padding: 150px 0 80px;
            text-align: center;
            color: var(--white);
        }

        .page-title {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .page-breadcrumb {
            display: flex;
            justify-content: center;
            gap: 10px;
            font-size: 0.9rem;
        }

        .page-breadcrumb a:hover {
            color: var(--secondary);
        }

        /* Cart Button in Header */
        .cart-header-btn {
            position: relative;
            padding: 10px 15px;
            background: var(--secondary);
            color: var(--dark);
            border-radius: 50px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            transition: all 0.3s;
            cursor: pointer;
            border: none;
        }

        .cart-header-btn:hover {
            background: var(--primary);
            color: var(--white);
        }

        .cart-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--danger);
            color: white;
            font-size: 0.75rem;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        /* Product Detail Styles */
        .product-detail {
            padding: 80px 0;
            background: var(--light);
        }

        .product-detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: start;
        }

        .product-image {
            position: sticky;
            top: 100px;
        }

        .product-main-image {
            width: 100%;
            height: 400px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: var(--border-radius);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8rem;
            color: var(--white);
            margin-bottom: 20px;
        }

        .product-info h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            color: var(--dark);
        }

        .product-category {
            display: inline-block;
            background: var(--accent);
            color: var(--primary);
            padding: 5px 15px;
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .product-price {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 20px;
        }

        .product-description {
            color: var(--gray-600);
            line-height: 1.8;
            margin-bottom: 30px;
        }

        .product-actions {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .quantity-selector {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--white);
            padding: 10px 20px;
            border-radius: var(--border-radius-sm);
            box-shadow: var(--shadow);
        }

        .qty-btn {
            width: 30px;
            height: 30px;
            border: none;
            background: var(--primary);
            color: var(--white);
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }

        .qty-btn:hover {
            background: var(--primary-dark);
        }

        .quantity-selector input {
            width: 50px;
            text-align: center;
            border: none;
            font-size: 1.125rem;
            font-weight: 600;
        }

        .btn-order-now {
            padding: 12px 30px;
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: var(--border-radius-sm);
            font-weight: 700;
            font-size: 1.1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: var(--transition);
            width: 100%;
        }

        .btn-order-now:hover {
            background: var(--primary-dark);
        }

        .product-meta {
            padding-top: 30px;
            border-top: 1px solid var(--gray-200);
        }

        .meta-item {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
            color: var(--gray-600);
        }

        .meta-item i {
            color: var(--primary);
            width: 20px;
        }

        /* Cart Modal */
        .cart-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: flex-end;
        }

        .cart-modal.active {
            display: flex;
        }

        .cart-modal-content {
            width: 100%;
            max-width: 500px;
            height: 100%;
            background: var(--white);
            box-shadow: -5px 0 25px rgba(0, 0, 0, 0.2);
            display: flex;
            flex-direction: column;
            animation: slideInRight 0.3s ease;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
            }

            to {
                transform: translateX(0);
            }
        }

        .cart-modal-header {
            padding: 20px;
            background: var(--dark);
            color: var(--white);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .cart-modal-header h3 {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.25rem;
        }

        .close-cart {
            background: none;
            border: none;
            color: var(--white);
            font-size: 1.5rem;
            cursor: pointer;
            padding: 5px;
        }

        .cart-modal-body {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
        }

        .cart-empty {
            text-align: center;
            padding: 40px 20px;
            color: var(--gray-600);
        }

        .cart-empty i {
            font-size: 4rem;
            color: var(--gray-300);
            margin-bottom: 20px;
        }

        .cart-empty p {
            margin-bottom: 20px;
        }

        .cart-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            background: var(--gray-100);
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .cart-item-info {
            flex: 1;
        }

        .cart-item-info h4 {
            font-size: 1rem;
            margin-bottom: 5px;
            color: var(--dark);
        }

        .cart-item-price {
            color: var(--gray-600);
            font-size: 0.875rem;
        }

        .cart-item-quantity {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .cart-item-quantity .qty-btn {
            width: 28px;
            height: 28px;
            border: none;
            background: var(--primary);
            color: var(--white);
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .cart-item-quantity .qty-btn:hover {
            background: var(--primary-dark);
        }

        .cart-item-quantity span {
            min-width: 25px;
            text-align: center;
            font-weight: 600;
        }

        .cart-item-total {
            min-width: 70px;
            text-align: right;
            font-weight: 700;
            color: var(--primary);
            font-size: 0.9rem;
        }

        .cart-item-remove {
            background: none;
            border: none;
            color: var(--danger);
            cursor: pointer;
            padding: 5px;
            font-size: 1rem;
        }

        .cart-modal-footer {
            padding: 20px;
            border-top: 2px solid var(--gray-200);
            background: var(--white);
        }

        .cart-total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .cart-total-label {
            font-size: 1rem;
            color: var(--gray-600);
        }

        .cart-total-amount {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
        }

        /* Checkout Button */
        .btn-checkout {
            width: 100%;
            padding: 15px;
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s;
        }

        .btn-checkout:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(111, 78, 55, 0.3);
        }

        .btn-checkout:disabled {
            background: var(--gray-400);
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* Checkout Form (Hidden by default, shown when checkout clicked) */
        .checkout-form {
            display: none;
        }

        .checkout-form.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: var(--dark);
            font-size: 0.9rem;
        }

        .form-group label .required {
            color: var(--danger);
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid var(--gray-200);
            border-radius: 8px;
            font-size: 1rem;
            font-family: inherit;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        .payment-methods {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }

        .payment-option {
            display: none;
        }

        .payment-option+label {
            display: block;
            padding: 12px 8px;
            text-align: center;
            border: 2px solid var(--gray-200);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 0.85rem;
        }

        .payment-option:checked+label {
            border-color: var(--primary);
            background: var(--primary);
            color: var(--white);
        }

        .payment-option+label i {
            display: block;
            font-size: 1.3rem;
            margin-bottom: 5px;
        }

        .btn-submit-order {
            width: 100%;
            padding: 15px;
            background: #e74c3c;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s;
            margin-top: 15px;
        }

        .btn-submit-order:hover {
            background: #c0392b;
        }

        .btn-back-to-cart {
            width: 100%;
            padding: 12px;
            background: var(--gray-200);
            color: var(--dark);
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 15px;
            transition: all 0.3s;
        }

        .btn-back-to-cart:hover {
            background: var(--gray-300);
        }

        /* Order Success Modal */
        .order-success-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 10000;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .order-success-modal.active {
            display: flex;
        }

        .order-success-content {
            background: var(--white);
            padding: 40px;
            border-radius: 15px;
            text-align: center;
            max-width: 400px;
            animation: scaleIn 0.3s ease;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0.8);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .order-success-content i {
            font-size: 4rem;
            color: var(--success);
            margin-bottom: 20px;
        }

        .order-success-content h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: var(--dark);
        }

        .order-success-content .kode-order {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary);
            margin: 15px 0;
            padding: 10px;
            background: var(--gray-100);
            border-radius: 8px;
        }

        .order-success-content p {
            color: var(--gray-600);
            margin-bottom: 20px;
        }

        .order-success-content .btn-primary {
            display: inline-block;
            padding: 12px 30px;
            background: var(--primary);
            color: var(--white);
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
        }

        /* Loading state */
        .btn-loading {
            position: relative;
            pointer-events: none;
        }

        .btn-loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border: 2px solid transparent;
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
        }

        @keyframes spin {
            to {
                transform: translateY(-50%) rotate(360deg);
            }
        }

        @media (max-width: 768px) {
            .product-detail-grid {
                grid-template-columns: 1fr;
            }

            .product-image {
                position: static;
            }

            .product-actions {
                flex-direction: column;
            }

            .quantity-selector {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <nav class="navbar">
                <a href="../index.php" class="logo">
                    <div class="logo-icon">
                        <i class="fas fa-coffee"></i>
                    </div>
                    <span>
                        <?= $settings['site_name'] ?>
                    </span>
                </a>

                <ul class="nav-menu">
                    <li><a href="../index.php#home" class="nav-link">Beranda</a></li>
                    <li><a href="menu.php" class="nav-link active">Menu</a></li>
                    <li><a href="about.php" class="nav-link">Tentang</a></li>
                    <li><a href="gallery.php" class="nav-link">Galeri</a></li>
                    <li><a href="contact.php" class="nav-link">Kontak</a></li>
                    <li>
                        <button class="cart-header-btn" id="cart-btn">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Keranjang</span>
                            <span class="cart-count" id="cart-count" style="display: none;">0</span>
                        </button>
                    </li>
                </ul>

                <div class="nav-toggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </nav>
        </div>
    </header>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1 class="page-title">
                <?= $product['nama_produk'] ?>
            </h1>
            <div class="page-breadcrumb">
                <a href="../index.php">Beranda</a>
                <span>/</span>
                <a href="menu.php">Menu</a>
                <span>/</span>
                <span>
                    <?= $product['nama_produk'] ?>
                </span>
            </div>
        </div>
    </section>

    <!-- Product Detail -->
    <section class="product-detail">
        <div class="container">
            <div class="product-detail-grid">
                <div class="product-image">
                    <div class="product-main-image">
                        <i class="fas fa-mug-hot"></i>
                    </div>
                </div>

                <div class="product-info">
                    <span class="product-category">
                        <?= $product['nama_kategori'] ?>
                    </span>
                    <h1>
                        <?= $product['nama_produk'] ?>
                    </h1>
                    <div class="product-price">
                        <?= formatCurrency($product['harga']) ?>
                    </div>
                    <p class="product-description">
                        <?= $product['deskripsi'] ?>
                    </p>

                    <div class="product-actions">
                        <div class="quantity-selector">
                            <button class="qty-btn qty-minus" type="button"
                                onclick="updateDetailQuantity(-1)">-</button>
                            <input type="number" id="detail-quantity" value="1" min="1" readonly>
                            <button class="qty-btn qty-plus" type="button" onclick="updateDetailQuantity(1)">+</button>
                        </div>
                        <button class="btn-order-now" onclick="orderNow()">
                            <i class="fas fa-shopping-bag"></i> Pesan Sekarang
                        </button>
                    </div>

                    <div class="product-meta">
                        <div class="meta-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Stok: Tersedia</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-clock"></i>
                            <span>Siap dalam 5-10 menit</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-star"></i>
                            <span>Rating: 4.8/5</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Products -->
            <?php if (!empty($related_products)): ?>
                <div style="margin-top: 80px;">
                    <h2 style="font-size: 2rem; margin-bottom: 30px;">Menu Terkait</h2>
                    <div class="menu-grid">
                        <?php foreach ($related_products as $related): ?>
                            <div class="menu-card">
                                <div class="menu-image-placeholder">
                                    <i class="fas fa-mug-hot"></i>
                                </div>
                                <div class="menu-content">
                                    <span class="menu-category">
                                        <?= $related['nama_kategori'] ?>
                                    </span>
                                    <h3 class="menu-title">
                                        <?= $related['nama_produk'] ?>
                                    </h3>
                                    <p class="menu-description">
                                        <?= $related['deskripsi'] ?>
                                    </p>
                                    <div class="menu-footer">
                                        <span class="menu-price">
                                            <?= formatCurrency($related['harga']) ?>
                                        </span>
                                        <a href="product-detail.php?id=<?= $related['id'] ?>" class="menu-btn">Pesan</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a href="../index.php" class="logo">
                        <div class="logo-icon">
                            <i class="fas fa-coffee"></i>
                        </div>
                        <span>
                            <?= $settings['site_name'] ?>
                        </span>
                    </a>
                    <p>
                        Kopi berkualitas tinggi dengan suasana nyaman.
                        Tempat sempurna untuk menikmati kopi favoritmu.
                    </p>
                    <div class="social-links">
                        <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>

                <div class="footer-links">
                    <h4 class="footer-title">Menu</h4>
                    <ul>
                        <li><a href="../index.php#home">Beranda</a></li>
                        <li><a href="menu.php">Menu</a></li>
                        <li><a href="about.php">Tentang</a></li>
                        <li><a href="gallery.php">Galeri</a></li>
                        <li><a href="contact.php">Kontak</a></li>
                    </ul>
                </div>

                <div class="footer-links">
                    <h4 class="footer-title">Jam Buka</h4>
                    <ul>
                        <li>Senin - Jumat: 07:00 - 22:00</li>
                        <li>Sabtu: 08:00 - 23:00</li>
                        <li>Minggu: 08:00 - 21:00</li>
                    </ul>
                </div>

                <div class="footer-links">
                    <h4 class="footer-title">Lokasi</h4>
                    <ul>
                        <li>
                            <?= $settings['address'] ?>
                        </li>
                        <li>
                            <?= $settings['phone'] ?>
                        </li>
                        <li>
                            <?= $settings['email'] ?>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy;
                    <?= date('Y') ?>
                    <?= $settings['site_name'] ?>. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    <!-- Cart Modal -->
    <div class="cart-modal" id="cart-modal">
        <div class="cart-modal-content">
            <div class="cart-modal-header">
                <h3><i class="fas fa-shopping-cart"></i> Keranjang Belanja</h3>
                <button class="close-cart" id="close-cart">&times;</button>
            </div>
            <div class="cart-modal-body" id="cart-items">
                <!-- Cart items will be loaded here -->
            </div>
            <div class="cart-modal-footer" id="cart-total">
                <!-- Cart total and checkout button will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Order Success Modal -->
    <div class="order-success-modal" id="order-success-modal">
        <div class="order-success-content">
            <i class="fas fa-check-circle"></i>
            <h3>Pesanan Berhasil!</h3>
            <div class="kode-order" id="success-kode-order"></div>
            <p>Silakan simpan kode order Anda. Staff kami akan memanggil Anda berdasarkan nomor meja.</p>
            <a href="menu.php" class="btn-primary" onclick="Cart.clearCart(); closeSuccessModal();">Pesan Lagi</a>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/cart.js"></script>
    <script>
        // Product data
        const currentProduct = {
            id: <?= $product['id'] ?>,
            name: '<?= addslashes($product['nama_produk']) ?>',
            price: <?= $product['harga'] ?>
        };

        // Update quantity in detail page
        function updateDetailQuantity(change) {
            const input = document.getElementById('detail-quantity');
            let value = parseInt(input.value) + change;
            if (value < 1) value = 1;
            input.value = value;
        }

        // Order now - adds current product to cart and opens checkout modal
        function orderNow() {
            const quantity = parseInt(document.getElementById('detail-quantity').value);
            // Clear cart first and add only this product
            Cart.clearCart();
            Cart.addItem(currentProduct.id, currentProduct.name, currentProduct.price, quantity);
            // Open cart modal with checkout
            Cart.openCartModal();
            // Show checkout form directly
            setTimeout(() => showCheckoutForm(), 300);
        }

        // Show checkout form
        function showCheckoutForm() {
            const cartTotal = document.getElementById('cart-total');
            if (cartTotal) {
                const total = Cart.getTotalPrice();
                cartTotal.innerHTML = `
                    <div class="checkout-form active">
                        <button type="button" class="btn-back-to-cart" onclick="showCartItems()">
                            <i class="fas fa-arrow-left"></i> Kembali ke Keranjang
                        </button>
                        <div class="cart-total-row">
                            <span class="cart-total-label">Total Pembayaran</span>
                            <span class="cart-total-amount">Rp ${total.toLocaleString('id-ID')}</span>
                        </div>
                        <form id="checkout-form" onsubmit="submitOrder(event)">
                            <div class="form-group">
                                <label>Nama Pelanggan <span class="required">*</span></label>
                                <input type="text" name="nama_pelanggan" id="nama_pelanggan" required placeholder="Masukkan nama Anda">
                            </div>
                            <div class="form-group">
                                <label>Nomor Meja/Kursi <span class="required">*</span></label>
                                <input type="text" name="nomor_meja" id="nomor_meja" required placeholder="Contoh: Meja 5 atau Kursi 12">
                            </div>
                            <div class="form-group">
                                <label>Metode Pembayaran <span class="required">*</span></label>
                                <div class="payment-methods">
                                    <input type="radio" name="metode_pembayaran" id="payment-tunai" value="tunai" class="payment-option" checked>
                                    <label for="payment-tunai">
                                        <i class="fas fa-money-bill-wave"></i>
                                        Tunai
                                    </label>
                                    
                                    <input type="radio" name="metode_pembayaran" id="payment-qris" value="qris" class="payment-option">
                                    <label for="payment-qris">
                                        <i class="fas fa-qrcode"></i>
                                        QRIS
                                    </label>
                                    
                                    <input type="radio" name="metode_pembayaran" id="payment-transfer" value="transfer" class="payment-option">
                                    <label for="payment-transfer">
                                        <i class="fas fa-university"></i>
                                        Transfer
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Catatan (Opsional)</label>
                                <textarea name="catatan" id="catatan" placeholder="Contoh: Tanpa gula, ice less, dll"></textarea>
                            </div>
                            <button type="submit" class="btn-submit-order" id="btn-submit">
                                <i class="fas fa-paper-plane"></i> Pesan
                            </button>
                        </form>
                    </div>
                `;
            }
        }

        // Show cart items view
        function showCartItems() {
            Cart.updateCartModal();
        }

        // Override cart update to include checkout button
        const originalUpdateCartModal = Cart.updateCartModal;
        Cart.updateCartModal = function () {
            const cartItemsContainer = document.getElementById('cart-items');
            const cartTotal = document.getElementById('cart-total');

            if (!cartItemsContainer) return;

            const cart = this.getCart();

            if (cart.length === 0) {
                cartItemsContainer.innerHTML = `
                    <div class="cart-empty">
                        <i class="fas fa-shopping-basket"></i>
                        <p>Keranjang Anda kosong</p>
                    </div>
                `;
                if (cartTotal) cartTotal.innerHTML = '';
                return;
            }

            // Show cart items
            let itemsHTML = '';
            cart.forEach(item => {
                const itemTotal = item.price * item.quantity;
                itemsHTML += `
                    <div class="cart-item">
                        <div class="cart-item-info">
                            <h4>${item.productName}</h4>
                            <p class="cart-item-price">Rp ${item.price.toLocaleString('id-ID')}</p>
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
                            Rp ${itemTotal.toLocaleString('id-ID')}
                        </div>
                        <button class="cart-item-remove" onclick="Cart.removeItem(${item.productId})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                `;
            });

            cartItemsContainer.innerHTML = itemsHTML;

            // Show total and checkout button
            if (cartTotal) {
                const total = this.getTotalPrice();
                cartTotal.innerHTML = `
                    <div class="cart-total-row">
                        <span class="cart-total-label">Total Pembayaran</span>
                        <span class="cart-total-amount">Rp ${total.toLocaleString('id-ID')}</span>
                    </div>
                    <button class="btn-checkout" onclick="showCheckoutForm()">
                        <i class="fas fa-credit-card"></i> Checkout
                    </button>
                `;
            }
        };

        // Submit Order
        async function submitOrder(event) {
            event.preventDefault();

            const btn = document.getElementById('btn-submit');
            const originalText = btn.innerHTML;
            btn.classList.add('btn-loading');
            btn.disabled = true;
            btn.innerHTML = 'Memproses...';

            try {
                const formData = new FormData(event.target);
                const cart = Cart.getCart();

                const cartItems = cart.map(item => ({
                    productId: item.productId,
                    productName: item.productName,
                    price: item.price,
                    quantity: item.quantity
                }));

                const requestData = {
                    nama_pelanggan: formData.get('nama_pelanggan'),
                    nomor_meja: formData.get('nomor_meja'),
                    metode_pembayaran: formData.get('metode_pembayaran'),
                    catatan: formData.get('catatan'),
                    cart_items: cartItems
                };

                const response = await fetch('process-order.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(requestData)
                });

                const result = await response.json();

                if (result.success) {
                    document.getElementById('success-kode-order').textContent = result.kode_order;
                    document.getElementById('order-success-modal').classList.add('active');
                    Cart.clearCart();
                    Cart.closeCartModal();
                } else {
                    alert('Gagal: ' + result.message);
                }

            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan. Silakan coba lagi.');
            } finally {
                btn.classList.remove('btn-loading');
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        }

        // Close success modal
        function closeSuccessModal() {
            document.getElementById('order-success-modal').classList.remove('active');
        }
    </script>
</body>

</html>