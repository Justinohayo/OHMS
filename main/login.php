<?php
session_start();
include("php/config.php");

if (isset($_POST['submit'])) {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = mysqli_real_escape_string($conn, trim($_POST['password']));

    // Query to get the user based on username
    $sql = "SELECT * FROM useraccount WHERE Username = '$username'";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        die("Query failed: " . mysqli_error($conn)); // Debugging
    }

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        // Verify the password
        if (password_verify($password, $row['Password'])) {
            $_SESSION['userid'] = $row['UserAccountID']; // Store user ID in session

            // Add the following block to store PatientID for patients
            if ($row['UserType'] === 'Patient') {
                $patient_query = "SELECT PatientID FROM patient WHERE UserAccountID = ?";
                $stmt = $conn->prepare($patient_query);
                $stmt->bind_param("i", $row['UserAccountID']);
                $stmt->execute();
                $patient_result = $stmt->get_result();
                
                if ($patient_result && $patient_result->num_rows > 0) {
                    $patient_row = $patient_result->fetch_assoc();
                    $_SESSION['PatientID'] = $patient_row['PatientID']; // Store PatientID in session
                    echo "PatientID set in session: " . $_SESSION['PatientID'] . "<br>"; 
                }else {
                    echo "<div class='message'><p>Unable to retrieve PatientID. Please contact support.</p></div>";
                    exit();
                }
            }
                if ($row['UserType'] === 'Doctor') {
                    $doctor_query = "SELECT DoctorID FROM doctor WHERE UserAccountID = ?";
                    $stmt = $conn->prepare($doctor_query);
                    $stmt->bind_param("s", $row['UserAccountID']);
                    $stmt->execute();
                    $doctor_result = $stmt->get_result();
                    
                    if ($doctor_result && $doctor_result->num_rows > 0) {
                        $doctor_row = $doctor_result->fetch_assoc();
                        $_SESSION['DoctorID'] = $doctor_row['DoctorID']; // Store DoctorID in session
                        echo "DoctorID set in session: " . $_SESSION['DoctorID'] . "<br>"; 

                    } else {
                        echo "<div class='message'><p>Unable to retrieve DoctorID. Please contact support.</p></div>";
                        exit();
                    }
                }
                if ($row['UserType'] === 'Staff') {
                    $staff_query = "SELECT StaffID FROM staff WHERE UserAccountID = ?";
                    $stmt = $conn->prepare($staff_query);
                    $stmt->bind_param("s", $row['UserAccountID']);
                    $stmt->execute();
                    $staff_result = $stmt->get_result();
                    
                    if ($staff_result && $staff_result->num_rows > 0) {
                        $staff_row = $staff_result->fetch_assoc();
                        $_SESSION['StaffID'] = $staff_row['StaffID']; // Store DoctorID in session
                        echo "PatientID set in session: " . $_SESSION['PatientID'] . "<br>"; 
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
            }
        } else {
            echo "<div class='message'><p>Incorrect Username or Password</p></div>";
        }
    } else {
        echo "<div class='message'><p>User not found. Please check your credentials.</p></div>";
    }
}
?>

<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style2.css"> 
    <title>OHMS</title>
</head>
<body>
    <div class="containerlogin">
        <div class="box2 form-box2">
            <header>Login</header>
            <form action="" method="post">
                <div class="field input">
                    <label for="username">Username</label>
                    <input type="text" placeholder="Enter Username" name="username" id="username" autocomplete="off" required>
                </div>
                <div class="field input">
                    <label>Password</label>
                    <input type="password" placeholder="Enter Password" name="password" id="password" autocomplete="off" required>
                </div>
                <div class="field">
                    <input type="submit" class="btn2" name="submit" value="Login" required>
                </div>
                <div class="links2">
                    Don't have an account? <a href="register.php">Sign Up Now</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
