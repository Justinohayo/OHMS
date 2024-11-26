<?php
session_start();
include("php/config.php");

// Ensure the user is logged in
if (!isset($_SESSION['userid'])) 
{
    echo "<p>You are not logged in. Please log in to view your portal.</p>";
    exit();
}

// Store the current UserAccountID in a variable
$useraccountID = $_SESSION['userid']; // User ID from the session

// Query to retrieve the PatientID using the UserAccountID
$patient_query = "SELECT PatientID FROM Patient WHERE UserAccountID = ?";
$stmt = $conn->prepare($patient_query);
$stmt->bind_param("s", $useraccountID);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $current_patient_id = $row['PatientID']; // Store the PatientID
} else 
{
    echo "<p>Unable to retrieve PatientID. Please contact support.</p>";
    exit();
}

// Determine the active section of the patient portal
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
                $search = isset($_GET['search_results']) ? mysqli_real_escape_string($conn, $_GET['search_results']) : '';

                // Queries for prescriptions
                $blood_test_query = "
                    SELECT abt.AssignedBloodTestID, abt.DoctorID, abt.DateAssigned, abt.BloodTestType 
                    FROM AssignedBloodTest abt
                    WHERE abt.PatientID = '$current_patient_id' AND
                    ('$search' = '' OR abt.BloodTestType LIKE '%$search%' OR abt.DateAssigned LIKE '%$search%')";

                $assigned_test_query = "
                    SELECT at.AssignedTestID, at.DoctorID, at.DateAssigned, at.TestType 
                    FROM AssignedTest at
                    WHERE at.PatientID = '$current_patient_id' AND
                    ('$search' = '' OR at.TestType LIKE '%$search%' OR at.DateAssigned LIKE '%$search%')";

                $blood_test_result = mysqli_query($conn, $blood_test_query);
                $assigned_test_result = mysqli_query($conn, $assigned_test_query);
                ?>

                <h3>Assigned Blood Tests</h3>
                <?php if ($blood_test_result && mysqli_num_rows($blood_test_result) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Doctor</th>
                                <th>Date</th>
                                <th>Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($blood_test_result)): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['AssignedBloodTestID']) ?></td>
                                    <td><?= htmlspecialchars($row['DoctorID']) ?></td>
                                    <td><?= htmlspecialchars($row['DateAssigned']) ?></td>
                                    <td><?= htmlspecialchars($row['BloodTestType']) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No blood tests assigned yet.</p>
                <?php endif; ?>

                <h3>Assigned General Tests</h3>
                <?php if ($assigned_test_result && mysqli_num_rows($assigned_test_result) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Doctor</th>
                                <th>Date</th>
                                <th>Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($assigned_test_result)): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['AssignedTestID']) ?></td>
                                    <td><?= htmlspecialchars($row['DoctorID']) ?></td>
                                    <td><?= htmlspecialchars($row['DateAssigned']) ?></td>
                                    <td><?= htmlspecialchars($row['TestType']) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No general tests assigned yet.</p>
                <?php endif; ?>
            </section>
            <?php
            break;

            case 'MyProfile':
                ?>
                <section id="MyProfile">
                    <h2>My Profile</h2>
            
                    <?php
                    // Query to fetch profile data
                    $profile_query = "
                        SELECT p.Firstname, p.Lastname, p.DOB, p.Sex, c.Phone, c.Email
                        FROM Patient p
                        JOIN Contact c ON p.ContactID = c.ContactID
                        WHERE p.PatientID = ?";
                    $stmt = $conn->prepare($profile_query);
                    $stmt->bind_param("s", $current_patient_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
            
                    if ($result && $result->num_rows > 0):
                        $profile = $result->fetch_assoc();
                        ?>
                        <!-- Read-Only Profile Form -->
                        <form id="profileForm" method="POST" style="display: block;">
                            <label>First Name:</label>
                            <input type="text" value="<?= htmlspecialchars($profile['Firstname']) ?>" readonly>
                            <label>Last Name:</label>
                            <input type="text" value="<?= htmlspecialchars($profile['Lastname']) ?>" readonly>
                            <label>Date of Birth:</label>
                            <input type="date" value="<?= htmlspecialchars($profile['DOB']) ?>" readonly>
                            <label>Gender:</label>
                            <input type="text" value="<?= htmlspecialchars($profile['Sex']) ?>" readonly>
                            <label>Phone:</label>
                            <input type="text" value="<?= htmlspecialchars($profile['Phone']) ?>" readonly>
                            <label>Email:</label>
                            <input type="email" value="<?= htmlspecialchars($profile['Email']) ?>" readonly>
                            <button type="button" id="modifyButton">Modify Details</button>
                        </form>
            
                        <!-- Editable Profile Form -->
                        <form id="modifyProfileForm" method="POST" action="update_profile.php" style="display: none;">
    <label>First Name:</label>
    <input type="text" name="firstName" value="<?= htmlspecialchars($profile['Firstname']) ?>" required>
    <label>Last Name:</label>
    <input type="text" name="lastName" value="<?= htmlspecialchars($profile['Lastname']) ?>" required>
    <label>Date of Birth:</label>
    <input type="date" name="dob" value="<?= htmlspecialchars($profile['DOB']) ?>" required>
    <label>Gender:</label>
    <select name="gender" required>
        <option value="Male" <?= $profile['Sex'] === 'Male' ? 'selected' : '' ?>>Male</option>
        <option value="Female" <?= $profile['Sex'] === 'Female' ? 'selected' : '' ?>>Female</option>
        <option value="Other" <?= $profile['Sex'] === 'Other' ? 'selected' : '' ?>>Other</option>
    </select>
    <label>Phone:</label>
    <input type="text" name="phone" value="<?= htmlspecialchars($profile['Phone']) ?>" required>
    <label>Email:</label>
    <input type="email" name="email" value="<?= htmlspecialchars($profile['Email']) ?>" required>
    <button type="submit">Save Changes</button>
</form>



            
                        <script>
                            // JavaScript to toggle between read-only and editable forms
                            document.getElementById("modifyButton").addEventListener("click", function () {
                                document.getElementById("profileForm").style.display = "none";
                                document.getElementById("modifyProfileForm").style.display = "block";
                            });
                        </script>
                    <?php else: ?>
                        <p>Profile not found.</p>
                    <?php endif; ?>
                </section>
                <?php
                break;
            

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