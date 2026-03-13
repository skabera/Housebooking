<?php
include '../config/database.php';
include '../includes/metadata.php';

// Authentication Guard
if (!isset($_SESSION['user_id'])) {
    header("Location: ../user/login.php");
    exit();
}

include '../includes/components/sidebar.php';
include '../includes/components/topbar.php';

if(!isset($_GET['id'])) {
    header("Location: houses.php");
    exit();
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM houses WHERE id = ?");
$stmt->execute([$id]);
$house = $stmt->fetch();

if(!$house) {
    header("Location: houses.php");
    exit();
}
?>

<div class="container">
    <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 3rem; margin-top: 2rem;">
        <div>
            <img src="/booking/uploads/houses/<?php echo $house['image']; ?>" style="width: 100%; border-radius: 20px; box-shadow: var(--glass-shadow);" alt="<?php echo $house['title']; ?>">
            <div style="margin-top: 3rem;">
                <h1 style="font-size: 3rem; font-weight: 800; margin-bottom: 0.75rem; letter-spacing: -0.02em;"><?php echo $house['title']; ?></h1>
                <div style="display: flex; align-items: center; gap: 8px; color: var(--primary); font-weight: 700; font-size: 1.1rem; margin-bottom: 2.5rem;">
                    <i class="fas fa-map-marker-alt"></i> <?php echo $house['location']; ?>
                </div>
                <div class="glass" style="padding: 2.5rem; border-radius: 24px; border: 1px solid rgba(255,255,255,0.05); background: rgba(30, 41, 59, 0.2);">
                    <h3 style="margin-bottom: 1.5rem; font-size: 1.4rem; font-weight: 700;">About this retreat</h3>
                    <p style="color: var(--text-muted); white-space: pre-line; line-height: 1.8; font-size: 1.05rem;"><?php echo $house['description']; ?></p>
                </div>
            </div>
        </div>

        <div>
            <div class="glass" style="padding: 2rem; position: sticky; top: 120px;">
                <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 1.5rem;">
                    <p style="font-size: 1.8rem; font-weight: 800; color: var(--primary);">$<?php echo $house['price']; ?></p>
                    <p style="color: var(--text-muted);">/ night</p>
                </div>
                
                <div style="margin-bottom: 2rem; border-top: 1px solid var(--glass-border); padding-top: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
                    <span style="color: var(--text-muted); font-size: 0.95rem; font-weight: 500;">Current Status</span>
                    <span style="padding: 0.4rem 1rem; border-radius: 12px; font-size: 0.85rem; font-weight: 700; background: <?php echo $house['status'] == 'available' ? 'rgba(16, 185, 129, 0.1)' : 'rgba(239, 68, 68, 0.1)'; ?>; color: <?php echo $house['status'] == 'available' ? '#10b981' : '#f87171'; ?>; border: 1px solid currentColor;">
                        <i class="fas <?php echo $house['status'] == 'available' ? 'fa-check-circle' : 'fa-times-circle'; ?>" style="margin-right: 6px;"></i>
                        <?php echo $house['status'] == 'available' ? 'Available' : 'Booked'; ?>
                    </span>
                </div>

                <?php if($house['status'] == 'available'): ?>
                    <form action="/booking/user/book_house.php" method="POST">
                        <input type="hidden" name="house_id" value="<?php echo $house['id']; ?>">
                        <div style="margin-bottom: 1.25rem;">
                            <label style="display: block; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.6rem; font-weight: 600;">Check-in Date</label>
                            <input type="date" name="check_in" required class="form-control" style="width: 100%;">
                        </div>
                        <div style="margin-bottom: 2rem;">
                            <label style="display: block; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.6rem; font-weight: 600;">Check-out Date</label>
                            <input type="date" name="check_out" required class="form-control" style="width: 100%;">
                        </div>
                        <button type="submit" class="btn btn-primary" style="width: 100%; border-radius: 18px; padding: 1.2rem;">
                            <i class="fas fa-calendar-alt"></i> Reserve This House
                        </button>
                    </form>
                <?php else: ?>
                    <button class="btn glass" style="width: 100%; cursor: not-allowed; opacity: 0.6; border-color: rgba(255,255,255,0.05); border-radius: 18px; padding: 1.2rem;" disabled>
                        <i class="fas fa-lock"></i> Currently Booked
                    </button>
                <?php endif; ?>

                <p style="margin-top: 1.5rem; text-align: center; color: var(--text-muted); font-size: 0.85rem;">You won't be charged yet</p>
            </div>
        </div>
    </div>
</div>

</div> <!-- Close Topbar Content Spacer -->
<?php include '../includes/footer.php'; ?>
