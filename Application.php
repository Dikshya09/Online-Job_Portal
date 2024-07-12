<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(!isset($_SESSION['username'])){
    header('location:adminlogin.php');
    exit(); // Ensure script stops after redirection
}

$c_email = $_SESSION['username'];
$mysqli = new mysqli("localhost", "root", "", "jobportal");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$result = $mysqli->query("
SELECT 
    a.application_id,
    js.jobseeker_name,
    js.jobseeker_email,
    j.jobTitle
FROM 
    application a
JOIN 
    jobseeker js ON a.jobseeker_email = js.jobseeker_email
JOIN 
    job j ON a.job_id = j.job_id;
");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applicant Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .Application_list {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 650px;
            border-radius: 5px;
            margin: auto;
            margin-top: 20px;
        }
        table {
            position: relative;
            height: auto;
            width: 700px;
            border-collapse: collapse;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(90, 89, 89, 0.3);
            background-color:#fff
        }
        table tr th {
            border: none;
            border-bottom: 1px solid grey;
        }
        th, td {
            padding: 12px;
            border: none;
            border-bottom: 1px solid #d3d3d3;
            text-align: center;
        }
        h3 {
            color: #338573;
            text-align: center;
            font-size: 24px;
        }
    </style>
</head>
<body>
<h3>Applicants</h3>
    <div class="Application_list">
        <table>
            <thead>
                <tr>
                    <th>Job Title</th>
                    <th>Applicant Name</th>
                    <th>Applicant Email</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['jobTitle']); ?></td>
                            <td><?php echo htmlspecialchars($row['jobseeker_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['jobseeker_email']); ?></td>
                            <td>
                                <a href="deleteapplication.php?id=<?php echo htmlspecialchars($row['application_id']); ?>">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No applicants found.</td> <!-- Corrected colspan to 4 -->
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$mysqli->close();
?>
