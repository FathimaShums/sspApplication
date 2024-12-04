<?php
session_start();

require 'config.php';

// Ensure the user is logged in and is a delivery man
if (!isset($_SESSION['user']) || $_SESSION['user']['designation'] != 'Delivery-Man') {
    header("Location: login.php");
    exit;
}

// Get the logged-in delivery man's username from the session
$deliveryManID = $_SESSION['user']['username'];

// Fetch pending orders assigned to this delivery man
$stmt = $pdo->prepare("SELECT * FROM TheOrder WHERE orderStatus = 'pending' AND deliveryManID = ?");
$stmt->execute([$deliveryManID]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle order status update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $orderID = $_POST['orderID'];

    // Update the order status to 'Complete'
    $stmt = $pdo->prepare("UPDATE TheOrder SET orderStatus = 'complete' WHERE orderID = ?");
    $stmt->execute([$orderID]);

    // Reload the page to reflect updated status
    header("Location: deliveryMan_dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Man Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script>
        function confirmCompletion(orderID) {
            if (confirm("Are you sure you want to mark this order as complete?")) {
                document.getElementById('completeForm-' + orderID).submit();
            }
        }
    </script>
</head>
<header>
<form action="logout.php" method="POST" class="inline">
    <button type="submit" class="hover:underline">Logout</button>
</form>
</header>

<body class="bg-gray-100 text-gray-800">
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6">Your Assigned Pending Orders</h1>

        <?php if (count($orders) > 0): ?>
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-2 px-4">Order ID</th>
                        <th class="py-2 px-4">Customer Name</th>
                        <th class="py-2 px-4">Address</th>
                        <th class="py-2 px-4">Phone</th>
                        <th class="py-2 px-4">Total Amount</th>
                        <th class="py-2 px-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($order['orderID']); ?></td>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($order['customerName']); ?></td>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($order['address']); ?></td>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($order['phone']); ?></td>
                            <td class="border px-4 py-2">$<?php echo number_format($order['totalAmount'], 2); ?></td>
                            <td class="border px-4 py-2">
                                <!-- Form to mark order as complete -->
                                <form id="completeForm-<?php echo $order['orderID']; ?>" method="POST">
                                    <input type="hidden" name="orderID" value="<?php echo $order['orderID']; ?>">
                                    <button type="button" onclick="confirmCompletion(<?php echo $order['orderID']; ?>)" class="bg-green-500 text-white py-2 px-4 rounded">Mark as Complete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-center">No pending orders assigned to you.</p>
        <?php endif; ?>
    </div>
</body>

</html>
