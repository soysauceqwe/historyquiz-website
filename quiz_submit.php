<?php
require_once 'config.php'; // Database connection
require_once 'functions.php'; // Function to calculate score, save result, etc.

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_answers = $_POST['answers']; // Capturing the user's answers (array of question_id => answer_id)
    
    // Get the correct answers for comparison
    $correct_answers = getCorrectAnswers(array_keys($user_answers));
    
    // Calculate the score
    $score = calculateScore($user_answers, $correct_answers);
    $total_questions = count($user_answers);

    // Save the quiz result and get the result_id
    $user_id = $_SESSION['user_id']; // Assuming user_id is stored in session
    $result_id = saveQuizResult($user_id, $score, $total_questions);

    // Insert the user's answers into the user_answers table
    foreach ($user_answers as $question_id => $answer_id) {
        $stmt = $pdo->prepare("INSERT INTO user_answers (result_id, question_id, answer_id) VALUES (?, ?, ?)");
        $stmt->execute([$result_id, $question_id, $answer_id]);
    }

    // Redirect to result page
    header("Location: result_page.php?result_id=$result_id");
    exit();
}
?>
