<?php
session_start();
require_once '../SQL/database.php';
$conn = Database::getInstance()->getConnection();

if($_SERVER['REQUEST_METHOD'] === 'POST') {
$product_id = (int)($_POST['product_id'] ?? 0);
$quantity   = max(1, (int)($_POST['quantity'] ?? 1));
$size       = trim($_POST['size'] ?? '');
$color      = trim($_POST['color'] ?? '');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login Required</title>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
        <script>
            Swal.fire({
                icon: "warning",
                title: "Login Required",
                text: "You must log in first",
                showConfirmButton: true,
                confirmButtonText: "Go to Login"
            }).then(() => {
                window.location.href = "auth/login.php";
            });
        </script>
    </body>
    </html>';
    exit;
}

$user_id = $_SESSION['user_id'] ?? null;

if ($product_id <= 0 || $size === '' || $color === '') {
    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
    echo '<script>
        Swal.fire({
            icon: "error",
            title: "Invalid Input",
            text: "Please select size, color, and quantity",
            showConfirmButton: true
        }).then(() => {
            window.history.back();
        });
    </script>';
    exit;
}

$stmt = $conn->prepare("SELECT cart_id FROM cart WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $cart_id = $row['cart_id'];
} else {
    $stmt = $conn->prepare("INSERT INTO cart (user_id) VALUES (?)");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $cart_id = $conn->insert_id;
}

$stmt = $conn->prepare("SELECT variant_id FROM product_variant WHERE product_id = ? AND size = ? AND color = ?");
$stmt->bind_param("iss", $product_id, $size, $color);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $variant_id = $row['variant_id'];
} else {
    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
    echo '<script>
        Swal.fire({
            icon: "error",
            title: "Variant Not Found",
            text: "Selected variant does not exist",
            showConfirmButton: true
        }).then(() => {
            window.history.back();
        });
    </script>';
    exit;
}

$stmt = $conn->prepare("SELECT cart_items_id, cart_items_quantity FROM cart_items WHERE cart_id = ? AND variant_id = ?");
$stmt->bind_param("ii", $cart_id, $variant_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $newQty = $row['cart_items_quantity'] + $quantity;
    $stmt = $conn->prepare("UPDATE cart_items SET cart_items_quantity = ? WHERE cart_items_id = ?");
    $stmt->bind_param("ii", $newQty, $row['cart_items_id']);
    $stmt->execute();
	
} else {
    $stmt = $conn->prepare("INSERT INTO cart_items (cart_id, variant_id, cart_items_quantity) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $cart_id, $variant_id, $quantity);
    $stmt->execute();
}


echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
echo '<script>
    Swal.fire({
        icon: "success",
        title: "Added to Cart",
        text: "Product has been added to your cart",
        showConfirmButton: true
    }).then(() => {
       location.href = "shoping-cart.php";
    });
</script>';
header('Location: product.php');
exit;
}
$stmt = $conn->prepare("SELECT 
ci.cart_id,
    ci.cart_items_id,
    p.product_id,
    p.product_name,
    p.product_price,
	p.product_discount,
    pi.image_url,
    v.size,
    v.color,
    ci.cart_items_quantity
FROM cart_items ci
JOIN cart c ON ci.cart_id = c.cart_id
JOIN product_variant v ON ci.variant_id = v.variant_id
JOIN products p ON v.product_id = p.product_id
LEFT JOIN product_images pi ON p.product_id = pi.product_id
WHERE c.user_id = ?
GROUP BY ci.cart_items_id
");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$cartItems = [];
while ($row = $result->fetch_assoc()) {
	$cartItems[] = $row;
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Shoping Cart</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->	
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
	<link rel="stylesheet" type="text/css" href="vendor/perfect-scrollbar/perfect-scrollbar.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="css/util.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
<!--===============================================================================================-->
</head>
<body class="animsition">
	
	<!-- Header -->
	<?php include 'header-main.php'; ?>

	<!-- Cart -->
	


	<!-- breadcrumb -->
	<div class="container">
		<div class="bread-crumb flex-w p-l-25 p-r-15 p-t-30 p-lr-0-lg">
			<a href="index.html" class="stext-109 cl8 hov-cl1 trans-04">
				Home
				<i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i>
			</a>

			<span class="stext-109 cl4">
				Shoping Cart
			</span>
		</div>
	</div>
		

	<!-- Shoping Cart -->
	<form class="bg0 p-t-75 p-b-85" action="update-cart.php" method="post" >
		<div class="container">
			<div class="row">
				<div class="col-lg-10 col-xl-7 m-lr-auto m-b-50">
					<div class="m-l-25 m-r--38 m-lr-0-xl">
						<div class="wrap-table-shopping-cart">
							<table class="table-shopping-cart">
								<tr class="table_head">
									<th class="column-1">Product</th>
									<th class="column-2"></th>
									<th class="column-3">Price</th>
									<th class="column-4">Quantity</th>
									<th class="column-5">Total</th>
								</tr>

	
		<?php foreach ($cartItems as $item): ?>
								<tr class="table_row">
									<td class="column-1">
										<div class="how-itemcart1">
											<img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="IMG">
										</div>
									</td>
									<td class="column-2"><?php echo htmlspecialchars($item['product_name']); ?></td>
                                         <?php
                if ($item['product_discount'] > 0) {

                    $newPrice = $item['product_price'] - 
                                ($item['product_price'] * $item['product_discount'] / 100);

                   
                    
                } else {

                    $newPrice = $item['product_price'];
                }
                ?>
									<td class="column-3">$ <?php echo number_format($newPrice, 2); ?></td>
									
									<td class="column-4">
										<div class="wrap-num-product flex-w m-l-auto m-r-0">
											<div class="btn-num-product-down cl8 hov-btn3 trans-04 flex-c-m">
												<i class="fs-16 zmdi zmdi-minus"></i>
											</div>

											<input class="mtext-104 cl3 txt-center num-product" type="number" name="quantity[]" value="<?php echo htmlspecialchars($item['cart_items_quantity']); ?>">
    <input type="hidden" name="cart_items_id[]" value="<?= $item['cart_items_id'] ?>">

											<div class="btn-num-product-up cl8 hov-btn3 trans-04 flex-c-m">
												<i class="fs-16 zmdi zmdi-plus"></i>
											</div>
										</div>
									</td>
									<td class="column-5">$ <?php echo number_format($newPrice * $item['cart_items_quantity'], 2); ?></td>
								</tr>
<?php endforeach; ?>
								<tr class="table_row">
									<td class="column-1">
										<div class="how-itemcart1">
											<img src="images/item-cart-05.jpg" alt="IMG">
										</div>
									</td>
									<td class="column-2">Lightweight Jacket</td>
									<td class="column-3">$ 16.00</td>
									<td class="column-4">
										<div class="wrap-num-product flex-w m-l-auto m-r-0">
											<div class="btn-num-product-down cl8 hov-btn3 trans-04 flex-c-m">
												<i class="fs-16 zmdi zmdi-minus"></i>
											</div>

											<input class="mtext-104 cl3 txt-center num-product" type="number" name="num-product2" value="1">

											<div class="btn-num-product-up cl8 hov-btn3 trans-04 flex-c-m">
												<i class="fs-16 zmdi zmdi-plus"></i>
											</div>
										</div>
									</td>
									<td class="column-5">$ 16.00</td>
								</tr>
							</table>
						</div>

						<div class="flex-w flex-sb-m bor15 p-t-18 p-b-15 p-lr-40 p-lr-15-sm">
							<div class="flex-w flex-m m-r-20 m-tb-5">
								<input class="stext-104 cl2 plh4 size-117 bor13 p-lr-20 m-r-10 m-tb-5" type="text" name="coupon" placeholder="Coupon Code">
									
								<div class="flex-c-m stext-101 cl2 size-118 bg8 bor13 hov-btn3 p-lr-15 trans-04 pointer m-tb-5">
									Apply coupon
								</div>
							</div>

							<div class="flex-c-m stext-101 cl2 size-119 bg8 bor13 hov-btn3 p-lr-15 trans-04 pointer m-tb-10">
								
    <button type="submit" style="background:none; border:none; color:black; cursor:pointer; padding:0; font:inherit;">
        Update Cart
    </button>

							</div>
						</div>
					</div>
				</div>
			</form>

				<div class="col-sm-10 col-lg-7 col-xl-5 m-lr-auto m-b-50">
					<form action="process_checkout.php" method="POST" >





					
    <div class="bor10 p-lr-40 p-t-30 p-b-40 m-l-63 m-r-40 m-lr-0-xl p-lr-15-sm">
        <h4 class="mtext-109 cl2 p-b-30">Cart Totals</h4>

        <div class="flex-w flex-t bor12 p-b-13">
            <div class="size-208"><span class="stext-110 cl2">Subtotal:</span></div>
            <div class="size-209">
                <span class="mtext-110 cl2">
                    $<?php
						$subtotal = 0;
									foreach ($cartItems as $item) {
										$subtotal += $item['product_price'] * $item['cart_items_quantity'];
									}
									echo  number_format($subtotal, 2);
									
					?>
                </span>
            </div>
        </div>

        <div class="flex-w flex-t bor12 p-t-15 p-b-30">
            <div class="size-208 w-full-ssm"><span class="stext-110 cl2">Shipping Address:</span></div>
            <div class="size-209 p-r-18 p-r-0-sm w-full-ssm">
                <div class="p-t-15">
                    <div class="rs1-select2 rs2-select2 bor8 bg0 m-b-12">
                        <select class="js-select2" name="address_country" required>
                            <option value="">Select a country...</option>
                            <option value="Jordan">Jordan</option>
                            <option value="SaudiArabia">SaudiArabia</option>
                        </select>
                        <div class="dropDownSelect2"></div>
                    </div>
                    <div class="bor8 bg0 m-b-12">
                        <input class="stext-111 cl8 plh3 size-111 p-lr-15" type="text" name="address_city" placeholder="City" required>
                    </div>
                    <div class="bor8 bg0 m-b-12">
                        <input class="stext-111 cl8 plh3 size-111 p-lr-15" type="text" name="address_street" placeholder="Street Address" required>
                    </div>
                    <div class="bor8 bg0 m-b-22">
                        <input class="stext-111 cl8 plh3 size-111 p-lr-15" type="text" name="address_zipcode" placeholder="Postcode / Zip" required>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-t-20 p-b-20">
            <h5 class="stext-110 cl2 p-b-15">Payment Method</h5>
            <div class="form-check m-b-10">
                <input class="form-check-input" type="radio" name="payment_method" id="pay_delivery" value="delivery" checked onclick="toggleVisaForm(false)">
                <label class="form-check-label stext-111 cl6" for="pay_delivery">Cash on Delivery</label>
            </div>
            <div class="form-check m-b-10">
                <input class="form-check-input" type="radio" name="payment_method" id="pay_visa" value="visa" onclick="toggleVisaForm(true)">
                <label class="form-check-label stext-111 cl6" for="pay_visa">Visa / Credit Card</label>
            </div>
        </div>

        <div id="visa_details_form" style="display: none;" class="p-b-30">
			<div class="bor8 bg0 m-b-12">
        <input class="stext-111 cl8 plh3 size-111 p-lr-15" type="text" name="card_name" placeholder="Full Name on Card" >
    </div>
            <div class="bor8 bg0 m-b-12">
                <input class="stext-111 cl8 plh3 size-111 p-lr-15" type="text" name="card_number" placeholder="Card Number"  >
            </div>
            <div class="flex-w gap-2">
                <div class="bor8 bg0 m-b-12 size-111" style="width: 48%;">
                    <input class="stext-111 cl8 plh3 p-lr-15 w-full h-full" type="text" name="card_expiry" placeholder="MM/YY" >
                </div>
                <div class="bor8 bg0 m-b-12 size-111" style="width: 48%; margin-left: auto;">
                    <input class="stext-111 cl8 plh3 p-lr-15 w-full h-full" type="text" name="card_cvv" placeholder="CVV" >
                </div>
            </div>
        </div>

        <div class="flex-w flex-t p-t-27 p-b-33">
            <div class="size-208"><span class="mtext-101 cl2">Total:</span></div>
            <div class="size-209 p-t-1">
                <span class="mtext-110 cl2">$<?php
						$subtotal = 0;
									foreach ($cartItems as $item) {
										$subtotal += $item['product_price'] * $item['cart_items_quantity'];
									}
									echo  number_format($subtotal, 2);
									
					
					  ?></span>
            </div>
        </div>

        <button type="submit" name="checkout" class="flex-c-m stext-101 cl0 size-116 bg3 bor14 hov-btn3 p-lr-15 trans-04 pointer">
            Proceed to Checkout
        </button>
    </div>

				</div>
			</div></form>
		</div>
	
		
	
		

	<!-- Footer -->
	<footer class="bg3 p-t-75 p-b-32">
		<div class="container">
			<div class="row">
				<div class="col-sm-6 col-lg-3 p-b-50">
					<h4 class="stext-301 cl0 p-b-30">
						Categories
					</h4>

					<ul>
						<li class="p-b-10">
							<a href="#" class="stext-107 cl7 hov-cl1 trans-04">
								Women
							</a>
						</li>

						<li class="p-b-10">
							<a href="#" class="stext-107 cl7 hov-cl1 trans-04">
								Men
							</a>
						</li>

						<li class="p-b-10">
							<a href="#" class="stext-107 cl7 hov-cl1 trans-04">
								Shoes
							</a>
						</li>

						<li class="p-b-10">
							<a href="#" class="stext-107 cl7 hov-cl1 trans-04">
								Watches
							</a>
						</li>
					</ul>
				</div>

				<div class="col-sm-6 col-lg-3 p-b-50">
					<h4 class="stext-301 cl0 p-b-30">
						Help
					</h4>

					<ul>
						<li class="p-b-10">
							<a href="#" class="stext-107 cl7 hov-cl1 trans-04">
								Track Order
							</a>
						</li>

						<li class="p-b-10">
							<a href="#" class="stext-107 cl7 hov-cl1 trans-04">
								Returns 
							</a>
						</li>

						<li class="p-b-10">
							<a href="#" class="stext-107 cl7 hov-cl1 trans-04">
								Shipping
							</a>
						</li>

						<li class="p-b-10">
							<a href="#" class="stext-107 cl7 hov-cl1 trans-04">
								FAQs
							</a>
						</li>
					</ul>
				</div>

				<div class="col-sm-6 col-lg-3 p-b-50">
					<h4 class="stext-301 cl0 p-b-30">
						GET IN TOUCH
					</h4>

					<p class="stext-107 cl7 size-201">
						Any questions? Let us know in store at 8th floor, 379 Hudson St, New York, NY 10018 or call us on (+1) 96 716 6879
					</p>

					<div class="p-t-27">
						<a href="#" class="fs-18 cl7 hov-cl1 trans-04 m-r-16">
							<i class="fa fa-facebook"></i>
						</a>

						<a href="#" class="fs-18 cl7 hov-cl1 trans-04 m-r-16">
							<i class="fa fa-instagram"></i>
						</a>

						<a href="#" class="fs-18 cl7 hov-cl1 trans-04 m-r-16">
							<i class="fa fa-pinterest-p"></i>
						</a>
					</div>
				</div>

				<div class="col-sm-6 col-lg-3 p-b-50">
					<h4 class="stext-301 cl0 p-b-30">
						Newsletter
					</h4>

					<form>
						<div class="wrap-input1 w-full p-b-4">
							<input class="input1 bg-none plh1 stext-107 cl7" type="text" name="email" placeholder="email@example.com">
							<div class="focus-input1 trans-04"></div>
						</div>

						<div class="p-t-18">
							<button class="flex-c-m stext-101 cl0 size-103 bg1 bor1 hov-btn2 p-lr-15 trans-04">
								Subscribe
							</button>
						</div>
					</form>
				</div>
			</div>

			<div class="p-t-40">
				<div class="flex-c-m flex-w p-b-18">
					<a href="#" class="m-all-1">
						<img src="images/icons/icon-pay-01.png" alt="ICON-PAY">
					</a>

					<a href="#" class="m-all-1">
						<img src="images/icons/icon-pay-02.png" alt="ICON-PAY">
					</a>

					<a href="#" class="m-all-1">
						<img src="images/icons/icon-pay-03.png" alt="ICON-PAY">
					</a>

					<a href="#" class="m-all-1">
						<img src="images/icons/icon-pay-04.png" alt="ICON-PAY">
					</a>

					<a href="#" class="m-all-1">
						<img src="images/icons/icon-pay-05.png" alt="ICON-PAY">
					</a>
				</div>

				<p class="stext-107 cl6 txt-center">
					<!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved | Made with <i class="fa fa-heart-o" aria-hidden="true"></i> by <a href="https://colorlib.com" target="_blank">Colorlib</a> &amp; distributed by <a href="https://themewagon.com" target="_blank">ThemeWagon</a>
<!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->

				</p>
			</div>
		</div>
	</footer>


	<!-- Back to top -->
	<div class="btn-back-to-top" id="myBtn">
		<span class="symbol-btn-back-to-top">
			<i class="zmdi zmdi-chevron-up"></i>
		</span>
	</div>

<!--===============================================================================================-->	
	<script src="vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/animsition/js/animsition.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/bootstrap/js/popper.js"></script>
	<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/select2/select2.min.js"></script>
	<script>
		$(".js-select2").each(function(){
			$(this).select2({
				minimumResultsForSearch: 20,
				dropdownParent: $(this).next('.dropDownSelect2')
			});
		})
	</script>
<!--===============================================================================================-->
	<script src="vendor/MagnificPopup/jquery.magnific-popup.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/perfect-scrollbar/perfect-scrollbar.min.js"></script>
	<script>
		$('.js-pscroll').each(function(){
			$(this).css('position','relative');
			$(this).css('overflow','hidden');
			var ps = new PerfectScrollbar(this, {
				wheelSpeed: 1,
				scrollingThreshold: 1000,
				wheelPropagation: false,
			});

			$(window).on('resize', function(){
				ps.update();
			})
		});
	</script>
<!--===============================================================================================-->
	<script src="js/main.js"></script>
<script>
function toggleVisaForm(show) {
    const visaForm = document.getElementById('visa_details_form');
    const inputs = visaForm.querySelectorAll('input');
    
    if (show) {
        visaForm.style.display = 'block';
        // Make fields required only if visible
        inputs.forEach(input => input.setAttribute('required', 'true'));
    } else {
        visaForm.style.display = 'none';
        // Remove required attribute if hidden
        inputs.forEach(input => input.removeAttribute('required'));
    }
}
</script>
<script>
document.querySelectorAll('.wrap-num-product').forEach(wrap => {
    const input = wrap.querySelector('input.num-product');
    const btnUp = wrap.querySelector('.btn-num-product-up');
    const btnDown = wrap.querySelector('.btn-num-product-down');

    btnUp.addEventListener('click', () => {
        input.value = parseInt(input.value || 0) + 1;
    });

  
});
</script>

</body>
</html>