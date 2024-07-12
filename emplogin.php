<?php
$err = [];
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$connection = new mysqli('localhost', 'root', '', 'jobportal');

$email = $password = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['email']) && !empty($_POST['email']) && trim($_POST['email'])) {
        $email = trim($_POST['email']);
    } else {
        $err['email'] = 'Enter Email';
    }

    if (isset($_POST['password']) && !empty($_POST['password'])) {
        $password = $_POST['password'];
    } else {
        $err['password'] = 'Enter password';
    }

    if (count($err) == 0) {
        // Use prepared statements to prevent SQL injection
        $stmt = $connection->prepare("SELECT * FROM company WHERE c_email = ? AND password = ?");
        $stmt->bind_param('ss', $email, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $_SESSION['email'] = $email; // Store email in session
            header("Location: empdashboard.php"); // Redirect to dashboard
            exit(); // Ensure script stops after redirection
        } else {
            $err['authentication'] = 'Invalid email or password';
        }
        
        $stmt->close();
    }
}

$connection->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login.css"> 
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
         .error-message {
            color: red; /* Change the text color to red */
            
        }
    </style>
</head>
<body>
    <div class="header">
        <nav>
            <div class="logo">
                <h2>Job Portal</h2>
            </div>
        </nav>
    </div>
    <section class="form-box">
        <form action="" method="post" name="login_form">
            <h3> EMPLOYER'S LOGIN</h3>
           
            <div class="field-group">
                <label for="email"><b>Email:</b></label>
                <input type="text" id="email" name="email">
                <span><?php echo isset($err['email']) ? $err['email'] : ''; ?></span>
            </div>
            <div class="field-group">
                <label for="password"><b>Password:</b></label>
                <input type="password" id="password" name="password">
                <span><?php echo isset($err['password']) ? $err['password'] : ''; ?></span>
            </div>
            <?php if (isset($err['authentication'])) echo "<p class='error-message'>{$err['authentication']}</p>"; ?>

            <div class="btn-group">
                <button type="submit" name="btnLogin">Log In</button>
            </div>
            <p>Don't have an account yet? <a href="user.php">Create a new account</a> today.</p>
        </form>
    </section>
</body>
</html>
