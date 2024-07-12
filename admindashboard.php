<?php
session_start();
if(!isset($_SESSION['username'])){
    header('location:adminlogin.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>admin dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="admindashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    
    <?php
    $activePage = isset($_GET['page']) ? $_GET['page'] : 'jobseeker';
    ?>
    <div>
        <div class="container">
            <div class="logo"> 
                <h2>Job Portal</h2>
            </div>
            <br>
            <ol>
                <li><a href="?page=jobseeker" <?php echo ($activePage === 'jobseeker') ? 'class="active"' : ''; ?>><i class="fas fa-users"></i>&nbsp;Jobseeker</a></li>
                <li><a href="?page=employer" <?php echo ($activePage === 'employer') ? 'class="active"' : ''; ?>><i class="fas fa-building"></i>&nbsp;Company</a></li>
                <li><a href="?page=job" <?php echo ($activePage === 'job') ? 'class="active"' : ''; ?>><i class="fa-solid fa-briefcase"></i>&nbsp;Jobs</a></li>
                <li><a href="?page=Application" <?php echo ($activePage === 'Application') ? 'class="active"' : ''; ?>><i class="fa-solid fa-file"></i>&nbsp;Application</a></li>
                <li><a href="?page=admin_category"<?php echo ($activePage === 'admin_category') ? 'class="active"' : ''; ?>><i class="fa-solid fa-list"></i>&nbsp;Category</a></li>
                <li><a href="adminlogout.php"><i class="fa-solid fa-right-from-bracket"></i>&nbsp;Log Out</a></li>
            </ol>
        </div>
        <?php
        // Include the content based on the active page
        switch ($activePage) {
            case 'jobseeker':
            include 'jobseeker.php';
            break;
            case 'employer':
            include 'employer.php';
            break;
            case 'job':
            include 'job.php';
            break;
            case 'Application':
                include 'Application.php';
                break;
                case 'admin_category':
                include 'admin_category.php';
                break;
            default:
            // Handle other cases or set a default behavior
            include 'jobseeker.php';
            break;
        }
        ?>
        <!-- <footer id="footer">
        <div class="footer-bottom-content">
          <p>Designed By Job Portal teams</p>
          <div class="copyright">
              <p>&copy;Copyright <strong>Job portal</strong>.All Rights Reserved</p>
          </div>
        </div>
        </footer>  -->
</body>
</html>