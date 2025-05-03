<?php
require_once 'includes/auth.php';
require_once 'includes/functions.php';

redirectIfNotLoggedIn();

if (!isset($_SESSION['quiz_result'])) {
    header("Location: quiz.php");
    exit();
}

$score = $_SESSION['quiz_result']['score'];
$total = $_SESSION['quiz_result']['total'];
$percentage = round(($score / $total) * 100);

// Determine grade
if ($percentage >= 90) $grade = 'A';
elseif ($percentage >= 80) $grade = 'B';
elseif ($percentage >= 70) $grade = 'C';
elseif ($percentage >= 60) $grade = 'D';
else $grade = 'F';

unset($_SESSION['quiz_result']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Results</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Quiz Results</h1>
        
        <div class="result-card">
            <h2>Your Score: <?php echo $score; ?>/<?php echo $total; ?></h2>
            <h3>Percentage: <?php echo $percentage; ?>%</h3>
            <h3>Grade: <?php echo $grade; ?></h3>
            
            <div class="progress-bar">
                <div class="progress" style="width: <?php echo $percentage; ?>%"></div>
            </div>
            
            <p>
                <?php if ($percentage >= 70): ?>
                    Congratulations! You have a good knowledge of history.
                <?php else: ?>
                    Keep studying! History is full of fascinating stories waiting to be discovered.
                <?php endif; ?>
            </p>
            
            <a href="quiz.php" class="btn">Take Quiz Again</a>
            <a href="index.php" class="btn">Return Home</a>
        </div>
    </div>
</body>
</html>