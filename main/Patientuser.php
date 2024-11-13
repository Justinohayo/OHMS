<!DOCTYPE html>
<html lang="en">
    <?php
    session_start(); 
    ?>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Style2.css">
    <title>OHMS - Online Health Monitor System</title>
</head>

<body>
    <header>
        <p><a  href="index.html" class = "logo">OHMS</a></p>
        <nav class = user>
        <a href ="#" onclick="showSection('home')">Home</a>
         <a href="#" onclick="showSection('ViewResults')">View Results</a>
         <a href="#" onclick="showSection('Appointments')">Appointments</a>
         <a href="#" onclick="showSection('Prescription')">Prescription</a>
         <a href="#" onclick="showSection('MyProfile')">My Profile</a>
         <span></span>
       </nav>

    </header>

<main>

    <div class="container">
        <main class="content">

            <!-- Home Section-->
            <section id="home" class="page-section">
                <h2>Welcome Justin to the Online Health Monitor System</h2>
                <p>Explore your health records, manage appointments, and connect with healthcare professionals easily.</p>
            </section>

          <!-- View Results Section-->
          <section id="ViewResults" class="page-section">
          <input type="text" placeholder="Search...">

        </section>

          <!-- Appointment Section-->
          <section id="appointments" class="page-section">
          <input type="text" placeholder="Search...">

        </section>

          <!-- Prescription Section-->
          <section id="Prescription" class="page-section">
          <input type="text" placeholder="Search...">
           <table>

           </table>
        </section>

          <!-- My Profile Section-->
          <section id="MyProfile" class="page-section">
            <p> First name: </p>
            <p> Last name: </p>
            <p> Date of Birth: </p>
            <p> Email: </p>
            <p> Phone Number: </p>
        </section>

    </div>
</main>

<script>
function showSection(sectionId){


    const sections = document.querySelectorAll('.page-section');

    sections.forEach(section =>
        { section.style.display ='none';
        });

        document.getElementById(sectionId).style.display = 'block';

    };

    window.onload =function(){
        showSection('home');
    }; 


</script>

    <footer>
        <p>&copy; 2024 OHMS</p>
    </footer>

</body>
</html>