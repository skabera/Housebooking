<?php
include '../config/database.php';
include '../includes/metadata.php';
include '../includes/components/sidebar.php';
include '../includes/components/topbar.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../user/login.php");
    exit();
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $location = $_POST['location'];
    
    // Image Upload
    $imageName = time() . "_" . $_FILES['image']['name'];
    $target = "../uploads/houses/" . $imageName;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO houses (title, description, price, location, image) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$title, $description, $price, $location, $imageName]);
            $message = "House added successfully!";
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
        }
    } else {
        $message = "Failed to upload image.";
    }
}
?>

<div class="container" style="max-width: 800px;">
    <div class="glass" style="padding: 2.5rem;">
        <h2 style="margin-bottom: 2rem;">Add New House</h2>
        <?php if($message): ?>
            <p style="color: #10b981; margin-bottom: 1rem;"><?php echo $message; ?></p>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>House Title</label>
                <input type="text" name="title" class="form-control" required placeholder="Modern Villa with Pool">
            </div>
            <div class="form-group">
                <label>Location</label>
                <input type="text" name="location" class="form-control" required placeholder="Los Angeles, CA">
            </div>
            <div class="form-group">
                <label>Price per Night ($)</label>
                <input type="number" step="0.01" name="price" class="form-control" required placeholder="250.00">
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="5" required placeholder="Describe the house..."></textarea>
            </div>
            <div class="form-group">
                <label>House Image</label>
                <input type="file" name="image" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Upload House</button>
        </form>
    </div>
</div>

</div> <!-- Close Topbar Content Spacer -->
<?php include '../includes/footer.php'; ?>
