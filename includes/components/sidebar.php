<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$role = $_SESSION['role'] ?? 'guest';
$base_path = '/booking';
$current_page = $_SERVER['PHP_SELF'];

function isActive($path, $current_page) {
    return strpos($current_page, $path) !== false ? 'active' : '';
}
?>

<div class="sidebar" style="width: 280px; height: 100vh; position: fixed; left: 0; top: 0; padding: 2rem 1.5rem; display: flex; flex-direction: column; border-right: 1px solid var(--glass-border); z-index: 1000;">
    <!-- Logo Section -->
    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 3rem; padding-left: 0.5rem;">
        <div style="width: 48px; height: 48px; background: var(--primary); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem; box-shadow: 0 8px 16px rgba(59, 130, 246, 0.4);">
            <i class="fas fa-hotel"></i>
        </div>
        <div>
            <div style="font-size: 1.25rem; font-weight: 800; color: white; letter-spacing: -0.02em;">LuxeAbodes</div>
            <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em;">Booking System</div>
        </div>
    </div>

    <!-- Navigation Section -->
    <nav style="display: flex; flex-direction: column; gap: 0.5rem; flex-grow: 1;">
        <a href="<?php echo $base_path; ?>/index.php" class="nav-link <?php echo ($current_page == $base_path.'/index.php' || $current_page == '/index.php') ? 'active' : ''; ?>">
            <i class="fas fa-home"></i> Dashboard
        </a>
        
        <?php if ($role === 'admin'): ?>
            <a href="<?php echo $base_path; ?>/admin/manage_houses.php" class="nav-link <?php echo isActive('manage_houses.php', $current_page); ?>">
                <i class="fas fa-house-user"></i> Properties
            </a>
            <a href="<?php echo $base_path; ?>/admin/bookings.php" class="nav-link <?php echo isActive('bookings.php', $current_page); ?>">
                <i class="fas fa-calendar-check"></i> Bookings
            </a>
            <a href="<?php echo $base_path; ?>/admin/customers.php" class="nav-link <?php echo isActive('customers.php', $current_page); ?>">
                <i class="fas fa-users"></i> Customers
            </a>
            <a href="<?php echo $base_path; ?>/admin/reports.php" class="nav-link <?php echo isActive('reports.php', $current_page); ?>">
                <i class="fas fa-chart-pie"></i> Reports
            </a>
        <?php else: ?>
            <a href="<?php echo $base_path; ?>/pages/houses.php" class="nav-link <?php echo isActive('houses.php', $current_page); ?>">
                <i class="fas fa-search"></i> Browse Stays
            </a>
            <?php if ($role === 'user'): ?>
                <a href="<?php echo $base_path; ?>/user/my_bookings.php" class="nav-link <?php echo isActive('my_bookings.php', $current_page); ?>">
                    <i class="fas fa-suitcase"></i> My Trips
                </a>
            <?php endif; ?>
        <?php endif; ?>
    </nav>

    <!-- Profile Section -->
    <div style="padding-top: 1.5rem; border-top: 1px solid var(--glass-border); margin-top: auto;">
        <?php if (isset($_SESSION['user_id'])): ?>
            <div style="padding: 1rem; background: rgba(255, 255, 255, 0.03); border-radius: 16px; margin-bottom: 1rem; display: flex; align-items: center; gap: 12px; position: relative; border: 1px solid rgba(255,255,255,0.05);">
                <div style="width: 44px; height: 44px; border-radius: 12px; background: linear-gradient(135deg, var(--primary), #6366f1); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.1rem; border: 2px solid rgba(255,255,255,0.1);">
                    <?php echo strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)); ?>
                </div>
                <div style="flex-grow: 1; min-width: 0;">
                    <div style="font-size: 0.9rem; font-weight: 700; color: white; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></div>
                    <div style="display: flex; align-items: center; gap: 6px;">
                        <span style="font-size: 0.7rem; color: white; background: var(--primary); padding: 2px 8px; border-radius: 6px; font-weight: 700; text-transform: uppercase;"><?php echo htmlspecialchars($role); ?></span>
                    </div>
                </div>
                <i class="fas fa-chevron-right" style="font-size: 0.8rem; color: var(--text-muted);"></i>
            </div>
            <a href="<?php echo $base_path; ?>/logout.php" class="nav-link" style="color: #f87171;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 0.8rem;">
                <a href="<?php echo $base_path; ?>/user/login.php" class="btn btn-primary" style="width: 100%;">Login</a>
                <a href="<?php echo $base_path; ?>/user/register.php" class="btn" style="width: 100%; background: rgba(255,255,255,0.05); color: white;">Register</a>
            </div>
        <?php endif; ?>
    </div>
</div>
