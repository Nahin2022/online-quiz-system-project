<?php
include 'db_connect.php'; // This file sets up the $pdo connection

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        // Prepare and execute the query to fetch the topic
        $query = "SELECT * FROM topics WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $topic = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if topic exists
        if (!$topic) {
            echo "Topic not found!";
            exit;
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
} else {
    die("Error: No topic ID provided.");
}
?>

<h3>Edit Topic</h3>
<form action="update_topic.php" method="POST">
    <input type="hidden" name="id" value="<?php echo $topic['id']; ?>">
    <label for="topic_name">Topic Name:</label>
    <input type="text" id="topic_name" name="topic_name" value="<?php echo htmlspecialchars($topic['topic_name']); ?>" required>
    <br>
    <label for="total_questions">Total Questions:</label>
    <input type="number" id="total_questions" name="total_questions" value="<?php echo $topic['total_questions']; ?>" required>
    <br>
    <label for="marks_per_question">Marks per Question:</label>
    <input type="number" id="marks_per_question" name="marks_per_question" value="<?php echo $topic['marks_per_question']; ?>" required>
    <br>
    <label for="negative_marks">Negative Marks:</label>
    <input type="number" id="negative_marks" name="negative_marks" value="<?php echo $topic['negative_marks']; ?>" required>
    <br>
    <button type="submit">Update Topic</button>
</form>
