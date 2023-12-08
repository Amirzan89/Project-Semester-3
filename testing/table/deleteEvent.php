<?php
require_once(__DIR__.'/../../web/koneksi.php');
$database = koneksi::getInstance();
$conn = $database->getConnection();
$idUser = $_POST['id_user'];
$query = mysqli_query($conn, "SELECT id_detail FROM events WHERE id_user = '$idUser'");
if ($query) {
    // Fetch the first row from the result
    $resultArray = mysqli_fetch_assoc($query);
    if (!$resultArray) {
        // If no row is found, handle the error
        $response = ['status' => 'error', 'message' => 'No matching records found'];
        echo json_encode($response);
        exit();
    }
    // Use prepared statement to prevent SQL injection
    $stmt[0] = $conn->prepare("DELETE FROM detail_events WHERE id_detail = ?");
    if (!$stmt[0]) {
        // Check for errors in the preparation of the statement
        $response = ['status' => 'error', 'message' => 'Prepare statement failed: ' . $conn->error];
        echo json_encode($response);
        exit(); // Stop script execution
    }
    // Use the id_detail from the fetched result
    $stmt[0]->bind_param('i', $resultArray['id_detail']);
    if ($stmt[0]->execute()) {
        $stmt[0]->close();
        $stmt[1] = $conn->prepare("DELETE FROM events WHERE id_user = ?");
        if (!$stmt[1]) {
            // Check for errors in the preparation of the statement
            $response = ['status' => 'error', 'message' => 'Prepare statement failed: ' . $conn->error];
            echo json_encode($response);
            exit(); // Stop script execution
        }
        $stmt[1]->bind_param('i', $idUser);
        if ($stmt[1]->execute()) {
            // If deletion is successful
            $stmt[1]->close();
            $response = ['status' => 'success', 'message' => 'Record deleted successfully'];
            echo json_encode($response);
        } else {
            // If deletion fails
            $stmt[1]->close();
            $response = ['status' => 'error', 'message' => 'Delete query failed: ' . $stmt[0]->error];
            echo json_encode($response);
        }
    } else {
        // If deletion fails
        $stmt[0]->close();
        $response = ['status' => 'error', 'message' => 'Delete query failed: ' . $stmt[0]->error];
        echo json_encode($response);
    }
} else {
    // If the query fails
    $response = ['status' => 'error', 'message' => 'Query failed: ' . mysqli_error($conn)];
    echo json_encode($response);
}
?>
