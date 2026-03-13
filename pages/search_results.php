<?php
include '../config/database.php';
include '../includes/header.php';

// Authentication Guard
if (!isset($_SESSION['user_id'])) {
    header("Location: ../user/login.php");
    exit();
}

include '../includes/components/sidebar.php';
include '../includes/components/topbar.php';

$query = $_GET['q'] ?? '';
$results = [];

if (!empty($query)) {
    $search = "%$query%";
    $stmt = $pdo->prepare("
        SELECT * FROM houses 
        WHERE (title LIKE ? OR location LIKE ? OR description LIKE ?) 
        AND status = 'available'
    ");
    $stmt->execute([$search, $search, $search]);
    $results = $stmt->fetchAll();
}
?>

<div class="container">
    <div style="margin-bottom: 2rem;">
        <h1 style="font-size: 2rem;">Search Results for: "<span style="color: var(--primary);"><?php echo htmlspecialchars($query); ?></span>"</h1>
        <p style="color: var(--text-muted);"><?php echo count($results); ?> properties found</p>
    </div>

    <div class="house-grid">
        <?php foreach($results as $house): ?>
        <a href="house_details.php?id=<?php echo $house['id']; ?>" style="text-decoration: none; color: inherit;">
            <div class="house-card glass">
                <img src="/booking/uploads/houses/<?php echo $house['image']; ?>" class="house-image" alt="<?php echo $house['title']; ?>">
                <div class="house-info">
                    <p style="color: var(--primary); font-size: 0.85rem; font-weight: 700; text-transform: uppercase; margin-bottom: 0.5rem;"><?php echo $house['location']; ?></p>
                    <h3 class="house-title"><?php echo $house['title']; ?></h3>
                    <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.5rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?php echo $house['description']; ?></p>
                    <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--glass-border); padding-top: 1.5rem;">
                        <p class="house-price">$<?php echo $house['price']; ?> <span style="font-size: 0.8rem; color: var(--text-muted); font-weight: 400; -webkit-text-fill-color: var(--text-muted);">/ night</span></p>
                        <span style="font-weight: 700; font-size: 0.9rem; color: var(--primary);">View <i class="fas fa-chevron-right" style="font-size: 0.75rem; margin-left: 4px;"></i></span>
                    </div>
                </div>
            </div>
        </a>
        <?php endforeach; ?>

        <?php if(count($results) == 0): ?>
            <div style="grid-column: 1/-1; text-align: center; padding: 4rem;">
                <i class="fas fa-search" style="font-size: 3rem; color: var(--glass-border); margin-bottom: 1rem; display: block;"></i>
                <p style="color: var(--text-muted); font-size: 1.1rem;">We couldn't find anything matching your search.</p>
                <a href="houses.php" class="btn btn-primary" style="margin-top: 1.5rem; display: inline-block;">Browse All Properties</a>
            </div>
        <?php endif; ?>
    </div>
</div>

</div> <!-- Close Topbar Content Spacer -->
<?php include '../includes/footer.php'; ?>
