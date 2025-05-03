<?php
require_once 'includes/auth.php';
require_once 'includes/config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    // Validate inputs
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error = 'All fields are required.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        // Check if username exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);

        if ($stmt->rowCount() > 0) {
            $error = 'Username already taken.';
        } else {
            // Register the user
            if (register($username, $password)) {
                $_SESSION['success'] = 'Registration successful! Please login.';
                header("Location: login.php");
                exit();
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - History Quiz</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=MedievalSharp&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="header-decoration">
            <span class="history-icon">✍️</span> Scholar Enrollment <span class="history-icon">📖</span>
        </div>

        <h1>Become a Scholar</h1>

        <?php if (!empty($error)): ?>
            <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="post" class="auth-form">
            <div class="form-group float-label">
                <input type="text" name="username" id="username" class="form-control with-icon" placeholder=" " required />
                <label for="username">Choose Your Scholar Name</label>
                <span class="input-icon"><i class="fas fa-user-graduate"></i></span>
            </div>

            <div class="form-group float-label">
                <input type="password" name="password" id="password" class="form-control with-icon" placeholder=" " required />
                <label for="password">Secret Knowledge Phrase</label>
                <span class="input-icon"><i class="fas fa-key"></i></span>
            </div>

            <div class="form-group float-label">
                <input type="password" name="confirm_password" id="confirm_password" class="form-control with-icon" placeholder=" " required />
                <label for="confirm_password">Repeat Your Phrase</label>
                <span class="input-icon"><i class="fas fa-key"></i></span>
            </div>

            <div class="form-group">
                <div class="password-strength">
                    <div class="strength-meter">
                        <div class="strength-bar"></div>
                    </div>
                    <div class="strength-text">Phrase Strength: <span>Weak</span></div>
                </div>
            </div>

            <div style="text-align: center; margin: 30px 0;">
                <button type="submit" class="btn">
                    <i class="fas fa-feather-alt"></i> Seal Your Enrollment
                </button>
            </div>
        </form>

        <div style="text-align: center; margin: 20px 0;">
            <p>Already enrolled? <a href="login.php" style="color: #8b4513; text-decoration: underline;">Access the Archives</a></p>
            <p><a href="index.php" style="color: #5c3a21;"><i class="fas fa-arrow-left"></i> Return to the Great Hall</a></p>
        </div>

        <div class="footer">
            <p>By enrolling, you agree to safeguard our historical knowledge</p>
            <p>© <?php echo date('Y'); ?> History Archives</p>
        </div>
    </div>

    <script>
        // Password strength checker
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.querySelector('.strength-bar');
            const strengthText = document.querySelector('.strength-text span');

            strengthBar.style.width = '0%';
            strengthBar.style.backgroundColor = '#8b0000';
            strengthText.textContent = 'Weak';

            if (password.length === 0) return;

            let strength = 0;
            if (password.length >= 6) strength += 25;
            if (password.length >= 8) strength += 25;
            if (/[A-Z]/.test(password)) strength += 15;
            if (/[0-9]/.test(password)) strength += 15;
            if (/[^A-Za-z0-9]/.test(password)) strength += 20;

            strengthBar.style.width = strength + '%';

            if (strength < 40) {
                strengthBar.style.backgroundColor = '#8b0000';
                strengthText.textContent = 'Weak';
            } else if (strength < 70) {
                strengthBar.style.backgroundColor = '#8b4513';
                strengthText.textContent = 'Moderate';
            } else {
                strengthBar.style.backgroundColor = '#556b2f';
                strengthText.textContent = 'Strong';
            }
        });

        // Confirm password live checker
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;

            if (confirmPassword !== password && confirmPassword.length > 0) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
    </script>
</body>
</html>
