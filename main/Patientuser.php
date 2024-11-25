<?php
session_start();
include("php/config.php");

// Determine the active section
$active_section = isset($_GET['section']) ? htmlspecialchars($_GET['section']) : 'home';
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

<main>
    <?php
    switch ($active_section) {
        case 'ViewDoctors':
            ?>
            <section id="ViewDoctors">
                <form role="search" method="GET" class="searchbar">
                    <input type="hidden" name="section" value="ViewDoctors">
                    <label for="search_doctors">Search Doctors</label>
                    <input id="search_doctors" name="search_doctors" type="search" placeholder="Search..." autofocus required>
                    <button type="submit">Go</button>
                </form>
                <table>
                    <thead>
                        <tr>
                            <th>Doctor ID</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>DOB</th>
                            <th>Gender</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT * FROM doctor";
                        if (isset($_GET['search_doctors'])) {
                            $search = mysqli_real_escape_string($conn, $_GET['search_doctors']);
                            $query = "SELECT * FROM doctor WHERE CONCAT(DoctorID, FirstName, LastName) LIKE '%$search%'";
                        }
                        $result = mysqli_query($conn, $query);

                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>
                                    <td>" . htmlspecialchars($row['DoctorID']) . "</td>
                                    <td>" . htmlspecialchars($row['Firstname']) . "</td>
                                    <td>" . htmlspecialchars($row['Lastname']) . "</td>
                                    <td>" . htmlspecialchars($row['DOB']) . "</td>
                                    <td>" . htmlspecialchars($row['Sex']) . "</td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>No Records Found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </section>
            <?php
            break;

        case 'Prescription':
           

            $userAccountID = $_SESSION['userid'];

            // Get the PatientID
            $query_patient_id = "SELECT PatientID FROM patient WHERE UserAccountID =  ?";
            $result_patient_id = mysqli_query($conn, $query_patient_id);

            if ($result_patient_id) 
            {
                if(mysqli_num_rows($result_patient_id) > 0)
                {
                $patient_data = mysqli_fetch_assoc($result_patient_id);
                $patient_id = $patient_data['patientID'];
                }
                else
                echo"<p>You dont have any prescription";
            
            } else {
                echo "<p>Unable to retrieve your Patient ID. Please contact support.</p>";
                exit();
            }

            // Unified query
            $unified_query = "
                SELECT AssignedBloodTestID AS TestID, PatientID, DoctorID, DateAssigned, BloodTestType AS TestType
                FROM assignedbloodtest
                WHERE PatientID = '$patient_id'
                UNION
                SELECT AssignedTestID AS TestID, PatientID, DoctorID, DateAssigned, TestType
                FROM assignedtest
                WHERE PatientID = '$patient_id'";

            if (isset($_GET['unified_test_search']) && !empty($_GET['unified_test_search'])) {
                $test_id = mysqli_real_escape_string($conn, $_GET['unified_test_search']);
                $unified_query .= " AND (AssignedBloodTestID = '$test_id' OR AssignedTestID = '$test_id')";
            }

            $unified_result = mysqli_query($conn, $unified_query);
            ?>
            <section id="Prescription">
                <h2>Prescriptions</h2>
                <form action="" method="GET">
                    <input type="hidden" name="section" value="Prescription">
                    <label for="unified_test_search">Search Test ID:</label>
                    <input type="text" id="unified_test_search" name="unified_test_search" placeholder="Enter Test ID">
                    <button type="submit">Search</button>
                </form>
                <?php
                if ($unified_result && mysqli_num_rows($unified_result) > 0) {
                    echo "<table><thead><tr><th>TestID</th><th>PatientID</th><th>DoctorID</th><th>DateAssigned</th><th>TestType</th></tr></thead><tbody>";
                    while ($row = mysqli_fetch_assoc($unified_result)) {
                        $formatted_date = date('Y-m-d', strtotime($row['DateAssigned']));
                        echo "<tr>
                            <td>" . htmlspecialchars($row['TestID']) . "</td>
                            <td>" . htmlspecialchars($row['PatientID']) . "</td>
                            <td>" . htmlspecialchars($row['DoctorID']) . "</td>
                            <td>" . $formatted_date . "</td>
                            <td>" . htmlspecialchars($row['TestType']) . "</td>
                        </tr>";
                    }
                    echo "</tbody></table>";
                } else {
                    echo "<p>No prescriptions found for your Patient ID.</p>";
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
                $doctor_id = $_SESSION['userAccountID']; // Assuming userAccountID represents PatientID
                $query = "SELECT * FROM doctor WHERE PatientID = '$doctor_id'";
                $result = mysqli_query($conn, $query);

                if ($result && mysqli_num_rows($result) > 0) {
                    $patient = mysqli_fetch_assoc($result);
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
            </section>
            <?php
    }
    ?>
</main>
</body>
</html>
