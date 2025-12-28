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
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Error</title>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
        <script>
            Swal.fire({
                icon: "error",
                title: "Cart Error",
                text: "No cart found",
                showConfirmButton: true,
                confirmButtonText: "OK"
            }).then(() => {
                window.history.back();
            });
        </script>
    </body>
    </html>';
    exit;
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
    // Delete the cart after successful order
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Order Success</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .review-card {
            background: #f9f9f9;
            border: 1px solid #eee;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 15px;
            text-align: left;
        }
        .review-title { font-weight: 600; color: #333; display: block; font-size: 1rem; }
        .review-sub { font-size: 0.85rem; color: #888; margin-bottom: 8px; display: block; }
        .review-select { height: 40px !important; margin: 5px 0 !important; border-radius: 8px !important; font-size: 0.9rem !important; width: 100% !important; }
        .review-text { height: 70px !important; margin: 5px 0 !important; border-radius: 8px !important; font-size: 0.85rem !important; width: 100% !important; padding: 10px !important; }
    </style>
</head>
<body>

<script>
    // 1. Success Message
    Swal.fire({
        icon: 'success',
        title: 'Order Placed!',
        text: 'Your order #<?= $order_id ?> has been successfully placed.',
        showCancelButton: true,
        confirmButtonText: 'Rate Items',
        cancelButtonText: 'Skip',
        confirmButtonColor: '#333'
    }).then((result) => {
        if (result.isConfirmed) {
            // 2. Rating Modal (Only if they clicked "Rate Items")
            Swal.fire({
                title: 'Rate Your Items',
                width: '600px',
                html: `
                    <div style="max-height: 450px; overflow-y: auto; padding-right: 10px;">
                        <?php 
                        // Re-fetch order details for the loop
                        $stmt = $conn->prepare("SELECT od.order_details_id, p.product_name, v.size, v.color 
                                              FROM order_details od 
                                              JOIN product_variant v ON od.variant_id = v.variant_id 
                                              JOIN products p ON v.product_id = p.product_id 
                                              WHERE od.order_id = ?");
                        $stmt->bind_param("i", $order_id);
                        $stmt->execute();
                        $order_details = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

                        foreach ($order_details as $detail): ?>
                            <div class="review-card" data-id="<?= $detail['order_details_id'] ?>">
                                <span class="review-title"><?= htmlspecialchars($detail['product_name']) ?></span>
                                <span class="review-sub">Size: <?= $detail['size'] ?> | Color: <?= $detail['color'] ?></span>
                                
                                <select class="swal2-select review-select rating-input">
                                    <option value="">Select Rating</option>
                                    <option value="5">★★★★★ Excellent</option>
                                    <option value="4">★★★★ Good</option>
                                    <option value="3">★★★ Okay</option>
                                    <option value="2">★★ Poor</option>
                                    <option value="1">★ Bad</option>
                                </select>

                                <textarea class="swal2-textarea review-text feedback-input" placeholder="Feedback (Optional)"></textarea>
                            </div>
                        <?php endforeach; ?>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Submit Feedback',
                preConfirm: () => {
                    const results = [];
                    const cards = document.querySelectorAll('.review-card');
                    let allRated = true;

                    cards.forEach(card => {
                        const rating = card.querySelector('.rating-input').value;
                        const feedback = card.querySelector('.feedback-input').value;
                        const id = card.getAttribute('data-id');

                        if (!rating) {
                            allRated = false;
                        }
                        results.push({ id: id, rating: rating, feedback: feedback });
                    });

                    if (!allRated) {
                        Swal.showValidationMessage('Please rate all items');
                        return false;
                    }
                    return results;
                }
            }).then((reviewResult) => {
                if (reviewResult.isConfirmed && reviewResult.value) {
                    // 3. Send to Database via Fetch
                    fetch('save_feedback.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(reviewResult.value)
                    }).then(() => {
                        Swal.fire('Thank You!', 'Your feedback was saved.', 'success')
                            .then(() => window.location.href = 'index.php');
                    });
                } else {
                    window.location.href = 'index.php';
                }
            });
        } else {
            // If they click "Skip"
            window.location.href = 'index.php';
        }
    });
</script>
</body>
</html>
<?php
    exit;
} else {
    echo "Error processing order details.";
    exit;
}
} ?>