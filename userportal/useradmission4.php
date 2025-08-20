<?php
ob_start();
include('..\php\connection.php');
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit();
}

$username = $_SESSION['user'];

// Fetch grade status
$stmt = $conn->prepare("SELECT grade_status FROM application_status WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$grade_status = 0;
if ($row = $result->fetch_assoc()) {
    $grade_status = $row['grade_status'];
}

// Fetch check_status for step progress
$step_stmt = $conn->prepare("SELECT * FROM check_status WHERE username = ?");
$step_stmt->bind_param("s", $username);
$step_stmt->execute();
$step_result = $step_stmt->get_result();
$status = $step_result->fetch_assoc() ?: [
    'admission_info_completed' => 0,
    'personal_info_completed' => 0,
    'family_bg_completed' => 0,
    'education_bg_completed' => 0,
    'med_his_info_completed' => 0,
    'control_number_click' => 0,
    'control_number' => '',
    'current_stage' => 0
];

// Fetch admission_info
$admission_sql = $conn->prepare("SELECT entry, type, strand, lrn, program FROM admission_info WHERE username = ?");
$admission_sql->bind_param("s", $username);
$admission_sql->execute();
$admission_result = $admission_sql->get_result();
$admission_data = $admission_result->fetch_assoc() ?: [
    'entry' => '',
    'type' => '',
    'strand' => '',
    'lrn' => '',
    'program' => ''
];

// Fetch personal_info
$personal_sql = $conn->prepare("SELECT firstname, middlename, lastname, phonenumber, birthday, birthplace FROM personal_info WHERE username = ?");
$personal_sql->bind_param("s", $username);
$personal_sql->execute();
$personal_result = $personal_sql->get_result();
$personal_data = $personal_result->fetch_assoc() ?: [
    'firstname' => '',
    'middlename' => '',
    'lastname' => '',
    'phonenumber' => '',
    'birthday' => '',
    'birthplace' => ''
];

function renderStepProgress($status) {
    $step1Complete = (
        $status['admission_info_completed'] &&
        $status['personal_info_completed'] &&
        $status['family_bg_completed'] &&
        $status['education_bg_completed'] &&
        $status['med_his_info_completed']
    );
    $step2Complete = ($status['control_number_click'] == 1 && !empty($status['control_number']));
    $step3Complete = ($status['current_stage'] >= 3);
    $step4Complete = ($status['current_stage'] >= 4);

    $steps = [
        ['label' => 'Applicant Information', 'complete' => $step1Complete],
        ['label' => 'Requirements', 'complete' => $step2Complete],
        ['label' => 'Entrance Exam', 'complete' => $step3Complete],
        ['label' => 'Exam Results', 'complete' => $step4Complete],
    ];

    echo '<section class="hero"><div class="center-wrapper"><div class="dashboard-container">';
    foreach ($steps as $i => $step) {
        $circleClass = $step['complete'] ? 'circle completed' : 'circle';
        echo '<div class="circle-box">';
        echo '<div class="' . $circleClass . '">' . ($i + 1) . '</div>';
        echo '<div class="circle-label">' . htmlspecialchars($step['label']) . '</div>';
        echo '</div>';
    }
    echo '</div></div></section>';
}

include('../php/useradmissionheader.php');
?>

<style>
.container-box { padding: 20px; border-radius: 10px; background-color: #f7f7f7; margin-bottom: 20px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
.show-btn { padding: 10px 20px; font-size: 1rem; font-weight: bold; border: none; border-radius: 5px; background-color: #007bff; color: white; cursor: pointer; }
.show-btn:disabled { background-color: gray; cursor: not-allowed; }
table { width: 100%; border-collapse: collapse; margin-top: 10px; }
td { padding: 8px; border-bottom: 1px solid #ddd; }
td.label { font-weight: bold; width: 30%; background-color: #f0f0f0; }
.upload-group input[type="file"] { border: 2px solid #aaa; border-radius: 5px; padding: 6px; background-color: #f9f9f9; width: 100%; }
.button2:disabled { background-color: #ccc; cursor: not-allowed; }
.message { padding: 10px; background-color: #f0f8ff; border: 1px solid #3399ff; color: #003366; border-radius: 5px; margin-bottom: 15px; }
</style>
</head>

<div class="content">
<?php renderStepProgress($status); ?>
<div class="maincontainer">

  <div class="container-box">
    <h2>Briefly Important Information</h2>
    <table>
      <tr><td class="label">Full Name</td><td><?php echo htmlspecialchars($personal_data['firstname'] . ' ' . $personal_data['middlename'] . ' ' . $personal_data['lastname']); ?></td></tr>
      <tr><td class="label">Phone Number</td><td><?php echo htmlspecialchars($personal_data['phonenumber']); ?></td></tr>
      <tr><td class="label">Birthday</td><td><?php echo htmlspecialchars($personal_data['birthday']); ?></td></tr>
      <tr><td class="label">Birthplace</td><td><?php echo htmlspecialchars($personal_data['birthplace']); ?></td></tr>
      <tr><td class="label">Entry</td><td><?php echo htmlspecialchars($admission_data['entry']); ?></td></tr>
      <tr><td class="label">Type</td><td><?php echo htmlspecialchars($admission_data['type']); ?></td></tr>
      <tr><td class="label">Strand</td><td><?php echo htmlspecialchars($admission_data['strand']); ?></td></tr>
      <tr><td class="label">LRN</td><td><?php echo htmlspecialchars($admission_data['lrn']); ?></td></tr>
      <tr><td class="label">Program</td><td><?php echo htmlspecialchars($admission_data['program']); ?></td></tr>
    </table>
  </div>

<!-- [Previous code remains the same until the exam results container] -->

  <div class="container-box">
    <h2>Exam Results</h2>
    <div class="result-container">
        <?php 
        if ($grade_status == 1 || $grade_status == 2): 
            $exam_query = $conn->query("
                SELECT a.*, 
                (SELECT COUNT(*) FROM exam_answers ans 
                 JOIN questions q ON ans.question_no = q.question_no
                 WHERE ans.attempt_id = a.attempt_id AND ans.answer = q.answer) as correct,
                (SELECT COUNT(*) FROM exam_answers WHERE attempt_id = a.attempt_id) as total_answered
                FROM exam_attempts a
                WHERE a.email = '$username' AND a.is_submitted = 1
                ORDER BY a.ended_at DESC LIMIT 1
            ");
            
            if ($exam_query !== false && $exam_query->num_rows > 0):
                $exam_data = $exam_query->fetch_assoc();
                $total_questions = (int)($exam_data['total_answered'] ?? 0);
                $correct_answers = (int)($exam_data['correct'] ?? 0);
                $percentage = $total_questions > 0 ? round(($correct_answers/$total_questions)*100, 2) : 0;
        ?>
                <div class="result-card <?php echo $grade_status == 1 ? 'passed' : 'failed'; ?>">
                    <!-- Score Display -->
                    <div class="score-header">
                        <div class="result-icon">
                            <?php if ($grade_status == 1): ?>
                                <i class="fas fa-check-circle"></i>
                            <?php else: ?>
                                <i class="fas fa-times-circle"></i>
                            <?php endif; ?>
                        </div>
                        <h3 class="result-title">
                            <?php echo $grade_status == 1 ? 'Congratulations!' : 'Exam Results'; ?>
                        </h3>
                    </div>
                    
                    <div class="score-display">
                        <div class="score-percent">
                            <?php echo $percentage; ?>%
                            <div class="score-label">Overall Score</div>
                        </div>
                        <div class="score-breakdown">
                            <div class="breakdown-item">
                                <span class="breakdown-value"><?php echo $correct_answers; ?></span>
                                <span class="breakdown-label">Correct Answers</span>
                            </div>
                            <div class="breakdown-item">
                                <span class="breakdown-value"><?php echo $total_questions; ?></span>
                                <span class="breakdown-label">Total Questions</span>
                            </div>
                            <div class="breakdown-item">
                                <span class="breakdown-value"><?php echo date('M j, Y', strtotime($exam_data['ended_at'] ?? 'now')); ?></span>
                                <span class="breakdown-label">Exam Date</span>
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($grade_status == 1): ?>
                        <div class="success-panel">
                            <div class="success-header">
                                <i class="fas fa-graduation-cap"></i>
                                <h4>Next Steps for Enrollment</h4>
                            </div>
                            <ul class="success-steps">
                                <li>
                                    <i class="fas fa-envelope"></i>
                                    <div>
                                        <strong>Check your email</strong>
                                        <p>We'll send enrollment instructions within 3 business days</p>
                                    </div>
                                </li>
                                <li>
                                    <i class="fas fa-file-alt"></i>
                                    <div>
                                        <strong>Prepare your documents</strong>
                                        <p>Have your requirements ready for verification</p>
                                    </div>
                                </li>
                                <li>
                                    <i class="fas fa-calendar-check"></i>
                                    <div>
                                        <strong>Orientation schedule</strong>
                                        <p>Watch for orientation date announcements</p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <div class="retake-panel">
                            <div class="retake-header">
                                <i class="fas fa-redo-alt"></i>
                                <h4>Retake Information</h4>
                            </div>
                            <div class="retake-content">
                                <p>You may retake the exam after:</p>
                                <div class="retake-date">
                                    <?php echo date('F j, Y', strtotime('+2 weeks', strtotime($exam_data['ended_at'] ?? 'now'))); ?>
                                </div>
                                <p class="retake-tip">Use this time to review the study materials</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
        <?php else: ?>
                <div class="result-message">
                    <i class="fas fa-hourglass-half"></i>
                    <p>Exam results are not available yet.</p>
                    <?php if ($conn->error): ?>
                        <p class="error-text">(System message: <?php echo htmlspecialchars($conn->error); ?>)</p>
                    <?php endif; ?>
                </div>
        <?php endif; ?>
        <?php elseif ($status['current_stage'] >= 3): ?>
            <div class="result-message">
                <i class="fas fa-clipboard-check"></i>
                <p>You've completed the exam. Results will be available soon.</p>
            </div>
        <?php else: ?>
            <div class="result-message">
                <i class="fas fa-hourglass-half"></i>
                <p>You haven't taken the exam yet.</p>
            </div>
        <?php endif; ?>
    </div>
  </div>

<!-- [Rest of your code remains the same] -->

</div>
</div>
</div>

</body>
</html>
<?php ob_end_flush(); ?>
