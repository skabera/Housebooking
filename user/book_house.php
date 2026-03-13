<?php
include '../config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?msg=Please login to book a house.");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] != 'POST' || !isset($_POST['house_id'])) {
    header("Location: ../pages/houses.php");
    exit();
}

$house_id = $_POST['house_id'];
$user_id = $_SESSION['user_id'];
$check_in = $_POST['check_in'];
$check_out = $_POST['check_out'];

// Basic date validation
if (strtotime($check_in) >= strtotime($check_out) || strtotime($check_in) < strtotime(date('Y-m-d'))) {
    header("Location: ../pages/house_details.php?id=$house_id&msg=Invalid date range selected.");
    exit();
}

try {
    // Check if house is still available
    $stmt = $pdo->prepare("SELECT status FROM houses WHERE id = ?");
    $stmt->execute([$house_id]);
    $house = $stmt->fetch();

    if ($house && $house['status'] == 'available') {
        // Create booking with 'pending' status (default in DB)
        $stmt = $pdo->prepare("INSERT INTO bookings (house_id, user_id, check_in, check_out, status) VALUES (?, ?, ?, ?, 'pending')");
        $stmt->execute([$house_id, $user_id, $check_in, $check_out]);

        header("Location: my_bookings.php?msg=Booking request sent! Waiting for admin approval.");
    } else {
        header("Location: ../pages/houses.php?msg=House is no longer available.");
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
