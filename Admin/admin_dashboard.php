<?php
session_start();
include "../config/db_connect.php";
include "../config/session_cleanup.php";


if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

cleanupSession();


try {
    $stats = array();
    
    
    $query = "SELECT COUNT(*) as total_users FROM users";
    $result = $conn->query($query);
    $stats['users'] = $result->fetch_assoc()['total_users'];
    
    
    $query = "SELECT COUNT(*) as total_candidates FROM candidates";
    $result = $conn->query($query);
    $stats['candidates'] = $result->fetch_assoc()['total_candidates'];
    
    
    $query = "SELECT COUNT(*) as total_votes FROM votes";
    $result = $conn->query($query);
    $stats['votes'] = $result->fetch_assoc()['total_votes'];
    
} catch (Exception $e) {
    error_log($e->getMessage());
    $error = "Error loading dashboard statistics.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <header class="dashboard-header">
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></h2>
            <a href="admin_logout.php" class="logout-btn">Logout</a>
        </header>

        <?php if (isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="stats-container">
            <div class="stat-box">
                <h3>Total Users</h3>
                <p><?php echo isset($stats['users']) ? $stats['users'] : '0'; ?></p>
            </div>
            <div class="stat-box">
                <h3>Total Candidates</h3>
                <p><?php echo isset($stats['candidates']) ? $stats['candidates'] : '0'; ?></p>
            </div>
            <div class="stat-box">
                <h3>Total Votes</h3>
                <p><?php echo isset($stats['votes']) ? $stats['votes'] : '0'; ?></p>
            </div>
        </div>

        <div class="admin-menu">
            <a href="view_result.php" class="menu-item">
                <i class="icon-results"></i>
                View Results
            </a>
            <a href="manage_candidates.php" class="menu-item">
                <i class="icon-candidates"></i>
                Manage Candidates
            </a>
            <a href="view_users.php" class="menu-item">
                <i class="icon-users"></i>
                View Users
            </a>
        </div>
    </div>
</body>
</html>