<?php
include("php/config.php");

// Query to select all users
$sql = "SELECT UserAccountID, Password FROM useraccount";
$result = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_assoc($result)) {
    $userId = $row['UserAccountID'];
    $plainPassword = $row['Password'];

    // Check if the password is already hashed (hashed passwords start with $2y$)
    if (substr($plainPassword, 0, 4) !== '$2y$') {
        // Hash the plain-text password
        $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

        // Update the database with the hashed password
        $updateQuery = "UPDATE useraccount SET Password = '$hashedPassword' WHERE UserAccountID = '$userId'";
        mysqli_query($conn, $updateQuery);

        echo "Updated password for UserAccountID: $userId<br>";
    } else {
        echo "Password for UserAccountID: $userId is already hashed.<br>";
    }
}

echo "Password update process completed.";
?>
