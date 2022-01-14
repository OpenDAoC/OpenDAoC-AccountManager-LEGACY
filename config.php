<?php
/* Database credentials. Assuming you are running MySQL
server with default setting (user 'root' with no password) */
define('DB_SERVER', '172.19.208.1');
define('DB_USERNAME', 'atlas');
define('DB_PASSWORD', 'atlas');
define('DB_NAME', 'atlas');

/* Attempt to connect to MySQL database */
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

function cryptPassword($pass)
{
    $len = strlen($pass);
    $res = "";
for ($i = 0; $i < $len; $i++)
    {
        $res = $res . chr(ord(substr($pass, $i, 1)) >> 8);
        $res = $res . chr(ord(substr($pass, $i, 1)));
    }

    $hash = strtoupper(md5($res));
    $len = strlen($hash);
    for ($i = ($len-1)&~1; $i >= 0; $i-=2)
    {
        if (substr($hash, $i, 1) == "0")
            $hash = substr($hash, 0, $i) . substr($hash, $i+1, $len);
    }

    $crypted = "##" . $hash;
    return $crypted;
}

?>