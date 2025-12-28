<?php  

require_once '../SQL/Database.php';
session_start();
$conn = Database::getInstance()->getConnection();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   $input = json_decode(file_get_contents("php://input"), true);

if (!$input) {
    http_response_code(400);
    exit("No input received");
}
$stmt = $conn->prepare("
    SELECT p.product_id
    FROM order_details od
    JOIN product_variant v ON od.variant_id = v.variant_id
    JOIN products p ON v.product_id = p.product_id
    WHERE od.order_details_id = ?
");

$insertStmt = $conn->prepare("
    INSERT INTO feedback (product_id, feedback_rating, feedback_comment, user_id)
    VALUES (?, ?, ?, ?)
");

foreach ($input as $item) {
    $order_details_id = (int)$item['id'];
    $rating = (int)$item['rating'];
    $feedback = trim($item['feedback']);

    $stmt->bind_param("i", $order_details_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $product_id = $row['product_id'];

        $insertStmt->bind_param(
            "iisi",
            $product_id,
            $rating,
            $feedback,
            $_SESSION['user_id']
        );
        $insertStmt->execute();
    }
}

echo json_encode(['status' => 'success']);
exit;
}?>