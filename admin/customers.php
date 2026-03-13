<?php
include '../config/database.php';
include '../includes/metadata.php';
include '../includes/components/sidebar.php';
include '../includes/components/topbar.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../user/login.php");
    exit();
}

// Fetch all customers (users) and their booking counts
$stmt = $pdo->query("
    SELECT u.*, COUNT(b.id) as booking_count 
    FROM users u 
    LEFT JOIN bookings b ON u.id = b.user_id 
    WHERE u.role = 'user' 
    GROUP BY u.id
    ORDER BY u.created_at DESC
");
$customers = $stmt->fetchAll();
?>

<div style="margin-bottom: 3rem; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h2 style="font-size: 1.75rem; font-weight: 800; color: white; margin-bottom: 0.5rem;">Customer Directory</h2>
        <p style="color: var(--text-muted); font-weight: 500;">Manage your registered guests and view their activity.</p>
    </div>
    <div style="display: flex; gap: 1rem;">
        <button class="btn" style="background: rgba(255,255,255,0.05); color: white;"><i class="fas fa-file-export"></i> Export CSV</button>
    </div>
</div>

<div class="chart-container" style="padding: 0; overflow: hidden; border-radius: 20px;">
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; min-width: 800px;">
            <thead>
                <tr style="background: rgba(255, 255, 255, 0.02); border-bottom: 1px solid var(--glass-border);">
                    <th style="padding: 1.25rem 1.5rem; color: var(--text-muted); font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Customer</th>
                    <th style="padding: 1.25rem 1.5rem; color: var(--text-muted); font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Email Address</th>
                    <th style="padding: 1.25rem 1.5rem; color: var(--text-muted); font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Join Date</th>
                    <th style="padding: 1.25rem 1.5rem; color: var(--text-muted); font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Total Bookings</th>
                    <th style="padding: 1.25rem 1.5rem; color: var(--text-muted); font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($customers as $customer): ?>
                <tr style="border-bottom: 1px solid var(--glass-border); transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.01)'" onmouseout="this.style.background='transparent'">
                    <td style="padding: 1.5rem;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(59, 130, 246, 0.1); display: flex; align-items: center; justify-content: center; color: var(--primary); font-weight: 800; font-size: 1rem; border: 1px solid rgba(59, 130, 246, 0.2);">
                                <?php echo strtoupper(substr($customer['name'], 0, 1)); ?>
                            </div>
                            <div style="font-weight: 700; color: white;"><?php echo $customer['name']; ?></div>
                        </div>
                    </td>
                    <td style="padding: 1.5rem; color: var(--text-muted);"><?php echo $customer['email']; ?></td>
                    <td style="padding: 1.5rem; color: var(--text-muted);">
                        <?php echo date('M d, Y', strtotime($customer['created_at'])); ?>
                    </td>
                    <td style="padding: 1.5rem;">
                        <span style="display: inline-flex; align-items: center; justify-content: center; min-width: 32px; height: 32px; padding: 0 8px; background: rgba(255, 255, 255, 0.05); border-radius: 8px; color: white; font-weight: 700; font-size: 0.85rem;">
                            <?php echo $customer['booking_count']; ?>
                        </span>
                    </td>
                    <td style="padding: 1.5rem;">
                        <span style="padding: 6px 12px; border-radius: 8px; background: rgba(16, 185, 129, 0.1); color: #10b981; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.02em;">
                            Active
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if(count($customers) == 0): ?>
                    <tr>
                        <td colspan="5" style="padding: 6rem; text-align: center;">
                            <i class="fas fa-user-slash" style="font-size: 3rem; color: var(--text-muted); margin-bottom: 1.5rem;"></i>
                            <h3 style="color: white; margin-bottom: 0.5rem;">No Customers Found</h3>
                            <p style="color: var(--text-muted);">When new users register, they will appear in this directory.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</div> <!-- Close Topbar Content Spacer -->
<?php include '../includes/footer.php'; ?>
