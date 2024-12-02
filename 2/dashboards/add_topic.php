<?php
include 'db_connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $topic_name = $_POST['topic_name'];
    $total_questions = $_POST['total_questions'];
    $marks_per_question = $_POST['marks_per_question'];
    $negative_marks = $_POST['negative_marks'];
    $teacher_id = $_SESSION['user_id']; // Assuming this is set when the teacher logs in

    // Check if teacher_id exists
    if (!isset($teacher_id)) {
        echo "Error: Teacher ID is missing. Please log in again.";
        exit();
    }

    try {
        // Prepare the SQL query
        $query = "INSERT INTO topics (topic_name, total_questions, marks_per_question, negative_marks, teacher_id) 
                  VALUES (:topic_name, :total_questions, :marks_per_question, :negative_marks, :teacher_id)";
        
        // Prepare statement
        $stmt = $pdo->prepare($query);
        
        // Bind parameters to the SQL query
        $stmt->bindParam(':topic_name', $topic_name);
        $stmt->bindParam(':total_questions', $total_questions);
        $stmt->bindParam(':marks_per_question', $marks_per_question);
        $stmt->bindParam(':negative_marks', $negative_marks);
        $stmt->bindParam(':teacher_id', $teacher_id);
        
        // Execute the statement
        if ($stmt->execute()) {
            header('Location: dashboard.html'); // Redirect to the Topics section
            exit();
        } else {
            echo "Error: Could not add the topic.";
            header('Location: dashboard.html'); // Redirect to the Topics section
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage(); // Display detailed error message
    }
}
?>
