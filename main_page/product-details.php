<?php 
require_once '../SQL/database.php';
$conn = Database::getInstance()->getConnection();
$variant_id = (int)($_GET['variantid'] ?? 0);
$variant_id = (int)($_GET['variantid'] ?? 0);

$sizes = [];
$colors = [];

					
					if($variant_id > 0) {
					$stmt = $conn->prepare("SELECT 
    p.product_id,
    p.product_name,
    p.product_price,
    p.product_description,
    p.product_quantity,
    v.size,
    v.color
FROM products p
JOIN product_variant v ON p.product_id = v.product_id
WHERE p.product_id = ?");
if(!$stmt){
	die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $variant_id);
$stmt->execute();
$result = $stmt->get_result();
$variants = $result->fetch_all(MYSQLI_ASSOC);
if (empty($variants)) {
    die("Product not found");
}


$variant = $variants[0]; 
foreach ($variants as $row) {
    $sizes[] = $row['size'];
    $colors[] = $row['color'];
}
$sizes = array_unique($sizes);
$colors = array_unique($colors);

					}
					
?>
<!DOCTYPE html>
<html lang="en">
                    

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    	<link rel="icon" type="image/png" href="images/icons/favicon.png"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/iconic/css/material-design-iconic-font.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/linearicons-v1.0.0/icon-font.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="vendor/css-hamburgers/hamburgers.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/animsition/css/animsition.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="vendor/daterangepicker/daterangepicker.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/slick/slick.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/MagnificPopup/magnific-popup.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/perfect-scrollbar/perfect-scrollbar.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="css/util.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
    <style>
        /* Minimalist Input Styling */
.modern-qty-input {
    border: 2px solid #eee;
    border-radius: 12px;
    padding: 12px 15px;
    width: 100%;
    max-width: 120px;
    font-weight: 600;
    text-align: center;
    transition: all 0.3s ease;
    outline: none;
}

.modern-qty-input:focus {
    border-color: #000;
    background-color: #fff;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

/* Hide the annoying up/down arrows in Chrome/Safari/Firefox */
.modern-qty-input::-webkit-outer-spin-button,
.modern-qty-input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
.modern-qty-input[type=number] {
    -moz-appearance: textfield;
}
    body { background-color: #f8f9fa; font-family: 'Poppins', sans-serif; }
    .product-card { border-radius: 20px; overflow: hidden; background: #fff; }
    
    /* Custom Radio Buttons for Sizes */
    .size-option, .color-option { display: none; }
    .size-label {
        display: inline-block;
        padding: 8px 20px;
        border: 2px solid #eee;
        border-radius: 10px;
        cursor: pointer;
        transition: 0.3s;
        margin-right: 5px;
        font-weight: 500;
    }
    .size-option:checked + .size-label {
        border-color: #000;
        background-color: #000;
        color: #fff;
    }

    /* Color Swatches */
    .color-label {
        display: inline-block;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        border: 2px solid #eee;
        cursor: pointer;
        transition: 0.3s;
        margin-right: 10px;
        position: relative;
    }
    .color-option:checked + .color-label {
        transform: scale(1.2);
        border-color: #000;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .btn-add-cart {
        background: #000;
        color: #fff;
        border-radius: 50px;
        padding: 15px;
        font-weight: 600;
        letter-spacing: 1px;
        transition: 0.3s;
    }
    .btn-add-cart:hover { background: #333; color: #fff; transform: translateY(-2px); }
    /* Counter Styling */
.qty-counter {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #f1f1f1;
    border-radius: 50px;
    padding: 5px;
    width: 140px;
    user-select: none; /* Prevents text selection on double click */
}

.qty-btn {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    border: none;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    transition: all 0.2s ease;
}

.qty-btn:hover {
    background: #000;
    color: #fff;
}

.qty-btn:active {
    transform: scale(0.9);
}

.qty-input-readonly {
    border: none;
    background: transparent;
    width: 40px;
    text-align: center;
    font-weight: 700;
    font-size: 1.1rem;
    pointer-events: none; /* User cannot click or type here */
}
</style>
</head>
                    

<?php 

if (!$variant_id || empty($variants)) {
    header("Location: product.php");
    exit;
}
?>

<?php if(isset($variant_id)): ?>
  <body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-5">
            <div class="card product-card shadow-lg border-0">
                <div class="card-body p-5">
                    
                    <span class="badge bg-light text-dark mb-2 px-3 py-2 rounded-pill border">New Arrival</span>
                    
                    <h2 class="display-6 fw-bold mb-2"><?= htmlspecialchars($variant['product_name']) ?></h2>
                    
                    <div class="d-flex align-items-center mb-4">
                        <h3 class="text-dark fw-bold mb-0">$<?= htmlspecialchars($variant['product_price']) ?></h3>
                        <span class="ms-3 text-muted text-decoration-line-through small">$<?= number_format($variant['product_price'] * 1.2, 2) ?></span>
                    </div>

                    <p class="text-muted mb-4 lh-lg">
                        <?= htmlspecialchars($variant['product_description']) ?>
                    </p>

                    <form action="shoping-cart.php" method="POST" >
                        <input type="hidden" name="product_id" value="<?= $variant['product_id'] ?>">
                       
                        <div class="mb-4">
                            <label class="form-label d-block fw-bold small text-uppercase mb-3">Select Size</label>
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach ($sizes as $index => $size): ?>
                                    <input type="radio" name="size" value="<?= $size ?>" id="size-<?= $index ?>" class="size-option" <?= $index === 0 ? 'checked' : '' ?>>
                                    <label for="size-<?= $index ?>" class="size-label"><?= htmlspecialchars($size) ?></label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="mb-5">
                            <label class="form-label d-block fw-bold small text-uppercase mb-3">Color Choice</label>
                            <div class="d-flex flex-wrap">
                                <?php foreach ($colors as $index => $color): ?>
                                    <input type="radio" name="color" value="<?= $color ?>" id="color-<?= $index ?>" class="color-option" <?= $index === 0 ? 'checked' : '' ?>>
                                    <label for="color-<?= $index ?>" class="color-label" style="background-color: <?= htmlspecialchars($color) ?>;" title="<?= htmlspecialchars($color) ?>"></label>
                                <?php endforeach; ?>
                            </div>
                        </div>
<div class="mb-5">
    <label class="form-label d-block fw-bold small text-uppercase mb-3">Quantity</label>
    
    <div class="qty-counter">
        <button type="button" class="qty-btn" onclick="changeQty(-1)">
            <i class="fa fa-minus"></i>
        </button>

        <input type="text" 
               id="display-qty" 
               name="quantity" 
               value="1" 
               class="qty-input-readonly" 
               readonly>

        <button type="button" class="qty-btn" onclick="changeQty(1)">
            <i class="fa fa-plus"></i>
        </button>
    </div>
    
    <small class="text-muted mt-2 d-block px-2">
        Only <?= (int)$variant['product_quantity'] ?> left in stock
    </small>
</div>
                        <div class="d-grid gap-3">
                            <button type="submit" class="btn btn-add-cart" name="add-to-cart">
                                <i class="fa fa-shopping-cart me-2"></i> ADD TO CART
                            </button>
                            <a href="product.php" class="btn btn-link text-muted text-decoration-none small mt-2">
                                <i class="fa fa-arrow-left"></i> Continue Shopping
                            </a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
<script>
    // Max stock from PHP
    const maxStock = <?= (int)$variant['product_quantity'] ?>;
    const qtyInput = document.getElementById('display-qty');

    function changeQty(amount) {
        let currentVal = parseInt(qtyInput.value);
        let newVal = currentVal + amount;

        // Ensure value stays between 1 and maxStock
        if (newVal >= 1 && newVal <= maxStock) {
            qtyInput.value = newVal;
        }
    }
</script>
</body>






<?php endif; ?>