<?php
require_once 'includes/auth.php';
require_once 'includes/config.php';

$error = '';



$error = '';
$username = '';
$password = '';

if (isLoggedIn()) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    if (login($username, $password)) {
        // Check if user is admin, then redirect to admin dashboard
        if ($_SESSION['is_admin'] == 1) {
            header("Location: admin/index.php");  // Admin dashboard
        } else {
            header("Location: index.php");  // User dashboard
        }
        exit();
    } else {
        $error = "Invalid login credentials.";
    }
}



// Check for registration success message
$success = $_SESSION['success'] ?? '';
unset($_SESSION['success']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - History Quiz</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=MedievalSharp&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="header-decoration">
            <span class="history-icon">🗝</span> Access the Archives <span class="history-icon">📜</span>
        </div>
        
        <h1>Login to Your Account</h1>
        
        <?php if (!empty($error)): ?>
            <div class="alert error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="post" class="auth-form">
    <div class="form-group float-label">
        <input type="text" name="username" id="username" class="form-control with-icon" placeholder=" " required />
        <label for="username">Username</label>
        <span class="input-icon"><i class="fas fa-user-graduate"></i></span>
    </div>

    <div class="form-group float-label">
        <input type="password" name="password" id="password" class="form-control with-icon" placeholder=" " required />
        <label for="password">Password</label>
        <span class="input-icon"><i class="fas fa-lock"></i></span>
    </div>

    <div style="text-align: center; margin: 30px 0;">
        <button type="submit" class="btn">
            <i class="fas fa-sign-in-alt"></i> Enter the Archives
        </button>
    </div>
</form>

        
        <div style="text-align: center; margin: 20px 0;">
            <p>Don't have an account? <a href="register.php" style="color: #8b4513; text-decoration: underline;">Enroll as a Scholar</a></p>
            <p><a href="index.php" style="color: #5c3a21;"><i class="fas fa-arrow-left"></i> Return to the Great Hall</a></p>
        </div>
        
        <div class="footer">
            <p>Knowledge is the treasure of the wise</p>
            <p>© <?php echo date('Y'); ?> History Archives</p>
        </div>
    </div>

    <script>
        // Toggle password visibility
        function togglePassword(id) {
            const input = document.getElementById(id);
            const icon = document.querySelector(`#${id} + .password-toggle i`);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        // Add validation on blur
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('blur', function() {
                if (this.value.trim() === '' && this.required) {
                    this.classList.add('is-invalid');
                    const feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    feedback.textContent = 'This field is required';
                    this.parentNode.appendChild(feedback);
                } else {
                    this.classList.remove('is-invalid');
                    const feedback = this.parentNode.querySelector('.invalid-feedback');
                    if (feedback) feedback.remove();
                }
            });
        });
    </script>
</body>
</html>