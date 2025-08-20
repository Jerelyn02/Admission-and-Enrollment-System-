<?php
include('..\php\connection.php');
include('..\php\check_status_helper.php');

session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

$username = $_SESSION['user'];

// Handle control number button click
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_cn'])) {
    $stmt = $conn->prepare("UPDATE check_status SET control_number_click = 1 WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    header("Location: useradmission.php");
    exit();
}

// Fetch current check_status
$stmt = $conn->prepare("SELECT * FROM check_status WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

$control_number = '';
$control_number_click = 0;
$allCompleted = false;
$status = null;

if ($row = $result->fetch_assoc()) {
    $status = $row;
    $control_number = $row['control_number'];
    $control_number_click = $row['control_number_click'];

    $filled = [
        'Admission Information' => $row['admission_info_completed'],
        'Personal Information' => $row['personal_info_completed'],
        'Family Background' => $row['family_bg_completed'],
        'Educational Background' => $row['education_bg_completed'],
        'Medical History Information' => $row['med_his_info_completed']
    ];

    $allCompleted = array_sum($filled) === 5;

    // Fetch form data safely
    $admission = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM admission_info WHERE username='$username'")) ?? [];
    $personal = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM personal_info WHERE username='$username'")) ?? [];
    $family = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM family_bg WHERE username='$username'")) ?? [];
    $education = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM education_bg WHERE username='$username'")) ?? [];
    $medical = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM med_his_info WHERE username='$username'")) ?? [];
} else {
    $filled = [
        'Admission Information' => 0,
        'Personal Information' => 0,
        'Family Background' => 0,
        'Educational Background' => 0,
        'Medical History Information' => 0
    ];
}

// Generate control number if all forms completed
checkAndGenerateControlNumber($conn, $username);

// Re-fetch control_number
$stmt = $conn->prepare("SELECT control_number FROM check_status WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$control_result = $stmt->get_result();
if ($control = $control_result->fetch_assoc()) {
    if (!empty($control['control_number'])) {
        header("Location: useradmission2.php");
        exit();
    }
}

// Fetch user account info
$stmt = $conn->prepare("SELECT * FROM accounts WHERE email = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$user_result = $stmt->get_result();
$user_data = $user_result->fetch_assoc();

if (!$status) {
    $status = [
        'admission_info_completed' => 0,
        'personal_info_completed' => 0,
        'family_bg_completed' => 0,
        'education_bg_completed' => 0,
        'med_his_info_completed' => 0,
        'control_number_click' => 0,
        'control_number' => '',
        'current_stage' => 0
    ];
}

// Render step progress circles
function renderStepProgress($status) {
    if (!$status) return;

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
?>

<?php include('../php/useradmissionheader.php'); ?>

<?php
if(isset($_SESSION['admission_complete'])){
    echo "<div class='alert success'>".$_SESSION['admission_complete']."</div>";
    unset($_SESSION['admission_complete']);
}
if (isset($_SESSION['family_bg_success'])) {
    echo "<div class='success-message'>".$_SESSION['family_bg_success']."</div>";
    unset($_SESSION['family_bg_success']);
}
?>

<div class="content">
    <?php renderStepProgress($status); ?>

    <div class="dashboard-cards">
        <!-- Applicant Data Links -->
        <div class="card">
            <h1 style="font-size: 28px; font-weight: bold; color: #0049cf;">Applicant Data</h1>
            <?php foreach ($filled as $section => $completed): ?>
                <p>
                    <?php if (!$completed): ?>
                        <a href="<?php
                            echo match($section) {
                                'Admission Information' => 'admission_info.php',
                                'Personal Information' => 'personal_info.php',
                                'Family Background' => 'family_bg.php',
                                'Educational Background' => 'education_bg.php',
                                'Medical History Information' => 'med_his_info.php',
                                default => '#'
                            };
                        ?>"><?php echo $section; ?></a>
                    <?php else: ?>
                        <span style="color: #555;"><?php echo $section; ?> ✅</span>
                    <?php endif; ?>
                </p>
            <?php endforeach; ?>
        </div>

        <!-- Control Number Card -->
        <div class="card card2">
            <div style="background-color: #fff3cd; border: 1px solid #f3da90ff; padding: 15px; border-radius: 5px; margin-bottom: 20px; color: #856404;">
    <strong>Important Reminder:</strong> Once you click <em>"Get Control Number"</em>,your application will be finalized and you will no longer be able to make any changes. 
    Please carefully review all the information you have entered to ensure it is correct. 
    You can make edits now before finalizing your submission.
</div>
            <?php if ($allCompleted && !$control_number_click): ?>
                <form method="POST">
                    <button class="button2" type="submit" name="generate_cn">Get Control Number</button>
                </form>
            <?php elseif ($control_number_click): ?>
                <p style="color: green;">Control number is being generated. Redirecting...</p>
            <?php else: ?>
                <button class="button2" disabled>Get Control Number</button>
                <p style="color: red;">Complete all 5 forms to unlock your Control Number.</p>
            <?php endif; ?>
        </div>
    </div>

   <!-- Review Section -->
<div class="review-container">
    <h2>Review Your Submitted Information</h2>
    <div class="review-scroll">

        <!-- Admission Information -->
<?php if (!empty($admission)): ?>
<div class="review-section">
    <h3>Admission Information <a href="admission_info.php" class="edit-btn">Edit</a></h3>
    <?php
// Always show Entry, Type (if exists), Program
if (!empty($admission['entry'])) {
    echo "<p><strong>Entry:</strong> " . htmlspecialchars($admission['entry']) . "</p>";
}
if ($admission['entry'] === 'New Student' && !empty($admission['type'])) {
    echo "<p><strong>Type:</strong> " . htmlspecialchars($admission['type']) . "</p>";
}
if (!empty($admission['program'])) {
    echo "<p><strong>Program:</strong> " . htmlspecialchars($admission['program']) . "</p>";
}

// Conditional details
if ($admission['entry'] === 'New Student') {
    if ($admission['type'] === 'K-12') {
        if (!empty($admission['strand'])) {
            echo "<p><strong>SHS Strand:</strong> " . htmlspecialchars($admission['strand']) . "</p>";
        }
        if (!empty($admission['lrn'])) { // I saw your DB column is "lrm", not "lrn"
            echo "<p><strong>LRN:</strong> " . htmlspecialchars($admission['lrn']) . "</p>";
        }
    } elseif ($admission['type'] === 'Old Curriculum') {
        if (!empty($admission['prev_school'])) {
            echo "<p><strong>Previous School:</strong> " . htmlspecialchars($admission['prev_school']) . "</p>";
        }
        if (!empty($admission['last_year'])) {
            echo "<p><strong>Last Year Completed:</strong> " . htmlspecialchars($admission['last_year']) . "</p>";
        }
    }
} elseif (in_array($admission['entry'], ['Shiftee', 'Transferee', 'Returnee'])) {
    if (!empty($admission['prev_school'])) {
        echo "<p><strong>Previous School:</strong> " . htmlspecialchars($admission['prev_school']) . "</p>";
    }
    if (!empty($admission['last_year'])) {
        echo "<p><strong>Last Year Attended:</strong> " . htmlspecialchars($admission['last_year']) . "</p>";
    }
    if (!empty($admission['reason'])) {
        echo "<p><strong>Reason:</strong> " . htmlspecialchars($admission['reason']) . "</p>";
    }
}
?>

</div>
<?php endif; ?>


        <!-- Personal Information -->
        <?php if (!empty($personal)): ?>
        <div class="review-section">
            <h3>Personal Information <a href="personal_info.php" class="edit-btn">Edit</a></h3>
            <?php
            $personal_labels = [
                'firstname' => 'First Name',
                'middlename' => 'Middle Name',
                'lastname' => 'Last Name',
                'phonenumber' => 'Phone Number',
                'civilstatus' => 'Civil Status',
                'sex' => 'Sex',
                'birthday' => 'Birthday',
                'birthplace' => 'Birth Place',
                'region' => 'Region',
                'province' => 'Province',
                'town' => 'Town/City'

            ];
            foreach ($personal_labels as $key => $label): ?>
                <p><strong><?php echo $label; ?>:</strong> <?php echo htmlspecialchars($personal[$key] ?? ''); ?></p>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Family Background -->
        <?php if (!empty($family)): ?>
        <div class="review-section">
            <h3>Family Background <a href="family_bg.php" class="edit-btn">Edit</a></h3>
            <?php
            $family_labels = [
                'fathername' => 'Father\'s Name',
                'fnumber' => 'Phone number',
                'foccupation' => 'Father\'s Occupation',
                'mothername' => 'Mother\'s Name',
                'mnumber' => 'Phone number',
                'moccupation' => 'Mother\'s Occupation',
                'gurdianname' => 'Guardian\'s Name',
                'gnumber' => 'Phone Number',
                'goccupation' => 'Guardian\'s Occupation'
            ];
            foreach ($family_labels as $key => $label): ?>
                <p><strong><?php echo $label; ?>:</strong> <?php echo htmlspecialchars($family[$key] ?? ''); ?></p>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Educational Background -->
        <?php if (!empty($education)): ?>
        <div class="review-section">
            <h3>Educational Background <a href="education_bg.php" class="edit-btn">Edit</a></h3>
            <?php
            $education_labels = [
                
                'elemname' => 'Elementary School Name',
                'elemaddress' => 'Elementary School Address',
                'elemyear' => 'Year Graduated',
                'elemtype' => 'Type of School',
                'midname' => 'Junior High School Name',
                'midaddress' => 'Junior High School Address',
                'midyear' => 'Year Graduated',
                'midtype' => 'Type of School',
                'seniorname' => 'Senior High School Name',
                'senioraddress' => 'Senior High School Address',
                'senioryear' => 'Year Graduated',

            ];
            foreach ($education_labels as $key => $label): ?>
                <p><strong><?php echo $label; ?>:</strong> <?php echo htmlspecialchars($education[$key] ?? ''); ?></p>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Medical History -->
        <?php if (!empty($medical)): ?>
        <div class="review-section">
            <h3>Medical History <a href="med_his_info.php" class="edit-btn">Edit</a></h3>
            <?php
            $medical_labels = [
                'chronic_illness' => 'Do you have chronic illnesses?',
                'major_surgeries' => 'Have you had any major surgeries or hospitalizations?',
                'physical_disabilities' => 'Do you have any physical disabilities or limitations?',
                'medications' => 'Medications that are being taken?',
                'medical_conditions' => 'Do you have Scoliosis, Diabetes, Asthma , Heart Disease?',
                'allergies' => 'Other illness?',
                'family_hereditary' => 'Family history or hereditary diseases?'

            ];
            foreach ($medical_labels as $key => $label): ?>
                <p><strong><?php echo $label; ?>:</strong> <?php echo htmlspecialchars($medical[$key] ?? ''); ?></p>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

    </div>
</div>
