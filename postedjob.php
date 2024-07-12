<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['email'])) {
    header('location:c_login.php');
}

$c_email = $_SESSION['email'];
$mysqli = new mysqli("localhost", "root", "", "jobportal");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Fetch the jobs posted by the company
$stmt = $mysqli->prepare("SELECT job_id, jobTitle,  jobLocation, salaryRange, applicationDeadline, status FROM job WHERE c_email = ?");
$stmt->bind_param("s", $c_email);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Posted Jobs</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="job-list.css">
    
</head>
<body>
<div class="section">
    <h2>Jobs Posted by You</h2>
    <table>
        <thead>
            <tr>
                
                <th>Job Title</th>
                <th>Job Location</th>
                <th>Salary</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                   
                    echo "<td>" . $row['jobTitle'] . "</td>";
                    echo "<td>" . $row['jobLocation'] . "</td>";
                    echo "<td>" . $row['salaryRange'] . "</td>";
                    echo "<td>" . $row['status'] . "</td>";
                    echo "<td><a href='update_job.php?id=" . $row['job_id'] . "'>Update</a> | ";
                    echo "<a href='delete_job.php?id=" . $row['job_id'] . "' onclick='return confirm(\"Are you sure you want to delete this job?\")'>Delete</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No jobs posted yet.</td></tr>";
            }
            $stmt->close();
            $mysqli->close();
            ?>
        </tbody>
    </table>
    </div>
</body>
</html>