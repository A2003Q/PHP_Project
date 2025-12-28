<?php
require_once "../CRUD/CRUD.php";

class Products extends CRUD {

    public function getAllProducts() {
        return $this->read("SELECT * FROM products ORDER BY product_id DESC");
    }

  public function addProduct($name, $price, $description, $quantity, $discount) {
    $stmt = $this->conn->prepare(
        "INSERT INTO products 
        (product_name, product_price, product_description, product_quantity, product_discount)
        VALUES (?, ?, ?, ?, ?)"
    );
    $stmt->bind_param("sdsii", $name, $price, $description, $quantity, $discount); //Binds your PHP variables to the placeholders in the SQL ,"sdsii" explains the types of the variables ,this with prepare stat -->This ensures correct type handling and prevents SQL injection.

    if ($stmt->execute()) {
        return $this->conn->insert_id; // ✅ RETURN PRODUCT ID
    }
    return false;
}


    public function updateProduct($id, $name, $price, $description, $quantity, $discount) {
        $stmt = $this->conn->prepare(
            "UPDATE products SET 
            product_name=?, product_price=?, product_description=?, 
            product_quantity=?, product_discount=?
            WHERE product_id=?"
        );
        $stmt->bind_param("sdsiii", $name, $price, $description, $quantity, $discount, $id);
        return $stmt->execute();
    }
    
    public function addProductCategory($productId, $categoryId) {
    $stmt = $this->conn->prepare(
        "INSERT INTO product_categories (product_id, categories_id) VALUES (?, ?)"
    );
    $stmt->bind_param("ii", $productId, $categoryId);
    $stmt->execute();
}


    public function deleteProduct($id) {
        $stmt = $this->conn->prepare("DELETE FROM products WHERE product_id=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function getFilteredProducts($category_id = 0, $sort = 'default', $price_range = 'all', $search = '') {
    $orderBy = '';
    if ($sort === 'name_asc') $orderBy = ' ORDER BY p.product_name ASC';
    elseif ($sort === 'name_desc') $orderBy = ' ORDER BY p.product_name DESC';
    elseif ($sort === 'price_asc') $orderBy = ' ORDER BY p.product_price ASC';
    elseif ($sort === 'price_desc') $orderBy = ' ORDER BY p.product_price DESC';

    // Price condition
    $priceCondition = "";
    if ($price_range !== 'all') {
        if ($price_range === '0-50') $priceCondition = " AND p.product_price BETWEEN 0 AND 50";
        elseif ($price_range === '50-100') $priceCondition = " AND p.product_price BETWEEN 50.01 AND 100";
        elseif ($price_range === '100-150') $priceCondition = " AND p.product_price BETWEEN 100.01 AND 150";
        elseif ($price_range === '150-200') $priceCondition = " AND p.product_price BETWEEN 150.01 AND 200";
        elseif ($price_range === '200+') $priceCondition = " AND p.product_price > 200";
    }

    $params = [];
    $types = '';
    $whereClause = ' WHERE 1 '; // default always true

    if (!empty(trim($search))) {
        $whereClause .= " AND (p.product_name LIKE ? OR p.product_description LIKE ?)";
        $searchSafe = "%$search%";
        $params[] = $searchSafe;
        $params[] = $searchSafe;
        $types .= 'ss';
    }

    if ($category_id > 0) {
        $whereClause .= " AND pc.categories_id = ?";
        $params[] = $category_id;
        $types .= 'i';
        $sql = "
            SELECT p.*, pi.image_url 
            FROM products p
            JOIN product_categories pc ON p.product_id = pc.product_id
            LEFT JOIN product_images pi ON p.product_id = pi.product_id
            $whereClause
            $priceCondition
            $orderBy
        ";
    } else {
        $sql = "
            SELECT p.*, pi.image_url 
            FROM products p
            LEFT JOIN product_images pi ON p.product_id = pi.product_id
            $whereClause
            $priceCondition
            $orderBy
        ";
    }

    $stmt = $this->conn->prepare($sql);
    if ($params) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt->get_result();
}

}

