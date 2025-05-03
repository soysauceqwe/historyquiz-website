<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';

redirectIfNotLoggedIn();
redirectIfNotAdmin();

// Handle question deletion
if (isset($_GET['delete_question'])) {
    $question_id = $_GET['delete_question'];
    $stmt = $pdo->prepare("DELETE FROM questions WHERE id = ?");
    $stmt->execute([$question_id]);
    header("Location: questions.php");
    exit();
}

// Handle form submission for new question
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question_text = $_POST['question_text'];
    $difficulty = $_POST['difficulty'];
    $answers = $_POST['answers'];
    $correct_answer = $_POST['correct_answer'];
    
    // Insert question
    $stmt = $pdo->prepare("INSERT INTO questions (question_text, difficulty) VALUES (?, ?)");
    $stmt->execute([$question_text, $difficulty]);
    $question_id = $pdo->lastInsertId();
    
    // Insert answers
    foreach ($answers as $index => $answer_text) {
        if (!empty($answer_text)) {
            $is_correct = ($index == $correct_answer) ? 1 : 0;
            $stmt = $pdo->prepare("INSERT INTO answers (question_id, answer_text, is_correct) VALUES (?, ?, ?)");
            $stmt->execute([$question_id, $answer_text, $is_correct]);
        }
    }
    
    header("Location: questions.php");
    exit();
}

// Get all questions
$stmt = $pdo->query("SELECT q.*, COUNT(a.id) as answer_count FROM questions q LEFT JOIN answers a ON q.id = a.question_id GROUP BY q.id");
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Questions</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Manage Questions</h1>
        
        <div class="admin-nav">
            <a href="index.php">Dashboard</a>
            <a href="questions.php" class="active">Questions</a>
            <a href="results.php">Results</a>
            <a href="../logout.php">Logout</a>
        </div>
        
        <h2>Add New Question</h2>
        <form method="post">
            <div class="form-group">
                <label for="question_text">Question Text:</label>
                <textarea id="question_text" name="question_text" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="difficulty">Difficulty:</label>
                <select id="difficulty" name="difficulty" required>
                    <option value="easy">Easy</option>
                    <option value="average">Average</option>
                    <option value="difficult">Difficult</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Answers (mark the correct one):</label>
                <?php for ($i = 0; $i < 4; $i++): ?>
                    <div class="answer-input">
                        <input type="radio" name="correct_answer" value="<?php echo $i; ?>" <?php echo $i === 0 ? 'checked' : ''; ?>>
                        <input type="text" name="answers[]" placeholder="Answer option <?php echo $i + 1; ?>" required>
                    </div>
                <?php endfor; ?>
            </div>
            
            <button type="submit" class="btn">Add Question</button>
        </form>
        
        <h2>Existing Questions</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Question</th>
                    <th>Difficulty</th>
                    <th>Answers</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($questions as $question): ?>
                    <tr>
                        <td><?php echo $question['id']; ?></td>
                        <td><?php echo htmlspecialchars($question['question_text']); ?></td>
                        <td><?php echo ucfirst($question['difficulty']); ?></td>
                        <td><?php echo $question['answer_count']; ?></td>
                        <td>
                            <a href="?delete_question=<?php echo $question['id']; ?>" 
                               onclick="return confirm('Are you sure you want to delete this question?')" 
                               class="btn danger">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>