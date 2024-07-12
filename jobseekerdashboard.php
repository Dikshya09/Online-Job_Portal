<!-- <?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['email'])) {
    header('location:jobseekerlogin.php');
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
    // Read the PDF file content from the path
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
    $category_id = intval($_GET['category_id']);
    $job_query .= " AND j.category_id = " . $category_id;
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
    <link rel="stylesheet" href="jobseekerdashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        
    </style>
</head>
<body>
    <div class="banner-main">
       <div class="banner-container">
        <div class="banner-slide">
            <div class="text_1"><h1><span>Find</span> Your </br> Team</h1></br></br>
            </div>
            <div class="banner_image"><img src="Images/gf.gif" ></div>
          </div>  
          <div class="banner-slide">
            <div class="text_1"><h1><span>Find</span> dreams </br> with <span>opportunities</span></h1></br></br>
            </div>
            <div class="banner_image"><img src="Images/gif3.gif" ></div>
          </div>
          <div class="banner-slide">
            <div class="text_1"><h1><span>Enhance </span> Your </br> Skill </h1></br></br>
            </div>
            <div class="banner_image"><img src="Images/gif4.gif" ></div>
          </div>
          <div class="banner-slide">
            <div class="text_1"><h1><span>Grab</span> Work </br> Life <span>Balance</span></h1></br></br>
            </div>
            <div class="banner_image"><img src="Images/gif5.gif"></div>
          </div>
        </div>
        <div class="rightside-banner">
            <div class="card-container">
                <div class="image-container">
                    <img src="<?php echo $j_image; ?>" alt="Profile Image" />
                    <h4><?php echo $jobseeker_name; ?></h4>
                </div>
                <hr>
                <div class="lower-container">
                    <h4><?php echo $dob; ?></h4>
                    <p><?php echo $address; ?></p>
                    <button class="button2" onclick="toggleDropdowncv()">View CV</button>
                    <div class="for_cv" id="for_cv">
                        <?php if (isset($cv_base64) && $cv_base64): ?>
                            <p><strong>CV:</strong></p>
                            <iframe src="data:application/pdf;base64,<?php echo $cv_base64; ?>" style="width:100%; height:500px;" frameborder="0"></iframe>
                        <?php else: ?>
                            <p>No CV uploaded.</p>
                        <?php endif; ?>
                    </div>
                    <ul>
                        <li><i class="fab fa-x"></i>
                        <i class="fab fa-linkedin"></i>
                        <i class="fab fa-facebook"></i>
                        <i class="fab fa-github"></i>
                    </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

     
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const slides = document.querySelectorAll('.banner-slide');
        let currentSlide = 0;

        function showSlide(index) {
            slides.forEach((slide, i) => {
                slide.style.display = i === index ? 'flex' : 'none';
            });
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % slides.length;
            showSlide(currentSlide);
        }

        showSlide(currentSlide);
        setInterval(nextSlide, 3000);
    });

    function toggleDropdowncv() {
        const overlay = document.getElementById('overlay');
        const for_cv = document.getElementById('for_cv');
        const isActive = for_cv.classList.contains('active');

        if (isActive) {
            overlay.classList.remove('active');
            for_cv.classList.remove('active');
        } else {
            overlay.classList.add('active');
            for_cv.classList.add('active');
        }
    }

    document.addEventListener('click', function(event) {
        const for_cv = document.getElementById('for_cv');
        const button = document.querySelector('.button2');
        const overlay = document.getElementById('overlay');

        if (!for_cv.contains(event.target) && !button.contains(event.target)) {
            for_cv.classList.remove('active');
            overlay.classList.remove('active');
        }
    });
    </script>

    <div id="overlay" class="overlay"></div>

    <div class="job_box1"> 
        <div class="job-box2">
            <div class="job_list1">
                <h3>Jobs You may like</h3>
                <?php if (count($jobs) > 0) {
                    foreach ($jobs as $job) { ?>
                        <div class="job_details">
                            <div class="company-logo">
                                <a href="#">
                                    <img src="<?php echo $job['c_image']; ?>" style="width: 55px;">
                                </a>
                            </div>
                            <div class="inner">
                                <p><?php echo $job['c_name']; ?></p>
                                <h3><?php echo $job['jobTitle']; ?></h3>
                                <i class="fa-solid fa-location-dot"></i><span><?php echo $job['jobLocation']; ?></span>
                                <i class="fa-solid fa-business-time"></i><span><?php echo $job['jobType']; ?></span>
                            </div>
                            <a href="jobdetails.php?job_id=<?php echo $job['job_id']; ?>"><button class="button2">View Details</button></a>
                        </div>
                    <?php }
                } else {
                    echo 'No jobs found';
                } ?>
            </div>
        </div>
        <div class="left_container">
            <h3>Jobs by categories</h3>
            <div class="category_container">
                <?php while ($row = $category_result->fetch_assoc()) { ?>
                    <div class="category_type">
                        <a href="jobseekerheader.php?category_id=<?php echo $row['category_id']; ?>">
                            <img src="<?php echo $row['category_image']; ?>" alt="icon">
                        </a>
                        <span><?php echo $row['category_name']; ?></span>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</body>
</html>
 -->
 <?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['email'])) {
    header('location:jobseekerlogin.php');
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
    // Read the PDF file content from the path
    if (file_exists($cv_path)) {
        $cv_content = file_get_contents($cv_path);
        $cv_base64 = base64_encode($cv_content);
    } else {
        $cv_base64 = null;
    }
} else {
    echo "<script>alert('No jobseeker found with the given email.'); window.location.href='jobseekerlogin.php';</script>";
}

$stmt->close();
$mysqli->close();
?>
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
    $category_id = intval($_GET['category_id']);
    $job_query .= " AND j.category_id = " . $category_id;
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
    <link rel="stylesheet" href="jobseekerdashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7); /* Semi-transparent black background */
    display: none; /* Initially hidden */
    z-index: 999; /* Ensure it's above other content */
}

.overlay.active {
    display: block; /* Show overlay when active */
}

    </style>
</head>
<body>
    <div class="banner-main">
       <div class="banner-container">
        <div class="banner-slide">
            <div class="text_1"><h1><span>Find</span> Your </br> Team</h1></br></br>
            </div>
            <div class="banner_image"><img src="Images/gf.gif" ></div>
          </div>  
          <div class="banner-slide">
            <div class="text_1"><h1><span>Find</span> dreams </br> with <span>opportunities</span></h1></br></br>
            </div>
            <div class="banner_image"><img src="Images/gif3.gif" ></div>
          </div>
          <div class="banner-slide">
            <div class="text_1"><h1><span>Enhance </span> Your </br> Skill </h1></br></br>
            </div>
            <div class="banner_image"><img src="Images/gif4.gif" ></div>
          </div>
          <div class="banner-slide">
            <div class="text_1"><h1><span>Grab</span> Work </br> Life <span>Balance</span></h1></br></br>
            </div>
            <div class="banner_image"><img src="Images/gif5.gif"></div>
          </div>
        </div>
        <div class="rightside-banner">
            <div class="card-container">
                <div class="image-container">
                    <img src="<?php echo $j_image; ?>" alt="Profile Image" />
                    <h4><?php echo $jobseeker_name; ?></h4>
                </div>
                <hr>
                <div class="lower-container">
                    <p><?php echo $address; ?></p>
                    <button class="button2" onclick="toggleDropdowncv()">View CV</button>
                    <div class="for_cv" id="for_cv">
                        <?php if (isset($cv_base64) && $cv_base64): ?>
                            <p><strong>CV:</strong></p>
                            <iframe src="data:application/pdf;base64,<?php echo $cv_base64; ?>" style="width:100%; height:500px;" frameborder="0"></iframe>
                        <?php else: ?>
                            <p>No CV uploaded.</p>
                        <?php endif; ?>
                    </div>
                    <ul>
                        <li><i class="fab fa-x"></i>
                        <i class="fab fa-linkedin"></i>
                        <i class="fab fa-facebook"></i>
                        <i class="fab fa-github"></i>
                    </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Javascript for banner -->
    <script>

document.addEventListener('DOMContentLoaded', function() {
    const slides = document.querySelectorAll('.banner-slide');
    let currentSlide = 0;

    function showSlide(index) {
        slides.forEach((slide, i) => {
            slide.style.display = i === index ? 'flex' : 'none';
        });
    }

    function nextSlide() {
        currentSlide = (currentSlide + 1) % slides.length;
        showSlide(currentSlide);
    }

    showSlide(currentSlide);
    setInterval(nextSlide, 3000);
});

function toggleDropdowncv() {
    const for_cv = document.getElementById('for_cv');
    const overlay = document.getElementById('overlay');
    
    for_cv.classList.toggle('active');
    overlay.classList.toggle('active'); // Toggle overlay visibility
}

document.addEventListener('click', function(event) {
    const for_cv = document.getElementById('for_cv');
    const button = document.querySelector('.button2');
    const overlay = document.getElementById('overlay');

    if (!for_cv.contains(event.target) && !button.contains(event.target)) {
        for_cv.classList.remove('active');
        overlay.classList.remove('active'); // Remove overlay when clicking outside CV
    }
});


    </script>

    <div id="overlay" class="overlay"></div>

    <div class="job_box1" id="job_box1"> 
        <div class="job-box2">
        <div class="job_list1">
    <h3>Jobs You May Like</h3>
    <div id="jobResults">
        <?php if (count($jobs) > 0) {
            foreach ($jobs as $job) { ?>
                <div class="job_details">
                    <div class="company-logo">
                        <a href="#">
                            <img src="<?php echo $job['c_image']; ?>" style="width: 55px;">
                        </a>
                    </div>
                    <div class="inner">
                        <p><?php echo $job['c_name']; ?></p>
                        <h3><?php echo $job['jobTitle']; ?></h3>
                        <i class="fa-solid fa-location-dot"></i><span><?php echo $job['jobLocation']; ?></span>
                        <i class="fa-solid fa-business-time"></i><span><?php echo $job['jobType']; ?></span>
                    </div>
                    <a href="jobdetails.php?job_id=<?php echo $job['job_id']; ?>"><button class="button2">View Details</button></a>
                </div>
            <?php }
        } else {
            echo 'No jobs found';
        } ?>
    </div>
    <div id="searchResults"></div>
    </div>
    </div>
        <div class="left_container">
            <h3>Jobs by categories</h3>
            <div class="category_container">
                <?php while ($row = $category_result->fetch_assoc()) { ?>
                    <div class="category_type">
                        <a href="jobseekerheader.php?category_id=<?php echo $row['category_id']; ?>">
                            <img src="<?php echo $row['category_image']; ?>" alt="icon">
                        </a>
                        <span><?php echo $row['category_name']; ?></span>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</body>
</html>

