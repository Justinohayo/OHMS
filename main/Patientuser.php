<?php
session_start();
include("php/config.php");

// Ensure the user is logged in
if (!isset($_SESSION['userid'])) {
    echo "<p>You are not logged in. Please log in to view your portal.</p>";
    exit();
}

// Store the current UserAccountID in a variable
$useraccountID = $_SESSION['userid'];

// Query to retrieve the PatientID using the UserAccountID
$patient_query = "SELECT PatientID FROM Patient WHERE UserAccountID = ?";
$stmt = $conn->prepare($patient_query);
$stmt->bind_param("s", $useraccountID);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $current_patient_id = $row['PatientID'];
} else {
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
    <script>
        // Toggle between read-only and editable form
        function toggleEditMode() {
            const readOnlyForm = document.getElementById('readOnlyProfileForm');
            const editableForm = document.getElementById('editableProfileForm');
            readOnlyForm.style.display = 'none';
            editableForm.style.display = 'block';
        }
    </script>
</head>
<body>
<header>
    <p><a href="index.html" class="logo">OHMS</a></p>
    <nav class="user">
        <a href="?section=home" class="<?= $active_section === 'home' ? 'active' : '' ?>">Home</a>
        <a href="?section=Prescription" class="<?= $active_section === 'Prescription' ? 'active' : '' ?>">Prescription</a>
        <a href="?section=MyProfile" class="<?= $active_section === 'MyProfile' ? 'active' : '' ?>">My Profile</a>
        <a href="?section=Appointment" class="<?= $active_section === 'Appointment' ? 'active' : '' ?>">Appointment</a>
        <a href="index.html">Logout</a>
        
    </nav>
    <span> </span>
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
                $profile_query = "
                    SELECT p.Firstname, p.Lastname, p.DOB, p.Sex, c.Phone, c.Email, c.ContactID
                    FROM Patient p
                    JOIN Contact c ON p.ContactID = c.ContactID
                    WHERE p.PatientID = ?";
                $stmt = $conn->prepare($profile_query);
                $stmt->bind_param("s", $current_patient_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $profile = $result->fetch_assoc();

                if ($profile):
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'updateProfile') {
                        $firstname = htmlspecialchars($_POST['Firstname']);
                        $lastname = htmlspecialchars($_POST['Lastname']);
                        $dob = htmlspecialchars($_POST['DOB']);
                        $sex = htmlspecialchars($_POST['Sex']);
                        $phone = htmlspecialchars($_POST['Phone']);
                        $email = htmlspecialchars($_POST['Email']);
                        $contactID = $profile['ContactID'];

                        $update_patient = "UPDATE Patient SET Firstname = ?, Lastname = ?, DOB = ?, Sex = ? WHERE PatientID = ?";
                        $stmt_patient = $conn->prepare($update_patient);
                        $stmt_patient->bind_param('sssss', $firstname, $lastname, $dob, $sex, $current_patient_id);

                        $update_contact = "UPDATE Contact SET Phone = ?, Email = ? WHERE ContactID = ?";
                        $stmt_contact = $conn->prepare($update_contact);
                        $stmt_contact->bind_param('sss', $phone, $email, $contactID);

                        if ($stmt_patient->execute() && $stmt_contact->execute()) {
                            echo "<p style='color:green;'>Profile updated successfully!</p>";
                        } else {
                            echo "<p style='color:red;'>Error updating profile. Please try again.</p>";
                        }
                    }
                    ?>
                    <form id="readOnlyProfileForm" style="display: block;">
                        <label>Firstname:</label>
                        <input type="text" value="<?= htmlspecialchars($profile['Firstname']) ?>" readonly>
                        <label>Lastname:</label>
                        <input type="text" value="<?= htmlspecialchars($profile['Lastname']) ?>" readonly>
                        <label>Date of Birth:</label>
                        <input type="date" value="<?= htmlspecialchars($profile['DOB']) ?>" readonly>
                        <label>Sex:</label>
                        <input type="text" value="<?= htmlspecialchars($profile['Sex']) ?>" readonly>
                        <label>Phone:</label>
                        <input type="text" value="<?= htmlspecialchars($profile['Phone']) ?>" readonly>
                        <label>Email:</label>
                        <input type="email" value="<?= htmlspecialchars($profile['Email']) ?>" readonly>
                        <button type="button" onclick="toggleEditMode()">Update Profile</button>
                    </form>

                    <form id="editableProfileForm" method="POST" action="?section=MyProfile" style="display: none;">
                        <input type="hidden" name="action" value="updateProfile">
                        <label>Firstname:</label>
                        <input type="text" name="Firstname" value="<?= htmlspecialchars($profile['Firstname']) ?>" required>
                        <label>Lastname:</label>
                        <input type="text" name="Lastname" value="<?= htmlspecialchars($profile['Lastname']) ?>" required>
                        <label>Date of Birth:</label>
                        <input type="date" name="DOB" value="<?= htmlspecialchars($profile['DOB']) ?>" required>
                        <label>Sex:</label>
                        <select name="Sex" required>
                            <option value="Male" <?= $profile['Sex'] === 'Male' ? 'selected' : '' ?>>Male</option>
                            <option value="Female" <?= $profile['Sex'] === 'Female' ? 'selected' : '' ?>>Female</option>
                            <option value="Other" <?= $profile['Sex'] === 'Other' ? 'selected' : '' ?>>Other</option>
                        </select>
                        <label>Phone:</label>
                        <input type="text" name="Phone" value="<?= htmlspecialchars($profile['Phone']) ?>" required>
                        <label>Email:</label>
                        <input type="email" name="Email" value="<?= htmlspecialchars($profile['Email']) ?>" required>
                        <button type="submit">Save Changes</button>
                    </form>
                <?php else: ?>
                    <p>Profile not found. Please contact support.</p>
                <?php endif; ?>
            </section>
            <?php
            break;

            case 'Appointment':
                ?>
                <section id="Appointment" class="page-section active">
                    <h2 class="appointment-title">Appointments</h2>
                    <p class="appointment-description">Welcome to the Appointment Section. Here, you can view and manage your appointments.</p>
            
                    <h3 class="appointment-title">Upcoming Appointments</h3>
                    <div class="tablecontainer">
                        <?php
                        if (!isset($_SESSION['appointments'])) {
                            $_SESSION['appointments'] = [
                                [
                                    'date' => '2024-12-01',
                                    'time' => '10:00 AM',
                                    'doctor' => 'Dr. John Doe',
                                ],
                                [
                                    'date' => '2024-12-15',
                                    'time' => '2:30 PM',
                                    'doctor' => 'Dr. Jane Smith',
                                ],
                            ];
                        }
            
                        if (!empty($_SESSION['appointments'])) {
                            echo "<table>
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Doctor</th>
                                        </tr>
                                    </thead>
                                    <tbody>";
                            foreach ($_SESSION['appointments'] as $appointment) {
                                echo "<tr>
                                        <td>{$appointment['date']}</td>
                                        <td>{$appointment['time']}</td>
                                        <td>{$appointment['doctor']}</td>
                                      </tr>";
                            }
                            echo "</tbody>
                                </table>";
                        } else {
                            echo "<p class='no-appointments'>No upcoming appointments.</p>";
                        }
                        ?>
                    </div>  
            
                    <h3 class="appointment-title">Book a New Appointment</h3>
                    <form method="POST" action="?section=Appointment" class="appointment-form">
                        <label for="appointmentDate">Date:</label>
                        <input type="date" id="appointmentDate" name="appointmentDate" class="patientbar" required>
                        
                        <label for="appointmentTime">Time:</label>
                        <input type="time" id="appointmentTime" name="appointmentTime" class="patientbar" required>
                        
                        <label for="doctorName">Doctor:</label>
                        <select id="doctorName" name="doctorName" class="patientbar" required>
                            <option value="Dr. John Doe">Dr. John Doe</option>
                            <option value="Dr. Jane Smith">Dr. Jane Smith</option>
                            <option value="Dr. Alan Brown">Dr. Alan Brown</option>
                        </select>
                        
                        <button type="submit">Book Appointment</button>
                    </form>
            
                    <?php
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitAppointment'])) {
                        $date = htmlspecialchars($_POST['appointmentDate']);
                        $time = htmlspecialchars($_POST['appointmentTime']);
                        $doctor = htmlspecialchars($_POST['doctorName']);
            
                        $_SESSION['appointments'][] = [
                            'date' => $date,
                            'time' => $time,
                            'doctor' => $doctor,
                        ];
            
                        echo "<p class='success-message'>Appointment booked successfully!</p>";
                        echo "<script>location.href='?section=Appointment';</script>";
                    }
                    ?>
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