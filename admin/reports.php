<?php
include '../config/database.php';
include '../includes/metadata.php';
include '../includes/components/sidebar.php';
include '../includes/components/topbar.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../user/login.php");
    exit();
}

// 1. Financial Summary
$finance_stmt = $pdo->query("
    SELECT 
        SUM(h.price * (DATEDIFF(b.check_out, b.check_in))) as total_revenue,
        COUNT(b.id) as total_bookings,
        AVG(h.price * (DATEDIFF(b.check_out, b.check_in))) as avg_value
    FROM bookings b 
    JOIN houses h ON b.house_id = h.id 
    WHERE b.status IN ('approved', 'finished')
");
$finance = $finance_stmt->fetch();

// 2. Booking Trend (Last 6 Months)
$trend_stmt = $pdo->query("
    SELECT DATE_FORMAT(booking_date, '%b %Y') as month_label, COUNT(*) as count 
    FROM bookings 
    WHERE status IN ('approved', 'finished') 
    GROUP BY month_label 
    ORDER BY MIN(booking_date) ASC 
    LIMIT 6
");
$trends = $trend_stmt->fetchAll();
$labels = [];
$counts = [];
foreach($trends as $t) {
    $labels[] = $t['month_label'];
    $counts[] = $t['count'];
}

// 3. Top Performing Properties
$top_properties = $pdo->query("
    SELECT h.title, h.location, COUNT(b.id) as bookings, SUM(h.price * DATEDIFF(b.check_out, b.check_in)) as revenue 
    FROM houses h 
    JOIN bookings b ON h.id = b.house_id 
    WHERE b.status IN ('approved', 'finished') 
    GROUP BY h.id 
    ORDER BY revenue DESC 
    LIMIT 5
")->fetchAll();
?>

<div style="margin-bottom: 3rem; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h2 style="font-size: 1.75rem; font-weight: 800; color: white; margin-bottom: 0.5rem;">System Analytics</h2>
        <p style="color: var(--text-muted); font-weight: 500;">Deep dive into financial performance and booking trends.</p>
    </div>
    <div style="display: flex; gap: 1rem;">
        <button class="btn btn-primary" onclick="window.print()"><i class="fas fa-print"></i> Print Report</button>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;"><i class="fas fa-coins"></i></div>
        <div>
            <div class="stat-value">$<?php echo number_format($finance['total_revenue'] ?: 0, 0); ?></div>
            <div class="stat-label">Gross Revenue</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(59, 130, 246, 0.1); color: var(--primary);"><i class="fas fa-chart-line"></i></div>
        <div>
            <div class="stat-value"><?php echo $finance['total_bookings'] ?: 0; ?></div>
            <div class="stat-label">Successful Bookings</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;"><i class="fas fa-award"></i></div>
        <div>
            <div class="stat-value">$<?php echo number_format($finance['avg_value'] ?: 0, 0); ?></div>
            <div class="stat-label">Avg. Booking Value</div>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; margin-bottom: 3rem;">
    <!-- Trend Chart -->
    <div class="chart-container">
        <h3 style="font-size: 1.2rem; font-weight: 700; color: white; margin-bottom: 2rem;">Booking Trends</h3>
        <div style="height: 300px;">
            <canvas id="trendChart"></canvas>
        </div>
    </div>

    <!-- Additional Stats -->
    <div class="chart-container" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(99, 102, 241, 0.1)); border: 1px solid rgba(59, 130, 246, 0.2);">
        <h3 style="font-size: 1.2rem; font-weight: 700; color: white; margin-bottom: 1.5rem;">Insights</h3>
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <div>
                <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 6px;">Peak Performance</p>
                <p style="color: white; font-weight: 700;">Summer Season '26</p>
            </div>
            <div style="padding-top: 1.5rem; border-top: 1px solid rgba(255,255,255,0.05);">
                <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 6px;">Top Location</p>
                <p style="color: white; font-weight: 700;">Beverly Hills, CA</p>
            </div>
        </div>
    </div>
</div>

<div class="chart-container" style="padding: 1.5rem 0;">
    <div style="padding: 0 1.5rem 1.5rem 1.5rem; border-bottom: 1px solid var(--glass-border);">
        <h3 style="font-size: 1.2rem; font-weight: 700; color: white;">Top Performing Properties</h3>
    </div>
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 1px solid var(--glass-border);">
                    <th style="padding: 1.25rem 1.5rem; color: var(--text-muted); font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Property</th>
                    <th style="padding: 1.25rem 1.5rem; color: var(--text-muted); font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Bookings</th>
                    <th style="padding: 1.25rem 1.5rem; color: var(--text-muted); font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Revenue</th>
                    <th style="padding: 1.25rem 1.5rem; color: var(--text-muted); font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Efficiency</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($top_properties as $prop): ?>
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.03);">
                    <td style="padding: 1.25rem 1.5rem;">
                        <div style="font-weight: 700; color: white;"><?php echo $prop['title']; ?></div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);"><?php echo $prop['location']; ?></div>
                    </td>
                    <td style="padding: 1.25rem 1.5rem; color: white; font-weight: 600;"><?php echo $prop['bookings']; ?></td>
                    <td style="padding: 1.25rem 1.5rem; color: #10b981; font-weight: 700;">$<?php echo number_format($prop['revenue'], 0); ?></td>
                    <td style="padding: 1.25rem 1.5rem;">
                        <div style="width: 100%; height: 6px; background: rgba(255,255,255,0.05); border-radius: 10px; overflow: hidden;">
                            <div style="width: <?php echo min(100, ($prop['revenue'] / ($finance['total_revenue'] ?: 1)) * 500); ?>%; height: 100%; background: var(--primary);"></div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('trendChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($labels); ?>,
            datasets: [{
                label: 'Successful Bookings',
                data: <?php echo json_encode($counts); ?>,
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#3b82f6',
                pointBorderColor: 'rgba(255,255,255,0.2)',
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(255, 255, 255, 0.05)' },
                    ticks: { color: '#94a3b8' }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#94a3b8' }
                }
            }
        }
    });
});
</script>

</div> <!-- Close Topbar Content Spacer -->
<?php include '../includes/footer.php'; ?>
