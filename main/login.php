<?php
session_start();
include("php/config.php");

if (isset($_POST['submit'])) {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = mysqli_real_escape_string($conn, trim($_POST['password']));

    // Query to get the user based on username
    $sql = "SELECT * FROM useraccount WHERE Username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $row['Password'])) {
            $_SESSION['userid'] = $row['UserAccountID']; // Store user ID in session

            // Add the following blocks to store IDs for specific roles
            if ($row['UserType'] === 'Patient') {
                $patient_query = "SELECT PatientID FROM patient WHERE UserAccountID = ?";
                $stmt = $conn->prepare($patient_query);
                $stmt->bind_param("i", $row['UserAccountID']);
                $stmt->execute();
                $patient_result = $stmt->get_result();
                
                if ($patient_result && $patient_result->num_rows > 0) {
                    $patient_row = $patient_result->fetch_assoc();
                    $_SESSION['PatientID'] = $patient_row['PatientID']; // Store PatientID in session
                } else {
                    echo "<div class='message'><p>Unable to retrieve PatientID. Please contact support.</p></div>";
                    exit();
                }
            } elseif ($row['UserType'] === 'Doctor') {
                $doctor_query = "SELECT DoctorID FROM doctor WHERE UserAccountID = ?";
                $stmt = $conn->prepare($doctor_query);
                $stmt->bind_param("i", $row['UserAccountID']);
                $stmt->execute();
                $doctor_result = $stmt->get_result();

                if ($doctor_result && $doctor_result->num_rows > 0) {
                    $doctor_row = $doctor_result->fetch_assoc();
                    $_SESSION['DoctorID'] = $doctor_row['DoctorID']; // Store DoctorID in session
                } else {
                    echo "<div class='message'><p>Unable to retrieve DoctorID. Please contact support.</p></div>";
                    exit();
                }
            } elseif ($row['UserType'] === 'Staff') {
                $staff_query = "SELECT StaffID FROM staff WHERE UserAccountID = ?";
                $stmt = $conn->prepare($staff_query);
                $stmt->bind_param("i", $row['UserAccountID']);
                $stmt->execute();
                $staff_result = $stmt->get_result();

                if ($staff_result && $staff_result->num_rows > 0) {
                    $staff_row = $staff_result->fetch_assoc();
                    $_SESSION['StaffID'] = $staff_row['StaffID']; // Store StaffID in session
                } else {
                    echo "<div class='message'><p>Unable to retrieve StaffID. Please contact support.</p></div>";
                    exit();
                }
            }

            // Redirect based on UserType
            switch ($row['UserType']) {
                case 'Doctor':
                    header("Location: doctoruser.php");
                    exit();
                case 'Staff':
                    header("Location: staffuser.php");
                    exit();
                case 'Patient':
                    header("Location: patientuser.php");
                    exit();
                case 'Admin':
                    header("Location: admin.php");
                    exit();
                default:
                    echo "<div class='message'><p>Unknown UserType. Please contact support.</p></div>";
                    exit();
            }
        } else {
            echo "<div class='message'><p>Incorrect Username or Password</p></div>";
        }
    } else {
        echo "<div class='message'><p>User not found. Please check your credentials.</p></div>";
    }
}
?>
