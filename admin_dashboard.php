<?php
require 'config.php';  

// Function to get all employees
function getAllEmployees($pdo)
{
    $stmt = $pdo->query("SELECT * FROM Employee");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to add an employee (with password hashing)
if (isset($_POST['add_employee'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $designation = $_POST['designation'];

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO Employee (username, password, firstName, lastName, designation) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$username, $hashedPassword, $firstname, $lastname, $designation]);
}

// Function to delete an employee
if (isset($_POST['delete_employee'])) {
    $username = $_POST['username'];
    $stmt = $pdo->prepare("DELETE FROM Employee WHERE username = ?");
    $stmt->execute([$username]);
}

// Fetch all employees for display
$employees = getAllEmployees($pdo);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 text-gray-800">
<div>
    <div>
    <a href="logout.php" class="ml-4 px-4 py-2 bg-green-600 hover:bg-green-500 text-white rounded-lg">
    Log Out
</a>

    </div>


   
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">Employee Management</h1>

        <!-- Display all employees -->
        <div class="bg-white shadow-md rounded p-4 mb-6">
            <h2 class="text-xl font-semibold mb-4">All Employees</h2>
            <table class="table-auto w-full">
                <thead>
                    <tr>
                        <th class="px-4 py-2">Username</th>
                        <th class="px-4 py-2">First Name</th>
                        <th class="px-4 py-2">Last Name</th>
                        <th class="px-4 py-2">Designation</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($employees as $employee) : ?>
                        <tr>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($employee['username']); ?></td>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($employee['firstName']); ?></td>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($employee['lastname']); ?></td>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($employee['designation']); ?></td>
                            <td class="border px-4 py-2">
                                <form method="POST" class="inline-block">
                                    <input type="hidden" name="username" value="<?php echo htmlspecialchars($employee['username']); ?>">
                                    <button type="submit" name="delete_employee" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Add a new employee -->
        <div class="bg-white shadow-md rounded p-4">
            <h2 class="text-xl font-semibold mb-4">Add Employee</h2>
            <form method="POST">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="username">Username</label>
                    <input type="text" name="username" id="username" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="password">Password</label>
                    <input type="password" name="password" id="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="firstname">First Name</label>
                    <input type="text" name="firstname" id="firstname" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="lastname">Last Name</label>
                    <input type="text" name="lastname" id="lastname" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="designation">Designation</label>
                    <input type="text" name="designation" id="designation" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div>
                    <button type="submit" name="add_employee" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Add Employee</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
