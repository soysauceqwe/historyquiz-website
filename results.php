<?php
require_once '../includes/auth.php';
require_once '../includes/config.php';
require_once '../includes/functions.php';

redirectIfNotLoggedIn();
redirectIfNotAdmin();

// Pagination
$results_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page - 1) * $results_per_page;

// Filtering
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'recent';

$sort_sql = "ORDER BY r.taken_at DESC";
switch ($sort) {
    case 'oldest':
        $sort_sql = "ORDER BY r.taken_at ASC";
        break;
    case 'highest':
        $sort_sql = "ORDER BY r.score DESC";
        break;
    case 'lowest':
        $sort_sql = "ORDER BY r.score ASC";
        break;
}

$search_sql = '';
$params = [];
if ($search !== '') {
    $search_sql = "WHERE u.username LIKE :search";
    $params[':search'] = "%$search%";
}

// Get total results count
$count_stmt = $pdo->prepare(
    "SELECT COUNT(*) FROM quiz_results r JOIN users u ON r.user_id = u.id $search_sql"
);
$count_stmt->execute($params);
$total_results = $count_stmt->fetchColumn();
$total_pages = ceil($total_results / $results_per_page);

// Get results with pagination
$sql = "SELECT r.id, u.username, r.score, r.total_questions, r.taken_at 
        FROM quiz_results r 
        JOIN users u ON r.user_id = u.id 
        $search_sql 
        $sort_sql 
        LIMIT :start, :limit";
$stmt = $pdo->prepare($sql);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':start', $start_from, PDO::PARAM_INT);
$stmt->bindValue(':limit', $results_per_page, PDO::PARAM_INT);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get performance stats
$stats = $pdo->query(
    "SELECT 
        AVG(score) as avg_score,
        MAX(score) as max_score,
        MIN(score) as min_score,
        COUNT(DISTINCT user_id) as unique_users
     FROM quiz_results"
)->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Results - History Quiz</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=MedievalSharp&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="header-decoration">
            <span class="history-icon">📜</span> Trial Records <span class="history-icon">🔍</span>
        </div>
        
        <h1>Scholars' Performance Archives</h1>
        
        <div class="admin-nav">
            <a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="questions.php"><i class="fas fa-question-circle"></i> Questions</a>
            <a href="results.php" class="active"><i class="fas fa-scroll"></i> Results</a>
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
        
        <div class="results-stats">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-calculator"></i></div>
                <div class="stat-value"><?php echo round($stats['avg_score'], 2); ?></div>
                <div class="stat-label">Average Score</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-trophy"></i></div>
                <div class="stat-value"><?php echo $stats['max_score']; ?></div>
                <div class="stat-label">Highest Score</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-heartbeat"></i></div>
                <div class="stat-value"><?php echo $stats['min_score']; ?></div>
                <div class="stat-label">Lowest Score</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-user-friends"></i></div>
                <div class="stat-value"><?php echo $stats['unique_users']; ?></div>
                <div class="stat-label">Unique Scholars</div>
            </div>
        </div>
        
        <div class="results-filter">
            <form method="get" action="results.php">
                <div class="form-row">
                    <div class="form-group">
                        <label for="search"><i class="fas fa-search"></i> Search Scholar:</label>
                        <input type="text" id="search" name="search" placeholder="Enter username..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="form-group">
                        <label for="sort"><i class="fas fa-sort"></i> Sort By:</label>
                        <select id="sort" name="sort">
                            <option value="recent" <?php if ($sort === 'recent') echo 'selected'; ?>>Most Recent</option>
                            <option value="oldest" <?php if ($sort === 'oldest') echo 'selected'; ?>>Oldest First</option>
                            <option value="highest" <?php if ($sort === 'highest') echo 'selected'; ?>>Highest Score</option>
                            <option value="lowest" <?php if ($sort === 'lowest') echo 'selected'; ?>>Lowest Score</option>
                        </select>
                    </div>
                    <button type="submit" class="btn"><i class="fas fa-filter"></i> Apply</button>
                </div>
            </form>
        </div>
        
        <div class="results-table">
            <table>
                <thead>
                    <tr>
                        <th>Record ID</th>
                        <th>Scholar</th>
                        <th>Score</th>
                        <th>Percentage</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $result): 
                        $percentage = round(($result['score'] / $result['total_questions']) * 100);
                        $grade = $percentage >= 90 ? 'A' : 
                                ($percentage >= 80 ? 'B' : 
                                ($percentage >= 70 ? 'C' : 
                                ($percentage >= 60 ? 'D' : 'F')));
                    ?>
                        <tr>
                            <td><?php echo $result['id']; ?></td>
                            <td><?php echo htmlspecialchars($result['username']); ?></td>
                            <td><?php echo $result['score']; ?>/<?php echo $result['total_questions']; ?></td>
                            <td>
                                <span class="grade-badge grade-<?php echo strtolower($grade); ?>">
                                    <?php echo $percentage; ?>% (<?php echo $grade; ?>)
                                </span>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($result['taken_at'])); ?></td>
                            <td>
                                <a href="result_detail.php?result_id=<?php echo $result['id']; ?>" class="btn-small">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="results.php?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo $sort; ?>" class="btn">
                    <i class="fas fa-arrow-left"></i> Previous
                </a>
            <?php endif; ?>
            
            <span>Page <?php echo $page; ?> of <?php echo $total_pages; ?></span>
            
            <?php if ($page < $total_pages): ?>
                <a href="results.php?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo $sort; ?>" class="btn">
                    Next <i class="fas fa-arrow-right"></i>
                </a>
            <?php endif; ?>
        </div>
        
        <div class="footer">
            <p>Knowledge carefully recorded for posterity</p>
            <p>© <?php echo date('Y'); ?> History Archives - Restricted Access</p>
        </div>
    </div>
</body>
</html>
