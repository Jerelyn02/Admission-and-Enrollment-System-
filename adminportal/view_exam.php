<?php 
include('..\php\connection.php');
session_start();

if (!isset($_GET['username'])) {
    die("Username not specified.");
}
$username = $_GET['username'];

// Fetch latest submitted attempt
$attempt = $conn->prepare("SELECT * FROM exam_attempts WHERE email = ? AND is_submitted = 1 ORDER BY ended_at DESC LIMIT 1");
$attempt->bind_param("s", $username);
$attempt->execute();
$attempt_result = $attempt->get_result();
if ($attempt_result->num_rows === 0) {
    die("No submitted exam found for this user.");
}
$attempt_data = $attempt_result->fetch_assoc();
$exam_id = $attempt_data['exam_id'];
$attempt_id = $attempt_data['attempt_id'];

// Fetch questions
$questions_stmt = $conn->prepare("SELECT * FROM questions WHERE exam_id = ?");
$questions_stmt->bind_param("i", $exam_id);
$questions_stmt->execute();
$questions_result = $questions_stmt->get_result();

// Fetch answers
$answers_stmt = $conn->prepare("SELECT question_no, answer FROM exam_answers WHERE attempt_id = ?");
$answers_stmt->bind_param("i", $attempt_id);
$answers_stmt->execute();
$answers_result = $answers_stmt->get_result();

$user_answers = [];
while ($row = $answers_result->fetch_assoc()) {
    $user_answers[$row['question_no']] = $row['answer'];
}

// Grade checking
$total_questions = 0;
$correct_answers = 0;
$questions = [];

while ($row = $questions_result->fetch_assoc()) {
    $qno = $row['question_no'];
    $user_answer = $user_answers[$qno] ?? 'N/A';
    $is_correct = ($user_answer === $row['answer']);
    if ($is_correct) $correct_answers++;
    $total_questions++;

    $questions[] = [
        'question_no' => $qno,
        'question' => $row['question'],
        'opt1' => $row['opt1'],
        'opt2' => $row['opt2'],
        'opt3' => $row['opt3'],
        'opt4' => $row['opt4'],
        'correct_answer' => $row['answer'],
        'user_answer' => $user_answer,
        'is_correct' => $is_correct
    ];
}

// Calculate score
$score = "$correct_answers / $total_questions";
$passed = $correct_answers >= ceil($total_questions * 0.5); // 50% passing

// Automatically update application_status grade_status based on exam result
$grade_status = $passed ? 1 : 2;
$update_status = $conn->prepare("UPDATE application_status SET grade_status=? WHERE username=?");
$update_status->bind_param("is", $grade_status, $username);
$update_status->execute();

// Admin confirmation (visual only)
$admin_confirmed = false;
$confirm_message = "";
if (isset($_GET['confirm'])) {
    $admin_confirmed = true;
    $confirm_message = $passed 
        ? "🎉 Congratulations! The user passed the exam." 
        : "ℹ️ The user did not pass the exam.";

    // Update admin_confirmed column
    $update_confirm = $conn->prepare("UPDATE application_status SET admin_confirmed = 1 WHERE username = ?");
    $update_confirm->bind_param("s", $username);
    $update_confirm->execute();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>View Exam</title>
  <link rel="stylesheet" href="..\css\adminEC.css">
  <style>
    .question { margin-bottom: 1.5rem; padding: 1rem; border: 1px solid #ccc; border-radius: 10px; }
    .correct { color: green; font-weight: bold; }
    .incorrect { color: red; font-weight: bold; }
    .answer-box { margin-top: 0.5rem; }
    .score-box { font-size: 1.2rem; margin: 1rem 0; }
    .confirm-btn { margin: 0.5rem; padding: 8px 16px; font-weight: bold; }
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
    <div class="left">
      <button class="backbtn" onclick="history.back()">&larr; Back</button>
    </div>
    <div class="center"></div>
    <div class="right">
      <p>Welcome, <span><?php echo htmlspecialchars($_SESSION['admin']); ?></span></p>
      <a href="..\php\logout.php"><button class="btn font-weight-bold">Logout</button></a>
    </div>
  </div>

  <div class="subheader">
    <div>Exam Review for <strong><?php echo htmlspecialchars($username); ?></strong></div>
  </div>

  <div class="content">
      <div class="score-box">
        <p>Score: <strong><?php echo $score; ?></strong></p>
        <p>Result: <strong style="color:<?php echo $passed ? 'green' : 'red'; ?>"><?php echo $passed ? 'Passed' : 'Failed'; ?></strong></p>
      </div>

      <?php foreach ($questions as $q): ?>
        <div class="question">
          <p><strong>Q<?php echo $q['question_no']; ?>:</strong> <?php echo htmlspecialchars($q['question']); ?></p>
          <ul class="answer-box">
            <?php for ($i = 1; $i <= 4; $i++): 
              $opt = $q["opt$i"];
              $is_user = $q['user_answer'] === $opt;
              $is_correct = $q['correct_answer'] === $opt;
              ?>
              <li style="margin-bottom: 4px;">
                <?php echo htmlspecialchars($opt); ?>
                <?php if ($is_correct): ?>
                  <span class="correct">✔ Correct Answer</span>
                <?php endif; ?>
                <?php if ($is_user && !$is_correct): ?>
                  <span class="incorrect">✖ Your Answer</span>
                <?php elseif ($is_user && $is_correct): ?>
                  <span class="correct">✔ Your Answer</span>
                <?php endif; ?>
              </li>
            <?php endfor; ?>
          </ul>
        </div>
      <?php endforeach; ?>

      <div style="margin-top: 2rem;">
        <?php if ($admin_confirmed): ?>
            <div style="padding: 10px; background-color: #e7f3fe; border-left: 5px solid <?php echo $passed ? '#4CAF50' : '#f44336'; ?>;">
                <?php echo $confirm_message; ?>
            </div>
        <?php else: ?>
            <h3>Confirm Grade:</h3>
            <a href="?username=<?php echo urlencode($username); ?>&confirm=1">
                <button class="confirm-btn" style="background-color: #2196F3; color: white;">✔ Confirm</button>
            </a>
        <?php endif; ?>
      </div>

  </div>
</div>

</body>
</html>
