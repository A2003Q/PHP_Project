<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once '../SQL/Database.php';
$conn = Database::getInstance()->getConnection();

?>
<?php

// Get filters from URL
$category_id = (int)($_GET['id'] ?? 0);
$sort = $_GET['sort'] ?? 'default';
$price_range = $_GET['price'] ?? 'all'; // 👈 NEW
$search = $_GET['query'] ?? '';

// Build ORDER BY
$orderBy = '';
if ($sort === 'name_asc') {
    $orderBy = ' ORDER BY p.product_name ASC';
} elseif ($sort === 'name_desc') {
    $orderBy = ' ORDER BY p.product_name DESC';
} elseif ($sort === 'price_asc') {
    $orderBy = ' ORDER BY p.product_price ASC';
} elseif ($sort === 'price_desc') {
    $orderBy = ' ORDER BY p.product_price DESC';
}

// Build price condition
$priceCondition = "";
if ($price_range !== 'all') {
    if ($price_range === '0-50') {
        $priceCondition = " AND p.product_price BETWEEN 0 AND 50 ";
    } elseif ($price_range === '50-100') {
        $priceCondition = " AND p.product_price BETWEEN 50.01 AND 100 ";
    } elseif ($price_range === '100-150') {
        $priceCondition = " AND p.product_price BETWEEN 100.01 AND 150 ";
    } elseif ($price_range === '150-200') {
        $priceCondition = " AND p.product_price BETWEEN 150.01 AND 200 ";
    } elseif ($price_range === '200+') {
        $priceCondition = " AND p.product_price > 200 ";
    }
}

if (!empty(trim($search))) {
    $searchSafe = "%$search%";
    $sql = "
        SELECT p.*, pi.image_url
        FROM products p
        LEFT JOIN product_images pi ON p.product_id = pi.product_id
        WHERE (p.product_name LIKE ? OR p.product_description LIKE ?)
        $priceCondition
        $orderBy
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $searchSafe, $searchSafe);
} else {
    $baseWhere = $priceCondition;
    if ($category_id > 0) {
        $baseWhere = " WHERE pc.categories_id = ? $priceCondition ";
        $sql = "
            SELECT p.*, pi.image_url
            FROM products p
            JOIN product_categories pc ON p.product_id = pc.product_id
            LEFT JOIN product_images pi ON p.product_id = pi.product_id
            $baseWhere
            $orderBy
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $category_id);
    } else {
        $whereClause = $priceCondition ? " WHERE " . ltrim($priceCondition, " AND ") : "";
        $sql = "
            SELECT p.*, pi.image_url
            FROM products p
            LEFT JOIN product_images pi ON p.product_id = pi.product_id
            $whereClause
            $orderBy
        ";
        $stmt = $conn->prepare($sql);
    }
}

// 3. Execute
$stmt->execute();
$result   = $stmt->get_result();
$products = $result->fetch_all(MYSQLI_ASSOC);
?>




<?php 

$sizes = [];
$colors = [];

					$variant_id = (int)($_GET['variantid'] ?? 0);
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
	<title>Product</title>
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
<!--===============================================================================================-->
</head>
<body class="animsition">
	
	<!-- Header -->
		<header class="header-v4">
		
	<!-- Header desktop -->
<div class="container-menu-desktop">
    <!-- Topbar -->
    <div class="wrap-menu-desktop">
        <nav class="limiter-menu-desktop container">
            
            <!-- Logo desktop -->		
            <a href="#" class="logo">
                <img src="images/icons/logo-01.png" alt="IMG-LOGO">
            </a>

            <!-- Menu desktop -->
            <div class="menu-desktop">
                <ul class="main-menu">
                    <li class="active-menu">
                        <a href="index.php">Home</a>
                    </li>

                    <li>
                        <a href="product.php">Shop</a>
                    </li>

                    <li class="label1" data-label1="hot">
                        <a href="shoping-cart.html">Features</a>
                    </li>

                    <li>
                        <a href="blog.html">Blog</a>
                    </li>

                    <li>
                        <a href="about.html">About</a>
                    </li>

                    <li>
                        <a href="contact.html">Contact</a>
                    </li>
				  <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>	

        <li class="menu-item">
  <a href="#">My Account</a>

  <ul class="sub-menu">
    <li><a href="index.html">My info</a></li>
    <li><a href="auth/logout.php">Logout</a></li>
  </ul>
</li> <?php endif;  ?>
                </ul>

            </div>	
  
                
                     <!-- Login and Signup buttons -->
            <div class="auth-buttons-container m-r-20">
    <?php if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true): ?>
    <div class="auth-buttons flex-w flex-r-m">
        <a href="auth/login.php" class="flex-c-m stext-101 cl0 size-107 bg3 bor2 hov-btn3 p-lr-15 trans-04 m-r-10">
            Login
        </a>
        <a href="auth/sign-up.php" class="flex-c-m stext-101 cl0 size-107 bg1 bor1 hov-btn1 p-lr-15 trans-04">
            Sign Up
        </a>
    </div>
                    <?php endif; ?>
                </ul>
            </div>	
            
            <!-- Icon header and auth buttons -->
            <div class="wrap-icon-header flex-w flex-r-m">
                <?php if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true): ?>
              
                <?php endif; ?>

                

                <!-- Icon header -->
                <div class="icon-header-group flex-w flex-r-m">
                    <div class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 js-show-modal-search">
                        <i class="zmdi zmdi-search"></i>
                    </div>

                    <div class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 icon-header-noti js-show-cart" data-notify="2">
                        <i class="zmdi zmdi-shopping-cart"></i>
                    </div>

                    <a href="#" class="dis-block icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 icon-header-noti" data-notify="0">
                        <i class="zmdi zmdi-favorite-outline"></i>
                    </a>
                </div>
            </div>
        </nav>
    </div>	
</div>

		<!-- Header Mobile -->
		


		<!-- Menu Mobile -->
	<!-- Menu Mobile -->
<div class="menu-mobile">
  <ul class="topbar-mobile">
    <li>
      <div class="left-top-bar">
        Free shipping for standard order over $100
      </div>
    </li>
    <li>
      <div class="right-top-bar flex-w h-full">
        <a href="#" class="flex-c-m p-lr-10 trans-04">Help & FAQs</a>
        <a href="#" class="flex-c-m p-lr-10 trans-04">EN</a>
        <a href="#" class="flex-c-m p-lr-10 trans-04">USD</a>
      </div>
    </li>
  </ul>

  <ul class="main-menu-m">
    <li><a href="index.html">Home</a></li>
    <li><a href="product.html">Shop</a></li>
    <li><a href="shoping-cart.html" class="label1 rs1" data-label1="hot">Features</a></li>
    <li><a href="blog.html">Blog</a></li>
    <li><a href="about.html">About</a></li>
    <li><a href="contact.html">Contact</a></li>

    <!-- Simple Login / Signup - Mobile Optimized -->
    <li class="p-t-15 p-b-10 text-center">
      <a href="login.html" class="flex-c-m stext-101 cl0 size-102 bg3 bor2 hov-btn3 p-lr-15 p-tb-5 trans-04 m-b-8">
        Login
      </a>
      <a href="register.html" class="flex-c-m stext-101 cl0 size-102 bg1 bor1 hov-btn1 p-lr-15 p-tb-5 trans-04">
        Sign Up
      </a>
	  
    </li>
  </ul>
</div>

		<!-- Modal Search -->
		<div class="modal-search-header flex-c-m trans-04 js-hide-modal-search">
			<div class="container-search-header">
				<button class="flex-c-m btn-hide-modal-search trans-04 js-hide-modal-search">
					<img src="images/icons/icon-close2.png" alt="CLOSE">
				</button>

				<form class="wrap-search-header flex-w p-l-15">
					<button class="flex-c-m trans-04">
						<i class="zmdi zmdi-search"></i>
					</button>
					<input class="plh3" type="text" name="search" placeholder="Search...">
				</form>
			</div>
		</div>
	</header>

	<!-- Cart -->
	<div class="wrap-header-cart js-panel-cart">
		<div class="s-full js-hide-cart"></div>

		<div class="header-cart flex-col-l p-l-65 p-r-25">
			<div class="header-cart-title flex-w flex-sb-m p-b-8">
				<span class="mtext-103 cl2">
					Your Cart
				</span>

				<div class="fs-35 lh-10 cl2 p-lr-5 pointer hov-cl1 trans-04 js-hide-cart">
					<i class="zmdi zmdi-close"></i>
				</div>
			</div>
			
			<div class="header-cart-content flex-w js-pscroll">
				<ul class="header-cart-wrapitem w-full">
					<li class="header-cart-item flex-w flex-t m-b-12">
						<div class="header-cart-item-img">
							<img src="images/item-cart-01.jpg" alt="IMG">
						</div>

						<div class="header-cart-item-txt p-t-8">
							<a href="#" class="header-cart-item-name m-b-18 hov-cl1 trans-04">
								White Shirt Pleat
							</a>

							<span class="header-cart-item-info">
								1 x $19.00
							</span>
						</div>
					</li>

					<li class="header-cart-item flex-w flex-t m-b-12">
						<div class="header-cart-item-img">
							<img src="images/item-cart-02.jpg" alt="IMG">
						</div>

						<div class="header-cart-item-txt p-t-8">
							<a href="#" class="header-cart-item-name m-b-18 hov-cl1 trans-04">
								Converse All Star
							</a>

							<span class="header-cart-item-info">
								1 x $39.00
							</span>
						</div>
					</li>

					<li class="header-cart-item flex-w flex-t m-b-12">
						<div class="header-cart-item-img">
							<img src="images/item-cart-03.jpg" alt="IMG">
						</div>

						<div class="header-cart-item-txt p-t-8">
							<a href="#" class="header-cart-item-name m-b-18 hov-cl1 trans-04">
								Nixon Porter Leather
							</a>

							<span class="header-cart-item-info">
								1 x $17.00
							</span>
						</div>
					</li>
				</ul>
				
				<div class="w-full">
					<div class="header-cart-total w-full p-tb-40">
						Total: $75.00
					</div>

					<div class="header-cart-buttons flex-w w-full">
						<a href="shoping-cart.html" class="flex-c-m stext-101 cl0 size-107 bg3 bor2 hov-btn3 p-lr-15 trans-04 m-r-8 m-b-10">
							View Cart
						</a>

						<a href="shoping-cart.html" class="flex-c-m stext-101 cl0 size-107 bg3 bor2 hov-btn3 p-lr-15 trans-04 m-b-10">
							Check Out
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>

	
	<!-- Product -->
	<section class="bg0 p-t-23 p-b-140">
		<div class="container">
			<div class="p-b-10">
				<h3 class="ltext-103 cl5">
					Product Overview
				</h3>
			</div>

			<div class="flex-w flex-sb-m p-b-52">
				<div class="flex-w flex-l-m filter-tope-group m-tb-10">
					
				
				<form action="" method="get">
			<button class="stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-5 <?= ($category_id == 0) ? 'how-active1' : '' ?>" data-filter="*">
    All Products
</button>

<?php 
	$stmt = $conn->prepare("SELECT * FROM categories");
$stmt->execute();
$result = $stmt->get_result();
$categories = $result->fetch_all(MYSQLI_ASSOC);
foreach ($categories as $cat): ?>
    <button 
        class="stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-5 <?= ($cat['categories_id'] == $category_id) ? 'how-active1' : '' ?>"
        type="submit"
        name="id"
        value="<?= $cat['categories_id'] ?>"
        data-id="<?= $cat['categories_id'] ?>">
        <?= htmlspecialchars($cat['categories_name']) ?>
    </button>
<?php endforeach; ?>
	


					</form>

			
			



				</div>

				<div class="flex-w flex-c-m m-tb-10">
    <!-- Filter button -->
    <div class="flex-c-m stext-106 cl6 size-104 bor4 pointer hov-btn3 trans-04 m-r-8 m-tb-4 js-show-filter">
        <i class="icon-filter cl2 m-r-6 fs-15 trans-04 zmdi zmdi-filter-list"></i>
        <i class="icon-close-filter cl2 m-r-6 fs-15 trans-04 zmdi zmdi-close dis-none"></i>
        Filter
    </div>

    <!-- Search button (toggle) -->
    
  

<!-- Hidden search form -->
<form method="GET"   class="dis-none js-search-form flex-c-m m-tb-10">
    <input type="text" name="query" placeholder="Search products..." class="stext-106 bor4 size-105 p-l-10 p-r-10" required>
    <button type="submit" class="flex-c-m stext-106 cl6 size-105 bor4 pointer hov-btn3 trans-04 m-l-4">Go</button>
</form>
 
</div>
<script>
    const searchToggle = document.querySelector('.js-show-search');
    const searchForm = document.querySelector('.js-search-form');
    const searchOpenIcon = searchToggle.querySelector('.zmdi-search');
    const searchCloseIcon = searchToggle.querySelector('.zmdi-close');

    searchToggle.addEventListener('click', () => {
        searchForm.classList.toggle('dis-none');
        searchOpenIcon.classList.toggle('dis-none');
        searchCloseIcon.classList.toggle('dis-none');
    });
</script>


				<!-- Search product -->
				 <!--<div class="dis-none panel-search w-full p-t-10 p-b-15">
					<div class="bor8 dis-flex p-l-15">
						<button class="size-113 flex-c-m fs-16 cl2 hov-cl1 trans-04">
							<i class="zmdi zmdi-search"></i>
						</button>

						<input class="mtext-107 cl2 size-114 plh2 p-r-15" type="text" name="search-product" placeholder="Search">
					</div>	
				</div> -->

				<!-- Filter -->
				<div class="dis-none panel-filter w-full p-t-10">
					<div class="wrap-filter flex-w bg6 w-full p-lr-40 p-t-27 p-lr-15-sm">
						<div class="filter-col1 p-r-15 p-b-27">
    <div class="mtext-102 cl2 p-b-15">
        Sort By
    </div>

    <ul>
        <li class="p-b-6">
            <a href="?id=<?= $category_id ?>&sort=default" class="filter-link stext-106 trans-04 <?= ($_GET['sort'] ?? 'default') === 'default' ? 'filter-link-active' : '' ?>">
                Default
            </a>
        </li>

        <li class="p-b-6">
            <a href="?id=<?= $category_id ?>&sort=name_asc" class="filter-link stext-106 trans-04 <?= ($_GET['sort'] ?? '') === 'name_asc' ? 'filter-link-active' : '' ?>">
                by name (A-Z)
            </a>
        </li>

        <li class="p-b-6">
            <a href="?id=<?= $category_id ?>&sort=name_desc" class="filter-link stext-106 trans-04 <?= ($_GET['sort'] ?? '') === 'name_desc' ? 'filter-link-active' : '' ?>">
                by name (Z-A)
            </a>
        </li>

        <li class="p-b-6">
            <a href="?id=<?= $category_id ?>&sort=price_asc" class="filter-link stext-106 trans-04 <?= ($_GET['sort'] ?? '') === 'price_asc' ? 'filter-link-active' : '' ?>">
                Price: Low to High
            </a>
        </li>

        <li class="p-b-6">
            <a href="?id=<?= $category_id ?>&sort=price_desc" class="filter-link stext-106 trans-04 <?= ($_GET['sort'] ?? '') === 'price_desc' ? 'filter-link-active' : '' ?>">
                Price: High to Low
            </a>
        </li>
    </ul>
</div>

								<div class="filter-col2 p-r-15 p-b-27">
    <div class="mtext-102 cl2 p-b-15">
        Price
    </div>

    <ul>
        <li class="p-b-6">
            <a href="?<?= http_build_query(array_filter(['id' => $category_id ?: null, 'sort' => $sort, 'price' => 'all'])) ?>" 
               class="filter-link stext-106 trans-04 <?= ($price_range === 'all') ? 'filter-link-active' : '' ?>">
                All
            </a>
        </li>

        <li class="p-b-6">
            <a href="?<?= http_build_query(array_filter(['id' => $category_id ?: null, 'sort' => $sort, 'price' => '0-50'])) ?>" 
               class="filter-link stext-106 trans-04 <?= ($price_range === '0-50') ? 'filter-link-active' : '' ?>">
                $0.00 - $50.00
            </a>
        </li>

        <li class="p-b-6">
            <a href="?<?= http_build_query(array_filter(['id' => $category_id ?: null, 'sort' => $sort, 'price' => '50-100'])) ?>" 
               class="filter-link stext-106 trans-04 <?= ($price_range === '50-100') ? 'filter-link-active' : '' ?>">
                $50.00 - $100.00
            </a>
        </li>

        <li class="p-b-6">
            <a href="?<?= http_build_query(array_filter(['id' => $category_id ?: null, 'sort' => $sort, 'price' => '100-150'])) ?>" 
               class="filter-link stext-106 trans-04 <?= ($price_range === '100-150') ? 'filter-link-active' : '' ?>">
                $100.00 - $150.00
            </a>
        </li>

        <li class="p-b-6">
            <a href="?<?= http_build_query(array_filter(['id' => $category_id ?: null, 'sort' => $sort, 'price' => '150-200'])) ?>" 
               class="filter-link stext-106 trans-04 <?= ($price_range === '150-200') ? 'filter-link-active' : '' ?>">
                $150.00 - $200.00
            </a>
        </li>

        <li class="p-b-6">
            <a href="?<?= http_build_query(array_filter(['id' => $category_id ?: null, 'sort' => $sort, 'price' => '200+'])) ?>" 
               class="filter-link stext-106 trans-04 <?= ($price_range === '200+') ? 'filter-link-active' : '' ?>">
                $200.00+
            </a>
        </li>
    </ul>
</div>


						<div class="filter-col3 p-r-15 p-b-27">
							<div class="mtext-102 cl2 p-b-15">
								Color
							</div>

							<ul>
								<li class="p-b-6">
									<span class="fs-15 lh-12 m-r-6" style="color: #222;">
										<i class="zmdi zmdi-circle"></i>
									</span>

									<a href="#" class="filter-link stext-106 trans-04">
										Black
									</a>
								</li>

								<li class="p-b-6">
									<span class="fs-15 lh-12 m-r-6" style="color: #4272d7;">
										<i class="zmdi zmdi-circle"></i>
									</span>

									<a href="#" class="filter-link stext-106 trans-04 filter-link-active">
										Blue
									</a>
								</li>

								<li class="p-b-6">
									<span class="fs-15 lh-12 m-r-6" style="color: #b3b3b3;">
										<i class="zmdi zmdi-circle"></i>
									</span>

									<a href="#" class="filter-link stext-106 trans-04">
										Grey
									</a>
								</li>

								<li class="p-b-6">
									<span class="fs-15 lh-12 m-r-6" style="color: #00ad5f;">
										<i class="zmdi zmdi-circle"></i>
									</span>

									<a href="#" class="filter-link stext-106 trans-04">
										Green
									</a>
								</li>

								<li class="p-b-6">
									<span class="fs-15 lh-12 m-r-6" style="color: #fa4251;">
										<i class="zmdi zmdi-circle"></i>
									</span>

									<a href="#" class="filter-link stext-106 trans-04">
										Red
									</a>
								</li>

								<li class="p-b-6">
									<span class="fs-15 lh-12 m-r-6" style="color: #aaa;">
										<i class="zmdi zmdi-circle-o"></i>
									</span>

									<a href="#" class="filter-link stext-106 trans-04">
										White
									</a>
								</li>
							</ul>
						</div>

						<div class="filter-col4 p-b-27">
							<div class="mtext-102 cl2 p-b-15">
								Tags
							</div>

							<div class="flex-w p-t-4 m-r--5">
								<a href="#" class="flex-c-m stext-107 cl6 size-301 bor7 p-lr-15 hov-tag1 trans-04 m-r-5 m-b-5">
									Fashion
								</a>

								<a href="#" class="flex-c-m stext-107 cl6 size-301 bor7 p-lr-15 hov-tag1 trans-04 m-r-5 m-b-5">
									Lifestyle
								</a>

								<a href="#" class="flex-c-m stext-107 cl6 size-301 bor7 p-lr-15 hov-tag1 trans-04 m-r-5 m-b-5">
									Denim
								</a>

								<a href="#" class="flex-c-m stext-107 cl6 size-301 bor7 p-lr-15 hov-tag1 trans-04 m-r-5 m-b-5">
									Streetstyle
								</a>

								<a href="#" class="flex-c-m stext-107 cl6 size-301 bor7 p-lr-15 hov-tag1 trans-04 m-r-5 m-b-5">
									Crafts
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="row isotope-grid">
    <?php foreach ($products as $prod): ?>
        <div class="col-sm-6 col-md-4 col-lg-3 p-b-35 isotope-item">
            <!-- Block2 -->
            <div class="block2">
                <div class="block2-pic hov-img0">
                    <img src="images/banner-01.jpg" alt="IMG-PRODUCT">

                    <a href="?variantid=<?php echo $prod['product_id']; ?>" class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04 js-show-modal1">
                        Quick View
                    </a>
                </div>

                <div class="block2-txt flex-w flex-t p-t-14">
                    <div class="block2-txt-child1 flex-col-l">
                        <a href="product-detail.php?id=<?php echo $prod['product_id']; ?>" class="stext-104 cl4 hov-cl1 trans-04 js-name-b2 p-b-6">
                            <?php echo $prod['product_name']; ?>
                        </a>

                        <span class="stext-105 cl3">
                            $<?php echo $prod['product_price']; ?>
                        </span>
                    </div>

                    <div class="block2-txt-child2 flex-r p-t-3">
                        <a href="#" class="btn-addwish-b2 dis-block pos-relative js-addwish-b2">
                            <img class="icon-heart1 dis-block trans-04" src="images/icons/icon-heart-01.png" alt="ICON">
                            <img class="icon-heart2 dis-block trans-04 ab-t-l" src="images/icons/icon-heart-02.png" alt="ICON">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>


			<!-- Load more -->
			<div class="flex-c-m flex-w w-full p-t-45">
				<a href="#" class="flex-c-m stext-101 cl5 size-103 bg2 bor1 hov-btn1 p-lr-15 trans-04">
					Load More
				</a>
			</div>
		</div>
	</section>
		

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
Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved |Made with <i class="fa fa-heart-o" aria-hidden="true"></i> by <a href="https://colorlib.com" target="_blank">Colorlib</a> &amp; distributed by <a href="https://themewagon.com" target="_blank">ThemeWagon</a>
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

	<!-- Modal1 -->
	<div class="wrap-modal1 js-modal1 p-t-60 p-b-20">
		<div class="overlay-modal1 js-hide-modal1"></div>

		<div class="container">
			<div class="bg0 p-t-60 p-b-30 p-lr-15-lg how-pos3-parent">
				<button class="how-pos3 hov3 trans-04 js-hide-modal1">
					<img src="images/icons/icon-close.png" alt="CLOSE">
				</button>

				<div class="row">
					<div class="col-md-6 col-lg-7 p-b-30">
						<div class="p-l-25 p-r-30 p-lr-0-lg">
							<div class="wrap-slick3 flex-sb flex-w">
								<div class="wrap-slick3-dots"></div>
								<div class="wrap-slick3-arrows flex-sb-m flex-w"></div>

								<div class="slick3 gallery-lb">
									<div class="item-slick3" data-thumb="images/product-detail-01.jpg">
										<div class="wrap-pic-w pos-relative">
											<img src="images/product-detail-01.jpg" alt="IMG-PRODUCT">

											<a class="flex-c-m size-108 how-pos1 bor0 fs-16 cl10 bg0 hov-btn3 trans-04" href="images/product-detail-01.jpg">
												<i class="fa fa-expand"></i>
											</a>
										</div>
									</div>

									<div class="item-slick3" data-thumb="images/product-detail-02.jpg">
										<div class="wrap-pic-w pos-relative">
											<img src="images/product-detail-02.jpg" alt="IMG-PRODUCT">

											<a class="flex-c-m size-108 how-pos1 bor0 fs-16 cl10 bg0 hov-btn3 trans-04" href="images/product-detail-02.jpg">
												<i class="fa fa-expand"></i>
											</a>
										</div>
									</div>

									<div class="item-slick3" data-thumb="images/product-detail-03.jpg">
										<div class="wrap-pic-w pos-relative">
											<img src="images/product-detail-03.jpg" alt="IMG-PRODUCT">

											<a class="flex-c-m size-108 how-pos1 bor0 fs-16 cl10 bg0 hov-btn3 trans-04" href="images/product-detail-03.jpg">
												<i class="fa fa-expand"></i>
											</a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					
					<div class="col-md-6 col-lg-5 p-b-30">
						<?php 
						
						
						?>
						<div class="p-r-50 p-t-5 p-lr-0-lg">
							<h4 class="mtext-105 cl2 js-name-detail p-b-14">
								<?php echo htmlspecialchars($variant['product_name']); ?>
							</h4>

							<span class="mtext-106 cl2">
								$<?php echo htmlspecialchars($variant['product_price']); ?>
							</span>

							<p class="stext-102 cl3 p-t-23">
								<?php echo htmlspecialchars($variant['product_description']); ?>
							</p>
							
							<!--  -->
							<div class="p-t-33">
								<div class="flex-w flex-r-m p-b-10">
									<div class="size-203 flex-c-m respon6">
										Size
									</div>

									<div class="size-204 respon6-next">
										<div class="rs1-select2 bor8 bg0">
											<select class="js-select2" name="size">
												<?php foreach ($sizes as $size): ?>
													<option><?php echo htmlspecialchars($size); ?></option>
												<?php endforeach; ?>
											</select>
											<div class="dropDownSelect2"></div>
										</div>
									</div>
								</div>

								<div class="flex-w flex-r-m p-b-10">
									<div class="size-203 flex-c-m respon6">
										Color
									</div>

									<div class="size-204 respon6-next">
										<div class="rs1-select2 bor8 bg0">
											<select class="js-select2" name="color">
												<option>Choose an option</option>
												<?php foreach ($colors as $color): ?>
													<option><?php echo htmlspecialchars($color); ?></option>
												<?php endforeach; ?>
											</select>
											<div class="dropDownSelect2"></div>
										</div>
									</div>
								</div>

								<div class="flex-w flex-r-m p-b-10">
									<div class="size-204 flex-w flex-m respon6-next">
										<div class="wrap-num-product flex-w m-r-20 m-tb-10">
											<div class="btn-num-product-down cl8 hov-btn3 trans-04 flex-c-m">
												<i class="fs-16 zmdi zmdi-minus"></i>
											</div>

											<input class="mtext-104 cl3 txt-center num-product" type="number" name="num-product" value="1">

											<div class="btn-num-product-up cl8 hov-btn3 trans-04 flex-c-m">
												<i class="fs-16 zmdi zmdi-plus"></i>
											</div>
										</div>

										<button class="flex-c-m stext-101 cl0 size-101 bg1 bor1 hov-btn1 p-lr-15 trans-04 js-addcart-detail">
											Add to cart
										</button>
									</div>
								</div>	
							</div>

							<!--  -->
							<div class="flex-w flex-m p-l-100 p-t-40 respon7">
								<div class="flex-m bor9 p-r-10 m-r-11">
									<a href="#" class="fs-14 cl3 hov-cl1 trans-04 lh-10 p-lr-5 p-tb-2 js-addwish-detail tooltip100" data-tooltip="Add to Wishlist">
										<i class="zmdi zmdi-favorite"></i>
									</a>
								</div>

								<a href="#" class="fs-14 cl3 hov-cl1 trans-04 lh-10 p-lr-5 p-tb-2 m-r-8 tooltip100" data-tooltip="Facebook">
									<i class="fa fa-facebook"></i>
								</a>

								<a href="#" class="fs-14 cl3 hov-cl1 trans-04 lh-10 p-lr-5 p-tb-2 m-r-8 tooltip100" data-tooltip="Twitter">
									<i class="fa fa-twitter"></i>
								</a>

								<a href="#" class="fs-14 cl3 hov-cl1 trans-04 lh-10 p-lr-5 p-tb-2 m-r-8 tooltip100" data-tooltip="Google Plus">
									<i class="fa fa-google-plus"></i>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
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
	<script src="vendor/daterangepicker/moment.min.js"></script>
	<script src="vendor/daterangepicker/daterangepicker.js"></script>
<!--===============================================================================================-->
	<script src="vendor/slick/slick.min.js"></script>
	<script src="js/slick-custom.js"></script>
<!--===============================================================================================-->
	<script src="vendor/parallax100/parallax100.js"></script>
	<script>
        $('.parallax100').parallax100();
	</script>
<!--===============================================================================================-->
	<script src="vendor/MagnificPopup/jquery.magnific-popup.min.js"></script>
	<script>
		$('.gallery-lb').each(function() { // the containers for all your galleries
			$(this).magnificPopup({
		        delegate: 'a', // the selector for gallery item
		        type: 'image',
		        gallery: {
		        	enabled:true
		        },
		        mainClass: 'mfp-fade'
		    });
		});
	</script>
<!--===============================================================================================-->
	<script src="vendor/isotope/isotope.pkgd.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/sweetalert/sweetalert.min.js"></script>
	<script>
		$('.js-addwish-b2, .js-addwish-detail').on('click', function(e){
			e.preventDefault();
		});

		$('.js-addwish-b2').each(function(){
			var nameProduct = $(this).parent().parent().find('.js-name-b2').html();
			$(this).on('click', function(){
				swal(nameProduct, "is added to wishlist !", "success");

				$(this).addClass('js-addedwish-b2');
				$(this).off('click');
			});
		});

		$('.js-addwish-detail').each(function(){
			var nameProduct = $(this).parent().parent().parent().find('.js-name-detail').html();

			$(this).on('click', function(){
				swal(nameProduct, "is added to wishlist !", "success");

				$(this).addClass('js-addedwish-detail');
				$(this).off('click');
			});
		});

		/*---------------------------------------------*/

		$('.js-addcart-detail').each(function(){
			var nameProduct = $(this).parent().parent().parent().parent().find('.js-name-detail').html();
			$(this).on('click', function(){
				swal(nameProduct, "is added to cart !", "success");
			});
		});
	
	</script>
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

</body>
</html>