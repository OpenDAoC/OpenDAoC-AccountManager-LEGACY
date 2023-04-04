<?php

# A function to redirect user.
function redirect($url)
{
    header('Location: '.$url);
    exit();
}

// DOL encryption function
function cryptPassword($pass)
{
    $len = strlen($pass);
    $res = "";
    for ($i = 0; $i < $len; $i++) {
        $res = $res . chr(ord(substr($pass, $i, 1)) >> 8);
        $res = $res . chr(ord(substr($pass, $i, 1)));
    }

    $hash = strtoupper(md5($res));
    $len = strlen($hash);
    for ($i = ($len - 1) & ~1; $i >= 0; $i -= 2) {
        if (substr($hash, $i, 1) == "0") {
            $hash = substr($hash, 0, $i) . substr($hash, $i + 1, $len);
        }
    }

    $crypted = "##" . $hash;
    return $crypted;
}

// Getting the GameAccount
function getGameAccount(string $DiscordID)
{
    if (empty($DiscordID)) {
        return null;
    }

    $dsn = 'mysql:host=' . DB_SERVER . ';dbname=' . DB_NAME;
    $pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD);

    $stmt = $pdo->prepare("SELECT Name FROM account WHERE DiscordID = :DiscordID");
    $stmt->execute(['DiscordID' => $DiscordID]);

    $gameAccount = $stmt->fetchColumn();

    if ($gameAccount) {
        return $gameAccount;
    } else {
        return null;
    }
}

function linkDiscord(string $gameAccount)
{
    $dsn = 'mysql:host=' . DB_SERVER . ';dbname=' . DB_NAME;
    $pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD);

    $stmt = $pdo->prepare("UPDATE account SET DiscordID = :discord WHERE Name = :username");

    $stmt->execute([
        'discord' => $_SESSION['user_id'],
        'username' => $gameAccount
    ]);

    $stmt = $pdo->prepare("UPDATE account SET DiscordName = :discordName WHERE Name = :username");

    $stmt->execute([
        'discordName' => $_SESSION['user']['username'] . '#' . $_SESSION['discrim'],
        'username' => $gameAccount
    ]);

    redirect("index.php");
}

function getCat(){
    $opts = [
        "http" => [
            "method" => "GET",
            "header" => "x-api-key: e0084554-0ee5-4687-aaf4-30b0a5f4b518"
        ]
    ];

    $context = stream_context_create($opts);
    $json = file_get_contents('https://api.thecatapi.com/v1/images/search?limit=1&size=small',false, $context);
    $obj = json_decode($json);
    return $obj[0]->url;
}

function getDog(){
    $json = file_get_contents('https://dog.ceo/api/breeds/image/random',);
    $obj = json_decode($json);
    return $obj->message;
}

function setDiscordName(string $gameAccount){
    $dsn = "mysql:host=".DB_SERVER.";dbname=".DB_NAME;
    $pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD);
    $sql = "UPDATE account SET DiscordName = ? WHERE Name = ?";
    $discordName = $_SESSION['user']['username'] . '#' . $_SESSION['discrim'];

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(1, $discordName, PDO::PARAM_STR);
    $stmt->bindParam(2, $gameAccount, PDO::PARAM_STR);

    if ($stmt->execute()) {
        // Password updated successfully. Destroy the session, and redirect to login page
        session_destroy();
        header("location: index.php");
        exit();
    } else {
        echo $_SESSION["username"];
    }
}