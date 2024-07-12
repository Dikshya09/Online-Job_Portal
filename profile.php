<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['email'])) {
    header('location:adminlogin.php');
    exit();
}

$email = $_SESSION['email'];

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
} else {
    echo "No user found.";
    exit();
}

$errors = [];
$uploadDir = 'uploads/';
$allowedCvTypes = ['pdf'];
$maxCvSize = 2048576; // 2MB

// Ensure the upload directory exists
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_info'])) {
        $field = $_POST['field'];
        $value = $_POST['value'] ?? '';

        if (empty($errors)) {
            $sql = "UPDATE jobseeker SET $field = ? WHERE jobseeker_email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $value, $email);
            if ($stmt->execute()) {
                header("Location: profile.php");
                exit();
            } else {
                $errors['update'] = 'Failed to update information.';
            }
        }
    } elseif (isset($_POST['update_image'])) {
        if (isset($_FILES['fileImg']) && $_FILES['fileImg']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['fileImg']['tmp_name'];
            $fileName = $_FILES['fileImg']['name'];
            $fileSize = $_FILES['fileImg']['size'];
            $fileType = $_FILES['fileImg']['type'];

            $allowedImageTypes = ['image/jpeg', 'image/png'];
            $maxImageSize = 524288000; // 500MB

            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            if (!in_array($fileType, $allowedImageTypes)) {
                $errors['image'] = 'Only JPEG and PNG images are allowed.';
            } elseif ($fileSize > $maxImageSize) {
                $errors['image'] = 'File size must be less than 500MB.';
            } else {
                $newFileName = $uploadDir . uniqid('', true) . '.' . $fileExtension;
                if (move_uploaded_file($fileTmpPath, $newFileName)) {
                    $sql = "UPDATE jobseeker SET j_image = ? WHERE jobseeker_email = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ss", $newFileName, $email);
                    if ($stmt->execute()) {
                        header("Location: profile.php");
                        exit();
                    } else {
                        $errors['image'] = 'Failed to update image.';
                    }
                } else {
                    $errors['image'] = 'Failed to upload image.';
                }
            }
        } else {
            $errors['image'] = 'Please select an image file.';
        }
    } elseif (isset($_POST['update_cv'])) {  // Ensure this condition matches the button name
        if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['cv']['tmp_name'];
            $fileName = $_FILES['cv']['name'];
            $fileSize = $_FILES['cv']['size'];
            $fileType = $_FILES['cv']['type'];

            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            if (!in_array($fileExtension, $allowedCvTypes)) {
                $errors['cv'] = 'Only PDF files are allowed.';
            } elseif ($fileSize > $maxCvSize) {
                $errors['cv'] = 'File size must be less than 2MB.';
            } else {
                $newCvName = $uploadDir . uniqid('', true) . '.' . $fileExtension;
                if (move_uploaded_file($fileTmpPath, $newCvName)) {
                    $sql = "UPDATE jobseeker SET cv = ? WHERE jobseeker_email = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ss", $newCvName, $email);
                    if ($stmt->execute()) {
                        header("Location: profile.php");
                        exit();
                    } else {
                        $errors['cv'] = 'Failed to update CV.';
                    }
                } else {
                    $errors['cv'] = 'Failed to upload CV.';
                }
            }
        } else {
            $errors['cv'] = 'Please select a CV file.';
        }
    }
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
            width: 500px;
            height: 300px;
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
    </style>
</head>
<body>
<?php include 'header.php';?>

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
                    <div class="rightRound" id="upload">
                        <div class="file-input-wrapper">
                            <input type="file" name="fileImg" id="fileImg" accept=".jpg, .jpeg, .png">
                            <i class="fa fa-camera"></i>
                        </div>
                    </div>
                    <div class="leftRound" id="cancel" style="display: none;">
                        <i class="fa fa-times"></i>
                    </div>
                    <div class="rightRound" id="confirm" style="display: none;">
                        <button type="submit" name="update_image"><i class="fa fa-check"></i></button>
                    </div>
                </div>
            </form>
        </div>
        <div class="name">
            <div class="form-group">
                <span id="jobseeker_name"><h2><?php echo htmlspecialchars($jobseeker['jobseeker_name']); ?></h2></span>
                <span id="address"><?php echo htmlspecialchars($jobseeker['address']); ?></span>
                <button class="edit-icon" onclick="toggleEdit('address')"><img src="Images/edit-text.png" alt=""></button>
                <div class="popup-form" id="popup-address">
                    <form action="profile.php" method="post">
                        <input type="hidden" name="field" value="address">
                        <textarea name="value"><?php echo htmlspecialchars($jobseeker['address']); ?></textarea>
                        <button type="submit" name="update_info">Save</button>
                        <button type="button" onclick="closeForm('address')">Close</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="buttons_2">
        <div class="form-group">
            <div class="button">
                <button class="edit-icon" onclick="toggleEdit('cv')">Change CV</button>
            </div>
            <div class="popup-form" id="popup-cv">
                <form action="profile.php" method="post" enctype="multipart/form-data">
                <input type="file" id="cv" name="cv"><br>
                <span><?php echo isset($errors['cv']) ? $errors['cv'] : ''; ?></span><br>
                    <button type="submit" name="update_cv">Update</button>
                    <button type="button" onclick="closeForm('cv')">Close</button>
                </form>
            </div>
        </div>

            

        <div class="button">
            <button onclick="location.href='preview_profile.php?email=<?php echo $jobseeker['jobseeker_email'] ?>'">Preview profile</button>
        </div>
    </div>
    <hr>
    <div class="profile__container">
        <div class="form-group">
            <div class="descript_button"><button class="edit-icon" onclick="toggleEdit('description')"><img src="Images/edit-text.png" alt=""></button></div>
            <div class="description">
                <span id="description"><?php echo nl2br($jobseeker['description']); ?></span>
                <div class="popup-form" id="popup-description">
                    <form action="profile.php" method="post">
                        <input type="hidden" name="field" value="description">
                        <textarea name="value"><?php echo htmlspecialchars($jobseeker['description']); ?></textarea>
                        <button type="submit" name="update_info">Save</button>
                        <button type="button" onclick="closeForm('description')">Close</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById("fileImg").onchange = function() {
    document.getElementById("image").src = URL.createObjectURL(fileImg.files[0]); // Preview new image
    document.getElementById("cancel").style.display = "block";
    document.getElementById("confirm").style.display = "block";
    document.getElementById("upload").style.display = "none";
}

var userImage = document.getElementById('image').src;
document.getElementById("cancel").onclick = function() {
    document.getElementById("image").src = userImage; // Back to previous image
    document.getElementById("cancel").style.display = "none";
    document.getElementById("confirm").style.display = "none";
    document.getElementById("upload").style.display = "block";
}

let activeForm = null;

function toggleEdit(field) {
    const form = document.getElementById(`popup-${field}`);
    const overlay = document.getElementById('overlay');
    
    if (activeForm && activeForm !== form) {
        activeForm.classList.add('flicker');
        setTimeout(() => activeForm.classList.remove('flicker'), 400);
    } else {
        form.classList.toggle('active');
        overlay.classList.toggle('active');
        activeForm = form.classList.contains('active') ? form : null;
    }
}

function closeForm(field) {
    const form = document.getElementById(`popup-${field}`);
    const overlay = document.getElementById('overlay');
    form.classList.remove('active');
    overlay.classList.remove('active');
    activeForm = null;
}
</script>
<div id="overlay" class="overlay"></div>
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
</body>
</html>

