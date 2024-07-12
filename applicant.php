<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['email'])) {
    header('location:emplogin.php');
}

$c_email = $_SESSION['email'];
$mysqli = new mysqli("localhost", "root", "", "jobportal");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$stmt = $mysqli->prepare("
    SELECT  j.jobTitle, a.jobseeker_email, js.mobno, js.jobseeker_name
    FROM application a
    JOIN job j ON a.job_id = j.job_id
    JOIN jobseeker js ON a.jobseeker_email = js.jobseeker_email
    WHERE j.c_email = ?
");
$stmt->bind_param("s", $c_email);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applicant Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="job-list.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="section">
    
    
    <h2>Applicants</h2>
    <table>
        <thead>
            <tr>
                
                <th>Job Title</th>
                <th>Applicant Name</th>
                <th>Contact</th>
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
                        <td><?php echo htmlspecialchars($row['mobno']); ?></td>
                        <td><?php echo htmlspecialchars($row['jobseeker_email']); ?></td>
                        <td>
                            <a href="preview_profile.php?email=<?php echo urlencode($row['jobseeker_email']); ?>">View Profile</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No applicants found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php
    $stmt->close();
    $mysqli->close();
    ?>
    </div>
</body>
</html>
