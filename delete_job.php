<?php
require_once 'connection.php';
if(isset ($_GET['id'])){
try{
    $id = $_GET['id'];
    $sql = "delete from job where job_id='$id'";
    $connection->query($sql);
    echo "jobs deleted successfully";
    header('location:empdashboard.php?page=postedjob');
    exit();
}catch(Exception $ex){
    die('Error: ' . $ex->getMessage());
}
}
?>