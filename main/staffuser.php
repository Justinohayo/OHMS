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
        <a href="index.html">Logout</a>
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

                <?php if ($patient_selected && $patient): ?>
                    <h3>Patient: <?= $patient['FullName'] ?></h3>
                    <form method="POST" action="?section=CreateTestResults">
                        <label for="TestType">Select Test Type: </label>
                        <select id="TestType" name="TestType" onchange="generateTestFields()">
                            <option value="">--Select Test Type--</option>
                            <option value="Renal Function" <?= $test_selected == 'Renal Function' ? 'selected' : '' ?>>Renal Function</option>
                            <option value="Liver Function" <?= $test_selected == 'Liver Function' ? 'selected' : '' ?>>Liver Function</option>
                            <option value="Routine Hematology" <?= $test_selected == 'Routine Hematology' ? 'selected' : '' ?>>Routine Hematology</option>
                            <option value="Coagulation" <?= $test_selected == 'Coagulation' ? 'selected' : '' ?>>Coagulation</option>
                            <option value="Routine Chemistry" <?= $test_selected == 'Routine Chemistry' ? 'selected' : '' ?>>Routine Chemistry</option>
                            <option value="Tumor Markers" <?= $test_selected == 'Tumor Markers' ? 'selected' : '' ?>>Tumor Markers</option>
                            <option value="Endocrinology" <?= $test_selected == 'Endocrinology' ? 'selected' : '' ?>>Endocrinology</option>
                            <option value="Pancreas Function" <?= $test_selected == 'Pancreas Function' ? 'selected' : '' ?>>Pancreas Function</option>
                            <option value="Urine Test" <?= $test_selected == 'Urine Test' ? 'selected' : '' ?>>Urine Test</option>
                        </select>
                        <button type="submit">Next</button>
                    </form>
                    <div id="TestResults"></div>
                    <form method="POST" action="?section=CreateTestResults">
                        <input type="hidden" name="PatientID" value="<?= $patient_selected ?>">
                        <input type="hidden" name="TestType" value="<?= $test_selected ?>">
                        <button type="submit" name="TestResults" value="Save">Save Test Results</button>
                    </form>
                <?php endif; ?>
            </section>
            <?php break;

case 'MyProfile':
    ?>
    <section id="MyProfile">
        <h2>My Profile</h2>
        <?php
        if (isset($_SESSION['StaffID'])) {
            // Check if profile update action is triggered
            if (isset($_POST['action']) && $_POST['action'] === 'updateProfile') {
                // Sanitize and capture the submitted data
                $firstname = htmlspecialchars($_POST['Firstname']);
                $lastname = htmlspecialchars($_POST['Lastname']);
                $sex = htmlspecialchars($_POST['Sex']);
                $dob = htmlspecialchars($_POST['DOB']);

                // Update the staff profile in the database
                $query = "UPDATE staff SET Firstname = ?, Lastname = ?, Sex = ?, DOB = ? WHERE StaffID = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('sssss', $firstname, $lastname, $sex, $dob, $_SESSION['StaffID']);
                if ($stmt->execute()) {
                    echo "<p>Profile updated successfully!</p>";
                } else {
                    echo "<p style='color:red;'>Error updating profile. Please try again.</p>";
                }
            }

            // Fetch staff profile details
            $query = "SELECT Firstname, Lastname, Sex, DOB FROM staff WHERE StaffID = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('s', $_SESSION['StaffID']);
            $stmt->execute();
            $result = $stmt->get_result();
            $staff = $result->fetch_assoc();

            if ($staff) {
                ?>
                <form method="POST" action="?section=MyProfile">
                    <input type="hidden" name="action" value="updateProfile">
                    <label for="Firstname">Firstname:</label>
                    <input type="text" name="Firstname" id="Firstname" value="<?= htmlspecialchars($staff['Firstname']) ?>" required>

                    <label for="Lastname">Lastname:</label>
                    <input type="text" name="Lastname" id="Lastname" value="<?= htmlspecialchars($staff['Lastname']) ?>" required>

                    <label for="Sex">Sex:</label>
                    <select name="Sex" id="Sex" required>
                        <option value="Male" <?= $staff['Sex'] === 'Male' ? 'selected' : '' ?>>Male</option>
                        <option value="Female" <?= $staff['Sex'] === 'Female' ? 'selected' : '' ?>>Female</option>
                        <option value="Other" <?= $staff['Sex'] === 'Other' ? 'selected' : '' ?>>Other</option>
                    </select>

                    <label for="DOB">Date of Birth:</label>
                    <input type="date" name="DOB" id="DOB" value="<?= htmlspecialchars($staff['DOB']) ?>" required>

                    <button type="submit">Update Profile</button>
                </form>
                <?php
            } else {
                echo "<p>Unable to fetch your profile details. Please contact the administrator.</p>";
            }
        } else {
            echo "<p>You are not logged in. Please log in to view your profile.</p>";
        }
        ?>
    </section>
    <?php
    break;


        default:
            echo "<p>Welcome to the Staff Portal</p>";
            break;
    }
    ?>
</main>
</body>
</html>

