<?php
session_start();
require_once "../php/orders.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$order = new Orders();
$orders = $order->getAllOrders();
//$orderbyid = $ordersModel->getOrderById($orderbyid);

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Products</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<style>
.page-header { background-color: #4f3131; color: white; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; }
.table thead { background-color: #4f3131; color: white; }
.btn-primary { background-color: #4f3131; border: none; }
.btn-primary:hover { background-color: #3e2626; }
.card { border-radius: 12px; box-shadow: 0 0 15px rgba(0,0,0,0.08); }
</style>
</head>
<body>
<div id="wrapper">
    <?php include "../php/sidebar.php"; ?>
</div>

<div id="content">
<div class="page-header">
    <h3>Orders Management</h3>
</div>
<table class="table table-bordered">
<thead class="table-dark">
<tr>
    <th>order_id</th><th>user_id</th><th>order_status</th><th>order_totalprice</th><th>address_id</th><th>order_date</th><th>cart_id</th><th>payment_id</th><th>Actions</th>
</tr>
</thead>
<tbody>
<?php while ($order = $orders->fetch_assoc()): ?>
<tr>
<td><?= $order['order_id'] ?></td>
<td><?= htmlspecialchars($order['user_id']) ?></td>
<td><?= htmlspecialchars($order['order_status']) ?></td>
<td><?= htmlspecialchars($order['order_totalprice']) ?></td>
<td><?= htmlspecialchars($order['address_id']) ?></td>
<td><?= htmlspecialchars($order['order_date']) ?></td>
<td><?= htmlspecialchars($order['cart_id']) ?></td>
<td><?= htmlspecialchars($order['payment_id']) ?></td>
<td>
    <button class="btn btn-sm btn-primary view-details"
            data-order-id="<?= $order['order_id'] ?>">
        <i class="fa fa-eye"></i> View Details
    </button>
</td>


</tr>
<?php endwhile; ?>
</tbody>
</table>


</div>
<!-- Order Details Modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Order Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <table class="table table-bordered">
         <thead class="table-dark">
<tr>
  <th>order_details_id</th>
  <th>cart_items_id</th>
  <th>product</th>
  <th>size</th>
  <th>color</th>
  <th>quantity</th>
  <th>price_atpurchase</th>
</tr>
</thead>

          <tbody id="orderDetailsBody">
            <tr>
              <td colspan="7" class="text-center">Loading...</td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>
  </div>
</div>
<script>
document.querySelectorAll('.view-details').forEach(btn => {
    btn.addEventListener('click', function () {

        const orderId = this.dataset.orderId;
        const modal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
        modal.show();

      fetch("../php/order-fetch.php?order_id=" + orderId)
   .then(res => res.json())
   .then(data => {
       const body = document.getElementById("orderDetailsBody");
       body.innerHTML = "";

       if (!Array.isArray(data) || data.length === 0) {
           body.innerHTML = `<tr><td colspan="7" class="text-center">No details found</td></tr>`;
           return;
       }

       data.forEach(row => {
           body.innerHTML += `
           <tr>
               <td>${row.order_details_id}</td>
               
               <td>${row.cart_items_id}</td>
               <td>${row.product_id} - ${row.product_name}</td>
               <td>${row.size}</td>
               <td>${row.color}</td>
               <td>${row.cart_items_quantity}</td>
               <td>${row.price_atpurchase}</td>
           </tr>
           `;
       });
   })
   .catch(err => console.error(err));


    });
});
</script>

</body>
</html>




