<?php
require __DIR__ . "/includes/functions.php";
require __DIR__ . "/includes/discord.php";
require __DIR__ . "/config.php";

session_start();

// Define variables and initialize with empty values
$password = $new_account = "";
$password_err = $account_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate new account
    $temp_account = trim($_POST["new_account"]);

    $contains_prohibited = false;

    foreach (PROHIBITED_CHARACTERS as $char) {
        if (str_contains($temp_account, $char)) {
            $contains_prohibited = true;
            break;
        }
    }

    if (empty($temp_account)) {
        $account_err = "Please enter a username.";
    } elseif (strlen($temp_account) < 4) {
        $account_err = "The username must have at least 4 characters.";
    } elseif ($contains_prohibited) {
        $account_err = "The username cannot contain any of the following characters: " . implode(", ", PROHIBITED_CHARACTERS);
    } else {
        $new_account = $temp_account;
    }

    $stmt = $link->prepare("SELECT * FROM account WHERE Name = ?");
    $stmt->execute([$new_account]);
    if ($stmt->rowCount() > 0) {
        $account_err = "This account already exists.";
    }

// Validate new password
    $temp_password = trim($_POST["password"]);
    $contains_prohibited = false;

    foreach (PROHIBITED_CHARACTERS as $char) {
        if (str_contains($temp_password, $char)) {
            $contains_prohibited = true;
            break;
        }
    }

    if (empty($temp_password)) {
        $password_err = "Please enter a password.";
    } elseif (strlen($temp_password) < 6) {
        $password_err = "The password must have at least 6 characters.";
    } elseif ($contains_prohibited) {
        $password_err = "The password cannot contain any of the following characters: " . implode(", ", PROHIBITED_CHARACTERS);
    } else {
        $password = $temp_password;
    }

// Check input errors before updating the database
    if (empty($password_err) && empty($account_err) && !empty($new_account) && !empty($password)) {
        // Prepare an insert statement
        $sql = "INSERT INTO account (Name, Password, DiscordID, CreationDate, Account_ID, PrivLevel) VALUES (?, ?, ?, ?, ?, 1)";
        $stmt = $link->prepare($sql);

        // Set parameters
        $param_account = $param_id = $new_account;
        $param_password = cryptPassword($password);
        $param_discord = $_SESSION['user_id'];
        $param_creation = date("Y-m-d H:i:s");

        // Attempt to execute the prepared statement
        if ($stmt->execute([$param_account, $param_password, $param_discord, $param_creation, $param_id])) {
            // Redirect to login page
            header("location: /");
        } else {
            echo "Error: " . $stmt->errorInfo()[2] . "<br>" . $param_username . "<br>" . $param_password . "<br>" . $param_username . "<br>" . $param_creation;
        }
    }

}
?>

<html lang="en">

<head>
    <title>Atlas Account</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
    <script src="https://unpkg.com/bootstrap-show-password@1.2.1/dist/bootstrap-show-password.min.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>
<header>
    <header>
        <span class="logo">
        <a href="/"><img src="https://cdn.discordapp.com/attachments/879754382231613451/978241143751966740/50px.png" class="logo-img" alt="Atlas Logo">Atlas Account Manager</a></span>
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
        <div class="col-md-6 mx-auto center">
            <?php if (getGameAccount($_SESSION['user_id']) == null) { ?>


                <h3 class="my-3 center">Account registration</h3>
                <h5 class="my-3 center"><?php echo $_SESSION['user']['username'] . '#' . $_SESSION['discrim']; ?></h5>

                <div class="alert alert-warning center" role="alert">Please don't use <b>%</b>, <b>&</b>, <b>#</b>, <b>^</b> or spaces for your account or password.</div>

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="new_account"
                               class="form-control <?php echo (!empty($account_err)) ? 'is-invalid' : ''; ?>"
                               value="<?php echo $new_account; ?>">
                        <span class="error-msg"><?php echo $account_err; ?></span>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input data-toggle="password" type="password" name="password"
                               class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                        <span class="error-msg"><?php echo $password_err; ?></span>
                    </div>
                    <div class="form-group center">
                        <a class="btn btn-secondary" href="index.php">Cancel</a>
                        <input type="submit" class="btn btn-success bold" value="Register">
                    </div>
                </form>


            <?php } else { ?>
                <br>
                <div class="alert alert-danger" role="alert">
                    <b><?php echo $_SESSION['user']['username'] . '#' . $_SESSION['discrim']; ?></b> is already
                    associated to game account <b><?php echo getGameAccount($_SESSION['user_id']); ?></b></div>
                <a class="btn btn-secondary" href="index.php">Cancel</a>

            <?php } ?>
        </div>
    </div>
</div>

</body>

</html>