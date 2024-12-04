<?php
require 'config.php'; 

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // customer table
    $stmt = $pdo->prepare("SELECT * FROM customer WHERE username = ?");
    $stmt->execute([$username]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($customer && password_verify($password, $customer['password'])) {
        $_SESSION['user'] = $customer;
        $_SESSION['role'] = 'customer';
        header("Location: index.php");
        exit;
    }

    //  employee table
    $stmt = $pdo->prepare("SELECT * FROM employee WHERE username = ?");
    $stmt->execute([$username]);
    $employee = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($employee && password_verify($password, $employee['password'])) {
        $_SESSION['user'] = $employee;
        $_SESSION['role'] = 'employee';

        // Check the designation of the employee
        if ($employee['designation'] == 'Delivery-Man') {
            header("Location: deliveryMan_dashboard.php");
        } else {
            header("Location: employee_dashboard.php");
        }
        exit;
    }

    // Check in admin table 
    $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ? AND password = ?");
    $stmt->execute([$username, $password]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin) {
        $_SESSION['user'] = $admin;
        $_SESSION['role'] = 'admin';
        header("Location: admin_dashboard.php");
        exit;
    }

    // If login fails
    $error = "Invalid username or password";
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .bg-olive {
            background-color: #808000;
        }

        .hover\:bg-olive-dark:hover {
            background-color: #6B8E23;
        }
    </style>
</head>
<header class="bg-green-500 text-white py-4">
    <div class="container mx-auto flex justify-between items-center">
        <h1 class="text-2xl font-bold">Restaurant Menu</h1>
        <nav class="space-x-4">
           
            <a href="index.php" class="font-bold">Home</a>
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
                <a href="signup.php" class="hover:underline">Sign up</a>
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

    <div class="flex justify-center items-center min-h-screen">
        <div class="bg-white p-8 rounded shadow-md w-full max-w-sm">
            <h1 class="text-2xl font-bold mb-6 text-center">Login</h1>
            
            <?php if (!empty($error)) : ?>
                <div class="bg-red-100 text-red-700 p-2 rounded mb-4">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-4">
                    <label for="username" class="block text-gray-700 font-semibold mb-2">Username</label>
                    <input type="text" id="username" name="username" class="w-full p-2 border rounded" required>
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-gray-700 font-semibold mb-2">Password</label>
                    <input type="password" id="password" name="password" class="w-full p-2 border rounded" required>
                </div>
                <button type="submit" class="w-full bg-olive hover:bg-olive-dark text-white font-bold py-2 px-4 rounded">Login</button>
            </form>
        </div>
    </div>
</body>

</html>
