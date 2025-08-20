<?php
include("php\connection.php");
session_start();

$msg='';
if(isset($_POST['submit'])){
  $email = $_POST['email'];
  $password = $_POST['password'];

  $select1 = "SELECT * FROM accounts WHERE email = '$email' AND password ='$password'";
  $select_user = mysqli_query($conn, $select1);

  if(mysqli_num_rows($select_user) > 0){
    $row1 = mysqli_fetch_assoc($select_user);
    if ($row1['password'] === $password) {
      if ($row1['user_type'] == 'user') {
        $_SESSION['user'] = $row1['email'];
        $_SESSION['id'] = $row1['id'];
        header('location:userportal\userdashboard.php');
      } elseif ($row1['user_type'] == 'admin') {
        $_SESSION['admin'] = $row1['email'];
        $_SESSION['id'] = $row1['id'];
        header('location:adminportal\admindashboard.php');
      }
      if($row['email_verified'] == 0){
    echo "Please verify your email first.";
} else {
    $_SESSION['user'] = $email;
    header("Location: dashboard.php");
}

    } else {
      $msg = "Incorrect email or password!";
    }
  } else {
    $msg = "Incorrect email or password!";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Page</title>
  <link rel="stylesheet" href="css\logreg.css">
</head>
<body>
  <div class="hero">
    <div class="left">
      <img src="image/logo_new2.png?v=1" alt="Imus International University" class="login-logo">
      <h2>Welcome!</h2>
      <p>Login Now</p>
    </div>

    <div class="right">
      <form action="" method="post">
        <h2>Login</h2>
        <p class="msg"><?php echo $msg; ?></p>

        <div class="form-group">
          <input type="email" name="email" placeholder="Enter your email" class="form-control" required>
        </div>
        <div class="form-group">
          <input type="password" name="password" placeholder="Enter your password" class="form-control" required>
        </div>

        <button class="btn" name="submit">Login Now</button>
        <p style="margin-top: 10px;">Don't have an account? <a href="register.php">Register Now</a></p>
      </form>
    </div>
  </div>
</body>
</html>
