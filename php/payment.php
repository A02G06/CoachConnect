<?php
session_start();

// Get booking/payment details from session or POST (adjust as needed)
$amount = $_SESSION['payment_amount'] ?? $_POST['payment'] ?? 0;
$booking_id = $_SESSION['booking_id'] ?? $_POST['booking_id'] ?? null;

// For demonstration, you can set $amount from the booking or POST
if (!$amount) {
    $amount = 300; // Default/fallback amount
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment - Coach Connect</title>
    <link rel="stylesheet" href="../css/booking.css">
    <style>
        .payment-container {
            max-width: 400px;
            margin: 60px auto;
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            text-align: center;
        }
        .payment-amount {
            font-size: 2em;
            color: #2e7d32;
            margin: 20px 0;
        }
        .pay-btn {
            background: #2e7d32;
            color: #fff;
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            font-size: 1.1em;
            cursor: pointer;
        }
        .pay-btn:disabled {
            background: #aaa;
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <h2>Payment Required</h2>
        <p>Please pay the following amount to confirm your booking:</p>
        <div class="payment-amount">&#8377; <?php echo htmlspecialchars($amount); ?></div>
        <form action="payment_handler.php" method="POST">
            <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($booking_id); ?>">
            <input type="hidden" name="amount" value="<?php echo htmlspecialchars($amount); ?>">
            <!-- Simulate payment gateway -->
            <button type="submit" class="pay-btn">Pay Now</button>
        </form>
        <p style="margin-top:20px;color:#888;">(This is a demo payment page. Integrate with your payment gateway as needed.)</p>
    </div>
</body>
</html>