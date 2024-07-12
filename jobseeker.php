<?php
error_reporting(0);
try{
    require_once 'connection.php';
    $sql = "select jobseeker_email, jobseeker_name, mobno, dob, address, cv from jobseeker ";
    $result = $connection->query($sql);
    $jobseekers = [];
    if ($result->num_rows > 0) { // mysqli_num_rows()
        while ($record = $result->fetch_assoc()) { // mysqli_fetch_assoc()
            array_push($jobseekers,$record);
        }
    } else {
        echo 'Data not found';
    }
    // print_r($products);
}catch(Exception $ex){
    die('Error: ' . $ex->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>jobseeker</title>
    <link rel="stylesheet" href="admindashboard.css">
</head>
<body>
<h3>List of jobseeker</h3>
    <div class="section">
        <table>
            <tr>
                <!-- <th>P_id</th> -->
                <th>Name</th>
                <th>Contact No</th>
                <th>Dob</th>
                <th>Address</th>
                <th>Action</th>
            </tr>
            <?php foreach ($jobseekers as $key => $jobseeker) { ?>
                <tr>
                    <!-- <td><?php echo $jobseeker['p_id'] ?></td> -->
                    <td><?php echo $jobseeker['jobseeker_name'] ?></td>
                    <td><?php echo $jobseeker['mobno'] ?></td>
                    <td><?php echo $jobseeker['dob'] ?></td>
                    <td><?php echo $jobseeker['address'] ?></td>
                    <td>
                    <a onclick="return confirm('Are you sure to delete?')" href="delete.php?email=<?php echo $jobseeker['jobseeker_email'] ?>">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>
