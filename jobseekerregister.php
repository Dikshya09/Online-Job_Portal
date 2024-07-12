<?php
require_once 'connection.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$errors = [];
$jobseeker_name = $dob = $mobno = $address = $jobseeker_email = $password = $confirm_password = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['jobseeker_name']) && !empty($_POST['jobseeker_name']) && trim($_POST['jobseeker_name'])) {
        if (preg_match('/^[a-zA-Z\s]+$/', $_POST['jobseeker_name'])) {
            $jobseeker_name = $_POST['jobseeker_name'];
        } else {
            $errors['jobseeker_name'] = 'Name should contain only alphabets';
        }
    } else {
        $errors['jobseeker_name'] = 'Enter your name';
    }

    if (isset($_POST['dob']) && !empty($_POST['dob']) && trim($_POST['dob'])) {
        $dob = $_POST['dob'];
        $dob_date = strtotime($dob);
        $current_date = strtotime(date('Y-m-d'));
        if ($dob_date >= $current_date) {
            $errors['dob'] = 'Date of birth must be in the past';
        }
    } else {
        $errors['dob'] = 'Enter your date of birth';
    }

    if (isset($_POST['mobno']) && !empty($_POST['mobno']) && trim($_POST['mobno'])) {
        if (preg_match('/^[0-9]{10}$/', $_POST['mobno'])) {
            $mobno = $_POST['mobno'];
        } else {
            $errors['mobno'] = 'Mobile number must be a 10-digit number';
        }
    } else {
        $errors['mobno'] = 'Enter your mobile number';
    }

    if (isset($_POST['address']) && !empty($_POST['address']) && trim($_POST['address'])) {
        $address = $_POST['address'];
    } else {
        $errors['address'] = 'Enter your address';
    }

    if (isset($_POST['jobseeker_email']) && !empty($_POST['jobseeker_email']) && trim($_POST['jobseeker_email'])) {
        if (filter_var($_POST['jobseeker_email'], FILTER_VALIDATE_EMAIL)) {
            $jobseeker_email = $_POST['jobseeker_email'];
        } else {
            $errors['jobseeker_email'] = 'Enter a valid email address';
        }
    } else {
        $errors['jobseeker_email'] = 'Enter your email';
    }

    if (isset($_POST['password']) && !empty($_POST['password']) && trim($_POST['password'])) {
        $password = $_POST['password'];
        // Password validation
        if (!preg_match('/^(?=.*[A-Z])(?=.*\d).{8,}$/', $password)) {
            $errors['password'] = 'Password must be at least 8 characters with at least one uppercase letter and one number';
        }
    } else {
        $errors['password'] = 'Enter a password';
    }

    if (isset($_POST['confirm_password']) && !empty($_POST['confirm_password']) && trim($_POST['confirm_password'])) {
        $confirm_password = $_POST['confirm_password'];
        if ($password !== $confirm_password) {
            $errors['confirm_password'] = 'Passwords do not match';
        }
    } else {
        $errors['confirm_password'] = 'Confirm your password';
    }

    if (!isset($_POST['terms'])) {
        $errors['terms'] = 'You must agree to the terms and conditions';
    }

    if (empty($errors)) {
        $sql = "INSERT INTO jobseeker (jobseeker_name, dob, mobno, address, jobseeker_email, password) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param('ssssss', $jobseeker_name, $dob, $mobno, $address, $jobseeker_email, $password);

        if ($stmt->execute()) {
            $_SESSION['jobseeker_email'] = $jobseeker_email;
            echo "Registration successful";
            header('Location: extrainfo.php');
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
    <link rel="stylesheet" href="login.css">
    <title>Jobseeker Registration</title>
</head>
<body>
<div class="header">
        <nav>
            <div class="logo">
                <h2 onclick="window.location.href='index.php'">Job Portal</h2>
            </div>
            <div class="member">
                <p> Are you Already a member?
                    <a href="jobseekerlogin.php">Login here</a></p>
            </div>
        </nav>
    </div>
    <section class="form-box">
        <form action="" method="post" onsubmit="return validateForm()">
            <h3>Jobseeker Registration</h3>
            <div class="field-group">
                <label for="jobseeker_name"><b>Name:</b></label>
                <span style="color:red;"><?php echo isset($errors['jobseeker_name']) ? $errors['jobseeker_name'] : ''; ?></span><br>
                <input type="text" id="jobseeker_name" name="jobseeker_name" value="<?php echo htmlspecialchars($jobseeker_name); ?>" placeholder="Enter your name"><br>
            </div>
            <div class="field-group">
                <label for="dob">Date of Birth:</label>
                <span style="color:red;"><?php echo isset($errors['dob']) ? $errors['dob'] : ''; ?></span><br>
                <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($dob); ?>"><br>
            </div>
            <div class="field-group">
                <label for="mobno">Mobile Number:</label>
                <span style="color:red;"><?php echo isset($errors['mobno']) ? $errors['mobno'] : ''; ?></span><br>
                <input type="text" id="mobno" name="mobno" value="<?php echo htmlspecialchars($mobno); ?>" placeholder="Enter mobile number"><br>
            </div>
            <div class="field-group">
                <label for="address">Address:</label>
                <span style="color:red;"><?php echo isset($errors['address']) ? $errors['address'] : ''; ?></span><br>
                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($address); ?>" placeholder="Enter your address"><br>
            </div>
            <div class="field-group">
                <label for="jobseeker_email">Email:</label>
                <span style="color:red;"><?php echo isset($errors['jobseeker_email']) ? $errors['jobseeker_email'] : ''; ?></span><br>
                <input type="text" id="jobseeker_email" name="jobseeker_email" value="<?php echo htmlspecialchars($jobseeker_email); ?>" placeholder="Enter your email"><br>
            </div>
            <div class="field-group">
            <label for="password">Password:</label>
            <span style="color:red;"><?php echo isset($errors['password']) ? $errors['password'] : ''; ?></span><br>
            <input type="password" id="password" name="password"><br>
          </div>

            <div class="field-group">
            <label for="confirm_password">Confirm Password:</label>
            <span style="color:red;"><?php echo isset($errors['confirm_password']) ? $errors['confirm_password'] : ''; ?></span><br>
            <input type="password" id="confirm_password" name="confirm_password"><br>
            </div>
            
            <input type="checkbox" id="terms" name="terms">
            <label for="terms">I agree to the terms and conditions.</label>
            <span style="color:red;"><?php echo isset($errors['terms']) ? $errors['terms'] : ''; ?></span><br>

            <button type="submit">Register</button>
        </form>
    </section>
    <script>
        function validateForm() {
            const dob = new Date(document.getElementById('dob').value);
            const today = new Date();
            if (dob >= today) {
                alert('Date of birth must be in the past');
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
