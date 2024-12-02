<?php
session_start();
include 'partials/_dbconnect.php';

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}

// Handle update request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $newUsername = $_POST['new_username']; // Assuming you want to change the username

    // Update the user in the database
    $updateSql = "UPDATE users SET username = ? WHERE username = ?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("ss", $newUsername, $username);
    $stmt->execute();
    $stmt->close();

    // Redirect back to the users page
    header("Location: registered_users.php");
    exit;
}

// Fetch the current user's details
$username = $_GET['username'];
$sql = "SELECT username FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Update User</title>
</head>
<body>
    <div class="container my-4">
        <h1>Update User</h1>
        <form method="post">
            <div class="mb-3">
                <label for="username" class="form-label">Current Username</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="new_username" class="form-label">New Username</label>
                <input type="text" class="form-control" id="new_username" name="new_username" required>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</body>
</html>

<?php
mysqli_close($conn);
?>