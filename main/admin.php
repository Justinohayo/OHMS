<!DOCTYPE html>
<html lang="en">
    <?php
    session_start(); 
    ?>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Userpage.css">
    <title>OHMS - Online Health Monitor System</title>
</head>

<body>

    <header>
        <p><a  href="index.html" class = "logo">OHMS</a></p>

        <nav class = user>
        <a href ="#" onclick="showSection('home')">Home</a>
         <a href="#" onclick="showSection('ViewResults')">View Results</a>
         <a href="#" onclick="showSection('ViewAccounts')">View Accounts</a>
         <a href="#" onclick="showSection('Reports')">Reports</a>
         <a href="#" onclick="showSection('AdminProfile')">Admin Profile</a>
            <span></span>
        </nav>
    </header>

<main>
    <div class="container">
        <main class="content">

            <!-- Home Section-->
        <section id="home" class="page-section">
            <h2>Welcome Administor to the Online Health Monitor System</h2>
        </section>


          <!-- View Results Section-->
        <section id="ViewResults" class="page-section">

         <form onsubmit="event.preventDefault();" role="search">
            <label for="search"> Search Results</label>
            <input id="search" type="search" placeholder="Search..." autofocus required>
            <button type="submit">Go</button>
         </form>
        <div id="testresult"></div>
        </section>


          <!-- ViewAccount Section-->
          <section id="ViewAccounts" class="page-section">
          <form onsubmit="event.preventDefault();" role="search">
            <label for="search"> Search Results</label>
            <input id="search" type="search" placeholder="Search..." autofocus required>
            <button type="submit">Go</button>
         </form>

          <div id="accounts"></div>
        </section>


          <!-- Report Section-->
          <section id="Reports" class="page-section">

           <form onsubmit="event.preventDefault();" role="search">
            <label for="search"> Search Results</label>
            <input id="search" type="search" placeholder="Search..." autofocus required>
            <button type="submit">Go</button>
         </form>

          

        </section> 

        <!-- AdminProfile Section-->
        <section id="AdminProfile" class="page-section">
         
        <div class="box">
            

        </div>

        </section>

    </div>
</main>


<script src="fetchData.js">
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
