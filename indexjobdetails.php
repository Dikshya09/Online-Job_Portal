<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Details</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="jobdetails.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .popup {
            display: none;
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            border: 1px solid black;
            z-index: 1000;
        }
        .popup.visible {
            display: block;
        }
    </style>
</head>
<body>
<?php
//  $fetch_job = null; // Initialize the variable
if (isset($_GET['job_id'])) {
    $job_id = $_GET['job_id'];
    
    $connection = new mysqli('localhost', 'root', '', 'jobportal');
    
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }
    
    $sql = "SELECT 
                job.job_id, 
                job.jobTitle, 
                job.jobType,
                job.qualification, 
                job.experienceLevel, 
                job.jobLocation, 
                job.applicationDeadline, 
                job.salaryRange, 
                job.jobDescription, 
                job.jobrequirement, 
                company.industry,
                company.c_image,
                company.c_name, 
                company.c_description
            FROM 
                job
            JOIN 
                company ON job.c_email = company.c_email
            WHERE 
                job.job_id = ?";
    
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $job_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $fetch_job = $result->fetch_assoc();
    }
    
    $stmt->close();
    $connection->close();
}
?>

<div class="header">
    <nav>
        <div class="logo">
            <h2 onclick="window.location.href='index.php'">Job Portal</h2>
        </div>
        <div class="search-bar">
            <form id="searchForm" action="#" method="GET">
                <input type="text" id="searchInput" name="search" placeholder="Search...">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
        </div>
        <div id="searchResults"></div>
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
</div>

<div class="company-name">
<h3><?php echo $fetch_job['jobTitle']; ?> - <?php echo $fetch_job['c_name'];?></h3>
</div>

<div class="jobdetails_container">
    <?php if ($fetch_job): ?>
    <div class="left-column">
        <div class="jobdetails_child1">
            <div class="row">
                <div class="pp-left">
                <div class="company-logo">
                            <img src="<?php echo $fetch_job['c_image'];?>" width="50px">
                        </div>
                    <div class="company-info">
                        <h4><?php echo htmlspecialchars($fetch_job['jobTitle']); ?></h4>
                        <p><?php echo htmlspecialchars($fetch_job['c_name']); ?></p>
                        <p><strong>Location:</strong> <?php echo htmlspecialchars($fetch_job['jobLocation']); ?></p>
                    </div>
                </div>
            </div>    
        </div>
        <div class="jobdetails_child2">
            <div class="section">
                <div class="section-content">
                    <h2>Job Specification</h2>
                    <div class="section__container banner__1__container">
                        <div class="icon">
                            <a href="#"><img src="Images/salary.png" alt="icon"></a>
                            <span>Offered Salary:</span>
                            <p><?php echo htmlspecialchars($fetch_job['salaryRange']); ?></p>
                        </div>
                        <div class="icon">
                            <a href="#"><img src="Images/hourglass.png" alt="icon"></a>
                            <span>Job Type:</span>                            
                            <p><?php echo htmlspecialchars($fetch_job['jobType']); ?></p>

                        </div>
                        <div class="icon">
                            <a href="#"><img src="Images/experience.png" alt="icon"></a>
                            <span>Experience:</span>
                            <p><?php echo htmlspecialchars($fetch_job['experienceLevel']); ?></p>
                        </div>
                    </div>
                    <div class="section__container banner__1__container">
                        <div class="icon">
                            <a href="#"><img src="Images/calender.png" alt="icon"></a>
                            <span>Valid Till:</span>
                            <p><?php echo htmlspecialchars($fetch_job['applicationDeadline']); ?></p>
                        </div>
                        <div class="icon">
                            <a href="#"><img src="Images/industry.png" alt="icon"></a>
                            <span>Industry:</span>
                            <p><?php echo htmlspecialchars($fetch_job['industry']); ?></p>
                        </div>
                        <div class="icon">
                            <a href="#"><img src="Images/qualification.png" alt="icon"></a>
                            <span>Qualification:</span>
                            <p><?php echo htmlspecialchars($fetch_job['qualification']); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <div class="jobdetails_child3">
        <div class="section">
            <h2>Job Description</h2>
            <div class="section-content">
                <p><?php echo htmlspecialchars($fetch_job['jobDescription']); ?></p>
            </div>
        </div>
        <div class="section">
            <h2>Requirements</h2>
            <div class="section-content">
                <ul>
                    <?php 
                    if (is_string($fetch_job['jobrequirement'])) {
                        $requirements = explode("\n", $fetch_job['jobrequirement']);
                        foreach($requirements as $requirement) {
                            echo "<li>" . htmlspecialchars($requirement, ENT_QUOTES, 'UTF-8') . "</li>";
                        }
                    }
                    ?>
                </ul>
            </div>
        </div>
       
    </div>
    <?php else: ?>
    <p>No job details found.</p>
    <?php endif; ?>
    </div>

    <div class="right-column">
        <div class="jobdetails_child4_sticky_wrapper">
            <div class="jobdetails_child4">
                <h2>About Company</h2>
                <p><?php echo $fetch_job['c_description']; ?></p>
            </div>
        </div>
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
<script>
$(document).ready(function() {
    $('#searchForm').submit(function(event) {
        event.preventDefault(); // Prevent default form submission behavior

        // Get the search query
        var query = $('#searchInput').val();

        // Perform AJAX request to fetch search results
        $.get('/search', { q: query })
            .done(function(response) {
                // Update the search results container with the response
                $('#searchResults').html(response);
            })
            .fail(function(xhr, status, error) {
                // Error handling
                console.error('Request failed with status:', status);
            });
    });
});
</script>
</body>
</html>
