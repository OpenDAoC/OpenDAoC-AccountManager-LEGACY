<?php

use DotEnv;

(new DotEnv(__DIR__ . '/.env'))->load();

# SCOPES SEPARATED BY SPACE
# example: identify email guilds connections  
$scopes = "identify";

# REDIRECT URL
# example: https://mydomain.com/includes/login.php
# example: https://mydomain.com/test/includes/login.php


# IMPORTANT READ THIS:
# - Set the `$bot_token` to your bot token if you want to use guilds.join scope to add a member to your server
# - Check login.php for more detailed info on this.
# - Leave it as it is if you do not want to use 'guilds.join' scope.

# https://i.imgur.com/2tlOI4t.png (screenshot)
$bot_token = null;

/* Database credentials. Assuming you are running MySQL
server with default setting (user 'root' with no password) */

define("DB_SERVER", getenv('DB_SERVER'));
define("DB_USERNAME", getenv('DB_USERNAME'));
define("DB_PASSWORD", getenv('DB_PASSWORD'));
define("DB_NAME", getenv('DB_NAME'));
$redirect_url = getenv('REDIRECT_URL');
$client_id = getenv('CLIENT_ID');
$secret_id = getenv('SECRET_ID');


/* Attempt to connect to MySQL database */
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);


// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
