<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $ldap_host = "ldap://192.168.30.122:389"; 
    $ldap_domain = "berca.co.id";          
    $username = $_POST["username"];
    $password = $_POST["password"];
    $ldap_user = $username . "@berca.co.id"; 
    $ldap_conn = ldap_connect($ldap_host);

    if ($ldap_conn) {
        ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 0);

        if (@ldap_bind($ldap_conn, $ldap_user, $password)) {
            $_SESSION["username"] = $username;
            header("Location: index.php");
            exit;
        } else {
           // $error = "Invalid username or password.";
            $ldap_error = ldap_error($ldap_conn);
            $error = "Invalid username or password. LDAP says: " . $ldap_error;
        }
    } else {
        $error = "Failed to connect to LDAP server.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Berca Hardayaperkasa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, #254a8a, #2a5da7);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-container {
            background: #fff;
            padding: 40px 30px;
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 380px;
            transition: all 0.3s ease;
            text-align: center;
        }

        .login-container img.logo {
            max-width: 160px;
            margin-bottom: 20px;
        }

        .login-container h2 {
            margin-bottom: 25px;
            color: #254a8a;
            font-weight: 600;
            font-size: 24px;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        label {
            font-size: 14px;
            margin-bottom: 6px;
            color: #333;
            display: block;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        input:focus {
            border-color: #2a5da7;
            box-shadow: 0 0 5px rgba(42, 93, 167, 0.2);
            outline: none;
        }

        .btn {
            width: 100%;
            padding: 13px;
            background: #2a5da7;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .btn:hover {
            background: #1f4885;
        }

        .error {
            color: #d8000c;
            background-color: #ffd2d2;
            border-radius: 6px;
            padding: 10px;
            margin-bottom: 15px;
            font-size: 13px;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <img src="login-logo.png" alt="Berca Hardayaperkasa Logo" class="logo">
        <h2>Sign in to OCR</h2>
        <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>
        <form method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" required autocomplete="username">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required autocomplete="current-password">
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
        <div class="footer">Active Directory Secure Login</div>
    </div>
</body>
</html>
