<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$email = $_SESSION['email'];
$jobTitle = $category_id = $jobType = $jobLocation = $salaryRange = $experienceLevel = $applicationDeadline = $qualification = $jobDescription = $jobrequirement = $status = '';
$errors = [];

// Database connection
$connection = new mysqli('localhost', 'root', '', 'jobportal');
if ($connection->connect_error) {
    die('Connection failed: ' . $connection->connect_error);
}

// Fetch categories for dropdown
$category_result = $connection->query("SELECT category_id, category_name FROM category");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate inputs
    if (isset($_POST['jobTitle']) && !empty($_POST['jobTitle']) && trim($_POST['jobTitle'])) {
        $jobTitle = $_POST['jobTitle'];
        if (!preg_match('/^[a-zA-Z\s]{3,50}$/', $jobTitle)) {
            $errors['jobTitle'] = 'Enter a valid job title';
        }
    } else {
        $errors['jobTitle'] = 'Enter job title';
    }
    
    if (isset($_POST['category_id']) && !empty($_POST['category_id'])) {
        $category_id = $_POST['category_id'];
    } else {
        $errors['category_id'] = 'Select job category';
    }
    
    if (isset($_POST['jobType']) && !empty($_POST['jobType'])) {
        $jobType = $_POST['jobType'];
    } else {
        $errors['jobType'] = 'Select job type';
    }
    
    if (isset($_POST['jobLocation']) && !empty($_POST['jobLocation']) && trim($_POST['jobLocation'])) {
        $jobLocation = $_POST['jobLocation'];
    } else {
        $errors['jobLocation'] = 'Enter job location';
    }
    
    if (isset($_POST['salaryRange']) && !empty($_POST['salaryRange']) && trim($_POST['salaryRange'])) {
        $salaryRange = $_POST['salaryRange'];
    } else {
        $errors['salaryRange'] = 'Enter salary range';
    }
    
    if (isset($_POST['experienceLevel']) && !empty($_POST['experienceLevel']) && trim($_POST['experienceLevel'])) {
        $experienceLevel = $_POST['experienceLevel'];
    } else {
        $errors['experienceLevel'] = 'Enter experience';
    }
    
    if (isset($_POST['applicationDeadline']) && !empty($_POST['applicationDeadline'])) {
        $applicationDeadline = $_POST['applicationDeadline'];
    } else {
        $errors['applicationDeadline'] = 'Enter deadline';
    }
    
    if (isset($_POST['qualification']) && !empty($_POST['qualification']) && trim($_POST['qualification'])) {
        $qualification = $_POST['qualification'];
    } else {
        $errors['qualification'] = 'Enter qualification';
    }
    
    if (isset($_POST['jobDescription']) && !empty($_POST['jobDescription']) && trim($_POST['jobDescription'])) {
        $jobDescription = $_POST['jobDescription'];
    } else {
        $errors['jobDescription'] = 'Enter job description';
    }
    
    if (isset($_POST['jobrequirement']) && !empty($_POST['jobrequirement'])) {
        $jobrequirement = trim($_POST['jobrequirement']);
        $requirements = explode("\n", $jobrequirement);
        $requirements = array_map('trim', $requirements);
        $requirements = array_filter($requirements, 'strlen'); 
        $jobrequirement_db = implode(", ", $requirements); 
    } else {
        $errors['jobrequirement'] = 'Enter job requirements';
    }

    if (empty($errors)) {
        try {
            $stmt = $connection->prepare("INSERT INTO job (jobTitle, category_id, jobType, jobLocation, salaryRange, experienceLevel, applicationDeadline, qualification, jobDescription, jobrequirement, c_email, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssssssss", $jobTitle, $category_id, $jobType, $jobLocation, $salaryRange, $experienceLevel, $applicationDeadline, $qualification, $jobDescription, $jobrequirement, $email, $status);
    
            if ($stmt->execute()) {
                echo "Job posted successfully";
                // header("Location: empdashboard.php?page=postedjob");
                // Update the status of jobs based on their deadlines
                $updateSql = "UPDATE job SET status = CASE WHEN applicationDeadline >= CURDATE() THEN 'Active' ELSE 'Expired' END";
                if ($connection->query($updateSql) === TRUE) {
                    echo " Status updated successfully.";
                } else {
                    echo " Error updating status: " . $connection->error;
                }
            } else {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
        } catch (Exception $ex) {
            die('Error: ' . $ex->getMessage());
        }
    }
}
$connection->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="Jobpost.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Job Post</title>
    <style>
        .error {
            color: red;
            font-size: 12px;
        }
    </style>
    <script>
        function validateForm() {
            let valid = true;
            document.querySelectorAll('.error').forEach(e => e.textContent = '');

            let jobTitle = document.forms["jobpost"]["jobTitle"].value.trim();
            if (!jobTitle) {
                document.getElementById('jobTitleError').textContent = "Enter job title";
                valid = false;
            }

            let category_id = document.forms["jobpost"]["category_id"].value;
            if (!category_id) {
                document.getElementById('category_idError').textContent = "Select job category";
                valid = false;
            }

            let jobType = document.forms["jobpost"]["jobType"].value;
            if (!jobType) {
                document.getElementById('jobTypeError').textContent = "Select job type";
                valid = false;
            }

            let jobLocation = document.forms["jobpost"]["jobLocation"].value.trim();
            if (!jobLocation) {
                document.getElementById('jobLocationError').textContent = "Enter job location";
                valid = false;
            }

            let salaryRange = document.forms["jobpost"]["salaryRange"].value.trim();
            if (!salaryRange) {
                document.getElementById('salaryRangeError').textContent = "Enter salary range";
                valid = false;
            }

            let experienceLevel = document.forms["jobpost"]["experienceLevel"].value.trim();
            if (!experienceLevel) {
                document.getElementById('experienceLevelError').textContent = "Enter experience";
                valid = false;
            }

            let applicationDeadline = document.forms["jobpost"]["applicationDeadline"].value;
            let currentDate = new Date().toISOString().split('T')[0];
            if (!applicationDeadline) {
                document.getElementById('applicationDeadlineError').textContent = "Enter deadline";
                valid = false;
            } else if (applicationDeadline < currentDate) {
                document.getElementById('applicationDeadlineError').textContent = "Deadline cannot be in the past";
                valid = false;
            }

            let qualification = document.forms["jobpost"]["qualification"].value.trim();
            if (!qualification) {
                document.getElementById('qualificationError').textContent = "Enter qualification";
                valid = false;
            }

            let jobDescription = document.forms["jobpost"]["jobDescription"].value.trim();
            if (!jobDescription) {
                document.getElementById('jobDescriptionError').textContent = "Enter job description";
                valid = false;
            }

            let jobrequirement = document.forms["jobpost"]["jobrequirement"].value.trim();
            if (!jobrequirement) {
                document.getElementById('jobrequirementError').textContent = "Enter job requirements";
                valid = false;
            }

            return valid;
        }
    </script>
</head>
<body>
    <div class="Job_post_container">
        <form action="" method="post" name="jobpost" onsubmit="return validateForm()">
            <h3>Post a job</h3>
            <div class="box_1">
                <div class="jobtitle">
                    <label for="jobTitle">Job Title:</label>
                    <span id="jobTitleError" class="error"></span><br>
                    <input type="text" id="jobTitle" name="jobTitle" value="<?php echo htmlspecialchars($jobTitle) ?>" placeholder="Enter Job Title"><br>
                </div>
                <div class="postedby">
                    <label for="qualification">Qualification:</label>
                    <span id="qualificationError" class="error"></span><br>
                    <input type="text" id="qualification" name="qualification" value="<?php echo htmlspecialchars($qualification) ?>" placeholder="Enter qualification" /><br>
                </div>
            </div>
            <div class="box_1">
                <div class="cate_gory">
                    <label for="category_id">Job Category:</label>
                    <span id="category_idError" class="error"></span><br>
                    <select id="category_id" name="category_id">
                        <option disabled selected value="">Select Job Category</option>
                        <?php while($row = $category_result->fetch_assoc()) { ?>
                            <option value="<?php echo $row['category_id']; ?>" <?php echo ($category_id == $row['category_id']) ? 'selected' : ''; ?>>
                                <?php echo $row['category_name']; ?>
                            </option>
                        <?php } ?>
                    </select><br>
                </div>
                <div class="Jobtype">
                    <label for="jobType">Job Type:</label>
                    <span id="jobTypeError" class="error"></span><br>
                    <select id="jobType" name="jobType">
                        <option disabled selected value="">Select Job Type</option>
                        <option value="full-time" <?php echo ($jobType == 'full-time') ? 'selected' : '' ?>>Full-time</option>
                        <option value="part-time" <?php echo ($jobType == 'part-time') ? 'selected' : '' ?>>Part-time</option>
                        <option value="contract" <?php echo ($jobType == 'contract') ? 'selected' : '' ?>>Contract</option>
                        <option value="temporary" <?php echo ($jobType == 'temporary') ? 'selected' : '' ?>>Temporary</option>
                    </select><br>
                </div>
            </div>
            <div class="box_1">
                <div class="location">
                    <label for="jobLocation">Job Location:</label>
                    <span id="jobLocationError" class="error"></span><br>
                    <input type="text" id="jobLocation" name="jobLocation" value="<?php echo htmlspecialchars($jobLocation) ?>" placeholder="Enter Job Location"><br>
                </div>
                <div class="salary_range">
                    <label for="salaryRange">Salary Range:</label>
                    <span id="salaryRangeError" class="error"></span><br>
                    <input type="text" id="salaryRange" name="salaryRange" value="<?php echo htmlspecialchars($salaryRange) ?>" placeholder="Enter Salary Range"><br>
                </div>
            </div>
            <div class="box_1">
                <div class="experience_1">
                    <label for="experienceLevel">Experience Level:</label>
                    <span id="experienceLevelError" class="error"></span><br>
                    <input type="text" id="experienceLevel" name="experienceLevel" value="<?php echo htmlspecialchars($experienceLevel) ?>" placeholder="Enter Experience Level"><br>
                </div>
                <div class="application_deadline">
                    <label for="applicationDeadline">Application Deadline:</label>
                    <span id="applicationDeadlineError" class="error"></span><br>
                    <input type="date" id="applicationDeadline" name="applicationDeadline" value="<?php echo htmlspecialchars($applicationDeadline) ?>"><br>
                </div>
            </div>
            <label for="jobDescription">Job Description:</label>
            <span id="jobDescriptionError" class="error"></span><br>
            <textarea id="jobDescription" name="jobDescription" placeholder="Enter Job Description" rows="4" cols="50"><?php echo htmlspecialchars($jobDescription) ?></textarea><br>

            <label for="jobrequirement">Requirements:</label>
            <span id="jobrequirementError" class="error"></span><br>
            <textarea id="jobrequirement" name="jobrequirement" placeholder="Enter Job Requirement" rows="4" cols="50"><?php echo htmlspecialchars($jobrequirement) ?></textarea><br>

            <div class="button_2">
                <button class="button" type="submit">Post</button>
            </div>
        </form>
    </div>
</body>
</html>
