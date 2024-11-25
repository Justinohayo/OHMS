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

include("php/config.php");

if (!isset($_SESSION['PatientID'])) {
    echo "<p>You are not logged in. Please log in to view your prescriptions.</p>";
    exit();
}

$current_patient_id = $_SESSION['PatientID']; // Assuming PatientID is stored in session

// Query for Assigned Blood Tests for the logged-in patient using mysqli_query
$blood_test_query = "SELECT * FROM assignedbloodtest WHERE PatientID = " . (int)$current_patient_id; // Casting to integer for safety
$blood_test_result = mysqli_query($conn, $blood_test_query);

if (!$blood_test_result) {
    die("Query failed: " . mysqli_error($conn));
}

// Query for Assigned General Tests for the logged-in patient using mysqli_query
$assigned_test_query = "SELECT * FROM assignedtest WHERE PatientID = " . (int)$current_patient_id; // Casting to integer for safety
$assigned_test_result = mysqli_query($conn, $assigned_test_query);

if (!$assigned_test_result) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<!-- Display Assigned Blood Tests -->
<h3>Assigned Blood Tests</h3>
<?php
if (mysqli_num_rows($blood_test_result) > 0) {
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
    while ($row = mysqli_fetch_assoc($blood_test_result)) {
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
?>

<!-- Display Assigned General Tests -->
<h3>Assigned General Tests</h3>
<?php
if (mysqli_num_rows($assigned_test_result) > 0) {
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
    while ($row = mysqli_fetch_assoc($assigned_test_result)) {
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


include("php/config.php");

if (!isset($_SESSION['PatientID'])) {
    echo "<p>You are not logged in. Please log in to view your profile.</p>";
    exit();
}

$current_patient_id = $_SESSION['PatientID']; // Assuming PatientID is stored in session
?>

<!-- Profile Section -->
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
        ?>
        <!-- Display Profile Information -->
        <p><strong>Name:</strong> <span id="profileName"><?= htmlspecialchars($profile_row['Firstname']) . " " . htmlspecialchars($profile_row['Lastname']) ?></span></p>
        <p><strong>Date of Birth:</strong> <span id="profileDOB"><?= htmlspecialchars($profile_row['DOB']) ?></span></p>
        <p><strong>Gender:</strong> <span id="profileGender"><?= htmlspecialchars($profile_row['Sex']) ?></span></p>
        <p><strong>Contact Info:</strong> <span id="profileContact"><?= htmlspecialchars($profile_row['ContactID']) ?></span></p>
        
        <!-- Modify Profile Button -->
        <button id="modifyProfileBtn">Modify Details</button>
        
        <!-- Modify Profile Form (Initially hidden) -->
        <form id="modifyProfileForm" style="display:none;" method="POST" action="update_profile.php">
            <label for="firstName">First Name:</label>
            <input type="text" id="firstName" name="firstName" value="<?= htmlspecialchars($profile_row['Firstname']) ?>"><br>
            
            <label for="lastName">Last Name:</label>
            <input type="text" id="lastName" name="lastName" value="<?= htmlspecialchars($profile_row['Lastname']) ?>"><br>
            
            <label for="dob">Date of Birth:</label>
            <input type="date" id="dob" name="dob" value="<?= htmlspecialchars($profile_row['DOB']) ?>"><br>
            
            <label for="gender">Gender:</label>
            <select id="gender" name="gender">
                <option value="Male" <?= $profile_row['Gender'] == 'Male' ? 'selected' : '' ?>>Male</option>
                <option value="Female" <?= $profile_row['Gender'] == 'Female' ? 'selected' : '' ?>>Female</option>
                <option value="Other" <?= $profile_row['Gender'] == 'Other' ? 'selected' : '' ?>>Other</option>
            </select><br>
            
            <label for="contactNumber">Contact Info:</label>
            <input type="text" id="contactNumber" name="contactNumber" value="<?= htmlspecialchars($profile_row['ContactID']) ?>"><br>
            
            <button type="submit">Save Changes</button>
            <button type="button" id="cancelModifyBtn">Cancel</button>
        </form>

        <script>
            // Handle Modify Profile Button click
            document.getElementById('modifyProfileBtn').addEventListener('click', function() {
                // Hide profile info and show modify form
                document.querySelectorAll('#profileName, #profileDOB, #profileGender, #profileContact').forEach(function(element) {
                    element.style.display = 'none';
                });
                document.getElementById('modifyProfileForm').style.display = 'block';
                document.getElementById('modifyProfileBtn').style.display = 'none';
            });

            // Handle Cancel Modify Button click
            document.getElementById('cancelModifyBtn').addEventListener('click', function() {
                // Hide modify form and show profile info
                document.querySelectorAll('#profileName, #profileDOB, #profileGender, #profileContact').forEach(function(element) {
                    element.style.display = 'inline';
                });
                document.getElementById('modifyProfileForm').style.display = 'none';
                document.getElementById('modifyProfileBtn').style.display = 'inline';
            });
        </script>
    <?php
    } else {
        echo "<p>No profile data available.</p>";
    }
    ?>
</section>

            <?php
            break;
    }
    ?>
</main>
</body>
</html>
