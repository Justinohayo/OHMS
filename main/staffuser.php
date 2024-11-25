<?php
session_start();
require_once("php/config.php");

// Initialize variables
$active_section = isset($_GET['section']) ? htmlspecialchars($_GET['section']) : 'home';
$patient_selected = isset($_POST['PatientID']) ? htmlspecialchars($_POST['PatientID']) : '';
$test_selected = isset($_POST['TestType']) ? htmlspecialchars($_POST['TestType']) : '';

// Fetch Patient Information if Patient ID is set
if ($patient_selected) {
    $query = "SELECT PatientID, CONCAT(Firstname, ' ', Lastname) AS FullName FROM patient WHERE PatientID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $patient_selected);
    $stmt->execute();
    $result = $stmt->get_result();
    $patient = $result->fetch_assoc();
}

// Handle form submission for test results
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['TestResults'])) {
    if (empty($patient_selected) || empty($test_selected)) {
        echo "<p style='color:red;'>Patient or Test Type is not selected. Please select both.</p>";
    } else {
        // Sanitize input and capture the test results
        $test_results = $_POST['TestResults'];
        $patient_id = $patient_selected;
        $test_type = $test_selected;

        // Insert the test result into the test_results table
        $query = "INSERT INTO test_results (PatientID, TestType) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ss', $patient_id, $test_type);
        $stmt->execute();

        // Get the last inserted TestResultID
        $test_result_id = $stmt->insert_id;

        // Insert each test attribute into the test_result_details table
        foreach ($test_results as $attribute => $value) {
            if (!empty($value)) { // Only insert non-empty values
                $query = "INSERT INTO test_result_details (TestResultID, Attribute, Value) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('iss', $test_result_id, $attribute, $value);
                $stmt->execute();
            }
        }

        echo "<p>Test results saved successfully!</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Userpage.css">
    <title>OHMS - Staff Portal</title>
    <script>
        // JavaScript to dynamically generate form fields based on test type
        function generateTestFields() {
            var testType = document.getElementById('TestType').value;
            var resultsDiv = document.getElementById('TestResults');
            resultsDiv.innerHTML = ''; // Clear previous inputs

            var testAttributes = [];
            switch (testType) {
                case 'Routine Hematology':
                    testAttributes = ['Hemoglobin Level (g/dL)', 'WBC Count (cells/mcL)', 'Platelet Count (cells/mcL)'];
                    break;
                case 'Coagulation':
                    testAttributes = ['PT (sec)', 'INR', 'APTT (sec)'];
                    break;
                case 'Routine Chemistry':
                    testAttributes = ['Glucose (mg/dL)', 'Creatinine (mg/dL)', 'Cholesterol (mg/dL)', 'Electrolytes (mmol/L)'];
                    break;
                case 'Renal Function':
                    testAttributes = ['eGFR (mL/min/1.73m2)', 'BUN (mg/dL)', 'Creatinine (mg/dL)'];
                    break;
                case 'Liver Function':
                    testAttributes = ['AST (U/L)', 'ALT (U/L)', 'Bilirubin (mg/dL)'];
                    break;
                case 'Pancreas Function':
                    testAttributes = ['Amylase (U/L)', 'Lipase (U/L)'];
                    break;
                case 'Endocrinology':
                    testAttributes = ['TSH (mU/L)', 'Free T4 (ng/dL)', 'Free T3 (pg/mL)'];
                    break;
                case 'Tumor Markers':
                    testAttributes = ['CA-125 (U/mL)', 'PSA (ng/mL)', 'AFP (ng/mL)'];
                    break;
                case 'ECG':
                    testAttributes = ['Heart Rate (bpm)', 'ECG Findings'];
                    break;
                case 'X-Ray':
                    testAttributes = ['Findings (Description)', 'Location'];
                    break;
                case 'CT Scan':
                    testAttributes = ['Findings (Description)', 'Location', 'Scan Type'];
                    break;
                case 'Ultrasound':
                    testAttributes = ['Findings (Description)', 'Location', 'Scan Type'];
                    break;
                default:
                    testAttributes = [];
            }

            // Generate a table with headings and input fields for each attribute
            var table = document.createElement('table');
            var thead = document.createElement('thead');
            var tbody = document.createElement('tbody');
            var headerRow = document.createElement('tr');

            // Add table headers
            var headers = ['Test Attribute', 'Input Value'];
            headers.forEach(function(header) {
                var th = document.createElement('th');
                th.textContent = header;
                headerRow.appendChild(th);
            });
            thead.appendChild(headerRow);
            table.appendChild(thead);

            // Add table rows for each attribute
            testAttributes.forEach(function(attribute) {
                var row = document.createElement('tr');
                var td1 = document.createElement('td');
                td1.textContent = attribute;
                var td2 = document.createElement('td');
                var input = document.createElement('input');
                input.type = 'text';
                input.name = 'TestResults[' + attribute + ']';
                input.id = attribute;
                td2.appendChild(input);
                row.appendChild(td1);
                row.appendChild(td2);
                tbody.appendChild(row);
            });

            table.appendChild(tbody);
            resultsDiv.appendChild(table);
        }
    </script>
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

                <form method="POST" action="?section=CreateTestResults">
                    <label for="PatientID">Enter Patient ID: </label>
                    <input id="PatientID" name="PatientID" type="text" value="<?= htmlspecialchars($patient_selected) ?>" required>
                    <button type="submit">Search</button>
                </form>

                <?php
                // If patient ID is selected and found, display the form for test type selection
                if ($patient_selected && isset($patient)) {
                    echo "<h3>Selected Patient: " . htmlspecialchars($patient['FullName']) . "</h3>";

                    // Display the test type selection
                    ?>
                    <form method="POST" action="?section=CreateTestResults" onsubmit="generateTestFields()">
                        <input type="hidden" name="PatientID" value="<?= htmlspecialchars($patient['PatientID']) ?>">
                        <label for="TestType">Test Type</label>
                        <select id="TestType" name="TestType" onchange="generateTestFields()" required>
                            <option value="">-- Select Test Type --</option>
                            <option value="Routine Hematology" <?= $test_selected == 'Routine Hematology' ? 'selected' : '' ?>>Routine Hematology</option>
                            <option value="Coagulation" <?= $test_selected == 'Coagulation' ? 'selected' : '' ?>>Coagulation</option>
                            <option value="Routine Chemistry" <?= $test_selected == 'Routine Chemistry' ? 'selected' : '' ?>>Routine Chemistry</option>
                            <option value="Renal Function" <?= $test_selected == 'Renal Function' ? 'selected' : '' ?>>Renal Function</option>
                            <option value="Liver Function" <?= $test_selected == 'Liver Function' ? 'selected' : '' ?>>Liver Function</option>
                            <option value="Pancreas Function" <?= $test_selected == 'Pancreas Function' ? 'selected' : '' ?>>Pancreas Function</option>
                            <option value="Endocrinology" <?= $test_selected == 'Endocrinology' ? 'selected' : '' ?>>Endocrinology</option>
                            <option value="Tumor Markers" <?= $test_selected == 'Tumor Markers' ? 'selected' : '' ?>>Tumor Markers</option>
                            <option value="ECG" <?= $test_selected == 'ECG' ? 'selected' : '' ?>>ECG</option>
                            <option value="X-Ray" <?= $test_selected == 'X-Ray' ? 'selected' : '' ?>>X-Ray</option>
                            <option value="CT Scan" <?= $test_selected == 'CT Scan' ? 'selected' : '' ?>>CT Scan</option>
                            <option value="Ultrasound" <?= $test_selected == 'Ultrasound' ? 'selected' : '' ?>>Ultrasound</option>
                        </select>

                        <div id="TestResults"></div> <!-- Test result inputs will be dynamically added here -->

                        <button type="submit">Save Test Results</button>
                    </form>
                </section>
                <?php
                }
                break;

        default:
            echo "<h2>Welcome to the OHMS System</h2>";
            break;
    }
    ?>
</main>
</body>
</html>
