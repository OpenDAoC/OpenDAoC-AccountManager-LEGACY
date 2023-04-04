<?php

# Enabling error display
error_reporting(E_ALL);
ini_set('display_errors', 1);

$username = $password = "";
$username_err = $password_err = $login_err = "";
$gameAccount = "";
# Including all the required scripts for demo
require __DIR__ . "/includes/functions.php";
require __DIR__ . "/includes/discord.php";
require __DIR__ . "/config.php";

# ALL VALUES ARE STORED IN SESSION!
# RUN `echo var_export([$_SESSION]);` TO DISPLAY ALL THE VARIABLE NAMES AND VALUES.
# FEEL FREE TO JOIN MY SERVER FOR ANY QUERIES - https://join.markis.dev

$request_method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : null;

if ($request_method == "POST") {
    // ...
    // Validate credentials
    if (empty($username_err) && empty($password_err)) {
        // Prepare a select statement
        $sql = "SELECT Account_ID, Name, Password, DiscordID FROM account WHERE Name = :username";
        $stmt = $link->prepare($sql);
        $stmt->bindParam(':username', $param_username, PDO::PARAM_STR);
        $param_username = $username;
        if ($stmt->execute()) {
            // Check if username exists, if yes then verify password
            if ($stmt->rowCount() == 1) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $id = $row["Account_ID"];
                $username = $row["Name"];
                $hashed_password = $row["Password"];
                $discordID = $row["DiscordID"];
                if($discordID != null){
                    $login_err = "This game account is already linked to another Discord.";
                } elseif (strcmp(cryptPassword($password), $hashed_password) == 0) {
                    // Password is correct, so start a new session
                    session_start();
                    // Store data in session variables
                    $_SESSION["loggedin"] = true;
                    $_SESSION["id"] = $id;
                    $_SESSION["username"] = $username;
                    linkDiscord($username);
                    setDiscordName($username);
                    // Redirect user to welcome page
                    // header("location: index.php");
                    header("Refresh:1");
                } else {
                    // Password is not valid, display a generic error message
                    $login_err = "Invalid username or password.";
                }
            } else {
                // Username doesn't exist, display a generic error message
                $login_err = "Invalid username or password.";
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
}

?>

<html lang="en">

<head>
    <title>OpenDAoC Account Manager</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
</head>

<body>

<header>
    <span class="logo">
        <a href="/"><img src="https://cdn.discordapp.com/avatars/865566424537104386/282901fdaa488f57a95faae665a7c245.webp" class="logo-img" alt="Logo">OpenDAoC Account Manager</a></span>

    <span class="menu">
			<?php
            $auth_url = url($client_id, $redirect_url, $scopes);
            if (isset($_SESSION['user'])) {
                if (getGameAccount($_SESSION['user_id']) != null){
                    echo '<a href="#" class="changePet small" id="changePet"></a>';
                }
                echo '<a href="includes/logout.php"><button class="btn btn-danger log-out">LOGOUT</button></a>';
            } else {
                echo "<a href='$auth_url'><button class='btn btn-success log-in'>LOGIN</button></a>";
            }
            ?>
		</span>
</header>
<?php
if (!isset($_SESSION['user'])) {?>
    <br><h3 class='center'>Login with Discord via the top-right button first. </h3><br/>
<?php } else { ?>
    <br><h3 class='center'><?php echo "Welcome, " . $_SESSION['user']['username'] . '#' . $_SESSION['discrim'] . '!'?></h3><br/>
    <?php $gameAccount = getGameAccount($_SESSION['user_id']);
    if ($gameAccount != null) {
        echo "<p class='center'>Your linked game account is: <b>$gameAccount</b></p>";
     }
}
?>

<?php if ($gameAccount == null && isset($_SESSION['user'])){

    if (!empty($login_err)) {
        echo '<div class="alert alert-danger">' . $login_err . '</div>';
    }
    ?>
    <div class="row justify-content-start center">
        <div class="col-3"></div>
        <div class="col-6">
            <div class="alert alert-warning center" role="alert">Login with your game account to link with Discord<br><small>You will be required to login again.</small></div>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username"
                           class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>"
                           value="<?php echo $username; ?>">
                    <span class="error-msg"><?php echo $username_err; ?></span>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password"
                           class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                    <span class="error-msg"><?php echo $password_err; ?></span>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Login">
                </div>
            </form>

            Don't have an account? <br>
            <a href="register.php" class="btn btn-success" role="button">Register here</a>
            <div class="col-3"></div>
        </div>
    </div>


    </div>
<?php
} else if ($gameAccount != null && isset($_SESSION['user'])) {

?>
    <div class="row center">
        <div class="col-3"></div>
        <div class="col-6">
            <a href="reset-password.php" class="btn btn-warning mx-auto " style="margin: 10px">Change Password</a>
        </div>

        <div class="col-3"></div>
    </div>
    <div class="row center" id="qol-dog">
        <img src="" alt="" class="center qol-pet" id="qol-pet">
    </div>
    <div class="row center" id="qol-dog">
<!--        <a href="#" class="changePet small" id="changePet" style="margin:auto;"></a>-->
    </div>

<?php } ?>

<script>
    $(document).ready(function() {
        var showDog = localStorage.getItem("showDog");

        if (showDog == "true") {
            var qolpeturl = "<?php echo getDog() ?>";
            var text = "🐱";
        } else {
            var qolpeturl = "<?php echo getCat() ?>";
            var text = "🐶";
        }
        document.getElementById("qol-pet").src = qolpeturl;
        document.getElementById("changePet").textContent = text;

    });


    $(".changePet").on("click", function() {
        if (document.getElementById("changePet").textContent == "🐶") {
            localStorage.setItem("showDog", true);
            var qolpeturl = "<?php echo getDog() ?>";
            var text = "🐱";
        } else {
            localStorage.setItem("showDog", false);
            var qolpeturl = "<?php echo getCat() ?>";
            var text = "🐶";
        }
        document.getElementById("qol-pet").src = qolpeturl;
        document.getElementById("changePet").textContent = text;
        return false;
    });
</script>

</body>

</html>
