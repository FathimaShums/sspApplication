<?php
session_start();

// Check if customer is logged in
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit;
}

require 'config.php';

// Get the logged-in customer's ID
$customerID = $_SESSION['user']['customerID'];

// Fetch pending orders for the customer
$stmt = $pdo->prepare("SELECT * FROM TheOrder WHERE customerID = ? AND orderStatus = 'pending'");
$stmt->execute([$customerID]);
$pendingOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch completed orders for the customer
$stmt = $pdo->prepare("SELECT * FROM TheOrder WHERE customerID = ? AND orderStatus = 'complete'");
$stmt->execute([$customerID]);
$completedOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<header class="bg-green-500 text-white py-4">
    <div class="container mx-auto flex justify-between items-center">
        <h1 class="text-2xl font-bold">Restaurant Menu</h1>
        <nav class="space-x-4">
            <!-- Always display Home and Cart links -->
            <a href="index.php" class="hover:underline">Home</a>
            <a href="checkout.php" class="hover:underline">Cart</a>

            <!-- Check if user is logged in -->
            <?php if (isset($_SESSION['user'])): ?>
                <a href="myorders.php" class="font-bold">My Orders</a>
                <a href="customerprofile.php" class="hover:underline">My Profile</a>
                <form action="logout.php" method="POST" class="inline">
                    <button type="submit" class="hover:underline">Logout</button>
                </form>
            <?php else: ?>
                <a href="login.php" class="hover:underline">Log In</a>
            <?php endif; ?>
        </nav>

        <!-- Cart button remains the same -->
        <div>
            <button id="cart-btn" class="relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.34 2M7 13h10l3.24-8H6.6M16 13a4 4 0 11-8 0m-4 0h4m6 0h6" />
                </svg>
            </button>
        </div>
    </div>
</header>
<body class="bg-gray-100 text-gray-800">

    <!-- Header -->
    <header class="bg-green-500 text-white py-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold">My Orders</h1>
            <nav class="space-x-4">
                <a href="index.php" class="hover:underline">Home</a>
                <a href="checkout.php" class="hover:underline">Cart</a>
                <a href="customerprofile.php" class="font-bold">Profile</a>
                <form action="logout.php" method="POST" class="inline">
                    <button type="submit" class="hover:underline">Logout</button>
                </form>
            </nav>
        </div>
    </header>

    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-bold mb-6">Pending Orders</h2>

        <!-- Table for Pending Orders -->
        <table class="min-w-full bg-white border">
            <thead>
                <tr>
                    <th class="py-2 px-4 border">Order ID</th>
                    <th class="py-2 px-4 border">Order Date</th>
                    <th class="py-2 px-4 border">Order Type</th>
                    <th class="py-2 px-4 border">Total Amount</th>
                    <th class="py-2 px-4 border">Order Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($pendingOrders) > 0): ?>
                    <?php foreach ($pendingOrders as $order): ?>
                        <tr>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($order['orderID']); ?></td>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($order['orderDate']); ?></td>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($order['orderType']); ?></td>
                            <td class="border px-4 py-2">$<?php echo number_format($order['totalAmount'], 2); ?></td>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($order['orderStatus']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center py-4">No pending orders found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Completed Orders Section -->
        <h2 class="text-2xl font-bold mt-12 mb-6">Completed Orders</h2>

        <!-- Table for Completed Orders -->
        <table class="min-w-full bg-white border">
            <thead>
                <tr>
                    <th class="py-2 px-4 border">Order ID</th>
                    <th class="py-2 px-4 border">Order Date</th>
                    <th class="py-2 px-4 border">Order Type</th>
                    <th class="py-2 px-4 border">Total Amount</th>
                    <th class="py-2 px-4 border">Order Status</th>
                    <th class="py-2 px-4 border">Delivery Man</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($completedOrders) > 0): ?>
                    <?php foreach ($completedOrders as $order): ?>
                        <tr>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($order['orderID']); ?></td>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($order['orderDate']); ?></td>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($order['orderType']); ?></td>
                            <td class="border px-4 py-2">$<?php echo number_format($order['totalAmount'], 2); ?></td>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($order['orderStatus']); ?></td>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($order['deliveryManID']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-4">No completed orders found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
