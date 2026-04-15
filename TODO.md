# TODO: Open Trip Admin & Transaction Fix

## Phase 1: Create admin/trips/ folder

- [x] 1.1 Create admin/trips/index.php - Daftar semua trip
- [x] 1.2 Create admin/trips/add.php - Form tambah trip
- [x] 1.3 Create admin/trips/edit.php - Form edit trip
- [x] 1.4 Create admin/trips/delete.php - Hapus trip
- [x] 1.5 Create admin/trips/bookings/index.php - Kelola booking trip
- [x] 1.6 Create admin/trips/bookings/view.php - Detail booking

## Phase 2: Fix Business Logic di functions.php

- [x] 2.1 Add createRentalOrder() function
- [x] 2.2 Add trip booking functions (createTripBooking, getAllBookings, etc.)
- [x] 2.3 Fix order status consistency
- [x] 2.4 Add update/delete functions untuk trip bookings
- [x] 2.5 Fix getProductsByCategory() to accept both 'available' and 'active' status

## Phase 3: Update Navigation

- [x] 3.1 Update admin/index.php sidebar - add Open Trip link
- [x] 3.2 Update admin/orders/index.php - add trips menu link
- [x] 3.3 Update admin/products/index.php - add trips menu link
- [x] 3.4 Update admin/categories/index.php - add trips menu link
- [x] 3.5 Update admin/messages/index.php - add trips menu link
- [x] 3.6 Update admin/reports/index.php - add trips menu link

## Phase 4: Fix Kasir POS

- [x] 4.1 Fix createRentalOrder parameter consistency
- [x] 4.2 Fix filter products to show both 'available' and 'active' status
- [x] 4.3 Add proper validation for checkout
- [x] 4.4 Add error handling for checkout process
