<?php
$c_name = $c_email = $industry = $contact_no = $location = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['c_name']) && !empty($_POST['c_name']) && trim($_POST['c_name'])) {
        $c_name = $_POST['c_name'];
        if (!preg_match('/^[a-zA-Z\s]{3,50}$/', $c_name)) {
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

    if (isset($_POST['location']) && !empty($_POST['location']) && trim($_POST['location'])) {
        $location = $_POST['location'];
    } else {
        $errors['location'] = 'Enter company location';
    }

    if (empty($errors)) {
        try {
            require_once 'connection.php';
            $sql = "UPDATE company SET c_name=?, industry=?, contact_no=?, c_location=? WHERE c_email=?";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param("sssss", $c_name, $industry, $contact_no, $location, $c_email);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                echo "Company updated successfully";
            } else {
                echo "No changes made";
            }
        } catch (Exception $ex) {
            die('Error: ' . $ex->getMessage());
        }
    } else {
        echo "Errors: ";
        print_r($errors);
    }
}

if (isset($_GET['c_email'])) {
    $c_email = $_GET['c_email'];
    try {
        require_once 'connection.php';
        $sql = "SELECT * FROM company WHERE c_email = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("s", $c_email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $record = $result->fetch_assoc();
            $c_name = $record['c_name'];
            $industry = $record['industry'];
            $contact_no = $record['contact_no'];
            $location = $record['c_location'];
        } else {
            die('Data not found');
        }
    } catch (Exception $ex) {
        die('Error: ' . $ex->getMessage());
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
                <h2 onclick="window.location.href='admindashboard.php'">Job Portal</h2>
            </div>
        </nav>
    </div>
    <div class="container_1">
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
            <h3>Registration Form</h3>
            <div class="inputbox">
                <label for="cname">Company Name:</label>
                <span><?php echo isset($errors['c_name']) ? $errors['c_name'] : ''; ?></span><br>
                <input type="text" id="cname" name="c_name" value="<?php echo htmlspecialchars($c_name); ?>" placeholder="Enter company name"><br>

                <label for="c_email">Email:</label>
                <span><?php echo isset($errors['c_email']) ? $errors['c_email'] : ''; ?></span><br>
                <input type="text" id="c_email" name="c_email" value="<?php echo htmlspecialchars($c_email); ?>" placeholder="Enter email"><br>

                <label for="industry">Company Industry:</label>
                <span><?php echo isset($errors['industry']) ? $errors['industry'] : ''; ?></span><br>
                <select id="industry" name="industry">
                    <option disabled selected value="">Select Company Industry</option>
                    <option value="it" <?php echo ($industry == 'it') ? 'selected' : ''; ?>>IT</option>
                    <option value="finance" <?php echo ($industry == 'finance') ? 'selected' : ''; ?>>Finance</option>
                    <option value="manufacturing" <?php echo ($industry == 'manufacturing') ? 'selected' : ''; ?>>Manufacturing</option>
                </select><br>

                <label for="contact_no">Company Contact No:</label>
                <span><?php echo isset($errors['contact_no']) ? $errors['contact_no'] : ''; ?></span><br>
                <input type="text" id="contact_no" name="contact_no" value="<?php echo htmlspecialchars($contact_no); ?>" placeholder="Enter contact number"><br>

                <label for="location">Company Location:</label>
                <span><?php echo isset($errors['location']) ? $errors['location'] : ''; ?></span><br>
                <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($location); ?>" placeholder="Enter company location"><br>
            </div>
            <div class="button">
                <button type="submit">Update</button>
            </div>
        </form>
    </div>
    <footer id="footer">
        <div class="footer-content">
            <div class="logo">
                <h2>Job Portal</h2>
            </div>
            <div class="social-links">
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
