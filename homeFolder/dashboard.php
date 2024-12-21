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

    $lastPaymentDate = !empty($paymentHistory) ? end($paymentHistory)['ph_date'] : 'No payment history';
} else {
    die("Error: User ID not provided!");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
    <script src="dashboard.js" defer></script>
</head>
<body>
    <div class="container">
        <div class="navigation">
            <div class="container1">
                <div class="title-container">
                    <h2 class="pricing-title">WaterWay Pricing Per Cubic Meter</h2>
                </div>
                <div class="card-container">
                    <!-- Card 1 -->
                    <div class="card1 playing">
                      <div class="content">
                        <div class="title">Basic Plan</div>
                            <div class="price">₱250</div>
                            <div class="description">
                                Ideal for small households with minimal water consumption.
                            </div>
                      </div>
                    </div>
                  
                    <!-- Card 2 -->
                    <div class="card1 playing">
                      <div class="content">
                        <div class="title">Standard Plan</div>
                            <div class="price">₱500</div>
                            <div class="description">
                                Suitable for medium-sized households or small businesses.
                            </div>
                      </div>
                    </div>
                  
                    <!-- Card 3 -->
                    <div class="card1 playing">
                      <div class="content">
                        <div class="title">Premium Plan</div>
                            <div class="price">₱1000</div>
                            <div class="description">
                                Best for large families or businesses with higher water needs.
                            </div>
                      </div>
                    </div>
                  
                    <!-- Card 4 -->
                    <div class="card1 playing">
                      <div class="content">
                        <div class="title">Custom Plan</div>
                            <div class="price1">Contact for Pricing</div>
                            <div class="description">
                                Tailored pricing for businesses or large-scale operations.
                            </div>
                      </div>
                    </div>
                </div>
                <!-- Message to Admin -->
                <div class="contact-container">
                    <center>
                    <p class="pricing-subtitle">Choose the best plan based on your water consumption</p>
                    <button class="cta">
                        <span>Contact Us &nbsp;</span>
                        <svg viewBox="0 0 13 10" height="10px" width="15px">
                        <path d="M1,5 L11,5"></path>
                        <polyline points="8 1 12 5 8 9"></polyline>
                        </svg>
                    </button>
                    </center>
                </div>                  
            </div>
        </div>
        

        <!-- Dashboard Section -->
        <div class="dashboard-container">
            <header class="header">
                <h1>Dashboard</h1>
                <button class="Btn" onclick="handleLogout()">
                <div class="sign"><svg viewBox="0 0 512 512"><path d="M217.9 105.9L340.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L217.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9l0-62.1L32 320c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM352 416l64 0c17.7 0 32-14.3 32-32l0-256c0-17.7-14.3-32-32-32l-64 0c-17.7 0-32-14.3-32-32s14.3-32 32-32l64 0c53 0 96 43 96 96l0 256c0 53-43 96-96 96l-64 0c-17.7 0-32-14.3-32-32s14.3-32 32-32z"></path></svg></div>
                <div class="text">Logout</div>
                </button>
            </header>

            <!-- Current Balance -->
            <div class="dashboard">
                <div class="card current-balance">
                    <table>
                        <tr>
                            <th colspan="2">Current Balance</th>
                        </tr>
                        <tr>
                            <td>+ 12.764 m<sup>3</sup></td>
                            <td style="color: red;">- equivalent in Peso</td>
                        </tr>
                    </table>
                </div>

                <!-- Welcome -->
                <div class="card welcome">
                    <table>
                        <tr>
                            <th>WELCOME!</th>
                        </tr>
                        <tr>
                            <td>*Account Status Message: Up to date, low balance, over limit, etc.</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Water Usage -->
            <div class="dashboard">
                <div class="card water-usage">
                    <table>
                        <tr>
                            <th>Water Usage</th>
                            <th>Timestamp</th>
                            <th>Percentage</th>
                        </tr>
                        <tr>
                            <td>0.256 m<sup>3</sup></td>
                            <td>Timestamp</td>
                            <td>1.71%</td>
                        </tr>
                        <tr>
                            <td>0.75 m<sup>3</sup></td>
                            <td>Timestamp</td>
                            <td>5%</td>
                        </tr>
                        <tr>
                            <td>1.23 m<sup>3</sup></td>
                            <td>Timestamp</td>
                            <td>8.2%</td>
                        </tr>
                    </table>
                </div>

                <!-- Basic Account Info -->
                <div class="card account-info">
                    <table>
                        <tr>
                            <th colspan="2">Basic Account Info</th>
                        </tr>
                        <tr>
                            <td>Account ID:</td>
                            <td><?php echo $user['client_id']; ?></td> <!-- Account ID from database -->
                        </tr>
                        <tr>
                            <td>Name:</td>
                            <td><?php echo htmlspecialchars($user['fname'] . ' ' . $user['lname']) ?></td>
                        </tr>
                        <tr>
                            <td>Status:</td>
                            <td><?php echo htmlspecialchars($user['account_status']); ?></td>
                        </tr>
                        <tr>
                            <td>Balance:</td>
                            <td><?php echo number_format($user['prepaid_limit']); ?> m³</td>
                        </tr>
                        <tr>
                            <td>Last Payment:</td>
                            <td><?php echo htmlspecialchars($lastPaymentDate); ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Transaction History -->
            <div class="card transaction-history">
                <table>
                    <tr>
                        <th>Amount</th>
                        <th>Type</th>
                        <th>Time</th>
                    </tr>
                    <tr>
                        <td>20 m<sup>3</sup></td>
                        <td style="color: red;">- P000</td>
                        <td>Yesterday, 2:00 PM</td>
                    </tr>
                    <tr>
                        <td>10 m<sup>3</sup></td>
                        <td style="color: red;">- P000</td>
                        <td>Timestamp</td>
                    </tr>
                    <tr>
                        <td>10 m<sup>3</sup></td>
                        <td style="color: green;">+ P000</td>
                        <td>Refund</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
