<?php

$host = "localhost";
$user = "root";
$password = "";
$db = "delivery_db";
$conn = new mysqli($host, $user, $password, $db);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $sql = "SELECT * FROM orders WHERE token='$token'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo "<h2>Order Status</h2>";
        echo "Name: " .$row['name'] . "</br>";
        echo "Order ID: " . $row['order_id'] . "<br>";
        echo "Status: <b>" . $row['status'] . "</b><br>";
    }
    else {
        echo "Invalid token Number";
    }
}    
    else {
        echo '<form method="GET">
                  <label>Enter Token Number:</label>
                  <input type="text" name="token" required>
                  <button type="submit">Track</button>
              </form>';

    }

?>