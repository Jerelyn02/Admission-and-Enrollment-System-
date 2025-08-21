<?php
include("php/connection.php");

$message = "";
$success = false;

if (isset($_GET['code'])) {
    $code = $_GET['code'];
    $check = mysqli_query($conn, "SELECT * FROM accounts WHERE verification_code='$code' AND email_verified=0");

    if (mysqli_num_rows($check) > 0) {
        mysqli_query($conn, "UPDATE accounts SET email_verified=1 WHERE verification_code='$code'");
        $message = "Your email has been verified successfully!";
        $success = true;
    } else {
        $message = "⚠️ Invalid or already verified link.";
    }
} else {
    $message = "⚠️ Invalid request.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #00316E;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .verify-box {
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            text-align: center;
            max-width: 400px;
            width: 100%;
            box-shadow: 0px 6px 16px rgba(0,0,0,0.2);
        }
        h1 {
            color: #00316E;
            margin-bottom: 15px;
        }
        p {
            font-size: 16px;
            margin: 10px 0;
        }
        .success {
            color: #16a34a;
            font-weight: bold;
        }
        .error {
            color: #dc2626;
            font-weight: bold;
        }
        a {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 18px;
            background: #2563EB;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
            transition: 0.3s;
        }
        a:hover {
            background: #1e40af;
        }
    </style>
</head>
<body>
    <div class="verify-box">
        <h1>Email Verification</h1>
        <p class="<?php echo $success ? 'success' : 'error'; ?>">
            <?php echo $message; ?>
        </p>
        <?php if ($success): ?>
            <a href="login.php">Login Now</a>
        <?php endif; ?>
    </div>
</body>
</html>
