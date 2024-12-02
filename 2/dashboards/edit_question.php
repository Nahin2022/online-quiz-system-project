<?php
require_once 'db_connect.php';
session_start();

if (!isset($_SESSION['teacher_id'])) {
    echo "Teacher ID not set. Please log in again.";
    exit;
}

if (isset($_GET['id'])) {
    $question_id = $_GET['id'];

    // Fetch the question details for editing
    $query = "SELECT * FROM questions WHERE id = :question_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':question_id', $question_id, PDO::PARAM_INT);
    $stmt->execute();
    $question = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$question) {
        echo "Question not found or you don't have permission to edit it.";
        exit;
    }
} else {
    echo "No question ID provided.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Question</title>
</head>
<body>
    <h2>Edit Question</h2>
    <form action="edit_question_process.php" method="POST">
        <input type="hidden" name="question_id" value="<?php echo htmlspecialchars($question['id']); ?>">

        <label for="question_text">Question:</label>
        <textarea name="question_text" required><?php echo htmlspecialchars($question['question_text']); ?></textarea><br>

        <label for="option_a">Option A:</label>
        <input type="text" name="option_a" value="<?php echo htmlspecialchars($question['option_a']); ?>" required><br>

        <label for="option_b">Option B:</label>
        <input type="text" name="option_b" value="<?php echo htmlspecialchars($question['option_b']); ?>" required><br>

        <label for="option_c">Option C:</label>
        <input type="text" name="option_c" value="<?php echo htmlspecialchars($question['option_c']); ?>"><br>

        <label for="option_d">Option D:</label>
        <input type="text" name="option_d" value="<?php echo htmlspecialchars($question['option_d']); ?>"><br>

        <label for="option_e">Option E:</label>
        <input type="text" name="option_e" value="<?php echo htmlspecialchars($question['option_e']); ?>"><br>

        <label for="correct_option">Correct Option (A/B/C/D/E):</label>
        <input type="text" name="correct_option" value="<?php echo htmlspecialchars($question['correct_option']); ?>" required><br>

        <button type="submit">Save Changes</button>
    </form>
</body>
</html>
