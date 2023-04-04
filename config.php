<?php

require_once __DIR__ . '/vendor/autoload.php'; // Make sure to include the Composer autoload file

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

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

const PROHIBITED_CHARACTERS = [" ", "#", "&", "%", ".", "!", "^", "_", "-"];

define("DB_SERVER", $_ENV['DB_SERVER']);
define("DB_USERNAME", $_ENV['DB_USERNAME']);
define("DB_PASSWORD", $_ENV['DB_PASSWORD']);
define("DB_NAME", $_ENV['DB_NAME']);
$redirect_url = $_ENV['REDIRECT_URL'];
$client_id = $_ENV['CLIENT_ID'];
$secret_id = $_ENV['SECRET_ID'];

/* Attempt to connect to MySQL database */
try {
    $dsn = "mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME;
    $link = new PDO($dsn, DB_USERNAME, DB_PASSWORD);
    $link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}