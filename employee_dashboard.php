<?php
session_start();

require 'config.php'; 

// Check if the form to assign a delivery man is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['orderID'], $_POST['deliveryManID'])) {
    $orderID = $_POST['orderID'];
    $deliveryManID = $_POST['deliveryManID'];

    // Update the TheOrder table to assign the selected delivery man
    $stmt = $pdo->prepare("UPDATE TheOrder SET deliveryManID = ? WHERE orderID = ?");
    $stmt->execute([$deliveryManID, $orderID]);

    // Reload the page to reflect changes
    header("Location: employee_dashboard.php");
    exit;
}

// Get pending orders
$stmt = $pdo->query("SELECT * FROM TheOrder WHERE orderStatus = 'pending'");
$pendingOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get completed orders
$stmt = $pdo->query("SELECT * FROM TheOrder WHERE orderStatus = 'complete'");
$completedOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get delivery men
$stmt = $pdo->prepare("SELECT username FROM employee WHERE designation = 'Delivery-Man'");
$stmt->execute();
$deliveryMen = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<header>
<form action="logout.php" method="POST" class="inline">
    <button type="submit" class="hover:underline">Logout</button>
</form>
</header>
<body class="bg-gray-100 text-gray-800">
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6">Pending Orders</h1>

        <!-- Table for Pending Orders -->
        <table class="min-w-full bg-white">
            <thead>
                <tr>
                    <th class="py-2 px-4">Order ID</th>
                    <th class="py-2 px-4">Customer ID</th>
                    <th class="py-2 px-4">Total Amount</th>
                    <th class="py-2 px-4">Delivery Man</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pendingOrders as $order): ?>
                    <tr>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($order['orderID']); ?></td>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($order['customerID']); ?></td>
                        <td class="border px-4 py-2">$<?php echo number_format($order['totalAmount'], 2); ?></td>
                        <td class="border px-4 py-2">
                            <!-- Show assigned delivery man or prompt to assign one -->
                            <?php if (!empty($order['deliveryManID'])): ?>
                                <?php echo htmlspecialchars($order['deliveryManID']); ?>
                            <?php else: ?>
                                <form method="POST">
                                    <input type="hidden" name="orderID" value="<?php echo $order['orderID']; ?>">
                                    <select name="deliveryManID" required>
                                        <option value="" disabled selected>Select Delivery Man</option>
                                        <?php foreach ($deliveryMen as $man): ?>
                                            <option value="<?php echo htmlspecialchars($man['username']); ?>">
                                                <?php echo htmlspecialchars($man['username']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded">Assign Delivery Man</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Completed Orders Section -->
        <h1 class="text-2xl font-bold mt-12 mb-6">Completed Orders</h1>

        <!-- Table for Completed Orders -->
        <table class="min-w-full bg-white">
            <thead>
                <tr>
                    <th class="py-2 px-4">Order ID</th>
                    <th class="py-2 px-4">Customer ID</th>
                    <th class="py-2 px-4">Total Amount</th>
                    <th class="py-2 px-4">Delivery Man</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($completedOrders as $order): ?>
                    <tr>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($order['orderID']); ?></td>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($order['customerID']); ?></td>
                        <td class="border px-4 py-2">$<?php echo number_format($order['totalAmount'], 2); ?></td>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($order['deliveryManID']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
