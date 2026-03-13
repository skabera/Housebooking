<?php
include 'config/database.php';
include 'includes/metadata.php';

// Authentication Guard: Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: user/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'] ?? 'user';
$today = date('Y-m-d');

// --- COMMON DATA ---
$available_houses = $pdo->query("SELECT COUNT(*) FROM houses WHERE status = 'available'")->fetchColumn();
$total_houses = $pdo->query("SELECT COUNT(*) FROM houses")->fetchColumn();
$booked_houses = $total_houses - $available_houses;

if ($user_role === 'admin') {
    // --- ADMIN STATS ---
    $pending_bookings = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'pending'")->fetchColumn();
    $total_customers = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
    
    // Calculate Total Revenue
    $revenue_stmt = $pdo->query("
        SELECT SUM(h.price * (DATEDIFF(b.check_out, b.check_in))) 
        FROM bookings b 
        JOIN houses h ON b.house_id = h.id 
        WHERE b.status IN ('approved', 'finished')
    ");
    $total_revenue = $revenue_stmt->fetchColumn() ?: 0;
    
    $stats = [
        ['label' => 'Total Revenue', 'value' => '$' . number_format($total_revenue, 0), 'icon' => 'fa-dollar-sign', 'color' => '#10b981', 'bg' => 'rgba(16, 185, 129, 0.1)'],
        ['label' => 'Total Properties', 'value' => $total_houses, 'icon' => 'fa-home', 'color' => '#3b82f6', 'bg' => 'rgba(59, 130, 246, 0.1)'],
        ['label' => 'Pending Requests', 'value' => $pending_bookings, 'icon' => 'fa-clock', 'color' => '#f59e0b', 'bg' => 'rgba(245, 158, 11, 0.1)'],
        ['label' => 'Total Customers', 'value' => $total_customers, 'icon' => 'fa-users', 'color' => '#8b5cf6', 'bg' => 'rgba(139, 92, 246, 0.1)']
    ];
} else {
    // --- USER STATS ---
    $my_bookings = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE user_id = ?");
    $my_bookings->execute([$user_id]);
    $my_bookings_count = $my_bookings->fetchColumn();
    
    $active_stays = $pdo->prepare("
        SELECT COUNT(*) FROM bookings 
        WHERE user_id = ? AND status = 'approved' AND check_in <= ? AND check_out >= ?
    ");
    $active_stays->execute([$user_id, $today, $today]);
    $active_stays_count = $active_stays->fetchColumn();
    
    // Calculate Personal Spending
    $spending_stmt = $pdo->prepare("
        SELECT SUM(h.price * (DATEDIFF(b.check_out, b.check_in))) 
        FROM bookings b 
        JOIN houses h ON b.house_id = h.id 
        WHERE b.user_id = ? AND b.status IN ('approved', 'finished')
    ");
    $spending_stmt->execute([$user_id]);
    $total_spent = $spending_stmt->fetchColumn() ?: 0;
    
    $stats = [
        ['label' => 'My Bookings', 'value' => $my_bookings_count, 'icon' => 'fa-calendar-check', 'color' => '#3b82f6', 'bg' => 'rgba(59, 130, 246, 0.1)'],
        ['label' => 'Active Stays', 'value' => $active_stays_count, 'icon' => 'fa-key', 'color' => '#10b981', 'bg' => 'rgba(16, 185, 129, 0.1)'],
        ['label' => 'Total Spent', 'value' => '$' . number_format($total_spent, 0), 'icon' => 'fa-wallet', 'color' => '#8b5cf6', 'bg' => 'rgba(139, 92, 246, 0.1)'],
        ['label' => 'Available Stays', 'value' => $available_houses, 'icon' => 'fa-search-location', 'color' => '#f59e0b', 'bg' => 'rgba(245, 158, 11, 0.1)']
    ];
}

$houses = $pdo->query("SELECT * FROM houses WHERE status = 'available' ORDER BY created_at DESC LIMIT 4")->fetchAll();

include 'includes/components/sidebar.php';
include 'includes/components/topbar.php';
?>

<!-- Statistics Section -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
    <?php foreach ($stats as $stat): ?>
    <div class="stat-card">
        <div class="stat-icon" style="background: <?php echo $stat['bg']; ?>; color: <?php echo $stat['color']; ?>;"><i class="fas <?php echo $stat['icon']; ?>"></i></div>
        <div>
            <div class="stat-value"><?php echo $stat['value']; ?></div>
            <div class="stat-label"><?php echo $stat['label']; ?></div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div style="display: grid; grid-template-columns: 1fr 350px; gap: 2rem; margin-bottom: 3rem;">
    <!-- Featured Properties -->
    <div class="chart-container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h3 style="font-size: 1.25rem; font-weight: 700; color: white;">Market Highlights</h3>
            <a href="pages/houses.php" style="color: var(--primary); text-decoration: none; font-size: 0.9rem; font-weight: 600;">Explore All <i class="fas fa-arrow-right" style="font-size: 0.75rem; margin-left: 4px;"></i></a>
        </div>
        
        <div class="house-grid" style="grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));">
            <?php foreach($houses as $house): ?>
            <a href="pages/house_details.php?id=<?php echo $house['id']; ?>" style="text-decoration: none; color: inherit;">
                <div class="house-card">
                    <img src="/booking/uploads/houses/<?php echo $house['image']; ?>" class="house-image" style="height: 160px;">
                    <div class="house-info">
                        <h4 class="house-title" style="font-size: 1rem;"><?php echo $house['title']; ?></h4>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <p class="house-price" style="font-size: 0.9rem;">$<?php echo $house['price']; ?>/night</p>
                            <span style="font-size: 0.75rem; color: var(--text-muted);"><i class="fas fa-map-marker-alt" style="margin-right: 4px;"></i><?php echo $house['location']; ?></span>
                        </div>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Distribution Chart -->
    <div class="chart-container" style="display: flex; flex-direction: column;">
        <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 2rem; color: white;">Property Status</h3>
        <div style="flex-grow: 1; display: flex; align-items: center; justify-content: center; position: relative;">
            <canvas id="statusChart"></canvas>
        </div>
        <div style="margin-top: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="width: 12px; height: 12px; border-radius: 3px; background: var(--primary);"></div>
                    <span style="font-size: 0.9rem; color: var(--text-muted);">Available</span>
                </div>
                <span style="font-weight: 700; color: white;"><?php echo $available_houses; ?></span>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="width: 12px; height: 12px; border-radius: 3px; background: rgba(59, 130, 246, 0.3);"></div>
                    <span style="font-size: 0.9rem; color: var(--text-muted);">Occupied</span>
                </div>
                <span style="font-weight: 700; color: white;"><?php echo $booked_houses; ?></span>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('statusChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Available', 'Booked'],
            datasets: [{
                data: [<?php echo $available_houses; ?>, <?php echo $booked_houses; ?>],
                backgroundColor: ['#3b82f6', 'rgba(59, 130, 246, 0.2)'],
                borderColor: 'rgba(255, 255, 255, 0.1)',
                borderWidth: 2,
                cutout: '75%'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            }
        }
    });
});
</script>

</div> <!-- Close Topbar Content Spacer -->
<?php include 'includes/footer.php'; ?>
