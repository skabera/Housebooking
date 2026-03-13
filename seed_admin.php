<?php
include 'config/database.php';

$admin_email = 'admin@example.com';
$admin_pass = password_hash('admin123', PASSWORD_DEFAULT);
$admin_name = 'System Admin';

try {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$admin_email]);
    if (!$stmt->fetch()) {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'admin')");
        $stmt->execute([$admin_name, $admin_email, $admin_pass]);
        echo "Admin user created successfully!<br>";
        echo "Email: admin@example.com<br>";
        echo "Password: admin123<br>";
    } else {
        echo "Admin user already exists.";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
<br><a href="index.php">Go to Home Page</a>
