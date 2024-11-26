<?php
session_start();
include("php/config.php");

// Ensure user is logged in
if (!isset($_SESSION['userid']) || empty($_SESSION['userid'])) {
    header("Location: login.php");
    exit;
}

// Fetch DoctorID for the logged-in user
$useraccountid = $_SESSION['userid'];
$doctoridquery = "SELECT DoctorID FROM doctor WHERE UserAccountID = '$useraccountid'";
$doctorresult = mysqli_query($conn, $doctoridquery);

if ($doctorresult && mysqli_num_rows($doctorresult) > 0) {
    $row = mysqli_fetch_assoc($doctorresult);
    $_SESSION['DoctorID'] = $row['DoctorID'];
} else {
    echo "Error: DoctorID not found. Please contact support.";
    exit;
}

$active_section = isset($_GET['section']) ? $_GET['section'] : 'home';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="doctor.css">
    <title>OHMS - Doctor Portal</title>
</head>
<body>
<header>
    <p><a href="index.html" class="logo">OHMS</a></p>
    <nav class="doctor">
        <a href="?section=home" class="<?= $active_section === 'home' ? 'active' : '' ?>">Home</a>
        <a href="?section=MonitorPatients" class="<?= $active_section === 'MonitorPatients' ? 'active' : '' ?>">Monitor Patients</a>
        <a href="?section=Prescription" class="<?= $active_section === 'Prescription' ? 'active' : '' ?>">Prescription</a>
        <a href="?section=DoctorProfile" class="<?= $active_section === 'DoctorProfile' ? 'active' : '' ?>">Profile</a>
        <a href="index.html">Logout</a>
        <span class="doctormenu"></span>
    </nav>
</header>

<main>
    <?php
    switch ($active_section) {
        case 'MonitorPatients':
            ?>
            <section id="MonitorPatients">
                <form role="search" method="GET" class="searchbar">
                    <input type="hidden" name="section" value="ViewPatients">
                    <label class="label" for="search_patients">Search Patients</label>
                    <input id="search_patients" name="search_patients" type="search" placeholder="Search..." autofocus required>
                    <button type="submit">Go</button>
                </form>
                <table>
                    <thead>
                        <tr>
                            <th>Patient ID</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Date Of Birth</th>
                            <th>Gender</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (isset($_GET['search_patients'])) {
                            $search = mysqli_real_escape_string($conn, $_GET['search_patients']);
                            $query = "SELECT * FROM patient WHERE CONCAT(PatientID, FirstName, LastName) LIKE '%$search%'";
                        } else {
                            $query = "SELECT * FROM patient";
                        }
                        $result = mysqli_query($conn, $query);

                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>
                                    <td>" . htmlspecialchars($row['PatientID']) . "</td>
                                    <td>" . htmlspecialchars($row['Firstname']) . "</td>
                                    <td>" . htmlspecialchars($row['Lastname']) . "</td>
                                    <td>" . htmlspecialchars($row['DOB']) . "</td>
                                    <td>" . htmlspecialchars($row['Sex']) . "</td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>No Records Found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </section>
            <?php
            break;

        case 'Prescription':
            ?>
            <form role="search" method="GET" class="searchbar">
                <input type="hidden" name="section" value="ViewPatients">
                <label for="search_patients">Search Patients</label>
                <input id="search_patients" name="seach_patients" type="search" placeholder="Search.." autofocus required> 
                <button type="submit">Go</button>
            </form>

            <table>
                    <thead>
                        <tr>
                            <th>Patient ID</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Date Of Birth</th>
                            <th>Gender</th>
                            <th>Action</th>
                        </tr>
                    </thead>
            <?php
                if (isset($_GET['search_patients'])) {
                    $search = mysqli_real_escape_string($conn, $_GET['search_patients']);
                    $query = "SELECT * FROM patient WHERE CONCAT(PatientID, FirstName, LastName) LIKE '%$search%'";
                    $patientID = $row['PatientID']; 
                    
                } else {
                    $query = "SELECT * FROM patient";
                }
                $result = mysqli_query($conn, $query);
                if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                            <td>" . htmlspecialchars($row['PatientID']) . "</td>
                            <td>" . htmlspecialchars($row['Firstname']) . "</td>
                            <td>" . htmlspecialchars($row['Lastname']) . "</td>
                            <td>" . htmlspecialchars($row['DOB']) . "</td>
                            <td>" . htmlspecialchars($row['Sex']) . "</td>
                             <td><a href='?section=AssignTest&patientID=" . htmlspecialchars($row['PatientID']) . "'>Assign Test</a></td>
                        </tr>";

                    }
                } else {
                    echo "<tr><td colspan='6'>No Records Found</td></tr>";
                }
                ?>
            </tbody>
        </table>
          
            </section>
            <?php
            break;
            case 'AssignTest':
                case 'AssignTest':
                    $patientID = $_GET['patientID'] ?? null;
                    if (!$patientID) {
                        echo "<p>Error: No patient selected. Please go back and select a patient.</p>";
                    } else {
                        ?>
                        <h3>Assign Tests for Patient ID: <?= htmlspecialchars($patientID); ?></h3>
                        <form method="POST" action="process_assign_tests.php">
                        <input type="hidden" name="patientID" value="<?= htmlspecialchars($patientID); ?>">
                        
                        <h4>General Tests</h4>
                        <label class="assign-label"><input type="checkbox" name="tests[]" value="CT-Scan"> CT-Scan</label><br>
                        <label class="assign-label"><input type="checkbox" name="tests[]" value="ECG"> ECG</label><br>
                        <label class="assign-label"><input type="checkbox" name="tests[]" value="Ultrasound"> Ultrasound</label><br>
                        <label class="assign-label"><input type="checkbox" name="tests[]" value="X-Ray"> X-Ray</label><br>
                        <label class="assign-label"><input type="checkbox" name="tests[]" value="Urine Test"> Urine Test</label><br>

                        <h4>Blood Tests</h4>
                        <label class="assign-label"><input type="checkbox" name="tests[]" value="Coagulation"> Coagulation</label><br>
                        <label class="assign-label"><input type="checkbox" name="tests[]" value="Endocrinology"> Endocrinology</label><br>
                        <label class="assign-label"><input type="checkbox" name="tests[]" value="Liver Function Test"> Liver Function Test</label><br>
                        <label class="assign-label"><input type="checkbox" name="tests[]" value="Pancreas Function Test"> Pancreas Function Test</label><br>
                        <label class="assign-label"><input type="checkbox" name="tests[]" value="Renal Function Test"> Renal Function Test</label><br>
                        <label class="assign-label"><input type="checkbox" name="tests[]" value="Routine Chemistry Test"> Routine Chemistry Test</label><br>
                        <label class="assign-label"><input type="checkbox" name="tests[]" value="Routine Hematology Test"> Routine Hematology Test</label><br>
                        <label class="assign-label"><input type="checkbox" name="tests[]" value="Tumor Marker"> Tumor Marker</label><br>


                         <button type="submit">Assign Tests</button>
                        </form>

                        
            <?php
                    }
                    break;
            
        case 'DoctorProfile':
            ?>
            <section id="DoctorProfile">
                <h2>My Profile</h2>
                <?php
                $doctor_id = $_SESSION['DoctorID']; // Assuming doctor ID is stored in session
                $query = "SELECT * FROM doctor WHERE DoctorID = '$doctor_id'";
                $result = mysqli_query($conn, $query);

                if ($result && mysqli_num_rows($result) > 0) {
                    $doctor = mysqli_fetch_assoc($result);
                    echo "<p><strong>Name:</strong> " . htmlspecialchars($doctor['Firstname'] . ' ' . $doctor['Lastname']) . "</p>";
                    echo "<p><strong>Email:</strong> " . htmlspecialchars($doctor['Email']) . "</p>";
                    echo "<p><strong>Phone:</strong> " . htmlspecialchars($doctor['Phone']) . "</p>";
                    echo "<p><strong>Specialization:</strong> " . htmlspecialchars($doctor['Specialization']) . "</p>";
                } else {
                    echo "<p>Profile information not found.</p>";
                }
                ?>
            </section>
            <?php
            break;

        default:
            ?>
            <section id="home">
                <h2>Welcome, Doctor, to the Online Health Monitor System</h2>
            </section>
            <?php
    }
    ?>
</main>
</body>
</html>

