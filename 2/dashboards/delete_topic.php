<?php
require_once 'db_connect.php'; // Use require_once to include the database connection file

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // Prepare the delete query
        $query = "DELETE FROM topics WHERE id = :id";
        $stmt = $pdo->prepare($query); // Use $pdo instead of $conn
        
        // Bind the id parameter
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Execute the statement
        if ($stmt->execute()) {
            header('Location: dashboard.html?message=Topic+deleted+successfully'); // Redirect with a success message
            exit();
        } else {
            echo "Error deleting the topic.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage(); // Display detailed error message
    }
} else {
    echo "Invalid request. Topic ID is missing.";
}
?>
