<?php
require_once 'config.php';

function getQuestionsByDifficulty($difficulty, $limit = 10) {
    global $pdo;
    
    // First get random question IDs with the specified limit
    $stmt = $pdo->prepare("SELECT id FROM questions WHERE difficulty = ? ORDER BY RAND() LIMIT ?");
    $stmt->bindValue(1, $difficulty, PDO::PARAM_STR);
    $stmt->bindValue(2, $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    $question_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($question_ids)) {
        return [];
    }
    
    // Then get the questions and their answers
    $in = str_repeat('?,', count($question_ids) - 1) . '?';
    $stmt = $pdo->prepare("SELECT q.id, q.question_text, a.id as answer_id, a.answer_text 
                          FROM questions q 
                          JOIN answers a ON q.id = a.question_id 
                          WHERE q.id IN ($in)");
    $stmt->execute($question_ids);
    
    $questions = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $questions[$row['id']]['question_text'] = $row['question_text'];
        $questions[$row['id']]['answers'][$row['answer_id']] = $row['answer_text'];
    }
    
    return $questions;
}

function getCorrectAnswers($question_ids) {
    global $pdo;
    $in = str_repeat('?,', count($question_ids) - 1) . '?';
    $stmt = $pdo->prepare("SELECT question_id, id as answer_id 
                          FROM answers 
                          WHERE is_correct = 1 AND question_id IN ($in)");
    $stmt->execute($question_ids);
    
    $correct_answers = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $correct_answers[$row['question_id']] = $row['answer_id'];
    }
    
    return $correct_answers;
}

function calculateScore($user_answers, $correct_answers) {
    $score = 0;
    foreach ($user_answers as $question_id => $answer_id) {
        if (isset($correct_answers[$question_id]) && $correct_answers[$question_id] == $answer_id) {
            $score++;
        }
    }
    return $score;
}


// functions.php

function saveQuizResult($user_id, $score, $total_questions) {
    global $pdo;

    try {
        $stmt = $pdo->prepare("INSERT INTO quiz_results (user_id, score, total_questions) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $score, $total_questions]);
        return $pdo->lastInsertId();
    } catch (PDOException $e) {
        error_log("Failed to save quiz result: " . $e->getMessage());
        return false;
    }
}


// functions.php


function saveUserAnswers($result_id, $user_answers) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO user_answers (result_id, question_id, answer_id) VALUES (?, ?, ?)");
        foreach ($user_answers as $question_id => $answer_id) {
            $stmt->execute([$result_id, $question_id, $answer_id]);
        }
        return true;
    } catch (PDOException $e) {
        // Log the error message
        error_log("Error saving user answers: " . $e->getMessage());
        return false;
    }
}
?>

