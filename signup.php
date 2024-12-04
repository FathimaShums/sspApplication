<?php
session_start();
require 'config.php'; // Include the database connection

// Initialize error messages and success message
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['signup'])) {
    // Collect form data
    $username = trim($_POST['username']);
    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirm_password']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    
    // Validate form data
    if (empty($username) || empty($firstName) || empty($lastName) || empty($email) || empty($password) || empty($phone) || empty($address)) {
        $error = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } else {
        try {
            // Check if the email or username already exists
            $stmt = $pdo->prepare("SELECT * FROM Customer WHERE email = ? OR username = ?");
            $stmt->execute([$email, $username]);
            if ($stmt->rowCount() > 0) {
                $error = 'An account with this email or username already exists.';
            } else {
                // Hash the password 
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert customer data into the database
                $stmt = $pdo->prepare("INSERT INTO Customer (username, firstName, lastName, email, password, phone, address) 
                                       VALUES (?, ?, ?, ?, ?, ?, ?)");
                if ($stmt->execute([$username, $firstName, $lastName, $email, $hashedPassword, $phone, $address])) {
                    $success = 'Account created successfully! You can now <a href="login.php" class="underline">log in</a>.';
                } else {
                    $error = 'Failed to create account. Please try again.';
                }
            }
        } catch (PDOException $e) {
            $error = 'Error: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Signup</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<header class="bg-green-500 text-white py-4">
    <div class="container mx-auto flex justify-between items-center">
        <h1 class="text-2xl font-bold">Restaurant Menu</h1>
        <nav class="space-x-4">
            <a href="index.php" class="hover:underline">Home</a>
            <a href="checkout.php" class="hover:underline">Cart</a>

            <!-- Check if user is logged in -->
            <?php if (isset($_SESSION['user'])): ?>
                <a href="myorders.php" class="hover:underline">My Orders</a>
                <a href="customerprofile.php" class="hover:underline">My Profile</a>


                <form action="logout.php" method="POST" class="inline">
                    <button type="submit" class="hover:underline">Logout</button>
                </form>
            <?php else: ?>
                <a href="login.php" class="hover:underline">Log In</a>
                <a href="signup.php" class="font-bold">Sign up</a>
            <?php endif; ?>
        </nav>

        <!-- Cart button-->
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
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6">Customer Sign Up</h1>

        <!-- Display error or success message -->
        <?php if (!empty($error)): ?>
            <p class="text-red-500"><?php echo $error; ?></p>
        <?php elseif (!empty($success)): ?>
            <p class="text-green-500"><?php echo $success; ?></p>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-4">
                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                <input type="text" name="username" id="username" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div class="mb-4">
                <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                <input type="text" name="first_name" id="first_name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div class="mb-4">
                <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                <input type="text" name="last_name" id="last_name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="email" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" id="password" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div class="mb-4">
                <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div class="mb-4">
                <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                <input type="text" name="phone" id="phone" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div class="mb-4">
                <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                <input type="text" name="address" id="address" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div>
                <button type="submit" name="signup" class="bg-green-500 text-white py-2 px-4 rounded">Sign Up</button>
            </div>
        </form>
    </div>
</body>
</html>
