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
        <a href="?section=ViewPatients" class="<?= $active_section === 'ViewPatients' ? 'active' : '' ?>">View Patients</a>
        <a href="?section=PatientHistory" class="<?= $active_section === 'PatientHistory' ? 'active' : '' ?>">Patient History</a>
        <a href="?section=DoctorProfile" class="<?= $active_section === 'DoctorProfile' ? 'active' : '' ?>">Profile</a>
        <a href="index.html">Logout</a>
        <span></span>
    </nav>
</header>

<main>
    <?php
    switch ($active_section) {
        case 'ViewPatients':
            ?>
            <section id="ViewPatients">
                <form role="search" method="GET" class="searchbar">
                    <input type="hidden" name="section" value="ViewPatients">
                    <label for="search_patients">Search Patients</label>
                    <input id="search_patients" name="search_patients" type="search" placeholder="Search..." autofocus required>
                    <button type="submit">Go</button>
                </form>
                <table>
                    <thead>
                        <tr>
                            <th>Patient ID</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Age</th>
                            <th>Gender</th>
                            <th>Last Visit Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (isset($_GET['search_patients'])) {
                            $search = mysqli_real_escape_string($conn, $_GET['search_patients']);
                            $query = "SELECT * FROM patients WHERE CONCAT(PatientID, FirstName, LastName) LIKE '%$search%'";
                        } else {
                            $query = "SELECT * FROM patients";
                        }
                        $result = mysqli_query($conn, $query);

                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>
                                    <td>" . htmlspecialchars($row['PatientID']) . "</td>
                                    <td>" . htmlspecialchars($row['FirstName']) . "</td>
                                    <td>" . htmlspecialchars($row['LastName']) . "</td>
                                    <td>" . htmlspecialchars($row['Age']) . "</td>
                                    <td>" . htmlspecialchars($row['Gender']) . "</td>
                                    <td>" . htmlspecialchars($row['LastVisitDate']) . "</td>
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
           

            
           <!--- 
           
           Search Patient function to assign the test, seach function could work by searching name or patient ID 

           put different check list for test right here
           general test and blood test

           two checklist in one submit form
           provide generate id 
            
           need to provide test date also so?

           3

           --->







                <?php
            /* generate query, row and database check connection 

            get the submit result from the checklist above*/
            
                ?>
            </section>
            <?php
            break;

        case 'DoctorProfile':
            ?>
            <section id="DoctorProfile">
                <h2>My Profile</h2>
                <?php
                $doctor_id = $_SESSION['DoctorID']; // Assuming doctor ID is stored in session
                $query = "SELECT * FROM doctor WHERE DoctorID = '$doctor_id'";
                $result = mysqli_query($conn, $query);

                if ($result && mysqli_num_rows($result) > 0) {
                    $doctor = mysqli_fetch_assoc($result);
                    echo "<p><strong>Name:</strong> " . htmlspecialchars($doctor['Firstname'] . ' ' . $doctor['Lastname']) . "</p>";
                    echo "<p><strong>Email:</strong> " . htmlspecialchars($doctor['Email']) . "</p>";
                    echo "<p><strong>Phone:</strong> " . htmlspecialchars($doctor['Phone']) . "</p>";
                    echo "<p><strong>Specialization:</strong> " . htmlspecialchars($doctor['Specialization']) . "</p>";
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
                <h2>Welcome, Doctor, to the Online Health Monitor System</h2>
            </section>
            <?php
    }
    ?>
</main>
</body>
</html>

