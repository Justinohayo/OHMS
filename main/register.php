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
        if(isset($_POST['submit']))
        {
            $username = $_POST["username"]; 
            $password = $_POST["password"]; 
            $firstname = $_POST["firstname"];
            $lastname = $_POST["lastname"]; 
            $sex = $_POST["sex"];
            $email = $_POST["email"]; 
            $dob = $_POST["dob"];
            $street =$_POST["street"];
            $city =$_POST["city"]; 
            $postalcode=$_POST["postalcode"]; 


            //veryfying unique email
            $verify_query = mysqli_query($conn, "SELECT Email FROM contact WHERE Email='$email'");
            


            if(!$verify_query)
            {
                die('Query failed: ' . mysqli_error($conn)); 

            }
            if(mysqli_num_rows($verify_query))
            {
                echo "<div class='message'>
                <p>This email is used by another user, try another one please!</p>
                </div><br>";
                echo "<a href='javascript:self.history.back()'><button class='btn'>Go Back</button></a>";
            }

            else
            {
                //insert data into the corresponding table 
                $insert_query = "INSERT INTO useraccount (UserType, Username, Password, AccountStatus) VALUES ('Patient','$username', '$password', 'Pending)";
                $insert_query2= "INSERT INTO patient (Firstname, Lastname, DOB, Sex) VALUES ('$firstname', '$lastname','$dob','$sex')";
                $insert_query3= "INSERT INTO contact (Email) VALUES ('$email')";
                
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
                        <label>Gender</label>
                        <input type="text" placeholder="Male/Female/Others" name="sex" id="sex" autocomplete="off" required>
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

    <script>
        document.getElementById('myform').addEventListener('submit', function(event) {
     
        const sexValue = document.getElementById('sex').value.trim();

       
        if (!sexValue) {
            alert("Please enter a value for sex.");
            event.preventDefault(); 
            return;
        }

        const validValues = ['Male', 'male', 'Female', 'female','Others'];
        if (!validValues.includes(sexValue)) {
            alert("Please enter a valid sex: Male, Female, or Others.");
            event.preventDefault();
        }
    });
      </script>

</body>
</html>