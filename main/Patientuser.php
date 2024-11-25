<?php
session_start();
include("php/config.php");


// Ensure the user is logged in
if (!isset($_SESSION['userid'])) {
    echo "<p>You are not logged in. Please log in to view your portal.</p>";
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
        <a href="?section=home" class="<?= $active_section === 'Home' ? 'active' : '' ?>">Home</a>
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
    // Query for Assigned Blood Tests for the logged-in patient, including patient name
    $blood_test_query = "
        SELECT abt.AssignedBloodTestID, p.Firstname, p.Lastname, abt.DoctorID, abt.DateAssigned, abt.BloodTestType 
        FROM assignedbloodtest abt
        JOIN Patient p ON abt.PatientID = p.PatientID
        WHERE abt.PatientID = ?";
    $stmt_blood_test = $conn->prepare($blood_test_query);
    $stmt_blood_test->bind_param("i", $current_patient_id);
    $stmt_blood_test->execute();
    $blood_test_result = $stmt_blood_test->get_result();

    // Query for Assigned General Tests for the logged-in patient, including patient name
    $assigned_test_query = "
        SELECT at.AssignedTestID, p.Firstname, p.Lastname, at.DoctorID, at.DateAssigned, at.TestType 
        FROM assignedtest at
        JOIN Patient p ON at.PatientID = p.PatientID
        WHERE at.PatientID = ?";
    $stmt_assigned_test = $conn->prepare($assigned_test_query);
    $stmt_assigned_test->bind_param("i", $current_patient_id);
    $stmt_assigned_test->execute();
    $assigned_test_result = $stmt_assigned_test->get_result();
    ?>

    <!-- Display Assigned Blood Tests -->
    <h3>Assigned Blood Tests</h3>
    <?php
    if ($blood_test_result->num_rows > 0) {
        echo "<table>
                <thead>
                    <tr>
                        <th>AssignedBloodTestID</th>
                        <th>Patient Name</th>
                        <th>DoctorID</th>
                        <th>DateAssigned</th>
                        <th>BloodTestType</th>
                    </tr>
                </thead>
                <tbody>";
        while ($row = $blood_test_result->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['AssignedBloodTestID']) . "</td>
                    <td>" . htmlspecialchars($row['Firstname']) . " " . htmlspecialchars($row['Lastname']) . "</td>
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
    if ($assigned_test_result->num_rows > 0) {
        echo "<table>
                <thead>
                    <tr>
                        <th>AssignedTestID</th>
                        <th>Patient Name</th>
                        <th>DoctorID</th>
                        <th>DateAssigned</th>
                        <th>TestType</th>
                    </tr>
                </thead>
                <tbody>";
        while ($row = $assigned_test_result->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['AssignedTestID']) . "</td>
                    <td>" . htmlspecialchars($row['Firstname']) . " " . htmlspecialchars($row['Lastname']) . "</td>
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
                $profile_query = "SELECT p.PatientID, p.Firstname, p.Lastname, p.DOB, p.Sex, c.ContactID, c.Phone, c.Email 
                                  FROM Patient p
                                  JOIN Contact c ON p.ContactID = c.ContactID
                                  WHERE p.PatientID = ?";
                $stmt_profile = $conn->prepare($profile_query);
                $stmt_profile->bind_param("i", $current_patient_id);
                $stmt_profile->execute();
                $profile_result = $stmt_profile->get_result();

                if ($profile_result && $profile_result->num_rows > 0) {
                    $profile_row = $profile_result->fetch_assoc();
                    ?>
                    <p><strong>Name:</strong> <?= htmlspecialchars($profile_row['Firstname']) . " " . htmlspecialchars($profile_row['Lastname']) ?></p>
                    <p><strong>Date of Birth:</strong> <?= htmlspecialchars($profile_row['DOB']) ?></p>
                    <p><strong>Gender:</strong> <?= htmlspecialchars($profile_row['Sex']) ?></p>
                    <p><strong>Phone:</strong> <?= htmlspecialchars($profile_row['Phone']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($profile_row['Email']) ?></p>

                    <button>Modify Details</button>

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
                            <option value="Male" <?= $profile_row['Sex'] == 'Male' ? 'selected' : '' ?>>Male</option>
                            <option value="Female" <?= $profile_row['Sex'] == 'Female' ? 'selected' : '' ?>>Female</option>
                            <option value="Other" <?= $profile_row['Sex'] == 'Other' ? 'selected' : '' ?>>Other</option>
                        </select><br>

                        <label for="phone">Phone:</label>
                        <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($profile_row['Phone']) ?>"><br>

                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($profile_row['Email']) ?>"><br>

                        <input type="submit" value="Save Changes">
                    </form>
                    <?php
                } else {
                    echo "<p>Profile not found.</p>";
                }
                ?>
            </section>
            <?php
            break;

        case 'home':
        default:
            ?>
            <section>
                <h2>Welcome to your Patient Portal</h2>
                <p>Choose a section from the menu to get started.</p>
            </section>
            <?php
            break;
    }
    ?>
</main>
</body>
</html>
