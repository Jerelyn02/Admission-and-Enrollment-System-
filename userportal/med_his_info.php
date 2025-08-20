<?php
include('..\php\connection.php');
session_start();
$user = $_SESSION['user'];

// Initialize form variables
$edit_mode = false;
$medications = '';
$conditions = [];
$allergies = '';
$chronic_illness = $chronic_illness_details = '';
$major_surgeries = $major_surgeries_details = '';
$physical_disabilities = $disability_details = '';
$family_hereditary = $hereditary_type = $hereditary_details = '';
$family_mental_health = $mental_health_details = '';

// Fetch existing data
$res = mysqli_query($conn, "SELECT * FROM med_his_info WHERE username = '$user'");
if (mysqli_num_rows($res) > 0) {
    $edit_mode = true;
    $row = mysqli_fetch_assoc($res);
    $medications = $row['medications'];
    $conditions = explode(",", $row['medical_conditions']); 
    $allergies = $row['allergies'];
    $chronic_illness = $row['chronic_illness'];
    $chronic_illness_details = $row['chronic_illness_details'];
    $major_surgeries = $row['major_surgeries'];
    $major_surgeries_details = $row['major_surgeries_details'];
    $physical_disabilities = $row['physical_disabilities'];
    $disability_details = $row['disability_details'];
    $family_hereditary = $row['family_hereditary'];
    $hereditary_type = $row['hereditary_type'];
    $hereditary_details = $row['hereditary_details'];
    $family_mental_health = $row['family_mental_health'];
    $mental_health_details = $row['mental_health_details'];
}

if (isset($_POST['submit'])) {
    $medications = $_POST['medications'];
    $conditions_array = isset($_POST['medical_conditions']) ? $_POST['medical_conditions'] : [];
    $conditions_str = implode(",", $conditions_array);
    $allergies = $_POST['allergies'];
    $chronic_illness = $_POST['chronic_illness'];
    $chronic_illness_details = $_POST['chronic_illness_details'];
    $major_surgeries = $_POST['major_surgeries'];
    $major_surgeries_details = $_POST['major_surgeries_details'];
    $physical_disabilities = $_POST['physical_disabilities'];
    $disability_details = $_POST['disability_details'];
    $family_hereditary = $_POST['family_hereditary'];
    $hereditary_type = $_POST['hereditary_type'];
    $hereditary_details = $_POST['hereditary_details'];
    $family_mental_health = $_POST['family_mental_health'];
    $mental_health_details = $_POST['mental_health_details'];

    if ($edit_mode) {
        mysqli_query($conn, "UPDATE med_his_info SET 
            medications='$medications',
            medical_conditions='$conditions_str',
            allergies='$allergies',
            chronic_illness='$chronic_illness',
            chronic_illness_details='$chronic_illness_details',
            major_surgeries='$major_surgeries',
            major_surgeries_details='$major_surgeries_details',
            physical_disabilities='$physical_disabilities',
            disability_details='$disability_details',
            family_hereditary='$family_hereditary',
            hereditary_type='$hereditary_type',
            hereditary_details='$hereditary_details',
            family_mental_health='$family_mental_health',
            mental_health_details='$mental_health_details'
            WHERE username='$user'");
    } else {
        mysqli_query($conn, "INSERT INTO med_his_info 
            (username, medications, medical_conditions, allergies,
            chronic_illness, chronic_illness_details,
            major_surgeries, major_surgeries_details,
            physical_disabilities, disability_details,
            family_hereditary, hereditary_type, hereditary_details,
            family_mental_health, mental_health_details)
            VALUES 
            ('$user', '$medications', '$conditions_str', '$allergies',
            '$chronic_illness', '$chronic_illness_details',
            '$major_surgeries', '$major_surgeries_details',
            '$physical_disabilities', '$disability_details',
            '$family_hereditary', '$hereditary_type', '$hereditary_details',
            '$family_mental_health', '$mental_health_details')");
    }

    mysqli_query($conn, "INSERT INTO check_status (username) VALUES ('$user') ON DUPLICATE KEY UPDATE med_his_info_completed = 1");

    include_once('..\php\check_status_helper.php');
    checkAndGenerateControlNumber($conn, $user);

    $_SESSION['admission_complete'] = "Your Medical History Information has been Submitted Successfully!";
header("Location: useradmission.php");
exit();
}

function isChecked($conditions, $value) {
    return in_array($value, $conditions) ? 'checked' : '';
}
?>

<script>
  // Checkbox logic
  function handleCheckboxClick(clicked) {
    if (clicked.value === "None" && clicked.checked) {
      document.querySelectorAll('input[name="medical_conditions[]"]').forEach(cb => {
        if (cb !== clicked) cb.checked = false;
      });
    } else if (clicked.value !== "None") {
      document.querySelector('input[value="None"]').checked = false;
    }
  }

  // Enable/disable text inputs based on Yes/No selection
  function toggleRequired(selectElem, inputElemId) {
    const inputElem = document.getElementById(inputElemId);
    const errElem = document.getElementById('err-' + inputElemId);
    if (selectElem.value === "Yes") {
      inputElem.disabled = false;
      errElem.style.display = 'block'; // show error if blank
    } else {
      inputElem.disabled = true;
      inputElem.value = '';
      errElem.style.display = 'none';
    }
  }

  // Form validation
  function validateForm() {
    let valid = true;
    document.querySelectorAll('.error-msg').forEach(err => err.style.display = 'none');

    const fields = [
      {select:'chronic_illness', input:'chronic_illness_details'},
      {select:'major_surgeries', input:'major_surgeries_details'},
      {select:'physical_disabilities', input:'disability_details'},
      {select:'family_hereditary', input:'hereditary_details'},
      {select:'family_mental_health', input:'mental_health_details'}
    ];

    fields.forEach(f => {
      const selectVal = document.getElementById(f.select).value;
      const inputElem = document.getElementById(f.input);
      const errElem = document.getElementById('err-' + f.input);

      if (selectVal === "Yes" && inputElem.value.trim() === '') {
        errElem.style.display = 'block';
        valid = false;
      }
    });

    ['medications', 'allergies'].forEach(id => {
      const elem = document.getElementById(id);
      const errElem = document.getElementById('err-' + id);
      if (elem.value.trim() === '') {
        errElem.style.display = 'block';
        valid = false;
      }
    });

    return valid;
  }

  // On page load, toggle inputs according to previous selection
  window.onload = function() {
    ['chronic_illness','major_surgeries','physical_disabilities','family_hereditary','family_mental_health'].forEach(id => {
      const selectElem = document.getElementById(id);
      const inputId = {
        'chronic_illness':'chronic_illness_details',
        'major_surgeries':'major_surgeries_details',
        'physical_disabilities':'disability_details',
        'family_hereditary':'hereditary_details',
        'family_mental_health':'mental_health_details'
      }[id];
      toggleRequired(selectElem, inputId);
    });
  }
</script>

<style>
  .admission-form { max-width: 800px; margin: 40px auto; padding: 30px; background: #f8f9fa; border-radius: 12px; box-shadow: 0 0 15px rgba(0,0,0,0.1); font-family: Arial, sans-serif; font-size: 16px; color: #333; }
  .admission-form h2 { text-align:center; margin-bottom:25px; font-size:28px; color:#0049cf; }
  .form-group { margin-bottom:20px; display:flex; flex-direction:column; }
  .form-group label { font-size:18px; font-weight:600; margin-bottom:6px; }
  .form-group input[type="text"], .form-group select { padding:10px 12px; font-size:16px; border-radius:6px; border:1px solid #ccc; width:100%; box-sizing:border-box; }
  .form-control { display:flex; flex-direction:column; gap:10px; }
  .form-group .checkbox-group label { font-size:16px; font-weight:normal; }
  .submit-section { text-align:center; margin-top:25px; }
  .confirm-btn { background:#0049cf; color:#fff; font-size:18px; padding:12px 30px; border:none; border-radius:8px; cursor:pointer; transition:0.3s; }
  .confirm-btn:hover { background:#0033a0; }
  .error-msg { color:red; font-size:14px; margin-top:4px; display:none; }
</style>

<?php include('../php/userformheader.php'); ?>

<div class="content">
  <div class="maincontainer">
    <form method="POST" class="admission-form" onsubmit="return validateForm()">
      <h2>Medical History Information</h2>

      <!-- Chronic Illness -->
      <div class="form-group">
        <label>Do you have any chronic illnesses?</label>
        <select name="chronic_illness" id="chronic_illness" onchange="toggleRequired(this,'chronic_illness_details')">
          <option value="No" <?php echo ($chronic_illness=='No')?'selected':''; ?>>No</option>
          <option value="Yes" <?php echo ($chronic_illness=='Yes')?'selected':''; ?>>Yes</option>
        </select>
        <input type="text" name="chronic_illness_details" id="chronic_illness_details" placeholder="If yes, specify" value="<?php echo htmlspecialchars($chronic_illness_details); ?>">
        <div class="error-msg" id="err-chronic_illness_details">This field is required if Yes is selected.</div>
      </div>

      <!-- Major Surgeries -->
      <div class="form-group">
        <label>Have you had any major surgeries or hospitalizations?</label>
        <select name="major_surgeries" id="major_surgeries" onchange="toggleRequired(this,'major_surgeries_details')">
          <option value="No" <?php echo ($major_surgeries=='No')?'selected':''; ?>>No</option>
          <option value="Yes" <?php echo ($major_surgeries=='Yes')?'selected':''; ?>>Yes</option>
        </select>
        <input type="text" name="major_surgeries_details" id="major_surgeries_details" placeholder="If yes, provide details" value="<?php echo htmlspecialchars($major_surgeries_details); ?>">
        <div class="error-msg" id="err-major_surgeries_details">This field is required if Yes is selected.</div>
      </div>

      <!-- Physical Disabilities -->
      <div class="form-group">
        <label>Do you have any physical disabilities or limitations?</label>
        <select name="physical_disabilities" id="physical_disabilities" onchange="toggleRequired(this,'disability_details')">
          <option value="No" <?php echo ($physical_disabilities=='No')?'selected':''; ?>>No</option>
          <option value="Yes" <?php echo ($physical_disabilities=='Yes')?'selected':''; ?>>Yes</option>
        </select>
        <input type="text" name="disability_details" id="disability_details" placeholder="If yes, specify" value="<?php echo htmlspecialchars($disability_details); ?>">
        <div class="error-msg" id="err-disability_details">This field is required if Yes is selected.</div>
      </div>

      <!-- Medications -->
      <div class="form-group">
        <label for="medications">Medications that are being taken:</label>
        <input type="text" id="medications" name="medications" placeholder="List any medications or N/A" value="<?php echo htmlspecialchars($medications); ?>">
        <div class="error-msg" id="err-medications">This field is required.</div>
      </div>

      <!-- Medical Conditions -->
      <div class="form-group" style="flex-direction: column;">
        <label>Do you have any of the following sickness or injury?</label>
        <div class="form-control checkbox-group">
          <label><input type="checkbox" name="medical_conditions[]" value="Scoliosis" <?php echo isChecked($conditions, "Scoliosis"); ?> onclick="handleCheckboxClick(this)"> Scoliosis</label>
          <label><input type="checkbox" name="medical_conditions[]" value="Diabetes" <?php echo isChecked($conditions, "Diabetes"); ?> onclick="handleCheckboxClick(this)"> Diabetes</label>
          <label><input type="checkbox" name="medical_conditions[]" value="Asthma" <?php echo isChecked($conditions, "Asthma"); ?> onclick="handleCheckboxClick(this)"> Asthma</label>
          <label><input type="checkbox" name="medical_conditions[]" value="Heart Disease" <?php echo isChecked($conditions, "Heart Disease"); ?> onclick="handleCheckboxClick(this)"> Heart Disease</label>
          <label><input type="checkbox" name="medical_conditions[]" value="None" <?php echo isChecked($conditions, "None"); ?> onclick="handleCheckboxClick(this)"> None</label>
        </div>
      </div>

      <!-- Allergies -->
      <div class="form-group">
        <label for="allergies">Other or more specific (i.e., kinds of allergies):</label>
        <input type="text" id="allergies" name="allergies" placeholder="e.g., Peanuts, Penicillin or N/A" value="<?php echo htmlspecialchars($allergies); ?>">
        <div class="error-msg" id="err-allergies">This field is required.</div>
      </div>

      <!-- Family Hereditary Diseases -->
      <div class="form-group">
        <label>Family history of hereditary diseases?</label>
        <select name="family_hereditary" id="family_hereditary" onchange="toggleRequired(this,'hereditary_details')">
          <option value="No" <?php echo ($family_hereditary=='No')?'selected':''; ?>>No</option>
          <option value="Yes" <?php echo ($family_hereditary=='Yes')?'selected':''; ?>>Yes</option>
        </select>
        <select name="hereditary_type" id="hereditary_type">
          <option value="">Select type</option>
          <option value="Heart Disease" <?php echo ($hereditary_type=='Heart Disease')?'selected':''; ?>>Heart Disease</option>
          <option value="Diabetes" <?php echo ($hereditary_type=='Diabetes')?'selected':''; ?>>Diabetes</option>
          <option value="Cancer" <?php echo ($hereditary_type=='Cancer')?'selected':''; ?>>Cancer</option>
          <option value="Other" <?php echo ($hereditary_type=='Other')?'selected':''; ?>>Other</option>
        </select>
        <input type="text" name="hereditary_details" id="hereditary_details" placeholder="Details" value="<?php echo htmlspecialchars($hereditary_details); ?>">
        <div class="error-msg" id="err-hereditary_details">This field is required if Yes is selected.</div>
      </div>

      <!-- Family Mental Health -->
      <div class="form-group">
        <label>Any history of mental health conditions in the family?</label>
        <select name="family_mental_health" id="family_mental_health" onchange="toggleRequired(this,'mental_health_details')">
          <option value="No" <?php echo ($family_mental_health=='No')?'selected':''; ?>>No</option>
          <option value="Yes" <?php echo ($family_mental_health=='Yes')?'selected':''; ?>>Yes</option>
        </select>
        <input type="text" name="mental_health_details" id="mental_health_details" placeholder="Details" value="<?php echo htmlspecialchars($mental_health_details); ?>">
        <div class="error-msg" id="err-mental_health_details">This field is required if Yes is selected.</div>
      </div>

      <div class="submit-section">
        <button type="submit" name="submit" class="confirm-btn">Confirm</button>
      </div>
    </form>
  </div>
</div>
