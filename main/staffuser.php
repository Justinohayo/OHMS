<?php
session_start();
include("php/config.php");

// Determine the active section
$active_section = isset($_GET['section']) ? htmlspecialchars($_GET['section']) : 'home';

// Check if the patient has been selected
$patient_selected = isset($_POST['PatientID']) ? $_POST['PatientID'] : (isset($_GET['PatientID']) ? $_GET['PatientID'] : ''); 

// Check if the test type is selected
$test_selected = isset($_POST['TestType']) ? $_POST['TestType'] : '';

// Get the attributes based on the selected test type
$test_attributes = [];
if ($test_selected) {
    switch ($test_selected) {
        case 'Routine Hematology':
            $test_attributes = ['Hemoglobin Level (g/dL)', 'WBC Count (cells/mcL)', 'Platelet Count (cells/mcL)'];
            break;
        case 'Coagulation':
            $test_attributes = ['PT (sec)', 'INR', 'APTT (sec)'];
            break;
        case 'Routine Chemistry':
            $test_attributes = ['Glucose (mg/dL)', 'Creatinine (mg/dL)', 'Cholesterol (mg/dL)', 'Electrolytes (mmol/L)'];
            break;
        case 'Renal Function':
            $test_attributes = ['eGFR (mL/min/1.73m2)', 'BUN (mg/dL)', 'Creatinine (mg/dL)'];
            break;
        case 'Liver Function':
            $test_attributes = ['AST (U/L)', 'ALT (U/L)', 'Bilirubin (mg/dL)'];
            break;
        case 'Pancreas Function':
            $test_attributes = ['Amylase (U/L)', 'Lipase (U/L)'];
            break;
        case 'Endocrinology':
            $test_attributes = ['TSH (mU/L)', 'Free T4 (ng/dL)', 'Free T3 (pg/mL)'];
            break;
        case 'Tumor Markers':
            $test_attributes = ['CA-125 (U/mL)', 'PSA (ng/mL)', 'AFP (ng/mL)'];
            break;
        case 'ECG':
            $test_attributes = ['Heart Rate (bpm)', 'ECG Findings'];
            break;
        case 'X-Ray':
            $test_attributes = ['Findings (Description)', 'Location'];
            break;
        case 'CT Scan':
            $test_attributes = ['Findings (Description)', 'Location', 'Scan Type'];
            break;
        case 'Ultrasound':
            $test_attributes = ['Findings (Description)', 'Location', 'Scan Type'];
            break;
        default:
            $test_attributes = [];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Userpage.css">
    <title>OHMS - Staff Portal</title>
</head>
<body>
<header>
    <p><a href="index.html" class="logo">OHMS</a></p>
    <nav class="user">
        <a href="?section=home" class="<?= $active_section === 'home' ? 'active' : '' ?>">Home</a>
        <a href="?section=CreateTestResults" class="<?= $active_section === 'CreateTestResults' ? 'active' : '' ?>">Create Test Results</a>
        <a href="?section=ModifyTestResults" class="<?= $active_section === 'ModifyTestResults' ? 'active' : '' ?>">Modify Test Results</a>
        <a href="?section=MyProfile" class="<?= $active_section === 'MyProfile' ? 'active' : '' ?>">My Profile</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<main>
    <?php
    switch ($active_section) {
        case 'CreateTestResults':
            ?>
            <section id="CreateTestResults">
                <h2>Create Test Results</h2>

                <?php
                // If a patient ID is selected, show the test creation form
                if ($patient_selected) {
                    // Fetch patient details
                    $query = "SELECT PatientID, CONCAT(Firstname, ' ', Lastname) AS FullName FROM patient WHERE PatientID = '$patient_selected'";
                    $result = mysqli_query($conn, $query);
                    $patient = mysqli_fetch_assoc($result);
                    if ($patient) {
                        ?>
                        <form method="POST" action="create_test_result.php">
                            <label for="PatientID">Patient: </label>
                            <input id="PatientID" name="PatientID" type="text" value="<?= htmlspecialchars($patient['FullName']) ?>" readonly required>

                            <label for="TestType">Test Type</label>
                            <select id="TestType" name="TestType" required>
                                <option value="">-- Select Test Type --</option>
                                <optgroup label="Blood Tests">
                                    <option value="Routine Hematology">Routine Hematology</option>
                                    <option value="Coagulation">Coagulation</option>
                                    <option value="Routine Chemistry">Routine Chemistry</option>
                                    <option value="Renal Function">Renal Function</option>
                                    <option value="Liver Function">Liver Function</option>
                                    <option value="Pancreas Function">Pancreas Function</option>
                                    <option value="Endocrinology">Endocrinology</option>
                                    <option value="Tumor Markers">Tumor Markers</option>
                                </optgroup>
                                <optgroup label="Imaging Tests">
                                    <option value="ECG">ECG</option>
                                    <option value="X-Ray">X-Ray</option>
                                    <option value="CT Scan">CT Scan</option>
                                    <option value="Ultrasound">Ultrasound</option>
                                </optgroup>
                            </select>

                            <button type="submit">Create Test</button>
                        </form>
                        <?php
                        if ($test_selected) {
                            // Show the form for attributes based on the selected test type
                            echo "<form method='POST' action='create_test_result.php'>";
                            echo "<h3>Test Attributes for " . htmlspecialchars($test_selected) . "</h3>";
                            foreach ($test_attributes as $attribute) {
                                echo "<label for='" . htmlspecialchars($attribute) . "'>" . htmlspecialchars($attribute) . ":</label>";
                                echo "<input type='text' id='" . htmlspecialchars($attribute) . "' name='" . htmlspecialchars($attribute) . "' required><br>";
                            }
                            echo "<button type='submit'>Submit Test Results</button>";
                            echo "</form>";
                        }
                    } else {
                        echo "<p>No patient found with the selected ID.</p>";
                    }
                } else {
                    // If no patient is selected, show the patient search bar
                    ?>
                    <form method="GET" action="" class="searchbar">
                        <input type="hidden" name="section" value="CreateTestResults">
                        <label for="search_patient">Search Patient</label>
                        <input id="search_patient" name="search_patient" type="search" placeholder="Enter Patient ID, First Name, or Last Name" autofocus required>
                        <button type="submit">Search</button>
                    </form>
                    <?php

                    // If a search query is provided, fetch and display the results
                    if (isset($_GET['search_patient'])) {
                        $search = mysqli_real_escape_string($conn, $_GET['search_patient']);
                        $query = "
                            SELECT PatientID, CONCAT(Firstname, ' ', Lastname) AS FullName 
                            FROM patient 
                            WHERE PatientID LIKE '%$search%' 
                               OR Firstname LIKE '%$search%' 
                               OR Lastname LIKE '%$search%'
                        ";
                        $result = mysqli_query($conn, $query);

                        // Display the search results
                        if ($result && mysqli_num_rows($result) > 0) {
                            echo "<table>";
                            echo "<thead><tr><th>Patient ID</th><th>Name</th><th>Action</th></tr></thead>";
                            echo "<tbody>";
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>
                                    <td>" . htmlspecialchars($row['PatientID']) . "</td>
                                    <td>" . htmlspecialchars($row['FullName']) . "</td>
                                    <td>
                                        <form method='GET' action=''>
                                            <input type='hidden' name='section' value='CreateTestResults'>
                                            <input type='hidden' name='PatientID' value='" . htmlspecialchars($row['PatientID']) . "'>
                                            <button type='submit'>Select</button>
                                        </form>
                                    </td>
                                </tr>";
                            }
                            echo "</tbody>";
                            echo "</table>";
                        } else {
                            echo "<p>No patients found for your search query.</p>";
                        }
                    }
                }
                ?>
            </section>
            <?php
            break;

        case 'ModifyTestResults':
            // Modify Test Results code here...
            break;
        
        // Other sections here...
    }
    ?>
</main>

</body>
</html>
