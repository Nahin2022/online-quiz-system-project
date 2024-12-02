<?php
include 'db_connect.php'; // This file sets up the $pdo connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Fetch and sanitize input data
    $id = $_POST['id'];
    $topic_name = $_POST['topic_name'];
    $total_questions = $_POST['total_questions'];
    $marks_per_question = $_POST['marks_per_question'];
    $negative_marks = $_POST['negative_marks'];

    try {
        // Update query
        $query = "UPDATE topics 
                  SET topic_name = :topic_name, 
                      total_questions = :total_questions, 
                      marks_per_question = :marks_per_question, 
                      negative_marks = :negative_marks 
                  WHERE id = :id";

        // Use $pdo to prepare the statement
        $stmt = $pdo->prepare($query);

        // Bind parameters
        $stmt->bindParam(':topic_name', $topic_name);
        $stmt->bindParam(':total_questions', $total_questions, PDO::PARAM_INT);
        $stmt->bindParam(':marks_per_question', $marks_per_question, PDO::PARAM_INT);
        $stmt->bindParam(':negative_marks', $negative_marks, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Execute the query
        $stmt->execute();

        // Redirect to the dashboard after a successful update
        header('Location: dashboard.html');
        exit;
    } catch (PDOException $e) {
        // Handle errors
        die("Error: " . $e->getMessage());
    }
} else {
    die("Invalid request method.");
}
?>
