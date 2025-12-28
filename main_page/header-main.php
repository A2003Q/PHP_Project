
<?php 
if(session_status() == PHP_SESSION_NONE){
    session_start();
}
$cart_stmt = $conn->prepare("SELECT cart_id FROM cart WHERE user_id = ?");
$cart_stmt->bind_param("i", $_SESSION['user_id']);
$cart_stmt->execute();
$cart_result = $cart_stmt->get_result();

if ($cart_result->num_rows > 0) {
  
    $cart_row = $cart_result->fetch_assoc();
    $cart_id = $cart_row['cart_id'];
    
    $items_stmt = $conn->prepare("SELECT COUNT(*) as count FROM cart_items WHERE cart_id = ?");
    $items_stmt->bind_param("i", $cart_id);
    $items_stmt->execute();
    $items_result = $items_stmt->get_result();
    $items_row = $items_result->fetch_assoc();
    $cart_count = $items_row['count'];
} else {

    $cart_count = 0;
}
?>






<header class="header-v4" >
		
	<!-- Header desktop -->
<div class="container-menu-desktop">
    <!-- Topbar -->
    <div class="wrap-menu-desktop">
        <nav class="limiter-menu-desktop container">
            
            <!-- Logo desktop -->		
            <a href="index.php" class="logo">
                <img src="images/icons/logo-01.png" alt="IMG-LOGO">
            </a>

            <!-- Menu desktop -->
            <div class="menu-desktop">
                <ul class="main-menu">
                    <li class="">
                        <a href="index.php">Home</a>
                    </li>

                    <li>
                        <a href="product.php">Shop</a>
                    </li>

                    

                    <li>
                        <a href="my_orders.php">my orders</a>
                    </li>

                    <li>
                        <a href="about.php">About</a>
                    </li>

                    <li>
                        <a href="contact.php">Contact</a>
                    </li>
				  <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>	

        <li class="menu-item">
  <a href="#">My Account</a>

  <ul class="sub-menu">
    <li><a href="myinfo.php">My info</a></li>
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

                    <div class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 icon-header-noti js-show-cart" data-notify="<?= $cart_count ?? 0?>" >
                       <a href="shoping-cart.php" > <i class="zmdi zmdi-shopping-cart" style="color: black;"></i></a>
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