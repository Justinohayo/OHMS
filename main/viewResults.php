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
    <title>OHMS - Online Health Monitor System</title>
</head>
<body>

<header>
    <p><a href="index.html" class="logo">OHMS</a></p>
    <nav class="user">
        <a href="?section=home">Home</a>
        <a href="?section=ViewResults">View Results</a>
        <a href="?section=ViewAccounts">View Accounts</a>
        <a href="?section=Reports">Reports</a>
        <a href="?section=AdminProfile">Admin Profile</a>
        <span></span>
    </nav>
</header>

<main>
    <div class="container">
        <main class="content">

        <!-- Home Section -->
        <section id="home" class="page-section <?= $active_section === 'home' ? 'active' : '' ?>">
            <h2>Welcome Administrator to the Online Health Monitor System</h2>
        </section>

        <!-- View Results Section -->
        <section id="ViewResults" class="page-section <?= $active_section === 'ViewResults' ? 'active' : '' ?>">
            <form role="search" method="GET">
                <input type="hidden" name="section" value="ViewResults">
                <label for="search_results">Search Results</label>
                <input id="search_results" name="search_results" type="search" placeholder="Search..." autofocus required>
                <button type="submit">Go</button>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>Test Result ID</th>
                        <th>Staff ID</th>
                        <th>Patient ID</th>
                        <th>Assigned Test ID</th>
                        <th>Assigned Blood Test ID</th>
                        <th>Date Updated</th>
                        <th>Doctor Note</th>
                        <th>Result</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (isset($_GET['search_results'])) {
                        $search = mysqli_real_escape_string($conn, $_GET['search_results']);
                        $query = "SELECT * FROM test_results WHERE CONCAT(TestResultID, StaffID, PatientID, AssignedTestID, AssignedBloodTestID, DateUpdated, DoctorNote, Result) LIKE '%$search%'";
                    } else {
                        $query = "SELECT * FROM test_results";
                    }
                    $result = mysqli_query($conn, $query);

                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>
                                <td>" . htmlspecialchars($row['TestResultID']) . "</td>
                                <td>" . htmlspecialchars($row['StaffID']) . "</td>
                                <td>" . htmlspecialchars($row['PatientID']) . "</td>
                                <td>" . htmlspecialchars($row['AssignedTestID']) . "</td>
                                <td>" . htmlspecialchars($row['AssignedBloodTestID']) . "</td>
                                <td>" . htmlspecialchars($row['DateUpdated']) . "</td>
                                <td>" . htmlspecialchars($row['DoctorNote']) . "</td>
                                <td>" . htmlspecialchars($row['Result']) . "</td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8'>No Records Found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>

        <!-- View Accounts Section -->
       
    </div>
</main>


</body>
<footer>
    <p>&copy; 2024 OHMS</p>
</footer>

</html>
