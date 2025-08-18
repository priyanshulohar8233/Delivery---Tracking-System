<?php

$host = "localhost";
$user = "root";
$password = "";
$db = "delivery_db";
$conn = new mysqli($host, $user, $password, $db);

if ($conn->connect_error) {
    die("Database connection failed:" .$conn->connect_error);
}

$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$address = $_POST['address'] ?? '';
$city = $_POST['city'] ?? '';
$zipcode = $_POST['zipcode'] ?? '';

$order_id = "ORD-" . date("Ymd") . "-" . rand(1000, 9999);

$delivery_date = $_POST['delivery_date'] ?? '';

$token = "DEL-" .date("Ymd") . "_" . rand(10000, 99999);

$sql = "INSERT INTO orders (name, email, phone, address, city, zipcode, order_id, delivery_date, token, status)
        VALUES ('$name','$email','$phone','$address','$city','$zipcode','$order_id','$delivery_date','$token','Processing')";


if ($conn->query($sql) === TRUE) {
    echo "Order Successful placed!<br>";
    echo "Your Tracking Token: <b>$token</b><br>";
    echo "<a href='track.php?token=$token'>Track Your Order</a>";
} 
else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>