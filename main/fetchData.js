
function fetchtestresults() {
    fetch('fetchtestresults.php')
        .then(response => response.json())
        .then(data => {
            const testResultsDiv = document.getElementById('testresult');
            if (data.error) {
                testResultsDiv.innerHTML = `<p>Error: ${data.error}</p>`;
            } else {
                let tableHTML = `<table class = "table"><tr><th>TestResultID</th><th>StaffID</th><th>PatientID</th><th>AssignedTestID</th><th>AssignedBloodTestID</th><th>Date Updated</th><th>Doctor Note</th><th>Result</th></tr>`;
                data.forEach(row => {
                    tableHTML += `<tr><td>${row.TestResultID}</td><td>${row.StaffID}</td><td>${row.PatientID}</td><td>${row.AssignedTestID}</td><td>${row.AssignedBloodTestID}</td><td>${row.DateUpdated}</td><td>${row.DoctorNote}</td><td>${row.Result}</td></tr>`;
                });
                tableHTML += `</table>`;
                testResultsDiv.innerHTML = tableHTML;
            }
        })
        .catch(error => console.error('Error fetching test results:', error));
}


// Function to fetch and display User Accounts
function fetchaccounts() {
    fetch('fetchaccounts.php')
        .then(response => response.json())
        .then(data => {
            const accountsDiv = document.getElementById('accounts');
            if (data.error) {
                accountsDiv.innerHTML = `<p>Error: ${data.error}</p>`;
            } else {
                let tableHTML = `<table class = "tablestyle">
                <tr>
                <th>UserAccountID</th>
                <th>UserType</th>
                <th>Username</th>
                <th>AccountStatus</th>
                </tr>`;
                data.forEach(row => {
                    tableHTML += `
                    <tr>
                    <td>${row.UserAccountID}</td>
                    <td>${row.UserType}</td>
                    <td>${row.Username}</td>
                    <td>${row.AccountStatus}</td>
                    </tr>`;
                });
                tableHTML += `</table>`;
                accountsDiv.innerHTML = tableHTML;
            }
        })
        .catch(error => console.error('Error fetching accounts:', error));
}


function showSection(sectionId)
{
    const sections = document.querySelectorAll('.page-section');
    sections.forEach(section => section.style.display = 'none');

    document.getElementById(sectionId).style.display = 'block';

    // Load the data based on the section
    if (sectionId === 'ViewResults') {
        fetchtestresults();
    } else if (sectionId === 'ViewAccounts') {
        fetchaccounts();
    }
}
// Call fetch functions when relevant sections are shown
document.addEventListener('DOMContentLoaded', function () {
    showSection('home');
})
