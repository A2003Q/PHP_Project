<?php  

require_once '../SQL/Database.php';
session_start();
$conn = Database::getInstance()->getConnection();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_details_ids = $_POST['order_details_id'];
    $ratings = $_POST['rating'];
    $feedbacks = $_POST['feedback'];

$stmt= $conn->prepare("SELECT p.product_id
FROM order_details od
JOIN product_variant v ON od.variant_id = v.variant_id
JOIN products p ON v.product_id = p.product_id
WHERE od.order_details_id = ?");

foreach ($order_details_ids as $index => $id) {
    


$stmt->bind_param("i", $id);
        $stmt->execute();
$result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $product_id = $row['product_id'];
            $rating = $ratings[$index];
            $feedback = $feedbacks[$index];

    }
     $insertStmt = $conn->prepare("
                INSERT INTO feedback (product_id, feedback_rating, feedback_comment,user_id)
                VALUES (?, ?, ?,?)
            ");
            $insertStmt->bind_param("iisi", $product_id, $rating, $feedback, $_SESSION['user_id']);
            $insertStmt->execute();
            $insertStmt->close();

   
}

 header("Location: index.php");
    exit;


}


?>