<?php
require 'config.php'; 

// Function to get all food items
function getAllFoodItems($pdo)
{
    $stmt = $pdo->query("SELECT * FROM FoodItem");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to add an item to the cart
session_start();

if (isset($_POST['add_to_cart'])) {
    $itemID = $_POST['itemID'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    
    $cart_item = [
        'itemID' => $itemID,
        'name' => $name,
        'price' => $price,
        'quantity' => 1
    ];

    if (isset($_SESSION['cart'][$itemID])) {
        $_SESSION['cart'][$itemID]['quantity'] += 1;
    } else {
        $_SESSION['cart'][$itemID] = $cart_item;
    }
}

// Fetch all food items for display
$foodItems = getAllFoodItems($pdo);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .cart-popup {
            display: none;
            position: fixed;
            top: 10%;
            left: 50%;
            transform: translateX(-50%);
            width: 300px;
            background-color: white;
            border: 1px solid #ddd;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            z-index: 10;
            padding: 20px;
            border-radius: 10px;
        }
        .cart-popup.open {
            display: block;
        }
    </style>
</head>

<body class="bg-gray-100 text-gray-800">
    <!-- Header with Cart Icon -->
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


    <!-- Cart Popup -->
    <div id="cart-popup" class="cart-popup">
        <h2 class="text-lg font-bold mb-4">Your Cart</h2>
        <ul id="cart-items" class="mb-4"></ul>
        <p class="font-bold">Total: $<span id="total-amount">0.00</span></p>
        <button id="checkout-btn" class="mt-4 bg-green-500 text-white py-2 px-4 rounded">Checkout</button>
    </div>

    <div class="container mx-auto p-6">
        <!-- Display all food items -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($foodItems as $item) : ?>
                <div class="bg-white shadow-md rounded p-4">
                    <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="w-full h-48 object-cover mb-4 rounded">
                    <h2 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($item['name']); ?></h2>
                    <p class="text-gray-700 mb-2"><?php echo htmlspecialchars($item['description']); ?></p>
                    <p class="text-gray-900 font-bold mb-4">$<?php echo number_format($item['price'], 2); ?></p>
                    <form method="POST">
                        <input type="hidden" name="itemID" value="<?php echo htmlspecialchars($item['itemID']); ?>">
                        <input type="hidden" name="name" value="<?php echo htmlspecialchars($item['name']); ?>">
                        <input type="hidden" name="price" value="<?php echo htmlspecialchars($item['price']); ?>">
                        <button type="submit" name="add_to_cart" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Add to Cart</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        const cartBtn = document.getElementById('cart-btn');
        const cartPopup = document.getElementById('cart-popup');
        const cartItems = document.getElementById('cart-items');
        const totalAmount = document.getElementById('total-amount');
        const checkoutBtn = document.getElementById('checkout-btn');

        let cart = <?php echo json_encode($_SESSION['cart'] ?? []); ?>;

        function updateCartPopup() {
            cartItems.innerHTML = '';
            let total = 0;

            for (const key in cart) {
                const item = cart[key];
                const li = document.createElement('li');
                li.classList.add('mb-2');
                li.textContent = `${item.name} - $${item.price} x ${item.quantity}`;
                cartItems.appendChild(li);
                total += item.price * item.quantity;
            }

            totalAmount.textContent = total.toFixed(2);
        }

        cartBtn.addEventListener('click', () => {
            cartPopup.classList.toggle('open');
            updateCartPopup();
        });

        checkoutBtn.addEventListener('click', () => {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'checkout.php';
            const cartDataInput = document.createElement('input');
            cartDataInput.type = 'hidden';
            cartDataInput.name = 'cart_data';
            cartDataInput.value = JSON.stringify(cart);
            form.appendChild(cartDataInput);
            document.body.appendChild(form);
            form.submit();
        });

        updateCartPopup();
    </script>
</body>

</html>
