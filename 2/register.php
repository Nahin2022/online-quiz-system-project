<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['txt'];
    $email = $_POST['email'];
    $password = password_hash($_POST['pswd'], PASSWORD_BCRYPT);
    $role = 'student';

    try {
        // Insert user into 'users' table
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $email, $password, $role]);
        $userId = $pdo->lastInsertId();

        // Check role and insert into 'students' table if role is 'student'
        if ($role == 'student') {
            $studentId = $_POST['studentId'];
            $university = $_POST['university'];
            $stmt = $pdo->prepare("INSERT INTO students (user_id, student_id, university) VALUES (?, ?, ?)");
            $stmt->execute([$userId, $studentId, $university]);
        }

        // Success message and redirect
        echo "<script>alert('Registration successful! Redirecting to login page...');</script>";
        echo "<script>setTimeout(function(){ window.location.href = 'index.html'; }, 500);</script>";
        
        exit;
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

