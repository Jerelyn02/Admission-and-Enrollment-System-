<?php
include('../php/connection.php');
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['username'])) {
    echo "Invalid request.";
    exit();
}

$username = $_GET['username'];

$admission = $conn->query("SELECT * FROM admission_info WHERE username = '$username'")->fetch_assoc();
$personal = $conn->query("SELECT * FROM personal_info WHERE username = '$username'")->fetch_assoc();
$family = $conn->query("SELECT * FROM family_bg WHERE username = '$username'")->fetch_assoc();
$education = $conn->query("SELECT * FROM education_bg WHERE username = '$username'")->fetch_assoc();
$medical = $conn->query("SELECT * FROM med_his_info WHERE username = '$username'")->fetch_assoc();
$status = $conn->query("SELECT * FROM application_status WHERE username = '$username'")->fetch_assoc();
$control = $conn->query("SELECT control_number FROM check_status WHERE username = '$username'")->fetch_assoc();

$entry = $admission['entry'] ?? '';
$control_number = $control['control_number'] ?? 'N/A';

$files = []; // default empty

switch ($entry) {
    case 'new': // Freshmen
        $files_result = $conn->query("SELECT * FROM freshmen_files WHERE username = '$username'");
        break;
    case 'transferee': // Shiftee
        $files_result = $conn->query("SELECT * FROM transferee_files WHERE username = '$username'");
        break;
    case 'returnee': // Re-admission
        $files_result = $conn->query("SELECT * FROM returnee_files WHERE username = '$username'");
        break;
        case 'shiftee':
        $files_result = $conn->query("SELECT * FROM shiftee_files WHERE username = '$username'");
        break;
    default:
        $files_result = false;
}

if ($files_result && $files_result->num_rows > 0) {
    $files = $files_result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_status = isset($_POST['accept']) ? 1 : (isset($_POST['reject']) ? 3 : 0);
    $stmt = $conn->prepare("INSERT INTO application_status (username, status) VALUES (?, ?) ON DUPLICATE KEY UPDATE status = VALUES(status)");
    $stmt->bind_param("si", $username, $new_status);
    $stmt->execute();
    $stmt->close();

    header("Location: adminadmission.php");
    exit();
}

function formatLabel($key) {
    $map = [
        'gurdianname' => "Guardian's Name",
        'gnumber' => "Guardian's Number",
        'goccupation' => "Guardian's Occupation",
        'fathername' => "Father's Name",
        'fnumber' => "Father's Number",
        'foccupation' => "Father's Occupation",
        'mothername' => "Mother's Name",
        'mnumber' => "Mother's Number",
        'moccupation' => "Mother's Occupation",
        'fam_month_inc' => 'Family Monthly Income',
        'numsibling' => 'Number of Siblings',
        'birthorder' => 'Birth Order',
        'soloparent' => 'Solo Parent',
        'fam_work_abroad' => 'Family Working Abroad',
        'elemname' => 'Elementary School Name',
        'elemaddress' => 'Elementary Address',
        'elemyear' => 'Elementary Year Graduated',
        'elemtype' => 'Elementary Type',
        'midname' => 'Junior High School Name',
        'midaddress' => 'Junior High Address',
        'midyear' => 'Junior High Year Graduated',
        'midtype' => 'Junior High Type',
        'seniorname' => 'Senior High School Name',
        'senioraddress' => 'Senior High Address',
        'senioryear' => 'Senior High Year Graduated',
        'seniortype' => 'Senior High Type',
        'vocname' => 'Vocational School Name',
        'vocaddress' => 'Vocational Address',
        'vocyear' => 'Vocational Year Graduated',
        'voctype' => 'Vocational Type'
    ];
    return $map[$key] ?? ucfirst(str_replace('_', ' ', $key));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Profile</title>
 <link rel="stylesheet" href="../css/adminEC.css">
  <style>
  </style>
</head>
<body>
  
  <!-- Sidebar -->
<div class="sidebar">
  <div>
    <div class="logo">ADMIN PANEL</div>
    <div class="nav-top">
      <button class="nav-btn" onclick="window.location.href='admindashboard.php'">Dashboard</button>
      <button class="nav-btn" onclick="window.location.href='adminadmission.php'">Manage Admission</button>
      <button class="nav-btn" onclick="window.location.href='applicants_info.php'">Applicants Information</button> <!-- New button -->
      <button class="nav-btn" onclick="window.location.href='admingrading.php'">Grade Assessment</button>
      <button class="nav-btn" onclick="window.location.href='exam_category.php'">Manage Exam Details</button>
    </div>
  </div>
</div>


  <div class="main">
    <div class="topbar">
  <div class="left">
    <button class="backbtn" onclick="history.back()">&larr; Back</button>
  </div>
  <div class="center"></div>
  <div class="right">
    <p>Welcome, <span><?php echo htmlspecialchars($_SESSION['admin']); ?></span></p>
    <a href="../php/logout.php"><button class="btn font-weight-bold">Logout</button></a>
  </div>
</div>


    <div class="content">
      <h2>Applicant Profile</h2>
      <p><strong>Username:</strong> <?php echo htmlspecialchars($username); ?></p>
      <p><strong>Control Number:</strong> <?php echo htmlspecialchars($control_number); ?></p>

      <h3>Personal Information</h3>
      <div class="grid-2">
      <?php foreach ($personal as $k => $v) echo "<p><strong>" . formatLabel($k) . ":</strong> " . htmlspecialchars($v) . "</p>"; ?>
      </div>

      <h3>Admission Information</h3>
      <div class="grid-2">
      <?php foreach ($admission as $k => $v) echo "<p><strong>" . formatLabel($k) . ":</strong> " . htmlspecialchars($v) . "</p>"; ?>
      </div>

      <h3>Family Background</h3>
      <div class="grid-2">
      <?php foreach ($family as $k => $v) echo "<p><strong>" . formatLabel($k) . ":</strong> " . htmlspecialchars($v) . "</p>"; ?>
      </div>

      <h3>Educational Background</h3>
      <div class="grid-2">
      <?php foreach ($education as $k => $v) echo "<p><strong>" . formatLabel($k) . ":</strong> " . htmlspecialchars($v) . "</p>"; ?>
      </div>

      <h3>Medical History</h3>
      <div class="grid-2">
      <?php foreach ($medical as $k => $v) echo "<p><strong>" . formatLabel($k) . ":</strong> " . htmlspecialchars($v) . "</p>"; ?>
      </div>

      <h3>Uploaded Files</h3>
      <div class="grid-2">
      <?php if ($files): foreach ($files as $label => $file): 
        if (!in_array($label, ['id', 'submitted_at', 'username', 'control_number']) && $file): ?>
          <p><strong><?php echo formatLabel($label); ?>:</strong> <a href="../uploads/<?php echo htmlspecialchars($file); ?>" target="_blank">View</a></p>
      <?php endif; endforeach; else: echo "<p>No files found.</p>"; endif; ?>
      </div>

      <h3>Application Status</h3>
      <?php
        $stat = $status['status'] ?? 0;
        $statText = $stat == 1 ? 'Accepted' : ($stat == 3 ? 'Rejected' : 'Pending');
        $statClass = $stat == 1 ? 'accepted' : ($stat == 3 ? 'rejected' : 'pending');
      ?>
      <p>Status: <span class="status <?php echo $statClass; ?>"><?php echo $statText; ?></span></p>

      <?php if (!isset($status['status']) || $status['status'] == 0): ?>
      <form method="POST">
        <button type="submit" name="accept">Accept</button>
        <button type="submit" name="reject">Reject</button>
      </form>
      <?php endif; ?>
    </div>
  </div>

</body>
</html>


