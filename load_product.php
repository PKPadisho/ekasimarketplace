<?php 

session_start();
include "db.php";

$sql = "SELECT * FROM products ORDER BY id DESC";
$result = mysqli_query($conn,$sql);
if (!$result){
	echo "<p class='text-danger col-12'>DB Error: " . mysqli_error($conn). "</p>";
	exit();
}

if(mysqli_num_rows($result) == 0){
	echo "<p class='text-muted col-12'>No products listed yet. Be first to Sell on eKasi Marketplace!</p>";
}

while($product = mysqli_fetch_assoc($result)){
	
	$loggedInUserId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
	$isOwner = ($loggedInUserId !== null && $product['user_id'] == $loggedInUserId);
	$price = floatval($product['price']);
	$stock = intval($product['stock'] ?? 0);
?>

<div class="col-md-3 col-6">
    <div class="card product-card h-100">

        <a href="product.php?id=<?php echo intval($product['id']); ?>" style="text-decoration:none;">
        <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>"
		     class="card-img-top"
			 alt="<?php echo htmlspecialchars($product['product_name']); ?>"
			 style="height:200px; object-fit:cover;">
        </a>
			 
	  <div class="card-body d-flex flex-column justify-content-between">

        <div>
            <h6><a href="product.php?id=<?php echo intval($product['id']); ?>" 
                   style="text-decoration:none; color:inherit;">
                   <?php echo htmlspecialchars($product['product_name']); ?>
             </a></h6>
            <p class="text-muted small"><?php echo htmlspecialchars($product['category']); ?></p>
			<p class="small"><?php echo htmlspecialchars($product['details']); ?></p>
			<p class="small text-muted bg-light d-inline-block px-3 py-1 rounded-pill border">
			   Stock: <?php echo $stock; ?>
			</p>
		</div>

        <div>
		    <p class="fw-bold mb-2">R<?php echo number_format($price,2); ?></p>
			
           
            <?php if($isOwner): ?>
			   <button class="btn btn-secondary w-100" disabled>Your Product</button>
			   
			<?php elseif($stock < 1): ?>
               <button class='btn btn-warning w-100' disabled>Out of Stock</button>
			   
			<?php else: ?>
			   <div class ="d-flex gap-2">
			   
           	   <button class="btn btn-success flex-grow-1 add-to-cart"
			           data-id="<?php echo intval($product['id']); ?>"
					   data-name="<?php echo htmlspecialchars($product['product_name']); ?>"
					   data-price="<?php echo $price; ?>">
					 Add to cart
			   </button>
			   
			   <button class="btn btn-outline-danger add-to-wishlist"
                       data-id="<?php echo intval($product['id']); ?>"
                       data-name="<?php echo htmlspecialchars($product['product_name']); ?>"
                       data-price="<?php echo $price; ?>" title="Wishlist">
                    <i class="bi bi-heart-fill"></i>
               </button>
			  
			  </div> 
           <?php endif; ?>
       </div>
     </div>
  </div>
</div>
<?php } ?>
			