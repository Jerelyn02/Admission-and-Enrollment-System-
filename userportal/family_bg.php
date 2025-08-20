<?php
include('../php/connection.php');
include_once('../php/check_status_helper.php');
session_start();

$user = $_SESSION['user'];
$edit_mode = false;

// Initialize variables
$guardianName = $gnumber = $goccupation = '';
$fatherName = $fnumber = $foccupation = '';
$motherName = $mnumber = $moccupation = '';
$fam_month_inc = $numsibling = $birthorder = $soloparent = $fam_work_abroad = '';

// Fetch data if it exists
$res = mysqli_query($conn, "SELECT * FROM family_bg WHERE username = '$user'");
if (mysqli_num_rows($res) > 0) {
    $edit_mode = true;
    $row = mysqli_fetch_assoc($res);
    $guardianName = $row['gurdianname'];
    $gnumber = $row['gnumber'];
    $goccupation = $row['goccupation'];
    $fatherName = $row['fathername'];
    $fnumber = $row['fnumber'];
    $foccupation = $row['foccupation'];
    $motherName = $row['mothername'];
    $mnumber = $row['mnumber'];
    $moccupation = $row['moccupation'];
    $fam_month_inc = $row['fam_month_inc'];
    $numsibling = $row['numsibling'];
    $birthorder = $row['birthorder'];
    $soloparent = $row['soloparent'];
    $fam_work_abroad = $row['fam_work_abroad'];
}

// Handle form submission
if (isset($_POST['submit'])) {
    $guardianName = $_POST['guardian_name'];
    $gnumber = $_POST['guardian_contact'];
    $goccupation = $_POST['guardian_occupation'];
    $fatherName = $_POST['father_name'];
    $fnumber = $_POST['father_contact'];
    $foccupation = $_POST['father_occupation'];
    $motherName = $_POST['mother_name'];
    $mnumber = $_POST['mother_contact'];
    $moccupation = $_POST['mother_occupation'];
    $fam_month_inc = $_POST['income'];
    $numsibling = $_POST['siblings'];
    $birthorder = $_POST['birth_order'];
    $soloparent = $_POST['solo_parent'];
    $fam_work_abroad = $_POST['abroad'];

    if ($edit_mode) {
        $update = "UPDATE family_bg SET 
            gurdianname='$guardianName', gnumber='$gnumber', goccupation='$goccupation',
            fathername='$fatherName', fnumber='$fnumber', foccupation='$foccupation',
            mothername='$motherName', mnumber='$mnumber', moccupation='$moccupation',
            fam_month_inc='$fam_month_inc', numsibling='$numsibling', birthorder='$birthorder',
            soloparent='$soloparent', fam_work_abroad='$fam_work_abroad'
            WHERE username='$user'";
        mysqli_query($conn, $update) or die(mysqli_error($conn));
        mysqli_query($conn, "UPDATE check_status SET family_bg_completed = 1 WHERE username = '$user'");
    } else {
        $insert = "INSERT INTO family_bg (username, gurdianname, gnumber, goccupation,
            fathername, fnumber, foccupation,
            mothername, mnumber, moccupation,
            fam_month_inc, numsibling, birthorder,
            soloparent, fam_work_abroad)
            VALUES (
                '$user', '$guardianName', '$gnumber', '$goccupation',
                '$fatherName', '$fnumber', '$foccupation',
                '$motherName', '$mnumber', '$moccupation',
                '$fam_month_inc', '$numsibling', '$birthorder',
                '$soloparent', '$fam_work_abroad')";
        mysqli_query($conn, $insert) or die(mysqli_error($conn));
        mysqli_query($conn, "INSERT INTO check_status (username) VALUES ('$user') ON DUPLICATE KEY UPDATE family_bg_completed = 1");

        if(function_exists('checkAndGenerateControlNumber')){
            checkAndGenerateControlNumber($conn, $user);
        }
    }

    // Set success message
    $_SESSION['admission_complete'] = "Your Family Background Information Submitted Successfully!";
    header("Location: useradmission.php");
    exit();
}
?>

<?php include('../php/userformheader.php'); ?>

<div class="content">
  <div class="maincontainer">
    <div class="admission-form">
      <h2>Family Background</h2>

      <form method="POST" id="familyForm">
        <!-- Mother Section -->
        <div class="form-section">
          <h3>Mother's Information</h3>
          <div class="form-group">
            <div class="form-control">
              <label>Name</label>
              <input type="text" id="mother_name" name="mother_name" value="<?php echo $motherName; ?>" placeholder="Mother's Name">
              <span class="error-message" id="error_mother_name"></span>
            </div>
            <div class="form-control">
              <label>Contact Number</label>
              <input type="text" id="mother_contact" name="mother_contact" value="<?php echo $mnumber; ?>" placeholder="Contact Number">
              <span class="error-message" id="error_mother_contact"></span>
            </div>
            <div class="form-control">
              <label>Occupation</label>
              <input type="text" id="mother_occupation" name="mother_occupation" value="<?php echo $moccupation; ?>" placeholder="Occupation">
              <span class="error-message" id="error_mother_occupation"></span>
            </div>
          </div>
        </div>

        <!-- Father Section -->
        <div class="form-section">
          <h3>Father's Information</h3>
          <div class="form-group">
            <div class="form-control">
              <label>Name</label>
              <input type="text" id="father_name" name="father_name" value="<?php echo $fatherName; ?>" placeholder="Father's Name">
              <span class="error-message" id="error_father_name"></span>
            </div>
            <div class="form-control">
              <label>Contact Number</label>
              <input type="text" id="father_contact" name="father_contact" value="<?php echo $fnumber; ?>" placeholder="Contact Number">
              <span class="error-message" id="error_father_contact"></span>
            </div>
            <div class="form-control">
              <label>Occupation</label>
              <input type="text" id="father_occupation" name="father_occupation" value="<?php echo $foccupation; ?>" placeholder="Occupation">
              <span class="error-message" id="error_father_occupation"></span>
            </div>
          </div>
        </div>

        <!-- Guardian Section -->
        <div class="form-section">
          <h3>Guardian's Information</h3>
          <div class="form-group">
            <div class="form-control">
              <label><input type="checkbox" id="same_as_mother"> Same as Mother</label>
              <label><input type="checkbox" id="same_as_father"> Same as Father</label>
            </div>
            <div class="form-control">
              <label>Name</label>
              <input type="text" id="guardian_name" name="guardian_name" value="<?php echo $guardianName; ?>" placeholder="Guardian's Name">
              <span class="error-message" id="error_guardian_name"></span>
            </div>
            <div class="form-control">
              <label>Contact Number</label>
              <input type="text" id="guardian_contact" name="guardian_contact" value="<?php echo $gnumber; ?>" placeholder="Contact Number">
              <span class="error-message" id="error_guardian_contact"></span>
            </div>
            <div class="form-control">
              <label>Occupation</label>
              <input type="text" id="guardian_occupation" name="guardian_occupation" value="<?php echo $goccupation; ?>" placeholder="Occupation">
              <span class="error-message" id="error_guardian_occupation"></span>
            </div>
          </div>
        </div>

        <!-- Other Info Section -->
        <div class="form-section">
          <div class="form-group">
            <div class="form-control">
              <label>Family Monthly Income</label>
              <input type="text" id="income" name="income" value="<?php echo $fam_month_inc; ?>" placeholder="₱10000, ₱10000-₱15000, etc.">
              <span class="error-message" id="error_income"></span>
            </div>
            <div class="form-control">
              <label>Number of Siblings</label>
              <input type="text" id="siblings" name="siblings" value="<?php echo $numsibling; ?>" placeholder="Number of Siblings">
              <span class="error-message" id="error_siblings"></span>
            </div>
            <div class="form-control">
              <label>Birth Order</label>
              <input type="text" id="birth_order" name="birth_order" value="<?php echo $birthorder; ?>" placeholder="First, Second, etc.">
              <span class="error-message" id="error_birth_order"></span>
            </div>
          </div>
          <div class="form-group">
            <div class="form-control">
              <label>Are you a solo parent?</label>
              <select id="solo_parent" name="solo_parent">
                <option value="">Select</option>
                <option value="Yes" <?php if($soloparent=="Yes") echo "selected"; ?>>Yes</option>
                <option value="No" <?php if($soloparent=="No") echo "selected"; ?>>No</option>
              </select>
              <span class="error-message" id="error_solo_parent"></span>
            </div>
            <div class="form-control">
              <label>Family member working abroad?</label>
              <select id="abroad" name="abroad">
                <option value="">Select</option>
                <option value="Yes" <?php if($fam_work_abroad=="Yes") echo "selected"; ?>>Yes</option>
                <option value="No" <?php if($fam_work_abroad=="No") echo "selected"; ?>>No</option>
              </select>
              <span class="error-message" id="error_abroad"></span>
            </div>
          </div>
        </div>

        <div class="submit-section">
          <button type="submit" name="submit" class="confirm-btn">Confirm</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
// JS for Guardian copy + validation
const motherNameField = document.getElementById('mother_name');
const motherContactField = document.getElementById('mother_contact');
const motherOccField = document.getElementById('mother_occupation');

const fatherNameField = document.getElementById('father_name');
const fatherContactField = document.getElementById('father_contact');
const fatherOccField = document.getElementById('father_occupation');

const guardianNameField = document.getElementById('guardian_name');
const guardianContactField = document.getElementById('guardian_contact');
const guardianOccField = document.getElementById('guardian_occupation');

const sameMother = document.getElementById('same_as_mother');
const sameFather = document.getElementById('same_as_father');

sameMother.addEventListener('change', function() {
  if(this.checked){
    guardianNameField.value = motherNameField.value;
    guardianContactField.value = motherContactField.value;
    guardianOccField.value = motherOccField.value;
    sameFather.checked = false;
  } else {
    guardianNameField.value = '';
    guardianContactField.value = '';
    guardianOccField.value = '';
  }
});

sameFather.addEventListener('change', function() {
  if(this.checked){
    guardianNameField.value = fatherNameField.value;
    guardianContactField.value = fatherContactField.value;
    guardianOccField.value = fatherOccField.value;
    sameMother.checked = false;
  } else {
    guardianNameField.value = '';
    guardianContactField.value = '';
    guardianOccField.value = '';
  }
});

// Validation
document.getElementById('familyForm').addEventListener('submit', function(e){
  let valid = true;

  function checkField(field, errorId, message){
    const error = document.getElementById(errorId);
    if(field.value.trim() === ''){
      error.innerHTML = '<b>'+message+'</b>';
      valid = false;
    } else { error.textContent = ''; }
  }

  checkField(motherNameField, 'error_mother_name', 'Mother name is required!');
  checkField(motherContactField, 'error_mother_contact', 'Mother contact is required!');
  checkField(motherOccField, 'error_mother_occupation', 'Mother occupation is required!');

  checkField(fatherNameField, 'error_father_name', 'Father name is required!');
  checkField(fatherContactField, 'error_father_contact', 'Father contact is required!');
  checkField(fatherOccField, 'error_father_occupation', 'Father occupation is required!');

  if(!sameMother.checked && !sameFather.checked){
    checkField(guardianNameField, 'error_guardian_name', 'Guardian name is required!');
    checkField(guardianContactField, 'error_guardian_contact', 'Guardian contact is required!');
    checkField(guardianOccField, 'error_guardian_occupation', 'Guardian occupation is required!');
  }

  checkField(document.getElementById('income'), 'error_income', 'Family income is required!');
  checkField(document.getElementById('siblings'), 'error_siblings', 'Number of siblings is required!');
  checkField(document.getElementById('birth_order'), 'error_birth_order', 'Birth order is required');

  if(document.getElementById('solo_parent').value === ''){
    document.getElementById('error_solo_parent').innerHTML = '<b>Please select if you are a solo parent!</b>';
    valid = false;
  }

  if(document.getElementById('abroad').value === ''){
    document.getElementById('error_abroad').innerHTML = '<b>Please select if family member works abroad!</b>';
    valid = false;
  }

  if(!valid) e.preventDefault();
  
}

);

</script>

