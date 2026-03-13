<?php
include '../config/database.php';
include '../includes/metadata.php';

// Authentication Guard
if (!isset($_SESSION['user_id'])) {
    header("Location: ../user/login.php");
    exit();
}

include '../includes/components/sidebar.php';
include '../includes/components/topbar.php';

$where = "status = 'available'";
$params = [];

if(isset($_GET['search']) && !empty($_GET['search'])) {
    $search = "%" . $_GET['search'] . "%";
    $where .= " AND (title LIKE ? OR location LIKE ? OR description LIKE ?)";
    $params = [$search, $search, $search];
}

$pagination = get_pagination_data($pdo, 'houses', $where, $params, 6);
$stmt = $pdo->prepare("SELECT * FROM houses WHERE $where ORDER BY created_at DESC LIMIT {$pagination['limit']} OFFSET {$pagination['offset']}");
$stmt->execute($params);
$houses = $stmt->fetchAll();
?>

<div style="margin-bottom: 3rem;">
    <h2 style="font-size: 1.75rem; font-weight: 800; color: white; margin-bottom: 0.5rem;">Explore Properties</h2>
    <p style="color: var(--text-muted); font-weight: 500;">Find your perfect stay from our curated collection.</p>
</div>

<div class="chart-container" style="margin-bottom: 3rem;">
    <form method="GET" style="display: flex; gap: 1rem; align-items: center;">
        <div style="position: relative; flex-grow: 1;">
            <i class="fas fa-search" style="position: absolute; left: 1.25rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.9rem;"></i>
            <input type="text" name="search" class="form-control" placeholder="Search by location, title, or keywords..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>" style="width: 100%; padding-left: 3.2rem;">
        </div>
        <button type="submit" class="btn btn-primary">Find Stays</button>
        <?php if(isset($_GET['search'])): ?>
            <a href="houses.php" class="btn" style="background: rgba(255,255,255,0.05); color: white;">Clear</a>
        <?php endif; ?>
    </form>
</div>

<div class="house-grid">
    <?php foreach($houses as $house): ?>
    <a href="house_details.php?id=<?php echo $house['id']; ?>" style="text-decoration: none; color: inherit;">
        <div class="house-card">
            <img src="/booking/uploads/houses/<?php echo $house['image']; ?>" class="house-image">
            <div class="house-info">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <span style="color: var(--primary); font-size: 0.75rem; font-weight: 800; text-transform: uppercase;"><?php echo $house['location']; ?></span>
                    <span style="padding: 4px 10px; border-radius: 6px; background: rgba(59, 130, 246, 0.1); color: var(--primary); font-size: 0.7rem; font-weight: 700;">Verified</span>
                </div>
                <h3 class="house-title"><?php echo $house['title']; ?></h3>
                <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 1.5rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 2.6rem;">
                    <?php echo $house['description']; ?>
                </p>
                <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--glass-border); padding-top: 1.25rem;">
                    <p class="house-price">$<?php echo $house['price']; ?> <span style="font-size: 0.8rem; color: var(--text-muted); font-weight: 400;">/ night</span></p>
                    <span class="btn" style="padding: 0.5rem 1rem; font-size: 0.8rem; background: rgba(255,255,255,0.05); color: white; border-radius: 10px;">View</span>
                </div>
            </div>
        </div>
    </a>
    <?php endforeach; ?>

    <?php if(count($houses) == 0): ?>
        <div style="grid-column: 1/-1; text-align: center; padding: 6rem; background: rgba(30, 41, 59, 0.2); border-radius: 20px; border: 1px dashed var(--glass-border);">
            <i class="fas fa-search" style="font-size: 3rem; color: var(--text-muted); margin-bottom: 1.5rem;"></i>
            <h3 style="color: white; margin-bottom: 0.5rem;">No Houses Found</h3>
            <p style="color: var(--text-muted);">Try adjusting your search criteria or explore other locations.</p>
        </div>
    <?php endif; ?>
</div>

<?php render_pagination($pagination['current_page'], $pagination['total_pages']); ?>

</div> <!-- Close Topbar Content Spacer -->
<?php include '../includes/footer.php'; ?>
