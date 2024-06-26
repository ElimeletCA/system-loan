<?php
/*
CREATE DATABASE dbloans;
USE dbloans;

CREATE TABLE object (
    uuid_object CHAR(36) PRIMARY KEY,
    name VARCHAR(255),
    description TEXT,
    object_status VARCHAR(50)
);

CREATE TABLE loan (
    id_loan INT AUTO_INCREMENT PRIMARY KEY,
    uuid_object CHAR(36),
    employee_name VARCHAR(255),
    loan_date DATE,
    estimated_loan_days INT,
    reception_date DATE,
    notes TEXT,
    FOREIGN KEY (uuid_object) REFERENCES object(uuid_object)
);*/


// Function to establish database connection
function connectToDatabase() {
    $servername = "localhost:3306"; // Change this to your MySQL server hostname if necessary
    $username = "root"; // Change this to your MySQL username
    $password = "toorwolf"; // Change this to your MySQL password
    $dbname = "dbloans"; // Change this to your MySQL database name

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

function getObjectById($idObject) {
    if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $idObject)) {
        die("Error executing query: This is not a valid UUID");
    }
    $conn = connectToDatabase();

    $exists = checkUUIDExists($conn, $idObject);
    
    if (!$exists) {
        $sql_insert = "INSERT INTO object (uuid_object, object_status) VALUES ('$idObject', 'AVAILABLE')";
        if (!$conn->query($sql_insert)) {
            die("Error executing query: " . $conn->error);
        }
    }

    // Query to select object and its related loans
    $sql = "SELECT o.*, l.* 
            FROM object AS o 
            LEFT JOIN loan AS l 
            ON o.uuid_object = l.uuid_object 
            WHERE o.uuid_object = '$idObject'";
    
    $result = $conn->query($sql);

    // Check if the query was successful
    if ($result === false) {
        die("Error executing query: " . $conn->error);
    }

    // Initialize an array to store the object data and its related loans
    $objectData = array();

    // Loop through the result set
    while ($row = $result->fetch_assoc()) {
        // Store the object data
        if (empty($objectData)) {
            // Store the object data only once
            $objectData['object'] = array(
                'object_status' => $row['object_status']
            );
        }

        // Store the loan data
        $objectData['loans'][] = array(
            'employee_name' => $row['employee_name'],
            'loan_date' => $row['loan_date'],
            'estimated_loan_days' => $row['estimated_loan_days'],
            'reception_date' => !empty($row['reception_date']) ? $row['reception_date'] : 'Waiting...',
            'notes' => $row['notes']
        );
    }

    // Close the database connection
    closeDatabaseConnection($conn);

    // Return the object data as JSON
    return $objectData;
}

function checkUUIDExists($conn, $idObject) {
    $sql = "SELECT COUNT(*) AS count FROM object WHERE uuid_object = '$idObject'";
    $result = $conn->query($sql);
    if ($result === false) {
        die("Error executing query: " . $conn->error);
    }
    $row = $result->fetch_assoc();
    return $row['count'] > 0;
}
// Function to add a new loan to the database
function addNewLoan($idObject, $employeeName, $estimatedLoanDays, $notes) {
    $conn = connectToDatabase();
    
    // Get the current date
    $loanDate = date('Y-m-d');
    
    // Prepare the SQL statement
    $sql = "INSERT INTO loan (uuid_object, employee_name, loan_date, estimated_loan_days, notes) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    // Bind parameters
    $stmt->bind_param("sssss", $idObject, $employeeName, $loanDate, $estimatedLoanDays, $notes);
    
    // Execute the statement
    $stmt->execute();
    
    // Check if the query was successful
    if ($stmt->affected_rows === -1) {
        die("Error executing query: " . $stmt->error);
    }
    
    // Close the statement and the database connection
    $stmt->close();
    closeDatabaseConnection($conn);
    
    // Return true if successful
    return true;
}



// Function to update the object status to "ONLOAN"
function updateObjectStatusToOnLoan($idObject) {
    
    if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $idObject)) {
        die("Error executing query: This is not a valid UUID");
    }

    $conn = connectToDatabase();

    // Update the object status to "ONLOAN"
    $sql = "UPDATE object SET object_status = 'ONLOAN' WHERE uuid_object = '$idObject'";
    $conn->query($sql);

    // Close the database connection
    closeDatabaseConnection($conn);
}

// Function to update the object status to "AVAILABLE"
function returnObject($idObject){
    
    if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $idObject)) {
        die("Error executing query: This is not a valid UUID");
    }
    $conn = connectToDatabase();
    // Get the current date
    $receptionDate = date('Y-m-d');

    // Update loan reception date to
    $sql = "UPDATE loan AS l
            INNER JOIN object AS o 
            ON l.uuid_object = o.uuid_object
            SET l.reception_date = '$receptionDate', o.object_status = 'AVAILABLE'
            WHERE l.reception_date IS NULL AND l.uuid_object = '$idObject'";

    $result = $conn->query($sql);
    // Check if the query was successful
    if ($result === false) {
        die("Error executing query: " . $conn->error);
    }
    // Close the database connection
    closeDatabaseConnection($conn);
    
    // Return the object data
    return $result;

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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && count($_POST) == 4) {
    // Retrieve the loan data from the request
    $idObject = $_POST['id_object'];
    $employeeName = $_POST['employee_name'];
    $estimatedLoanDays = $_POST['estimated_loan_days'];
    $notes = $_POST['notes'];
    // Add the new loan to the database
    if (addNewLoan($idObject, $employeeName, $estimatedLoanDays, $notes)) {
        // If the loan was added successfully, update the object status to "ONLOAN"
        updateObjectStatusToOnLoan($idObject);
        echo json_encode(array('success' => true));
    } else {
        echo json_encode(array('success' => false));
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && count($_POST) == 1) {
    $idObject = $_POST['id_object'];
    // Add the new loan to the database
    if (returnObject($idObject)) {
        echo json_encode(array('success' => true));
    } else {
        echo json_encode(array('success' => false));
    }
}


?>
