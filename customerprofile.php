<?php
session_start();
require 'config.php'; 

// Check if the user is logged in
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit;
}

$customer = $_SESSION['user'];

// Handle profile update (excluding username and password)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    // Update the customer profile
    $stmt = $pdo->prepare("UPDATE customer SET firstName = ?, lastName = ?, email = ?, phone = ?, address = ? WHERE customerID = ?");
    $stmt->execute([$firstName, $lastName, $email, $phone, $address, $customer['customerID']]);

    // Update session data
    $_SESSION['user']['firstName'] = $firstName;
    $_SESSION['user']['lastName'] = $lastName;
    $_SESSION['user']['email'] = $email;
    $_SESSION['user']['phone'] = $phone;
    $_SESSION['user']['address'] = $address;

    $message = "Profile updated successfully!";
}

// Handle username and password update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_credentials'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Update username and password
    $stmt = $pdo->prepare("UPDATE customer SET username = ?, password = ? WHERE customerID = ?");
    $stmt->execute([$username, $password, $customer['customerID']]);

    // Update session data
    $_SESSION['user']['username'] = $username;

    $message = "Username and password updated successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 text-gray-800">
    
    <!-- Header with navigation links -->
    <header class="bg-green-500 text-white py-4">
    <div class="container mx-auto flex justify-between items-center">
        <h1 class="text-2xl font-bold">Restaurant Menu</h1>
        <nav class="space-x-4">
            
            <a href="index.php" class="font-bold">Home</a>
            <a href="checkout.php" class="hover:underline">Cart</a>

            <!-- Check if user is logged in -->
            <?php if (isset($_SESSION['user'])): ?>
                <a href="myorders.php" class="hover:underline">My Orders</a>
                <a href="customerprofile.php" class="font-bold">My Profile</a>
                <form action="logout.php" method="POST" class="inline">
                    <button type="submit" class="hover:underline">Logout</button>
                </form>
            <?php else: ?>
                <a href="login.php" class="hover:underline">Log In</a>
            <?php endif; ?>
        </nav>

        
        <div>
            <button id="cart-btn" class="relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.34 2M7 13h10l3.24-8H6.6M16 13a4 4 0 11-8 0m-4 0h4m6 0h6" />
                </svg>
            </button>
        </div>
    </div>
</header>


    <div class="container mx-auto p-6">
        <!-- Success message -->
        <?php if (!empty($message)) : ?>
            <div class="bg-green-100 text-green-700 p-2 rounded mb-4">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Profile update section -->
        <div class="bg-white p-6 rounded shadow-md mb-6">
            <h2 class="text-xl font-bold mb-4">Update Profile Information</h2>
            <form method="POST">
                <input type="hidden" name="update_profile" value="1">
                <div class="mb-4">
                    <label for="firstName" class="block text-gray-700 font-semibold mb-2">First Name</label>
                    <input type="text" id="firstName" name="firstName" value="<?php echo htmlspecialchars($customer['firstName']); ?>" class="w-full p-2 border rounded" required>
                </div>
                <div class="mb-4">
                    <label for="lastName" class="block text-gray-700 font-semibold mb-2">Last Name</label>
                    <input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars($customer['lastName']); ?>" class="w-full p-2 border rounded" required>
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 font-semibold mb-2">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($customer['email']); ?>" class="w-full p-2 border rounded" required>
                </div>
                <div class="mb-4">
                    <label for="phone" class="block text-gray-700 font-semibold mb-2">Phone</label>
                    <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($customer['phone']); ?>" class="w-full p-2 border rounded" required>
                </div>
                <div class="mb-4">
                    <label for="address" class="block text-gray-700 font-semibold mb-2">Address</label>
                    <textarea id="address" name="address" class="w-full p-2 border rounded" required><?php echo htmlspecialchars($customer['address']); ?></textarea>
                </div>
                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">Update Profile</button>
            </form>
        </div>

        <!-- Username and password update section -->
        <div class="bg-white p-6 rounded shadow-md">
            <h2 class="text-xl font-bold mb-4">Update Username and Password</h2>
            <form method="POST">
                <input type="hidden" name="update_credentials" value="1">
                <div class="mb-4">
                    <label for="username" class="block text-gray-700 font-semibold mb-2">Username</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($customer['username']); ?>" class="w-full p-2 border rounded" required>
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-gray-700 font-semibold mb-2">Password</label>
                    <input type="password" id="password" name="password" class="w-full p-2 border rounded" required>
                </div>
                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">Update Credentials</button>
            </form>
        </div>
    </div>

</body>
</html>
