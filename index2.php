<!DOCTYPE html>
<html>

<?php
session_start();

if (
    isset($_GET['payment']) &&
    $_GET['payment'] === 'success' &&
    isset($_SESSION['pending_cart'])
) {

    include "db.php";

    $buyerId = $_SESSION['user_id'];
    $address = $_SESSION['pending_address'];
    $cart    = $_SESSION['pending_cart'];

    foreach($cart as $item){

        $productId = intval($item['id']);
        $price     = floatval($item['price']);

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO orders
            (buyer_id, product_id, address, amount, payment_status)
            VALUES (?, ?, ?, ?, 'Paid')"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "iisd",
            $buyerId,
            $productId,
            $address,
            $price
        );

        mysqli_stmt_execute($stmt);
		
		$upd = mysqli_prepare($conn,
            "UPDATE products SET stock = GREATEST(stock - 1, 0) WHERE id = ?");
        mysqli_stmt_bind_param($upd, "i", $productId);
        mysqli_stmt_execute($upd);

    }

    unset($_SESSION['pending_cart']);
    unset($_SESSION['pending_address']);
    unset($_SESSION['pending_total']);
}

?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eKasi Marketplace</title>
    <link rel="stylesheet" href="bootstrap-5.3.8-dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css4.css">
</head>

<body>

<!-- Promo Banner -->
<div style="background:#198754; color:#fff; text-align:center; 
            padding:9px; font-size:0.88rem; font-weight:600; 
            letter-spacing:0.3px;">
    <i class="bi bi-truck"></i> Free delivery for orders above R1000
</div>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
     <div class="container-fluid d-flex align-items-center" style="height:80px;">

       <button class="btn" data-bs-toggle="offcanvas" data-bs-target="#profileMenu">
              <i class="bi bi-list fs-4"></i>
       </button>
		

        <a class="navbar-brand" href="#" style="padding:0;">
           <img src="uploads/ekasi logo blue.png" 
                alt="eKasi Marketplace Logo">
        </a>

        <div class="d-flex align-items-center gap-3">

    <!-- Search -->
    <input type="text"
           id="search-input"
           class="form-control form-control-sm"
           placeholder="Search products..."
           style="width: 180px;">

    <!-- Cart -->
    <button class="btn btn-success btn-sm position-relative"
            data-bs-toggle="modal"
            data-bs-target="#cartModal">

        <i class="bi bi-cart3"></i> Cart

        <span id="cart-count"
              class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
            0
        </span>

    </button>

    <!-- Wishlist -->
    <button class="btn btn-outline-danger btn-sm position-relative"
            data-bs-toggle="modal"
            data-bs-target="#wishlistModal">

        <i class="bi bi-heart-fill"></i>

        <span id="wishlist-count"
              class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
            0
        </span>

    </button>
 
</div>
</div>
</nav>

<!-- Welcome -->
<div class="container mt-4 text-center">
    <h2>Welcome to eKasi Marketplace</h2>
    <p class="text-muted">
        Buy and sell products directly with traders near you safely and easily.
    </p>

</div>

<!-- Categories -->
<div class="container mt-5">

    <h3 class="mb-4">
        Featured Categories
    </h3>

   <div class="row text-center g-4">

        <!-- Electronics -->
        <div class="col-md-3 col-6">
           <div class="category-card category-filter" data-category="Electronics">
                <img src="uploads/download.jpg" alt="Electronics">
                <p>Electronics</p>
            </div>
        </div>

        <!-- Fashion -->
        <div class="col-md-3 col-6">
            <div class="category-card category-filter" data-category="Fashion">
                <img src="uploads/fashion.jpg" alt="Fashion">
                <p>Fashion</p>
            </div>
        </div>

        <!-- Personal Care -->
        <div class="col-md-3 col-6">
             <div class="category-card category-filter" data-category="Personal Care">
                <img src="uploads/personal.jpg" alt="Personal Care">
                <p>Personal Care</p>
            </div>
        </div>

        <!-- Home -->
        <div class="col-md-3 col-6">
            <div class="category-card category-filter" data-category="Home & Lifestyle">
                <img src="uploads/home.jpg" alt="Home & Lifestyle">
				
                <p>Home &amp; Lifestyle</p>
            </div>
        </div>
    </div>
</div>

<!-- Products -->
<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-4">

     <h3>
        Latest Products
     </h3>

     <button class="btn btn-primary" id="list-product-btn">
        <i class="bi bi-plus-lg"></i> List Product
      </button>

    </div>

    <div class="row g-4" id = "products-container">
	    <p class="text-muted" id="loading-msg">
		   loading products...
		</p>
	</div>

      
</div>

<!-- Sidebar --> 
<div class="offcanvas offcanvas-start" tabindex="-1" id="profileMenu">

    <div class="offcanvas-header">
        <h5> My Profile</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>

    <div class="offcanvas-body" id="sidebar-content"></div>
 
</div>

<!-- Address + Payment Modal -->
<div class = "modal fade" id = "addressModal" tabindex = "-1">	
	<div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Delivery Address</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
          <input type="text"
                 id="user-address"
                 class="form-control mb-3"
                 placeholder="Enter delivery address">
           
          <button class="btn btn-success w-100" id="pay-now-btn">
                Proceed to Payment
          </button>
       </div>
     </div>
   </div>
</div>   

<!-- Cart Modal -->
<div class="modal fade" id="cartModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Shopping Cart</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <ul id="cart-items" class="list-group mb-3"></ul>
                
                <div id="cart-empty-state" class="text-center py-3" style="display:none;">
                      <p class="text-muted mb-2">Your cart is empty</p>
                      <a href="index2.php" class="btn btn-outline-success btn-sm">Continue Shopping</a>
                </div>

                
                <div id="cart-summary">
                     <p class="mb-1">Subtotal: R<span id="cart-subtotal">0.00</span></p>
                     <p class="mb-1" id="delivery-line">
                        Delivery: <span id="delivery-fee-display">R90.00</span>
                    </p>
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
                <h5 class="modal-title">Wishlist</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <ul id="wishlist-items" class="list-group"></ul>
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
                <button type="button"class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center">
                <p>You need to login or register first.</p>
                <a href="Login.php" class="btn btn-success me-2">Login</a>
                <a href="Register.php" class="btn btn-outline-success">Register</a>
            </div>
        </div>
    </div>
</div>

<!-- Product Listing -->
<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">List Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="product-form" method = "POST" action = "add_product.php" enctype = "multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Product Image</label>
                        <input type="file" class="form-control" id="product-image" name="product-image" accept="image/*">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="product-name" name="product-name">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Product Category</label>
                        <select type="text" class="form-control" id="product-category" name="product-category">
						  <option value="">-- Select Category --</option>
						  <option value="Electronics">Electronics</option>
						  <option value="Fashion">Fashion</option>
						  <option value="Personal Care">Personal Care</option>
						  <option value="Home & Lifestyle">Home & Lifestyle</option>
						</select> 
						  
						  
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Product Price</label>
                        <input type="number" class="form-control" id="product-price" name="product-price" min="0">
                    </div>
					
					<div class="mb-3">
					    <label class="form-label">Stock</label>
						<input type="number" class="form-control" id="stock" name="stock" min="0">
                    </div>
						

                    <div class="mb-3">
                        <label class="form-label">Product Details</label>
                        <textarea class="form-control" id="product-details" name="product-details"></textarea>
                    </div>

                    <button type="submit" class="btn btn-success w-100">
                        Add Product
                    </button>
					
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Account Settings -->
<div class="modal fade" id="settingsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="password" id="new-password" class="form-control mb-3" placeholder="New Password">
                <button class="btn btn-success w-100" id="save-password-btn">
                    Save Password
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Admin Login Modal -->
<div class="modal fade" id="adminLoginModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title"><i class="bi bi-shield-lock"></i> Admin Panel</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">Enter your admin credentials.</p>
                <div id="admin-login-error" class="alert alert-danger d-none"></div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" id="admin-email" class="form-control" placeholder="admin@example.com">
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" id="admin-password" class="form-control">
                </div>
                <button class="btn btn-dark w-100" id="admin-login-btn">
                    &#128272; Login to Admin Panel
                </button>
            </div>
        </div>
    </div>
</div>
<script src="bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>

<script>
 
let cart = [];
let wishlist = [];
let total = 0;
let isLoggedIn = <?php echo isset($_SESSION['user_id'])? 'true' : 'false'; ?>;

const sidebarContent = document.getElementById("sidebar-content");

function updateSidebar(){
	
	      console.log(sidebarContent.innerHTML);
	      console.log("Sidebar loaded");
		  console.log(sidebarContent);

          const categoryLinks = `
		      <button class="list-group-item list-group-item-action 
			          d-flex justify-content-between align-items-center" 
			          id = "shop-category-btn">
			      Shop by category
				  <span id="cat-arrow"><i class="bi bi-chevron-right"></i></span>
			  </button>
			  
			  <div id = "category-list" style = "display:none;" class="ps-3 py-2">
			    <a class = "d-block py-1 text-decoration-none text-dark"
			        href = "category.php?category=Electronics"><i class="bi bi-laptop"></i> Electronics</a>
			  
			    <a class = "d-block py-1 text-decoration-none text-dark"
			       href = "category.php?category=Fashion"><i class="bi bi-bag-heart"></i> Fashion</a>
			 
			    <a class = "d-block py-1 text-decoration-none text-dark"
			       href = "category.php?category=Personal Care"><i class="bi bi-stars"></i> Personal Care</a>
			  
			    <a class = "d-block py-1 text-decoration-none text-dark"
			       href = "category.php?category=Home &amp; Lifestyle"><i class="bi bi-house-heart"></i> Home &amp; Lifestyle</a>
			  </div>
			`;  
			 
          if(isLoggedIn){

             sidebarContent.innerHTML = `     
          		<div class="list-group">	
			  
			       <button class="list-group-item list-group-item-action" id= "sell-btn">
			             <i class="bi bi-shop"></i> Sell on eKasi Marketplace
			       </button>
                 
			       ${categoryLinks}
			   
                   <a href="my_orders.php" class="list-group-item list-group-item-action">
                      <i class="bi bi-bag-check"></i> Orders
                   </a>		

                   <button class="list-group-item list-group-item-action
			                 d-flex justify-content-between align-items-center"
                            id="my-account-btn">
				        <i class="bi bi-person-circle"></i> My Account
                     <span id="my-account-arrow"><i class="bi bi-chevron-right"></i></span>				
                   </button>	

                <div id="account-menu" style="display:none;" class="ps-3 py-2">

                 <button class="btn btn-link text-start text-decoration-none text-dark d-block"
                            id="personal-details-btn">
                    Personal Details
                 </button>

                  <button class="btn btn-link text-start text-decoration-none text-dark d-block"
                          id="my-listings-btn">
                     My Listings
                 </button>
				 
				 

              </div>

              <div id="account-content" class="mt-3"></div>

              <hr>

              <button class="btn btn-outline-primary w-100"
                         id="logout-btn">
                  Logout
              </button> 
              
              <hr>
      
	          <button class="btn btn-dark w-100 mt-2" id="admin-panel-btn">
                  <i class="bi bi-shield-lock"></i> Admin Panel
              </button>

              </div>
        `;

    }else{

        sidebarContent.innerHTML = `

            <div class="list-group mb-3">
		  
			  <button class="list-group-item list-group-item-action" id= "sell-btn">
			      <i class="bi bi-shop"></i> Sell on eKasi Marketplace
			  </button>
			  
			  ${categoryLinks}
			  
			</div>
			
			  <hr>
	
              <p class = "text-center text-muted mb-2">Already have an account?</p>
			  <a href= "Login.php" class="btn btn-outline-success w-100">Login</a>
			  
			  <p class = "text-center text-muted mb-2">New to eKasi?</p>
			  <a href= "Register.php" class="btn btn-outline-success w-100">Register</a>
			  
			  <hr>

              <button class="btn btn-dark w-100" id="admin-panel-btn">
                  <i class="bi bi-shield-lock"></i> Admin Panel
              </button>
        `;

    }
}

document.addEventListener("DOMContentLoaded", function() {
    updateSidebar();
    updateCart();
	
//Bootstrap modals
const loginModal = new bootstrap.Modal(document.getElementById("loginRequiredModal"));
const productModal = new bootstrap.Modal(document.getElementById("productModal"));
const addressModal = new bootstrap.Modal(document.getElementById("addressModal"));
const settingsModal = new bootstrap.Modal(document.getElementById("settingsModal"));
const adminModal = new bootstrap.Modal(document.getElementById("adminLoginModal"));
    
    
//Checkout
document.getElementById("checkout-btn").addEventListener("click", () => {
	if(!isLoggedIn){loginModal.show(); return; }
	if(cart.length === 0) { alert("Your cart is empty!"); return ;}
	
	const saveAddress = localStorage.getItem("userAddress")
	if(saveAddress){
	   document.getElementById("user-address").value = saveAddress;
	 }
	 addressModal.show();	
}); 

//sell on ekasi - redirect to login or open product Modal
document.addEventListener("click", function(e){
	if(e.target.id === "sell-btn"){
		if(!isLoggedIn){
			loginModal.show();
	}else{
		const offcanvas = bootstrap.Offcanvas.getInstance(
		    document.getElementById("profileMenu")
		);
        if(offcanvas) offcanvas.hide();
        setTimeout(() => productModal.show(), 300);
    }
  }
});	
    
//Listproduct button on hp
document.getElementById("list-product-btn").addEventListener("click", () => {
    if(!isLoggedIn){loginModal.show();
        return;
    }
    productModal.show();
});
    
//Paynow
document.getElementById("pay-now-btn").addEventListener("click", () => {
    
	const address = document.getElementById("user-address").value;
	
	if(address === ""){alert("Please enter address");return;}
	
	localStorage.setItem("userAddress", address);
    
    // Save address to database
fetch("address.php", {
    method: "POST",
    headers: {"Content-Type": "application/x-www-form-urlencoded"},
    body: "address=" + encodeURIComponent(address)
})
.then(response => response.text())
.then(data => {
	
	return fetch("save_cart.php", {
	      method: "POST",
		  headers: { "Content-Type": "application/json" },
		  body: JSON.stringify({
			  cart: cart,
			  address: address,
			  total: total
		  })
       });
     }) 
	.then(response => response.json())
	.then(data => {
		if (data.status !== 'ok') {
			alert("Could not save cart. Please try again.");
            return;
		}
		
		const paymentForm = document.createElement("form");
		paymentForm.method = "POST";
		paymentForm.action = "https://sandbox.payfast.co.za/eng/process";
		paymentForm.innerHTML = `
		    <input type="hidden" name="merchant_id"  value="10000100">
			<input type="hidden" name="merchant_key" value="46f0cd694581a">
			<input type="hidden" name="amount"       value="${total.toFixed(2)}"> 
			<input type="hidden" name="item_name"    value="eKasi Marketplace Order">
			<input type="hidden" name="return_url"   value="http://ekasi-marketplace.rf.gd/index2.php?payment=success">
            <input type="hidden" name="cancel_url"   value="http://ekasi-marketplace.rf.gd/index2.php">
            <input type="hidden" name="notify_url"   value="http://ekasi-marketplace.rf.gd/save_order.php">
        `;
        document.body.appendChild(paymentForm);
        paymentForm.submit();
    })
    .catch(err => {
        console.error(err);
        alert("Something went wrong. Please try again.");
    });
});
    
//load products
fetch("load_product.php")
    .then(response => response.text())
    .then(data => {

        const container = document.getElementById("products-container");
        const loadingMsg = document.getElementById("loading-msg");
        if (loadingMsg) loadingMsg.remove();

        container.innerHTML = data;
	   
	   container.querySelectorAll(".add-to-cart").forEach(button => {
		   button.addEventListener("click", () => {
			   const name = button.dataset.name;
			   const price = parseFloat(button.dataset.price || 0);
			   
			  if (isNaN(price)) return;
                cart.push({ id: button.dataset.id, name, price });
                total += price;
                updateCart();
                button.textContent = "Added!";
                button.classList.replace("btn-success", "btn-secondary");
                
				setTimeout(() => {
                    button.textContent = "Add To Cart";
                    button.classList.replace("btn-secondary", "btn-success");
                }, 1500);
            });
        });

        container.querySelectorAll(".add-to-wishlist").forEach(btn => {
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
    })
    .catch(err => {
        console.error("load_product.php error:", err);
        document.getElementById("products-container").innerHTML = `
            <div class="col-12">
                <div class="alert alert-warning">
                     &#9888; Could not load products. Make sure
                    <strong>load_product.php</strong> exists and your database is running.
                </div>
            </div>
        `;
    });
});

//Logout
document.addEventListener("click", function(e){
    if(e.target.id === "logout-btn"){
        window.location.href = "logout.php";
    }
});

//My account toggle
document.addEventListener("click", function(e){

    if(e.target.id === "my-account-btn" || e.target.id === "my-account-arrow"){
        const menu = document.getElementById("account-menu");
        const arrow = document.getElementById("my-account-arrow");
        const content = document.getElementById("account-content");
        const isOpen = menu.style.display === "block";
        
        menu.style.display = isOpen ? "none" : "block";
        arrow.innerHTML = isOpen ? '<i class="bi bi-chevron-right"></i>' : '<i class="bi bi-chevron-down"></i>';
        
        if(isOpen){
            content.innerHTML = "";
        }
    }
});

//shop by category table
document.addEventListener("click", function(e){
	if(e.target.id === "shop-category-btn" || e.target.id == "cat-arrow"){
		const list = document.getElementById("category-list");
	    const arrow = document.getElementById("cat-arrow");
		const isOpen = list.style.display === "block";
		list.style.display = isOpen ? "none" : "block";
		arrow.innerHTML = isOpen ? '<i class="bi bi-chevron-right"></i>' : '<i class="bi bi-chevron-down"></i>';
     }
});



//Personal details button(shows details only when clicked)
document.addEventListener("click", function(e){

    if(e.target.id === "personal-details-btn"){

        document.getElementById("account-content").innerHTML = `
            <h6>Personal Details</h6>

            <p>
                <strong>Name:</strong>
                <?php echo htmlspecialchars($_SESSION["fullname"] ?? ''); ?>
            </p>

            <p>
                <strong>Email:</strong>
                <?php echo htmlspecialchars($_SESSION["email"] ?? ''); ?>
            </p>

            <p>
                <strong>Address:</strong>
                <?php
                echo !empty($_SESSION["address"])
                    ? htmlspecialchars($_SESSION["address"])
                    : "Not added";
                ?>
            </p>
        `;
    }

});

//My listing(opens user's listings)
document.addEventListener("click", function(e){

    if(e.target.id === "my-listings-btn"){

        fetch("my_listings.php")
            .then(response => response.text())
            .then(data => {

                document.getElementById("account-content").innerHTML = `
                    <h6>My Listings</h6>
                    ${data}
                `;

            });
    }

});



		 
//Cart
const cartItems = document.getElementById("cart-items");
const cartTotal = document.getElementById("cart-total");
const cartCount = document.getElementById("cart-count");


function updateCart() {
    cartItems.innerHTML = "";
    
    const emptyState = document.getElementById("cart-empty-state");
    const summary    = document.getElementById("cart-summary");
    
    if(cart.length === 0){
        emptyState.style.display = "block";
        summary.style.display = "none";
    }else {
        emptyState.style.display = "none";
        summary.style.display = "block";
    }

    cart.forEach((item, index) => {
        const li = document.createElement("li");
        li.className = "list-group-item d-flex justify-content-between align-items-center";
        li.innerHTML = `
            <div>
                <strong>${item.name}</strong><br>
                R${item.price.toFixed(2)}
            </div>
            <div class="d-flex gap-1">
               <button class="btn btn-warning btn-sm"
                    onclick="moveToWishlist(${index})"><i class="bi bi-heart-fill"></i></button>
               <button class="btn btn-danger btn-sm"
                       onclick="removeFromCart(${index})">Remove</button>
           </div>
        `;
        cartItems.appendChild(li);
    });

    // Delivery fee logic
    const subtotal = cart.reduce((sum, item) => sum + item.price, 0);
    const deliveryFee = subtotal >= 1000 ? 0 : (cart.length > 0 ? 90 : 0);
    const grandTotal = subtotal + deliveryFee;

    document.getElementById("cart-subtotal").textContent  = subtotal.toFixed(2);
    document.getElementById("cart-total").textContent     = grandTotal.toFixed(2);
    document.getElementById("delivery-fee-display").textContent = 
        deliveryFee === 0 ? (cart.length > 0 ? "FREE \u{1F289} " : "R0.00") : "R90.00";
    document.getElementById("delivery-line").style.color = 
        deliveryFee === 0 && cart.length > 0 ? "green" : "inherit";

    cartCount.textContent = cart.length;

    total = grandTotal;
}
function removeFromCart(index){
    cart.splice(index, 1);
    updateCart(); 
}

function moveToWishlist(index){
    wishlist.push(cart[index]);
    cart.splice(index,1);
    updateCart();
    updateWishlist();
}

//Product for submit 
document.getElementById("product-form").addEventListener("submit", function(e){

  
    const image = document.getElementById("product-image").files[0];
    const name = document.getElementById("product-name").value;
    const category = document.getElementById("product-category").value;
    const price = document.getElementById("product-price").value;
	const stock = document.getElementById("stock").value;
    const details = document.getElementById("product-details").value;

    if(!image || !name || !category || !price || !stock || !details){
        alert("Please fill in all fields.");
		e.preventDefault();
        return;
    }

});

    
//wishlist
const wishlistItems = document.getElementById("wishlist-items");
const wishlistCount = document.getElementById("wishlist-count");

function updateWishlist(){

    wishlistItems.innerHTML = "";

    wishlist.forEach((item, index) => {

        const li = document.createElement("li");

        li.className =
            "list-group-item d-flex justify-content-between align-items-center";

        li.innerHTML = `
            <div>
                <strong>${item.name}</strong><br>
                R${item.price}
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

        wishlistItems.appendChild(li);
    });

    wishlistCount.textContent = wishlist.length;
}


function moveToCart(index){
    cart.push(wishlist[index]);
    wishlist.splice(index,1);
    updateCart();
    updateWishlist();
}

function removeWishlist(index){
    wishlist.splice(index,1);
    updateWishlist();
}

//Search
document.getElementById("search-input").addEventListener("keyup", () => {
    const value = document.getElementById("search-input").value.toLowerCase();
    document.querySelectorAll(".product-card").forEach(card => {
        card.parentElement.style.display = 
		    card.innerText.toLowerCase().includes(value) ? "block" : "none";
    });
});


//Category filter
document.querySelectorAll(".category-filter").forEach(cat => {
    cat.addEventListener("click", () => {
		window.location.href = "category.php?category=" + encodeURIComponent(cat.dataset.category);
	
	});
	
});

//Settings
document.addEventListener("click", function(e){
    if(e.target.id === "account-settings-btn"){settingsModal.show();}
});

// Open admin modal from sidebar
document.addEventListener("click", function(e) {
    if (e.target.id === "admin-panel-btn") {
        window.location.href = "Admin/admin_login.php";
    }
});

// Admin login submit
document.getElementById("admin-login-btn").addEventListener("click", function() {
    const email    = document.getElementById("admin-email").value.trim();
    const password = document.getElementById("admin-password").value;
    const errorDiv = document.getElementById("admin-login-error");

    errorDiv.classList.add("d-none");

    if (!email || !password) {
        errorDiv.textContent = "Please enter both email and password.";
        errorDiv.classList.remove("d-none");
        return;
    }

    const btn = this;
    btn.disabled = true;
    btn.textContent = "Logging in...";

    fetch("Admin/admin_check_login.php", {
        method: "POST",
		credentials: "same-orgin",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "email=" + encodeURIComponent(email) +
              "&password=" + encodeURIComponent(password)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            window.location.href = "Admin/admin_dashboard.php";
        } else {
            errorDiv.textContent = data.message || "Invalid credentials.";
            errorDiv.classList.remove("d-none");
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-shield-lock"></i> Login to Admin Panel';
        }
    })
    .catch(() => {
        errorDiv.textContent = "Connection error. Please try again.";
        errorDiv.classList.remove("d-none");
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-shield-lock"></i> Login to Admin Panel';
    });
});

// Allow Enter key on password field
document.getElementById("admin-password").addEventListener("keydown", function(e) {
    if (e.key === "Enter") document.getElementById("admin-login-btn").click();
});



document.addEventListener("click", function(e){
    if(e.target.id === "save-password-btn"){
        const newPassword = document.getElementById("new-password").value;
        if(newPassword === ""){alert("Enter a password"); return;}

        localStorage.setItem(
            "userPassword",
            newPassword
        );

        alert("Password updated!");

        settingsModal.hide();

    }

});


</script> 

<!-- Footer -->
<footer class="bg-light text-center p-3 mt-5">

    <p>
        &copy; 2026 ekasi Marketplace
    </p>

</footer>

</body>
</html>