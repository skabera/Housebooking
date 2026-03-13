<?php
include '../config/database.php';
include '../includes/metadata.php';
include '../includes/components/sidebar.php';
include '../includes/components/topbar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("
    SELECT b.*, h.title, h.location, h.price, h.image 
    FROM bookings b 
    JOIN houses h ON b.house_id = h.id 
    WHERE b.user_id = ? 
    ORDER BY b.booking_date DESC
");
$stmt->execute([$user_id]);
$bookings = $stmt->fetchAll();

$message = isset($_GET['msg']) ? $_GET['msg'] : "";
?>

<div class="container" style="padding: 2rem;">
    <h1 style="margin-bottom: 2.5rem;">My Bookings</h1>
    
    <?php if($message): ?>
        <p style="color: #10b981; margin-bottom: 2rem; background: rgba(16, 185, 129, 0.1); padding: 1rem; border-radius: 8px; border: 1px solid rgba(16, 185, 129, 0.2);"><?php echo $message; ?></p>
    <?php endif; ?>

    <div class="glass" style="overflow-x: auto; border-radius: 16px;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 1px solid var(--glass-border);">
                    <th style="padding: 1.5rem; color: var(--text-muted);">Property</th>
                    <th style="padding: 1.5rem; color: var(--text-muted);">Stay Dates</th>
                    <th style="padding: 1.5rem; color: var(--text-muted);">Price</th>
                    <th style="padding: 1.5rem; color: var(--text-muted);">Booking Date</th>
                    <th style="padding: 1.5rem; color: var(--text-muted);">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($bookings as $booking): ?>
                <tr style="border-bottom: 1px solid var(--glass-border);">
                    <td style="padding: 1.5rem;">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <img src="/booking/uploads/houses/<?php echo $booking['image']; ?>" style="width: 80px; height: 50px; object-fit: cover; border-radius: 8px;">
                            <div>
                                <span style="font-weight: 600;"><?php echo $booking['title']; ?></span><br>
                                <small style="color: var(--text-muted);"><?php echo $booking['location']; ?></small>
                            </div>
                        </div>
                    </td>
                    <td style="padding: 1.5rem; font-size: 0.9rem;">
                        <?php echo date('M d', strtotime($booking['check_in'])); ?> - <?php echo date('M d, Y', strtotime($booking['check_out'])); ?>
                    </td>
                    <td style="padding: 1.5rem; font-weight: 700; color: var(--primary);">$<?php echo $booking['price']; ?></td>
                    <td style="padding: 1.5rem; color: var(--text-muted);"><?php echo date('M d, Y', strtotime($booking['booking_date'])); ?></td>
                    <td style="padding: 1.5rem;">
                        <?php 
                        $status_colors = [
                            'pending' => ['#f59e0b', 'rgba(245, 158, 11, 0.1)'],
                            'approved' => ['#10b981', 'rgba(16, 185, 129, 0.1)'],
                            'rejected' => ['#ef4444', 'rgba(239, 68, 68, 0.1)'],
                            'finished' => ['#64748b', 'rgba(100, 116, 139, 0.1)'],
                            'cancelled' => ['#94a3b8', 'rgba(148, 163, 184, 0.1)']
                        ];
                        $color = $status_colors[$booking['status']] ?? ['#fff', 'rgba(255,255,255,0.1)'];
                        ?>
                        <span style="padding: 0.4rem 1rem; border-radius: 20px; font-size: 0.8rem; background: <?php echo $color[1]; ?>; color: <?php echo $color[0]; ?>; border: 1px solid <?php echo $color[0]; ?>33;">
                            <?php echo ucfirst($booking['status']); ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if(count($bookings) == 0): ?>
                    <tr>
                        <td colspan="5" style="padding: 4rem; text-align: center; color: var(--text-muted);">You haven't made any bookings yet. <a href="../pages/houses.php" style="color: var(--primary); text-decoration: none;">Browse houses</a></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</div> <!-- Close Topbar Content Spacer -->
<?php include '../includes/footer.php'; ?>
