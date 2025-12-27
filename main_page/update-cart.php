<?php 
require_once '../SQL/database.php';
session_start();

$conn = Database::getInstance()->getConnection();
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['cart_items_id'] as $index => $id) {
    $quantity = (int)$_POST['quantity'][$index];
    if ($quantity <= 0) {
        $stmt = $conn->prepare("DELETE FROM cart_items WHERE cart_items_id = ?");
        $stmt->bind_param("i", $id);
    } else {
        $stmt = $conn->prepare("UPDATE cart_items SET cart_items_quantity = ? WHERE cart_items_id = ?");
        $stmt->bind_param("ii", $quantity, $id);
    }
    $stmt->execute();
   
}
    
}?>
   <!DOCTYPE html>
    <html>
    <head>
        <title>cart updated</title>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Cart Updated!',
            text: 'Your cart has been successfully updated.',
            confirmButtonText: 'OK'
        }).then(() => {
            window.location.href = 'shoping-cart.php';
        });
    </script>
    </body>
    </html>












?>