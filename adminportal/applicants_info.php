<?php
include('../php/connection.php');
session_start();

// Redirect to login if admin session not set
if (!isset($_SESSION['admin'])) {
    header("Location: ../php/logout.php");
    exit();
}

// Fetch all applicants with JOINs using personal_info instead of accounts
$applicantsQuery = mysqli_query($conn, "
    SELECT 
        CONCAT(pi.firstname, ' ', pi.lastname) AS name,
        pi.username AS email,
        ai.program,
        ai.entry,
        a.status,
        a.grade_status
    FROM application_status a
    LEFT JOIN admission_info ai ON a.username = ai.username
    LEFT JOIN personal_info pi ON a.username = pi.username
    ORDER BY pi.firstname ASC, pi.lastname ASC
");

if (!$applicantsQuery) {
    die("Query Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Applicants Information</title>
<link rel="stylesheet" href="../css/admin.css">
<style>
    body { font-family: 'Segoe UI', sans-serif; background: #f8f9fa; margin: 0; padding: 0; }
    .content { padding: 30px; }
    h2 { margin-bottom: 20px; }

    .search-box { margin-bottom: 20px; }
    .search-box input {
        width: 100%;
        max-width: 400px;
        padding: 10px 12px;
        border-radius: 5px;
        border: 1px solid #ccc;
        font-size: 14px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background-color: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }

    th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #ddd; }
    th { background-color: #007bff; color: white; }
    tr:hover { background-color: #e2e8f0; }

    .status {
        font-weight: bold;
        padding: 4px 8px;
        border-radius: 5px;
        color: white;
        font-size: 13px;
    }
    
    .pending { background-color: #f0ad4e; }
    .approved { background-color: #28a745; }
    .exam-taken { background-color: #28a745; }
    .exam-not-taken { background-color: #6c757d; }

    /* Sidebar & topbar */
    .sidebar { width: 220px; background: darkblue; height: 100vh; position: fixed; color: #fff; padding-top: 20px; }
    .logo {
    text-align: center;
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 30px;
}.logo, .nav-top,
.nav-bottom {
    display: flex;
    flex-direction: column;
    gap: 15px;
    align-items: center;
}

.nav-btn {
    background: none;
    border: none;
    color: white;
    padding: 10px 20px;
    cursor: pointer;
    width: 100%;
    text-align: left;
    transition: background 0.2s;
}

.nav-btn:hover {
    background-color: rgb(81, 121, 253);
}

    .main { margin-left: 220px; }
    .topbar { display: flex; justify-content: space-between; align-items: center; padding: 15px 30px; background: #fff; border-bottom: 1px solid #ccc; }
    .topbar .right p { margin: 0; font-weight: 500; }
    .btn { padding: 8px 16px; border: none; border-radius: 5px; background:darkblue; color: #fff; cursor: pointer; font-weight: 500; transition: 0.3s; }
    .btn:hover { background: rgb(81, 121, 253)56b3; }
</style>
<link rel="stylesheet" href="..\css\admin.css">
</head>
<body>

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
      <a href="../php/logout.php"><button class="btn">Logout</button></a>
    </div>
  </div>

  <!-- Content -->
  <div class="content">
    <h2>Applicants Information</h2>

    <!-- Search box -->
    <div class="search-box">
        <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Search by Name, Email, Program or Entry...">
    </div>

    <table id="applicantsTable">
      <thead>
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Email</th>
          <th>Program</th>
          <th>Entry</th>
          <th>Application Status</th>
          <th>Exam Status</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $count = 1;

        if (mysqli_num_rows($applicantsQuery) > 0) {
            while ($row = mysqli_fetch_assoc($applicantsQuery)) {

                $name = !empty($row['name']) ? $row['name'] : "N/A";
                $email = !empty($row['email']) ? $row['email'] : "N/A";
                $program = !empty($row['program']) ? $row['program'] : "N/A";
                $entry = !empty($row['entry']) ? $row['entry'] : "N/A";

                $appStatus = ($row['status'] == 0) ? 
                    "<span class='status pending'>Pending</span>" : 
                    "<span class='status approved'>Approved</span>";

                $examStatus = ($row['grade_status'] == 0) ? 
                    "<span class='status exam-not-taken'>Not Taken</span>" : 
                    "<span class='status exam-taken'>Taken</span>";

                echo "<tr>
                        <td>{$count}</td>
                        <td>{$name}</td>
                        <td>{$email}</td>
                        <td>{$program}</td>
                        <td>{$entry}</td>
                        <td>{$appStatus}</td>
                        <td>{$examStatus}</td>
                      </tr>";
                $count++;
            }
        } else {
            echo "<tr><td colspan='7'>No applicants found</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</div>

<script>
function filterTable() {
    var input = document.getElementById("searchInput");
    var filter = input.value.toLowerCase();
    var table = document.getElementById("applicantsTable");
    var tr = table.getElementsByTagName("tr");

    for (var i = 1; i < tr.length; i++) {
        tr[i].style.display = "none";
        var tdName = tr[i].getElementsByTagName("td")[1];
        var tdEmail = tr[i].getElementsByTagName("td")[2];
        var tdProgram = tr[i].getElementsByTagName("td")[3];
        var tdEntry = tr[i].getElementsByTagName("td")[4];

        if (tdName || tdEmail || tdProgram || tdEntry) {
            var txtValueName = tdName.textContent || tdName.innerText;
            var txtValueEmail = tdEmail.textContent || tdEmail.innerText;
            var txtValueProgram = tdProgram.textContent || tdProgram.innerText;
            var txtValueEntry = tdEntry.textContent || tdEntry.innerText;

            if (txtValueName.toLowerCase().indexOf(filter) > -1 ||
                txtValueEmail.toLowerCase().indexOf(filter) > -1 ||
                txtValueProgram.toLowerCase().indexOf(filter) > -1 ||
                txtValueEntry.toLowerCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            }
        }
    }
}

</script>


</body>
</html>
