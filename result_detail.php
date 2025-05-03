<?php
require_once '../includes/auth.php';
require_once '../includes/config.php';
require_once '../includes/functions.php';

redirectIfNotLoggedIn();
redirectIfNotAdmin();

// Check if 'result_id' is passed via GET
echo "Received result_id: " . $_GET['result_id'] . "<br>";

if (!isset($_GET['result_id']) || !is_numeric($_GET['result_id'])) {
    echo "Invalid result ID.";
    exit();
}

$result_id = (int) $_GET['result_id']; // Sanitize the result_id

// Ensure the result_id exists in quiz_results table
$stmt = $pdo->prepare("SELECT COUNT(*) FROM quiz_results WHERE id = ?");
$stmt->execute([$result_id]);
$exists = $stmt->fetchColumn();

if (!$exists) {
    echo "No result found for result_id: $result_id";
    exit();
}

// Fetch the user's answers along with the questions and the correct answers
$stmt = $pdo->prepare("SELECT 
                            u.question_id,
                            u.answer_id, 
                            q.question_text, 
                            a.answer_text AS user_answer, 
                            a.is_correct
                        FROM user_answers u
                        JOIN questions q ON u.question_id = q.id
                        LEFT JOIN answers a ON u.answer_id = a.id
                        WHERE u.result_id = ?");
$stmt->execute([$result_id]);
$answers = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($answers)) {
    echo "No answers found for this result_id: $result_id";
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Result Detail - History Quiz</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=MedievalSharp&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="header-decoration">
            <span class="history-icon">📜</span> Result Details <span class="history-icon">🔍</span>
        </div>
        
        <h1>Scholar's Performance Overview</h1>
        
        <div class="admin-nav">
            <a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="questions.php"><i class="fas fa-question-circle"></i> Questions</a>
            <a href="results.php"><i class="fas fa-scroll"></i> Results</a>
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>

        <div class="result-details">
            <h2>Answers Review</h2>
            <table>
                <thead>
                    <tr>
                        <th>Question</th>
                        <th>Your Answer</th>
                        <th>Correct Answer</th>
                        <th>Result</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($answers as $answer): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($answer['question_text']); ?></td>
                            <td><?php echo htmlspecialchars($answer['user_answer']); ?></td>
                            <td>
                                <?php 
                                $stmt = $pdo->prepare("SELECT answer_text FROM answers WHERE question_id = ? AND is_correct = 1");
                                $stmt->execute([$answer['question_id']]);
                                $correct_answer = $stmt->fetchColumn();
                                echo htmlspecialchars($correct_answer);
                                ?>
                            </td>
                            <td>
                                <?php echo $answer['is_correct'] ? '✅ Correct' : '❌ Incorrect'; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php
    // Error handling in case of any issues with the query
    if ($stmt->errorCode() !== '00000') {
        error_log("Database error: " . implode(", ", $stmt->errorInfo()));
    }
    ?>
</body>
</html>
