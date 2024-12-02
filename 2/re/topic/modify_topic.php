<?php
if (isset($_POST['modify_topic'])) {
    $topic_id = filter_input(INPUT_POST, 'topic_id', FILTER_VALIDATE_INT);
    $topic_name = filter_input(INPUT_POST, 'topic_name', FILTER_SANITIZE_STRING);
    $total_questions = filter_input(INPUT_POST, 'total_questions', FILTER_VALIDATE_INT);
    $marks_per_question = filter_input(INPUT_POST, 'marks_per_question', FILTER_VALIDATE_INT);
    $negative_marks = filter_input(INPUT_POST, 'negative_marks', FILTER_VALIDATE_INT);

    // Update topic in the database
    $stmt = $pdo->prepare("UPDATE topics
                           SET topic_name = :topic_name, total_questions = :total_questions, marks_per_question = :marks_per_question, negative_marks = :negative_marks
                           WHERE id = :topic_id");
    $stmt->execute([
        ':topic_name' => $topic_name,
        ':total_questions' => $total_questions,
        ':marks_per_question' => $marks_per_question,
        ':negative_marks' => $negative_marks,
        ':topic_id' => $topic_id
    ]);

    header("Location: dashboard.php"); // Redirect after successful update
}
?>
