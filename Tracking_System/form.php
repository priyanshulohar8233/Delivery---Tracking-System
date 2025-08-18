<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content ="width=device-width,initial-scale=1.0">
        <title>Delivery Details Form</title>
        <style>
            body {
                font-family:Arial, sans-serif;
                background-color: #e1e7e8ff;
                paddings: 20px;
            }
            .form-container {
                background: skyblue;
                padding: 20px;
                max-width: 550px;
                margin: auto;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            }
            h2 {
                text-align: center;
                color: #333;
            }
            label {
                front-weight: bold;
                display: block;
                margin-top: 10px;
            }
            input, textarea, select {
                width: 100%;
                padding: 8px;
                margin-top: 5px;
                border: 1px solid #ccc;
                border-radius: 5px;
            }
            button {
                margin-top: 15px;
                background: rgba(244, 7, 7, 0.4);
                color: black;
                border: black;
                padding: 10px;
                border-radius: 10px;
                width: 100%;
                font-size: 16px;
                cursor: pointer;
            }
            button:hover {
                background: #12ef5cff;
            }
        </style>    
    </head>
    <body>
        <div class="form-container">
            <h2>Delivery Details Form</h2>
            <form action="save_delivery.php" method="post">
                <label>Full Name:</label>
                <input type="text" name="name" placeholder="Full Name" required><br>

                <label>Email Address:</label>
                <input type="email" name="email" placeholder="Email" required><br>

                <label>Phone Number:</label>
                <input type="tel" name="phone" placeholder="Phone" required><br>

                <label>Address:</label>
                <input type="text" name="address" placeholder="Address" required><br>

                <label>City:</label>
                <input type="text" name="city" placeholder="City" required><br>

                <label>Zip Code:</label>
                <input type="text" name="zip_code" placeholder="Zipcode" required><br>

                <label>Preferred Delivery Date:</label>
                <input type="date" name="delivery_date">

                <button type="Submit">Place Order</button>
            </form>
        </div> 
        </body>
        </html>           



