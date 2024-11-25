<?php
session_start();
include("php/config.php");

// Ensure the user is logged in
if (!isset($_SESSION['PatientID'])) {
    echo "<p>You are not logged in. Please log in to view your prescriptions.</p>";
    exit();
}

$current_patient_id = $_SESSION['PatientID']; // This should hold the logged-in patient's ID
$active_section = isset($_GET['section']) ? $_GET['section'] : 'home';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Userpage.css">
    <title>OHMS - Patient Portal</title>
</head>
<body>
<header>
    <p><a href="index.html" class="logo">OHMS</a></p>
    <nav class="user">
        <a href="?section=home" class="<?= $active_section === 'home' ? 'active' : '' ?>">Home</a>
        <a href="?section=Prescription" class="<?= $active_section === 'Prescription' ? 'active' : '' ?>">Prescription</a>
        <a href="?section=MyProfile" class="<?= $active_section === 'MyProfile' ? 'active' : '' ?>">My Profile</a>
        <a href="logout.php">Logout</a>
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
                // Query for Assigned Blood Tests for the logged-in patient
                $blood_test_query = "SELECT * FROM assignedbloodtest WHERE PatientID = $current_patient_id";
                $stmt_blood_test = $conn->prepare($blood_test_query);
                $stmt_blood_test->bind_param("i", $current_patient_id); // Ensure we are selecting tests for the logged-in patient
                $stmt_blood_test->execute();
                $blood_test_result = $stmt_blood_test->get_result();
        
                // Query for Assigned General Tests for the logged-in patient
                $assigned_test_query = "SELECT * FROM assignedtest WHERE PatientID = $current_patient_id";
                $stmt_assigned_test = $conn->prepare($assigned_test_query);
                $stmt_assigned_test->bind_param("i", $current_patient_id); // Same here for the general tests
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
                // Query for patient profile information
                $profile_query = "SELECT * FROM Patient WHERE PatientID = ?";
                $stmt_profile = $conn->prepare($profile_query);
                $stmt_profile->bind_param("i", $current_patient_id);
                $stmt_profile->execute();
                $profile_result = $stmt_profile->get_result();

                if ($profile_result && $profile_result->num_rows > 0) {
                    $profile_row = $profile_result->fetch_assoc();
                    echo "<p><strong>Name:</strong> " . htmlspecialchars($profile_row['FirstName']) . " " . htmlspecialchars($profile_row['LastName']) . "</p>";
                    echo "<p><strong>Date of Birth:</strong> " . htmlspecialchars($profile_row['DateOfBirth']) . "</p>";
                    echo "<p><strong>Gender:</strong> " . htmlspecialchars($profile_row['Gender']) . "</p>";
                    echo "<p><strong>Contact Info:</strong> " . htmlspecialchars($profile_row['ContactNumber']) . "</p>";
                } else {
                    echo "<p>No profile data available.</p>";
                }
                ?>
            </section>
            <?php
            break;

        default:
            ?>
            <section id="home">
                <h2>Welcome to your Patient Portal</h2>
                <p>Here you can view your prescriptions and profile information.</p>
            </section>
            <?php
            break;
    }
    ?>
</main>
</body>
</html>
