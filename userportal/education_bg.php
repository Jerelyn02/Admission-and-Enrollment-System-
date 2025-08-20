<?php
include('..\php\connection.php');
session_start();

$user = $_SESSION['user'];
$msg = '';
$edit_mode = false;

// Initialize variables
$elemname = $elemaddress = $elemyear = $elemtype = '';
$midname = $midaddress = $midyear = $midtype = '';
$seniorname = $senioraddress = $senioryear = $seniortype = '';
$vocname = $vocaddress = $vocyear = $voctype = '';

// Check for existing entry
$res = mysqli_query($conn, "SELECT * FROM education_bg WHERE username = '$user'");
if (mysqli_num_rows($res) > 0) {
    $edit_mode = true;
    $row = mysqli_fetch_assoc($res);
    extract($row);
}

if (isset($_POST['submit'])) {
    $elemname = $_POST['elemname'];
    $elemaddress = $_POST['elemaddress'];
    $elemyear = $_POST['elemyear'];
    $elemtype = $_POST['elemtype'];
    $midname = $_POST['midname'];
    $midaddress = $_POST['midaddress'];
    $midyear = $_POST['midyear'];
    $midtype = $_POST['midtype'];
    $seniorname = $_POST['seniorname'];
    $senioraddress = $_POST['senioraddress'];
    $senioryear = $_POST['senioryear'];
    $seniortype = $_POST['seniortype'];
    $vocname = $_POST['vocname'];
    $vocaddress = $_POST['vocaddress'];
    $vocyear = $_POST['vocyear'];
    $voctype = $_POST['voctype'];

    if ($edit_mode) {
        $stmt = $conn->prepare("UPDATE education_bg SET 
            elemname=?, elemaddress=?, elemyear=?, elemtype=?,
            midname=?, midaddress=?, midyear=?, midtype=?,
            seniorname=?, senioraddress=?, senioryear=?, seniortype=?,
            vocname=?, vocaddress=?, vocyear=?, voctype=?
            WHERE username=?");
        $stmt->bind_param("sssssssssssssssss", 
            $elemname, $elemaddress, $elemyear, $elemtype,
            $midname, $midaddress, $midyear, $midtype,
            $seniorname, $senioraddress, $senioryear, $seniortype,
            $vocname, $vocaddress, $vocyear, $voctype,
            $user
        );
        $stmt->execute();
        $msg = "Updated successfully";
    } else {
        $stmt = $conn->prepare("INSERT INTO education_bg (
            username, elemname, elemaddress, elemyear, elemtype,
            midname, midaddress, midyear, midtype,
            seniorname, senioraddress, senioryear, seniortype,
            vocname, vocaddress, vocyear, voctype
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssssssssssss",
            $user,
            $elemname, $elemaddress, $elemyear, $elemtype,
            $midname, $midaddress, $midyear, $midtype,
            $seniorname, $senioraddress, $senioryear, $seniortype,
            $vocname, $vocaddress, $vocyear, $voctype
        );
        $stmt->execute();
        $msg = "Submitted successfully";
    }

    mysqli_query($conn, "INSERT INTO check_status (username) VALUES ('$user') 
        ON DUPLICATE KEY UPDATE education_bg_completed = 1");

    include_once('..\php\check_status_helper.php');
    checkAndGenerateControlNumber($conn, $user);

    // Set success message
    $_SESSION['admission_complete'] = "Your Educational Background Information has been Submitted Successfully!";
header("Location: useradmission.php");
exit();
}
?>

<?php include('../php/userformheader.php'); ?>

<div class="content">
    <div class="maincontainer">
        <div class="admission-form">
            <h2>Educational Background</h2>
            <form method="POST" id="educationForm" novalidate>

                <!-- Elementary School -->
                <div class="form-control">
                    <label for="elem_name">ELEMENTARY SCHOOL NAME</label>
                    <input type="text" name="elemname" id="elem_name" value="<?php echo htmlspecialchars($elemname); ?>" placeholder="Input Text Field">
                    <small class="error" id="error-elem_name"></small>
                </div>
                <div class="form-control">
                    <label for="elem_address">SCHOOL ADDRESS</label>
                    <input type="text" name="elemaddress" id="elem_address" value="<?php echo htmlspecialchars($elemaddress); ?>" placeholder="Input Text Field">
                    <small class="error" id="error-elem_address"></small>
                </div>
                <div class="form-row" style="display: flex; gap: 10px;">
                    <div class="form-control" style="flex: 1;">
                        <label for="elem_type">TYPE</label>
                        <select name="elemtype" id="elem_type">
                            <option value="">Select Item</option>
                            <option value="Public" <?php if ($elemtype == 'Public') echo 'selected'; ?>>Public</option>
                            <option value="Private" <?php if ($elemtype == 'Private') echo 'selected'; ?>>Private</option>
                        </select>
                        <small class="error" id="error-elem_type"></small>
                    </div>
                    <div class="form-control" style="flex: 1;">
                        <label for="elem_year">YEAR GRADUATED</label>
                        <input type="number" name="elemyear" id="elem_year" value="<?php echo htmlspecialchars($elemyear); ?>" placeholder="Year"/>
                        <small class="error" id="error-elem_year"></small>
                    </div>
                </div>

                <!-- Junior High School -->
                <div class="form-control">
                    <label for="middle_name">JUNIOR HIGH SCHOOL NAME</label>
                    <input type="text" name="midname" id="middle_name" value="<?php echo htmlspecialchars($midname); ?>" placeholder="Input Text Field">
                    <small class="error" id="error-middle_name"></small>
                </div>
                <div class="form-control">
                    <label for="middle_address">SCHOOL ADDRESS</label>
                    <input type="text" name="midaddress" id="middle_address" value="<?php echo htmlspecialchars($midaddress); ?>" placeholder="Input Text Field">
                    <small class="error" id="error-middle_address"></small>
                </div>
                <div class="form-row" style="display: flex; gap: 10px;">
                    <div class="form-control" style="flex: 1;">
                        <label for="mid_type">TYPE</label>
                        <select name="midtype" id="mid_type">
                            <option value="">Select Item</option>
                            <option value="Public" <?php if ($midtype == 'Public') echo 'selected'; ?>>Public</option>
                            <option value="Private" <?php if ($midtype == 'Private') echo 'selected'; ?>>Private</option>
                        </select>
                        <small class="error" id="error-mid_type"></small>
                    </div>
                    <div class="form-control" style="flex: 1;">
                        <label for="middle_year">YEAR GRADUATED</label>
                        <input type="number" name="midyear" id="middle_year" value="<?php echo htmlspecialchars($midyear); ?>" placeholder="Year"/>
                        <small class="error" id="error-middle_year"></small>
                    </div>
                </div>

                <!-- Senior High School -->
                <div class="form-control">
                    <label for="shs_name">SENIOR HIGH SCHOOL NAME</label>
                    <input type="text" name="seniorname" id="shs_name" value="<?php echo htmlspecialchars($seniorname); ?>" placeholder="Input Text Field">
                    <small class="error" id="error-shs_name"></small>
                </div>
                <div class="form-control">
                    <label for="shs_address">SCHOOL ADDRESS</label>
                    <input type="text" name="senioraddress" id="shs_address" value="<?php echo htmlspecialchars($senioraddress); ?>" placeholder="Input Text Field">
                    <small class="error" id="error-shs_address"></small>
                </div>
                <div class="form-row" style="display: flex; gap: 10px;">
                    <div class="form-control" style="flex: 1;">
                        <label for="senior_type">TYPE</label>
                        <select name="seniortype" id="senior_type">
                            <option value="">Select Item</option>
                            <option value="Public" <?php if ($seniortype == 'Public') echo 'selected'; ?>>Public</option>
                            <option value="Private" <?php if ($seniortype == 'Private') echo 'selected'; ?>>Private</option>
                        </select>
                        <small class="error" id="error-senior_type"></small>
                    </div>
                    <div class="form-control" style="flex: 1;">
                        <label for="shs_year">YEAR GRADUATED</label>
                        <input type="number" name="senioryear" id="shs_year" value="<?php echo htmlspecialchars($senioryear); ?>" placeholder="Year"/>
                        <small class="error" id="error-shs_year"></small>
                    </div>
                </div>

                <!-- Vocational / Special Program (Optional) -->
                <div class="divider"></div>
                <h1 style="font-size: 20px; font-weight: bold; color: #01040aff;">Special Program Taken (Optional)</h1>
                <div class="form-control">
                    <label for="vocational_name">PROGRAM NAME</label>
                    <input type="text" name="vocname" id="vocational_name" value="<?php echo htmlspecialchars($vocname); ?>" placeholder="Input Text Field">
                </div>
                <div class="form-control">
                    <label for="vocational_address">SCHOOL / TRAINING CENTER</label>
                    <input type="text" name="vocaddress" id="vocational_address" value="<?php echo htmlspecialchars($vocaddress); ?>" placeholder="Input Text Field">
                </div>
                <div class="form-row" style="display: flex; gap: 10px;">
                    <div class="form-control" style="flex: 1;">
                        <label for="voc_type">TYPE</label>
                        <select name="voctype" id="voc_type">
                            <option value="">Select Item</option>
                            <option value="Public" <?php if ($voctype == 'Public') echo 'selected'; ?>>Public</option>
                            <option value="Private" <?php if ($voctype == 'Private') echo 'selected'; ?>>Private</option>
                        </select>
                    </div>
                    <div class="form-control" style="flex: 1;">
                        <label for="vocational_year">YEAR COMPLETED</label>
                        <input type="number" name="vocyear" id="vocational_year" value="<?php echo htmlspecialchars($vocyear); ?>" placeholder="Year"/>
                    </div>
                </div>

                <div class="submit-section">
                    <button type="submit" name="submit" class="confirm-btn">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>


<style>
.error {
    color: red;
    font-size: 13px;
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("educationForm").addEventListener("submit", function(e) {
        let isValid = true;
        const currentYear = new Date().getFullYear();
        document.querySelectorAll(".error").forEach(el => el.innerText = "");

        // Elementary
        const elemName = document.getElementById("elem_name").value.trim();
        const elemAddress = document.getElementById("elem_address").value.trim();
        const elemType = document.getElementById("elem_type").value;
        const elemYear = document.getElementById("elem_year").value;

        if (!elemName) {
            document.getElementById("error-elem_name").innerText = "The elementary school name is required!";
            isValid = false;
        }
        if (!elemAddress) {
            document.getElementById("error-elem_address").innerText = "The elementary school address is required!";
            isValid = false;
        }
        if (!elemType) {
            document.getElementById("error-elem_type").innerText = "Please select the elementary school type!";
            isValid = false;
        }
        if (!elemYear || isNaN(elemYear) || elemYear < 1900 || elemYear > currentYear) {
            document.getElementById("error-elem_year").innerText = "Enter a valid graduation year!";
            isValid = false;
        }

        // Junior High
        const midName = document.getElementById("middle_name").value.trim();
        const midAddress = document.getElementById("middle_address").value.trim();
        const midType = document.getElementById("mid_type").value;
        const midYear = document.getElementById("middle_year").value;

        if (!midName) {
            document.getElementById("error-middle_name").innerText = "The junior high school name is required!";
            isValid = false;
        }
        if (!midAddress) {
            document.getElementById("error-middle_address").innerText = "The junior high school address is required!";
            isValid = false;
        }
        if (!midType) {
            document.getElementById("error-mid_type").innerText = "Please select the junior high school type!";
            isValid = false;
        }
        if (!midYear || isNaN(midYear) || midYear < 1900 || midYear > currentYear) {
            document.getElementById("error-middle_year").innerText = "Enter a valid graduation year!";
            isValid = false;
        }

        // Senior High
        const shsName = document.getElementById("shs_name").value.trim();
        const shsAddress = document.getElementById("shs_address").value.trim();
        const shsType = document.getElementById("senior_type").value;
        const shsYear = document.getElementById("shs_year").value;

        if (!shsName) {
            document.getElementById("error-shs_name").innerText = "The senior high school name is required!";
            isValid = false;
        }
        if (!shsAddress) {
            document.getElementById("error-shs_address").innerText = "The senior high school address is required!";
            isValid = false;
        }
        if (!shsType) {
            document.getElementById("error-senior_type").innerText = "Please select the senior high type!";
            isValid = false;
        }
        if (!shsYear || isNaN(shsYear) || shsYear < 1900 || shsYear > currentYear) {
            document.getElementById("error-shs_year").innerText = "Enter a valid graduation year!";
            isValid = false;
        }

        // Prevent submission if there are errors
        if (!isValid) e.preventDefault();

        

    });
});

</script>
