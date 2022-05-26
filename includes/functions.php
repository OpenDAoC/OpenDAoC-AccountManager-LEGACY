<?php

 
# A function to redirect user.
function redirect($url)
{
    if (!headers_sent())
    {    
        header('Location: '.$url);
        exit;
        }
    else
        {  
        echo '<script type="text/javascript">';
        echo 'window.location.href="'.$url.'";';
        echo '</script>';
        echo '<noscript>';
        echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
        echo '</noscript>';
        exit;
    }
}

# A function which returns users IP
function client_ip()
{
	if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
	{
		return $_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	else
	{
		return $_SERVER['REMOTE_ADDR'];
	}
}

# Check user's avatar type
function is_animated($avatar)
{
	$ext = substr($avatar, 0, 2);
	if ($ext == "a_")
	{
		return ".gif";
	}
	else
	{
		return ".png";
	}
}
// DOL encryption function
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

// Getting the GameAccount
function getGameAccount(string $DiscordID){
    if (empty($DiscordID)){
        return null;
    }

    $link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    $sql = "SELECT Name FROM account WHERE DiscordID = ?";

    if ($stmt = mysqli_prepare($link, $sql)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "s", $param_DiscordID);

        // Set parameters
        $param_DiscordID = $DiscordID;

        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Store result
            mysqli_stmt_store_result($stmt);

            // Check if username exists, if yes then verify password
            if (mysqli_stmt_num_rows($stmt) == 1) {
                // Bind result variables
                mysqli_stmt_bind_result($stmt, $gameAccount);
                if (mysqli_stmt_fetch($stmt)) {
                    return $gameAccount;
                }
            } else {
                return null;
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    mysqli_close($link);
}

function linkDiscord(string $gameAccount){
    $link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    $sql = "UPDATE account SET DiscordID = ? WHERE Name = ?";

    if ($stmt = mysqli_prepare($link, $sql)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "ss", $param_discord, $param_username);

        // Set parameters
        $param_username = $gameAccount;
        $param_discord = $_SESSION['user_id'];
        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Password updated successfully. Destroy the session, and redirect to login page
            session_destroy();
            header("location: index.php");
            exit();
        } else {
            echo $_SESSION["username"];
        }

        // Close statement
        mysqli_stmt_close($stmt);
    }

    $sql = "UPDATE account SET DiscordName = ? WHERE Name = ?";

    if ($stmt = mysqli_prepare($link, $sql)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "ss", $param_discord, $param_username);

        // Set parameters
        $param_discord = $_SESSION['user']['username'] . '#' . $_SESSION['discrim'];
        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Password updated successfully. Destroy the session, and redirect to login page
            session_destroy();
            header("location: index.php");
            exit();
        } else {
            echo $_SESSION["username"];
        }

        // Close statement
        mysqli_stmt_close($stmt);
    }

    mysqli_close($link);
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
    $link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    $sql = "UPDATE account SET DiscordName = ? WHERE Name = ?";
    $discordName = $_SESSION['user']['username'] . '#' . $_SESSION['discrim'];

    if ($stmt = mysqli_prepare($link, $sql)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "ss", $param_discord, $param_username);

        // Set parameters
        $param_discord = $discordName;
        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Password updated successfully. Destroy the session, and redirect to login page
            session_destroy();
            header("location: index.php");
            exit();
        } else {
            echo $_SESSION["username"];
        }

        // Close statement
        mysqli_stmt_close($stmt);
    }

    mysqli_close($link);
}

class DotEnv
{
	/**
	 * The directory where the .env file can be located.
	 *
	 * @var string
	 */
	protected $path;


	public function __construct(string $path)
	{
		if(!file_exists($path)) {
			throw new \InvalidArgumentException(sprintf('%s does not exist', $path));
		}
		$this->path = $path;
	}

	public function load() :void
	{
		if (!is_readable($this->path)) {
			throw new \RuntimeException(sprintf('%s file is not readable', $this->path));
		}

		$lines = file($this->path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		foreach ($lines as $line) {

			if (strpos(trim($line), '#') === 0) {
				continue;
			}

			list($name, $value) = explode('=', $line, 2);
			$name = trim($name);
			$value = trim($value);

			if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
				putenv(sprintf('%s=%s', $name, $value));
				$_ENV[$name] = $value;
				$_SERVER[$name] = $value;
			}
		}
	}
}
