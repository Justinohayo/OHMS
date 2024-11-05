document.getElementById("loginForm").addEventListener("submit", function(event) 
{
    event.preventDefault(); // Prevent form submission

    
    const role = document.getElementById("role").value;

   
    if (role === "staff") 
        {
        window.location.href = "staffuser.html";
    } else if (role === "patient") 
        {
        window.location.href = "Patientuser.html";
    } else if (role === "doctor") 
        {
        window.location.href = "doctoruser.html";
    } else
     {
        alert("Invalid role selected.");
    }
});
