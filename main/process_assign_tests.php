<?php
include("php/config.php");

// Ensure the user is logged in
if (!isset($_SESSION['userid']) || empty($_SESSION['userid'])) {
    header("Location: login.php");
    exit;
}

function generateID($prefix, $table, $column, $conn) {
    do {
        $id = $prefix . uniqid() . bin2hex(random_bytes(4));
        $query = "SELECT $column FROM $table WHERE $column = '$id'";
        $result = mysqli_query($conn, $query);
    } while (mysqli_num_rows($result) > 0); // Keep generating until a unique ID is found
    return $id;
}

//when doctor assign test, the test that being assign will have a AssignTestID

$AssignedTestID = generateID('AST_','assignedtest','AssignedTestID',$conn); 

// Fetch the logged-in doctor's ID
$doctorID = $_SESSION['DoctorID'] ?? null;
if (!$doctorID) {
    echo "Error: Doctor ID not found. Please log in again.";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patientID = $_POST['patientID'] ?? null;
    $tests = $_POST['tests'] ?? [];



    // Validate input
    if (!$patientID || empty($tests)) {
        echo "<p>Error: Please select a patient and at least one test.</p>";
        exit;
    }

    $assignDate = date('Y-m-d H:i:s'); // Current date and time

    // Insert assigned tests into the database
    $conn->begin_transaction(); // Start transaction
    try {
        foreach ($tests as $test) {
            // Generate a unique ID for the assigned test
            $assignedTestID = uniqid('AST_', true);

            // Insert the assigned test
            $query = "INSERT INTO AssignedTest ( PatientID, DoctorID, DateAssigned, TestType) 
                      VALUES ( '$patientID', '$doctorID', '$assignDate', '$test')";
            if (!mysqli_query($conn, $query)) {
                throw new Exception("Error assigning test: " . mysqli_error($conn));
            }
        }

        $conn->commit(); // Commit the transaction
        echo "<p>Tests assigned successfully to Patient ID: " . htmlspecialchars($patientID) . ".</p>";
        echo "<a href='doctoruser.php?section=Prescription'>Back to Prescription</a>";
    } catch (Exception $e) {
        $conn->rollback(); // Roll back the transaction on error
        echo "<p>Error: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p>Invalid request. Please submit the form.</p>";
}
?>