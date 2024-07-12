<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$c_description = '';
$c_image = '';
$errors = [];

if (!isset($_SESSION['c_email'])) {
    header('Location: companylogin.php');
    exit();
}

$c_email = $_SESSION['c_email'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Directory to upload images
    $uploadDir = 'uploads/'; // Make sure this directory exists and is writable

    // Validating image upload
    if (isset($_FILES['c_image']) && $_FILES['c_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['c_image']['tmp_name'];
        $fileName = $_FILES['c_image']['name'];
        $fileSize = $_FILES['c_image']['size'];
        $fileType = $_FILES['c_image']['type'];

        $allowedExtensions = array("jpeg", "jpg", "png");
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($fileExtension, $allowedExtensions)) {
            $errors['c_image'] = 'Only JPEG, JPG, and PNG files are allowed.';
        } elseif ($fileSize > 524288000) { // 500MB in bytes
            $errors['c_image'] = 'File size must be less than 500MB.';
        } else {
            $newFileName = uniqid('', true) . '.' . $fileExtension;
            $c_image = $uploadDir . $newFileName;
            if (!move_uploaded_file($fileTmpPath, $c_image)) {
                $errors['c_image'] = 'Error moving the uploaded file.';
            }
        }
    } else {
        $errors['c_image'] = 'Please select an image file.';
    }

    // Validating c_description
    if (isset($_POST['c_description']) && !empty($_POST['c_description'])) {
        $c_description = $_POST['c_description'];
    } else {
        $errors['c_description'] = 'Enter a Description';
    }

    if (empty($errors)) {
        require_once 'connection.php';
        $c_email = $_SESSION['c_email'];
        $connection = new mysqli('localhost', 'root', '', 'jobportal');
        
        if ($connection->connect_error) {
            die("Connection failed: " . $connection->connect_error);
        }

        $sql = "UPDATE company SET c_image = ?, c_description = ? WHERE c_email = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param('sss', $c_image, $c_description, $c_email);

        if ($stmt->execute()) {
            echo "Data updated successfully";
            header('Location: companylogin.php');
            exit();
        } else {
            echo 'Error: ' . $stmt->error;
        }

        $stmt->close();
        $connection->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="Employers_register.css">
    <title>Jobseeker Extra Information</title>
    <style>
        .description {
            white-space: pre-wrap;
             /* Preserve whitespace and line breaks */
        }
        textarea{
            height:100px;
            width: 600px;
            border: 1px solid#ccc;
            border-radius: 6px;
            outline: none;
            margin-bottom: 20px;
            font-size: medium;
        }
        .button3{
            display: flex;
            justify-content: space-between;
        }
        h3{
            margin-bottom:20px;
        }
    </style>
</head>
<body>
<div class="header">
        <nav>
            <div class="logo">
                <h2 onclick="window.location.href='index.php'">Job Portal</h2>
            </div>
            <div class="member">
                <p> Are you Already a member?
                    <a href="emplogin.php">Login here</a></p>
            </div>
        </nav>
    </div>
    <div class="container_1">
        <form action="" method="post" enctype="multipart/form-data">
    <h3>Additional Information</h3>
            <label for="c_image">Image:</label>
            <input type="file" id="c_image" name="c_image"><br>
            <span><?php echo isset($errors['c_image']) ? $errors['c_image'] : ''; ?></span><br>

            <label for="c_description">Description:</label>
            <textarea id="c_description" name="c_description"></textarea><br>
            <span><?php echo isset($errors['c_description']) ? $errors['c_description'] : ''; ?></span><br>

           <div class="button3"> <div></div><button type="submit">Finish</button></div>
        </form>
    </div>
</body>
</html>
