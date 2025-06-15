<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include "../config/db_connect.php";
include "../config/session_cleanup.php";


if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

cleanupSession();

try {
    $query = "SELECT c.name, c.position, COUNT(v.id) as vote_count 
              FROM candidates c 
              LEFT JOIN votes v ON c.id = v.candidate_id 
              GROUP BY c.id, c.name, c.position 
              ORDER BY c.position, vote_count DESC";
              
    if (!$result = $conn->query($query)) {
        throw new Exception("Query failed: " . $conn->error);
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    $error = "An error occurred while fetching results.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Results</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h2>Election Results</h2>
        <a href="admin_dashboard.php" class="back-link">Back to Dashboard</a>

        <?php if (isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Position</th>
                        <th>Candidate</th>
                        <th>Votes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['position']); ?></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo (int)$row['vote_count']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="no-results">No results found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>