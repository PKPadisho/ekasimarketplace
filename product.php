<?php
session_start();
include "db.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if($id === 0){ header("Location: index2.php"); exit(); }

$stmt = mysqli_prepare($conn, "SELECT * FROM products WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);

if(!$product){ header("Location: index2.php"); exit(); }

$price    = floatval($product['price']);
$stock    = intval($product['stock'] ?? 0);
$category = $product['category'];
$isOwner  = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $product['user_id'];
$isLoggedIn = isset($_SESSION['user_id']);

// Related products - same category, exclude current
$rel = mysqli_prepare($conn, "SELECT * FROM products WHERE category = ? AND id != ? LIMIT 4");
mysqli_stmt_bind_param($rel, "si", $category, $id);
mysqli_stmt_execute($rel);
$related = mysqli_stmt_get_result($rel);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['product_name']); ?> - eKasi Marketplace</title>
    <link rel="stylesheet" href="bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css4.css">
</head>
<body>

<!-- Promo Banner -->
<div style="background:#198754; color:#fff; text-align:center;
            padding:9px; font-size:0.88rem; font-weight:600;">
    <i class="bi bi-truck"></i> Free delivery for orders above R1000
</div>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container-fluid d-flex align-items-center" style="height:60px;">
        <a href="index2.php" class="btn me-2">
            <i class="bi bi-arrow-left fs-5"></i>
        </a>
        <a class="navbar-brand" href="index2.php">
            <img src="uploads/ekasi logo blue.png" alt="eKasi Marketplace Logo" style="height:44px;">
        </a>
        <div class="ms-auto d-flex align-items-center gap-3">
            <button class="btn btn-success btn-sm position-relative"
                    data-bs-toggle="modal" data-bs-target="#cartModal">
                <i class="bi bi-cart3"></i> Cart
                <span id="cart-count"
                      class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">0</span>
            </button>
            <button class="btn btn-outline-danger btn-sm position-relative"
                    data-bs-toggle="modal" data-bs-target="#wishlistModal">
                <i class="bi bi-heart-fill"></i>
                <span id="wishlist-count"
                      class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">0</span>
            </button>
        </div>
    </div>
</nav>

<!-- Product Detail -->
<div class="container mt-4">
    <div class="row g-4">

        <!-- Image -->
        <div class="col-md-6">
            <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>"
                 class="img-fluid rounded-3 shadow-sm w-100"
                 style="max-height:420px; object-fit:cover;"
                 alt="<?php echo htmlspecialchars($product['product_name']); ?>">
        </div>

        <!-- Details -->
        <div class="col-md-6">
            <p class="text-muted mb-1">
                <a href="category.php?category=<?php echo urlencode($category); ?>"
                   class="text-decoration-none text-muted">
                    <i class="bi bi-tag"></i> <?php echo htmlspecialchars($category); ?>
                </a>
            </p>

            <h2 class="fw-bold"><?php echo htmlspecialchars($product['product_name']); ?></h2>

            <h3 class="text-success fw-bold my-3">R<?php echo number_format($price, 2); ?></h3>

            <p class="text-muted"><?php echo htmlspecialchars($product['details']); ?></p>

            <span class="stock-pill mb-3 d-inline-block">
                <i class="bi bi-box-seam"></i> Stock: <?php echo $stock; ?>
            </span>

            <div class="mt-3">
                <?php if($isOwner): ?>
                    <button class="btn btn-secondary w-100" disabled>Your Product</button>

                <?php elseif($stock < 1): ?>
                    <button class="btn btn-warning w-100" disabled>Out of Stock</button>

                <?php else: ?>
                    <div class="d-flex gap-2">
                        <button class="btn btn-success flex-grow-1 add-to-cart"
                                data-id="<?php echo intval($product['id']); ?>"
                                data-name="<?php echo htmlspecialchars($product['product_name']); ?>"
                                data-price="<?php echo $price; ?>">
                            <i class="bi bi-cart3"></i> Add to Cart
                        </button>
                        <button class="btn btn-outline-danger add-to-wishlist"
                                data-id="<?php echo intval($product['id']); ?>"
                                data-name="<?php echo htmlspecialchars($product['product_name']); ?>"
                                data-price="<?php echo $price; ?>">
                            <i class="bi bi-heart-fill"></i>
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <?php if(mysqli_num_rows($related) > 0): ?>
    <div class="mt-5">
        <h3 class="mb-4">Related Products</h3>
        <div class="row g-4">
        <?php while($rel_product = mysqli_fetch_assoc($related)):
            $rp = floatval($rel_product['price']);
            $rs = intval($rel_product['stock'] ?? 0);
            $rid = intval($rel_product['id']);
            $rIsOwner = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $rel_product['user_id'];
        ?>
            <div class="col-md-3 col-6">
                <div class="card product-card h-100">
                    <a href="product.php?id=<?php echo $rid; ?>" style="text-decoration:none;">
                        <img src="uploads/<?php echo htmlspecialchars($rel_product['image']); ?>"
                             class="card-img-top"
                             style="height:200px; object-fit:cover;"
                             alt="<?php echo htmlspecialchars($rel_product['product_name']); ?>">
                    </a>
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <h6>
                                <a href="product.php?id=<?php echo $rid; ?>"
                                   style="text-decoration:none; color:inherit;">
                                    <?php echo htmlspecialchars($rel_product['product_name']); ?>
                                </a>
                            </h6>
                            <p class="text-muted small"><?php echo htmlspecialchars($rel_product['category']); ?></p>
                            <span class="stock-pill">Stock: <?php echo $rs; ?></span>
                        </div>
                        <div class="mt-2">
                            <p class="fw-bold text-success mb-2">R<?php echo number_format($rp, 2); ?></p>
                            <?php if($rIsOwner): ?>
                                <button class="btn btn-secondary w-100" disabled>Your Product</button>
                            <?php elseif($rs < 1): ?>
                                <button class="btn btn-warning w-100" disabled>Out of Stock</button>
                            <?php else: ?>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-success flex-grow-1 add-to-cart"
                                            data-id="<?php echo $rid; ?>"
                                            data-name="<?php echo htmlspecialchars($rel_product['product_name']); ?>"
                                            data-price="<?php echo $rp; ?>">
                                        Add to Cart
                                    </button>
                                    <button class="btn btn-outline-danger add-to-wishlist"
                                            data-id="<?php echo $rid; ?>"
                                            data-name="<?php echo htmlspecialchars($rel_product['product_name']); ?>"
                                            data-price="<?php echo $rp; ?>">
                                        <i class="bi bi-heart-fill"></i>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Cart Modal -->
<div class="modal fade" id="cartModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-cart3"></i> Shopping Cart</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <ul id="cart-items" class="list-group mb-3"></ul>
                <div id="cart-empty-state" class="text-center py-3">
                    <p class="text-muted mb-2">Your cart is empty</p>
                    <a href="index2.php" class="btn btn-outline-success btn-sm">Continue Shopping</a>
                </div>
                <div id="cart-summary" style="display:none;">
                    <p class="mb-1">Subtotal: R<span id="cart-subtotal">0.00</span></p>
                    <p class="mb-1" id="delivery-line">Delivery: <span id="delivery-fee-display">R90.00</span></p>
                    <h5 class="border-top pt-2">Total: R<span id="cart-total">0.00</span></h5>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" id="checkout-btn">Checkout</button>
            </div>
        </div>
    </div>
</div>

<!-- Wishlist Modal -->
<div class="modal fade" id="wishlistModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-heart-fill text-danger"></i> Wishlist</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <ul id="wishlist-items" class="list-group"></ul>
                <p id="wishlist-empty" class="text-center text-muted mt-2" style="display:none;">
                    Your wishlist is empty.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Login Required -->
<div class="modal fade" id="loginRequiredModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Login Required</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <p>You need to login or register first.</p>
                <a href="Login.php" class="btn btn-success me-2">Login</a>
                <a href="Register.php" class="btn btn-outline-success">Register</a>
            </div>
        </div>
    </div>
</div>

<footer class="bg-light text-center p-3 mt-5">
    <p>&copy; 2026 eKasi Marketplace</p>
</footer>

<script src="bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
<script>
let cart = [];
let wishlist = [];
let total = 0;
let isLoggedIn = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;

const cartItems    = document.getElementById("cart-items");
const cartCount    = document.getElementById("cart-count");
const wishlistItems = document.getElementById("wishlist-items");
const wishlistCount = document.getElementById("wishlist-count");

function updateCart() {
    cartItems.innerHTML = "";
    const emptyState = document.getElementById("cart-empty-state");
    const summary    = document.getElementById("cart-summary");

    if(cart.length === 0){
        emptyState.style.display = "block";
        summary.style.display    = "none";
    } else {
        emptyState.style.display = "none";
        summary.style.display    = "block";
    }

    cart.forEach((item, index) => {
        const li = document.createElement("li");
        li.className = "list-group-item d-flex justify-content-between align-items-center";
        li.innerHTML = `
            <div><strong>${item.name}</strong><br>R${item.price.toFixed(2)}</div>
            <button class="btn btn-danger btn-sm" onclick="removeFromCart(${index})">Remove</button>
        `;
        cartItems.appendChild(li);
    });

    const subtotal    = cart.reduce((s, i) => s + i.price, 0);
    const deliveryFee = subtotal >= 1000 ? 0 : (cart.length > 0 ? 90 : 0);
    const grandTotal  = subtotal + deliveryFee;

    document.getElementById("cart-subtotal").textContent      = subtotal.toFixed(2);
    document.getElementById("cart-total").textContent         = grandTotal.toFixed(2);
    document.getElementById("delivery-fee-display").textContent =
        deliveryFee === 0 ? (cart.length > 0 ? "FREE \u{1F289}" : "R0.00") : "R90.00";

    cartCount.textContent    = cart.length;
    total = grandTotal;
}

function removeFromCart(index){
    cart.splice(index, 1);
    updateCart();
}

function updateWishlist(){
    wishlistItems.innerHTML = "";
    const empty = document.getElementById("wishlist-empty");
    empty.style.display = wishlist.length === 0 ? "block" : "none";

    wishlist.forEach((item, index) => {
        const li = document.createElement("li");
        li.className = "list-group-item d-flex justify-content-between align-items-center";
        li.innerHTML = `
            <div><strong>${item.name}</strong><br>R${item.price.toFixed(2)}</div>
            <div class="d-flex gap-1">
                <button class="btn btn-success btn-sm" onclick="moveToCart(${index})">Add to Cart</button>
                <button class="btn btn-danger btn-sm" onclick="removeWishlist(${index})">Remove</button>
            </div>
        `;
        wishlistItems.appendChild(li);
    });
    wishlistCount.textContent = wishlist.length;
}

function moveToCart(index){
    cart.push(wishlist[index]);
    wishlist.splice(index, 1);
    updateCart();
    updateWishlist();
}

function removeWishlist(index){
    wishlist.splice(index, 1);
    updateWishlist();
}


document.querySelectorAll(".add-to-cart").forEach(btn => {
    btn.addEventListener("click", () => {
        if(!isLoggedIn){
            new bootstrap.Modal(document.getElementById("loginRequiredModal")).show();
            return;
        }
        const name  = btn.dataset.name;
        const price = parseFloat(btn.dataset.price);
        if(isNaN(price)) return;
        cart.push({ id: btn.dataset.id, name, price });
        updateCart();
        btn.innerHTML = '<i class="bi bi-check-lg"></i> Added!';
        btn.classList.replace("btn-success", "btn-secondary");
        setTimeout(() => {
            btn.innerHTML = '<i class="bi bi-cart3"></i> Add to Cart';
            btn.classList.replace("btn-secondary", "btn-success");
        }, 1500);
    });
});

document.querySelectorAll(".add-to-wishlist").forEach(btn => {
    btn.addEventListener("click", () => {
        const name  = btn.dataset.name;
        const price = parseFloat(btn.dataset.price);
        if(isNaN(price)) return;
        if(wishlist.some(i => i.name === name)){
            alert(`"${name}" is already in your wishlist.`);
            return;
        }
        wishlist.push({ name, price });
        updateWishlist();
    });
});

document.getElementById("checkout-btn").addEventListener("click", () => {
    if(!isLoggedIn){
        new bootstrap.Modal(document.getElementById("loginRequiredModal")).show();
        return;
    }
    window.location.href = "index2.php";
});

updateCart();
updateWishlist();
</script>
</body>
</html>