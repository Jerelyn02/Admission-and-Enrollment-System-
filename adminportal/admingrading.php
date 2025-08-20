<?php
include('..\php\connection.php');
session_start();

// Redirect if admin not logged in
if(!isset($_SESSION['admin'])){
    header("Location: ../login.php");
    exit();
}

// Function to calculate user's latest exam status
function getExamStatus($conn, $username) {
    // Fetch latest submitted attempt
    $stmt = $conn->prepare("SELECT * FROM exam_attempts WHERE email=? AND is_submitted=1 ORDER BY ended_at DESC LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $attempt_result = $stmt->get_result();
    if($attempt_result->num_rows === 0) return 0; // Pending if no submitted attempt

    $attempt = $attempt_result->fetch_assoc();
    $attempt_id = $attempt['attempt_id'];
    $exam_id = $attempt['exam_id'];

    // Fetch all questions for this exam
    $q_stmt = $conn->prepare("SELECT question_no, answer FROM questions WHERE exam_id=?");
    $q_stmt->bind_param("i", $exam_id);
    $q_stmt->execute();
    $q_result = $q_stmt->get_result();

    $total = 0;
    $correct = 0;

    // Fetch user's answers
    $a_stmt = $conn->prepare("SELECT question_no, answer FROM exam_answers WHERE attempt_id=?");
    $a_stmt->bind_param("i", $attempt_id);
    $a_stmt->execute();
    $a_result = $a_stmt->get_result();

    $user_answers = [];
    while($row = $a_result->fetch_assoc()){
        $user_answers[$row['question_no']] = $row['answer'];
    }

    while($row = $q_result->fetch_assoc()){
        $qno = $row['question_no'];
        $user_ans = $user_answers[$qno] ?? '';
        if($user_ans === $row['answer']) $correct++;
        $total++;
    }

    if($total === 0) return 0; // Pending if no questions found

    // Return 1 = Passed, 2 = Failed
    return ($correct >= ceil($total*0.5)) ? 1 : 2;
}

// Fetch all applicants with their status based on exam submission
$applicants_stmt = $conn->prepare("
    SELECT a.username, 
           CONCAT(p.lastname, ', ', p.firstname, ' ', COALESCE(p.middlename, '')) AS fullname,
           ad.entry, ad.program,
           a.admin_confirmed
    FROM application_status a
    JOIN personal_info p ON a.username=p.username
    JOIN admission_info ad ON a.username=ad.username
");
$applicants_stmt->execute();
$applicants = $applicants_stmt->get_result();

// Split applicants into sections
$pending = [];
$passed = [];
$failed = [];

while($row = $applicants->fetch_assoc()){
    $status = getExamStatus($conn, $row['username']);
    if($status === 0) $pending[] = $row;
    elseif($status === 1) $passed[] = $row;
    else $failed[] = $row;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Grading Management</title>
<link rel="stylesheet" href="..\css\adminEC.css">
<style>
table { width: 100%; border-collapse: collapse; margin-bottom: 2rem; }
th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
th { background: #f2f2f2; }
.check-link { color: blue; text-decoration: underline; cursor: pointer; }
.exam-table { margin-bottom: 40px; }
h2 { margin-bottom: 10px; color: #0c131bff; }
</style>
</head>
<body>

<div class="sidebar">
  <div>
    <div class="logo">ADMIN PANEL</div>
    <div class="nav-top">
      <button class="nav-btn" onclick="window.location.href='admindashboard.php'">Dashboard</button>
      <button class="nav-btn" onclick="window.location.href='adminadmission.php'">Manage Admission</button>
      <button class="nav-btn" onclick="window.location.href='applicants_info.php'">Applicants Information</button>
      <button class="nav-btn" onclick="window.location.href='admingrading.php'">Grade Assessment</button>
      <button class="nav-btn" onclick="window.location.href='exam_category.php'">Manage Exam Details</button>
    </div>
  </div>
</div>

<div class="main">
  <div class="topbar">
    <button class="backbtn" onclick="history.back()">&larr; Back</button>
    <div class="center"></div>
    <div class="right">
      <p>Welcome, <span><?php echo $_SESSION['admin']; ?></span></p>
      <a href="..\php\logout.php"><button class="btn font-weight-bold">Logout</button></a>
    </div>
  </div>

  <div class="subheader">
    <h2>Grading Management</h2>
  </div>

  <div class="content">
    <div class="exam-container">

      <!-- Pending Examinees -->
      <div class="exam-table">
        <h2>Pending Examinees</h2>
        <table>
          <tr><th>Username</th><th>Full Name</th><th>Entry</th><th>Program</th></tr>
          <?php foreach($pending as $row): ?>
          <tr>
              <td><?php echo htmlspecialchars($row['username']); ?></td>
              <td><?php echo htmlspecialchars($row['fullname']); ?></td>
              <td><?php echo htmlspecialchars($row['entry']); ?></td>
              <td><?php echo htmlspecialchars($row['program']); ?></td>
          </tr>
          <?php endforeach; ?>
        </table>
      </div>

      <!-- Passed Examinees -->
      <div class="exam-table">
        <h2>Passed Examinees</h2>
        <table>
          <tr><th>Username</th><th>Full Name</th><th>Entry</th><th>Program</th><th>Action</th></tr>
          <?php foreach($passed as $row): ?>
          <tr>
              <td><?php echo htmlspecialchars($row['username']); ?></td>
              <td><?php echo htmlspecialchars($row['fullname']); ?></td>
              <td><?php echo htmlspecialchars($row['entry']); ?></td>
              <td><?php echo htmlspecialchars($row['program']); ?></td>
              <td>
                <?php if ($row['admin_confirmed']): ?>
                    <span style="color: green; font-weight: bold;">Confirmed</span>
                <?php else: ?>
                    <a class="check-link" href="view_exam.php?username=<?php echo urlencode($row['username']); ?>">Confirm</a>
                <?php endif; ?>
              </td>
          </tr>
          <?php endforeach; ?>
        </table>
      </div>

      <!-- Failed Examinees -->
      <div class="exam-table">
        <h2>Failed Examinees</h2>
        <table>
          <tr><th>Username</th><th>Full Name</th><th>Entry</th><th>Program</th><th>Action</th></tr>
          <?php foreach($failed as $row): ?>
          <tr>
              <td><?php echo htmlspecialchars($row['username']); ?></td>
              <td><?php echo htmlspecialchars($row['fullname']); ?></td>
              <td><?php echo htmlspecialchars($row['entry']); ?></td>
              <td><?php echo htmlspecialchars($row['program']); ?></td>
              <td>
                <?php if ($row['admin_confirmed']): ?>
                    <span style="color: green; font-weight: bold;">Confirmed</span>
                <?php else: ?>
                    <a class="check-link" href="view_exam.php?username=<?php echo urlencode($row['username']); ?>">Confirm</a>
                <?php endif; ?>
              </td>
          </tr>
          <?php endforeach; ?>
        </table>
      </div>

    </div>
  </div>
</div>

</body>
</html>
