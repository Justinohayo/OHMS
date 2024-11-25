<?php
include("php/config.php");

// Query to select all users
$sql = "SELECT UserAccountID, Password FROM useraccount";
$result = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_assoc($result)) {
    $userId = $row['UserAccountID'];
    $passwordInDB = $row['Password'];

    // Check if the password is less than 60 characters or improperly hashed
    if (strlen($passwordInDB) < 60 || substr($passwordInDB, 0, 4) !== '$2y$') {
        // Assume the password is in plain text, hash it
        $hashedPassword = password_hash($passwordInDB, PASSWORD_DEFAULT);

        // Update the database with the hashed password
        $updateQuery = "UPDATE useraccount SET Password = '$hashedPassword' WHERE UserAccountID = '$userId'";
        if (mysqli_query($conn, $updateQuery)) {
            echo "Updated password for UserAccountID: $userId<br>";
        } else {
            echo "Error updating password for UserAccountID: $userId - " . mysqli_error($conn) . "<br>";
        }
    } else {
        echo "Password for UserAccountID: $userId is already valid.<br>";
    }
}

echo "Password update process completed.";
?>
