<?php
include('../php/connection.php');
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

$username = $_SESSION['user'];

// Check application_status and redirect
$stmt = $conn->prepare("SELECT status FROM application_status WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$app_result = $stmt->get_result();
$app_status = $app_result->fetch_assoc();

$is_pending = false;
$read_only_attr = '';

if ($app_status) {
    $status_code = (int)$app_status['status'];
    if ($status_code === 1) {
        header("Location: useradmission3.php");
        exit();
    } elseif ($status_code === 3) {
        header("Location: sorry_message.php");
        exit();
    } elseif ($status_code === 0) {
        $is_pending = true;
        $read_only_attr = 'disabled';
    }
}

// Fetch user status
$stmt = $conn->prepare("SELECT * FROM check_status WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: useradmission.php");
    exit();
}

$status = $result->fetch_assoc();

if ((int)$status['current_stage'] > 2) {
    header("Location: useradmission3.php");
    exit();
}

if ((int)$status['current_stage'] < 2 || empty($status['control_number'])) {
    header("Location: useradmission.php");
    exit();
}

$control_number = $status['control_number'];

// Fetch personal info
$stmt = $conn->prepare("SELECT firstname, lastname FROM personal_info WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$user_result = $stmt->get_result();
$user_data = $user_result->fetch_assoc() ?: ['firstname' => '', 'lastname' => ''];

// Fetch admission type
$stmt = $conn->prepare("SELECT entry, type FROM admission_info WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$type_result = $stmt->get_result();
$type_data = $type_result->fetch_assoc();

$entry = $type_data['entry'] ?? '';
$type = $type_data['type'] ?? '';
$pending_message = '';
$already_submitted = false;
$files = []; // Array to store fetched file names

// Determine table and fetch files
$entry_norm = strtolower(trim($entry));

if (strpos($entry_norm, 'new') !== false) {
    $table = 'freshmen_files';
    $file_fields = [
        'report_card' => 'Report Card (Form 138)',
        'gmc' => 'Good Moral Certificate',
        'birth_cert' => 'Birth Certificate'
    ];
} elseif (strpos($entry_norm, 'transferee') !== false) {
    $table = 'transferee_files';
    $file_fields = [
        'tor' => 'Transcript of Records (TOR)',
        'dismissal' => 'Honorable Dismissal',
        'gmc' => 'Good Moral Certificate',
        'nbi' => 'NBI Clearance',
        'birth_cert' => 'Birth Certificate'
    ];
} elseif (strpos($entry_norm, 'returnee') !== false) {
    $table = 'returnee_files';
    $file_fields = [
        'tor' => 'Transcript of Records (TOR)',
        'gmc' => 'Good Moral Certificate',
        'birth_cert' => 'Birth Certificate'
    ];
} elseif (strpos($entry_norm, 'shiftee') !== false) {
    $table = 'shiftee_files';
    $file_fields = [
        'tor' => 'Transcript of Records (TOR)',
        'gmc' => 'Good Moral Certificate',
        'birth_cert' => 'Birth Certificate'
    ];
} else {
    $table = null;
}

if ($table) {
    $check_stmt = $conn->prepare("SELECT * FROM $table WHERE username = ?");
    if ($check_stmt) {
        $check_stmt->bind_param("s", $username);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        $already_submitted = ($check_result->num_rows > 0);
        if ($already_submitted) {
            $files = $check_result->fetch_assoc(); // Fetch file names
        }
        $check_stmt->close();
    } else {
        error_log("Prepare failed for table $table: " . $conn->error);
    }
}

// File upload helper
function save_file($input_name, $username, $upload_dir = '../Uploads/') {
    if (!isset($_FILES[$input_name]) || $_FILES[$input_name]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $file_tmp = $_FILES[$input_name]['tmp_name'];
    $file_name = basename($_FILES[$input_name]['name']);
    $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $new_name = $username . '_' . $input_name . '_' . time() . '.' . $ext;
    $dest_path = $upload_dir . $new_name;

    if (move_uploaded_file($file_tmp, $dest_path)) {
        return $new_name;
    }
    return null;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_files'])) {
    $timestamp = date('Y-m-d H:i:s');
    $upload_dir = '../Uploads/';

    if (strpos($entry_norm, 'new') !== false) {
        $report_card = save_file('report_card', $username, $upload_dir);
        $gmc = save_file('gmc', $username, $upload_dir);
        $birth_cert = save_file('birth_cert', $username, $upload_dir);

        $stmt = $conn->prepare("INSERT INTO freshmen_files (username, control_number, report_card, gmc, birth_cert, submitted_at)
                                VALUES (?, ?, ?, ?, ?, ?)
                                ON DUPLICATE KEY UPDATE report_card=?, gmc=?, birth_cert=?, submitted_at=?");
        $stmt->bind_param("ssssssssss", $username, $control_number, $report_card, $gmc, $birth_cert, $timestamp,
                                          $report_card, $gmc, $birth_cert, $timestamp);
        $stmt->execute();

    } elseif (strpos($entry_norm, 'transferee') !== false) {
        $tor = save_file('tor', $username, $upload_dir);
        $dismissal = save_file('dismissal', $username, $upload_dir);
        $gmc = save_file('gmc', $username, $upload_dir);
        $nbi = save_file('nbi', $username, $upload_dir);
        $birth_cert = save_file('birth_cert', $username, $upload_dir);

        $stmt = $conn->prepare("INSERT INTO transferee_files (username, control_number, tor, dismissal, gmc, nbi, birth_cert, submitted_at)
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                                ON DUPLICATE KEY UPDATE tor=?, dismissal=?, gmc=?, nbi=?, birth_cert=?, submitted_at=?");
        $stmt->bind_param("ssssssssssssss", $username, $control_number, $tor, $dismissal, $gmc, $nbi, $birth_cert, $timestamp,
                                                 $tor, $dismissal, $gmc, $nbi, $birth_cert, $timestamp);
        $stmt->execute();

    } elseif (strpos($entry_norm, 'returnee') !== false) {
        $tor = save_file('tor', $username, $upload_dir);
        $gmc = save_file('gmc', $username, $upload_dir);
        $birth_cert = save_file('birth_cert', $username, $upload_dir);

        $stmt = $conn->prepare("INSERT INTO returnee_files (username, control_number, tor, gmc, birth_cert, submitted_at)
                                VALUES (?, ?, ?, ?, ?, ?)
                                ON DUPLICATE KEY UPDATE tor=?, gmc=?, birth_cert?, submitted_at=?");
        $stmt->bind_param("ssssssssss", $username, $control_number, $tor, $gmc, $birth_cert, $timestamp,
                                           $tor, $gmc, $birth_cert, $timestamp);
        $stmt->execute();
    } elseif (strpos($entry_norm, 'shiftee') !== false) {
        $tor = save_file('tor', $username, $upload_dir);
        $gmc = save_file('gmc', $username, $upload_dir);
        $birth_cert = save_file('birth_cert', $username, $upload_dir);

        $stmt = $conn->prepare("INSERT INTO shiftee_files (username, control_number, tor, gmc, birth_cert, submitted_at)
                                VALUES (?, ?, ?, ?, ?, ?)
                                ON DUPLICATE KEY UPDATE tor=?, gmc=?, birth_cert?, submitted_at=?");
        $stmt->bind_param("ssssssssss", $username, $control_number, $tor, $gmc, $birth_cert, $timestamp,
                                       $tor, $gmc, $birth_cert, $timestamp);
        $stmt->execute();
    }

    // Set application_status to pending (0)
    $stmt = $conn->prepare("INSERT INTO application_status (username, status) VALUES (?, 0)
                            ON DUPLICATE KEY UPDATE status=0");
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $already_submitted = true;
    $pending_message = 'Files submitted successfully. Awaiting review.';
}

// Function to render step progress
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

<style>
.file-item {
    margin-top: 10px;
}
.file-item a {
    color: #007bff;
    text-decoration: none;
}
.file-item a:hover {
    text-decoration: underline;
}
.file-item .error {
    color: red;
}
</style>

<div class="content">
    <?php renderStepProgress($status); ?>

    <div class="dashboard-cards">
        <div class="card control-card">
            <p class="label">Your Control Number</p>
            <h2 class="control-number"><?php echo htmlspecialchars($control_number); ?></h2>
        </div>

        <div class="card upload-card">
            <?php if (!empty($pending_message)): ?>
                <div class="message"><?php echo htmlspecialchars($pending_message); ?></div>
            <?php elseif (!$already_submitted): ?>
                <div class="message" style="background:#fff3cd; color:#856404;">
                    ⚠️ Reminder: Please double-check your files before submission. Once submitted, you cannot edit them.
                </div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data" id="uploadForm">
                <h3 style="text-align: center; margin-bottom: 25px;">
                    Admission Requirements for <span style="color: #007bff;">
                    <?php echo htmlspecialchars(ucfirst($entry) . ' / ' . ucfirst($type)); ?>
                    </span>
                </h3>

                <div class="requirement-grid">
                    <?php
                    function renderUploadField($label, $name, $already_submitted, $file_name, $upload_dir, $table) {
                        echo '
                        <div class="requirement-card">
                            <p class="requirement-label">' . htmlspecialchars($label) . '</p>';
                        if (!$already_submitted) {
                            echo '<input type="file" name="' . $name . '" id="' . $name . '" required>';
                        } else {
                            echo '<input type="file" name="' . $name . '" id="' . $name . '" disabled>';
                            echo '<div class="file-item">';
                            if (!empty($file_name)) {
                                $file_path = $upload_dir . $file_name;
                                if (file_exists($file_path)) {
                                    echo '<a href="download.php?file=' . urlencode($file_name) . '&table=' . urlencode($table) . '">Download ' . htmlspecialchars($file_name) . '</a>';
                                } else {
                                    echo '<span class="error">File not found on server.</span>';
                                }
                            } else {
                                echo '<span class="error">No file uploaded.</span>';
                            }
                            echo '</div>';
                        }
                        echo '</div>';
                    }

                    $upload_dir = '../Uploads/';
                    if (strpos($entry_norm, 'new') !== false) {
                        renderUploadField("Report Card (Form 138)", "report_card", $already_submitted, $files['report_card'] ?? '', $upload_dir, $table);
                        renderUploadField("Good Moral Certificate", "gmc", $already_submitted, $files['gmc'] ?? '', $upload_dir, $table);
                        renderUploadField("Birth Certificate", "birth_cert", $already_submitted, $files['birth_cert'] ?? '', $upload_dir, $table);
                    } elseif (strpos($entry_norm, 'transferee') !== false) {
                        renderUploadField("Transcript of Records (TOR)", "tor", $already_submitted, $files['tor'] ?? '', $upload_dir, $table);
                        renderUploadField("Honorable Dismissal", "dismissal", $already_submitted, $files['dismissal'] ?? '', $upload_dir, $table);
                        renderUploadField("Good Moral Certificate", "gmc", $already_submitted, $files['gmc'] ?? '', $upload_dir, $table);
                        renderUploadField("NBI Clearance", "nbi", $already_submitted, $files['nbi'] ?? '', $upload_dir, $table);
                        renderUploadField("Birth Certificate", "birth_cert", $already_submitted, $files['birth_cert'] ?? '', $upload_dir, $table);
                    } elseif (strpos($entry_norm, 'returnee') !== false) {
                        renderUploadField("Transcript of Records (TOR)", "tor", $already_submitted, $files['tor'] ?? '', $upload_dir, $table);
                        renderUploadField("Good Moral Certificate", "gmc", $already_submitted, $files['gmc'] ?? '', $upload_dir, $table);
                        renderUploadField("Birth Certificate", "birth_cert", $already_submitted, $files['birth_cert'] ?? '', $upload_dir, $table);
                    } elseif (strpos($entry_norm, 'shiftee') !== false) {
                        renderUploadField("Transcript of Records (TOR)", "tor", $already_submitted, $files['tor'] ?? '', $upload_dir, $table);
                        renderUploadField("Good Moral Certificate", "gmc", $already_submitted, $files['gmc'] ?? '', $upload_dir, $table);
                        renderUploadField("Birth Certificate", "birth_cert", $already_submitted, $files['birth_cert'] ?? '', $upload_dir, $table);
                    } else {
                        echo "<p style='color:red;'>⚠ Unknown entry type: " . htmlspecialchars($entry) . "</p>";
                    }
                    ?>

                    <button class="button2" type="submit" name="submit_files" 
                        <?php echo $already_submitted ? 'disabled' : ''; ?>>
                        <?php echo $already_submitted ? 'Submitted' : 'Submit All Files'; ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>