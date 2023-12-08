<?php
require_once(__DIR__.'/../../web/koneksi.php');
$database = koneksi::getInstance();
$conn = $database->getConnection();
    // Use prepared statement to prevent SQL injection
    $stmt[0] = $conn->prepare("DELETE FROM detail_events");
    if (!$stmt[0]) {
        // Check for errors in the preparation of the statement
        $response = ['status' => 'error', 'message' => 'Prepare statement failed: ' . $conn->error];
        echo json_encode($response);
        exit(); // Stop script execution
    }
    // Use the id_detail from the fetched result
    if ($stmt[0]->execute()) {
        $stmt[0]->close();
        $stmt[1] = $conn->prepare("DELETE FROM events");
        if (!$stmt[1]) {
            // Check for errors in the preparation of the statement
            $response = ['status' => 'error', 'message' => 'Prepare statement failed: ' . $conn->error];
            echo json_encode($response);
            exit(); // Stop script execution
        }
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
?>
