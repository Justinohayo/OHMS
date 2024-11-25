<?php
session_start();
require_once("php/config.php");

// Determine the active section
$active_section = isset($_GET['section']) ? htmlspecialchars($_GET['section']) : 'home';

// Check if the patient has been selected
$patient_selected = isset($_POST['PatientID']) ? htmlspecialchars($_POST['PatientID']) : 
    (isset($_GET['PatientID']) ? htmlspecialchars($_GET['PatientID']) : '');

// Check if the test type is selected
$test_selected = isset($_POST['TestType']) ? htmlspecialchars($_POST['TestType']) : '';

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
        <span></span>
    </nav>
</header>

<main>
    <?php
    switch ($active_section) {
        case 'CreateTestResults':
            ?>
            <section id="CreateTestResults">
                <h2>Create Test Results</h2>

                <?php if ($patient_selected): ?>
                    <?php
                    // Fetch patient details
                    $query = "SELECT PatientID, CONCAT(Firstname, ' ', Lastname) AS FullName FROM patient WHERE PatientID = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param('s', $patient_selected);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $patient = $result->fetch_assoc();

                    if ($patient): ?>
                        <form method="POST" action="">
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

                            <button type="submit">Submit</button>
                        </form>

                        <?php if ($test_selected): ?>
                            <form method="POST" action="">
                                <h3>Test Attributes for <?= htmlspecialchars($test_selected) ?></h3>
                                <?php foreach ($test_attributes as $attribute): ?>
                                    <label for="<?= htmlspecialchars($attribute) ?>"><?= htmlspecialchars($attribute) ?>:</label>
                                    <input type="text" id="<?= htmlspecialchars($attribute) ?>" name="<?= htmlspecialchars($attribute) ?>" required><br>
                                <?php endforeach; ?>
                                <button type="submit">Save Test Results</button>
                            </form>
                        <?php endif; ?>

                    <?php else: ?>
                        <p>No patient found with the selected ID.</p>
                    <?php endif; ?>

                <?php else: ?>
                    <form method="GET" action="" class="searchbar">
                        <input type="hidden" name="section" value="CreateTestResults">
                        <label for="search_patient">Search Patient</label>
                        <input id="search_patient" name="search_patient" type="search" placeholder="Enter Patient ID, First Name, or Last Name" required>
                        <button type="submit">Search</button>
                    </form>
                <?php endif; ?>
            </section>
            <?php
            break;

        case 'ModifyTestResults':
            // Modify Test Results logic here
            break;

        case 'MyProfile':
            // My Profile logic here
            break;
    }
    ?>
</main>

</body>
</html>
