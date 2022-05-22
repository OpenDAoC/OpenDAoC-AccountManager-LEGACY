<?php
# Including all the required scripts for demo
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
    if (empty(trim($_POST["new_account"]))) {
        $account_err = "Please enter an account name.";
    } elseif (strlen(trim($_POST["new_account"])) < 4) {
        $account_err = "The account must have at least 4 characters.";
    } else {
        $new_account = trim($_POST["new_account"]);
    }

    $check_account = mysqli_query($link,"SELECT * FROM account WHERE Name = '$new_account'");
    if (mysqli_num_rows($check_account) > 0) {
        $account_err = "This account already exists.";
    }


    // Validate new password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Check input errors before updating the database
    if (empty($password_err) && empty($account_err) && !empty($new_account) && !empty($password)) {
        // Prepare an update statement

        $sql = "INSERT INTO account (Name, Password, DiscordID, CreationDate, Account_ID) VALUES (?, ?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sssss", $param_account, $param_password, $param_discord, $param_creation, $param_id);

            // Set parameters
            $param_account = $param_id = $new_account;
            $param_password = cryptPassword($password);
            $param_discord = $_SESSION['user_id'];
            $param_creation = date("Y-m-d H:i:s");

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Redirect to login page
                header("location: /");
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($link) . "<br>" . $param_username . "<br>" . $param_password . "<br>" . $param_username . "<br>" . $param_creation;
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Close connection
    mysqli_close($link);
}

?>

<html>

<head>
    <title>Atlas Account</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>
<header><span class="logo">Atlas Account Manager</span>
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

                <div class="alert alert-warning center" role="alert">Please don't use <b>%</b>, <b>&</b> or spaces in
                    your password.</div>

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="new_account"
                               class="form-control <?php echo (!empty($account_err)) ? 'is-invalid' : ''; ?>"
                               value="<?php echo $new_account; ?>">
                        <span class="invalid-feedback"><?php echo $account_err; ?></span>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password"
                               class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                        <span class="invalid-feedback"><?php echo $password_err; ?></span>
                    </div>
                    <div class="form-group center">
                        <input type="submit" class="btn btn-success" value="Register">
                        <a class="btn btn-secondary" href="index.php">Cancel</a>
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