<?php
include '../config/database.php';
include '../includes/metadata.php';

$message = "";
if(isset($_GET['msg'])) $message = $_GET['msg'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['role'] = $user['role'];
        
        if($user['role'] == 'admin') {
            header("Location: ../admin/dashboard.php");
        } else {
            header("Location: ../index.php");
        }
        exit();
    } else {
        $message = "Invalid email or password.";
    }
}
?>

<div class="auth-wrapper">
    <div class="auth-split">
        <!-- Left Pane: Branding & Info -->
        <div class="auth-left">
            <div>
                <div class="auth-icon-box">
                    <i class="fas fa-hotel"></i>
                </div>
                <h1 style="font-size: 2.5rem; font-weight: 800; margin-bottom: 1.5rem; letter-spacing: -0.02em;">LuxeAbodes</h1>
                <p style="font-size: 1.1rem; line-height: 1.7; color: rgba(255,255,255,0.8); max-width: 400px;">
                    Experience luxury like never before. Access your curated selection of premium stays and manage your bookings in one seamless dashboard.
                </p>
            </div>
            
            <div>
                <div style="font-size: 0.9rem; font-weight: 600; color: rgba(255,255,255,0.6); margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.05em;">Secure Access</div>
                <p style="font-size: 0.85rem; color: rgba(255,255,255,0.7);">Your data is protected with enterprise-grade security and encryption.</p>
            </div>
        </div>

        <!-- Right Pane: Form -->
        <div class="auth-right">
            <div style="max-width: 400px; width: 100%; margin: 0 auto;">
                <h2 style="font-size: 2rem; font-weight: 800; margin-bottom: 0.5rem; color: #1e293b;">Sign In</h2>
                <p style="color: #64748b; margin-bottom: 2.5rem; font-weight: 500;">Welcome back! Please enter your details.</p>

                <?php if($message): ?>
                    <div style="padding: 1rem; background: rgba(239, 68, 68, 0.05); border: 1px solid rgba(239, 68, 68, 0.1); border-radius: 12px; margin-bottom: 1.5rem; color: #ef4444; font-size: 0.9rem; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="auth-form-group">
                        <label><i class="fas fa-envelope" style="font-size: 0.8rem;"></i> Email Address</label>
                        <input type="email" name="email" class="auth-input" required placeholder="name@company.com">
                    </div>

                    <div class="auth-form-group">
                        <label><i class="fas fa-lock" style="font-size: 0.8rem;"></i> Password</label>
                        <input type="password" name="password" class="auth-input" required placeholder="••••••••">
                    </div>

                    <div style="display: flex; justify-content: flex-end; margin-bottom: 2rem;">
                        <a href="#" style="font-size: 0.85rem; color: #3b82f6; text-decoration: none; font-weight: 700;">Forgot Password?</a>
                    </div>

                    <button type="submit" class="auth-btn">
                        Sign In <i class="fas fa-arrow-right" style="font-size: 0.8rem;"></i>
                    </button>
                </form>

                <p style="margin-top: 2rem; text-align: center; color: #64748b; font-size: 0.9rem;">
                    Don't have an account? <a href="register.php" style="color: #3b82f6; text-decoration: none; font-weight: 700;">Create account</a>
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    /* Inline styles for safety in case global CSS update fails */
    .auth-wrapper { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #020617; padding: 2rem; }
    .auth-split { display: flex; width: 100%; max-width: 1100px; min-height: 650px; background: white; border-radius: 32px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); }
    .auth-left { flex: 1; background: linear-gradient(135deg, #3b82f6, #6366f1); padding: 4rem; color: white; display: flex; flex-direction: column; justify-content: space-between; }
    .auth-right { flex: 1.2; padding: 4.5rem; background: #ffffff; display: flex; flex-direction: column; justify-content: center; color: #1e293b; }
    .auth-icon-box { width: 60px; height: 60px; background: rgba(255, 255, 255, 0.2); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; margin-bottom: 2rem; backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.3); }
    .auth-form-group { margin-bottom: 1.5rem; }
    .auth-form-group label { display: block; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; color: #475569; display: flex; align-items: center; gap: 8px; }
    .auth-input { width: 100%; padding: 1rem 1.25rem; border: 1px solid #e2e8f0; border-radius: 12px; background: #f8fafc; font-size: 1rem; color: #1e293b; transition: all 0.3s; }
    .auth-input:focus { outline: none; border-color: #3b82f6; background: white; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); }
    .auth-btn { width: 100%; padding: 1rem; background: #3b82f6; color: white; border: none; border-radius: 12px; font-size: 1rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px; transition: all 0.3s; margin-top: 1rem; }
    .auth-btn:hover { background: #2563eb; transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3); }
    @media (max-width: 900px) { .auth-left { display: none; } .auth-split { max-width: 500px; } }
</style>

</body>
</html>
