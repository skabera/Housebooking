<?php
include '../config/database.php';
include '../includes/metadata.php';
include '../includes/components/sidebar.php';
include '../includes/components/topbar.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../user/login.php");
    exit();
}

// Handle Delete
if(isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM houses WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: manage_houses.php");
    exit();
}

$pagination = get_pagination_data($pdo, 'houses', '1', [], 10);
$houses = $pdo->query("SELECT * FROM houses ORDER BY created_at DESC LIMIT {$pagination['limit']} OFFSET {$pagination['offset']}")->fetchAll();
?>

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>Manage Houses</h1>
        <a href="add_house.php" class="btn btn-primary">Add New House</a>
    </div>

    <div class="glass" style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 1px solid var(--glass-border);">
                    <th style="padding: 1rem; color: var(--text-muted);">Image</th>
                    <th style="padding: 1rem; color: var(--text-muted);">Title</th>
                    <th style="padding: 1rem; color: var(--text-muted);">Location</th>
                    <th style="padding: 1rem; color: var(--text-muted);">Price</th>
                    <th style="padding: 1rem; color: var(--text-muted);">Status</th>
                    <th style="padding: 1rem; color: var(--text-muted);">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($houses as $house): ?>
                <tr style="border-bottom: 1px solid var(--glass-border);">
                    <td style="padding: 1rem;">
                        <img src="../uploads/houses/<?php echo $house['image']; ?>" style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px;">
                    </td>
                    <td style="padding: 1rem;"><?php echo $house['title']; ?></td>
                    <td style="padding: 1rem;"><?php echo $house['location']; ?></td>
                    <td style="padding: 1rem;">$<?php echo $house['price']; ?></td>
                    <td style="padding: 1rem;">
                        <span style="padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.8rem; background: <?php echo $house['status'] == 'available' ? 'rgba(16, 185, 129, 0.1)' : 'rgba(245, 158, 11, 0.1)'; ?>; color: <?php echo $house['status'] == 'available' ? '#10b981' : '#f59e0b'; ?>; border: 1px solid currentColor;">
                            <?php echo ucfirst($house['status']); ?>
                        </span>
                    </td>
                    <td style="padding: 1rem;">
                        <a href="?delete=<?php echo $house['id']; ?>" style="color: #ef4444; text-decoration: none;" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php render_pagination($pagination['current_page'], $pagination['total_pages']); ?>
</div>

</div> <!-- Close Topbar Content Spacer -->
<?php include '../includes/footer.php'; ?>
