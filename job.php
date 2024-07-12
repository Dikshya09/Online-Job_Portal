<?php
error_reporting(0);
try {
    $connection = new mysqli('localhost', 'root', '', 'jobportal');
    $sql = "SELECT * FROM job ORDER BY jobTitle ASC"; 
    $result = $connection->query($sql);
    $jobs = [];

    if ($result->num_rows > 0) {
        while ($record = $result->fetch_assoc()) {
            array_push($jobs, $record);
        }
    } else {
        echo 'No job data found';
    }
} catch (Exception $ex) {
    die('Error: ' . $ex->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jobs</title>
    <link rel="stylesheet" href="admindashboard.css">
    <!-- <link rel="stylesheet" href="Job-list.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> -->
    
</head>

<body>
   
   <h3> Jobs</h3>

   <div class="list_jobs">
        <div class="section">
            <table>
                <tr>
                    <th>job-id</th>
                    <th>Job Title</th>
                    <th>Type</th>
                   
                    <th>Application Deadline</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($jobs as $key => $job) { ?>
                    <tr>
                        <td><?php echo $key + 1 ?></td>
                        <td><?php echo $job['jobTitle'] ?></td>
                        <td><?php echo $job['jobType'] ?></td>
                        <td><?php echo $job['applicationDeadline'] ?></td>
                        <td>
                            <a onclick="return confirm('Are you sure to update?')" href="update.php?id=<?php echo $job['job_id'] ?>">Update</a>
                            <a onclick="return confirm('Are you sure to delete?')" href="delete.php?id=<?php echo $job['job_id'] ?>">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
   </div>
   
</script> 
</body>
</html>

