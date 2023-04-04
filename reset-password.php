<?php
# Including all the required scripts for demo
require __DIR__ . "/includes/functions.php";
require __DIR__ . "/includes/discord.php";
require __DIR__ . "/config.php";

session_start();

// Define variables and initialize with empty values
$new_password = $confirm_password = "";
$new_password_err = $confirm_password_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate new password
    $temp_password = trim($_POST["new_password"]);
    $contains_prohibited = false;

    foreach (PROHIBITED_CHARACTERS as $char) {
        if (str_contains($temp_password, $char)) {
            $contains_prohibited = true;
            break;
        }
    }

    if (empty($temp_password)) {
        $new_password_err = "Please enter a password.";
    } elseif (strlen($temp_password) < 6) {
        $new_password_err = "The new password must have at least 6 characters.";
    } elseif ($contains_prohibited) {
        $new_password_err = "The new password cannot contain any of the following characters: " . implode(", ", PROHIBITED_CHARACTERS);
    } else {
        $new_password = $temp_password;
    }

    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm the password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($new_password_err) && ($new_password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }

    // Check input errors before updating the database
    if (empty($new_password_err) && empty($confirm_password_err)) {
        // Prepare an update statement
        $sql = "UPDATE account SET Password = :password WHERE DiscordID = :discord_id";
        $stmt = $link->prepare($sql);
        $stmt->bindParam(':password', $param_password);
        $stmt->bindParam(':discord_id', $param_discord_id);

        // Set parameters
        $param_password = cryptPassword($new_password);
        $param_discord_id = $_SESSION['user_id'];

        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
            // Password updated successfully. Destroy the session, and redirect to login page
            session_destroy();
            header("location: /");
            exit();
        } else {
            echo $_SESSION["username"];
        }
    }
}

?>

<html lang="en">

<head>
    <title>Atlas Account</title>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
    <script src="https://unpkg.com/bootstrap-show-password@1.2.1/dist/bootstrap-show-password.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>
<header>
    <header>
        <span class="logo">
        <a href="/"><img src="https://cdn.discordapp.com/avatars/865566424537104386/282901fdaa488f57a95faae665a7c245.webp" class="logo-img" alt="Logo">OpenDAoC Account Manager</a></span>
    <span class="menu">
			<?php

            if (isset($_SESSION['discord_username'])) {
                $discord_username = $_SESSION['discord_username'];
                $discord_discrim = $_SESSION['discrim'];
            }
            ?>
		</span>
</header>

<div class="container">
    <div class="row">
        <div class="col-md-6 mx-auto">

            <h3 class="my-3 center">Password Reset</h3>
            <h5 class="my-3 center"><?php echo $_SESSION['user']['username'] . '#' . $_SESSION['discrim']; ?></h5>

            <div class="alert alert-warning center" role="alert">Please don't use <b>%</b>, <b>&</b>, <b>#</b>, <b>^</b> or spaces for your password.<br><small>You will be required to login again.</small></div>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="new_password" data-toggle="password"
                           class="form-control <?php echo (!empty($new_password_err)) ? 'is-invalid' : ''; ?>"
                           value="<?php echo $new_password; ?>">
                    <span class="error-msg"><?php echo $new_password_err; ?></span>
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" data-toggle="password"
                           class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>">
                    <span class="error-msg"><?php echo $confirm_password_err; ?></span>
                </div>
                <div class="form-group center">
                    <a class="btn btn-secondary" href="index.php">Cancel</a>
                    <input type="submit" class="btn btn-warning" value="Change Password">
                </div>
            </form>

        </div>
    </div>
</div>

</body>

</html>