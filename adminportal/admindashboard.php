<?php
include('..\php\connection.php');
session_start();

// Function to calculate user's exam status: 0 = Pending, 1 = Passed, 2 = Failed
function getExamStatus($conn, $username) {
    $stmt = $conn->prepare("SELECT * FROM exam_attempts WHERE email=? AND is_submitted=1 ORDER BY ended_at DESC LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $attempt_result = $stmt->get_result();
    if($attempt_result->num_rows === 0) return 0; // Pending if not submitted

    $attempt = $attempt_result->fetch_assoc();
    $attempt_id = $attempt['attempt_id'];
    $exam_id = $attempt['exam_id'];

    // Fetch questions
    $q_stmt = $conn->prepare("SELECT question_no, answer FROM questions WHERE exam_id=?");
    $q_stmt->bind_param("i", $exam_id);
    $q_stmt->execute();
    $q_result = $q_stmt->get_result();

    // Fetch user's answers
    $a_stmt = $conn->prepare("SELECT question_no, answer FROM exam_answers WHERE attempt_id=?");
    $a_stmt->bind_param("i", $attempt_id);
    $a_stmt->execute();
    $a_result = $a_stmt->get_result();

    $user_answers = [];
    while($row = $a_result->fetch_assoc()){
        $user_answers[$row['question_no']] = $row['answer'];
    }

    $total = 0;
    $correct = 0;
    while($row = $q_result->fetch_assoc()){
        $qno = $row['question_no'];
        $user_ans = $user_answers[$qno] ?? '';
        if($user_ans === $row['answer']) $correct++;
        $total++;
    }

    if($total === 0) return 0; // Pending if no questions

    return ($correct >= ceil($total * 0.5)) ? 1 : 2; // 50% passing threshold
}

// Fetch all applicants
$applicants_result = mysqli_query($conn, "SELECT username FROM application_status");

$pendingGrades = 0;
$passed = 0;
$failed = 0;

while($applicant = $applicants_result->fetch_assoc()){
    $status = getExamStatus($conn, $applicant['username']);
    if($status === 0) $pendingGrades++;
    elseif($status === 1) $passed++;
    else $failed++;
}

// Other counts (admission pending and uploaded exams)
$pendingAdmission = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM application_status WHERE status = 0"));
$uploadedExams = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM uploaded_exams"));
$totalApplicants = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM application_status"));
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="..\css\admin.css">
  
</head>
<body>
<style>
    .dashboard-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      padding: 30px;
    }
    .dashboard-card {
      background-color: #f1f5fb;
      border-left: 6px solid #007bff;
      padding: 20px;
      border-radius: 10px;
      cursor: pointer;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .dashboard-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 15px rgba(0,0,0,0.1);
    }
    .dashboard-title {
      font-size: 18px;
      font-weight: bold;
      color: #333;
    }
    .dashboard-number {
      font-size: 32px;
      color: #007bff;
      margin-top: 10px;
    }
  </style>
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

<!-- Main area -->
<div class="main">
  <!-- Topbar -->
  <div class="topbar">
    <div class="left"></div>
    <div class="center"></div>
    <div class="right">
      <p>Welcome, <span><?php echo $_SESSION['admin']; ?></span></p>
      <a href="..\php\logout.php"><button class="btn font-weight-bold">Logout</button></a>
    </div>
  </div>

  <!-- Dashboard Cards -->
  <div class="content">
    <div class="dashboard-container">
      <div class="dashboard-card" onclick="window.location.href='adminadmission.php'">
        <div class="dashboard-title">Pending Admission Applicants</div>
        <div class="dashboard-number"><?php echo $pendingAdmission; ?></div>
      </div>
      
      <div class="dashboard-card" onclick="window.location.href='admingrading.php'">
        <div class="dashboard-title">Pending Examinees</div>
        <div class="dashboard-number"><?php echo $pendingGrades; ?></div>
      </div>

      <div class="dashboard-card" onclick="window.location.href='exam_category.php'">
        <div class="dashboard-title">Uploaded Exams</div>
        <div class="dashboard-number"><?php echo $uploadedExams; ?></div>
      </div>

      <div class="dashboard-card" onclick="window.location.href='admingrading.php'">
        <div class="dashboard-title">Passed Examinees</div>
        <div class="dashboard-number"><?php echo $passed; ?></div>
      </div>

      <div class="dashboard-card" onclick="window.location.href='admingrading.php'">
        <div class="dashboard-title">Failed Examinees</div>
        <div class="dashboard-number"><?php echo $failed; ?></div>
      </div>

      <div class="dashboard-card" onclick="window.location.href='applicants_info.php'">
  <div class="dashboard-title">Applicants Information</div>
  <?php 
    // Count all applicants
    $totalApplicants = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM application_status"));
  ?>
  <div class="dashboard-number"><?php echo $totalApplicants; ?></div>
</div>

    </div>
  </div>
</div>

</body>
</html>
