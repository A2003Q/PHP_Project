<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "../php/Products.php";
require_once "../php/product_variants.php";
require_once "../php/product_images.php";
require_once "../php/categories.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$productsClass = new Products();
$variantClass  = new ProductVariants();
$imageClass    = new ProductImages();


$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    switch ($_POST['action']) {
case 'add':
    $productId = $productsClass->addProduct(
        $_POST['name'],
        $_POST['price'],
        $_POST['description'],
        $_POST['quantity'],
        $_POST['discount']
    );

    if ($productId) {
        $productsClass->addProductCategory($productId, $_POST['categories_id']);
    }
break;


        case 'edit':
            $productsClass->updateProduct(
                $_POST['id'],
                $_POST['name'],
                $_POST['price'],
                $_POST['description'],
                $_POST['quantity'],
                $_POST['discount']
            );
            break;

        case 'delete':
            $productsClass->deleteProduct($_POST['id']);
            break;

        case 'add_variant':
            $variantClass->add(
                $_POST['product_id'],
                $_POST['size'],
                $_POST['color'],
                $_POST['quantity']
            );
            break;

        case 'edit_variant':
            $variantClass->updateVariant(
                $_POST['variant_id'],
                $_POST['size'],
                $_POST['color'],
                $_POST['quantity']
            );
            break;

        case 'delete_variant':
            $variantClass->deleteVariant($_POST['variant_id']);
            break;

        case 'add_image':
            $imageClass->add(
                $_POST['product_id'],
                $_POST['image_url']
            );
            break;

        case 'edit_image':
            $imageClass->updateImage(
                $_POST['product_images_id'],
                $_POST['image_url']
            );
            break;

        case 'delete_image':
            $imageClass->deleteImage($_POST['product_images_id']);
            break;
    }

    // 🔥 ONLY redirect if NOT ajax
    if (!$isAjax) {
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    }

    // Ajax response
    exit;
}



$categoriesClass = new Categories();
$categoriesResult = $categoriesClass->getAllCategories();

$category_id = (int)($_GET['category'] ?? 0);
$sort        = $_GET['sort'] ?? 'default';
$price_range = $_GET['price'] ?? 'all';
$search      = $_GET['query'] ?? '';

$products = $productsClass->getFilteredProducts($category_id, $sort, $price_range, $search);
// Fetch variants dynamically
if (isset($_GET['fetch_variants'])) {
    $variants = $variantClass->getByProduct($_GET['fetch_variants']);
    while ($v = $variants->fetch_assoc()) {
        echo '<tr>
        <td>'.htmlspecialchars($v['size']).'</td>
        <td>'.htmlspecialchars($v['color']).'</td>
        <td>'.$v['quantity'].'</td>
        <td>
        <form method="POST" class="d-inline">
        <input type="hidden" name="product_id" value="'.$v['product_id'].'">
            <input type="hidden" name="variant_id" value="'.$v['variant_id'].'">
            <input type="hidden" name="size" value="'.$v['size'].'">
            <input type="hidden" name="color" value="'.$v['color'].'">
            <input type="hidden" name="quantity" value="'.$v['quantity'].'">
   <button type="button"
        class="btn btn-primary btn-sm editVariantBtn"
        data-variant_id="'.$v['variant_id'].'"
        data-size="'.htmlspecialchars($v['size']).'"
        data-color="'.htmlspecialchars($v['color']).'"
        data-quantity="'.$v['quantity'].'">
        Edit
      </button>


<button type="submit" name="action" value="delete_variant" class="btn btn-danger btn-sm">Delete</button>

        </form>
        </td>
        </tr>';
    }
    exit;
}


if (isset($_GET['fetch_images'])) {
    $images = $imageClass->getByProduct($_GET['fetch_images']);
    while ($i = $images->fetch_assoc()) {
        echo '<tr>
        <td>'.$i['product_images_id'].'</td>
        <td><input name="image_url" value="'.htmlspecialchars($i['image_url']).'" class="form-control"></td>
        <td>
        <form method="POST" class="d-inline">
            <input type="hidden" name="product_images_id" value="'.$i['product_images_id'].'">
            <button type="button"
        class="btn btn-primary btn-sm editImageBtn"
        data-id="'.$i['product_images_id'].'"
        data-url="'.htmlspecialchars($i['image_url']).'">
        Edit
      </button>
            <button name="action" value="delete_image" class="btn btn-danger btn-sm">Delete</button>
        </form>
        </td>
        </tr>';
    }
    exit;
}
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
.page-header {  
    background: linear-gradient(150deg, #807777ff, #575f92ff); 
    color: white; 
    padding: 15px 20px; 
    border-radius: 8px; 
    margin-bottom: 20px; 
}
.table thead {
    background: linear-gradient(150deg, #807777ff, #575f92ff);
    color: #fff;
}
.table thead th {
      background-color: #d8d0d0ff;
       color: #1f1c1cff;
}

/* Add User Button */
.btn-success {
    background: linear-gradient(150deg, #807777ff, #575f92ff);
    border: none;
    color: #fff;
}
.btn-success:hover {
    background: linear-gradient(150deg, #575f92ff, #807777ff);
}

/* Action buttons (Edit & Delete) */
.btn-primary {
    background: linear-gradient(150deg, #807777ff, #575f92ff);
    border: none; /* remove edit border */
    color: #fff;
}
.btn-primary:hover {
    background: linear-gradient(150deg, #575f92ff, #807777ff);
}
.btn-danger {
    background: linear-gradient(150deg, #807777ff, #575f92ff);
    border: 1px solid #fff; /* keep delete border */
    color: #fff;
}
.btn-danger:hover {
    background: linear-gradient(150deg, #575f92ff, #807777ff);
}

/* Card */
.card { 
    border-radius: 12px; 
    box-shadow: 0 0 15px rgba(0,0,0,0.08); 
}

/* Modal Styling */
.modal-dialog {
    max-width: 600px; /* bigger modal */
    margin: 10% auto; /* center vertically */
}
.modal-content {
    border-radius: 12px;
    background: linear-gradient(150deg, #807777ff, #575f92ff);
    color: #fff;
}
.modal-body .form-control {
    background-color: rgba(255,255,255,0.1);
    border: 1px solid #fff;
    color: #fff;
}
.modal-body .form-control::placeholder {
    color: #ddd;
}
.modal-body .form-control:focus {
    background-color: rgba(255,255,255,0.15);
    color: #fff;
    border-color: #fff;
    box-shadow: none;
}
.modal-footer .btn {
    color: #fff;
    border: none;
}



</style>
</head>
<body>
<div id="wrapper">
    <?php include "../php/sidebar.php"; ?>
</div>

<div id="content">
<div class="page-header">
    <h3>Product Management</h3>
</div>

<button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addProductModal">
    <i class="fa fa-plus"></i> Add Product
</button>
<br>
<form method="GET" class="row g-2 mb-3">
    <div class="col-md-3">
        <input type="text" name="query" class="form-control" placeholder="Search..." value="<?= htmlspecialchars($search) ?>">
    </div>
    <div class="col-md-3">
        <select name="category" class="form-control">
            <option value="0">All Categories</option>
            <?php
            $categoriesResult = $categoriesClass->getAllCategories(); // re-fetch in case used earlier
            while ($c = $categoriesResult->fetch_assoc()): ?>
                <option value="<?= $c['categories_id'] ?>" <?= $c['categories_id'] == $category_id ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['categories_name']) ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="col-md-2">
        <select name="price" class="form-control">
            <option value="all" <?= $price_range == 'all' ? 'selected' : '' ?>>All Prices</option>
            <option value="0-50" <?= $price_range == '0-50' ? 'selected' : '' ?>>$0 - $50</option>
            <option value="50-100" <?= $price_range == '50-100' ? 'selected' : '' ?>>$50 - $100</option>
            <option value="100-150" <?= $price_range == '100-150' ? 'selected' : '' ?>>$100 - $150</option>
            <option value="150-200" <?= $price_range == '150-200' ? 'selected' : '' ?>>$150 - $200</option>
            <option value="200+" <?= $price_range == '200+' ? 'selected' : '' ?>>$200+</option>
        </select>
    </div>
    <div class="col-md-2">
        <select name="sort" class="form-control">
            <option value="default" <?= $sort == 'default' ? 'selected' : '' ?>>Default</option>
            <option value="name_asc" <?= $sort == 'name_asc' ? 'selected' : '' ?>>Name ↑</option>
            <option value="name_desc" <?= $sort == 'name_desc' ? 'selected' : '' ?>>Name ↓</option>
            <option value="price_asc" <?= $sort == 'price_asc' ? 'selected' : '' ?>>Price ↑</option>
            <option value="price_desc" <?= $sort == 'price_desc' ? 'selected' : '' ?>>Price ↓</option>
        </select>
    </div>
    <div class="col-md-2">
        <button class="btn btn-success w-100"><i class="fa fa-search"></i> Filter</button>
    </div>
</form>

<div class="card p-3">
<table class="table table-bordered">
<thead class="table-dark">
<tr>
<th>ID</th><th>Name</th><th>Price</th><th>Quantity</th><th>Description</th><th>Discount</th><th>Actions</th>
</tr>
</thead>
<tbody>
   <?php $i = 1; ?>
<?php while ($p = $products->fetch_assoc()): ?>
<tr>
<td><?= $i++ ?></td>
<td><?= htmlspecialchars($p['product_name']) ?></td>
<td>$<?= $p['product_price'] ?></td>
<td><?= $p['product_quantity'] ?></td>
<td><?= $p['product_description'] ?></td>
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

<!-- DELETE FORM -->
<form method="POST" class="d-inline deleteForm">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="id" value="<?= $p['product_id'] ?>">
    <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
</form>

<!-- VARIANTS & IMAGES (SEPARATE FROM DELETE FORM) -->
<button type="button"
        class="btn btn-primary btn-sm variantBtn"
        data-id="<?= $p['product_id'] ?>">
    <i class="fa fa-layer-group"></i>
</button>
<button type="button"
        class="btn btn-primary btn-sm imageBtn"
        data-id="<?= $p['product_id'] ?>">
    <i class="fa fa-image"></i>
</button>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>

<!-- ADD PRODUCT MODAL -->
<div class="modal fade" id="addProductModal">
<div class="modal-dialog">
<form method="POST">
<input type="hidden" name="action" value="add">
<div class="modal-content p-3">
<input name="name" class="form-control mb-2" placeholder="Name" required>
<select name="categories_id" class="form-control mb-2" required>
    <option value="">Select Category</option>
    <?php while ($c = $categoriesResult->fetch_assoc()): ?>
        <option value="<?= $c['categories_id'] ?>">
            <?= htmlspecialchars($c['categories_name']) ?>
        </option>
    <?php endwhile; ?>
</select>

<input name="price" type="number" step="0.01" class="form-control mb-2" placeholder="Price" required>
<textarea name="description" class="form-control mb-2" placeholder="Description"></textarea>
<input name="quantity" type="number" class="form-control mb-2" placeholder="Quantity" required>
<input name="discount" type="number" class="form-control mb-2" value="0">

<button class="btn btn-success">Add</button>
</div>
</form>
</div>
</div>

<!-- EDIT PRODUCT MODAL -->
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


<!-- ================= VARIANT MODAL ================= -->
<div class="modal fade" id="variantModal">
<div class="modal-dialog modal-lg">
<div class="modal-content p-3">
<h5>Variants (Product)</h5>

<!-- ADD VARIANT FORM -->
<form method="POST" class="row g-2 mb-3" id="variantAddForm">
<input type="hidden" name="action" value="add_variant">
<input type="hidden" name="product_id" id="variantProductId" value="">
<div class="col"><input name="size" class="form-control" placeholder="Size" required></div>
<div class="col"><input name="color" class="form-control" placeholder="Color" required></div>
<div class="col"><input name="quantity" type="number" class="form-control" placeholder="Qty" required></div>
<div class="col"><button class="btn btn-success w-100">Add</button></div>
</form>

<!-- VARIANT TABLE -->
<div id="variantList">
<!-- This will be filled dynamically -->
<?php
// optional default empty table
echo '<table class="table table-sm table-bordered"><thead><tr><th>Size</th><th>Color</th><th>Quantity</th><th>Actions</th></tr></thead><tbody></tbody></table>';
?>
</div>

</div>
</div>
</div>
<div class="modal fade" id="editVariantModal">
<div class="modal-dialog">
<form method="POST">
<input type="hidden" name="action" value="edit_variant">
<input type="hidden" name="variant_id" id="editVariantId">

<div class="modal-content p-3">
<input name="size" id="editVariantSize" class="form-control mb-2" required>
<input name="color" id="editVariantColor" class="form-control mb-2" required>
<input name="quantity" id="editVariantQty" type="number" class="form-control mb-2" required>

<button class="btn btn-primary">Update Variant</button>
</div>
</form>
</div>
</div>

<!-- ================= IMAGE MODAL ================= -->
<div class="modal fade" id="imageModal">
<div class="modal-dialog">
<div class="modal-content p-3">
<h5>Images (Product)</h5>

<!-- ADD IMAGE FORM -->
<form method="POST" class="row g-2 mb-3" id="imageAddForm">
<input type="hidden" name="action" value="add_image">
<input type="hidden" name="product_id" id="imageProductId" value="">
<div class="col"><input name="image_url" class="form-control" placeholder="Image URL" required></div>
<div class="col"><button class="btn btn-success w-100">Add</button></div>
</form>

<!-- IMAGE TABLE -->
<div id="imageList">
<!-- This will be filled dynamically -->
<?php
// optional default empty table
echo '<table class="table table-sm table-bordered"><thead><tr><th>ID</th><th>URL</th><th>Actions</th></tr></thead><tbody></tbody></table>';
?>
</div>
</div>
</div>
</div>
<div class="modal fade" id="editImageModal">
<div class="modal-dialog">
<form method="POST">
<input type="hidden" name="action" value="edit_image">
<input type="hidden" name="product_images_id" id="editImageId">

<div class="modal-content p-3">
<input name="image_url" id="editImageUrl" class="form-control mb-2" required>
<button class="btn btn-primary">Update Image</button>
</div>
</form>
</div>
</div>

<script>
// EDIT PRODUCT
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

// DELETE PRODUCT WITH SWEETALERT
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

// VARIANT BUTTON CLICK
document.querySelectorAll('.variantBtn').forEach(btn => {
    btn.onclick = () => {
        const productId = btn.dataset.id;
        document.getElementById('variantProductId').value = productId;

        // Fetch variants directly from PHP
        fetch(window.location.href + '?fetch_variants=' + productId)
            .then(res => res.text())
            .then(html => {
                document.querySelector('#variantList table tbody').innerHTML = html;
                new bootstrap.Modal(variantModal).show();
            });
    };
});

// IMAGE BUTTON CLICK
document.querySelectorAll('.imageBtn').forEach(btn => {
    btn.onclick = () => {
        const productId = btn.dataset.id;
        document.getElementById('imageProductId').value = productId;

        // Fetch images directly from PHP
        fetch(window.location.href + '?fetch_images=' + productId)
            .then(res => res.text())
            .then(html => {
                document.querySelector('#imageList table tbody').innerHTML = html;
                new bootstrap.Modal(imageModal).show();
            });
    };
});
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('editVariantBtn')) {
        editVariantId.value = e.target.dataset.variant_id;
        editVariantSize.value = e.target.dataset.size;
        editVariantColor.value = e.target.dataset.color;
        editVariantQty.value = e.target.dataset.quantity;

        new bootstrap.Modal(editVariantModal).show();
    }
});
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('editImageBtn')) {
        editImageId.value = e.target.dataset.id;
        editImageUrl.value = e.target.dataset.url;

        new bootstrap.Modal(editImageModal).show();
    }
});



// ONLY AJAX FOR ADD VARIANT
document.getElementById('variantAddForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const productId = document.getElementById('variantProductId').value;

    fetch('', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    }).then(() => {
        fetch('?fetch_variants=' + productId)
            .then(res => res.text())
            .then(html => {
                document.querySelector('#variantList table tbody').innerHTML = html;
            });
    });
});

// ONLY AJAX FOR ADD IMAGE
document.getElementById('imageAddForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const productId = document.getElementById('imageProductId').value;

    fetch('', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    }).then(() => {
        fetch('?fetch_images=' + productId)
            .then(res => res.text())
            .then(html => {
                document.querySelector('#imageList table tbody').innerHTML = html;
            });
    });
});



</script>

</body>
</html>



