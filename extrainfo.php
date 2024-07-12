<!-- <?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$description = '';
$errors = [];

if (!isset($_SESSION['jobseeker_email'])) {
    header('Location: jobseekerregister.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $uploadDir = 'uploads/';
    $allowedImageTypes = ['image/jpeg', 'image/png'];
    $allowedCvTypes = ['application/pdf'];
    $maxImageSize = 500 * 1024 * 1024; // 500MB
    $maxCvSize = 1 * 1024 * 1024; // 1MB

    // Image upload
    if (isset($_FILES['j_image']) && $_FILES['j_image']['error'] == UPLOAD_ERR_OK) {
        $j_image = $uploadDir . basename($_FILES['j_image']['name']);
        $imageType = mime_content_type($_FILES['j_image']['tmp_name']);
        if (!in_array($imageType, $allowedImageTypes)) {
            $errors['j_image'] = 'Only JPEG and PNG images are allowed';
        } elseif ($_FILES['j_image']['size'] > $maxImageSize) {
            $errors['j_image'] = 'Image file size exceeds the limit (500MB)';
        } elseif (!move_uploaded_file($_FILES['j_image']['tmp_name'], $j_image)) {
            $errors['j_image'] = 'Failed to upload image';
        }
    } else {
        $errors['j_image'] = 'Upload your image';
    }

    // CV upload
    if (isset($_FILES['cv']) && $_FILES['cv']['error'] == UPLOAD_ERR_OK) {
        $cv = $uploadDir . basename($_FILES['cv']['name']);
        $cvType = mime_content_type($_FILES['cv']['tmp_name']);
        if (!in_array($cvType, $allowedCvTypes)) {
            $errors['cv'] = 'Only PDF files are allowed for CV';
        } elseif ($_FILES['cv']['size'] > $maxCvSize) {
            $errors['cv'] = 'CV file size exceeds the limit (1MB)';
        } elseif (!move_uploaded_file($_FILES['cv']['tmp_name'], $cv)) {
            $errors['cv'] = 'Failed to upload CV';
        }
    } else {
        $errors['cv'] = 'Upload your CV';
    }

    // Description
    if (isset($_POST['description']) && !empty($_POST['description'])) {
        $description = $_POST['description'];
    } else {
        $errors['description'] = 'Enter a description';
    }

    // If no errors, update data
    if (empty($errors)) {
        require_once 'connection.php';
        $email = $_SESSION['jobseeker_email'];
        $sql = "UPDATE jobseeker SET j_image = ?, cv = ?, description = ? WHERE jobseeker_email = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param('ssss', $j_image, $cv, $description, $email);

        if ($stmt->execute()) {
            echo "Data updated successfully";
            header('Location: jobseekerlogin.php');
            exit();
        } else {
            echo 'Error: ' . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Jobseeker Extra Information</title>
</head>
<body>
    <div class="container">
        <form action="" method="post" enctype="multipart/form-data">
            <label for="j_image">Image:</label>
            <input type="file" id="j_image" name="j_image" accept="image/jpeg, image/png"><br>
            <span><?php echo isset($errors['j_image']) ? $errors['j_image'] : ''; ?></span><br>

            <label for="cv">CV:</label>
            <input type="file" id="cv" name="cv" accept="application/pdf"><br>
            <span><?php echo isset($errors['cv']) ? $errors['cv'] : ''; ?></span><br>

            <label for="description">Description:</label>
            <textarea id="description" name="description"></textarea><br>
            <span><?php echo isset($errors['description']) ? $errors['description'] : ''; ?></span><br>

            <button type="submit">Submit</button>
        </form>
    </div>
</body>
</html> -->
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$description = '';
$errors = [];

if (!isset($_SESSION['jobseeker_email'])) {
    header('Location: jobseekerregister.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validating image upload
    if ($_FILES['j_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['j_image']['tmp_name'];
        $fileName = $_FILES['j_image']['name'];
        $fileSize = $_FILES['j_image']['size'];
        $fileType = $_FILES['j_image']['type'];

        $allowedExtensions = array("jpeg", "jpg", "png");
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($fileExtension, $allowedExtensions)) {
            $errors['j_image'] = 'Only JPEG, JPG, and PNG files are allowed.';
        } elseif ($fileSize > 524288000) { // 500MB in bytes
            $errors['j_image'] = 'File size must be less than 500MB.';
        } else {
            $j_image = $uploadDir . uniqid('', true) . '.' . $fileExtension;
            move_uploaded_file($fileTmpPath, $j_image);
        }
    } else {
        $errors['j_image'] = 'Please select an image file.';
    }

    // Validating CV upload
    if ($_FILES['cv']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['cv']['tmp_name'];
        $fileName = $_FILES['cv']['name'];
        $fileSize = $_FILES['cv']['size'];
        $fileType = $_FILES['cv']['type'];

        $allowedExtensions = array("pdf");
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($fileExtension, $allowedExtensions)) {
            $errors['cv'] = 'Only PDF files are allowed.';
        } elseif ($fileSize > 1048576) { // 1MB in bytes
            $errors['cv'] = 'File size must be less than 1MB.';
        } else {
            $cv = $uploadDir . uniqid('', true) . '.' . $fileExtension;
            move_uploaded_file($fileTmpPath, $cv);
        }
    } else {
        $errors['cv'] = 'Please select a CV file.';
    }

    // Validating description
    if (isset($_POST['description']) && !empty($_POST['description'])) {
        $description = $_POST['description'];
    } else {
        $errors['description'] = 'Enter a description';
    }

    if (empty($errors)) {
        require_once 'connection.php';
        $email = $_SESSION['jobseeker_email'];
        $sql = "UPDATE jobseeker SET j_image = ?, cv = ?, description = ? WHERE jobseeker_email = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param('ssss', $j_image, $cv, $description, $email);

        if ($stmt->execute()) {
            echo "Data updated successfully";
            header('Location: login.php');
            exit();
        } else {
            echo 'Error: ' . $stmt->error;
        }
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
            padding:20px;
            margin-bottom:20px;
        }
    </style>
</head>
<?php include'header4extra.php';?>
<body>
    <div class="container_1">
        <form action="" method="post" enctype="multipart/form-data">
    <h3>Additional Information</h3>

            <label for="j_image">Image:</label>
            <input type="file" id="j_image" name="j_image"><br>
            <span><?php echo isset($errors['j_image']) ? $errors['j_image'] : ''; ?></span><br>

            <label for="cv">CV:</label>
            <input type="file" id="cv" name="cv"><br>
            <span><?php echo isset($errors['cv']) ? $errors['cv'] : ''; ?></span><br>

            <label for="description">Description:</label>
            <textarea id="description" name="description"><?php echo isset($description) ? htmlspecialchars($description) : ''; ?></textarea><br>
            <span><?php echo isset($errors['description']) ? $errors['description'] : ''; ?></span><br>

           <div class="button3"> 
            <div></div>
            <button type="submit">Finish</button></div>
        </form>

        <?php if (isset($description)): ?>
        <div class="description">
            <?php echo nl2br(htmlspecialchars($description)); ?>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>


