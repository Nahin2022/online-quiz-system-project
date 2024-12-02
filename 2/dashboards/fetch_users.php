<?php
require_once 'db_connect.php';
session_start();

// Check if the user is logged in as admin
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'teacher')) {
    echo "Access denied. Admins or Teachers only.";
    exit;
}


// Fetch students from the `users` table
$query = "SELECT id, username, email, is_blocked FROM users WHERE role = 'student'";
$stmt = $pdo->prepare($query);

try {
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching students: " . $e->getMessage());
    echo "Unable to load student data. Please try again later.";
    exit;
}

// Handle block/unblock request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id']) && isset($_POST['csrf_token'])) {
    // Validate CSRF token
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        echo "Invalid request. Please refresh the page and try again.";
        exit;
    }

    $user_id = filter_var($_POST['user_id'], FILTER_VALIDATE_INT);
    $is_blocked = filter_var($_POST['is_blocked'], FILTER_VALIDATE_INT);

    if ($user_id !== false && $is_blocked !== false) {
        $new_status = $is_blocked == 1 ? 0 : 1; // Toggle status

        try {
            $update_query = "UPDATE users SET is_blocked = :is_blocked WHERE id = :user_id AND role = 'student'";
            $update_stmt = $pdo->prepare($update_query);
            $update_stmt->bindParam(':is_blocked', $new_status, PDO::PARAM_INT);
            $update_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $update_stmt->execute();

            $_SESSION['success'] = "User status updated successfully.";
        } catch (PDOException $e) {
            error_log("Error updating user status: " . $e->getMessage());
            $_SESSION['error'] = "Failed to update user status. Please try again.";
        }
    } else {
        $_SESSION['error'] = "Invalid input.";
    }

    header("Location: dashboard.html");
    exit;
}

// Generate CSRF token for forms
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students</title>
    <style>
        table {
            width: 80%;
            border-collapse: collapse;
            margin: 20px auto;
        }
        table, th, td {
            border: 1px solid #000;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        button {
            padding: 5px 10px;
            cursor: pointer;
        }
        .message {
            text-align: center;
            margin: 20px;
        }
        .success {
            color: green;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <h1 style="text-align: center;">Manage Students</h1>

    <!-- Success/Error Message -->
    <div class="message">
        <?php if (isset($_SESSION['success'])): ?>
            <p class="success"><?= htmlspecialchars($_SESSION['success']) ?></p>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <p class="error"><?= htmlspecialchars($_SESSION['error']) ?></p>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    </div>

    <!-- Students Table -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Blocked Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($students)): ?>
                <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?= htmlspecialchars($student['id']) ?></td>
                        <td><?= htmlspecialchars($student['username']) ?></td>
                        <td><?= htmlspecialchars($student['email']) ?></td>
                        <td><?= $student['is_blocked'] == 1 ? 'Blocked' : 'Active' ?></td>
                        <td>
                            <form action="fetch_users.php" method="POST" style="display: inline;">
                                <input type="hidden" name="user_id" value="<?= htmlspecialchars($student['id']) ?>">
                                <input type="hidden" name="is_blocked" value="<?= htmlspecialchars($student['is_blocked']) ?>">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                <button type="submit"><?= $student['is_blocked'] == 1 ? 'Unblock' : 'Block' ?></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No students found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
