<?php
session_start(); // Start the session to store user information

include 'db_connect.php'; // Include your database connection (PDO connection)

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['pswd'];

    try {
        // Query to fetch the user data from the users table based on email
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(1, $email, PDO::PARAM_STR);
        $stmt->execute();

        // Check if a user with the given email exists
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify password
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['user_id'] = $user['id'];

                // If the user is a teacher, fetch the teacher_id from the topics table
                if ($user['role'] === 'teacher') {
                    // Query to get teacher_id from the topics table
                    $teacherQuery = "SELECT teacher_id FROM topics WHERE teacher_id = :user_id LIMIT 1";
                    $teacherStmt = $pdo->prepare($teacherQuery);
                    $teacherStmt->bindParam(':user_id', $user['id'], PDO::PARAM_INT);
                    $teacherStmt->execute();

                    // Check if the teacher exists in the topics table
                    if ($teacherStmt->rowCount() > 0) {
                        $teacher = $teacherStmt->fetch(PDO::FETCH_ASSOC);
                        $_SESSION['teacher_id'] = $teacher['teacher_id']; // Store teacher_id in session
                    }
                }

                // Redirect based on role
                switch ($user['role']) {
                    case 'teacher':
                        header("Location: dashboards/dashboard.html");
                        break;
                    case 'student':
                        header("Location: dashboards/s_dashboard.php");
                        break;
                    case 'admin':
                        header("Location: dashboards/a_dashboard.php");
                        break;
                    default:
                        echo "Invalid role.";
                }
                exit; // Stop further script execution
            } else {
                // Invalid login credentials
                echo "<script>alert('Invalid email or password!');</script>";
                echo "<script>window.location.href = 'index.html';</script>";
            }
        } else {
            // No user found with the provided email
            echo "<script>alert('No user found with this email!');</script>";
            echo "<script>window.location.href = 'index.html';</script>";
        }

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
