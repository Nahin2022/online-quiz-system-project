<?php
require_once 'db_connect.php';
session_start();

if (!isset($_SESSION['teacher_id'])) {
    echo "Teacher ID not set. Please log in again.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $question_id = $_POST['question_id'];
    $question_text = $_POST['question_text'];
    $option_a = $_POST['option_a'];
    $option_b = $_POST['option_b'];
    $option_c = !empty($_POST['option_c']) ? $_POST['option_c'] : null; // Handle null values
    $option_d = !empty($_POST['option_d']) ? $_POST['option_d'] : null; // Handle null values
    $option_e = !empty($_POST['option_e']) ? $_POST['option_e'] : null; // Handle null values
    $correct_option = $_POST['correct_option'];

    try {
        // Prepare the SQL query to update the question
        $query = "UPDATE questions 
                  SET question_text = :question_text, 
                      option_a = :option_a, 
                      option_b = :option_b, 
                      option_c = :option_c, 
                      option_d = :option_d, 
                      option_e = :option_e, 
                      correct_option = :correct_option 
                  WHERE id = :question_id";

        $stmt = $pdo->prepare($query);

        // Bind the parameters
        $stmt->bindParam(':question_id', $question_id, PDO::PARAM_INT);
        $stmt->bindParam(':question_text', $question_text, PDO::PARAM_STR);
        $stmt->bindParam(':option_a', $option_a, PDO::PARAM_STR);
        $stmt->bindParam(':option_b', $option_b, PDO::PARAM_STR);
        $stmt->bindParam(':option_c', $option_c, PDO::PARAM_STR);
        $stmt->bindParam(':option_d', $option_d, PDO::PARAM_STR);
        $stmt->bindParam(':option_e', $option_e, PDO::PARAM_STR);
        $stmt->bindParam(':correct_option', $correct_option, PDO::PARAM_STR);

        // Execute the statement
        if ($stmt->execute()) {
            header("Location: dashboard.html"); // Redirect to the dashboard or questions list
            exit;
        } else {
            $errorInfo = $stmt->errorInfo();
            echo "Error updating question: " . htmlspecialchars($errorInfo[2]);
        }
    } catch (PDOException $e) {
        echo "Error: " . htmlspecialchars($e->getMessage());
    }
} else {
    echo "Invalid request method.";
    exit;
}
