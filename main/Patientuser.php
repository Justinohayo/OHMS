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
        <a href="?section=ViewDoctors" class="<?= $active_section === 'ViewDoctors' ? 'active' : '' ?>">My Doctors</a>
        <a href="?section=Prescription" class="<?= $active_section === 'Prescription' ? 'active' : '' ?>">Prescription</a>
        <a href="?section=MyProfile" class="<?= $active_section === 'MyProfile' ? 'active' : '' ?>">My Profile</a>
        <a href="logout.php">Logout</a>
        <span></span>
    </nav>
</header>



<main>
    <?php
    switch ($active_section) {
        case 'ViewDoctors':
            ?>
            <section id="ViewDoctors">
                <form role="search" method="GET" class="searchbar">
                    <input type="hidden" name="section" value="ViewDoctors">
                    <label for="search_doctors">Search Doctors</label>
                    <input id="search_doctors" name="search_doctors" type="search" placeholder="Search..." autofocus required>
                    <button type="submit">Go</button>
                </form>
                <table>
                    <thead>
                        <tr>
                            <th>Doctor ID</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th> Date of Birth </th>
                            
                           
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (isset($_GET['search_doctors'])) {
                            $search = mysqli_real_escape_string($conn, $_GET['search_doctors']);
                            $query = "SELECT * FROM doctor WHERE CONCAT(DoctorID, FirstName, LastName) LIKE '%$search%'";
                        } else {
                            $query = "SELECT * FROM doctor";
                        }
                        $result = mysqli_query($conn, $query);

                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>
                                    <td>" . htmlspecialchars($row['DoctorID']) . "</td>
                                    <td>" . htmlspecialchars($row['FirstName']) . "</td>
                                    <td>" . htmlspecialchars($row['LastName']) . "</td>
                                    <td>" . htmlspecialchars($row['DOB']) . "</td>
                                   
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>No Records Found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </section>
            <?php
            break;

            
case 'Prescription':
    ?>
    <section id="Prescription">
        <h2>Prescriptions</h2>

        <?php
        // Query for Assigned Blood Tests
        $blood_test_query = "SELECT * FROM assignedbloodtest";
        $blood_test_result = mysqli_query($conn, $blood_test_query);

        // Query for Assigned General Tests
        $assigned_test_query = "SELECT * FROM assignedtest";
        $assigned_test_result = mysqli_query($conn, $assigned_test_query);

        // Display Assigned Blood Tests Table
        echo "<h3>Assigned Blood Tests</h3>";
        if ($blood_test_result && mysqli_num_rows($blood_test_result) > 0) {
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

        // Display Assigned General Tests Table
        echo "<h3>Assigned General Tests</h3>";
        if ($assigned_test_result && mysqli_num_rows($assigned_test_result) > 0) {
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
                <h2>Welcome, Patient, to the Online Health Monitor System</h2>
            </section>
            <?php
    }
    ?>
</main>
</body>
</html>
