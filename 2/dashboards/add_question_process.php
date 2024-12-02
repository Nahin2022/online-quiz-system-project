<?php
require_once 'db_connect.php';
session_start();

if (!isset($_SESSION['teacher_id'])) {
    echo "Teacher ID not set. Please log in again.";
    exit;
}

$teacher_id = $_SESSION['teacher_id']; // Ensure this is set during login.

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $topic_id = $_POST['topic_id'];
    $question_text = $_POST['question_text'];
    $option_a = $_POST['option_a'];
    $option_b = $_POST['option_b'];
    $option_c = !empty($_POST['option_c']) ? $_POST['option_c'] : null;
    $option_d = !empty($_POST['option_d']) ? $_POST['option_d'] : null;
    $option_e = !empty($_POST['option_e']) ? $_POST['option_e'] : null;
    $correct_option = $_POST['correct_option'];

    // Check the question limit for the selected topic
    $query = "SELECT total_questions FROM topics WHERE id = :topic_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':topic_id', $topic_id, PDO::PARAM_INT);
    $stmt->execute();
    $topic_info = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$topic_info) {
        echo "Invalid topic or access restricted.";
        exit;
    }

    $total_questions = $topic_info['total_questions'];

    // Get the current number of questions for this topic
    $query = "SELECT COUNT(*) FROM questions WHERE topic_id = :topic_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':topic_id', $topic_id, PDO::PARAM_INT);
    $stmt->execute();
    $current_question_count = $stmt->fetchColumn();

    if ($current_question_count >= $total_questions) {
        echo "You have reached the maximum number of questions for this topic. Please delete or modify existing questions.";
        exit;
    }

    try {
        // Insert the new question into the database
        $query = "INSERT INTO questions (topic_id, question_text, option_a, option_b, option_c, option_d, option_e, correct_option) 
                  VALUES (:topic_id, :question_text, :option_a, :option_b, :option_c, :option_d, :option_e, :correct_option)";

        $stmt = $pdo->prepare($query);

        // Bind the parameters
        $stmt->bindParam(':topic_id', $topic_id, PDO::PARAM_INT);
        $stmt->bindParam(':question_text', $question_text, PDO::PARAM_STR);
        $stmt->bindParam(':option_a', $option_a, PDO::PARAM_STR);
        $stmt->bindParam(':option_b', $option_b, PDO::PARAM_STR);
        $stmt->bindParam(':option_c', $option_c, PDO::PARAM_NULL | PDO::PARAM_STR);
        $stmt->bindParam(':option_d', $option_d, PDO::PARAM_NULL | PDO::PARAM_STR);
        $stmt->bindParam(':option_e', $option_e, PDO::PARAM_NULL | PDO::PARAM_STR);
        $stmt->bindParam(':correct_option', $correct_option, PDO::PARAM_STR);

        if ($stmt->execute()) {
            header("Location: dashboard.html"); // Redirect to the dashboard or questions list
        } else {
            echo "Error: " . implode(", ", $stmt->errorInfo());
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
