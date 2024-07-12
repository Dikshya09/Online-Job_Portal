<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['email'])) {
    header('location:index.php');
    exit();
}

if (!isset($_GET['email'])) {
    echo "No email provided.";
    exit();
}

$email = $_GET['email'];

// Fetch jobseeker data from the database
$conn = new mysqli("localhost", "root", "", "jobportal");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM jobseeker WHERE jobseeker_email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $jobseeker = $result->fetch_assoc();
    $cv_path = $jobseeker['cv'];
    // Define the CV path if cv_filename is set
    if (file_exists($cv_path)) {
        $cv_content = file_get_contents($cv_path);
        $cv_base64 = base64_encode($cv_content);
    } else {
        $cv_base64 = null;
    }
} 
else {
    echo "No user found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Profile Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"/>
    <link rel="stylesheet" href="profile.css">
    <link rel="stylesheet" href="style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            text-decoration: none;
            scroll-behavior: smooth;
            font-family: poppins;
        }
        .popup-form {
            width:500px;
            height:300px;
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            border: 1px solid #338573;
            border-radius: 5px;
        }
        .popup-form.active {
            display: block;
        }
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }
        .overlay.active {
            display: block;
        }
        .edit-icon {
            cursor: pointer;
        }

        /* .flicker {
            animation: flicker 0.2s 2;
        }
        @keyframes flicker {
            0%, 100% { opacity: 1; }
            50% { opacity: 0; }
        } */
    </style>
</head>
<body>
<?php include 'header4employer.php';?>


<div class="cols__container">
    <div class="img__container1">
        <div class="img__container">
        <form class="form" id="form" action="profile.php" enctype="multipart/form-data" method="post">
            <div class="upload">
                <?php
                if (isset($jobseeker['j_image']) && !empty($jobseeker['j_image'])) {
                    echo '<img src="' . htmlspecialchars($jobseeker['j_image']) . '" id="image">';
                } else {
                    echo '<img src="Images/profile.png" id="image">';
                }
                ?>
                
            </div>
        </form>
        </div>
    
         <div class="name">
            <div class="form-group">
                        <span id="jobseeker_name"><h2><?php echo htmlspecialchars($jobseeker['jobseeker_name']); ?></h2></span>
                        
                        <span id="address"><?php echo htmlspecialchars($jobseeker['address']); ?></span>
            </div>
        </div>
    </div>
    <div class="buttons_2">
            
            <div class="form-group">
           <div class="button"> <button class="edit-icon" onclick="toggleDropdowncv()">View CV</button>
           <div class="for_cv popup-form" id="for_cv">
                        <?php if (isset($cv_base64) && $cv_base64): ?>
                            <p><strong>CV:</strong></p>
                            <iframe src="data:application/pdf;base64,<?php echo $cv_base64; ?>" style="width:100%; height:500px;" frameborder="0"></iframe>
                        <?php else: ?>
                            <p>No CV uploaded.</p>
                        <?php endif; ?>
                    </div></div>
        </div>
        <div class="button">
    </div>
    </div>

<div class="overlay" id="overlay"></div>

<hr>
<div class="profile__container">
<div class="form-group">

    <div class="description">
    <span id="description"> <?php echo nl2br($jobseeker['description']); ?></span>
</div>
</div>
</div>
</div>
<script>
function toggleDropdowncv() {
        const for_cv = document.getElementById('for_cv');
        const overlay = document.getElementById('overlay');
        for_cv.classList.toggle('active');
        overlay.classList.toggle('active');
    }

    document.addEventListener('click', function(event) {
        const for_cv = document.getElementById('for_cv');
        const overlay = document.getElementById('overlay');

        if (!for_cv.contains(event.target) && !event.target.classList.contains('edit-icon')) {
            for_cv.classList.remove('active');
            overlay.classList.remove('active');
        }
    });
</script>

</body>
</html>
