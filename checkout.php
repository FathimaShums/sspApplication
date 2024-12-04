<?php
session_start();
require 'config.php'; 

// Check if the user is logged in
$loggedIn = isset($_SESSION['user']);

// For logged-in users, retrieve details from the session (Customer table)
if ($loggedIn) {
    $customerID = $_SESSION['user']['customerID'];
    $customerName = $_SESSION['user']['firstName'] . ' ' . $_SESSION['user']['lastName'];
    $address = $_SESSION['user']['address'];
    $phone = $_SESSION['user']['phone'];
}

// Initialize variables for non-logged-in users
$customerName = $address = $phone = ''; // Reset variables

// Handle form submission (placing the order)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['place_order'])) {
    // For non-logged-in users
    if (!$loggedIn) {
        $customerName = $_POST['full_name'];
        $address = $_POST['address'];
        $phone = $_POST['phone'];
        $customerID = NULL; 
    }

    // Get cart data
    $cart = $_SESSION['cart'] ?? [];
    $totalAmount = 0; // Initialize totalAmount

    // Calculate the total price of the cart
    foreach ($cart as $item) {
        $totalAmount += $item['price'] * $item['quantity'];
    }

    // Check if the cart is not empty
    if (!empty($cart)) {
        try {
            // Insert order into `TheOrder` table
            $orderDate = date('Y-m-d H:i:s');
            $orderType = 'Delivery'; // As per requirement
            $orderStatus = 'pending'; // Default status
            $deliveryManID = NULL; // DeliveryManID will be assigned later
            
            // Prepare SQL to insert the order
            $stmt = $pdo->prepare("INSERT INTO TheOrder (customerName, totalAmount, phone, address, customerID, deliveryManID, orderDate, orderType, orderStatus) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

            // Execute the statement
            if ($stmt->execute([$customerName, $totalAmount, $phone, $address, $customerID, $deliveryManID, $orderDate, $orderType, $orderStatus])) {
                echo "<script>
                        alert('Order has been placed successfully!');
                        window.location.href = 'index.php';
                      </script>";
            } else {
                echo "Failed to place the order. Please try again.";
            }
        } catch (PDOException $e) {
            // Display error message if there is a PDO exception
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Cart is empty. Cannot place an order.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<header class="bg-green-500 text-white py-4">
    <div class="container mx-auto flex justify-between items-center">
        <h1 class="text-2xl font-bold">Restaurant Menu</h1>
        <nav class="space-x-4">
            <a href="index.php" class="hover:underline">Home</a>
            <a href="checkout.php" class="font-bold">Cart</a>

            <!-- Check if user is logged in -->
            <?php if ($loggedIn): ?>
                <a href="myorders.php" class="hover:underline">My Orders</a>
                <a href="customerprofile.php" class="hover:underline">My Profile</a>
                <form action="logout.php" method="POST" class="inline">
                    <button type="submit" class="hover:underline">Logout</button>
                </form>
            <?php else: ?>
                <a href="login.php" class="hover:underline">Log In</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<body class="bg-gray-100 text-gray-800">
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6">Checkout</h1>

        <!-- Display user details if logged in or form to fill in if not logged in -->
        <div class="mb-4">
            <h2 class="text-xl font-semibold">Your Details</h2>
            <?php if ($loggedIn): ?>
                <!-- If logged in, show the customer details -->
                <p>Name: <?php echo htmlspecialchars($customerName); ?></p>
                <p>Address: <?php echo htmlspecialchars($address); ?></p>
                <p>Phone: <?php echo htmlspecialchars($phone); ?></p>
            <?php else: ?>
                <!-- If not logged in, show the form to collect details -->
                <form method="POST">
                    <div class="mb-4">
                        <label for="full_name" class="block text-sm font-medium text-gray-700">Full Name</label>
                        <input type="text" name="full_name" id="full_name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div class="mb-4">
                        <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                        <input type="text" name="address" id="address" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div class="mb-4">
                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                        <input type="text" name="phone" id="phone" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
            <?php endif; ?>
        </div>

        <!-- Order Summary -->
        <div class="mb-4">
            <h2 class="text-xl font-semibold">Order Summary</h2>
            <ul>
                <?php
                $totalAmount = 0; // Recalculate the total amount
                foreach ($_SESSION['cart'] as $item):
                    $totalAmount += $item['price'] * $item['quantity']; // Calculate total price
                ?>
                    <li><?php echo htmlspecialchars($item['name']); ?> - $<?php echo number_format($item['price'], 2); ?> x <?php echo $item['quantity']; ?></li>
                <?php endforeach; ?>
            </ul>
            <p class="font-bold">Total: $<?php echo number_format($totalAmount, 2); ?></p>
        </div>

        <!-- Place Order Button -->
        <form method="POST">
            <button type="submit" name="place_order" class="bg-green-500 text-white py-2 px-4 rounded">Place Order</button>
        </form>
    </div>
</body>
</html>
