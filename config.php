<?php
# CLIENT ID
# https://i.imgur.com/GHI2ts5.png (screenshot)
$client_id = "931670383025856563";

# CLIENT SECRET
# https://i.imgur.com/r5dYANR.png (screenshot)
$secret_id = "6rb00a86WzjW3RBP7L_j6oF-ompVKtGz";

# SCOPES SEPARATED BY SPACE
# example: identify email guilds connections  
$scopes = "identify";

# REDIRECT URL
# example: https://mydomain.com/includes/login.php
# example: https://mydomain.com/test/includes/login.php
$redirect_url = "http://localhost:8080/includes/login.php";

# IMPORTANT READ THIS:
# - Set the `$bot_token` to your bot token if you want to use guilds.join scope to add a member to your server
# - Check login.php for more detailed info on this.
# - Leave it as it is if you do not want to use 'guilds.join' scope.

# https://i.imgur.com/2tlOI4t.png (screenshot)
$bot_token = null;


/* Database credentials. Assuming you are running MySQL
server with default setting (user 'root' with no password) */
const DB_SERVER = '172.19.208.1';
const DB_USERNAME = 'atlas';
const DB_PASSWORD = 'atlas';
const DB_NAME = 'atlas';

/* Attempt to connect to MySQL database */
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
