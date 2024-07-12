<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'connection.php';

$job_id = isset($_GET['job_id']) ? $_GET['job_id'] : null;
$jobseeker_email = isset($_SESSION['email']) ? $_SESSION['email'] : null;

$conn = new mysqli('localhost', 'root', '', 'jobportal');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$already_applied = false;
if ($job_id && $jobseeker_email) {
    $query = "SELECT * FROM application WHERE job_id = ? AND jobseeker_email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $job_id, $jobseeker_email);
    $stmt->execute();
    $result = $stmt->get_result();

    $already_applied = ($result->num_rows > 0);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['apply'])) {
    if (!$already_applied && $jobseeker_email) {
        $insert_query = "INSERT INTO application (job_id, jobseeker_email) VALUES (?, ?)";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param("is", $job_id, $jobseeker_email);
        $insert_stmt->execute();
        header("Location: jobseekerheader.php?page=apply");
        exit();
    } elseif (!$jobseeker_email) {
        echo '<script>alert("Jobseeker email is not set. Please log in.");</script>';
    } else {
        echo '<script>
                alert("You have already applied for this job!");
                document.getElementById("applyButton").setAttribute("disabled", "disabled");
              </script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Details</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="jobdetails.css">
    <link rel="stylesheet" href="jobseekerdashboard.css">

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
    
        button:disabled {
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['email'])) {
        die("Access denied.");
    }

    if (isset($_GET['job_id'])) {
        $job_id = $_GET['job_id'];
        $connection = new mysqli('localhost', 'root', '', 'jobportal');
        
        if ($connection->connect_error) {
            die("Connection failed: " . $connection->connect_error);
        }
        
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
                    company.c_description, 
                    company.c_image, 
                    jobseeker.j_image, 
                    jobseeker.jobseeker_name
                FROM 
                    job
                JOIN 
                    company ON job.c_email = company.c_email
                LEFT JOIN 
                    application ON job.job_id = application.job_id AND application.jobseeker_email = ?
                LEFT JOIN 
                    jobseeker ON application.jobseeker_email = jobseeker.jobseeker_email
                WHERE 
                    job.job_id = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("si", $jobseeker_email, $job_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $fetch_job = $result->fetch_assoc();
    ?>
   <?php include 'header.php';?>
    
    <div class="company-name">
        <h3><?php echo $fetch_job['jobTitle']; ?> - <?php echo $fetch_job['c_name'];?></h3>
    </div>
    <div class="jobdetails_container">
        <div class="left-column">
            <div class="jobdetails_child1">
                <div class="row">
                    <div class="pp-left">
                        <div class="company-logo">
                            <img src="<?php echo $fetch_job['c_image'];?>" width="50px">
                        </div>
                        <div class="company-info">
                            <h4><?php echo $fetch_job['jobTitle']; ?></h4>
                            <p><?php echo $fetch_job['c_name'];?></p>
                            <p><strong>Location:</strong> <?php echo $fetch_job['jobLocation']; ?></p>
                        </div>
                    </div>
                    <div class="pp-right">
                        <div class="buttons">
                            <form method="post">
                                <button id="applyButton" type="submit" name="apply" <?php if ($already_applied) echo 'disabled'; ?>>
                                    <?php if ($already_applied) echo 'Applied'; else echo 'Apply'; ?>
                                </button>
                            </form>
                            <div class="popup" id="appliedPopup">
                                <p>You have already applied for this job!</p>
                                <button onclick="document.getElementById('appliedPopup').classList.remove('visible')">Close</button>
                            </div>
                            <script>
                                <?php if ($already_applied && $_SERVER['REQUEST_METHOD'] == 'POST'): ?>
                                    document.getElementById('appliedPopup').classList.add('visible');
                                <?php endif; ?>
                            </script>
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
                                <p><?php echo $fetch_job['salaryRange']; ?></p>
                            </div>
                            <div class="icon">
                                <a href="#"><img src="Images/hourglass.png" alt="icon"></a>
                                <span>Job Type:</span>
                                <p><?php echo $fetch_job['jobType']; ?></p>
                            </div>
                            <div class="icon">
                                <a href="#"><img src="Images/experience.png" alt="icon"></a>
                                <span>Experience:</span>
                                <p><?php echo $fetch_job['experienceLevel']; ?></p>
                            </div>
                        </div>
                        <div class="section__container banner__1__container">
                            <div class="icon">
                                <a href="#"><img src="Images/calender.png" alt="icon"></a>
                                <span>Valid Till:</span>
                                <p><?php echo $fetch_job['applicationDeadline']; ?></p>
                            </div>
                            <div class="icon">
                                <a href="#"><img src="Images/industry.png" alt="icon"></a>
                                <span>Industry:</span>
                                <p><?php echo $fetch_job['industry']; ?></p>
                            </div>
                            <div class="icon">
                                <a href="#"><img src="Images/qualification.png" alt="icon"></a>
                                <span>Qualification:</span>
                                <p><?php echo $fetch_job['qualification']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <div class="jobdetails_child3">
            <div class="section">
                <h2>Job Description</h2>
                <div class="section-content">
                    <p><?php echo $fetch_job['jobDescription']; ?></p>
                </div>
            </div>
            <div class="section">
                <h2>Requirements</h2>
                <div class="section-content">
                <?php
?>
<ul id="jobrequirement">
    <?php
    $requirements = explode(", ", $fetch_job['jobrequirement']);
    foreach ($requirements as $requirement) {
        echo '<li>' . htmlspecialchars(trim($requirement)) . '</li>';
    }
    ?>
</ul>

                </div>
            </div>
            
        <?php
        } else {
            echo 'No jobs found';
        }
    }
    ?>
        </div>
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
                <p>&copy; Copyright <strong>Job portal</strong>. All Rights Reserved</p>
            </div>
        </div>
    </footer>
</body>
</html>