<?php
// Database connection
include('connection.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(!isset($_SESSION['username'])){
    header('location:adminlogin.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_name = $_POST['category_name'];

    // Validating image upload
    if (isset($_FILES['category_image']) && $_FILES['category_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['category_image']['tmp_name'];
        $fileName = $_FILES['category_image']['name'];
        $fileSize = $_FILES['category_image']['size'];
        $fileType = $_FILES['category_image']['type'];

        $allowedExtensions = array("jpeg", "jpg", "png");
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($fileExtension, $allowedExtensions)) {
            $errors['category_image'] = 'Only JPEG, JPG, and PNG files are allowed.';
        } elseif ($fileSize > 524288000) { // 500MB in bytes
            $errors['category_image'] = 'File size must be less than 500MB.';
        } else {
            $uploadDir = 'uploads/'; // Define your upload directory here
            $category_image = $uploadDir . uniqid('', true) . '.' . $fileExtension;
            if (!move_uploaded_file($fileTmpPath, $category_image)) {
                $errors['category_image'] = 'Failed to move uploaded file.';
            }
        }
    } else {
        $errors['category_image'] = 'Please select an image file.';
    }

    if (empty($errors)) {
        // Insert the new category into the database
        $conn = new mysqli("localhost", "root", "", "jobportal");

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "INSERT INTO category (category_name, category_image) VALUES ('$category_name', '$category_image')";
        if (mysqli_query($conn, $sql)) {
            echo "New category added successfully.";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
        $conn->close();
    }
}

// Fetch categories from the database
$conn = new mysqli("localhost", "root", "", "jobportal");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM category";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .category_form {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 500px;
            border-radius: 5px;
            margin: auto;
            margin-top: 20px;
            box-shadow: rgba(49, 49, 49, 0.2) 0px 2px 8px 0px;
            font-size: larger;
            background-color: #fff;
            margin-bottom: 80px;
        }
        form {
            padding: 30px;
            width: 100%;
        }
        input[type="text"],
        input[type="file"] {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: medium;
            outline: none;
        }
        label {
            font-size: medium;
            color: #333;
            margin-bottom: 8px;
            display: block;
        }
        h3 {
            color: #338573;
            text-align: center;
            font-size: 24px;
        }
        span.error-message {
            color: red;
            font-size: 14px;
            margin-top: 4px;
            display: block;
        }
table{
position:relative;
height: auto;
width:570px;
border-collapse:collapse;
border-radius:10px;
overflow:hidden;
box-shadow: 0 2px 12px rgba(90, 89, 89, 0.3);
background-color: #fff;
}
table tr th{
border: none;
border-bottom: 1px solid grey;
}
th, td {
padding: 12px;
border: none;
border-bottom: 1px solid #d3d3d3;
text-align: center;
}
    .category_list{
        display: flex;
            justify-content: center;
            align-items: center;
            width: 650px;
            border-radius: 5px;
            margin: auto;
            margin-top: 20px;
    }

    </style>
</head>
<body>
    <h3>Add New Job Category</h3>
    <div class="category_form">
        <form method="post" action="" enctype="multipart/form-data">
            <label for="category_name">Category Name:</label>
            <input type="text" id="category_name" name="category_name" required>
            <label for="category_image">Image:</label>
            <input type="file" id="category_image" name="category_image" required>
            <span class="error-message"><?php echo isset($errors['category_image']) ? $errors['category_image'] : ''; ?></span>
            <button type="submit" value="Add Category">Add Category</button>
        </form>
    </div>
    
    <h3>Category List</h3>
    <div class="category_list">
    <table class="category_table">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Image</th>
            <th>Action</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["category_id"] . "</td>";
                echo "<td>" . $row["category_name"] . "</td>";
                echo "<td><img src='" . $row["category_image"] . "' alt='Category Image' width='50' height='50'></td>";
                echo "<td><a href='delete_category.php?id=" . $row["category_id"] . "'>Delete</a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No categories found.</td></tr>";
        }
        ?>
    </table>
    </div>
</body>
</html>
