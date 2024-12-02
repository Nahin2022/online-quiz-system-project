<?php
if (isset($_POST['delete_topic'])) {
    $topic_id = filter_input(INPUT_POST, 'topic_id', FILTER_VALIDATE_INT);

    // Delete the topic from the database
    $stmt = $pdo->prepare("DELETE FROM topics WHERE id = :topic_id");
    $stmt->execute([':topic_id' => $topic_id]);

    header("Location: dashboard.php"); // Redirect after successful deletion
}
?>
