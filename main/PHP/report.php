<?php
include("PHP/config.php"); // Include your database connection

// Generate a yearly report for a specific patient
function generateYearlyReport($patientId, $conn) {
    // Fetch the last 4 test results for the patient
    $query = "SELECT * FROM testresult WHERE PatientID = ? ORDER BY DateUpdated DESC LIMIT 4";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $patientId);
    $stmt->execute();
    $results = $stmt->get_result();

    if ($results->num_rows == 0) {
        return ["error" => "No test results found for the given Patient ID."];
    }

    $totalTests = $results->num_rows;

    // Ensure there are enough tests for prediction
    if ($totalTests < 4) {
        return ["error" => "Not enough data for prediction. At least 4 test results are required."];
    }

    $abnormalCount = 0;
    $abnormalTests = [];

    while ($row = $results->fetch_assoc()) {
        if ($row['Result'] === 'Abnormal') { // Use the Result column to check abnormality
            $abnormalCount++;
            $abnormalTests[] = $row; // Collect abnormal test details
        }
    }

    $abnormalRate = ($abnormalCount / $totalTests) * 100;

    return [
        "patient_id" => $patientId,
        "abnormal_rate" => $abnormalRate,
        "abnormal_tests" => $abnormalTests,
        "prediction" => $abnormalRate > 50 ? "Likely Abnormal" : "Likely Normal"
    ];
}

// Generate a general report for all patients
function generateGeneralReport($conn) {
    // Total unique patients tested
    $result1 = $conn->query("SELECT COUNT(DISTINCT PatientID) AS total FROM testresult");
    if (!$result1) {
        die("SQL Error (total patients): " . $conn->error);
    }
    $totalPatients = $result1->fetch_assoc()['total'];

    // Total patients with at least one abnormal result
    $result2 = $conn->query("
        SELECT COUNT(DISTINCT PatientID) AS abnormal 
        FROM testresult 
        WHERE Result = 'Abnormal'
    ");
    if (!$result2) {
        die("SQL Error (abnormal patients): " . $conn->error);
    }
    $abnormalPatients = $result2->fetch_assoc()['abnormal'];

    return [
        "total_patients" => $totalPatients,
        "abnormal_patients" => $abnormalPatients
    ];
}

// Handle incoming requests
if (isset($_GET['action'])) {
    $action = $_GET['action'];

    if ($action == 'yearly_report' && isset($_GET['patient_id'])) {
        $patient_id = trim($_GET['patient_id']);
        if (empty($patient_id)) {
            $response = ["error" => "Patient ID cannot be empty."];
        } else {
            $response = generateYearlyReport($patient_id, $conn);
        }
    } elseif ($action == 'general_report') {
        $response = generateGeneralReport($conn);
    } else {
        $response = ["error" => "Invalid action or missing parameters."];
    }

    // Return response as JSON
    header('Content-Type: application/json');
    echo json_encode([
        "status" => isset($response['error']) ? "error" : "success",
        "data" => $response
    ]);
}
?>
