<?php
include '../config/database.php';
include '../includes/metadata.php';
include '../includes/components/sidebar.php';
include '../includes/components/topbar.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../user/login.php");
    exit();
}

// Fetch stats
$total_houses = $pdo->query("SELECT COUNT(*) FROM houses")->fetchColumn();
$total_bookings = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
$pending_bookings = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'pending'")->fetchColumn();
?>

<div class="container" style="padding: 2rem;">
    <div style="margin-bottom: 3rem;">
        <h1 style="font-size: 2.2rem; font-weight: 800; letter-spacing: -0.02em;">Admin Dashboard</h1>
        <p style="color: var(--text-muted); font-weight: 500;">Welcome back! Here's an overview of your booking system.</p>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin-bottom: 4rem;">
        <div class="glass" style="padding: 2.5rem; border-radius: 32px; display: flex; align-items: flex-start; justify-content: space-between;">
            <div>
                <p style="color: var(--text-muted); font-size: 0.95rem; font-weight: 600; margin-bottom: 0.75rem;">Total Houses</p>
                <h2 style="font-size: 3rem; font-weight: 800; color: white; line-height: 1;"><?php echo $total_houses; ?></h2>
            </div>
            <div style="width: 60px; height: 60px; border-radius: 20px; background: rgba(129, 140, 248, 0.1); display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 1.5rem;">
                <i class="fas fa-home"></i>
            </div>
        </div>
        <div class="glass" style="padding: 2.5rem; border-radius: 32px; display: flex; align-items: flex-start; justify-content: space-between;">
            <div>
                <p style="color: var(--text-muted); font-size: 0.95rem; font-weight: 600; margin-bottom: 0.75rem;">Total Bookings</p>
                <h2 style="font-size: 3rem; font-weight: 800; color: var(--primary); line-height: 1;"><?php echo $total_bookings; ?></h2>
            </div>
            <div style="width: 60px; height: 60px; border-radius: 20px; background: rgba(129, 140, 248, 0.1); display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 1.5rem;">
                <i class="fas fa-calendar-check"></i>
            </div>
        </div>
        <div class="glass" style="padding: 2.5rem; border-radius: 32px; display: flex; align-items: flex-start; justify-content: space-between;">
            <div>
                <p style="color: var(--text-muted); font-size: 0.95rem; font-weight: 600; margin-bottom: 0.75rem;">Pending Requests</p>
                <h2 style="font-size: 3rem; font-weight: 800; color: #fbbf24; line-height: 1;"><?php echo $pending_bookings; ?></h2>
            </div>
            <div style="width: 60px; height: 60px; border-radius: 20px; background: rgba(251, 191, 36, 0.1); display: flex; align-items: center; justify-content: center; color: #fbbf24; font-size: 1.5rem;">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>

    <div style="display: flex; gap: 1.5rem; flex-wrap: wrap;">
        <a href="add_house.php" class="btn btn-primary" style="padding: 1.2rem 2.5rem; border-radius: 20px;">
            <i class="fas fa-plus-circle"></i> Add New House
        </a>
        <a href="manage_houses.php" class="btn glass" style="padding: 1.2rem 2.5rem; border-radius: 20px; border-color: rgba(255,255,255,0.1);">
            <i class="fas fa-tasks"></i> Manage Houses
        </a>
        <a href="bookings.php" class="btn glass" style="padding: 1.2rem 2.5rem; border-radius: 20px; border-color: rgba(255,255,255,0.1);">
            <i class="fas fa-list-ul"></i> View Bookings
        </a>
    </div>
</div>

</div> <!-- Close Topbar Content Spacer -->
<?php include '../includes/footer.php'; ?>
