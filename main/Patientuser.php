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
    <p><a href="index.html" class="logo">OHMS</a></p>
    <nav class="user">
        <a href="?section=home" class="<?= $active_section === 'home' ? 'active' : '' ?>">Home</a>
       
        <a href="?section=Prescription" class="<?= $active_section === 'Prescription' ? 'active' : '' ?>">Prescription</a>
        <a href="?section=MyProfile" class="<?= $active_section === 'MyProfile' ? 'active' : '' ?>">My Profile</a>
        <a href="logout.php">Logout</a>
        <span></span>
    </nav>
</header>



<main>
    <?php
    switch ($active_section) {
       
            
        case 'Prescription':
            ?>
            <section id="Prescription">
                <h2>Prescriptions</h2>
        
                <?php
                // Get the currently logged-in patient's ID from the session
                $current_patient_id = $_SESSION['PatientID'];
        
                // Query for Assigned Blood Tests for the logged-in patient
                $blood_test_query = "SELECT * FROM assignedbloodtest WHERE PatientID = ?";
                $stmt_blood_test = $conn->prepare($blood_test_query);
                $stmt_blood_test->bind_param("i", $current_patient_id);
                $stmt_blood_test->execute();
                $blood_test_result = $stmt_blood_test->get_result();
        
                // Query for Assigned General Tests for the logged-in patient
                $assigned_test_query = "SELECT * FROM assignedtest WHERE PatientID = ?";
                $stmt_assigned_test = $conn->prepare($assigned_test_query);
                $stmt_assigned_test->bind_param("i", $current_patient_id);
                $stmt_assigned_test->execute();
                $assigned_test_result = $stmt_assigned_test->get_result();
        
                // Display Assigned Blood Tests Table
                echo "<h3>Assigned Blood Tests</h3>";
                if ($blood_test_result && $blood_test_result->num_rows > 0) {
                    echo "<table>
                            <thead>
                                <tr>
                                    <th>AssignedBloodTestID</th>
                                    <th>PatientID</th>
                                    <th>DoctorID</th>
                                    <th>DateAssigned</th>
                                    <th>BloodTestType</th>
                                </tr>
                            </thead>
                            <tbody>";
                    while ($row = $blood_test_result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . htmlspecialchars($row['AssignedBloodTestID']) . "</td>
                                <td>" . htmlspecialchars($row['PatientID']) . "</td>
                                <td>" . htmlspecialchars($row['DoctorID']) . "</td>
                                <td>" . htmlspecialchars($row['DateAssigned']) . "</td>
                                <td>" . htmlspecialchars($row['BloodTestType']) . "</td>
                            </tr>";
                    }
                    echo "</tbody></table>";
                } else {
                    echo "<p>No blood tests assigned yet.</p>";
                }
        
                // Display Assigned General Tests Table
                echo "<h3>Assigned General Tests</h3>";
                if ($assigned_test_result && $assigned_test_result->num_rows > 0) {
                    echo "<table>
                            <thead>
                                <tr>
                                    <th>AssignedTestID</th>
                                    <th>PatientID</th>
                                    <th>DoctorID</th>
                                    <th>DateAssigned</th>
                                    <th>TestType</th>
                                </tr>
                            </thead>
                            <tbody>";
                    while ($row = $assigned_test_result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . htmlspecialchars($row['AssignedTestID']) . "</td>
                                <td>" . htmlspecialchars($row['PatientID']) . "</td>
                                <td>" . htmlspecialchars($row['DoctorID']) . "</td>
                                <td>" . htmlspecialchars($row['DateAssigned']) . "</td>
                                <td>" . htmlspecialchars($row['TestType']) . "</td>
                            </tr>";
                    }
                    echo "</tbody></table>";
                } else {
                    echo "<p>No general tests assigned yet.</p>";
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
                <h2>Welcome, Patient, to the Online Health Monitor Systxem</h2>
            </section>
            <?php
    }
    ?>
</main>
</body>
</html>
