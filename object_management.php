<?php

// Function to establish database connection
function connectToDatabase() {
    $servername = "localhost:3306"; // Change this to your MySQL server hostname if necessary
    $username = "elimeletca_admin"; // Change this to your MySQL username
    $password = "3e?r6O39u"; // Change this to your MySQL password
    $dbname = "elimeletca_dbloans"; // Change this to your MySQL database name

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}

// Function to close database connection
function closeDatabaseConnection($conn) {
    $conn->close();
}

function getObjectById($objectId){
    $conn = connectToDatabase();
    $sql = "SELECT * FROM object WHERE id_object = $objectId";
    $result = $conn->query($sql);
    
    // Check if the query was successful
    if ($result === false) {
        die("Error executing query: " . $conn->error);
    }
    
    // Fetch the data from the result set
    $object = $result->fetch_assoc();
    
    // Close the database connection
    closeDatabaseConnection($conn);
    
    // Return the object data
    return $object;

}

// Function to add a new loan to the database
function addNewLoan($idObject, $employeeName, $estimatedLoanDays, $notes) {
    $conn = connectToDatabase();

    // Get the current date
    $loanDate = date('Y-m-d');

    // Insert the new loan into the database
    $sql = "INSERT INTO loan (id_object, employee_name, loan_date, estimated_loan_days, notes) VALUES ('$idObject', '$employeeName', '$loanDate', '$estimatedLoanDays', '$notes')";
	
    /*$result = $conn->query($sql);
    
    // Check if the query was successful
    if ($result === false) {
        die("Error executing query: " . $conn->error);
    }
    
    // Fetch the data from the result set
    $object = $result->fetch_assoc();
    
    // Close the database connection
    closeDatabaseConnection($conn);
    */
    // Return the object data
    return $sql;
}


// Function to update the object status to "ONLOAN"
function updateObjectStatusToOnLoan($idObject) {
    $conn = connectToDatabase();

    // Update the object status to "ONLOAN"
    $sql = "UPDATE object SET object_status = 'ONLOAN' WHERE id_object = '$idObject'";
    $conn->query($sql);

    // Close the database connection
    closeDatabaseConnection($conn);
}
// Route to get the object data
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $objectId = $_GET['id'];
    // Retrieve the object data
    $object = getObjectById($objectId);

    // Output the object data as JSON
    header('Content-Type: application/json');
    echo json_encode($object);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the loan data from the request
    $idObject = $_POST['id_object'];
    $employeeName = $_POST['employee_name'];
    $estimatedLoanDays = $_POST['estimated_loan_days'];
    $notes = $_POST['notes'];
	
	$object = addNewLoan($idObject, $employeeName, $estimatedLoanDays, $notes);
    // Output the object data as JSON
    header('Content-Type: application/json');
    echo json_encode($object);
}
/*// Route to add new loan:
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the loan data from the request
    $idObject = $_POST['id_object'];
    $employeeName = $_POST['employee_name'];
    $estimatedLoanDays = $_POST['estimated_loan_days'];
    $notes = $_POST['notes'];

try {
    // Add the new loan to the database
    if (addNewLoan($idObject, $employeeName, $estimatedLoanDays, $notes)) {
        // If the loan was added successfully, update the object status to "ONLOAN"
        updateObjectStatusToOnLoan($idObject);
        echo json_encode(array('success' => true));
    } else {
        echo json_encode(array('success' => false));
    }
} catch (Exception $e) {
    // Catch any exceptions thrown by addNewLoan function
    echo json_encode(array('success' => false, 'error' => $e->getMessage()));
}
}*/
/*if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // El cliente está realizando una solicitud GET
    echo "GET request";
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // El cliente está realizando una solicitud POST
    echo "POST request";
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // El cliente está realizando una solicitud PUT
    echo "PUT request";
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // El cliente está realizando una solicitud DELETE
    echo "DELETE request";
} else {
    // Método de solicitud no reconocido
    echo "Unknown request method";
}*/


?>
