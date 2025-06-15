<?php
session_start();
include "../config/session_cleanup.php";
include "../config/db_connect.php";


if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

cleanupSession();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['add_candidate'])) {
            $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
            $position = filter_var($_POST['position'], FILTER_SANITIZE_STRING);
            
            $query = "INSERT INTO candidates (name, position) VALUES (?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ss", $name, $position);
            $stmt->execute();
            
            header('Location: manage_candidates.php');
            exit();
        }
        
        if (isset($_POST['delete_candidate'])) {
            $id = filter_var($_POST['candidate_id'], FILTER_SANITIZE_NUMBER_INT);
            
            $query = "DELETE FROM candidates WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            
            header('Location: manage_candidates.php');
            exit();
        }
    } catch (Exception $e) {
        $error = "Operation failed: " . $e->getMessage();
    }
}


try {
    $query = "SELECT * FROM candidates ORDER BY position, name";
    $result = $conn->query($query);
} catch (Exception $e) {
    $error = "Failed to fetch candidates: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Candidates</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h2>Manage Candidates</h2>
        <a href="admin_dashboard.php" class="back-link">Back to Dashboard</a>

        <?php if (isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <!-- Add Candidate Form -->
        <form method="POST" class="add-form">
            <h3>Add New Candidate</h3>
            <div class="form-group">
                <label>Name:</label>
                <input type="text" name="name" required>
            </div>
            <div class="form-group">
                <label>Position:</label>
                <input type="text" name="position" required>
            </div>
            <button type="submit" name="add_candidate">Add Candidate</button>
        </form>

        <!-- Candidates List -->
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Position</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($result) && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['position']); ?></td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="candidate_id" 
                                           value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="delete_candidate" 
                                            onclick="return confirm('Are you sure?')"
                                            class="delete-btn">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="no-results">No candidates found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>