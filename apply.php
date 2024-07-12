<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'connection.php';

if (!isset($_SESSION['email'])) {
    die("Access denied. Please log in to view your applications.");
}

$jobseeker_email = $_SESSION['email'];

$conn = new mysqli('localhost', 'root', '', 'jobportal');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$query = "SELECT j.job_id, j.jobTitle, j.jobLocation, j.jobType, c.c_name, c.c_image 
          FROM application a 
          JOIN job j ON a.job_id = j.job_id 
          JOIN company c ON j.c_email = c.c_email 
          WHERE a.jobseeker_email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $jobseeker_email);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" 
	href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Applied Jobs</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .job-box{
           display :flex;
           justify-content:center;
           margin-left:180px;
        }
    </style>
    
</head>
<body>

    <h3>Jobs You Have Applied For</h3>
            
  <div class="job-box">
        
	 <div class="job_list">
            <?php
            if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="job_details">';
				echo' <div class="company-logo"><img src="' . htmlspecialchars($row['c_image']) . '"style="width: 55px;" ></div>';
                echo '<div class="inner">';
                echo '<p>' . htmlspecialchars($row['c_name']) . '</p>';
                echo '<h3>' . htmlspecialchars($row['jobTitle']) . '</h3>';
                echo '<i class="fa-solid fa-location-dot"></i><span> ' . htmlspecialchars($row['jobLocation']) . '</span>';
                echo '<i class="fa-solid fa-business-time"></i><span> ' . htmlspecialchars($row['jobType']) . '</span>';
                echo '</div>';
                echo '<a href="jobdetails.php?job_id=' . htmlspecialchars($row['job_id']) . '"><button class="button2">View Details</button></a>';
                echo '</div>';
            }
            } else {
                echo '<p>You have not applied for any jobs yet.</p>';
            }
            ?>
		</div>
    </div>

    
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
