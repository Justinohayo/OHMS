<?php
session_start();
include("php/config.php");

// Ensure the user is logged in
if (!isset($_SESSION['userid'])) {
    echo "<p>You are not logged in. Please log in to update your profile.</p>";
    exit();
}

$current_patient_id = $_SESSION['PatientID']; // This should hold the logged-in patient's ID

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstName = mysqli_real_escape_string($conn, $_POST['firstName']);
    $lastName = mysqli_real_escape_string($conn, $_POST['lastName']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Ensure that the current patient ID matches a valid patient
    $profile_query = "SELECT p.PatientID, c.ContactID FROM Patient p JOIN Contact c ON p.ContactID = c.ContactID WHERE p.PatientID = ?";
    $stmt_profile = $conn->prepare($profile_query);
    $stmt_profile->bind_param("i", $current_patient_id);
    $stmt_profile->execute();
    $profile_result = $stmt_profile->get_result();

    if ($profile_result && $profile_result->num_rows > 0) {
        // Update query for Patient table
        $update_patient_query = "UPDATE Patient SET Firstname = ?, Lastname = ?, DOB = ?, Sex = ? WHERE PatientID = ?";
        $stmt_patient = $conn->prepare($update_patient_query);
        $stmt_patient->bind_param("ssssi", $firstName, $lastName, $dob, $gender, $current_patient_id);

        // Update query for Contact table
        $update_contact_query = "UPDATE Contact SET Phone = ?, Email = ? WHERE ContactID = (SELECT ContactID FROM Patient WHERE PatientID = ?)";
        $stmt_contact = $conn->prepare($update_contact_query);
        $stmt_contact->bind_param("ssi", $phone, $email, $current_patient_id);

        if ($stmt_patient->execute() && $stmt_contact->execute()) {
            echo "<p>Profile updated successfully.</p>";
            header("Location: userpage.php?section=MyProfile");
            exit();
        } else {
            echo "<p>Failed to update profile. Please try again later.</p>";
        }

        $stmt_patient->close();
        $stmt_contact->close();
    } else {
        echo "<p>Invalid patient ID. Please try again later.</p>";
    }

    $stmt_profile->close();
}

$conn->close();
?>
