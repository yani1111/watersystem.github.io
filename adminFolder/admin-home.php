<?php
require_once '../phpFolder/client.php';

$clientObj = new Client();
$clients = $clientObj->getClients();

// Handle form submission for adding a new client
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add-client'])) {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $prepaidLimit = $_POST['prepaid-limit'];
    
    // Add the new client
    if ($clientObj->addClient($fname, $lname, $address, $email, $prepaidLimit)) {
        header('Location: admin-home.php?message=Client added successfully');
        exit();
    } else {
        echo "Error adding client.";
    }

    // Add the new client
    if ($clientObj->addClient($fname, $lname, $address, $email, $prepaidLimit)) {
        // After adding the client, redirect to dashboard with success message
        header('Location: dashboard.php?message=Client added successfully');
        exit();
    } else {
        echo "Error adding client.";
    }
}

// Handle form submission for adding amount
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add-amount'])) {
    $clientId = $_POST['client-id'];
    $amount = $_POST['amount'];

    // Add the amount to the client's prepaid limit
    if ($clientObj->addAmount($clientId, $amount)) {
        header('Location: admin-home.php?message=Amount added successfully');
        exit();
    } else {
        echo "Error adding amount.";
    }

    if (isset($_GET['message'])) {
        echo '<div class="success-message">' . htmlspecialchars($_GET['message']) . '</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin-home.css">
    <script src="admin-home.js"></script> 
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <h1>Admin Dashboard</h1>
            </div>
            <nav>
                <ul class="admin-nav">
                    <li><a href="#" onclick="directMessage()">Message</a></li>
                    <li><a href="#" onclick="toggleAddClientForm()">Add Client</a></li>
                    <li><a href="#" onclick="handleLogout()">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>
    
    <main id="main">
        <!-- Table -->
        <table>
            <thead>
                <tr>
                    <th>Client ID</th>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Remaining Water (m³)</th>
                    <th>Prepaid Limit (m³)</th>
                    <th>Account Status</th>
                    <th>Last Updated</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Loop through clients and display data dynamically -->
                <?php foreach ($clients as $client): ?>
                    <tr>
                        <td><?= htmlspecialchars(str_pad($client['client_id'], 3, '0', STR_PAD_LEFT)) ?></td>

                        <td><?= htmlspecialchars($client['fname'] . ' ' . $client['lname']) ?></td>
                        <td><?= htmlspecialchars($client['address']) ?></td>
                        <td><?= htmlspecialchars($client['prepaid_limit']) ?></td>
                        <td><?= htmlspecialchars($client['prepaid_limit']) ?></td>
                        <td><?= htmlspecialchars($client['account_status']) ?></td>
                        <td>
                            <?php 
                            // Format the last updated date (assuming it's in the correct format)
                            $lastUpdated = $client['last_updated'];
                            echo date('F j, Y, g:i a', strtotime($lastUpdated)); // Display the date in a readable format
                            ?>
                        </td>
                        <td>
                            
                            <button class="view-btn" onclick="window.location.href='admin-view.php?id=<?= $client['client_id'] ?>';">View</button>
                            
                            <!--delete button-->
                            <form id="delete-form-<?= $client['client_id'] ?>" action="../phpFolder/delete_client.php" method="POST" style="display: none;">
                                <input type="hidden" name="client_id" value="<?= $client['client_id'] ?>">
                            </form>
                            <button class="delete-btn" onclick="confirmDelete(<?= $client['client_id'] ?>)">Delete</button>
                            <script>
                                function confirmDelete(clientId) {
                                    // Show confirmation dialog
                                    var confirmed = confirm("Are you sure you want to delete this client?");
                                    
                                    // If confirmed, submit the form
                                    if (confirmed) {
                                        // Find the form by ID and submit it
                                        document.getElementById("delete-form-" + clientId).submit();
                                    }
                                }
                            </script>

                            <button class="add-amount-btn" data-client-id="<?= $client['client_id'] ?>" >Add Amount</button>
                            <!-- Add Amount Form -->
                            <div id="add-amount-form-<?= $client['client_id'] ?>" class="floating-form" style="display: none;">
                                <h2>Add Amount</h2>
                                <form method="POST" action="admin-home.php">
                                    <input type="hidden" name="client-id" value="<?= $client['client_id'] ?>">
                                    <label for="amount">Amount</label>
                                    <input type="number" id="amount" name="amount" step="250" min="250" value="250" required>
                                    <div class="button-container">
                                        <button type="submit" name="add-amount">Submit</button>
                                        <button type="button" onclick="toggleAddAmountForm(<?= $client['client_id'] ?>)">Cancel</button>
                                    </div>
                                </form>
                            </div>
                            
                            <button class="add-water-btn" data-client-id="<?= $client['client_id'] ?>" >Add Water Used</button>
                            <!-- Add Water Used Form -->
                            <div id="add-water-form-<?= $client['client_id'] ?>" class="floating-form" style="display: none;">
                                <h2>Add Water Used</h2>
                                <form method="POST" action="admin-home.php">
                                    <input type="hidden" name="client-id" value="<?= $client['client_id'] ?>">
                                    <label for="amount">Water</label>
                                    <input type="number" id="amount" name="amount" required>
                                    <div class="button-container">
                                        <button type="submit" name="add-amount">Submit</button>
                                        <button type="button" onclick="toggleAddWaterForm(<?= $client['client_id'] ?>)">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        

                        </td>              
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Add Client Form -->
        <div id="add-client-form" class="floating-form" style="display: none;">
            <h2>Add Client</h2>
            <form method="POST" action="admin-home.php">
                <label for="fname">First Name</label>
                <input type="text" id="fname" name="fname" required>

                <label for="lname">Last Name</label>
                <input type="text" id="lname" name="lname" required>

                <label for="address">Address</label>
                <input type="text" id="address" name="address" required>

                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>

                <label for="prepaid-limit">Prepaid Limit</label>
                <input type="number" id="prepaid-limit" name="prepaid-limit" step="5" min="5" value="5" required>

                <div class="button-container">
                    <button type="submit" name="add-client">Add Client</button>
                    <button type="button" onclick="toggleAddClientForm()">Cancel</button>
                </div>
            </form>
        </div>
    </main>

    <footer id="footer">
        © 2024 Water-Way Works. All Rights Reserved.
    </footer>
</body>
</html>
