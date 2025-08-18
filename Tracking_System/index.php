<?php include 'includes/db.php'; ?>

<!DOCTYPE html>
<html>
    <head>
        <title>Track Your Package</title>
    </head>    

    <body>
        <h2>Package Tracking<h2>
        <form method=""GET>
            <input type="text" name="tracking_number" placeholder="Enter Tracking ID" required>
            <button type="submit">Track</button>
        </form>
        
        <?php
        if(isset($_GET['tracking_number'])) {
            $tracking_number = $GET['tracking_number'];
            $result = $conn->query("SELECT * FROM packages WHERE tracking_number = '$tracking_number'");

            if($result->num_rows > 0) {
                $package = $result->fetch_assoc();
                echo "<h3>Status: ".$package['status'] . "</h3>";
                echo "<p>Last Update: " . $package['last_update'] . "</p>";
            }
            else {
                echo "<p>NO package found with this Tracking ID.</p>";
            }
        }
        ?>

    </body>
    </html>