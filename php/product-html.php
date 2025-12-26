<?php
session_start();
require_once "../php/Products.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$productsClass = new Products();
$alert = null;

/* ---------- ADD ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    if ($productsClass->addProduct(
        $_POST['name'],
        $_POST['price'],
        $_POST['description'],
        $_POST['quantity'],
        $_POST['discount']
    )) {
        $alert = "added";
    }
}

/* ---------- UPDATE ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'edit') {
    if ($productsClass->updateProduct(
        $_POST['id'],
        $_POST['name'],
        $_POST['price'],
        $_POST['description'],
        $_POST['quantity'],
        $_POST['discount']
    )) {
        $alert = "updated";
    }
}

/* ---------- DELETE ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    if ($productsClass->deleteProduct($_POST['id'])) {
        $alert = "deleted";
    }
}

$products = $productsClass->getAllProducts();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Products</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- ✅ REQUIRED -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body class="p-4">

<h2>Product Management</h2>

<button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addProductModal">
    <i class="fa fa-plus"></i> Add Product
</button>

<table class="table table-bordered">
<thead class="table-dark">
<tr>
<th>ID</th>
<th>Name</th>
<th>Price</th>
<th>Qty</th>
<th>Discount</th>
<th>Actions</th>
</tr>
</thead>
<tbody>

<?php while ($p = $products->fetch_assoc()): ?>
<tr>
<td><?= $p['product_id'] ?></td>
<td><?= htmlspecialchars($p['product_name']) ?></td>
<td>$<?= $p['product_price'] ?></td>
<td><?= $p['product_quantity'] ?></td>
<td><?= $p['product_discount'] ?>%</td>
<td>

<button class="btn btn-primary btn-sm editBtn"
    data-id="<?= $p['product_id'] ?>"
    data-name="<?= htmlspecialchars($p['product_name']) ?>"
    data-price="<?= $p['product_price'] ?>"
    data-desc="<?= htmlspecialchars($p['product_description']) ?>"
    data-qty="<?= $p['product_quantity'] ?>"
    data-discount="<?= $p['product_discount'] ?>">
    <i class="fa fa-edit"></i>
</button>

<form method="POST" class="d-inline deleteForm">
<input type="hidden" name="action" value="delete">
<input type="hidden" name="id" value="<?= $p['product_id'] ?>">
<button type="submit" class="btn btn-danger btn-sm">
    <i class="fa fa-trash"></i>
</button>
</form>

</td>
</tr>
<?php endwhile; ?>

</tbody>
</table>
<div class="modal fade" id="addProductModal">
<div class="modal-dialog">
<form method="POST">
<input type="hidden" name="action" value="add">

<div class="modal-content p-3">
<input name="name" class="form-control mb-2" placeholder="Name" required>
<input name="price" type="number" step="0.01" class="form-control mb-2" placeholder="Price" required>
<textarea name="description" class="form-control mb-2" placeholder="Description"></textarea>
<input name="quantity" type="number" class="form-control mb-2" placeholder="Quantity" required>
<input name="discount" type="number" class="form-control mb-2" value="0">

<button class="btn btn-success">Add</button>
</div>
</form>
</div>
</div>
<div class="modal fade" id="editProductModal">
<div class="modal-dialog">
<form method="POST">
<input type="hidden" name="action" value="edit">
<input type="hidden" name="id" id="editId">

<div class="modal-content p-3">
<input name="name" id="editName" class="form-control mb-2" required>
<input name="price" id="editPrice" class="form-control mb-2" required>
<textarea name="description" id="editDesc" class="form-control mb-2"></textarea>
<input name="quantity" id="editQty" class="form-control mb-2" required>
<input name="discount" id="editDiscount" class="form-control mb-2">

<button class="btn btn-primary">Update</button>
</div>
</form>
</div>
</div>
<script>
document.querySelectorAll('.editBtn').forEach(btn => {
    btn.onclick = () => {
        editId.value = btn.dataset.id;
        editName.value = btn.dataset.name;
        editPrice.value = btn.dataset.price;
        editDesc.value = btn.dataset.desc;
        editQty.value = btn.dataset.qty;
        editDiscount.value = btn.dataset.discount;
        new bootstrap.Modal(editProductModal).show();
    };
});

document.querySelectorAll('.deleteForm').forEach(form => {
    form.onsubmit = e => {
        e.preventDefault();
        Swal.fire({
            title: 'Delete product?',
            icon: 'warning',
            showCancelButton: true
        }).then(res => {
            if (res.isConfirmed) form.submit();
        });
    };
});
</script>

