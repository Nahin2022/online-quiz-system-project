<?php
if (isset($_POST['create_topic'])) {
    $topic_name = filter_input(INPUT_POST, 'topic_name', FILTER_SANITIZE_STRING);
    $total_questions = filter_input(INPUT_POST, 'total_questions', FILTER_VALIDATE_INT);
    $marks_per_question = filter_input(INPUT_POST, 'marks_per_question', FILTER_VALIDATE_INT);
    $negative_marks = filter_input(INPUT_POST, 'negative_marks', FILTER_VALIDATE_INT);

    // Insert topic into the database
    $stmt = $pdo->prepare("INSERT INTO topics (topic_name, total_questions, marks_per_question, negative_marks)
                           VALUES (:topic_name, :total_questions, :marks_per_question, :negative_marks)");
    $stmt->execute([
        ':topic_name' => $topic_name,
        ':total_questions' => $total_questions,
        ':marks_per_question' => $marks_per_question,
        ':negative_marks' => $negative_marks,
    ]);

    header("Location: dashboard.php"); // Redirect after successful insertion
}
?>
