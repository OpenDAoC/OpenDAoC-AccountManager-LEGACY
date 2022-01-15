<?php


# Including all the required scripts for demo
require __DIR__ . "/includes/functions.php";
require __DIR__ . "/includes/discord.php";
require __DIR__ . "/config.php";

# ALL VALUES ARE STORED IN SESSION!
# RUN `echo var_export([$_SESSION]);` TO DISPLAY ALL THE VARIABLE NAMES AND VALUES.

$username = $password = "";
$username_err = $password_err = $login_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if username is empty
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter username.";
    } else {
        $username = trim($_POST["username"]);
    }

    // Check if password is empty
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate credentials
    if (empty($username_err) && empty($password_err)) {
        // Prepare a select statement
        $sql = "SELECT Account_ID, Name, Password, DiscordID FROM account WHERE Name = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);

            // Set parameters
            $param_username = $username;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_store_result($stmt);

                // Check if username exists, if yes then verify password
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password, $discordID);
                    if (mysqli_stmt_fetch($stmt)) {
                        if (strcmp(cryptPassword($password), $hashed_password) == 0) {
                            // Password is correct, so start a new session
                            session_start();

                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;

                            // Redirect user to welcome page
                            header("location: index.php");
                        } else {
                            // Password is not valid, display a generic error message
                            $login_err = "Invalid username or password.";
                        }
                    }
                } else {
                    // Username doesn't exist, display a generic error message
                    $login_err = "Invalid username or password.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

}

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    if (isset($_SESSION["username"])) {
        // Prepare a select statement
        $sql = "SELECT Name, DiscordID, DiscordName FROM account WHERE Name = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);

            // Set parameters
            $param_username = $_SESSION["username"];

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_store_result($stmt);

                // Check if username exists, if yes then verify password
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $username, $discordID, $discordName);
                    if (mysqli_stmt_fetch($stmt)) {
                        if ($discordID == "" || $discordID == null || $discordName == "" || $discordName == null) {
                            if (isset($_SESSION['user_id'])) {
                                $sql2 = "UPDATE account SET DiscordID = ?, DiscordName = ? WHERE Name = ?";

                                if ($stmt2 = mysqli_prepare($link, $sql2)) {
                                    // Bind variables to the prepared statement as parameters
                                    mysqli_stmt_bind_param($stmt2, "sss", $param_discordID, $param_discordName, $param_username);

                                    // Set parameters
                                    $param_discordID = $_SESSION['user_id'];
                                    $param_discordName = $_SESSION['discord_username'] . '#' . $_SESSION['discrim'];
                                    $param_username = $_SESSION["username"];

                                    // Attempt to execute the prepared statement
                                    if (mysqli_stmt_execute($stmt2)) {
                                        // Discord linked successfully. Destroy the session, and redirect to login page
                                        session_destroy();
                                        header("location: index.php");
                                        exit();
                                    } else {
                                        echo $_SESSION["username"];
                                    }

                                    // Close statement
                                    mysqli_stmt_close($stmt2);
                                }
                            }
                        }
                    } else {
                        // Username doesn't exist, display a generic error message
                        $login_err = "Invalid username or password.";
                    }
                } else {
                    echo "Oops! Something went wrong. Please try again later.";
                }

                // Close statement
                mysqli_stmt_close($stmt);
            }

        };


    }


}

// Close connection
mysqli_close($link);
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
            $auth_url = url($client_id, $redirect_url, $scopes);

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

            <h1 class="my-3" style="text-align: center;">Hi<?php if (isset($_SESSION['username'])) {
                    echo ", <b>" . htmlspecialchars($_SESSION["username"]) . "</b>";
                }
                if ($discordName != null) {
                    echo " (" . $discordName . ")";
                } ?>!</h1>


            <?php if (!isset($_SESSION['username'])) { ?>

                <h2>Login</h2>
                <p>Please fill in your credentials to login.</p>

                <?php
                if (!empty($login_err)) {
                    echo '<div class="alert alert-danger">' . $login_err . '</div>';
                }
                ?>

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username"
                               class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>"
                               value="<?php echo $username; ?>">
                        <span class="invalid-feedback"><?php echo $username_err; ?></span>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password"
                               class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                        <span class="invalid-feedback"><?php echo $password_err; ?></span>
                    </div>
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary" value="Login">
                    </div>
                </form>

            <?php } else { ?>
                <div class="center">

                    <a href="reset-password.php" class="btn btn-warning mx-auto" style="margin: 10px">Reset Your
                        Password</a>

                    <a href="includes/logout.php" class="btn btn-danger ml-3 mx-auto">Logout</a>

                </div>

            <?php } ?>

            <?php if (($discordID == null || $discordName == null) && isset($_SESSION['username'])) { ?>
                <div style="margin-top: 50px;" class="center">
                    <a href="<?php echo $auth_url ?>">
                        <button class='btn log-in'>Connect Discord</button>
                    </a>
                </div>
            <?php } ?>

        </div>
    </div>
</div>

</body>

</html>