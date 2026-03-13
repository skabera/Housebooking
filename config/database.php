<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'house_booking_system';

try {
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $db");
    $pdo->exec("USE $db");

    // --- AUTOMATIC STATUS UPDATES ---
    $today = date('Y-m-d');
    
    // 1. Mark 'approved' bookings as 'finished' if check_out date has passed
    $pdo->prepare("UPDATE bookings SET status = 'finished' WHERE status = 'approved' AND check_out < ?")->execute([$today]);
    
    // 2. Refresh house availability: 
    // Set 'booked' houses to 'available' IF they don't have an active 'approved' booking today
    $pdo->query("
        UPDATE houses h 
        SET h.status = 'available' 
        WHERE h.status = 'booked' 
        AND NOT EXISTS (
            SELECT 1 FROM bookings b 
            WHERE b.house_id = h.id 
            AND b.status = 'approved' 
            AND b.check_in <= '$today' 
            AND b.check_out >= '$today'
        )
    ");
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

/**
 * Pagination Helper
 */
function get_pagination_data($pdo, $table, $where = "1", $params = [], $limit = 6) {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($page < 1) $page = 1;
    $offset = ($page - 1) * $limit;

    $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM $table WHERE $where");
    $count_stmt->execute($params);
    $total_items = $count_stmt->fetchColumn();
    $total_pages = ceil($total_items / $limit);

    return [
        'limit' => $limit,
        'offset' => $offset,
        'current_page' => $page,
        'total_pages' => $total_pages,
        'total_items' => $total_items
    ];
}

function render_pagination($current_page, $total_pages) {
    if ($total_pages <= 1) return;
    
    echo '<div class="pagination" style="display: flex; justify-content: center; gap: 0.5rem; margin-top: 3rem;">';
    
    // Previous
    if ($current_page > 1) {
        $prev = $current_page - 1;
        echo "<a href=\"?page=$prev\" class=\"glass\" style=\"width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 8px; border: 1px solid var(--glass-border); color: white; text-decoration: none;\">&larr;</a>";
    }

    // Pages
    for ($i = 1; $i <= $total_pages; $i++) {
        $active_style = ($i == $current_page) ? 'background: var(--primary); border-color: var(--primary);' : 'border: 1px solid var(--glass-border);';
        echo "<a href=\"?page=$i\" class=\"glass\" style=\"width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 8px; text-decoration: none; color: white; $active_style\">$i</a>";
    }

    // Next
    if ($current_page < $total_pages) {
        $next = $current_page + 1;
        echo "<a href=\"?page=$next\" class=\"glass\" style=\"width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 8px; border: 1px solid var(--glass-border); color: white; text-decoration: none;\">&rarr;</a>";
    }
    
    echo '</div>';
}
?>
