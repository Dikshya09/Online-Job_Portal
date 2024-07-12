<?php
$connection = new mysqli('localhost', 'root', '', 'jobportal');
if ($connection->connect_error) {
    die('Connection failed: ' . $connection->connect_error);
}

// Fetch categories
$category_query = "SELECT * FROM category";
$category_result = $connection->query($category_query);

if (!$category_result) {
    die("Query failed: " . $connection->error);
}

// Fetch jobs based on category selection or all active jobs
$job_query = "SELECT j.job_id, c.c_image, c.c_name, j.jobTitle, j.jobLocation, j.jobType 
              FROM job j 
              JOIN company c ON j.c_email = c.c_email
              WHERE j.status='active'";

if (isset($_GET['category_id'])) {
    $category_id = $_GET['category_id'];
    $job_query .= " AND j.category_id = " . intval($category_id);
}

$job_result = $connection->query($job_query);

if (!$job_result) {
    die("Query failed: " . $connection->error);
}

$jobs = [];
if ($job_result->num_rows > 0) {
    while ($row = $job_result->fetch_assoc()) {
        $jobs[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Portal</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="header">
        <nav>
            <div class="logo">
                <h2 onclick="window.location.href='index.php'">Job Portal</h2>
            </div>
            <div class="search-bar">
                <form id="searchForm" action="#" method="GET">
                    <input id="searchInput" type="text" name="search" placeholder="Search...">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>
            <ul class="dropdown-container">
                <li class="dropdown">
                    <a href="#" class="dropbtn">Jobseeker</a>
                    <div class="dropdown-content">
                        <a href="jobseekerlogin.php">Login</a>
                        <a href="jobseekerregister.php">Register</a>
                    </div>
                </li>
            </ul>
            
            <ul class="dropdown-container">
                <li class="dropdown">
                    <a href="#" class="dropbtn">Employer</a>
                    <div class="dropdown-content">
                        <a href="emplogin.php">Login</a>
                        <a href="Employers registerform.php">Register</a>
                    </div>
                </li>
            </ul>
        </nav>
        <div class="banner">
            <div class="text_1">
                <h1><span>Connecting</span> dreams <br><span>with</span> opportunities</h1><br><br>
                <button class="button3" onclick="window.location.href='jobseekerregister.php'">Get Started</button>
            </div>
            <img src="Images/Untitled design (3).png" alt="Banner Image" width="auto">
        </div>
    </div>

    <h3 class="heading2">Job Categories</h3>
    <section class="banner__1">
        <div class=" banner__1__container">
            <?php while ($row = $category_result->fetch_assoc()) { ?>
                <div class="icon">
                    <a href="index.php?category_id=<?php echo $row['category_id']; ?>">
                        <img src="<?php echo $row['category_image']; ?>" alt="icon">
                    </a>
                    <span><?php echo $row['category_name']; ?></span>
                </div>
            <?php } ?>
        </div>
    </section>

    <div class="heading3">
        <h3>Jobs You May Like</h3>
    </div>
    <div class="job_boxs">
        <div class="left_image">
            <h3>Explore Opportunities!!</h3>
            <img src="Images/job_img2.png" alt="" width="350px">
        </div>
        <div class="job_list">
            <?php if (count($jobs) > 0) {
                foreach ($jobs as $job) { ?>
                    <div class="job_details">
                        <div class="company-logo">
                            <a href="#"><img src="<?php echo $job['c_image']; ?>" width="50px" alt="Company Logo"></a>
                        </div>
                        <div class="inner">
                            <p style="padding-left: 15px;"><?php echo $job['c_name']; ?></p>
                            <h3><?php echo $job['jobTitle']; ?></h3>
                            <i class="fa-solid fa-location-dot"></i><span><?php echo $job['jobLocation']; ?></span>
                            <i class="fa-solid fa-business-time"></i><span><?php echo $job['jobType']; ?></span>
                        </div>
                        <a href="indexjobdetails.php?job_id=<?php echo $job['job_id']; ?>">
                            <button class="button2">View Details</button>
                        </a>
                    </div>
                <?php }
            } else {
                echo 'No jobs found';
            } ?>
        </div>
        <div id ="searchResults"></div>
    </div>
    </div>

    <footer id="footer">
    <div class="footer-content">
        <div class="logo">
            <h2>Job Portal</h2>
        </div>
        <div class="socail-links">
            <i class="fa-brands fa-twitter"></i>
            <i class="fa-brands fa-facebook-f"></i>
            <i class="fa-brands fa-instagram"></i>
            <i class="fa-brands fa-youtube"></i>
            <i class="fa-brands fa-pinterest-p"></i>
        </div>
    </div>
    <div class="footer-bottom-content">
        <p>Designed By Job Portal teams</p>
        <div class="copyright">
            <p>&copy;Copyright <strong>Job portal</strong>.All Rights Reserved</p>
        </div>
    </div>
</footer>
    <script src="js/jquery.min.js"></script>
    <script src="js/custom.js"></script>

</body>
</html>

<?php $connection->close(); ?>
