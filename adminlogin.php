<?php
if (isset($_COOKIE['username'])) {
    session_start();
    $_SESSION['username'] = $_COOKIE['username'];
}
$username = $password = '';
if (isset($_POST['btnLogin'])) {
    $err =[];
    if (isset($_POST['username']) && !empty($_POST['username']) && trim($_POST['username'])){
        $username = trim($_POST['username']);
    } else {
        $err['username'] = 'Enter username';
    }
    if(isset($_POST['password']) && !empty($_POST['password'])){
        $password = $_POST['password'];
    } else {
        $err['password'] = 'Enter password';
    }

    //if error not occured
    if(count($err) == 0){
        //check valid username and password
        if($username == 'jobportal' && $password == 'jobportal123'){
            session_start();
            $_SESSION['username'] = $username;
            //check remember me
            if(isset($_POST['remember'])){
                setcookie('username',$username,time()+7*24*60*60);
            }
            header('location:admindashboard.php');
        }else {
            echo 'Login failed';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>admin login</title>
    <link rel="stylesheet" href="login.css"> 
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <script>
        // Toggle the visibility of the dropdown content
        function toggleDropdown(dropdownId) {
          var dropdown = document.getElementById(dropdownId);
          dropdown.classList.toggle("show");
        }
      
        // Close the dropdown if the user clicks outside of it
        window.onclick = function(event) {
          if (!event.target.matches('.dropdown-btn')) {
            var dropdowns = document.getElementsByClassName("dropdown-content");
            for (var i = 0; i < dropdowns.length; i++) {
              var openDropdown = dropdowns[i];
              if (openDropdown.classList.contains('show')) {
                openDropdown.classList.remove('show');
              }
            }
          }
        }
      </script> 
    <div class="header">
        <nav>
            <div class="logo">
            <h2>Job Portal</h2>
            </div>
        </nav>
        </div>
    <section class="form-box">
        <form action="<?php echo $_SERVER['PHP_SELF']?>" method="post" name="login_form">
            <h3>ADMIN LOGIN</h3>
            <div class="field-group">
                <label for="username"><b>Username:</b></label>
                <input type="text" id="username" name="username">
                <span><?php echo isset($err['username'])?$err['username']:''; ?></span>
            </div>
            <div class="field-group">
                <label for="password"><b>Password:</b></label>
                <input type="password" id="password" name="password" >
                <span><?php echo isset($err['password'])?$err['password']:''; ?></span>
            </div>
            <div class="btn-group">
                <button type="login" name="btnLogin">Log In</button>
            </div>
        </form>
    </section>
    <footer id="footer">
        <div class="footer-content">
          <div class="logo">
            <h2>Job Portal</h2>
            </div>
            <!-- <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Et labore suscipit nisi non, laudantium delectus?
                <br>Lorem ipsum dolor sit amet consectetur adipisicing elit. Deserunt, molestias!
            </p> -->
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