<?php
require_once '../phpFolder/client.php';

$clientObj = new Client();

// Check if user ID is passed
if (isset($_GET['id'])) {
    $userId = $_GET['id'];

    // Fetch the user data by ID
    $user = $clientObj->getClientById($userId);

    if (!$user) {
        die("Error: User not found!");
    }

    // Fetch payment history for the specific client
    $paymentHistory = $clientObj->getPaymentHistory($userId);
} else {
    die("Error: User ID not provided!");
}

// Handle form submission for updating user data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fname = $_POST['fname'];
    $mname = $_POST['mname'];
    $lname = $_POST['lname'];
    $username = $_POST['username'];
    $password = $_POST['password']; // Ideally, hash passwords before storing
    $address = $_POST['address'];
    $email = $_POST['email'];
    $phone_num = $_POST['phone_num'];

    // what even is this
    if (empty($password)) {
        // Keep the old password
        $password = $user['password'];
    } else {
        // Hash new password before storing
        $password = password_hash($password, PASSWORD_DEFAULT);
    }

    // Call the update function
    $updateSuccess = $clientObj->updateClient(
        $userId, $fname, $mname, $lname, $username, $password, $address, $email, $phone_num
    );

    if ($updateSuccess) {
        header("Location: admin-home.php?message=User updated successfully");
        exit();
    } else {
        echo "Error updating user!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin-view.css">
    <script src="admin-view.js"></script>
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <h1>ADMIN UPDATE whatever bruh</h1>
            </div>
            <nav>
                <ul class="admin-nav">
                    <li><a href="#" onclick="directMessage()">Message</a></li>
                    <li><a href="admin-home.php">Dashboard</a></li>
                    <li><a href="#" onclick="handleLogout()">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div id="container">
<!-- 1st Container: Update Client Information........... pero kagad nakalagay na yung info dito-->
        <div class="form-container">
            <h2>Update Client Information</h2>
            
            <form id="update-form" action="admin-view.php?id=<?= $userId ?>" method="POST">
                <label for="first-name">First Name</label>
                <input type="text" id="first-name" name="fname" placeholder="Enter first name" value="<?= htmlspecialchars($user['fname']) ?>">

                <label for="middle-name">Middle Name</label>
                <input type="text" id="middle-name" name="mname" placeholder="Enter middle name" value="<?= htmlspecialchars($user['mname'] ?? '') ?>" required>

                <label for="last-name">Last Name</label>
                <input type="text" id="last-name" name="lname" placeholder="Enter last name" value="<?= htmlspecialchars($user['lname']) ?>">

                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter username" value="<?= htmlspecialchars($user['username']) ?>" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter password" value="<?= htmlspecialchars($user['password']) ?>" required>

                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter email" value="<?= htmlspecialchars($user['email']) ?>">

                <label for="address">Address</label>
                <input type="text" id="address" name="address" placeholder="Enter address" value="<?= htmlspecialchars($user['address']) ?>">

                <label for="phone-number">Phone Number</label>
                <input type="tel" id="phone-number" name="phone_num" placeholder="Enter phone number" maxlength="11" pattern="\d{11}" value="<?=htmlspecialchars($user['phone_num']) ?>" required>


                <div class="button-group">
                    <button type="submit" class="update-btn">Update</button>
                    <button type="button" class="update-btn cancel-btn" onclick="window.history.back();">Cancel</button>
                </div>
            </form>
        </div>

<!-- 2nd Container: Payment History rip sayo-->
        <div class="payment-history-container">
            <h2>Payment History</h2>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    

                    <?php foreach ($paymentHistory as $payment): ?>
                        <tr>
                            <td><?= htmlspecialchars($payment['ph_date']) ?></td>
                            <td><?= htmlspecialchars($payment['amount_paid']) ?></td>
                            <td><?= htmlspecialchars($payment['ph_status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
