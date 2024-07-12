<?php
include('connection.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(!isset($_SESSION['username'])){
    header('location:adminlogin.php');
}

if (isset($_GET['id'])) {
    $category_id = $_GET['id'];

    $conn = new mysqli("localhost", "root", "", "jobportal");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "DELETE FROM category WHERE category_id = $category_id";
    if (mysqli_query($conn, $sql)) {
        header('Location: admindashboard.php?page=admin_category');
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }

    $conn->close();
} else {
    header('Location: index.php');
}
?>
