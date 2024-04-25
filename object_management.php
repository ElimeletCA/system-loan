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


// Route to get the object
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $objectId = $_GET['id'];
    
    // Retrieve the object data
    $object = getObjectById($objectId);

    // Output the object data as JSON
    header('Content-Type: application/json');
    echo json_encode($object);
}

?>