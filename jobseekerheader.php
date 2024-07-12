
<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['email'])) {
    header('location:jobseekerlogin.php');
    exit();
}

$jobseeker_email = $_SESSION['email'];
$mysqli = new mysqli("localhost", "root", "", "jobportal");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$stmt = $mysqli->prepare("SELECT j_image, jobseeker_name, mobno, address, cv, dob, description FROM jobseeker WHERE jobseeker_email = ?");
$stmt->bind_param("s", $jobseeker_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $j_image = $row['j_image'];
    $jobseeker_name = $row['jobseeker_name'];
    $mobno = $row['mobno'];
    $address = $row['address'];
    $cv_path = $row['cv'];
    $dob = $row['dob'];
    $description = $row['description'];
    if (file_exists($cv_path)) {
        $cv_content = file_get_contents($cv_path);
        $cv_base64 = base64_encode($cv_content);
    } else {
        $cv_base64 = null;
    }
} else {
    die("No jobseeker found with the given email.");
}

$stmt->close();
$mysqli->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Portal</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="jobseekerdashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Your existing CSS styles */
        h2 {
            color: #338573;
            margin-bottom: 20px;
        }
        .header nav {
            overflow: hidden;
            align-items: center;
            display: flex;
            position: relative;
            justify-content: space-between;
            height: 80px;
            padding: 30px;
            position: sticky;
            top: 0;
            z-index: 3;
            background-color: #ffffff; 
            border-bottom: 1px solid #338573;
        }
        nav a {
            display: block;
            color: #338573;
            padding: 10px 12px;
            text-decoration: none;
            font-weight: normal;
            font-size: 16px;
            position: relative;
            padding-bottom: 5px;
        }
        nav a::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 4px;
            background-color: #cccccc;
            left: 0;
            bottom: -2px;
            transform: scaleX(0);
            transition: transform 0.3s ease;
            transform-origin: bottom right;
        }
        nav a:hover::after{
            transform: scaleX(1);
            transform-origin: bottom left;
            background-color: #cccccc;
        }
        nav a.active::after {
            transform: scaleX(1);
            transform-origin: bottom left;
            background-color: #338573;
        }
        .nav-links {
            flex: 1;
            display: flex;
            justify-content: flex-end;
        }
        .nav-links ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            display: flex;
            margin-bottom:30px;
        }
        .nav-links li {
            margin-right: 20px;
        }
        .nav-links a {
            font-weight: bold;
            font-size: 18px;
        }
        .nav-links a:last-child {
            margin-right: 0;
        }
        .notification-icon {
            cursor: pointer;
        }
        .profile {
            cursor: pointer;
        }
        .logo {
            margin-right: 20px;
            padding:20px;
        }
        .dropdown {
            position: absolute;
            top: 73px;
            right: 10px;
            background-color: #fdfdfd;
            width: 150px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            padding: 15px;
            border-radius: 10px;
            z-index: 1000; /* Ensure this is higher than other elements */
            display: none; /* Initial state is hidden */
        }

        .dropdown::before{
            content: '';
            position: absolute;
            top: -18px;
            right: 28px;
            border: 10px solid;
            border-color: transparent transparent #f4f2f2 transparent;
        }
        .dropdown .user-info{
            display: flex;
            align-items: center;
        }
        .dropdown .user-info img{
            width: 30px;
            height: 30px;
            object-fit: cover;
            border-radius: 50%;
            cursor: pointer;
            border: 1.5px solid #338573;
            margin-right: 8px;
        }
        .dropdown .dropdownlist img{
            width: 15px;
            height: 15px;
            background: #e5e5e5;
            border-radius: 35%;
            padding: 5px;
            margin-right: 15px;
        }
        .dropdown hr{
            border: 0;
            width: 100%;
            height: 1px;
            margin: 10px 0 5px;
            background: #ccc;
        }
        .dropdown .dropdownlist .sub-menu-links{
            display: flex;
            align-items: center;
            color: #525252;
            margin: 12px 0;
        }
        .dropdown .dropdownlist .sub-menu-links p{
            width: 100%;
        }
        .dropdownlist :hover p{
            font-weight: 600;
            transition: transform 0.5s;
            transform: translateY(-0.4px);
        }
    </style>
</head>
<body>
    <?php
    $activePage = isset($_GET['page']) ? $_GET['page'] : 'jobseekerdashboard';
    ?>
    <div class="header">
        <nav>
            <div class="logo">
                <h2 onclick="window.location.href='jobseekerdashboard.php'">Job Portal</h2>
            </div>
            <div class="nav-links">
                <ul>
                    <li><a href="?page=jobseekerdashboard" <?php echo ($activePage === 'jobseekerdashboard') ? 'class="active"' : ''; ?>>Home</a></li>
                    <li><a href="?page=apply" <?php echo ($activePage === 'apply') ? 'class="active"' : ''; ?>>Applied</a></li>
                </ul>
            </div>
            <div class="search-bar">
                <form id="searchForm" action="#" method="GET">
                    <input id="searchInput" type="text" name="search" placeholder="Search...">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>

            <div class="notification-icon" onclick="window.location.href='notification.php'">
                <i class="fas fa-bell"></i>
            </div>
            <div class="profile" onclick="toggleDropdown()">
                <img src="<?php echo $j_image; ?>" alt="Profile Image" width=25px />
            </div>
        </nav>
    </div>
    <div class="dropdown" id="dropdownMenu">
        <div class="user-info">
            <img src="<?php echo $j_image; ?>" alt="Profile Image" width=25px />
            <h4><?php echo $jobseeker_name; ?></h4>
        </div>
        <hr>
        <div class="dropdownlist">
            <a href="jobseekerlogout.php" class="sub-menu-links"><img src="Images/exit.png"><p>Logout</p></a>
            <a href="Setting.php" class="sub-menu-links"><img src="Images/settings.png"><p>Settings</p></a>
            <a href="profile.php" class="sub-menu-links"><img src="Images/edit-info.png"><p>Profile</p></a>
        </div>
    </div>
    <div class="main-content">
        <?php
        switch ($activePage) {
            case 'jobseekerdashboard':
                include 'jobseekerdashboard.php';
                break;
            case 'apply':
                include 'apply.php';
                break;
            default:
                include 'jobseekerdashboard.php';
                break;
        }
        ?>
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
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const dropdownMenu = document.getElementById('dropdownMenu');

        window.toggleDropdown = function() {
            dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
            console.log("Toggle dropdown"); // Add this line to check if the function is called
        }

        document.addEventListener('click', function(event) {
            const profileIcon = document.querySelector('.profile');
            if (!profileIcon.contains(event.target) && !dropdownMenu.contains(event.target)) {
                dropdownMenu.style.display = 'none';
            }
        });
    });
    </script>

<script src="js/jquery.min.js"></script>
<script src="js/dashboardcustom.js"></script>
</body>
</html>
