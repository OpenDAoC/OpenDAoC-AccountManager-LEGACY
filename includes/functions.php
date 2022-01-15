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
