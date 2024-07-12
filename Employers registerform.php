<?php
require_once 'connection.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$c_name = $c_email = $password = $c_location = $contact_no = $industry = $terms = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['c_name']) && !empty($_POST['c_name']) && trim($_POST['c_name'])) {
        $c_name = $_POST['c_name'];
        if (!preg_match('/^[a-zA-Z\s]{2,50}$/', $c_name)) {
            $errors['c_name'] = 'Enter valid company name';
        }
    } else {
        $errors['c_name'] = 'Enter company name';
    }

    if (isset($_POST['c_email']) && !empty($_POST['c_email']) && trim($_POST['c_email'])) {
        $c_email = $_POST['c_email'];
    } else {
        $errors['c_email'] = 'Enter your email';
    }

    if (isset($_POST['password']) && !empty($_POST['password']) && trim($_POST['password'])) {
        $password = $_POST['password'];
        // Password validation
        if (!preg_match('/^(?=.*[A-Z])(?=.*\d).{6,}$/', $password)) {
            $errors['password'] = 'Password must be at least 6 characters with at least one uppercase letter and one number';
        }
    } else {
        $errors['password'] = 'Enter a password';
    }

    if (isset($_POST['industry']) && !empty($_POST['industry'])) {
        $industry = $_POST['industry'];
    } else {
        $errors['industry'] = 'Select Company Industry';
    }

    if (isset($_POST['contact_no']) && !empty($_POST['contact_no']) && trim($_POST['contact_no'])) {
        $contact_no = $_POST['contact_no'];
        if (!preg_match('/^[0-9]{10}$/', $contact_no)) {
            $errors['contact_no'] = 'Enter valid contact number';
        }
    } else {
        $errors['contact_no'] = 'Enter contact number';
    }

    if (isset($_POST['c_location']) && !empty($_POST['c_location']) && trim($_POST['c_location'])) {
        $c_location = $_POST['c_location'];
    } else {
        $errors['c_location'] = 'Enter company location';
    }

    if (!isset($_POST['terms'])) {
        $errors['terms'] = 'You must agree to the terms and conditions';
    } else {
        $terms = $_POST['terms'];
    }

    if (empty($errors)) {
        $connection = new mysqli('localhost','root','','jobportal');
        $sql = "INSERT INTO company (c_name, c_email, password, c_location, contact_no, industry, terms) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param('sssssss', $c_name, $c_email, $password, $c_location, $contact_no, $industry, $terms);

        if ($stmt->execute()) {
            $_SESSION['c_email'] = $c_email;
            echo "Registration successful";
            header('Location: company_extra_info.php');
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
    <link rel="stylesheet" href="Employers_register.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Employers Registration Form</title>
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
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <h3>Registration Form</h3>

            <div class="inputbox">
                <label for="c_name">Company Name:</label> 
                <span><?php echo isset($errors['c_name']) ? $errors['c_name'] : '' ?></span><br>
                <input type="text" id="c_name" name="c_name" value="<?php echo htmlspecialchars($c_name) ?>" placeholder="Enter company name"><br>

                <label for="c_email">Email:</label>
                <span><?php echo isset($errors['c_email']) ? $errors['c_email'] : '' ?></span><br>
                <input type="text" id="c_email" name="c_email" value="<?php echo htmlspecialchars($c_email) ?>" placeholder="Enter email"><br>

                <label for="password">Password:</label>
                <span><?php echo isset($errors['password']) ? $errors['password'] : '' ?></span><br>
                <input type="password" id="password" name="password" value="<?php echo htmlspecialchars($password) ?>" placeholder="Enter password"><br>

                <label for="c_location">Company Location:</label>
                <span><?php echo isset($errors['c_location']) ? $errors['c_location'] : '' ?></span><br>
                <input type="text" id="c_location" name="c_location" value="<?php echo htmlspecialchars($c_location) ?>" placeholder="Enter company location"><br>

                <label for="contact_no">Company Contact No:</label>
                <span><?php echo isset($errors['contact_no']) ? $errors['contact_no'] : '' ?></span><br>
                <input type="text" id="contact_no" name="contact_no" value="<?php echo htmlspecialchars($contact_no) ?>" placeholder="Enter contact number"><br>

                <label for="industry">Company Industry:</label>
                <span><?php echo isset($errors['industry']) ? $errors['industry'] : '' ?></span><br>
                <select id="industry" name="industry">
                    <option disabled selected value="">Select Company Industry</option>
                    <option value="it" <?php echo ($industry == 'it') ? 'selected' : '' ?>>IT</option>
                    <option value="finance" <?php echo ($industry == 'finance') ? 'selected' : '' ?>>Finance</option>
                    <option value="manufacturing" <?php echo ($industry == 'manufacturing') ? 'selected' : '' ?>>Manufacturing</option>
                </select><br>
            </div>

            <div class="term">
                <input type="checkbox" id="terms" name="terms" value="<?php echo isset($terms) ? 'checked' : '' ?>">
                <label for="terms">I agree to the terms and conditions.</label>
                <span><?php echo isset($errors['terms']) ? $errors['terms'] : '' ?></span><br>
            </div>

            <div class="button">
                <button type="submit">Register</button>
            </div>
        </form>
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
                <p>&copy; Copyright <strong>Job portal</strong>. All Rights Reserved</p>
            </div>
        </div>
    </footer>
</body>

</html>
