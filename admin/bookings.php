<?php
include '../config/database.php';
include '../includes/metadata.php';
include '../includes/components/sidebar.php';
include '../includes/components/topbar.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../user/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $booking_id = $_POST['booking_id'];
    $action = $_POST['action'];
    
    $stmt = $pdo->prepare("SELECT house_id FROM bookings WHERE id = ?");
    $stmt->execute([$booking_id]);
    $house_id = $stmt->fetchColumn();
    
    $pdo->beginTransaction();
    try {
        if ($action == 'update_status') {
            $new_status = $_POST['status'];
            $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?")->execute([$new_status, $booking_id]);
            
            // If setting to approved, mark house as booked
            if ($new_status == 'approved') {
                $pdo->prepare("UPDATE houses SET status = 'booked' WHERE id = ?")->execute([$house_id]);
            } else {
                // For any other status (rejected, finished, pending, cancelled), 
                // we'll let the automatic logic in database.php handle house availability,
                // but for immediate feedback, we check if ANY other approved booking exists for today.
                $today = date('Y-m-d');
                $stmt = $pdo->prepare("SELECT id FROM bookings WHERE house_id = ? AND status = 'approved' AND check_in <= ? AND check_out >= ?");
                $stmt->execute([$house_id, $today, $today]);
                if (!$stmt->fetch()) {
                    $pdo->prepare("UPDATE houses SET status = 'available' WHERE id = ?")->execute([$house_id]);
                }
            }
            $msg = "Booking status updated to " . ucfirst($new_status) . "!";
        } elseif ($action == 'cancel') {
            $pdo->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ?")->execute([$booking_id]);
            
            // Immediately check if we should free up the house
            $today = date('Y-m-d');
            $stmt = $pdo->prepare("SELECT id FROM bookings WHERE house_id = ? AND status = 'approved' AND check_in <= ? AND check_out >= ?");
            $stmt->execute([$house_id, $today, $today]);
            if (!$stmt->fetch()) {
                $pdo->prepare("UPDATE houses SET status = 'available' WHERE id = ?")->execute([$house_id]);
            }
            $msg = "Booking cancelled.";
        }
        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $msg = "Error: " . $e->getMessage();
    }
}

$stmt = $pdo->query("
    SELECT b.*, h.title, h.location, u.name as user_name, u.email as user_email
    FROM bookings b 
    JOIN houses h ON b.house_id = h.id 
    JOIN users u ON b.user_id = u.id
    ORDER BY b.booking_date DESC
");
$bookings = $stmt->fetchAll();
?>

<div style="margin-bottom: 3rem; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h2 style="font-size: 1.75rem; font-weight: 800; color: white; margin-bottom: 0.5rem;">Booking Management</h2>
        <p style="color: var(--text-muted); font-weight: 500;">Monitor and manage guest reservations across all properties.</p>
    </div>
    <div style="display: flex; gap: 1rem;">
        <button class="btn" style="background: rgba(255,255,255,0.05); color: white;"><i class="fas fa-download"></i> Export</button>
        <a href="manage_houses.php" class="btn btn-primary"><i class="fas fa-plus"></i> New Property</a>
    </div>
</div>

<?php if(isset($msg)): ?>
    <div style="padding: 1rem 1.5rem; background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.2); border-radius: 12px; margin-bottom: 2rem; color: #60a5fa; display: flex; align-items: center; gap: 12px;">
        <i class="fas fa-info-circle"></i> <?php echo $msg; ?>
    </div>
<?php endif; ?>

<div class="chart-container" style="padding: 0; overflow: hidden; border-radius: 20px;">
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; min-width: 900px;">
            <thead>
                <tr style="background: rgba(255, 255, 255, 0.02); border-bottom: 1px solid var(--glass-border);">
                    <th style="padding: 1.25rem 1.5rem; color: var(--text-muted); font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Property</th>
                    <th style="padding: 1.25rem 1.5rem; color: var(--text-muted); font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Customer</th>
                    <th style="padding: 1.25rem 1.5rem; color: var(--text-muted); font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Schedule</th>
                    <th style="padding: 1.25rem 1.5rem; color: var(--text-muted); font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Status</th>
                    <th style="padding: 1.25rem 1.5rem; color: var(--text-muted); font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($bookings as $booking): ?>
                <tr style="border-bottom: 1px solid var(--glass-border); transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.01)'" onmouseout="this.style.background='transparent'">
                    <td style="padding: 1.5rem;">
                        <div style="font-weight: 700; color: white; margin-bottom: 4px;"><?php echo $booking['title']; ?></div>
                        <div style="font-size: 0.8rem; color: var(--text-muted); display: flex; align-items: center; gap: 4px;">
                            <i class="fas fa-map-marker-alt" style="font-size: 0.7rem;"></i> <?php echo $booking['location']; ?>
                        </div>
                    </td>
                    <td style="padding: 1.5rem;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(255,255,255,0.05); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 0.9rem;">
                                <?php echo strtoupper(substr($booking['user_name'], 0, 1)); ?>
                            </div>
                            <div>
                                <div style="font-weight: 600; color: white; font-size: 0.9rem;"><?php echo $booking['user_name']; ?></div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);"><?php echo $booking['user_email']; ?></div>
                            </div>
                        </div>
                    </td>
                    <td style="padding: 1.5rem;">
                        <div style="font-size: 0.85rem; color: white; background: rgba(255,255,255,0.03); padding: 8px 12px; border-radius: 10px; display: inline-block; border: 1px solid rgba(255,255,255,0.05);">
                            <span style="color: var(--text-muted);">In:</span> <?php echo date('M d', strtotime($booking['check_in'])); ?>
                            <span style="color: var(--glass-border); margin: 0 8px;">|</span>
                            <span style="color: var(--text-muted);">Out:</span> <?php echo date('M d', strtotime($booking['check_out'])); ?>
                        </div>
                    </td>
                    <td style="padding: 1.5rem;">
                        <?php 
                        $status_colors = [
                            'pending' => ['#f59e0b', 'rgba(245, 158, 11, 0.1)'],
                            'approved' => ['#10b981', 'rgba(16, 185, 129, 0.2)'],
                            'rejected' => ['#ef4444', 'rgba(239, 68, 68, 0.1)'],
                            'finished' => ['#94a3b8', 'rgba(148, 163, 184, 0.1)'],
                            'cancelled' => ['#ef4444', 'rgba(239, 68, 68, 0.05)']
                        ];
                        $color = $status_colors[$booking['status']] ?? ['#fff', 'rgba(255,255,255,0.1)'];
                        ?>
                        <form method="POST" style="margin: 0;">
                            <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                            <input type="hidden" name="action" value="update_status">
                            <select name="status" onchange="this.form.submit()" style="padding: 0.4rem 0.75rem; border-radius: 8px; font-size: 0.75rem; font-weight: 700; background: <?php echo $color[1]; ?>; color: <?php echo $color[0]; ?>; border: 1px solid <?php echo $color[0]; ?>33; cursor: pointer; text-transform: uppercase; letter-spacing: 0.02em;">
                                <?php foreach($status_colors as $status => $colors): ?>
                                    <option value="<?php echo $status; ?>" <?php echo $booking['status'] == $status ? 'selected' : ''; ?>>
                                        <?php echo ucfirst($status); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    </td>
                    <td style="padding: 1.5rem;">
                        <div style="display: flex; gap: 0.5rem;">
                            <?php if($booking['status'] != 'cancelled' && $booking['status'] != 'finished'): ?>
                                <button class="btn" style="padding: 0.5rem; width: 36px; height: 36px; background: rgba(59, 130, 246, 0.1); color: var(--primary);"><i class="fas fa-eye"></i></button>
                                <form method="POST" style="margin: 0;" onsubmit="return confirm('Cancel this reservation?')">
                                    <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                    <button type="submit" name="action" value="cancel" class="btn" style="padding: 0.5rem; width: 36px; height: 36px; background: rgba(239, 68, 68, 0.1); color: #ef4444;"><i class="fas fa-times"></i></button>
                                </form>
                            <?php else: ?>
                                <span style="color: var(--text-muted); font-size: 0.75rem; font-weight: 500; font-style: italic;">No actions available</span>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if(count($bookings) == 0): ?>
                    <tr>
                        <td colspan="5" style="padding: 6rem; text-align: center;">
                            <i class="fas fa-calendar-times" style="font-size: 3rem; color: var(--text-muted); margin-bottom: 1.5rem;"></i>
                            <h3 style="color: white; margin-bottom: 0.5rem;">No Bookings Yet</h3>
                            <p style="color: var(--text-muted);">When guests book your properties, they will appear here.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</div> <!-- Close Topbar Content Spacer -->
<?php include '../includes/footer.php'; ?>
