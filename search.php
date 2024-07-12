<?php
$connection = new mysqli('localhost', 'root', '', 'jobportal');

// Check for connection error
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Get the search query from the AJAX request
$query = isset($_GET['q']) ? $connection->real_escape_string($_GET['q']) : '';

$sql = "SELECT 
            job.job_id, 
            job.jobTitle, 
            job.qualification, 
            job.experienceLevel, 
            job.jobType,
            job.jobLocation, 
            job.applicationDeadline, 
            job.salaryRange, 
            job.jobDescription, 
            job.jobrequirement, 
            company.industry, 
            company.c_name, 
            company.c_image,
            company.c_description,
            category.category_name
            FROM 
            job
            JOIN 
            company ON job.c_email = company.c_email
            JOIN 
            category ON job.category_id = category.category_id
            WHERE  status='active' AND ( jobTitle LIKE '%$query%' OR jobLocation LIKE '%$query%' OR company.c_name LIKE '%$query%' OR category.category_name LIKE '%$query%')";
$result = $connection->query($sql);

// Check if there are any results
if ($result->num_rows > 0) {
    while ($fetch_job = $result->fetch_assoc()) {
        ?>
        <div class="job_details">
        <div class="company-logo">
                        <a href="#">
                            <img src="<?php echo $fetch_job['c_image']; ?>" style="width: 55px;">
                        </a>
                    </div>
            <div class="inner">
            <p style="padding-left: 15px;"><?php echo $fetch_job['c_name']; ?></p>
                <!-- <p><?php echo htmlspecialchars($fetch_job['jobCategory']); ?></p> -->
                <h3><?php echo htmlspecialchars($fetch_job['jobTitle']); ?></h3>
                <i class="fa-solid fa-location-dot"></i><span><?php echo htmlspecialchars($fetch_job['jobLocation']); ?></span>
                <i class="fa-solid fa-business-time"></i><span><?php echo htmlspecialchars($fetch_job['jobType']); ?></span>
            </div>
            <a href="indexjobdetails.php?job_id=<?php echo htmlspecialchars($fetch_job['job_id']); ?>"><button class="button2">View Details</button></a>
        </div>
        <?php
    }
} else {
    echo 'No jobs found';
}

// Close the connection
$connection->close();
?>