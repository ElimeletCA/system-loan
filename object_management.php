<?php

// Function to establish database connection
function connectToDatabase() {
    $servername = "localhost"; // Change this to your MySQL server hostname if necessary
    $username = "elimeletca_admin1806"; // Change this to your MySQL username
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
    $sql = "SELECT * FROM file WHERE id_file = $objectId";
    $result = $conn->query($sql);
    return $result;

}
// Function to get the loan history of a file
function getLoanHistory($fileId) {
    $conn = connectToDatabase();

    $sql = "SELECT * FROM loan WHERE id_file = $fileId";
    $result = $conn->query($sql);

    $loanHistory = array();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $loanHistory[] = $row;
        }
    }

    closeDatabaseConnection($conn);

    return $loanHistory;
}

// Function to check the current status of the file (available or not)
function checkFileStatus($fileId) {
    $conn = connectToDatabase();

    $sql = "SELECT file_status FROM file WHERE id_file = $fileId";
    $result = $conn->query($sql);

    $fileStatus = "";
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $fileStatus = $row["file_status"];
    }

    closeDatabaseConnection($conn);

    return $fileStatus;
}

// Route to get the loan history of a file
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {

    $fileId = $_GET['fileId'];
    echo getObjectById($fileId);

    /*header('Content-Type: application/json');
    echo json_encode($loanHistory);*/
}

// Route to check the current status of the file
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id']) && isset($_GET['currentStatus'])) {
    /*$fileId = $_GET['fileId'];
    $fileStatus = checkFileStatus($fileId);
    header('Content-Type: application/json');
    echo json_encode(array('fileStatus' => $fileStatus));
    */
}

?>
