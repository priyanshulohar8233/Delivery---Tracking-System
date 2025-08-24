<?php

session_start();
require 'db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_parcel'])) {
    $customer_name = trim($_POST['customer_name']);
    $customer_email = trim($_POST['customer_email']);
    $customer_phone = trim($_POST['customer_phone']); 
    $sender_address = trim($_POST['sender_address']);
    $destination_address = trim($_POST['destination_address']);
    
    $consignment_no = 'FD' . time();

    $stmt = $conn->prepare("INSERT INTO parcels (consignment_no, customer_name, customer_email, customer_phone, sender_address, destination_address) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $consignment_no, $customer_name, $customer_email, $customer_phone, $sender_address, $destination_address);
    
    if ($stmt->execute()) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'priyanshulohar18@gmail.com'; 
            $mail->Password   = 'fvdwonnlkfwpmtul'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->setFrom('no-reply@fastdelivery.com', 'Fast Delivery Admin');
            $mail->addAddress($customer_email, $customer_name);
            $mail->isHTML(true);
            $mail->Subject = "Your Parcel Has Been Shipped! Consignment #: " . $consignment_no;
            $mail->Body    = "
            <html><body>
            <h2>Hello " . htmlspecialchars($customer_name) . ",</h2>
            <p>Your parcel has been created and is now in our system.</p>
            <p>Your unique consignment number is: <strong>" . htmlspecialchars($consignment_no) . "</strong></p>
            <p>You can track its progress on our website.</p>
            <p>Thank you for choosing Fast Delivery!</p>
            </body></html>";
            $mail->send();
        } catch (Exception $e) { }
    }
    $stmt->close();
    header("Location: admin.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $parcel_id = $_POST['parcel_id'];
    $new_status = $_POST['status'];
    $new_location = trim($_POST['current_location']); 
    $stmt = $conn->prepare("UPDATE parcels SET status = ?, current_location = ? WHERE id = ?");
    $stmt->bind_param("ssi", $new_status, $new_location, $parcel_id); 
    $stmt->execute();
    $stmt->close();
    header("Location: admin.php");
    exit();
}

$parcels_result = $conn->query("SELECT * FROM parcels ORDER BY created_at DESC"); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #1e40af;
            --danger-color: #dc2626;
            --success-color: #16a34a;
            --background-color: #f1f5f9;
            --text-color: #1e293b;
        }
        body { 
            font-family: 'Segoe UI', system-ui, sans-serif; 
            background-color: var(--background-color); 
            margin: 0; 
            padding: 20px;
            color: var(--text-color);
        }
        .container { 
            max-width: 1400px; 
            margin: auto; 
            background: #fff; 
            padding: 30px; 
            border-radius: 12px; 
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }
        h1, h2 { 
            color: var(--text-color);
            font-weight: 600;
        }
        .header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            border-bottom: 2px solid #e2e8f0; 
            padding-bottom: 20px; 
            margin-bottom: 30px;
        }
        .header span {
            background-color: #f8fafc;
            padding: 8px 16px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }
        .header a { 
            text-decoration: none; 
            background-color: var(--danger-color); 
            color: white; 
            padding: 10px 20px; 
            border-radius: 6px;
            transition: all 0.2s ease;
        }
        .header a:hover { 
            background-color: #b91c1c;
            transform: translateY(-1px);
        }
        .form-container, .table-container { 
            margin-bottom: 40px;
            background-color: #f8fafc;
            padding: 24px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        form label { 
            display: block; 
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-color);
        }
        form input[type="text"], 
        form input[type="email"], 
        form input[type="tel"],
        form textarea,
        form select { 
            width: 100%; 
            padding: 10px; 
            margin-bottom: 16px; 
            border: 1px solid #cbd5e1; 
            border-radius: 6px; 
            box-sizing: border-box;
            font-size: 1rem;
            transition: border-color 0.2s ease;
        }
        form input:focus, 
        form textarea:focus,
        form select:focus { 
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        form button { 
            background-color: var(--primary-color); 
            color: white; 
            padding: 12px 24px; 
            border: none; 
            border-radius: 6px; 
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        form button:hover {
            background-color: var(--secondary-color);
            transform: translateY(-1px);
        }
        table { 
            width: 100%; 
            border-collapse: separate;
            border-spacing: 0;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1);
        }
        th, td { 
            padding: 16px; 
            border-bottom: 1px solid #e2e8f0;
            text-align: left; 
        }
        th { 
            background-color: #f8fafc;
            font-weight: 600;
            color: var(--text-color);
        }
        tr:last-child td {
            border-bottom: none;
        }
        tr:hover {
            background-color: #f8fafc;
        }
        .status-form { 
            display: flex; 
            flex-direction: column;
            gap: 8px;
        }
        .status-form-row { 
             display: flex; 
             gap: 8px;
        }
        .status-form select { 
            margin-bottom: 0;
            flex-grow: 1;
        }
        .status-form button {
            padding: 8px 16px;
            white-space: nowrap;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>Admin Dashboard</h1>
        <span>Welcome, <strong><?php echo htmlspecialchars($_SESSION['admin_username']); ?></strong>!</span>
        <a href="logout.php">Logout</a>
    </div>

    <div class="form-container">
        <h2>Create New Parcel</h2>
        <form action="admin.php" method="post">
            <label for="customer_name">Customer Name:</label>
            <input type="text" id="customer_name" name="customer_name" required>
            
            <label for="customer_email">Customer Email:</label>
            <input type="email" id="customer_email" name="customer_email" required>
            
            <label for="customer_phone">Customer Phone:</label>
            <input type="tel" id="customer_phone" name="customer_phone">
            
            <label for="sender_address">Sender Address:</label>
            <textarea id="sender_address" name="sender_address" rows="3" required></textarea>
            
            <label for="destination_address">Destination Address:</label>
            <textarea id="destination_address" name="destination_address" rows="3" required></textarea>
            
            <button type="submit" name="add_parcel">Add Parcel</button>
        </form>
    </div>

    <div class="table-container">
        <h2>All Parcels</h2>
        <table>
            <thead>
                <tr>
                    <th>Consignment No.</th>
                    <th>Customer</th>
                    <th>Destination</th>
                    <th>Status & Location</th> <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $parcels_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['consignment_no']); ?></td>
                    <td>
                        <?php echo htmlspecialchars($row['customer_name']); ?><br>
                        <small><?php echo htmlspecialchars($row['customer_email']); ?></small><br>
                        <small><strong>Phone:</strong> <?php echo htmlspecialchars($row['customer_phone'] ?? 'N/A'); ?></small>
                    </td>
                    <td><?php echo htmlspecialchars($row['destination_address']); ?></td>
                    
                    <td>
                        <strong>Status:</strong> <?php echo htmlspecialchars($row['status']); ?><br>
                        <small><strong>Location:</strong> <?php echo htmlspecialchars($row['current_location'] ?? 'At Origin'); ?></small>
                    </td>

                    <td>
                        <form action="admin.php" method="post" class="status-form">
                            <input type="hidden" name="parcel_id" value="<?php echo $row['id']; ?>">
                            
                            <input type="text" name="current_location" placeholder="Update Location" value="<?php echo htmlspecialchars($row['current_location'] ?? ''); ?>">

                            <div class="status-form-row">
                                <select name="status">
                                    <option value="Created" <?php if ($row['status'] == 'Created') echo 'selected'; ?>>Created</option>
                                    <option value="In Transit" <?php if ($row['status'] == 'In Transit') echo 'selected'; ?>>In Transit</option>
                                    <option value="Out for Delivery" <?php if ($row['status'] == 'Out for Delivery') echo 'selected'; ?>>Out for Delivery</option>
                                    <option value="Delivered" <?php if ($row['status'] == 'Delivered') echo 'selected'; ?>>Delivered</option>
                                    <option value="Cancelled" <?php if ($row['status'] == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
                                </select>
                                <button type="submit" name="update_status">Update</button>
                            </div>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
<?php
$conn->close();
?> 