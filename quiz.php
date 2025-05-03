<?php
require_once 'includes/auth.php';
require_once 'includes/functions.php';

redirectIfNotLoggedIn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_answers = $_POST['answers'] ?? [];
    $question_ids = array_keys($user_answers);

    if (!empty($question_ids)) {
        $correct_answers = getCorrectAnswers($question_ids);
        $score = calculateScore($user_answers, $correct_answers);
        $total_questions = count($question_ids);

        $result_id = saveQuizResult($_SESSION['user_id'], $score, $total_questions);
        if ($result_id) {
            $success = saveUserAnswers($result_id, $user_answers);
            if ($success) {
                $_SESSION['quiz_result'] = [
                    'score' => $score,
                    'total' => $total_questions
                ];
                header("Location: result.php");
                exit();
            } else {
                echo "An error occurred while saving your answers.";
            }
        } else {
            echo "An error occurred while saving your quiz result.";
        }
    }
}

// Get questions by difficulty
$easy_questions = getQuestionsByDifficulty('easy');
$average_questions = getQuestionsByDifficulty('average');
$difficult_questions = getQuestionsByDifficulty('difficult');

// Alternative merging method
$all_questions = $easy_questions;
foreach ($average_questions as $id => $question) {
    $all_questions[$id] = $question;
}
foreach ($difficult_questions as $id => $question) {
    $all_questions[$id] = $question;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History Quiz</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=MedievalSharp&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="header-decoration">
            <span class="history-icon">⚔</span> History Knowledge Challenge <span class="history-icon">🏛</span>
        </div>
        
        <h1>History Quiz</h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
        
        <?php if (count($all_questions) < 30): ?>
            <div class="alert error">
                Warning: Only <?php echo count($all_questions); ?> questions available. 
                The quiz requires 30 questions (10 easy, 10 average, 10 difficult).
            </div>
        <?php endif; ?>
        
        <form action="quiz.php" method="post">
            <?php 
            $question_counter = 1;
            foreach ($all_questions as $question_id => $question): 
            ?>
                <div class="question">
                    <h3>Question <?php echo $question_counter++; ?>: <?php echo htmlspecialchars($question['question_text']); ?></h3>
                    
                    <?php foreach ($question['answers'] as $answer_id => $answer_text): ?>
                        <div class="answer">
                            <input type="radio" name="answers[<?php echo $question_id; ?>]" 
                                   id="answer_<?php echo $answer_id; ?>" value="<?php echo $answer_id; ?>" required>
                            <label for="answer_<?php echo $answer_id; ?>">
                                <?php echo htmlspecialchars($answer_text); ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
            
            <div style="text-align: center; margin: 30px 0;">
                <button type="submit" class="btn">Submit Your Answers</button>
            </div>
        </form>
        
        <div class="footer">
            <p>Ancient Wisdom • Timeless Knowledge • Endless Discovery</p>
            <p>© <?php echo date('Y'); ?> History Quiz</p>
        </div>
        
        <div style="text-align: center; margin-top: 20px;">
            <a href="logout.php" class="btn">Exit the Archives</a>
        </div>
    </div>
    
    <script src="assets/js/history.js"></script>
</body>
</html>
