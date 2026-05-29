<?php
include 'auth.php';
include 'role_check.php';
require_role(['Admin', 'Manager', 'Owner']);
include 'config.php';

$id = (int)$_GET['id'];

$result = $conn->query("SELECT * FROM categories WHERE category_id = $id");

if (!$result || $result->num_rows == 0) {
    die("Category not found.");
}

$category = $result->fetch_assoc();

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category_name = trim($_POST['category_name']);
    $description = trim($_POST['description']);

    $stmt = $conn->prepare("
        UPDATE categories
        SET category_name = ?, description = ?
        WHERE category_id = ?
    ");
    $stmt->bind_param("ssi", $category_name, $description, $id);

    if ($stmt->execute()) {
        header("Location: categories.php");
        exit();
    } else {
        $message = "Update failed.";
    }

    $stmt->close();
}

include 'header.php';
include 'sidebar.php';
?>

<div class="content">
    <h1 class="page-title">Edit Category</h1>

    <div class="page-card">
        <form method="POST">
            <label>Category Name</label>
            <input type="text" name="category_name" value="<?php echo htmlspecialchars($category['category_name']); ?>" required>

            <label>Description</label>
            <textarea name="description"><?php echo htmlspecialchars($category['description']); ?></textarea>

            <button type="submit" class="action-btn">Update Category</button>
        </form>

        <?php if (!empty($message)): ?>
            <p class="warning-text"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>