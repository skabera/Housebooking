<?php
$base_path = '/booking';
$current_page_name = basename($_SERVER['PHP_SELF'], ".php");
$page_title = ($current_page_name == 'index') ? 'Dashboard' : ucfirst(str_replace('_', ' ', $current_page_name));
?>

<div class="topbar" style="height: 90px; position: fixed; right: 0; left: 280px; top: 0; padding: 0 3rem; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--glass-border); z-index: 900; background: rgba(2, 6, 23, 0.7); backdrop-filter: blur(20px);">
    <div>
        <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: 500; margin-bottom: 4px;">Welcome back, <?php echo isset($_SESSION['user_name']) ? explode(' ', $_SESSION['user_name'])[0] : 'Guest'; ?>!</div>
        <h2 style="font-size: 1.5rem; font-weight: 800; color: white; letter-spacing: -0.02em;"><?php echo $page_title; ?></h2>
    </div>

    <div style="display: flex; align-items: center; gap: 2rem;">
        <div style="position: relative; max-width: 300px;">
            <i class="fas fa-search" style="position: absolute; left: 1.25rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.9rem;"></i>
            <input type="text" placeholder="Search..." class="form-control" style="width: 100%; padding-left: 3.2rem; border-radius: 14px; height: 44px; background: rgba(255,255,255,0.03); border-color: rgba(255,255,255,0.1); color: white;">
        </div>
        
        <div style="display: flex; gap: 1rem;">
            <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; color: var(--text-muted); cursor: pointer;">
                <i class="fas fa-bell"></i>
            </div>
            <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; color: var(--text-muted); cursor: pointer;">
                <i class="fas fa-cog"></i>
            </div>
        </div>
    </div>
</div>

<div style="margin-left: 280px; padding-top: 90px; min-height: 100vh;"> <!-- Content Spacer -->
    <div style="padding: 2.5rem 3rem;">
