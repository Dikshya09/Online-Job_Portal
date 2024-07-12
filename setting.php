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
$allowedImageTypes = ['image/jpeg', 'image/png'];
$allowedCvTypes = ['application/pdf'];
$maxImageSize = 524288000; // 500MB
$maxCvSize = 1048576; // 1MB

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_info'])) {
        $field = $_POST['field'];

        if (isset($_POST['value'])) {
            $value = $_POST['value'];
        } else {
            $value = '';
        }

        if ($field == 'password') {
            $oldPassword = $_POST['oldPassword'];
            $newPassword = $_POST['newPassword'];
            $confirmPassword = $_POST['confirmPassword'];

            if ($oldPassword != $jobseeker['password']) {
                $errors['password'] = 'Old password is incorrect.';
            } elseif ($newPassword != $confirmPassword) {
                $errors['password'] = 'New passwords do not match.';
            } else {
                $value = $newPassword; // No hashing
                $field = 'password';
            }
        }

        if (empty($errors)) {
            $sql = "UPDATE jobseeker SET $field = ? WHERE jobseeker_email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $value, $email);
            if ($stmt->execute()) {
                header("Location: Setting.php");
                exit();
            } else {
                $errors['update'] = 'Failed to update information.';
            }
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
        .file-input-wrapper {
            position: relative;
            display: inline-block;
        }

        .file-input-wrapper input[type="file"] {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .file-input-wrapper span {
            display: inline-block;
            padding: 8px 16px;
            background-color: #338573;
            color: #fff;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .file-input-wrapper span:hover {
            background-color: #45a049;
        }
        .update-form {
            display: none;
            margin-top: 10px;
        }
        .update-form.active {
            display: block;
        }
        .update {
            margin-top: 10px;
        }
    </style>
</head>
<body>
<?php include 'header.php';?>

        <div class="form-group" id="password-group">
            <label>Change Password:</label>
            <button class="edit-icon" onclick="toggleEdit('password')"><img src="Images/edit-text.png" alt=""></button>
            <div class="update-form" id="update-password">
                <form action="Setting.php" method="post">
                    <input type="hidden" name="field" value="password">
                    <input type="password" name="oldPassword" placeholder="Old Password">
                    <input type="password" name="newPassword" placeholder="New Password">
                    <input type="password" name="confirmPassword" placeholder="Confirm Password">
                    <button type="submit" name="update_info">Update</button>
                </form>
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

function toggleEdit(id) {
    var form = document.getElementById('update-' + id);
    if (form) {
        form.classList.toggle('active');
    }
}
</script>
</body>
</html>
