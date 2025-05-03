<?php
// test_questions.php - Temporary test file for question retrieval

require_once 'includes/config.php';
require_once 'includes/functions.php';

echo "<h1>Question Retrieval Test</h1>";

// Test each difficulty level
$test_easy = getQuestionsByDifficulty('easy');
$test_avg = getQuestionsByDifficulty('average');
$test_hard = getQuestionsByDifficulty('difficult');

// Display counts
echo "<h2>Question Counts by Difficulty</h2>";
echo "Easy questions: " . count($test_easy) . "<br>";
echo "Average questions: " . count($test_avg) . "<br>";
echo "Difficult questions: " . count($test_hard) . "<br>";

// Test merging
$combined = array_merge_recursive($test_easy, $test_avg, $test_hard);
echo "<h2>Combined Results</h2>";
echo "Total combined questions: " . count($combined) . "<br>";

// Display some sample questions if needed
echo "<h2>Sample Questions</h2>";
$sample = array_slice($combined, 0, 3, true); // Show first 3 questions
foreach ($sample as $id => $question) {
    echo "<h3>Question ID: $id</h3>";
    echo "<p>" . htmlspecialchars($question['question_text']) . "</p>";
    echo "<ul>";
    foreach ($question['answers'] as $ans_id => $answer) {
        echo "<li>" . htmlspecialchars($answer) . " (ID: $ans_id)</li>";
    }
    echo "</ul>";
}

// Database verification
echo "<h2>Database Verification</h2>";
try {
    $stmt = $pdo->query("SELECT difficulty, COUNT(*) as count FROM questions GROUP BY difficulty");
    $counts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1'>";
    echo "<tr><th>Difficulty</th><th>Count</th></tr>";
    foreach ($counts as $row) {
        echo "<tr><td>" . htmlspecialchars($row['difficulty']) . "</td><td>" . $row['count'] . "</td></tr>";
    }
    echo "</table>";
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
?>