<?php
session_start();
include("php/config.php");

// Determine the active section
$active_section = isset($_GET['section']) ? $_GET['section'] : 'home';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Userpage.css">
    <title>OHMS - Doctor Portal</title>
</head>
<body>
<header>
<<<<<<< HEAD
    <p><a href="index.html" class="logo">OHMS</a></p>
    <nav class="user">
        <a href="?section=home" class="<?= $active_section === 'home' ? 'active' : '' ?>">Home</a>
        <a href="?section=ViewDoctors" class="<?= $active_section === 'ViewDoctors' ? 'active' : '' ?>">My Doctors</a>
        <a href="?section=Prescription" class="<?= $active_section === 'Prescription' ? 'active' : '' ?>">Prescription</a>
        <a href="?section=MyProfile" class="<?= $active_section === 'MyProfile' ? 'active' : '' ?>">My Profile</a>
        <a href="logout.php">Logout</a>
        <span></span>
    </nav>
</header>


=======
    <p><a href="?section=home" class="logo">OHMS</a></p>
    <nav class="user">
        <a href="?section=home" class="<?= $active_section === 'home' ? 'active' : '' ?>">Home</a>
        <a href="?section=ViewPatients" class="<?= $active_section === 'ViewPatients' ? 'active' : '' ?>">View Patients</a>
        <a href="?section=PatientHistory" class="<?= $active_section === 'PatientHistory' ? 'active' : '' ?>">Patient History</a>
        <a href="?section=DoctorProfile" class="<?= $active_section === 'DoctorProfile' ? 'active' : '' ?>">Profile</a>
        <a href="index.html">Logout</a>
        <span></span>
    </nav>
</header>
>>>>>>> f3186bd8835a624c4269f3c24bb5bcfa14d776ed

<main>
    <?php
    switch ($active_section) {
<<<<<<< HEAD
        case 'ViewDoctors':
            ?>
            <section id="ViewDoctors">
                <form role="search" method="GET" class="searchbar">
                    <input type="hidden" name="section" value="ViewDoctors">
                    <label for="search_doctors">Search Doctors</label>
                    <input id="search_doctors" name="search_doctors" type="search" placeholder="Search..." autofocus required>
=======
        case 'ViewPatients':
            ?>
            <section id="ViewPatients">
                <form role="search" method="GET" class="searchbar">
                    <input type="hidden" name="section" value="ViewPatients">
                    <label for="search_patients">Search Patients</label>
                    <input id="search_patients" name="search_patients" type="search" placeholder="Search..." autofocus required>
>>>>>>> f3186bd8835a624c4269f3c24bb5bcfa14d776ed
                    <button type="submit">Go</button>
                </form>
                <table>
                    <thead>
                        <tr>
<<<<<<< HEAD
                            <th>Doctor ID</th>
=======
                            <th>Patient ID</th>
>>>>>>> f3186bd8835a624c4269f3c24bb5bcfa14d776ed
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Age</th>
                            <th>Gender</th>
<<<<<<< HEAD
                           
=======
                            <th>Last Visit Date</th>
>>>>>>> f3186bd8835a624c4269f3c24bb5bcfa14d776ed
                        </tr>
                    </thead>
                    <tbody>
                        <?php
<<<<<<< HEAD
                        if (isset($_GET['search_doctors'])) {
                            $search = mysqli_real_escape_string($conn, $_GET['search_doctors']);
                            $query = "SELECT * FROM doctor WHERE CONCAT(DoctorID, FirstName, LastName) LIKE '%$search%'";
                        } else {
                            $query = "SELECT * FROM doctor";
=======
                        if (isset($_GET['search_patients'])) {
                            $search = mysqli_real_escape_string($conn, $_GET['search_patients']);
                            $query = "SELECT * FROM patients WHERE CONCAT(PatientID, FirstName, LastName) LIKE '%$search%'";
                        } else {
                            $query = "SELECT * FROM patients";
>>>>>>> f3186bd8835a624c4269f3c24bb5bcfa14d776ed
                        }
                        $result = mysqli_query($conn, $query);

                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>
<<<<<<< HEAD
                                    <td>" . htmlspecialchars($row['DoctorID']) . "</td>
=======
                                    <td>" . htmlspecialchars($row['PatientID']) . "</td>
>>>>>>> f3186bd8835a624c4269f3c24bb5bcfa14d776ed
                                    <td>" . htmlspecialchars($row['FirstName']) . "</td>
                                    <td>" . htmlspecialchars($row['LastName']) . "</td>
                                    <td>" . htmlspecialchars($row['Age']) . "</td>
                                    <td>" . htmlspecialchars($row['Gender']) . "</td>
<<<<<<< HEAD
                                   
=======
                                    <td>" . htmlspecialchars($row['LastVisitDate']) . "</td>
>>>>>>> f3186bd8835a624c4269f3c24bb5bcfa14d776ed
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

<<<<<<< HEAD
        case 'Prescription':
            ?>
            <section id="PatientHistory">
                <form action="" method="GET">
                    <input type="hidden" name="section" value="PatientHistory">
                    <label for="patient_id">Enter Patient ID:</label>
                    <input type="text" id="patient_id" name="patient_id" placeholder="Search Patient ID..." required>
                    <button type="submit">View History</button>
                </form>
=======
        case 'PatientHistory':
            ?>
            <section id="PatientHistory">
            <form role="search" method="GET" class="searchbar">
                    <input type="hidden" name="section" value="ViewPatients">
                    <label for="patient_id">Search Patients</label>
                    <input id="patient_id" name="search_patients" type="search" placeholder="Search..." autofocus required>
                    <button type="submit">Go</button>
                </form>
                <h3>Patient Test Result</h3>
                <table>
                                <thead>
                                    <tr>
                                        <th>Test Result ID</th>
                                        <th>Date</th>
                                        <th>Test Type</th>
                                        <th>Result</th>
                                        <th>Doctor's Note</th>
                                    </tr>
                                </thead>
                                <tbody>
>>>>>>> f3186bd8835a624c4269f3c24bb5bcfa14d776ed
                <?php
                if (isset($_GET['patient_id'])) {
                    $patient_id = mysqli_real_escape_string($conn, $_GET['patient_id']);
                    $query = "SELECT * FROM testresult WHERE PatientID = '$patient_id'";

                    $result = mysqli_query($conn, $query);

                    if ($result && mysqli_num_rows($result) > 0) {
                        echo "<h3>Patient Test Results</h3>";
<<<<<<< HEAD
                        echo "<table>
                                <thead>
                                    <tr>
                                        <th>Test Result ID</th>
                                        <th>Date</th>
                                        <th>Test Type</th>
                                        <th>Result</th>
                                        <th>Doctor's Note</th>
                                    </tr>
                                </thead>
                                <tbody>";
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>
                                    <td>" . htmlspecialchars($row['TestResultID']) . "</td>
                                    <td>" . htmlspecialchars($row['DateUpdated']) . "</td>
                                    <td>" . htmlspecialchars($row['TestType']) . "</td>
                                    <td>" . htmlspecialchars($row['Result']) . "</td>
                                    <td>" . htmlspecialchars($row['DoctorNote']) . "</td>
                                </tr>";
                        }
                        echo "</tbody></table>";
                    } else {
                        echo "<p>No history found for this patient.</p>";
                    }
                }
                ?>
            </section>
            <?php
            break;

        case 'MyProfile':
            ?>
            <section id="MyProfile">
                <h2>My Profile</h2>
                <?php
                $doctor_id = $_SESSION['PatienID']; // Assuming doctor ID is stored in session
                $query = "SELECT * FROM doctor WHERE PatienID = '$doctor_id'";
                $result = mysqli_query($conn, $query);

                if ($result && mysqli_num_rows($result) > 0) {
                    $doctor = mysqli_fetch_assoc($result);
                    echo "<p><strong>Name:</strong> " . htmlspecialchars($patient['Firstname'] . ' ' . $patient['Lastname']) . "</p>";
                    echo "<p><strong>Email:</strong> " . htmlspecialchars($patient['Email']) . "</p>";
                    echo "<p><strong>Phone:</strong> " . htmlspecialchars($patient['Phone']) . "</p>";
                    echo "<p><strong>Specialization:</strong> " . htmlspecialchars($patient['Specialization']) . "</p>";
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
                <h2>Welcome, Patient, to the Online Health Monitor System</h2>
=======

                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>
                                    <td>" . htmlspecialchars($row['TestResultID']) . "</td>
                                    <td>" . htmlspecialchars($row['DateUpdated']) . "</td>
                                    <td>" . htmlspecialchars($row['TestType']) . "</td>
                                    <td>" . htmlspecialchars($row['Result']) . "</td>
                                    <td>" . htmlspecialchars($row['DoctorNote']) . "</td>
                                </tr>";
                        }
                        echo "</tbody></table>";
                    } else {
                        echo "<p>No history found for this patient.</p>";
                    }
                }
                ?>
            </section>
            <?php
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
>>>>>>> f3186bd8835a624c4269f3c24bb5bcfa14d776ed
            </section>
            <?php
    }
    ?>
</main>
</body>
</html>
