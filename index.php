<?php
$host = 'db';
$user = getenv('MYSQL_USER');
$pass = getenv('MYSQL_PASSWORD');
$dbname = getenv('MYSQL_DATABASE');

$conn = new mysqli($host, $user, $pass, $dbname);

// Retry logic for DB connection
$maxRetries = 20;
$attempt = 0;
while ($conn->connect_errno && $attempt < $maxRetries) {
    echo "Waiting for DB...<br>";
    $conn = @new mysqli($host, $user, $pass, $dbname);
    $attempt++;
    sleep(3);
}

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create table if not exists
$conn->query("CREATE TABLE IF NOT EXISTS names (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255))");

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST["name"])) {
    $name = $conn->real_escape_string($_POST["name"]);
    $conn->query("INSERT INTO names (name) VALUES ('$name')");
}

$result = $conn->query("SELECT * FROM names");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Name Registration</title>
</head>
<body>
    <h1>Register Name</h1>
    <form method="POST">
        <input type="text" name="name" placeholder="Enter name" required>
        <button type="submit">Submit</button>
    </form>

    <h2>Registered Names:</h2>
    <ul>
        <?php while ($row = $result->fetch_assoc()): ?>
            <li><?php echo htmlspecialchars($row["name"]); ?></li>
        <?php endwhile; ?>
    </ul>
</body>
</html>
