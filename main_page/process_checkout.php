<?php  
 session_start();
    require_once '../SQL/database.php';
 $conn = Database::getInstance()->getConnection();


if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(!isset($_SESSION['user_id'])|| !$_SESSION['logged_in']) {
        echo "  <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
            Swal.fire({
                icon: 'warning',
                title: 'Login Required',
                text: 'You must be logged in to checkout.',
                confirmButtonText: 'Login'
            }).then(() => {
                window.location.href = 'auth/login.php';
            });
        </script>";
     
    }
 if(isset($_POST['checkout'])) {
    $user_id = $_SESSION['user_id'];
    $street = htmlspecialchars($_POST['address_street'], ENT_QUOTES, 'UTF-8');
    $city = htmlspecialchars($_POST['address_city'], ENT_QUOTES, 'UTF-8');
    $zipcode = htmlspecialchars($_POST['address_zipcode'], ENT_QUOTES, 'UTF-8');
    $country = htmlspecialchars($_POST['address_country'], ENT_QUOTES, 'UTF-8');
    $payment_method = htmlspecialchars($_POST['payment_method'], ENT_QUOTES, 'UTF-8');
    if($payment_method=='visa')
    {

$payment_status='paid';
$card_name=htmlspecialchars($_POST['card_name'], ENT_QUOTES, 'UTF-8');
$card_number=htmlspecialchars($_POST['card_number'], ENT_QUOTES, 'UTF-8');
$card_expiry=htmlspecialchars($_POST['card_expiry'], ENT_QUOTES, 'UTF-8');
$cvv=htmlspecialchars($_POST['card_cvv'], ENT_QUOTES, 'UTF-8');
 $cardNumberPattern = '/^\d{13,19}$/';
    $expiryPattern = '/^(0[1-9]|1[0-2])\/\d{2}$/';
    $cvvPattern = '/^\d{3,4}$/';

    if (!$card_name || !preg_match($cardNumberPattern, $card_number) || 
        !preg_match($expiryPattern, $card_expiry) || !preg_match($cvvPattern, $cvv)) {
        echo "<!DOCTYPE html><html><head><script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script></head><body>";
        echo "<script>
            Swal.fire({
                icon: 'warning',
                title: 'invalid card details',
                text: 'please enter valid card details.',
                confirmButtonText: 'ok'
            }).then(() => {
                window.location.href = 'shoping-cart.php';
            });
           
        </script></body></html>";
        exit;
    }}


$zibcodePattern = '/^\d{4,10}$/';
    if (!preg_match($zibcodePattern, $zipcode)) {
        echo "<!DOCTYPE html><html><head><script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script></head><body>";
        echo "<script>
            Swal.fire({
                icon: 'warning',
                title: 'invalid zipcode',
                text: 'please enter valid zipcode.',
                confirmButtonText: 'ok'
            }).then(() => {
                window.location.href = 'shoping-cart.php';
            });
           
        </script></body></html>";
        exit;
    }

$stmt = $conn->prepare("INSERT INTO payment ( cardholder_name, card_number, CVV,expiry_date,  payment_method, user_id) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("siissi",  $card_name, $card_number, $cvv, $card_expiry, $payment_method, $user_id);
$stmt->execute();
$payment_id = $conn->insert_id;
    }
    else{
        $payment_status='pending';

    }
    $stmt = $conn->prepare("
    INSERT INTO address (user_id, address_city, address_street, address_zipcode)
    VALUES (?, ?, ?, ?)
");
$stmt->bind_param(
    "isss",
    $user_id,
    $city,
    $street,
    $zipcode
);
$stmt->execute();


$address_id = $conn->insert_id;

$stmt = $conn->prepare("SELECT cart_id FROM cart WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $cart_id = $row['cart_id'];
} else {
    // Handle error: no cart found
    die("No cart found");
}
$stmt2 = $conn->prepare("SELECT 
    SUM(ci.cart_items_quantity * p.product_price) AS total
FROM cart_items ci
JOIN product_variant v ON ci.variant_id = v.variant_id
JOIN products p ON v.product_id = p.product_id
WHERE ci.cart_id = ?");

$stmt2->bind_param("i", $cart_id);
$stmt2->execute();
$total=$stmt2->get_result()->fetch_assoc()['total'];
$newdate = date("Y-m-d H:i:s");
    $stmt = $conn->prepare("INSERT INTO orders (address_id, cart_id, order_date, order_status, order_totalprice, payment_id,user_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iissdii", $address_id, $cart_id, $newdate, $payment_status, $total, $payment_id, $user_id);
$stmt->execute();
$order_id = $conn->insert_id;



    $stmt2 = $conn->prepare("
    SELECT ci.variant_id, ci.cart_items_quantity, p.product_price
    FROM cart_items ci
    JOIN product_variant v ON ci.variant_id = v.variant_id
    JOIN products p ON v.product_id = p.product_id
    WHERE ci.cart_id = ?
");
$stmt2->bind_param("i", $cart_id);
$stmt2->execute();
$cartItems = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
$success = true;

foreach ($cartItems as $item) {
    $variant_id = $item['variant_id'];
    $price_atpurchase = $item['product_price'];

    $stmt_detail = $conn->prepare("
        INSERT INTO order_details (order_id, variant_id, price_atpurchase)
        VALUES (?, ?, ?)
    ");
    $stmt_detail->bind_param("iid", $order_id, $variant_id, $price_atpurchase);
    
    if (!$stmt_detail->execute()) {
        $success = false; // stop if any insert fails
        break;
    }
}if ($success) {
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    if (!$stmt) { die("Delete cart prepare failed: " . $conn->error); }
    $stmt->bind_param("i", $user_id);
    if (!$stmt->execute()) { die("Delete cart execute failed: " . $stmt->error); }
    $stmt->close();

    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Order Success</title>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Order Placed!',
            text: 'Your order has been successfully placed.',
            confirmButtonText: 'OK'
        }).then(() => {
            window.location.href = 'index.php';
        });
    </script>
    </body>
    </html>
    <?php
    exit;
}
else {
    echo "Error processing order details.";
    exit;
}
 }?>