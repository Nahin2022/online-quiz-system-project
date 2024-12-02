<?php
require_once 'db_connect.php';

// Fetch topics from the database
$query = "SELECT * FROM topics";
$stmt = $pdo->prepare($query);
$stmt->execute();
$topics = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Topics Table -->
<table border="1">
    <thead>
        <tr>
            <th>Topic Name</th>
            <th>Total Questions</th>
            <th>Marks Per Question</th>
            <th>Negative Marks</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if (count($topics) > 0) {
            foreach ($topics as $row) {
                echo "<tr>
                        <td>{$row['topic_name']}</td>
                        <td>{$row['total_questions']}</td>
                        <td>{$row['marks_per_question']}</td>
                        <td>{$row['negative_marks']}</td>
                        <td>
                            <a href='edit_topic.php?id={$row['id']}'>Edit</a> | 
                            <a href='delete_topic.php?id={$row['id']}'>Delete</a>
                        </td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No topics available.</td></tr>";
        }
        ?>
    </tbody>
</table>

<!-- Add Topic Form -->
<form action="add_topic.php" method="POST" style="margin-top: 20px;">
    <h3>Add New Topic</h3>
    <input type="text" name="topic_name" placeholder="Enter topic name" required>
    <input type="number" name="total_questions" placeholder="Total questions" required>
    <input type="number" name="marks_per_question" placeholder="Marks per question" required>
    <input type="number" name="negative_marks" placeholder="Negative marks" required>
    <button type="submit">Add Topic</button>
</form>
