<?php
//error_reporting(0);
require_once 'connection.php';
if(isset ($_GET['job_id'])){
try{
    $id = $_GET['job_id'];
    $sql = "delete from job where job_id='$id'";
    $connection->query($sql);
    echo "jobs deleted successfully";
    header('location:admindashboard.php?page=job');
    exit();
}catch(Exception $ex){
    die('Error: ' . $ex->getMessage());
}
}

if(isset($_GET['email'])){
try{
    $jobseeker_email = $_GET['email'];
    $sql = "delete from jobseeker where jobseeker_email='$jobseeker_email'";
    $connection->query($sql);
    echo "jobseeker deleted successfully";
    header('location:admindashboard.php?page=jobseeker');
    exit();
}catch(Exception $ex){
    die('Error: ' . $ex->getMessage());
}
}
if(isset($_GET['c_email'])){
    try{
        $c_email = $_GET['c_email'];
        $sql = "delete from company where c_email='$c_email'";
        $connection->query($sql);
        echo "employer deleted successfully";
        header('location:admindashboard.php?page=employer');
        exit();
    }catch(Exception $ex){
        die('Error: ' . $ex->getMessage());
    }
    }
?> 
