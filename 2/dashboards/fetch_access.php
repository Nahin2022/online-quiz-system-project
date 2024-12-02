<?php
require_once 'db_connect.php';  // Include the database connection file
session_start();

// Ensure teacher_id exists in the session
if (!isset($_SESSION['teacher_id'])) {
    die("Error: Teacher ID not found in session. Please log in.");
}

// Fetch the teacher_id from the session
$teacher_id = $_SESSION['teacher_id'];

// Function to display existing access codes
function fetchAccessCodes($pdo) {
    $sql = "SELECT a.access_id, u.username AS teacher_name, t.topic_name, a.topic_open_code
            FROM access a
            JOIN users u ON a.teacher_id = u.id
            JOIN topics t ON a.topic_id = t.id
            WHERE u.role = 'teacher'";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $accessRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($accessRecords) {
            echo "<table border='1'>";
            echo "<tr><th>Access ID</th><th>Teacher Name</th><th>Topic Name</th><th>Topic Open Code</th></tr>";
            foreach ($accessRecords as $row) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['access_id']) . "</td>
                        <td>" . htmlspecialchars($row['teacher_name']) . "</td>
                        <td>" . htmlspecialchars($row['topic_name']) . "</td>
                        <td>" . htmlspecialchars($row['topic_open_code']) . "</td>
                    </tr>";
            }
            echo "</table>";
        } else {
            echo "No access records found.";
        }
    } catch (PDOException $e) {
        echo "Error fetching data: " . $e->getMessage();
    }
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    var_dump($_POST); // Debugging: Check POST data

    if (isset($_POST['topic_open_code']) && isset($_POST['topic_id'])) {
        $topic_open_code = $_POST['topic_open_code'];
        $topic_id = $_POST['topic_id'];

        // Debugging: Check submitted values
        echo "teacher_id: $teacher_id, topic_open_code: $topic_open_code, topic_id: $topic_id"; 

        $sql = "INSERT INTO access (teacher_id, topic_open_code, topic_id) VALUES (:teacher_id, :topic_open_code, :topic_id)";
        
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':teacher_id', $teacher_id, PDO::PARAM_INT);
            $stmt->bindParam(':topic_open_code', $topic_open_code, PDO::PARAM_STR);
            $stmt->bindParam(':topic_id', $topic_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                echo "New access code added successfully.";
            } else {
                $errorInfo = $stmt->errorInfo();
                echo "Error: " . implode(" - ", $errorInfo);
            }
        } catch (PDOException $e) {
            echo "Error inserting data: " . $e->getMessage();
        }
    }
}

// Fetch existing access codes to display
fetchAccessCodes($pdo);
?>

<!-- Form to upload a new access code -->
<h2>Upload New Access Code</h2>
<form method="POST" action="">
    <label for="topic_open_code">Topic Open Code:</label>
    <input type="text" name="topic_open_code" id="topic_open_code" required><br><br>
    
    <label for="topic_id">Topic:</label>
    <select name="topic_id" id="topic_id" required>
        <?php
        try {
            $sql = "SELECT id, topic_name FROM topics";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $topics = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($topics as $topic) {
                echo "<option value='" . htmlspecialchars($topic['id']) . "'>" . htmlspecialchars($topic['topic_name']) . "</option>";
            }
        } catch (PDOException $e) {
            echo "Error fetching topics: " . $e->getMessage();
        }
        ?>
    </select><br><br>
    
    <input type="submit" value="Upload Access Code">
</form>
