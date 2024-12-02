<?php
// Include the PDO connection file
include 'db_connect.php';

// Start the session and regenerate session ID for security
session_start();
session_regenerate_id(true);

// Check if the user is logged in and has the correct role
if (!isset($_SESSION['role'])) {
    session_destroy();
    header("Location: index.html");  // Redirect to login page
    exit();
}

// Function to handle user blocking/unblocking
function updateUserStatus($pdo, $user_id, $status)
{
    $sql = "UPDATE users SET is_blocked = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$status, $user_id]);
}

// Function to add a question
function addQuestion($pdo, $data)
{
    $sql = "INSERT INTO questions (topic_id, question_text, option_a, option_b, option_c, option_d, option_e, correct_option)
            VALUES (:topic_id, :question_text, :option_a, :option_b, :option_c, :option_d, :option_e, :correct_option)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($data);
}

// Function to set notifications
function setNotification($type, $message)
{
    $_SESSION[$type] = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
}

// Fetch topics, users, and scores data from the database
$topics = $pdo->query("SELECT id, topic_name, total_questions, marks_per_question, negative_marks FROM topics");
$users = $pdo->query("SELECT id, username, role, is_blocked FROM users");
$scores = $pdo->query("SELECT users.username, users.id AS user_id, SUM(scores.score) AS total_score 
                       FROM scores
                       INNER JOIN users ON scores.user_id = users.id
                       GROUP BY scores.user_id");

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Handle user block/unblock actions
        if (isset($_POST['block_user']) || isset($_POST['unblock_user'])) {
            $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
            if ($user_id === false) {
                setNotification('error', 'Invalid user ID.');
                header("Location: dashboard.php");
                exit();
            }
            $status = isset($_POST['block_user']) ? 1 : 0;
            if (updateUserStatus($pdo, $user_id, $status)) {
                setNotification('success', 'User status updated successfully.');
            } else {
                setNotification('error', 'Failed to update user status.');
            }
        }

        // Handle question addition
        elseif (isset($_POST['add_question'])) {
            // Validate and sanitize input fields
            $data = [
                ':topic_id' => filter_input(INPUT_POST, 'topic_id', FILTER_VALIDATE_INT),
                ':question_text' => filter_input(INPUT_POST, 'question_text', FILTER_SANITIZE_STRING),
                ':option_a' => filter_input(INPUT_POST, 'option_a', FILTER_SANITIZE_STRING),
                ':option_b' => filter_input(INPUT_POST, 'option_b', FILTER_SANITIZE_STRING),
                ':option_c' => filter_input(INPUT_POST, 'option_c', FILTER_SANITIZE_STRING),
                ':option_d' => filter_input(INPUT_POST, 'option_d', FILTER_SANITIZE_STRING),
                ':option_e' => filter_input(INPUT_POST, 'option_e', FILTER_SANITIZE_STRING),
                ':correct_option' => filter_input(INPUT_POST, 'correct_option', FILTER_SANITIZE_STRING),
            ];

            if (addQuestion($pdo, $data)) {
                setNotification('success', 'Question added successfully.');
            } else {
                setNotification('error', 'Failed to add the question.');
            }
        }
    } catch (PDOException $e) {
        // Log the error for debugging
        error_log("Error: " . $e->getMessage());
        setNotification('error', "An error occurred. Please try again later.");
    }

    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const navLinks = document.querySelectorAll('.nav-link');
    const tabSections = document.querySelectorAll('.tab-section');

    function showSectionFromHash() {
        const hash = window.location.hash || '#dashboard';
        tabSections.forEach(section => {
            section.classList.toggle('active', `#${section.id}` === hash);
        });
        navLinks.forEach(link => {
            link.classList.toggle('active', link.getAttribute('href') === hash);
        });
    }

    navLinks.forEach(link => {
        link.addEventListener('click', event => {
            event.preventDefault(); // Prevent default navigation
            window.location.hash = link.getAttribute('href');
            showSectionFromHash();
        });
    });

    // Initialize on load
    showSectionFromHash();
});

</script>

<div class="container">
    <div class="sidebar">
        <div class="profile">
            <img src="profile.jpg" alt="Profile Image of <?php echo htmlspecialchars($_SESSION['username']); ?>">
            <h3><?php echo htmlspecialchars($_SESSION['username']); ?></h3>
        </div>
        <nav>
        <ul>
            <li><a href="#dashboard" class="nav-link">Dashboard</a></li>
            <li><a href="#topics" class="nav-link">Topics</a></li>
            <li><a href="#questions" class="nav-link">Questions</a></li>
            <li><a href="#users" class="nav-link">Users</a></li>
            <li><a href="#scores" class="nav-link">Scores</a></li>
            <li><a href="#feedback" class="nav-link">Feedback</a></li>
            <li><a href="logout.php" class="logout">Logout</a></li>
        </ul>

        </nav>
    </div>

    <main>
        <header class="header-content">
            <h1>Quiz System Dashboard</h1>
        </header>
        <div class="tab-content">
            <!-- Topics Section -->
            <section id="topics" class="tab-section active">
                <h2>Topics</h2>
                <table>
                    <thead>
                        <tr>
                            <th>S.N.</th>
                            <th>Topic</th>
                            <th>Total Questions</th>
                            <th>Marks</th>
                            <th>Negative Marks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $sn = 1; while ($row = $topics->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?php echo $sn++; ?></td>
                                <td><?php echo htmlspecialchars($row['topic_name']); ?></td>
                                <td><?php echo $row['total_questions']; ?></td>
                                <td><?php echo $row['marks_per_question']; ?></td>
                                <td><?php echo $row['negative_marks']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </section>

            <!-- Users Section -->
            <section id="users" class="tab-section">
                <h2>Users</h2>
                <table>
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $users->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                <td><?php echo ucfirst($row['role']); ?></td>
                                <td><?php echo $row['is_blocked'] ? 'Blocked' : 'Active'; ?></td>
                                <td>
                                    <form method="post">
                                        <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="<?php echo $row['is_blocked'] ? 'unblock_user' : 'block_user'; ?>">
                                            <?php echo $row['is_blocked'] ? 'Unblock' : 'Block'; ?>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </section>

            <!-- Scores Section -->
            <section id="scores" class="tab-section">
                <h2>Scores</h2>
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Total Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $scores->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                <td><?php echo $row['total_score']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </section>
            <!-- Add Question Section -->
<section id="add-question" class="tab-section">
    <h2>Add New Question</h2>
    <form method="POST" action="add_question.php">
        <div class="form-group">
            <label for="topic_id">Topic</label>
            <select id="topic_id" name="topic_id" required>
                <option value="">Select a Topic</option>
                <?php
                // Assuming $topics contains available topics from the database
                while ($row = $topics->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='{$row['id']}'>{$row['topic_name']}</option>";
                }
                ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="question_text">Question Text</label>
            <textarea id="question_text" name="question_text" rows="3" required></textarea>
        </div>
        
        <div class="form-group">
            <label for="option_a">Option A</label>
            <input type="text" id="option_a" name="option_a" required />
        </div>
        
        <div class="form-group">
            <label for="option_b">Option B</label>
            <input type="text" id="option_b" name="option_b" required />
        </div>
        
        <div class="form-group">
            <label for="option_c">Option C</label>
            <input type="text" id="option_c" name="option_c" required />
        </div>
        
        <div class="form-group">
            <label for="option_d">Option D</label>
            <input type="text" id="option_d" name="option_d" required />
        </div>
        
        <div class="form-group">
            <label for="option_e">Option E</label>
            <input type="text" id="option_e" name="option_e" />
        </div>
        
        <div class="form-group">
            <label for="correct_option">Correct Option</label>
            <select id="correct_option" name="correct_option" required>
                <option value="">Select Correct Option</option>
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="C">C</option>
                <option value="D">D</option>
                <option value="E">E</option>
            </select>
        </div>
        
        <button type="submit" name="add_question">Add Question</button>
    </form>
</section>

        </div>
    </main>
</div>

</body>
</html>
