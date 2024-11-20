<!DOCTYPE html>
<html lang="en">

    <?php
    session_start(); 
    ?>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style2.css"> 
    <title>OHMS</title>
</head>

<body>
    <div class="containerregister">
        <div class="box form-box">
       <?php
        include("php/config.php");

        function generateID($prefix) {
            return $prefix . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        }
        
        if (isset($_POST['submit'])) {
            $addressid = generateID('ADR_');
            $contactid = generateID('CNT_');
            $patientid = generateID('PAT_');
            $useraccountid = generateID('USR_');
        
            $username = $_POST["username"];
            $password = $_POST["password"];
            $firstname = $_POST["firstname"];
            $lastname = $_POST["lastname"];
            $sex = $_POST["sex"];
            $email = $_POST["email"];
            $dob = $_POST["dob"];
            $street = $_POST["street"];
            $city = $_POST["city"];
            $postalcode = $_POST["postalcode"];
        
            // Verify unique email
            $verify_query = mysqli_query($conn, "SELECT Email FROM Contact WHERE Email='$email'");
            if (mysqli_num_rows($verify_query) > 0) {
                echo "<div class='message'>
                        <p>This email is already in use by another user, try another one please!</p>
                      </div><br>";
                echo "<a href='javascript:self.history.back()'><button class='btn'>Go Back</button></a>";
            } else {
         
                mysqli_begin_transaction($conn);
        
                try {
                    $insert_query = "INSERT INTO UserAccount (UserAccountID, UserType, Username, Password, AccountStatus) 
                                     VALUES ('$useraccountid', 'Patient', '$username', '$password', 'Pending')";
                    if (!mysqli_query($conn, $insert_query)) {
                        throw new Exception("User Account Insert Error: " . mysqli_error($conn));
                    }
        
                    $insert_query3 = "INSERT INTO Contact (ContactID, Email) 
                                        VALUES ('$contactid', '$email')";
                    if (!mysqli_query($conn, $insert_query3)) {
                        throw new Exception("Contact Insert Error: " . mysqli_error($conn));
                    }
        
                    $insert_query4 = "INSERT INTO Address (AddressID, Street, City, PostalCode) 
                                      VALUES ('$addressid', '$street', '$city', '$postalcode')";
                    if (!mysqli_query($conn, $insert_query4)) {
                        throw new Exception("Address Insert Error: " . mysqli_error($conn));
                    }
        
                    $insert_query2 = "INSERT INTO Patient (PatientID, Firstname, Lastname, DOB, Sex, AddressID, ContactID) 
                                      VALUES ('$patientid', '$firstname', '$lastname', '$dob', '$sex', '$addressid', '$contactid')";
                    if (!mysqli_query($conn, $insert_query2)) {
                        throw new Exception("Patient Insert Error: " . mysqli_error($conn));
                    }
        
                    mysqli_commit($conn);
                    echo "<div class='message'><p>Registration successful!</p></div>";
        
                } catch (Exception $e) {
                    mysqli_rollback($conn);
                    echo "<div class='message'><p>Registration failed: " . $e->getMessage() . "</p></div>";
                }
            }
        }

        ?> 
            <header>Sign Up</header>
            <form action="" method="post" id="myform">
                <div class="field">
                    <div class="input">
                        <label for="username">Username</label>
                        <input type="text" placeholder="Enter Username" name="username" id="username" autocomplete="off" required>
                    </div>

                    <div class="input">
                        <label for="password">Password</label>
                        <input type="password" placeholder="Enter Password" name="password" id="password" autocomplete="off" required>
                    </div>
                </div>
            
                <div class="field">
                    <div class="input">
                        <label>First Name</label>
                        <input type="text" placeholder="Enter First Name" name="firstname" id="firstname" autocomplete="off" required>
                    </div>
                    <div class="input">
                        <label>Last Name</label>
                        <input type="text" placeholder="Enter Last Name" name="lastname" id="lastname" autocomplete="off" required>
                    </div>
                </div>
            
                <div class="field">
                    <div class="input">
                        <label for="sex">Gender</label>
                        <select name="sex" id="sex">
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="input">
                        <label>Date Of Birth</label>
                        <input type="date" name="dob" id="dob" autocomplete="off" required>
                    </div>
                </div>
            
                <div class="field">
                    <div class="input">
                        <label>Street</label>
                        <input type="text" placeholder="Enter Your Street" name="street" id="street" autocomplete="off" required>
                    </div>
            
                    <div class="input">
                        <label>City</label>
                        <input type="text" placeholder="Enter Your City" name="city" id="city" autocomplete="off" required>
                    </div>

                    <div class="input">
                    <label>Postal Code</label>
                    <input type="text" placeholder="Enter Your Postal Code" name="postalcode" autocomplete="off" required>
                    </div>

                    <div class="input">
                        <label>Email</label>
                        <input type="email" placeholder="Enter Your Email" name="email" id="email" autocomplete="off" required>
                    </div>

                </div>

                <div class="field full-width">
                    <input type="submit" class="btn" name="submit" value="Register">
                </div>
                <div class="links">
                    Already have an account? <a href="login.php">Sign In Now</a>
                </div>
            </form>
        </div>
    </div>
     

</body>
</html>