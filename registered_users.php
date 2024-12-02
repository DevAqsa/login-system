<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}

// Handle update request
if (isset($_POST['update'])) {
    $usernameToUpdate = $_POST['username'];
    
    // Redirect to an update page (you can create an update.php)
    header("Location: update.php?username=" . urlencode($usernameToUpdate));
    exit;
}

// Include database connection
include 'partials/_dbconnect.php';

// Handle delete request
if (isset($_POST['delete'])) {
    $usernameToDelete = $_POST['username'];
    $deleteSql = "DELETE FROM users WHERE username = ?";
    $stmt = $conn->prepare($deleteSql);
    $stmt->bind_param("s", $usernameToDelete);
    $stmt->execute();
    $stmt->close();
}

// Fetch all registered users
$sql = "SELECT username, dt FROM users ORDER BY dt DESC";
$result = mysqli_query($conn, $sql);
?>

<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Registered Users</title>
</head>
<body>
    <?php require 'partials/_nav.php'?>

    <div class="container my-4">
        <h1 class="text-center mb-4">Registered Users</h1>
        
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Username</th>
                        <th>Registration Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (mysqli_num_rows($result) > 0) {
                        $counter = 1;
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $counter . "</td>";
                            echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                            echo "<td>" . $row['dt'] . "</td>";
                            // Check if the current user is logged in and matches the row
                            // $status = (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && $_SESSION['username'] === $row['username']) ? 'Active' : 'Inactive';
                            // echo "<td> <button btn btn-primary >""</button> </td>";

                            $status = (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && $_SESSION['username'] === $row['username']) ? 'Active' : 'Inactive';

if ($status === 'Active') {
    echo "<td><button class='btn btn-success'>Active</button></td>";
} else {
    echo "<td><button class='btn btn-danger'>Inactive</button></td>";
}

                            echo "<td>
                                    <form method='post' style='display:inline;'>
                                        <input type='hidden' name='username' value='" . htmlspecialchars($row['username']) . "'>
                                        <button type='submit' name='delete' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this user?\");'>Delete</button>
                                    </form>

                                    

                                    <form method='post' style='display:inline;'>
            <input type='hidden' name='username' value='" . htmlspecialchars($row['username']) . "'>
            <button type='submit' name='update' class='btn btn-warning btn-sm'>Update</button>
        </form>
                                  </td>";

                            
                            echo "</tr>";
                            $counter++;
                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-center'>No registered users found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>

<?php
// Close the database connection
mysqli_close($conn);
?>