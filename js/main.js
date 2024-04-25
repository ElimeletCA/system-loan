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
        fetch('http://elimeletca.helioho.st/systemloan/object_management?id=' + objectId)
            .then(response => response.json())
            .then(data => {
                // Check the status field in the JSON response
                if (data.object_status === 'AVAILABLE') {
                    // Show the available div
                    document.getElementById('caseavailable').style.display = 'block';
					document.getElementById('id_object').value = objectId; 
                } else if (data.object_status === 'ONLOAN')  {
                    // Show the unavailable div
                    document.getElementById('caseonloan').style.display = 'block';
                }
            })
            .catch(error => console.error('Error:', error));


        // Event listener for form submission
		if (document.getElementById('loanForm')){
			document.getElementById('loanForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent the form from submitting normally

            // Collect form data
            const formData = new FormData(this);
            // Make a POST request to the API
            fetch('http://elimeletca.helioho.st/systemloan/object_management.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Handle the response
				alert(data);
            })
            .catch(error => console.error('Error:', error));
        });
		}
        