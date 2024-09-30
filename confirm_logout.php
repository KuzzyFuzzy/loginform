<!DOCTYPE html>
<html>
<head>
    <title>Logout Confirmation</title>
    <script>
        function confirmLogout() {
            if (confirm("Are you sure you want to log out?")) {
                // If confirmed, submit the form
                document.getElementById('logoutForm').submit();
            }
        }
    </script>
</head>
<body>
    <form id="logoutForm" action="logout.php" method="post">
        <!-- The form action is set to the PHP logout handler -->
    </form>
    <button onclick="confirmLogout()">Log Out</button>
</body>
</html>
