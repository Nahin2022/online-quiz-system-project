<?php
require_once 'db_connect.php';
session_start();

if (!isset($_SESSION['teacher_id'])) {
    echo "Teacher ID not set. Please log in again.";
    exit;
}

$teacher_id = $_SESSION['teacher_id']; // Ensure this is set during login.

// Fetch teacher's topics
$query = "SELECT id, topic_name FROM topics WHERE teacher_id = :teacher_id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':teacher_id', $teacher_id, PDO::PARAM_INT);
$stmt->execute();
$topics = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch question limit (could be stored in the database or predefined)
$query = "SELECT total_questions FROM topics WHERE teacher_id = :teacher_id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':teacher_id', $teacher_id, PDO::PARAM_INT);
$stmt->execute();
$topic_info = $stmt->fetch(PDO::FETCH_ASSOC);

// Assuming total_questions is a dynamic limit per topic
$total_questions = $topic_info['total_questions'];
?>

<form action="add_question_process.php" method="POST">
    <label for="topic_id">Choose Topic:</label>
    <select name="topic_id" required>
        <?php
        foreach ($topics as $topic) {
            echo "<option value='{$topic['id']}'>{$topic['topic_name']}</option>";
        }
        ?>
    </select><br>

    <label for="question_text">Question:</label>
    <textarea name="question_text" required></textarea><br>

    <label for="option_a">Option A:</label>
    <input type="text" name="option_a" required><br>

    <label for="option_b">Option B:</label>
    <input type="text" name="option_b" required><br>

    <label for="option_c">Option C:</label>
    <input type="text" name="option_c"><br>

    <label for="option_d">Option D:</label>
    <input type="text" name="option_d"><br>

    <label for="option_e">Option E:</label>
    <input type="text" name="option_e"><br>

    <label for="correct_option">Correct Option (A/B/C/D/E):</label>
    <input type="text" name="correct_option" required><br>

    <button type="submit">Add Question</button>
</form>
