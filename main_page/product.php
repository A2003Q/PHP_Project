<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once '../SQL/Database.php';

$conn = Database::getInstance()->getConnection();

?>
<?php


$category_id = (int)($_GET['id'] ?? 0);
$sort = $_GET['sort'] ?? 'default';
$price_range = $_GET['price'] ?? 'all';
$search = $_GET['query'] ?? '';


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
	<?php include 'header-main.php'; ?>

	<!-- Cart -->
	

	
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

                    <a href="product-details.php?variantid=<?php echo $prod['product_id']; ?>" class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04 ">
                        Quick View
                    </a>
                </div>

                <div class="block2-txt flex-w flex-t p-t-14">
                    <div class="block2-txt-child1 flex-col-l">
                        <a href="product-details.php?variantid=<?php echo $prod['product_id']; ?>" class="stext-104 cl4 hov-cl1 trans-04 js-name-b2 p-b-6">
                            <?php echo $prod['product_name']; ?>
                        </a>

                        <span class="stext-105 cl3">
                                       <?php
                if ($prod['product_discount'] > 0) {

                    $newPrice = $prod['product_price'] - 
                                ($prod['product_price'] * $prod['product_discount'] / 100);

                    echo "
                    <div class='d-flex align-items-center mb-4'>
                        <h3 class='text-dark fw-bold mb-0'>$ " . htmlspecialchars(number_format($newPrice, 2)) . "</h3>
                        <span class='ms-3 text-muted text-decoration-line-through small'><del  '>
                            $ " . htmlspecialchars(number_format($prod['product_price'], 2)) . "
                        </del></span>
                    </div>";
                    
                } else {

                    echo "
                    <div class='d-flex align-items-center mb-4'>
                        <h3 class='text-dark fw-bold mb-0'>
                            $ " . htmlspecialchars(number_format($prod['product_price'], 2)) . "
                        </h3>
                    </div>";
                }
                ?>
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