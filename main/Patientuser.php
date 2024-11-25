<?php
session_start();
include("php/config.php");

// Ensure the user is logged in
if (!isset($_SESSION['PatientID'])) {
    echo "<p>You are not logged in. Please log in to view your prescriptions.</p>";
    exit();
}

$current_patient_id = $_SESSION['PatientID'];
$active_section = isset($_GET['section']) ? $_GET['section'] : 'home';

// Handle form submission to update profile
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $contact_number = mysqli_real_escape_string($conn, $_POST['contact_number']);

    // Update the profile in the database
    $update_query = "UPDATE Patient SET FirstName = ?, LastName = ?, DateOfBirth = ?, Gender = ?, ContactNumber = ? WHERE PatientID = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("sssssi", $first_name, $last_name, $dob, $gender, $contact_number, $current_patient_id);

    if ($stmt->execute()) {
        echo "<p>Profile updated successfully!</p>";
    } else {
        echo "<p>Failed to update profile. Please try again.</p>";
    }
}
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
                    ?>
                    <!-- Display current profile details -->
                    <div id="profile-details">
                        <p><strong>Name:</strong> <?= htmlspecialchars($profile_row['FirstName']) ?> <?= htmlspecialchars($profile_row['LastName']) ?></p>
                        <p><strong>Date of Birth:</strong> <?= htmlspecialchars($profile_row['DateOfBirth']) ?></p>
                        <p><strong>Gender:</strong> <?= htmlspecialchars($profile_row['Gender']) ?></p>
                        <p><strong>Contact Info:</strong> <?= htmlspecialchars($profile_row['ContactNumber']) ?></p>

                        <!-- Modify button -->
                        <form method="POST" action="?section=MyProfile#edit-profile" id="modify-profile-form">
                            <button type="submit" name="modify" id="modify-btn">Modify Profile</button>
                        </form>
                    </div>

                    <?php
                    if (isset($_POST['modify'])) {
                        // Show the editable profile table
                        ?>
                        <h3>Edit Profile</h3>
                        <form method="POST" action="">
                            <table>
                                <tr>
                                    <td><label for="first_name">First Name:</label></td>
                                    <td><input type="text" name="first_name" value="<?= htmlspecialchars($profile_row['FirstName']) ?>" required></td>
                                </tr>
                                <tr>
                                    <td><label for="last_name">Last Name:</label></td>
                                    <td><input type="text" name="last_name" value="<?= htmlspecialchars($profile_row['LastName']) ?>" required></td>
                                </tr>
                                <tr>
                                    <td><label for="dob">Date of Birth:</label></td>
                                    <td><input type="date" name="dob" value="<?= htmlspecialchars($profile_row['DateOfBirth']) ?>" required></td>
                                </tr>
                                <tr>
                                    <td><label for="gender">Gender:</label></td>
                                    <td>
                                        <select name="gender" required>
                                            <option value="Male" <?= ($profile_row['Gender'] === 'Male') ? 'selected' : '' ?>>Male</option>
                                            <option value="Female" <?= ($profile_row['Gender'] === 'Female') ? 'selected' : '' ?>>Female</option>
                                            <option value="Other" <?= ($profile_row['Gender'] === 'Other') ? 'selected' : '' ?>>Other</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label for="contact_number">Contact Number:</label></td>
                                    <td><input type="text" name="contact_number" value="<?= htmlspecialchars($profile_row['ContactNumber']) ?>" required></td>
                                </tr>
                            </table>
                            <button type="submit" name="update_profile">Save Changes</button>
                        </form>
                        <?php
                    }
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
