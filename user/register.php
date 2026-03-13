<?php
include '../config/database.php';
include '../includes/metadata.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'user'; // Default role

    try {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$name, $email, $password, $role])) {
            header("Location: login.php?msg=Registration successful! Please login.");
            exit();
        }
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}
?>

<div class="auth-wrapper">
    <div class="auth-split" style="min-height: 700px;">
        <!-- Left Pane: Branding & Info -->
        <div class="auth-left">
            <div>
                <div class="auth-icon-box">
                    <i class="fas fa-key"></i>
                </div>
                <h1 style="font-size: 2.5rem; font-weight: 800; margin-bottom: 1.5rem; letter-spacing: -0.02em;">Join LuxeAbodes</h1>
                <p style="font-size: 1.1rem; line-height: 1.7; color: rgba(255,255,255,0.8); max-width: 400px;">
                    Create an account to start exploring our handpicked selection of premium houses and villas. Your luxury journey starts here.
                </p>
            </div>
            
            <div style="background: rgba(255,255,255,0.05); padding: 2rem; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1);">
                <div style="font-size: 1rem; font-weight: 700; margin-bottom: 0.5rem;"><i class="fas fa-star" style="color: #fbbf24; margin-right: 8px;"></i> Premium Benefits</div>
                <ul style="list-style: none; padding: 0; font-size: 0.9rem; color: rgba(255,255,255,0.7); display: flex; flex-direction: column; gap: 8px;">
                    <li><i class="fas fa-check" style="margin-right: 8px; font-size: 0.8rem;"></i> Exclusive property access</li>
                    <li><i class="fas fa-check" style="margin-right: 8px; font-size: 0.8rem;"></i> Fast & secure booking</li>
                    <li><i class="fas fa-check" style="margin-right: 8px; font-size: 0.8rem;"></i> Personalized recommendations</li>
                </ul>
            </div>
        </div>

        <!-- Right Pane: Form -->
        <div class="auth-right">
            <div style="max-width: 400px; width: 100%; margin: 0 auto;">
                <h2 style="font-size: 2rem; font-weight: 800; margin-bottom: 0.5rem; color: #1e293b;">Create Account</h2>
                <p style="color: #64748b; margin-bottom: 2.5rem; font-weight: 500;">Fill in your details to get started.</p>

                <?php if($message): ?>
                    <div style="padding: 1rem; background: rgba(239, 68, 68, 0.05); border: 1px solid rgba(239, 68, 68, 0.1); border-radius: 12px; margin-bottom: 1.5rem; color: #ef4444; font-size: 0.9rem; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="auth-form-group">
                        <label><i class="fas fa-user" style="font-size: 0.8rem;"></i> Full Name</label>
                        <input type="text" name="name" class="auth-input" required placeholder="John Doe">
                    </div>

                    <div class="auth-form-group">
                        <label><i class="fas fa-envelope" style="font-size: 0.8rem;"></i> Email Address</label>
                        <input type="email" name="email" class="auth-input" required placeholder="name@company.com">
                    </div>

                    <div class="auth-form-group">
                        <label><i class="fas fa-lock" style="font-size: 0.8rem;"></i> Password</label>
                        <input type="password" name="password" class="auth-input" required placeholder="••••••••">
                    </div>

                    <button type="submit" class="auth-btn" style="margin-top: 2rem;">
                        Create Account <i class="fas fa-user-plus" style="font-size: 0.8rem;"></i>
                    </button>
                </form>

                <p style="margin-top: 2rem; text-align: center; color: #64748b; font-size: 0.9rem;">
                    Already have an account? <a href="login.php" style="color: #3b82f6; text-decoration: none; font-weight: 700;">Sign in here</a>
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    /* Inline styles for consistency and reliability */
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
