<?php
include("php/connection.php");
$msg = '';

if(isset($_POST['submit'])){
    $email = $_POST['email'];
    $code = $_POST['code'];

    $query = "SELECT * FROM accounts WHERE email='$email' AND verification_code='$code'";
    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) > 0){
        mysqli_query($conn, "UPDATE accounts SET email_verified=1, verification_code=NULL WHERE email='$email'");
        $msg = "Email verified successfully! You can now <a href='login.php'>login</a>.";
    } else {
        $msg = "Invalid code. Please try again.";
    }
}
?>

<form method="post" action="">
    <h2>Email Verification</h2>
    <p><?php echo $msg; ?></p>
    <input type="email" name="email" placeholder="Enter your email" required>
    <input type="text" name="code" placeholder="Enter verification code" required>
    <button name="submit">Verify</button>
</form>
