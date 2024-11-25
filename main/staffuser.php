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

        // If the test is a Urine Test, insert the urine test data
        if ($test_type == 'Urine Test') {
            // Generate UrineTestResultID (e.g., UT000001)
            $urine_test_id = 'UT' . str_pad($test_result_id, 6, '0', STR_PAD_LEFT);

            // Prepare the urine test data
            $ph_level = isset($test_results['PH Level']) ? $test_results['PH Level'] : '';
            $glucose_level = isset($test_results['Glucose Level']) ? $test_results['Glucose Level'] : '';
            $urea = isset($test_results['Urea']) ? $test_results['Urea'] : '';
            $gravity = isset($test_results['Gravity']) ? $test_results['Gravity'] : '';
            $creatinine = isset($test_results['Creatinine']) ? $test_results['Creatinine'] : '';

            // Insert the Urine Test Results into UrineTestResult
            $query = "INSERT INTO UrineTestResult (UrineTestResultID, TestResultID, PHLevel, GlucoseLevel, Urea, Gravity, Creatinine) 
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('ssssddds', $urine_test_id, $test_result_id, $ph_level, $glucose_level, $urea, $gravity, $creatinine);
            $stmt->execute();
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
                case 'Renal Function':
                    testAttributes = [
                        'GFR Rate', 'Serum Creatinine', 'Uric Acid', 'Sodium', 'Blood Urea Nitrogen'
                    ];
                    break;
                case 'Liver Function':
                    testAttributes = [
                        'Alanine Aminotransferase', 'Albumin', 'Alkaline Phosphatase', 'Aspartate Aminotransferase', 'Conjugated Bilirubin'
                    ];
                    break;
                case 'Routine Hematology':
                    testAttributes = [
                        'MCV', 'Lymphocyte', 'RBC', 'Platelets', 'WBC'
                    ];
                    break;
                case 'Coagulation':
                    testAttributes = [
                        'Bleeding Time', 'Clotting Time', 'Prothrombin Time', 'INR'
                    ];
                    break;
                case 'Routine Chemistry':
                    testAttributes = [
                        'Calcium Ions', 'Potassium Ions', 'Sodium Ions', 'Bicarbonate', 'Chloride Ions'
                    ];
                    break;
                case 'Tumor Markers':
                    testAttributes = [
                        'Cancer Antigen', 'CA 27-29', 'CA 125', 'Carcinoembryonic Antigen', 'Circulating Tumor Cells'
                    ];
                    break;
                case 'Endocrinology':
                    testAttributes = [
                        'Throtropin', 'Testosterone', 'Growth Hormone', 'Insulin', 'Cortisol'
                    ];
                    break;
                case 'Pancreas Function':
                    testAttributes = [
                        'Insulin', 'Fasting Glucose', 'Lipase', 'Amylase', 'C-Peptide'
                    ];
                    break;
                case 'Urine Test':
                    testAttributes = [
                        'PH Level', 'Glucose Level', 'Urea', 'Gravity', 'Creatinine'
                    ];
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
                    ?>

                    <form method="POST" action="?section=CreateTestResults">
                        <input type="hidden" name="PatientID" value="<?= htmlspecialchars($patient_selected) ?>">

                        <label for="TestType">Select Test Type: </label>
                        <select name="TestType" id="TestType" onchange="generateTestFields()" required>
                            <option value="">--Select--</option>
                            <option value="Renal Function">Renal Function</option>
                            <option value="Liver Function">Liver Function</option>
                            <option value="Routine Hematology">Routine Hematology</option>
                            <option value="Coagulation">Coagulation</option>
                            <option value="Routine Chemistry">Routine Chemistry</option>
                            <option value="Tumor Markers">Tumor Markers</option>
                            <option value="Endocrinology">Endocrinology</option>
                            <option value="Pancreas Function">Pancreas Function</option>
                            <option value="Urine Test">Urine Test</option>
                        </select>
                        
                        <div id="TestResults"></div>

                        <button type="submit">Save Test Results</button>
                    </form>
                    <?php
                }
                ?>
            </section>
            <?php
            break;
        default:
            echo "<h1>Welcome to the OHMS System</h1>";
            break;
    }
    ?>
</main>

<footer>
    <p>&copy; 2024 OHMS. All Rights Reserved.</p>
</footer>
</body>
</html>
