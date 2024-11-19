<?php
session_start();
include("php/config.php");

// Determine the active section
$active_section = isset($_GET['section']) ? $_GET['section'] : 'home';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Userpage.css">
    <title>OHMS - Online Health Monitor System</title>
</head>
<body>

<header>
    <p><a href="index.html" class="logo">OHMS</a></p>
    <nav class="user">
        <a href="?section=home" class="<?= $active_section === 'home' ? 'active' : '' ?>">Home</a>
        <a href="?section=ViewResults" class="<?= $active_section === 'ViewResults' ? 'active' : '' ?>">View Results</a>
        <a href="?section=ViewAccounts" class="<?= $active_section === 'ViewAccounts' ? 'active' : '' ?>">View Accounts</a>
        <a href="?section=Reports" class="<?= $active_section === 'Reports' ? 'active' : '' ?>">Reports</a>
        <a href="?section=CreateAccount" class="<?= $active_section === 'CreateAccount' ? 'active' : '' ?>">Create Account</a>
        <a href="?section=AdminProfile" class="<?= $active_section === 'AdminProfile' ? 'active' : '' ?>">Admin Profile</a>
        <span></span>
    </nav>
</header>

<main>
    <?php
    switch ($active_section) {
        case 'ViewResults':
            ?>
            <section id="ViewResults">
                <form role="search" method="GET" class="searchbar">
                    <input type="hidden" name="section" value="ViewResults">
                    <label for="search_results">Search Results</label>
                    <input id="search_results" name="search_results" type="search" placeholder="Search..." autofocus required>
                    <button type="submit">Go</button>
                </form>
                <table>
                    <thead>
                        <tr>
                            <th>Test Result ID</th>
                            <th>Staff ID</th>
                            <th>Patient ID</th>
                            <th>Assigned Test ID</th>
                            <th>Assigned Blood Test ID</th>
                            <th>Date Updated</th>
                            <th>Doctor Note</th>
                            <th>Result</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (isset($_GET['search_results'])) {
                            $search = mysqli_real_escape_string($conn, $_GET['search_results']);
                            $query = "SELECT * FROM test_results WHERE CONCAT(TestResultID, StaffID, PatientID, AssignedTestID, AssignedBloodTestID, DateUpdated, DoctorNote, Result) LIKE '%$search%'";
                        } else {
                            $query = "SELECT * FROM test_results";
                        }
                        $result = mysqli_query($conn, $query);

                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>
                                    <td>" . htmlspecialchars($row['TestResultID']) . "</td>
                                    <td>" . htmlspecialchars($row['StaffID']) . "</td>
                                    <td>" . htmlspecialchars($row['PatientID']) . "</td>
                                    <td>" . htmlspecialchars($row['AssignedTestID']) . "</td>
                                    <td>" . htmlspecialchars($row['AssignedBloodTestID']) . "</td>
                                    <td>" . htmlspecialchars($row['DateUpdated']) . "</td>
                                    <td>" . htmlspecialchars($row['DoctorNote']) . "</td>
                                    <td>" . htmlspecialchars($row['Result']) . "</td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8'>No Records Found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </section>
            <?php
            break;

        case 'ViewAccounts':
            ?>
            <section id="ViewAccounts">
                <form role="search" method="GET" class="searchbar">
                    <input type="hidden" name="section" value="ViewAccounts">
                    <label for="search_accounts">Search Accounts</label>
                    <input id="search_accounts" name="search_accounts" type="search" placeholder="Search..." autofocus required>
                    <button type="submit">Go</button>
                </form>
                <table>
                    <thead>
                        <tr>
                            <th>UserAccountID</th>
                            <th>UserType</th>
                            <th>Username</th>
                            <th>AccountStatus</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (isset($_GET['search_accounts'])) {
                            $search = mysqli_real_escape_string($conn, $_GET['search_accounts']);
                            $query = "SELECT * FROM useraccount WHERE CONCAT(UserAccountID, UserType, Username, AccountStatus) LIKE '%$search%'";
                        } else {
                            $query = "SELECT * FROM useraccount";
                        }
                        $result = mysqli_query($conn, $query);

                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>
                                    <td>" . htmlspecialchars($row['UserAccountID']) . "</td>
                                    <td>" . htmlspecialchars($row['UserType']) . "</td>
                                    <td>" . htmlspecialchars($row['Username']) . "</td>
                                    <td>" . htmlspecialchars($row['AccountStatus']) . "</td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>No Records Found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </section>
            <?php
            break;

        case 'Reports':
            ?>
            <section id="Reports">
                <p>Reports functionality coming soon.</p>
            </section>
            <?php
            break;

        case 'CreateAccount':
            ?>
            <section id="CreateAccount">
                <form action="" method="POST">
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
            $usertype = $_POST["usertype"];
        
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
                                     VALUES ('$useraccountid', 'Patient', '$username', '$password', 'Approved')";
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

<section id="CreateAccount" class="page-section <?= $active_section === 'CreateAccount' ? 'active' : '' ?>">
    
<div class="containerregister">
    <div class="box">
        <div class="form-box">
            <header>Create Account</header>
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
                        <label for="firstname">First Name</label>
                        <input type="text" placeholder="Enter First Name" name="firstname" id="firstname" autocomplete="off" required>
                    </div>
                    <div class="input">
                        <label for="lastname">Last Name</label>
                        <input type="text" placeholder="Enter Last Name" name="lastname" id="lastname" autocomplete="off" required>
                    </div>
                </div>

                <div class="field">
                    <div class="input">
                        <label for="gender">Gender</label>
                        <select name="gender" id="gender">
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="input">
                        <label for="dob">Date of Birth</label>
                        <input type="date" name="dob" id="dob" required>
                    </div>
                </div>

                
                <div class="field">
                    <div class="input">
                        <label for="street">Street</label>
                        <input type="text" placeholder="Enter Street" name="street" id="street" autocomplete="off" required>
                    </div>

                    <div class="input">
                        <label for="city">City</label>
                        <input type="text" placeholder="Enter City" name="city" id="city" autocomplete="off" required>
                    </div>
                </div>

                <div class="field">
                    <div class="input">
                        <label for="postalcode">Postal Code</label>
                        <input type="text" placeholder="Enter Postal Code" name="postalcode" id="postalcode" autocomplete="off" required>
                    </div>

                    <div class="input">
                        <label for="email">Email</label>
                        <input type="email" placeholder="Enter Email" name="email" id="email" autocomplete="off" required>
                    </div>

                </div>

                <div class="field">
                    <div class="input">
                        <label for="usertype">Account Type</label>
                        <select name="usertype" id="usertype">
                            <option value="Staff">Staff</option>
                            <option value="Doctor">Doctor</option>
                        </select>
                    </div>
                </div>


                <div class="field full-width">
                    <input type="submit" class="btn" name="submit" value="Register">
                </div>

            </form>
        </div>
    </div>
</div>

</section>

                </form>
            </section>
            <?php
            break;

        case 'AdminProfile':
            ?>
            <section id="AdminProfile">
                <h2>Admin Profile</h2>
                <p>Admin profile functionality coming soon.</p>
            </section>
            <?php
            break;

        default:
            ?>
            <section id="home">
                <h2>Welcome Administrator to the Online Health Monitor System</h2>
            </section>
            <?php
    }
    ?>
</main>

<footer>
    <p>&copy; 2024 OHMS</p>
</footer>

</body>
</html>
