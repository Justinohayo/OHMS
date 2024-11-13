<!DOCTYPE html>
<?php

session_start();

?>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style2.css"> 
    <title>OHMS</title>
</head>

<body>

    <div class="containerlogin">
        <div class="box2 form-box2">

            <?php
                include("php/config.php"); 

               if(isset($_POST['submit'])){
                    $username  =  mysqli_real_escape_string($conn,$_POST['username']);
                    $password  =  mysqli_real_escape_string($conn,$_POST['password']);

                    $sql = "SELECT * FROM useraccount WHERE Username = '$username' AND Password = '$password'"; 
                    
                    $result = mysqli_query($conn,$sql); 
                    $row = mysqli_fetch_assoc($result); 


                    if(!$result)
                    {
                        die("Query failed: ".mysqli_error($conn)); 
                    }

                    else if(is_array($row) && !empty($row))
                    {
                        
                        $userid = $row['UserAccountID'];
                        $_SESSION['userid'] = $userid; 


                        $doctorQuery = "SELECT * FROM useraccount where UserAccountID = '$userid' AND UserType ='Doctor'";
                        $doctorResult = mysqli_query($conn, $doctorQuery); 
                        $doctorRow = mysqli_fetch_assoc($doctorResult); 
                        if(is_array($doctorRow) && !empty($doctorRow))
                        {
                            header("Location: doctoruser.php");
                        }
                      
                        $staffQuery = "SELECT * FROM useraccount where UserAccountID = '$userid' AND UserType='Staff'"; 
                        $staffResult = mysqli_query($conn,$staffQuery); 
                        $staffRow=mysqli_fetch_assoc($staffResult); 

                        if(is_array($staffRow) && !empty($staffRow))
                        {
                            header("Location: staffuser.php");
                        }

                        $patientQuery ="SELECT * FROM useraccount where UserAccountID ='$userid' AND UserType='Patient'"; 
                        $patientResult = mysqli_query($conn,$patientQuery); 
                        $patientRow=mysqli_fetch_assoc($patientResult); 
                        
                        if(is_array($patientRow) && !empty($patientRow))
                        {
                            header("Location: patientuser.php"); 
                        }

                        $adminQuery = "SELECT * FROM useraccount where UserAccountID ='$userid' AND UserType='Admin'";
                        $adminResult = mysqli_query($conn,$adminQuery); 
                        $adminRow=mysqli_fetch_assoc($adminResult);
                        if(is_array($adminRow) && !empty($adminRow))
                        {
                            header("Location: Admin.php");
                        }
                    }
                    else{
                        echo "<div class='message'>
                        <p>Wrong Username or Password</p>
                        </div><br>";
                        echo "<a href='index.php'><button class='btn'>Go Back</button></a>";
                    }
                    
                }else
                {

            ?>
            <header>Login</header>
            <form action="" method="post">
                <div class="field input">
                <label for="username">Username</label>
                <input type="text" placeholder="Enter Username" name="username" id="username" autocomplete="off" required>
            </div>
                <div class="field input">
                <label>Password</label>
                <input type="password" placeholder="Enter Password" name="password" id="password" autocomplete="off" required>
           
            </div>
               
            <div class="field">
                <input type="submit" class="btn2" name="submit" value="Login" required>
            </div>
            <div class="links2">
                Don't have account? <a href="register.php">Sign Up Now</a>
            </div>
            </form>

        </div>
            
   <?php } ?>

    </div>


</body>
</html>