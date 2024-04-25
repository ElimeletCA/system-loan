document.addEventListener('DOMContentLoaded', function() {
    // Function to get the value of a URL parameter
    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        var results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    };

    // Get the ID from the URL
    const objectId = getUrlParameter('id');
    
    // Make a GET request to your API
    fetch('http://localhost/system-loan/object_management?id=' + objectId)
        .then(response => response.json())
        .then(data => {

            // Check the status field in the JSON response
            if (data.object.object_status === 'AVAILABLE') {
                // Show the available div
                document.getElementById('caseavailable').style.display = 'block';
                document.getElementById('id_object').value = objectId;
            } else if (data.object.object_status === 'ONLOAN') {
                // Show the unavailable div
                document.getElementById('caseonloan').style.display = 'block';
            }
            // Check if loans data exists
            if (data.loans && data.loans.length > 0) {
                // Get the loan table element
                let table = document.getElementById('loanTable');

                // Iterate over each loan and add a row to the table
                data.loans.forEach(loan => {
                    let row = table.insertRow();

                    // Add cells to the row with loan data
                    Object.values(loan).forEach(value => {
                        let cell = row.insertCell();
                        cell.textContent = value;
                    });
                });
            }


        })
        .catch(error => console.error('Error:', error));

    document.getElementById('loanForm').addEventListener('submit', function (event) {
        event.preventDefault(); // Prevent the form from submitting normally

        // Collect form data
        //const formData = new FormData(this);
        var formData = new FormData(this);
        // Make a POST request to the API
        fetch('http://localhost/system-loan/object_management.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                // Reload the page after successful response
                window.location.reload();
            })
        .catch(error => console.error('Error:', error));
    });
    document.getElementById('btnreturn').addEventListener('click', function (event) {
        event.preventDefault(); // Prevent the form from submitting normally
        var bodyData = new FormData();
        bodyData.append('id_object', objectId);
        // Make a POST request to the API
        fetch('http://localhost/system-loan/object_management.php', {
            method: 'POST',
            body: bodyData
        })
            .then(response => response.json())
            .then(data => {
                // Reload the page after successful response
                window.location.reload();
            })
        .catch(error => console.error('Error:', error));
    });
               
});
