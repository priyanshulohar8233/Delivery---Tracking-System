<?php

require 'db.php';
$parcel_details = null;
$error_message = '';

if (isset($_GET['consignment_no']) && !empty($_GET['consignment_no'])) {
    $consignment_no = trim($_GET['consignment_no']);

    $stmt = $conn->prepare("SELECT * FROM parcels WHERE consignment_no = ?");
    $stmt->bind_param("s", $consignment_no);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $parcel_details = $result->fetch_assoc();
    } else {
        $error_message = "No parcel found with that consignment number.";
    }
    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Your Parcel</title>
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #1e40af;
            --background-color: #f1f5f9;
            --text-color: #1e293b;
            --error-color: #dc2626;
            --success-color: #16a34a;
            --warning-color: #ca8a04;
            --info-color: #2563eb;
        }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background-color: var(--background-color);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
            color: var(--text-color);
        }

        .track-container {
            background: #fff;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            width: 100%;
            max-width: 600px;
            text-align: center;
        }

        h1 {
            color: var(--text-color);
            margin-bottom: 2rem;
            font-weight: 600;
            font-size: 2rem;
        }

        .track-form {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
            gap: 0.5rem;
        }

        .track-form input[type="text"] {
            width: 70%;
            padding: 0.875rem;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 1rem;
            transition: all 0.2s ease;
            background-color: #f8fafc;
        }

        .track-form input[type="text"]:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            background-color: #fff;
        }

        .track-form button {
            padding: 0.875rem 1.5rem;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s ease;
            font-size: 1rem;
        }

        .track-form button:hover {
            background-color: var(--secondary-color);
            transform: translateY(-1px);
        }

        .results-container {
            text-align: left;
            background-color: #f8fafc;
            padding: 1.5rem;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            margin-top: 1rem;
        }

        .results-container h2 {
            color: var(--text-color);
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 0.75rem;
            margin-top: 0;
            font-weight: 600;
        }

        .results-container p {
            font-size: 1rem;
            line-height: 1.6;
            margin: 1rem 0;
        }

        .results-container strong {
            color: var(--text-color);
            font-weight: 500;
        }

        .status {
            display: inline-block;
            font-size: 1rem;
            font-weight: 600;
            padding: 0.625rem 1rem;
            border-radius: 6px;
            margin-top: 0.5rem;
            transition: all 0.2s ease;
        }

        .status-created {
            background-color: #eff6ff;
            color: var(--info-color);
            border: 1px solid #bfdbfe;
        }

        .status-in-transit {
            background-color: #fef3c7;
            color: var(--warning-color);
            border: 1px solid #fde68a;
        }

        .status-delivered {
            background-color: #f0fdf4;
            color: var(--success-color);
            border: 1px solid #bbf7d0;
        }
        
        .status-out-for-delivery {
            background-color: #fef3c7;
            color: var(--warning-color);
            border: 1px solid #fde68a;
        }

        .status-cancelled {
            background-color: #fef2f2;
            color: var(--error-color);
            border: 1px solid #fecaca;
        }

        .error {
            color: var(--error-color);
            background-color: #fee2e2;
            padding: 0.75rem;
            border-radius: 6px;
            margin-top: 1rem;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <div class="track-container">
        <h1>📦 Track Your Parcel</h1>
        <form action="track.php" method="get" class="track-form">
            <input type="text" name="consignment_no" placeholder="Enter Consignment Number" value="<?php echo isset($_GET['consignment_no']) ? htmlspecialchars($_GET['consignment_no']) : ''; ?>" required>
            <button type="submit">Track</button>
        </form>

        <?php if ($parcel_details): ?>
            <div class="results-container">
                <h2>Tracking Details</h2>
                <p><strong>Consignment No:</strong> <?php echo htmlspecialchars($parcel_details['consignment_no']); ?></p>
                <p><strong>Destination:</strong> <?php echo htmlspecialchars($parcel_details['destination_address']); ?></p>
                
                <p><strong>Current Location:</strong> <?php echo htmlspecialchars($parcel_details['current_location'] ?? 'At Origin Facility'); ?></p>

                <p>
                    <strong>Status:</strong><br>
                    <?php 
                        $status_class = 'status-' . strtolower(str_replace(' ', '-', $parcel_details['status']));
                        echo '<span class="status ' . $status_class . '">' . htmlspecialchars($parcel_details['status']) . '</span>';
                    ?>
                </p>
            </div>
        <?php elseif (!empty($error_message)): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php endif; ?>
    </div>
</body>
</html>