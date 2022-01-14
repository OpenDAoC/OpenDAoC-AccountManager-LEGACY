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

?>
