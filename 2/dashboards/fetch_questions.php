<?php
require_once 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "Teacher ID not set. Please log in again.";
    exit;
}

$teacher_id = $_SESSION['user_id']; // Ensure this is set during login.

try {
    // Fetch the list of topics for the teacher
    $topicQuery = "SELECT id, topic_name FROM topics WHERE teacher_id = :teacher_id";
    $topicStmt = $pdo->prepare($topicQuery);
    $topicStmt->bindParam(':teacher_id', $teacher_id, PDO::PARAM_INT);
    $topicStmt->execute();
    $topics = $topicStmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($topics as $topic) {
        // Query to fetch questions for the specific topic
        $query = "SELECT 
                      q.id, 
                      t.topic_name, 
                      q.question_text, 
                      q.option_a, 
                      q.option_b, 
                      q.option_c, 
                      q.option_d, 
                      q.option_e, 
                      q.correct_option
                  FROM 
                      questions q
                  JOIN 
                      topics t ON q.topic_id = t.id
                  WHERE 
                      t.teacher_id = :teacher_id AND q.topic_id = :topic_id";
        
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':teacher_id', $teacher_id, PDO::PARAM_INT);
        $stmt->bindParam(':topic_id', $topic['id'], PDO::PARAM_INT);
        $stmt->execute();
        $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Display Topic Name
        echo "<h3>Topic: {$topic['topic_name']}</h3>";

        if (count($questions) > 0) {
            echo "<table><tr>
                    <th>Topic Name</th>
                    <th>Question</th>
                    <th>Option A</th>
                    <th>Option B</th>
                    <th>Option C</th>
                    <th>Option D</th>
                    <th>Option E</th>
                    <th>Correct Option</th>
                    <th>Actions</th>
                  </tr>";
            
            foreach ($questions as $question) {
                echo "<tr>
                        <td>{$question['topic_name']}</td>
                        <td>{$question['question_text']}</td>
                        <td>{$question['option_a']}</td>
                        <td>{$question['option_b']}</td>
                        <td>{$question['option_c']}</td>
                        <td>{$question['option_d']}</td>
                        <td>{$question['option_e']}</td>
                        <td>{$question['correct_option']}</td>
                        <td>
                            <a href='edit_question.php?id={$question['id']}'>Edit</a> | 
                            <a href='delete_question.php?id={$question['id']}'>Delete</a>
                        </td>
                      </tr>
                      <a href='add_question.php?topic_id={$topic['id']}'>Add Question</a>";
            }
            
            echo "</table>";

        } else {
            // No questions available for this topic, show Add Question option
            echo "<p>No questions available for this topic.</p>";
            echo "<a href='add_question.php?topic_id={$topic['id']}'>Add Question</a>";
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
