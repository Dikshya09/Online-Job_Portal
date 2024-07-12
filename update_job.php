<?php
$jobTitle = $category_id = $jobType = $jobLocation = $salaryRange = $experienceLevel = $applicationDeadline = $qualification = $jobDescription = $jobrequirement = $status = '';
$errors = [];

// Database connection
$connection = new mysqli('localhost', 'root', '', 'jobportal');
if ($connection->connect_error) {
    die('Connection failed: ' . $connection->connect_error);
}

// Fetch categories for dropdown
$category_result = $connection->query("SELECT category_id, category_name FROM category");

// Fetch existing job data for the given job_id
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = $connection->query("SELECT * FROM job WHERE job_id = $id");

    if ($result && $result->num_rows == 1) {
        $record = $result->fetch_assoc();
    } else {
        die('Data not found');
    }
} else {
    die('Invalid job ID');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate inputs
    if (!empty($_POST['jobTitle']) && trim($_POST['jobTitle'])) {
        $jobTitle = $connection->real_escape_string(trim($_POST['jobTitle']));
        if (!preg_match('/^[a-zA-Z\s]{3,50}$/', $jobTitle)) {
            $errors['jobTitle'] = 'Enter a valid job title';
        }
    } else {
        $errors['jobTitle'] = 'Enter job title';
    }
    
    if (!empty($_POST['category_id'])) {
        $category_id = intval($_POST['category_id']);
    } else {
        $errors['category_id'] = 'Select job category';
    }
    
    if (!empty($_POST['jobType'])) {
        $jobType = $connection->real_escape_string($_POST['jobType']);
    } else {
        $errors['jobType'] = 'Select job type';
    }
    
    if (!empty($_POST['jobLocation']) && trim($_POST['jobLocation'])) {
        $jobLocation = $connection->real_escape_string(trim($_POST['jobLocation']));
    } else {
        $errors['jobLocation'] = 'Enter job location';
    }
    
    if (!empty($_POST['salaryRange']) && trim($_POST['salaryRange'])) {
        $salaryRange = $connection->real_escape_string(trim($_POST['salaryRange']));
    } else {
        $errors['salaryRange'] = 'Enter salary range';
    }
    
    if (!empty($_POST['experienceLevel']) && trim($_POST['experienceLevel'])) {
        $experienceLevel = $connection->real_escape_string(trim($_POST['experienceLevel']));
    } else {
        $errors['experienceLevel'] = 'Enter experience level';
    }
    
    if (!empty($_POST['applicationDeadline'])) {
        $applicationDeadline = $connection->real_escape_string($_POST['applicationDeadline']);
        if (strtotime($applicationDeadline) < strtotime(date('Y-m-d'))) {
            $errors['applicationDeadline'] = ' Invalid Deadline ';
        }
    } else {
        $errors['applicationDeadline'] = 'Enter application deadline';
    }
    
    if (!empty($_POST['qualification']) && trim($_POST['qualification'])) {
        $qualification = $connection->real_escape_string(trim($_POST['qualification']));
    } else {
        $errors['qualification'] = 'Enter qualification';
    }
    
    if (!empty($_POST['jobDescription']) && trim($_POST['jobDescription'])) {
        $jobDescription = $connection->real_escape_string(trim($_POST['jobDescription']));
    } else {
        $errors['jobDescription'] = 'Enter job description';
    }
    
    if (!empty($_POST['jobrequirement']) && trim($_POST['jobrequirement'])) {
        $jobrequirement = $connection->real_escape_string(implode(", ", array_filter(array_map('trim', explode("\n", $_POST['jobrequirement'])))));
    } else {
        $errors['jobrequirement'] = 'Enter job requirement';
    }

    if (empty($errors)) {
        $sql = "UPDATE job SET jobTitle='$jobTitle', qualification='$qualification', category_id='$category_id', jobType='$jobType', jobLocation='$jobLocation', salaryRange='$salaryRange', experienceLevel='$experienceLevel', applicationDeadline='$applicationDeadline', jobDescription='$jobDescription', jobrequirement='$jobrequirement', status='$status' WHERE job_id=$id";
        if ($connection->query($sql) === TRUE) {
            echo "Updated successfully";
            header('Location: empdashboard.php?page=postedjob');
            $updateSql = "UPDATE job SET status = CASE WHEN applicationDeadline >= CURDATE() THEN 'Active' ELSE 'Expired' END";
            if ($connection->query($updateSql) === TRUE) {
                echo " Status updated successfully.";
            } else {
                echo " Error updating status: " . $connection->error;
            }
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "Error updating record: " . $connection->error;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="Jobpost.css">
    <link rel="stylesheet" href="profile.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Update Job</title>
   
    <script>
        function validateForm() {
            let jobTitle = document.forms["jobpost"]["jobTitle"].value.trim();
            let category_id = document.forms["jobpost"]["category_id"].value;
            let jobType = document.forms["jobpost"]["jobType"].value;
            let jobLocation = document.forms["jobpost"]["jobLocation"].value.trim();
            let salaryRange = document.forms["jobpost"]["salaryRange"].value.trim();
            let experienceLevel = document.forms["jobpost"]["experienceLevel"].value.trim();
            let applicationDeadline = document.forms["jobpost"]["applicationDeadline"].value;
            let qualification = document.forms["jobpost"]["qualification"].value.trim();
            let jobDescription = document.forms["jobpost"]["jobDescription"].value.trim();
            let jobrequirement = document.forms["jobpost"]["jobrequirement"].value.trim();

            if (!jobTitle || !category_id || !jobType || !jobLocation || !salaryRange || !experienceLevel || !applicationDeadline || !qualification || !jobDescription || !jobrequirement) {
                alert("Please fill in all required fields.");
                return false;
            }
            return true;
        }
    </script>
   
</head>
<body>
    
<?php include 'header4employer.php';?>
   
    <div class="Job_post_container">
        <form action="" method="post">
            <h3>Update Job</h3>

            <div class="box_1">
                <div class="jobtitle">
                    <label for="jobTitle">Job Title:</label>
                    <span><?php echo isset($errors['jobTitle']) ? $errors['jobTitle'] : '' ?></span><br>
                    <input type="text" id="jobTitle" name="jobTitle" value="<?php echo htmlspecialchars($record['jobTitle']) ?>" placeholder="Enter Job Title"><br>
                </div>
                <div class="postedby">
                    <label for="qualification">Qualification:</label>
                    <span><?php echo isset($errors['qualification']) ? $errors['qualification'] : '' ?></span><br>
                    <input type="text" id="qualification" name="qualification" value="<?php echo htmlspecialchars($record['qualification']) ?>" placeholder="Enter qualification"><br>
                </div>
            </div>
            
            <div class="box_1">
                <div class="cate_gory">
                    <label for="category_id">Job Category:</label>
                    <span><?php echo isset($errors['category_id']) ? $errors['category_id'] : '' ?></span><br>
                    <select id="category_id" name="category_id">
                        <option disabled selected value="">Select Job Category</option>
                        <?php while ($row = $category_result->fetch_assoc()) { ?>
                            <option value="<?php echo $row['category_id']; ?>" <?php echo ($record['category_id'] == $row['category_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($row['category_name']); ?>
                            </option>
                        <?php } ?>
                    </select><br>
                </div>
                <div class="Jobtype">
                    <label for="jobType">Job Type:</label>
                    <span><?php echo isset($errors['jobType']) ? $errors['jobType'] : '' ?></span><br>
                    <select id="jobType" name="jobType">
                        <option disabled selected value="">Select Job Type</option>
                        <option value="full-time" <?php echo ($record['jobType'] == 'full-time') ? 'selected' : ''; ?>>Full-time</option>
                        <option value="part-time" <?php echo ($record['jobType'] == 'part-time') ? 'selected' : ''; ?>>Part-time</option>
                        <option value="contract" <?php echo ($record['jobType'] == 'contract') ? 'selected' : ''; ?>>Contract</option>
                        <option value="temporary" <?php echo ($record['jobType'] == 'temporary') ? 'selected' : ''; ?>>Temporary</option>
                    </select><br>
                </div>
            </div>
            
            <div class="box_1">
                <div class="location">
                    <label for="jobLocation">Job Location:</label>
                    <span><?php echo isset($errors['jobLocation']) ? $errors['jobLocation'] : '' ?></span><br>
                    <input type="text" id="jobLocation" name="jobLocation" value="<?php echo htmlspecialchars($record['jobLocation']) ?>" placeholder="Enter Job Location"><br>
                </div>
                <div class="salary_range">
                    <label for="salaryRange">Salary Range:</label>
                    <span><?php echo isset($errors['salaryRange']) ? $errors['salaryRange'] : '' ?></span><br>
                    <input type="text" id="salaryRange" name="salaryRange" value="<?php echo htmlspecialchars($record['salaryRange']) ?>" placeholder="Enter Salary Range"><br>
                </div>
            </div>
            
            <div class="box_1">
                <div class="experience_1">
                    <label for="experienceLevel">Experience Level:</label>
                    <span><?php echo isset($errors['experienceLevel']) ? $errors['experienceLevel'] : '' ?></span><br>
                    <input type="text" id="experienceLevel" name="experienceLevel" value="<?php echo htmlspecialchars($record['experienceLevel']) ?>" placeholder="Enter Experience Level"><br>
                </div>
                <div class="application_deadline">
                    <label for="applicationDeadline">Application Deadline:</label>
                    <span><?php echo isset($errors['applicationDeadline']) ? $errors['applicationDeadline'] : '' ?></span><br>
                    <input type="date" id="applicationDeadline" name="applicationDeadline" value="<?php echo htmlspecialchars($record['applicationDeadline']) ?>"><br>
                </div>
            </div>
            
            <label for="jobDescription">Job Description:</label>
            <span><?php echo isset($errors['jobDescription']) ? $errors['jobDescription'] : '' ?></span><br>
            <textarea id="jobDescription" name="jobDescription" placeholder="Enter Job Description" rows="4" cols="50"><?php echo htmlspecialchars($record['jobDescription']) ?></textarea><br>
            
            <label for="jobrequirement">Requirements:</label>
            <span><?php echo isset($errors['jobrequirement']) ? $errors['jobrequirement'] : '' ?></span><br>
            <textarea id="jobrequirement" name="jobrequirement" placeholder="Enter Job Requirement" rows="4" cols="50"><?php echo htmlspecialchars($record['jobrequirement']) ?></textarea><br>
            
            <div class="button_2">
                <button type="submit">Update Job</button>
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
            <p>&copy;Copyright <strong>Job portal</strong>.All Rights Reserved</p>
        </div>
    </div>
</footer>
</body>
</html>
