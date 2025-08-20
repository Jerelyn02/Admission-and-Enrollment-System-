<?php
include('../php/connection.php');
session_start();

$user = $_SESSION['user'];

$msg = '';
$errors = [];
$edit_mode = false;
$entry = $type = $strand = $lrn = $program = '';
$prev_school = $last_year = $reason = '';

// Fetch existing data if any
$res = mysqli_query($conn, "SELECT * FROM admission_info WHERE username = '$user'");
if (mysqli_num_rows($res) > 0) {
    $edit_mode = true;
    $row = mysqli_fetch_assoc($res);
    $entry = $row['entry'];
    $type = $row['type'];
    $strand = $row['strand'];
    $lrn = $row['lrn'];
    $program = $row['program'];
    $prev_school = $row['prev_school'] ?? '';
    $last_year = $row['last_year'] ?? '';
    $reason = $row['reason'] ?? '';
}

if(isset($_POST['submit'])){
    $entry = $_POST['entry'];
    $type = $_POST['type'] ?? '';
    $strand = $_POST['strand'] ?? '';
    $lrn = $_POST['lrn'] ?? '';
    $program = $_POST['program'];
    $prev_school = $_POST['prev_school'] ?? '';
    $last_year = $_POST['last_year'] ?? '';
    $reason = $_POST['reason'] ?? '';

    // Field-specific validation
    if(empty($entry)) $errors['entry'] = "Entry is required!";
    if(empty($program)) $errors['program'] = "Program is required!";
    if($entry=='New Student' && $type=='K-12'){
        if(empty($type)) $errors['type'] = "Type of new student is required!";
        if(empty($strand)) $errors['strand'] = "Strand is required!";
        if(empty($lrn)) $errors['lrn'] = "Learner’s Reference Number is required!";
    }
    if($type=='Old Curriculum' || $entry=='Transferee' || $entry=='Returnee' || $entry=='Shiftee'){
    if(empty($prev_school)) $errors['prev_school'] = "Previous School / Program is required!";
    if(empty($last_year)) $errors['last_year'] = "Last Year / Level Completed is required!";
    
    // Require reason ONLY for transferee/returnee/shiftee
    if($entry=='Transferee' || $entry=='Returnee' || $entry=='Shiftee'){
        if(empty($reason)) $errors['reason'] = "Reason is required!";
    
}

    }

    if(empty($errors)){
        if ($edit_mode) {
            $update = "UPDATE admission_info SET entry=?, type=?, strand=?, lrn=?, program=?, prev_school=?, last_year=?, reason=? WHERE username=?";
            $stmt = $conn->prepare($update);
            $stmt->bind_param("sssssssss", $entry, $type, $strand, $lrn, $program, $prev_school, $last_year, $reason, $user);
            $stmt->execute();
        } else {
            $insert = "INSERT INTO admission_info (username, entry, type, strand, lrn, program, prev_school, last_year, reason) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert);
            $stmt->bind_param("sssssssss", $user, $entry, $type, $strand, $lrn, $program, $prev_school, $last_year, $reason);
            $stmt->execute();
        }

        $check_status_sql = "INSERT INTO check_status (username, admission_info_completed)
                             VALUES ('$user', 1)
                             ON DUPLICATE KEY UPDATE admission_info_completed = 1";
        mysqli_query($conn, $check_status_sql);

        include_once('../php/check_status_helper.php');
        checkAndGenerateControlNumber($conn, $user);

        $_SESSION['admission_complete'] = "Your Admission Information has been Submitted Successfully!";
        header("Location: useradmission.php");
        exit();
    }
}
?>
<?php include('../php/userformheader.php'); ?>

<style>
.content { width: 100%; padding: 40px 60px; font-family: Arial, sans-serif; box-sizing: border-box; background: #f0f2f7; min-height: 100vh; }
.admission-form { background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 0 20px rgba(0,0,0,0.1); width: 100%; }
.admission-form h2 { text-align: center; margin-bottom: 30px; color: #0049cf; font-size: 28px; }
.form-group { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px; }
.form-control { display: flex; flex-direction: column; }
.form-control label { margin-bottom: 8px; font-weight: bold; color: #333; }
.form-control select, .form-control input { padding: 12px; border-radius: 6px; border: 1px solid #ccc; font-size: 15px; width: 100%; }
.submit-section { margin-top: 35px; display: flex; flex-direction: column; gap: 20px; }
.submit-section select { width: 100%; padding: 12px; border-radius: 6px; border: 1px solid #ccc; font-size: 15px; }
.confirm-btn { width: fit-content; padding: 12px 25px; background-color: #0049cf; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-size: 16px; transition: 0.3s; }
.confirm-btn:hover { background-color: #0033a0; }
.msg { margin-top: 5px; font-weight: bold; font-size: 14px; }
.divider { height: 1px; background: #ccc; margin: 30px 0; }
.conditional { transition: all 0.3s ease; }
@media(max-width: 768px) { .form-group { grid-template-columns: 1fr; } }
</style>

<div class="content">
    <div class="admission-form">
        <h2>Admission Information</h2>

        <form method="POST" action="">
            <div class="form-group">
                <!-- Entry -->
                <div class="form-control">
                    <label for="entry">ENTRY</label>
                    <select name="entry" id="entry">
                        <option value="">Select Entry</option>
                        <option value="New Student" <?php if($entry=='New Student') echo 'selected'; ?>>New Student</option>
                        <option value="Transferee" <?php if($entry=='Transferee') echo 'selected'; ?>>Transferee</option>
                        <option value="Returnee" <?php if($entry=='Returnee') echo 'selected'; ?>>Returnee</option>
                        <option value="Shiftee" <?php if($entry=='Shiftee') echo 'selected'; ?>>Shiftee</option>
                    </select>
                    <?php if(isset($errors['entry'])): ?><span class="msg" style="color:red;"><?php echo $errors['entry']; ?></span><?php endif; ?>
                </div>

                <!-- Type of New Student -->
                <div class="form-control conditional new-student" style="display:none;">
                    <label for="type">Type of New Student</label>
                    <select name="type" id="type">
                        <option value="">Select Item</option>
                        <option value="K-12" <?php if($type=='K-12') echo 'selected'; ?>>K-12</option>
                        <option value="Old Curriculum" <?php if($type=='Old Curriculum') echo 'selected'; ?>>Old Curriculum</option>
                    </select>
                    <?php if(isset($errors['type'])): ?><span class="msg" style="color:red;"><?php echo $errors['type']; ?></span><?php endif; ?>
                </div>

                <!-- Strand -->
                <div class="form-control conditional strand-field" style="display:none;">
                    <label for="strand">SHS STRAND</label>
                    <select name="strand">
                        <option value="">Select Strand</option>
                        <option value="STEM" <?php if($strand=='STEM') echo 'selected'; ?>>STEM</option>
                        <option value="ABM" <?php if($strand=='ABM') echo 'selected'; ?>>ABM</option>
                        <option value="HUMSS" <?php if($strand=='HUMSS') echo 'selected'; ?>>HUMSS</option>
                        <option value="GAS" <?php if($strand=='GAS') echo 'selected'; ?>>GAS</option>
                        <option value="TECH-VOC" <?php if($strand=='TECH-VOC') echo 'selected'; ?>>TECH-VOC</option>
                        <option value="ICT" <?php if($strand=='ICT') echo 'selected'; ?>>ICT</option>
                    </select>
                    <?php if(isset($errors['strand'])): ?><span class="msg" style="color:red;"><?php echo $errors['strand']; ?></span><?php endif; ?>
                </div>

                <!-- LRN -->
                <div class="form-control conditional lrn-field" style="display:none;">
                    <label for="lrn">LEARNER’S REFERENCE NUMBER</label>
                    <input type="text" name="lrn" placeholder="Input LRN" value="<?php echo htmlspecialchars($lrn); ?>">
                    <?php if(isset($errors['lrn'])): ?><span class="msg" style="color:red;"><?php echo $errors['lrn']; ?></span><?php endif; ?>
                </div>

                <!-- Previous School -->
                <div class="form-control conditional prev-school-field" style="display:none;">
                    <label for="prev_school">Previous School / Program</label>
                    <input type="text" name="prev_school" value="<?php echo htmlspecialchars($prev_school); ?>">
                    <?php if(isset($errors['prev_school'])): ?><span class="msg" style="color:red;"><?php echo $errors['prev_school']; ?></span><?php endif; ?>
                </div>

                <div class="form-control conditional prev-school-field" style="display:none;">
                    <label for="last_year">Last Year / Level Completed</label>
                    <input type="text" name="last_year" value="<?php echo htmlspecialchars($last_year); ?>">
                    <?php if(isset($errors['last_year'])): ?><span class="msg" style="color:red;"><?php echo $errors['last_year']; ?></span><?php endif; ?>
                </div>

                <!-- Reason -->
                <div class="form-control conditional transferee-returnee-shiftee" style="display:none;">
                    <label for="reason">Reason for Transfer / Shifting / Returnee</label>
                    <input type="text" name="reason" value="<?php echo htmlspecialchars($reason); ?>">
                    <?php if(isset($errors['reason'])): ?><span class="msg" style="color:red;"><?php echo $errors['reason']; ?></span><?php endif; ?>
                </div>
            </div>

            <div class="divider"></div>

            <!-- Program -->
            <div class="submit-section">
                <label for="program">CHOOSE YOUR PROGRAM</label>
                <select name="program" id="program">
                    <option value="">Select Course</option>
                    <option value="BSCS">BS Computer Science (BSCS)</option>
                    <option value="BSIT">BS Information Technology (BSIT)</option>
                    <option value="BSCE">BS Computer Engineering (BSCE)</option>
                    <option value="BSEE">BS Electrical Engineering (BSEE)</option>
                    <option value="BSCEng">BS Civil Engineering (BSCEng)</option>
                    <option value="BSME">BS Mechanical Engineering (BSME)</option>
                    <option value="BSBM">BS in Business Administration (BSBM)</option>
                    <option value="BSMA">BS in Management Accounting (BSMA)</option>
                    <option value="BSE">BS in Entrepreneurship (BSE)</option>
                    <option value="BSPA">BS in Public Administration (BSPA)</option>
                    <option value="BSHM">BS in Hospitality Management (BSHM)</option>
                    <option value="BSTM">BS in Tourism Management (BSTM)</option>
                    <option value="BSIE">BS in Industrial Education / Technology Education (BSIE)</option>
                    <option value="BEED">BS Elementary Education (BEED)</option>
                    <option value="BSED">BS Secondary Education (BSED)</option>
                </select>
                <?php if(isset($errors['program'])): ?><span class="msg" style="color:red;"><?php echo $errors['program']; ?></span><?php endif; ?>
                <button type="submit" name="submit" class="confirm-btn">Confirm</button>
            </div>
        </form>
    </div>
</div>

<script>
// JS for conditional fields and program filtering
const entrySelect = document.getElementById('entry');
const typeSelect = document.getElementById('type');
const newStudentFields = document.querySelectorAll('.new-student');
const strandField = document.querySelector('.strand-field');
const lrnField = document.querySelector('.lrn-field');
const prevSchoolFields = document.querySelectorAll('.prev-school-field');
const transfereeFields = document.querySelectorAll('.transferee-returnee-shiftee');
const strandSelectElement = document.querySelector('select[name="strand"]');
const programSelectElement = document.getElementById('program');

const validPrograms = {
    'STEM': ['BSCS','BSIT','BSCE','BSEE','BSCEng','BSME'],
    'ICT': ['BSCS','BSIT','BSCE'],
    'ABM': ['BSBM','BSMA','BSE','BSPA','BSHM','BSTM','BEED','BSED'],
    'HUMSS': ['BSBM','BSMA','BSE','BSPA','BSHM','BSTM','BEED','BSED'],
    'GAS': ['BSBM','BSIT','BSMA','BSE','BSPA','BSHM','BSTM','BEED','BSED'],
    'TECH-VOC': ['BSBM','BSIE']
};

const allPrograms = Array.from(programSelectElement.options);

function toggleFields() {
    if(entrySelect.value==='New Student'){
        newStudentFields.forEach(el=>el.style.display='flex');
        prevSchoolFields.forEach(el=>el.style.display='none');
        transfereeFields.forEach(el=>el.style.display='none');
        if(typeSelect.value==='K-12'){
            strandField.style.display='flex';
            lrnField.style.display='flex';
        } else {
            strandField.style.display='none';
            lrnField.style.display='none';
        }
        if(typeSelect.value==='Old Curriculum'){
    prevSchoolFields.forEach(el=>el.style.display='flex');
    transfereeFields.forEach(el=>el.style.display='none'); // hide reason
        }
    } else {
        newStudentFields.forEach(el=>el.style.display='none');
        strandField.style.display='none';
        lrnField.style.display='none';
        prevSchoolFields.forEach(el=>el.style.display='flex');
        transfereeFields.forEach(el=>el.style.display='flex');
    }
}

function filterPrograms(){
    const entryVal = entrySelect.value;
    const typeVal = typeSelect ? typeSelect.value : '';
    const strandVal = strandSelectElement ? strandSelectElement.value : '';

    programSelectElement.innerHTML = '';
    let optionsToShow = [];

    if(entryVal==='New Student' && typeVal==='K-12'){
        optionsToShow = validPrograms[strandVal] || [];
    } else {
        optionsToShow = allPrograms.map(opt=>opt.value); // All programs for Old Curriculum / Others
    }

    allPrograms.forEach(opt=>{
        if(optionsToShow.includes(opt.value) || opt.value===''){
            const optionEl = document.createElement('option');
            optionEl.value = opt.value;
            optionEl.text = opt.text;
            if(opt.value===programSelectElement.dataset.selected) optionEl.selected = true;
            programSelectElement.add(optionEl);
        }
    });
}

entrySelect.addEventListener('change', ()=>{ toggleFields(); filterPrograms(); });
if(typeSelect) typeSelect.addEventListener('change', ()=>{ toggleFields(); filterPrograms(); });
if(strandSelectElement) strandSelectElement.addEventListener('change', filterPrograms);

document.addEventListener('DOMContentLoaded', ()=>{
    toggleFields();
    filterPrograms();
});
</script>
