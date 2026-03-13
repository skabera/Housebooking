<nav class="glass">
    <a href="/booking/index.php" class="logo">LUXE ABODES</a>
    <ul>
        <li><a href="/booking/index.php">Home</a></li>
        <li><a href="/booking/pages/houses.php">Houses</a></li>
        <?php if(isset($_SESSION['user_id'])): ?>
            <?php if($_SESSION['role'] == 'admin'): ?>
                <li><a href="/booking/admin/dashboard.php">Dashboard</a></li>
            <?php else: ?>
                <li><a href="/booking/user/my_bookings.php">My Bookings</a></li>
            <?php endif; ?>
            <li><a href="/booking/logout.php" class="btn btn-primary">Logout</a></li>
        <?php else: ?>
            <li><a href="/booking/user/login.php">Login</a></li>
            <li><a href="/booking/user/register.php" class="btn btn-primary">Register</a></li>
        <?php endif; ?>
    </ul>
</nav>
