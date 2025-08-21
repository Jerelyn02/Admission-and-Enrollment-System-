<?php
include("php/connection.php");
session_start();

$msg = '';

if (isset($_POST['submit'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // 1) Fetch by email only (never include password in the SQL)
    $sql = "SELECT * FROM accounts WHERE email = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res && $res->num_rows === 1) {
            $user = $res->fetch_assoc();
            $stored = $user['password'];

            $auth_ok = false;

            // 2) First try the secure way
            if (password_verify($password, $stored)) {
                $auth_ok = true;

                // Optional: rehash if algorithm/cost changed
                if (password_needs_rehash($stored, PASSWORD_DEFAULT)) {
                    $newHash = password_hash($password, PASSWORD_DEFAULT);
                    $upd = $conn->prepare("UPDATE accounts SET password=? WHERE id=?");
                    $upd->bind_param("si", $newHash, $user['id']);
                    $upd->execute();
                    $upd->close();
                }
            } else {
                // 3) Legacy fallback: if the DB has a plain password, allow once and upgrade to hash
                // (Plain text is usually short and doesn't start with $2y$ / $argon2)
                $looks_hashed = str_starts_with($stored, '$2y$') || str_starts_with($stored, '$argon2');
                if (!$looks_hashed && $stored === $password) {
                    $auth_ok = true;
                    // Upgrade to hash
                    $newHash = password_hash($password, PASSWORD_DEFAULT);
                    $upd = $conn->prepare("UPDATE accounts SET password=? WHERE id=?");
                    $upd->bind_param("si", $newHash, $user['id']);
                    $upd->execute();
                    $upd->close();
                }
            }

            if ($auth_ok) {
                // 4) Check verification
                if ((int)$user['email_verified'] === 0) {
                    $msg = "Please verify your email first.";
                } else {
                    // 5) Login OK → set sessions and redirect by role
                    $_SESSION['id'] = $user['id'];
                    $_SESSION['email'] = $user['email'];

                    if ($user['user_type'] === 'admin') {
                        $_SESSION['admin'] = $user['email'];
                        header("Location: adminportal/admindashboard.php");
                        exit;
                    } else {
                        $_SESSION['user'] = $user['email'];
                        header("Location: userportal/userdashboard.php");
                        exit;
                    }
                }
            } else {
                $msg = "Incorrect email or password!";
            }
        } else {
            $msg = "Incorrect email or password!";
        }

        $stmt->close();
    } else {
        $msg = "Server error. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Page</title>
  <link rel="stylesheet" href="css/logreg.css">
</head>
<body>
  <div class="hero">
    <div class="left">
      <img src="image/logo_new2.png?v=1" alt="Imus International University" class="login-logo">
    </div>
    <div class="right">
      <form action="" method="post">
        <h2>Welcome!</h2>
        <p class="msg"><?php echo $msg; ?></p>

        <div class="form-group">
          <input type="email" name="email" placeholder="Enter your email" class="form-control" required>
        </div>
        <div class="form-group">
          <input type="password" name="password" placeholder="Enter your password" class="form-control" required>
        </div>

        <button class="btn" name="submit">Login Now</button>

        <p style="margin-top: 10px; text-align: center;">
          Don't have an account? <a href="signup.php">Sign Up</a>
        </p>
      </form>
    </div>
  </div>
</body>
</html>
