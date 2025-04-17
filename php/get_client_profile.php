<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard</title>
    <link rel="stylesheet" href="../css/home.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <a href="../php/home_loggedin.php">
                    <img src="../img/logo.png.webp" alt="Coach Connect Logo">
                </a>
            </div>
            <nav>
                <ul>
                    <li><a href="../php/home_loggedin.php">Home</a></li>
                    <li><a href="../html/aboutus.html">About Us</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="dashboard-container">
        <h1>Client Dashboard</h1>

        <!-- Profile Section -->
        <div class="profile-section">
            <h2>Your Profile</h2>
            <img id="clientProfilePicture" src="../images/default-profile.png" alt="Profile Picture">
            <p>Name: <span id="clientName">Loading...</span></p>
            <p>Email: <span id="clientEmail">Loading...</span></p>
            <p>Phone: <span id="clientPhone">Loading...</span></p>
            <button id="updateProfileButton">Update Profile</button>
        </div>

        <!-- Upcoming Bookings Section -->
        <div class="bookings-section">
            <h2>Your Upcoming Bookings</h2>
            <table>
                <thead>
                    <tr>
                        <th>Coach Name</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Session Type</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="upcomingBookingsTable">
                    <!-- Upcoming bookings will be dynamically loaded here -->
                </tbody>
            </table>
        </div>

        <!-- Booking History Section -->
        <div class="history-section">
            <h2>Your Booking History</h2>
            <table>
                <thead>
                    <tr>
                        <th>Coach Name</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Session Type</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="bookingHistoryTable">
                    <!-- Booking history will be dynamically loaded here -->
                </tbody>
            </table>
        </div>
    </div>

    <footer>
        <div class="footer-container">
            <p>&copy; 2025 Coach Connect. All Rights Reserved.</p>
        </div>
    </footer>

    <script>
        // Fetch and display client profile
        document.addEventListener('DOMContentLoaded', () => {
            fetch('../php/get_client_profile.php')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('clientName').textContent = data.name;
                    document.getElementById('clientEmail').textContent = data.email;
                    document.getElementById('clientPhone').textContent = data.phone;
                });

            // Fetch and display upcoming bookings
            fetch('../php/get_upcoming_bookings.php')
                .then(response => response.json())
                .then(bookings => {
                    const table = document.getElementById('upcomingBookingsTable');
                    bookings.forEach(booking => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${booking.coach_name}</td>
                            <td>${booking.date}</td>
                            <td>${booking.time}</td>
                            <td>${booking.session_type}</td>
                            <td><button onclick="cancelBooking(${booking.id})">Cancel</button></td>
                        `;
                        table.appendChild(row);
                    });
                });

            // Fetch and display booking history
            fetch('../php/get_booking_history.php')
                .then(response => response.json())
                .then(history => {
                    const table = document.getElementById('bookingHistoryTable');
                    history.forEach(booking => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${booking.coach_name}</td>
                            <td>${booking.date}</td>
                            <td>${booking.time}</td>
                            <td>${booking.session_type}</td>
                            <td>${booking.status}</td>
                        `;
                        table.appendChild(row);
                    });
                });
        });

        // Cancel booking function
        function cancelBooking(bookingId) {
            if (confirm('Are you sure you want to cancel this booking?')) {
                fetch(`../php/cancel_booking.php?id=${bookingId}`, { method: 'POST' })
                    .then(response => response.text())
                    .then(result => {
                        alert(result);
                        location.reload(); // Reload the page to update the bookings
                    });
            }
        }
    </script>
</body>
</html>