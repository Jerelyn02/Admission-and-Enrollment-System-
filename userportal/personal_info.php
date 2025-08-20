<?php
include('../php/connection.php');

include_once('../php/check_status_helper.php');

session_start();

$user = $_SESSION['user'];

$msg = '';
$errors = [];
$edit_mode = false;

// Initialize variables
$firstname = $middlename = $lastname = $region = $province = $town = '';
$phonenumber = $civilstatus = $sex = $birthday = $birthplace = $religion = '';

// Fetch existing personal info
$res = mysqli_query($conn, "SELECT * FROM personal_info WHERE username = '$user'");
if(mysqli_num_rows($res) > 0){
    $edit_mode = true;
    $row = mysqli_fetch_assoc($res);
    $firstname = $row['firstname'];
    $middlename = $row['middlename'];
    $lastname = $row['lastname'];
    $region = $row['region'];
    $province = $row['province'];
    $town = $row['town'];
    $phonenumber = $row['phonenumber'];
    $civilstatus = $row['civilstatus'];
    $sex = $row['sex'];
    $birthday = $row['birthday'];
    $birthplace = $row['birthplace'];
    $religion = $row['religion'];
}

if(isset($_POST['submit'])){
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $lastname = $_POST['lastname'];
    $region = $_POST['region'];
    $province = $_POST['province'];
    $town = $_POST['town'];
    $phonenumber = $_POST['phone'];
    $civilstatus = $_POST['civilstatus'];
    $sex = $_POST['sex'];
    $birthday = $_POST['birthday'];
    $birthplace = $_POST['birthplace'];
    $religion = $_POST['religion'];

    // Validation
    if(empty($firstname)) $errors['firstname'] = "Firstname is required!";
    if(empty($middlename)) $errors['middlename'] = "Middlename is required!";
    if(empty($lastname)) $errors['lastname'] = "Lastname is required!";
    if(empty($civilstatus)) $errors['civilstatus'] = "Civil status is required!";
    if(empty($sex)) $errors['sex'] = "Sex is required!";
    if(empty($birthday)) $errors['birthday'] = "Birthday is required!";
    if(empty($birthplace)) $errors['birthplace'] = "Birthplace is required!";
    if(empty($religion)) $errors['religion'] = "Religion is required!";
    if(empty($region)) $errors['region'] = "Region is required!";
    if(empty($province)) $errors['province'] = "Province is required!";
    if(empty($town)) $errors['town'] = "Town is required!";
    if(empty($phonenumber)) $errors['phonenumber'] = "Phone number is required!";

        if(empty($errors)){
        // Prepare data for database
        $firstname = mysqli_real_escape_string($conn, $firstname);
        $middlename = mysqli_real_escape_string($conn, $middlename);
        $lastname = mysqli_real_escape_string($conn, $lastname);
        $region = mysqli_real_escape_string($conn, $region);
        $province = mysqli_real_escape_string($conn, $province);
        $town = mysqli_real_escape_string($conn, $town);
        $phonenumber = mysqli_real_escape_string($conn, $phonenumber);
        $civilstatus = mysqli_real_escape_string($conn, $civilstatus);
        $sex = mysqli_real_escape_string($conn, $sex);
        $birthday = mysqli_real_escape_string($conn, $birthday);
        $birthplace = mysqli_real_escape_string($conn, $birthplace);
        $religion = mysqli_real_escape_string($conn, $religion);

        if($edit_mode){
            // Update existing record
            $sql = "UPDATE personal_info SET 
                    firstname = '$firstname',
                    middlename = '$middlename',
                    lastname = '$lastname',
                    region = '$region',
                    province = '$province',
                    town = '$town',
                    phonenumber = '$phonenumber',
                    civilstatus = '$civilstatus',
                    sex = '$sex',
                    birthday = '$birthday',
                    birthplace = '$birthplace',
                    religion = '$religion'
                    WHERE username = '$user'";
        } else {
            // Insert new record
            $sql = "INSERT INTO personal_info (username, firstname, middlename, lastname, region, province, town, phonenumber, civilstatus, sex, birthday, birthplace, religion)
                    VALUES ('$user', '$firstname', '$middlename', '$lastname', '$region', '$province', '$town', '$phonenumber', '$civilstatus', '$sex', '$birthday', '$birthplace', '$religion')";
        }

        if(mysqli_query($conn, $sql)){
            // Mark personal info as completed
            $check_status_sql = "INSERT INTO check_status (username, personal_info_completed) VALUES ('$user',1) 
                                 ON DUPLICATE KEY UPDATE personal_info_completed = 1";
            mysqli_query($conn, $check_status_sql);

            checkAndGenerateControlNumber($conn, $user);

            $_SESSION['admission_complete'] = "Your Personal Information has been Submitted Successfully!";
            header("Location: useradmission.php");
            exit();
        } else {
            $msg = "Error updating record: " . mysqli_error($conn);
        }
    }
}

?>

<?php include('../php/userformheader.php'); ?>
<link rel="stylesheet" href="../css/user_form.css">

<div class="content">
    <div class="admission-form">
        <h2>Personal Information</h2>
        <?php if(!empty($msg)): ?>
            <p class="msg" style="color:green;"><?php echo $msg; ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <!-- PERSONAL INFO -->
            <div class="form-group">
                <div class="form-control">
                    <label for="firstname">Firstname</label>
                    <input type="text" name="firstname" id="firstname" value="<?php echo htmlspecialchars($firstname); ?>">
                    <?php if(isset($errors['firstname'])): ?><span class="msg" style="color:red;"><?php echo $errors['firstname']; ?></span><?php endif; ?>
                </div>
                <div class="form-control">
                    <label for="middlename">Middlename</label>
                    <input type="text" name="middlename" id="middlename" value="<?php echo htmlspecialchars($middlename); ?>">
                    <?php if(isset($errors['middlename'])): ?><span class="msg" style="color:red;"><?php echo $errors['middlename']; ?></span><?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <div class="form-control">
                    <label for="lastname">Lastname</label>
                    <input type="text" name="lastname" id="lastname" value="<?php echo htmlspecialchars($lastname); ?>">
                    <?php if(isset($errors['lastname'])): ?><span class="msg" style="color:red;"><?php echo $errors['lastname']; ?></span><?php endif; ?>
                </div>
                <div class="form-control">
                    <label for="sex">Sex</label>
                    <select name="sex" id="sex">
                        <option value="">Select Sex</option>
                        <option value="Male" <?php if($sex=="Male") echo "selected"; ?>>Male</option>
                        <option value="Female" <?php if($sex=="Female") echo "selected"; ?>>Female</option>
                    </select>
                    <?php if(isset($errors['sex'])): ?><span class="msg" style="color:red;"><?php echo $errors['sex']; ?></span><?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <div class="form-control">
                    <label for="civilstatus">Civil Status</label>
                    <select name="civilstatus" id="civilstatus">
                        <option value="">Select Status</option>
                        <option value="Single" <?php if($civilstatus=="Single") echo "selected"; ?>>Single</option>
                        <option value="Married" <?php if($civilstatus=="Married") echo "selected"; ?>>Married</option>
                    </select>
                    <?php if(isset($errors['civilstatus'])): ?><span class="msg" style="color:red;"><?php echo $errors['civilstatus']; ?></span><?php endif; ?>
                </div>
                <div class="form-control">
                    <label for="birthday">Birthday</label>
                    <input type="date" name="birthday" id="birthday" value="<?php echo htmlspecialchars($birthday); ?>">
                    <?php if(isset($errors['birthday'])): ?><span class="msg" style="color:red;"><?php echo $errors['birthday']; ?></span><?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <div class="form-control">
                    <label for="birthplace">Birthplace</label>
                    <input type="text" name="birthplace" id="birthplace" value="<?php echo htmlspecialchars($birthplace); ?>">
                    <?php if(isset($errors['birthplace'])): ?><span class="msg" style="color:red;"><?php echo $errors['birthplace']; ?></span><?php endif; ?>
                </div>
                <div class="form-control">
                    <label for="religion">Religion</label>
                    <input type="text" name="religion" id="religion" value="<?php echo htmlspecialchars($religion); ?>">
                    <?php if(isset($errors['religion'])): ?><span class="msg" style="color:red;"><?php echo $errors['religion']; ?></span><?php endif; ?>
                </div>
            </div>

            <!-- ADDRESS INFO -->
            <div class="form-group">
                <div class="form-control">
                    <label for="region">Region</label>
                    <select name="region" id="region">
                        <option value="">Select Region</option>
                        <option value="Ilocos Region (Region I)" <?php if($region=="Ilocos Region (Region I)") echo "selected"; ?>>Ilocos Region (I)</option>
                        <option value="Cagayan Valley (Region II)" <?php if($region=="Cagayan Valley (Region II)") echo "selected"; ?>>Cagayan Valley (II)</option>
                        <option value="Central Luzon (Region III)" <?php if($region=="Central Luzon (Region III)") echo "selected"; ?>>Central Luzon (III)</option>
                        <option value="CALABARZON (Region IV-A)" <?php if($region=="CALABARZON (Region IV-A)") echo "selected"; ?>>CALABARZON (IV-A)</option>
                        <option value="MIMAROPA (Region IV-B)" <?php if($region=="MIMAROPA (Region IV-B)") echo "selected"; ?>>MIMAROPA (IV-B)</option>
                        <option value="Bicol Region (Region V)" <?php if($region=="Bicol Region (Region V)") echo "selected"; ?>>Bicol Region (V)</option>
                        <option value="Western Visayas (Region VI)" <?php if($region=="Western Visayas (Region VI)") echo "selected"; ?>>Western Visayas (VI)</option>
                        <option value="Central Visayas (Region VII)" <?php if($region=="Central Visayas (Region VII)") echo "selected"; ?>>Central Visayas (VII)</option>
                        <option value="Eastern Visayas (Region VIII)" <?php if($region=="Eastern Visayas (Region VIII)") echo "selected"; ?>>Eastern Visayas (VIII)</option>
                        <option value="Zamboanga Peninsula (Region IX)" <?php if($region=="Zamboanga Peninsula (Region IX)") echo "selected"; ?>>Zamboanga Peninsula (IX)</option>
                        <option value="Northern Mindanao (Region X)" <?php if($region=="Northern Mindanao (Region X)") echo "selected"; ?>>Northern Mindanao (X)</option>
                        <option value="Davao Region (Region XI)" <?php if($region=="Davao Region (Region XI)") echo "selected"; ?>>Davao Region (XI)</option>
                        <option value="SOCCSKSARGEN (Region XII)" <?php if($region=="SOCCSKSARGEN (Region XII)") echo "selected"; ?>>SOCCSKSARGEN (XII)</option>
                        <option value="Caraga (Region XIII)" <?php if($region=="Caraga (Region XIII)") echo "selected"; ?>>Caraga (XIII)</option>
                        <option value="BARMM" <?php if($region=="BARMM") echo "selected"; ?>>BARMM</option>
                        <option value="CAR" <?php if($region=="CAR") echo "selected"; ?>>CAR</option>
                        <option value="NCR" <?php if($region=="NCR") echo "selected"; ?>>NCR</option>
                    </select>
                    <?php if(isset($errors['region'])): ?><span class="msg" style="color:red;"><?php echo $errors['region']; ?></span><?php endif; ?>
                </div>

                <div class="form-control">
                    <label for="province">Province</label>
                    <select name="province" id="province">
                        <option value="">Select Province</option>
                    </select>
                    <?php if(isset($errors['province'])): ?><span class="msg" style="color:red;"><?php echo $errors['province']; ?></span><?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <div class="form-control">
                    <label for="town">Town</label>
                    <select name="town" id="town">
                        <option value="">Select Town</option>
                    </select>
                    <?php if(isset($errors['town'])): ?><span class="msg" style="color:red;"><?php echo $errors['town']; ?></span><?php endif; ?>
                </div>

                <div class="form-control">
                    <label for="phone">Phone Number</label>
                    <input type="text" name="phone" id="phone" value="<?php echo htmlspecialchars($phonenumber); ?>">
                    <?php if(isset($errors['phonenumber'])): ?><span class="msg" style="color:red;"><?php echo $errors['phonenumber']; ?></span><?php endif; ?>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="submit-section" style="margin-top: 20px; display: flex; justify-content: flex-start;">
                <button type="submit" name="submit" class="confirm-btn">Confirm</button>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript for dynamic filtering -->
<script>
const regionProvinceTown = {
    "Ilocos Region (Region I)": {
        "Ilocos Norte": ["Laoag", "Batac", "Paoay", "Pagudpud"],
        "Ilocos Sur": ["Vigan", "Candon", "Santa", "Tagudin"],
        "La Union": ["San Fernando", "Bacnotan", "Agoo", "Bangar"],
        "Pangasinan": ["Lingayen", "Dagupan", "Alaminos", "Urdaneta"]
    },
    "Cagayan Valley (Region II)": {
        "Batanes": ["Basco", "Itbayat", "Mahatao", "Sabtang"],
        "Cagayan": ["Tuguegarao", "Aparri", "Cauayan", "Lal-lo"],
        "Isabela": ["Ilagan", "Cauayan", "Santiago", "Cordon"],
        "Nueva Vizcaya": ["Bayombong", "Solano", "Aritao", "Sta. Fe"],
        "Quirino": ["Cabarroguis", "Diffun", "Maddela", "Nagtipunan"]
    },
    "Central Luzon (Region III)": {
        "Aurora": ["Baler", "Maria Aurora", "San Luis", "Dipaculao"],
        "Bataan": ["Balanga", "Dinalupihan", "Abucay", "Mariveles"],
        "Bulacan": ["Malolos", "Meycauayan", "San Jose del Monte", "Baliuag"],
        "Nueva Ecija": ["Cabanatuan", "Gapan", "Palayan", "San Jose"],
        "Pampanga": ["San Fernando", "Angeles", "Mabalacat", "Lubao"],
        "Tarlac": ["Tarlac City", "Concepcion", "Victoria", "Paniqui"],
        "Zambales": ["Olongapo", "Subic", "Iba", "Botolan"]
    },
    "CALABARZON (Region IV-A)": {
        "Cavite": ["Dasmariñas", "Bacoor", "Imus", "Tagaytay", "Cavite City"],
        "Laguna": ["Calamba", "San Pablo", "Sta. Rosa", "Biñan", "Santa Cruz"],
        "Batangas": ["Batangas City", "Lipa", "Tanauan", "Nasugbu"],
        "Rizal": ["Antipolo", "Cainta", "Angono", "Taytay"],
        "Quezon": ["Lucena", "Tayabas", "Candelaria", "Sariaya"]
    },
    "MIMAROPA (Region IV-B)": {
        "Occidental Mindoro": ["Mamburao", "Calintaan", "Abra de Ilog", "San Jose"],
        "Oriental Mindoro": ["Calapan", "Baco", "Puerto Galera", "Socorro"],
        "Marinduque": ["Boac", "Mogpog", "Sta. Cruz", "Gasan"],
        "Romblon": ["Romblon", "Odiongan", "San Jose", "Looc"],
        "Palawan": ["Puerto Princesa", "El Nido", "Coron", "Roxas"]
    },
    "Bicol Region (Region V)": {
        "Albay": ["Legazpi", "Tabaco", "Daraga", "Libon"],
        "Camarines Norte": ["Daet", "Labo", "Capalonga", "Jose Panganiban"],
        "Camarines Sur": ["Naga", "Iriga", "Sipocot", "Lagonoy"],
        "Catanduanes": ["Virac", "Baras", "Bagamanoc", "San Andres"],
        "Masbate": ["Masbate City", "Aroroy", "Cataingan", "Mobo"],
        "Sorsogon": ["Sorsogon City", "Bulusan", "Gubat", "Matnog"]
    },
    "Western Visayas (Region VI)": {
        "Aklan": ["Kalibo", "Malay", "Buruanga", "Numancia"],
        "Antique": ["San Jose de Buenavista", "Sibalom", "Hamtic", "Patnongon"],
        "Capiz": ["Roxas City", "Panay", "Cuartero", "Dao"],
        "Guimaras": ["Jordan", "Buenavista", "Nueva Valencia", "San Lorenzo"],
        "Iloilo": ["Iloilo City", "Dumangas", "Pavia", "Miagao"],
        "Negros Occidental": ["Bacolod", "Cadiz", "San Carlos", "Talisay"]
    },
    "Central Visayas (Region VII)": {
        "Bohol": ["Tagbilaran", "Loboc", "Panglao", "Cortes"],
        "Cebu": ["Cebu City", "Mandaue", "Lapu-Lapu", "Talisay", "Danao", "Molino 234"],
        "Negros Oriental": ["Dumaguete", "Bais", "Bayawan", "Sibulan"],
        "Siquijor": ["Siquijor", "Larena", "Maria", "Lazi"]
    },
    "Eastern Visayas (Region VIII)": {
        "Biliran": ["Biliran", "Almeria", "Caibiran", "Maripipi"],
        "Leyte": ["Tacloban", "Ormoc", "Baybay", "Maasin"],
        "Northern Samar": ["Catarman", "Laoang", "Allen", "San Roque"],
        "Samar": ["Catbalogan", "Calbayog", "Gandara", "Basey"],
        "Eastern Samar": ["Borongan", "Arteche", "Jipapad", "San Julian"],
        "Southern Leyte": ["Maasin", "Sogod", "Tomas Oppus", "Silago"]
    },
    "Zamboanga Peninsula (Region IX)": {
        "Zamboanga del Norte": ["Dipolog", "Dapitan", "Tampilisan", "Rizal"],
        "Zamboanga del Sur": ["Pagadian", "Zamboanga City", "Dumingag", "Tukuran"],
        "Zamboanga Sibugay": ["Ipil", "Buug", "Malangas", "Titay"]
    },
    "Northern Mindanao (Region X)": {
        "Bukidnon": ["Malaybalay", "Valencia", "Manolo Fortich", "Baungon"],
        "Camiguin": ["Mambajao", "Catarman", "Mahinog", "Sagay"],
        "Lanao del Norte": ["Iligan", "Tubod", "Kapatagan", "Bacolod"],
        "Misamis Occidental": ["Ozamiz", "Oroquieta", "Tangub", "Jimenez"],
        "Misamis Oriental": ["Cagayan de Oro", "El Salvador", "Gingoog", "Magsaysay"]
    },
    "Davao Region (Region XI)": {
        "Davao de Oro": ["Nabunturan", "Monkayo", "Maragusan", "Laak"],
        "Davao del Norte": ["Tagum", "Panabo", "Santo Tomas", "Asuncion"],
        "Davao del Sur": ["Davao City", "Padada", "Digos", "Bansalan"],
        "Davao Occidental": ["Malita", "Jose Abad Santos", "Don Marcelino", "Santa Maria"],
        "Davao Oriental": ["Mati", "Baganga", "Caraga", "Boston"]
    },
    "SOCCSKSARGEN (Region XII)": {
        "Cotabato": ["Kidapawan", "Matalam", "Makilala", "Tulunan"],
        "Sarangani": ["Alabel", "Glan", "Kiamba", "Maasim"],
        "South Cotabato": ["Koronadal", "Polomolok", "Santo Niño", "Tampakan"],
        "Sultan Kudarat": ["Isulan", "Tacurong", "Kalamansig", "Lutayan"]
    },
    "Caraga (Region XIII)": {
        "Agusan del Norte": ["Butuan", "Cabadbaran", "Buenavista", "Nasipit"],
        "Agusan del Sur": ["Bayugan", "Trento", "Prosperidad", "San Francisco"],
        "Surigao del Norte": ["Surigao City", "Burgos", "Claver", "Socorro"],
        "Surigao del Sur": ["Tandag", "Bislig", "Cagwait", "Tagbina"],
        "Dinagat Islands": ["Libjo", "Cagdianao", "Basilisa", "Tubajon"]
    },
    "BARMM": {
        "Basilan": ["Isabela", "Lamitan", "Maluso", "Tipo-Tipo"],
        "Lanao del Sur": ["Marawi", "Maguing", "Balindong", "Saguiaran"],
        "Maguindanao": ["Cotabato City", "Buluan", "Datu Piang", "Shariff Aguak"],
        "Sulu": ["Jolo", "Hadji Panglima Tahil", "Indanan", "Parang"],
        "Tawi-Tawi": ["Bongao", "Mapun", "Sapa-Sapa", "Tandubas"]
    },
    "CAR": {
        "Abra": ["Bangued", "Tineg", "Bucay", "Lagangilang"],
        "Apayao": ["Kabugao", "Conner", "Flora", "Calanasan"],
        "Benguet": ["Baguio", "La Trinidad", "Itogon", "Tuba"],
        "Ifugao": ["Hapao", "Banaue", "Kiangan", "Hingyon"],
        "Kalinga": ["Tabuk", "Lubuagan", "Pinukpuk", "Rizal"],
        "Mountain Province": ["Bontoc", "Sagada", "Bauko", "Paracelis"]
    },
    "NCR": {
        "Metro Manila": ["Manila", "Quezon City", "Makati", "Pasay", "Pasig", "Taguig", "Caloocan"]
    }
};

const regionSelect = document.getElementById('region');
const provinceSelect = document.getElementById('province');
const townSelect = document.getElementById('town');

function populateProvinces(){
    provinceSelect.innerHTML = '<option value="">Select Province</option>';
    townSelect.innerHTML = '<option value="">Select Town</option>';
    const provinces = regionProvinceTown[regionSelect.value];
    if(provinces){
        for(const prov in provinces){
            const option = document.createElement('option');
            option.value = prov;
            option.textContent = prov;
            if(prov === "<?php echo $province; ?>") option.selected = true;
            provinceSelect.appendChild(option);
        }
    }
}

function populateTowns(){
    townSelect.innerHTML = '<option value="">Select Town</option>';
    const towns = regionProvinceTown[regionSelect.value]?.[provinceSelect.value];
    if(towns){
        towns.forEach(town=>{
            const option = document.createElement('option');
            option.value = town;
            option.textContent = town;
            if(town === "<?php echo $town; ?>") option.selected = true;
            townSelect.appendChild(option);
        });
    }
}

regionSelect.addEventListener('change', populateProvinces);
provinceSelect.addEventListener('change', populateTowns);

// Pre-populate on page load
window.addEventListener('load', ()=>{
    if(regionSelect.value){
        populateProvinces(); // fills provinces
        provinceSelect.value = "<?php echo $province; ?>"; // select saved province
        populateTowns(); // fills towns for selected province
        townSelect.value = "<?php echo $town; ?>"; // select saved town
    }
});

</script>
