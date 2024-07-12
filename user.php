<?php
    $email = $password =$user_type ='';
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        if (isset ($_POST['email']) &&!empty($_POST['email']) && trim($_POST['email'])&& filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)) {
            $email = $_POST['email'];
        }
        else{
            $err['email'] = 'Enter Email';
        }
        if (isset($_POST['password']) && !empty($_POST['password'])) {
            $password = $_POST['password'];
        } else {
            $err['password'] = 'Enter password';
        }
    
        if (isset($_POST['confirm_password']) && !empty($_POST['confirm_password'])) {
            $confirm_password = $_POST['confirm_password'];
            if ($confirm_password !== $password) {
                $err['confirm_password'] = 'Passwords do not match';
            }
        } else {
            $err['confirm_password'] = 'Confirm password';
        }
        if (isset ($_POST['user_type']) &&!empty($_POST['user_type']) && trim($_POST['user_type'])) {
            $user_type = $_POST['user_type'];
        }
        else{
            $err['user_type'] = 'Select user type';
        }
    
        $email = $_POST['email'];
        $password = $_POST['password'];
        $user_type = $_POST['user_type'];
        try{
            require_once 'connection.php';
            $sql = "INSERT INTO users(email,password,user_type) values('$email','$password','$user_type')";
            $connection->query($sql);
            echo "Data inserted successfully";

            // Redirect based on user type
        if ($user_type == 'jobseeker') {
            header("Location: jobseekerregister.php");
            exit; // Ensure script execution stops after redirection
        } elseif ($user_type == 'employer') {
            header("Location: Employers registerform.php");
            exit; // Ensure script execution stops after redirection
        }
        }catch(Exception $ex){
            die('Error: ' . $ex->getMessage());
        }
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>user</title>
    <link rel="stylesheet" href="login.css"> 
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        <form action="" method="post" name="userlogin_form">
            <h3>USER REGISTER</h3>
            <div class="field-group">
                <label for="email"><b>Email:</b></label>
                <input type="text" id="email" name="email" >
                <span><?php echo isset($err['email'])?$err['email']:''; ?></span>
            </div>
            <div class="field-group">
                <label for="password"><b>Password:</b></label>
                <input type="password" id="password" name="password" >
                <span><?php echo isset($err['password'])?$err['password']:''; ?></span>
            </div>
            <div class="field-group">
                <label for="user_type"><b>User Type:</b> </label>
                <select name="user_type" id="user_type">
                    <option value="jobseeker">Jobseeker</option>
                    <option value="company">Company</option>
                </select>
                <span><?php echo isset($err['user_type'])?$err['user_type']:''; ?></span>
            </div>
            <div class="btn-group">
                <button type="login" name="btnLogin">Submit</button>
            </div>
        </form>
    </section>
    <footer id="footer">
        <div class="footer-content">
          <div class="logo">
            <h2>Job Portal</h2>
            </div>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Et labore suscipit nisi non, laudantium delectus?
                <br>Lorem ipsum dolor sit amet consectetur adipisicing elit. Deserunt, molestias!
            </p>
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