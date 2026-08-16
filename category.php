<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Category - eKasi Marketplace</title>
	<link rel="stylesheet" href="bootstrap-5.3.8-dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
	<Link rel="stylesheet" href="css4.css">
</head>
<body> 

<nav class="navbar navbar-light bg-light shadow-sm">
   <div class="container-fluid">
     <a class="navbar-brand" href="index2.php"><i class="bi bi-arrow-left"></i> eKasi Marketplace</a>
	</div>
</nav>

<div class="container mt-4">

   <h2 id="category-title" class="mb-4"></h2>
   
   <div class="row g-4" id="products">
       <p class="text-muted">Loading products...</p>
   </div>
   
</div> 

<!-- Cart Modal (needed so cart works on this page too) -->
<div class="modal fade" id="cartModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Shopping Cart</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <ul id="cart-items" class="list-group mb-3"></ul>
            <h5>Total: R<span id="cart-total">0.00</span></h5>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline-danger me-auto" id="clear-cart-btn">Clear Cart</button>
            <button class="btn btn-success" id="checkout-btn">Checkout</button>
        </div>
    </div></div>
</div>

<!-- Wishlist Modal -->
<div class="modal fade" id="wishlistModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title"><i class="bi bi-heart-fill text-danger"></i> Wishlist</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <ul id="wishlist-items" class="list-group"></ul>
            <p id="wishlist-empty" class="text-muted text-center mt-2" style="display:none;">Your wishlist is empty.</p>
        </div>
    </div></div>
</div>
  

<script src="bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>

<script>
let cart = JSON.parse(localStorage.getItem("cart")) || [];
let wishlist = [];
let total    = 0;

const cartItemsList  = document.getElementById("cart-items");
const cartTotalSpan  = document.getElementById("cart-total");
const wishlistList   = document.getElementById("wishlist-items");
const wishlistEmpty = document.getElementById("wishlist-empty");

function saveCart() {
    localStorage.setItem("cart", JSON.stringify(cart));
}

function updateCart() {

    cartItemsList.innerHTML = "";
	
	let running = 0;

    cart.forEach((item, index) => {
        const li = document.createElement("li");

        li.className =
            "list-group-item d-flex justify-content-between align-items-center";

        li.innerHTML = `
            <div>
                <strong>${item.name}</strong><br>
                <span class="text-muted small">R${item.price.toFixed(2)}</span>
            </div>

            <div class="d-flex gap-1">
                <button class="btn btn-warning btn-sm"
                        onclick="moveToWishlist(${index})">
                    <i class="bi bi-heart-fill"></i>
                </button>

                <button class="btn btn-danger btn-sm"
                        onclick="removeFromCart(${index})">
                    Remove
                </button>

            </div>
        `;

        cartItemsList.appendChild(li);
		running += item.price;
    });

    total = running;
    cartTotalSpan.textContent = total.toFixed(2);
}

function moveToWishlist(index){

    wishlist.push(cart[index]);

    total -= cart[index].price;

    cart.splice(index, 1);

    updateCart();
    updateWishlist();
}

function updateWishlist(){

    wishlistList.innerHTML = "";
	
    wishlistEmpty.style.display = wishlist.length === 0 ? "block" : "none";
	
    wishlist.forEach((item, index) => {
        const li = document.createElement("li");

        li.className =
            "list-group-item d-flex justify-content-between align-items-center";

        li.innerHTML = `
            <div>
                <strong>${item.name}</strong><br>
                <span class="text-muted small">R${item.price.toFixed(2)}</span>
            </div>

            <div class="d-flex gap-1">
                <button class="btn btn-success btn-sm"
                        onclick="moveToCart(${index})">
                    Add to Cart
                </button>

                <button class="btn btn-danger btn-sm"
                        onclick="removeWishlist(${index})">
                    Remove
                </button>

            </div>
        `;

        wishlistList.appendChild(li);
    });


}

function moveToCart(index){

    cart.push(wishlist[index]);

    total += wishlist[index].price;

    wishlist.splice(index, 1);

    updateCart();
    updateWishlist();
}

function removeWishlist(index){

    wishlist.splice(index, 1);

    updateWishlist();
}

//Buttons
document.getElementById("clear-cart-btn").addEventListener("click", () => {
    cart  = [];
    total = 0;
    updateCart();
});
 
document.getElementById("checkout-btn").addEventListener("click", () => {
    if (cart.length === 0) {
        alert("Your cart is empty!");
        return;
    }
    window.location.href = "index2.php";
});
 
// ATTACH LISTENERS TO LOADED PRODUCT BUTTONS
function attachListeners() {
    document.querySelectorAll(".add-to-cart").forEach(btn => {
        btn.addEventListener("click", () => {
            const name  = btn.dataset.name;
            const price = parseFloat(btn.dataset.price);
            if (isNaN(price)) return;
            cart.push({ name, price });
            saveCart();
            updateCart();
           
            const original = btn.textContent;
            btn.textContent = "Added!";
            btn.classList.replace("btn-success", "btn-secondary");
            setTimeout(() => {
                btn.textContent = original;
                btn.classList.replace("btn-secondary", "btn-success");
            }, 1500);
        });
    });
 
    document.querySelectorAll(".add-to-wishlist").forEach(btn => {
        btn.addEventListener("click", () => {
            const name  = btn.dataset.name;
            const price = parseFloat(btn.dataset.price);
            if (isNaN(price)) return;
            if (wishlist.some(i => i.name === name)) {
                alert(`"${name}" is already in your wishlist.`);
                return;
            }
            wishlist.push({ name, price });
            updateWishlist();
        });
    });
}
 
//load products
const params   = new URLSearchParams(window.location.search);
const category = params.get("category");
 
document.getElementById("category-title").innerText = category || "Category";
 
if (category) {
    fetch("get_products.php?category=" + encodeURIComponent(category))
        .then(r => r.text())
        .then(data => {
            document.getElementById("products").innerHTML = data;
            attachListeners();  
        })
        .catch(() => {
            document.getElementById("products").innerHTML =
                "<p class='text-danger col-12'>Failed to load products. Check that get_product.php exists.</p>";
        });
} else {
    document.getElementById("products").innerHTML =
        "<p class='text-muted col-12'>No category selected.</p>";
}
 
</script>
</body>
</html>
