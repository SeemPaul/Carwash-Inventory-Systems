<?php
include 'auth.php';
include 'role_check.php';
require_role(['Admin', 'Manager', 'Owner']);
include 'config.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category_name = trim($_POST['category_name']);
    $description = trim($_POST['description']);

    if (!empty($category_name)) {
        $stmt = $conn->prepare("
            INSERT INTO categories (category_name, description)
            VALUES (?, ?)
        ");
        $stmt->bind_param("ss", $category_name, $description);

        if ($stmt->execute()) {
            $message = "Category added successfully!";
        } else {
            $message = "Error adding category.";
        }

        $stmt->close();
    } else {
        $message = "Category name is required.";
    }
}

include 'header.php';
?>

<div class="main-wrapper">
    <?php include 'sidebar.php'; ?>

    <div class="content">
    <h1 class="page-title">Add Category</h1>

    <?php include 'breadcrumb.php'; ?>
  

    <div class="page-card">
        <a class="action-btn" href="categories.php">View Categories</a>
        <a class="action-btn" href="add_product.php">Add Product</a>
        <a class="action-btn" href="dashboard.php">Dashboard</a>
    </div>

    <div class="page-card">
        <form method="POST">
            <label>Category Name</label>
            <input type="text" name="category_name" required>

            <label>Description</label>
            <textarea name="description"></textarea>

            <button type="submit" class="action-btn">Save Category</button>
        </form>

        <?php if (!empty($message)): ?>
            <p class="success-text"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
    </div>

<?php include 'footer.php'; ?>