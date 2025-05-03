<?php
require_once '../includes/auth.php';
require_once '../includes/config.php';
require_once '../includes/functions.php';

redirectIfNotLoggedIn();
redirectIfNotAdmin();

// Get stats for dashboard
$stmt = $pdo->query("SELECT COUNT(*) as total_users FROM users");
$total_users = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) as total_questions FROM questions");
$total_questions = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) as total_results FROM quiz_results");
$total_results = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT AVG(score) as avg_score FROM quiz_results");
$avg_score = round($stmt->fetchColumn(), 2);

$recent_results = $pdo->query(
    "SELECT u.username, r.score, r.total_questions, r.taken_at 
     FROM quiz_results r 
     JOIN users u ON r.user_id = u.id 
     ORDER BY r.taken_at DESC LIMIT 5"
)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - History Quiz</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=MedievalSharp&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="header-decoration">
            <span class="history-icon">👑</span> Archivist's Dashboard <span class="history-icon">📊</span>
        </div>
        
        <h1>Chronicle Keepers' Command Center</h1>
        
        <div class="admin-nav">
            <a href="index.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="questions.php"><i class="fas fa-question-circle"></i> Manage Questions</a>
            <a href="results.php"><i class="fas fa-scroll"></i> View Results</a>
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Leave Archives</a>
        </div>
        
        <div class="dashboard-stats">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-value"><?php echo $total_users; ?></div>
                <div class="stat-label">Registered Scholars</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-question"></i></div>
                <div class="stat-value"><?php echo $total_questions; ?></div>
                <div class="stat-label">Historical Queries</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-scroll"></i></div>
                <div class="stat-value"><?php echo $total_results; ?></div>
                <div class="stat-label">Completed Trials</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-star"></i></div>
                <div class="stat-value"><?php echo $avg_score; ?></div>
                <div class="stat-label">Average Score</div>
            </div>
        </div>
        
        <div class="recent-results">
            <h2><i class="fas fa-history"></i> Recent Trials</h2>
            <table>
                <thead>
                    <tr>
                        <th>Scholar</th>
                        <th>Score</th>
                        <th>When</th>
                        <th>Performance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_results as $result): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($result['username']); ?></td>
                            <td><?php echo $result['score']; ?>/<?php echo $result['total_questions']; ?></td>
                            <td><?php echo date('M j, Y g:i a', strtotime($result['taken_at'])); ?></td>
                            <td>
                                <?php 
                                $percentage = round(($result['score'] / $result['total_questions']) * 100);
                                $class = $percentage >= 70 ? 'success' : ($percentage >= 50 ? 'warning' : 'danger');
                                ?>
                                <div class="progress-bar">
                                    <div class="progress <?php echo $class; ?>" style="width: <?php echo $percentage; ?>%">
                                        <?php echo $percentage; ?>%
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="quick-actions">
            <h2><i class="fas fa-bolt"></i> Quick Actions</h2>
            <div class="actions-grid">
                <a href="questions.php?action=add" class="action-card">
                    <i class="fas fa-plus-circle"></i>
                    <span>Add New Question</span>
                </a>
                <a href="results.php" class="action-card">
                    <i class="fas fa-chart-bar"></i>
                    <span>View All Results</span>
                </a>
                <a href="../quiz.php" class="action-card">
                    <i class="fas fa-question-circle"></i>
                    <span>Take the Quiz</span>
                </a>
                <a href="#" class="action-card" onclick="alert('Feature coming soon!')">
                    <i class="fas fa-download"></i>
                    <span>Export Data</span>
                </a>
            </div>
        </div>
        
        <div class="footer">
            <p>Only the wise may enter these halls</p>
            <p>© <?php echo date('Y'); ?> History Archives - Restricted Access</p>
        </div>
    </div>
</body>
</html>