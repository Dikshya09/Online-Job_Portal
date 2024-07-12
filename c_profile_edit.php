<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['email'])) {
    header('location:index.php');
    exit();
}

$c_email = $_SESSION['email'];

// Fetch company data from the database
$conn = new mysqli("localhost", "root", "", "jobportal");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM company WHERE c_email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $c_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $company = $result->fetch_assoc();
} else {
    echo "<script>alert('No company found with the given email.'); window.location.href='index.php';</script>";
    exit();
}

$errors = [];
$uploadDir = 'uploads/';
$allowedCvTypes = ['pdf'];
$maxCvSize = 1048576; // 1MB

// Ensure the upload directory exists
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_info'])) {
        $field = $_POST['field'];
        $value = $_POST['value'] ?? '';

        if (empty($errors)) {
            $sql = "UPDATE company SET $field = ? WHERE c_email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $value, $c_email);
            if ($stmt->execute()) {
                header("Location: c_profile_edit.php");
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
                    $sql = "UPDATE company SET c_image = ? WHERE c_email = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ss", $newFileName, $c_email);
                    if ($stmt->execute()) {
                        header("Location: c_profile_edit.php");
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
        footer{
            margin-top:200px;
        }
    </style>
</head>
<body>
   
<?php include 'header4employer.php';?>

<div class="cols__container">
    <div class="img__container1">
        <div class="img__container">
            <form class="form" id="form" action="" enctype="multipart/form-data" method="post">
                <div class="upload">
                    <?php
                    if (isset($company['c_image']) && !empty($company['c_image'])) {
                        echo '<img src="' . htmlspecialchars($company['c_image']) . '" id="image">';
                    } else {
                        echo '<img src="Images/pathau.png" id="image">';
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
                <span id="c_name"><h2><?php echo htmlspecialchars($company['c_name']); ?></h2></span>
                <span id="c_location"><?php echo htmlspecialchars($company['c_location']); ?></span>
                <button class="edit-icon" onclick="toggleEdit('c_location')"><img src="Images/edit-text.png" alt=""></button>
                <div class="popup-form" id="popup-c_location">
                    <form action="" method="post">
                        <input type="hidden" name="field" value="c_location">
                        <textarea name="value"><?php echo htmlspecialchars($company['c_location']); ?></textarea>
                        <button type="submit" name="update_info">Save</button>
                        <button type="button" onclick="closeForm('c_location')">Close</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="buttons_2">
        <div class="button">
            <button onclick="location.href='preview_profile.php?email=<?php echo $company['c_email'] ?>'">Preview profile</button>
        </div>
    </div>
    <hr>
    <div class="profile__container">
        <div class="form-group">
            <div class="descript_button"><button class="edit-icon" onclick="toggleEdit('c_description')"><img src="Images/edit-text.png" alt=""></button></div>
            <div class="c_description">
                <span id="c_description"><?php echo nl2br($company['c_description']); ?></span>
                <div class="popup-form" id="popup-c_description">
                    <form action="" method="post">
                        <input type="hidden" name="field" value="c_description">
                        <textarea name="value"><?php echo htmlspecialchars($company['c_description']); ?></textarea>
                        <button type="submit" name="update_info">Save</button>
                        <button type="button" onclick="closeForm('c_description')">Close</button>
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
        <div class="footer-bottom-content">
            <p>Designed By Job Portal teams</p>
            <div class="copyright">
                <p>&copy;Copyright <strong>Job portal</strong>.All Rights Reserved</p>
            </div>
        </div>
    </footer>
</body>
</html>
