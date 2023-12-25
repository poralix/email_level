#!/usr/local/bin/php -n
<?php
include("include.php");

parse_str($_SERVER["POST"], $_POST);
$action = (isset($_POST['action']) && $_POST['action']) ? $_POST['action'] : false;
$domain = (isset($_POST['domain']) && $_POST['domain']) ? $_POST['domain'] : false;
$http = (isset($_POST['http']) && $_POST['http']) ? $_POST['http'] : false;
$http2 = (isset($_POST['http2']) && $_POST['http2']) ? $_POST['http2'] : false;
$path = (isset($_POST['path']) && $_POST['path']) ? $_POST['path'] : false;
$path2 = (isset($_POST['path2']) && $_POST['path2']) ? $_POST['path2'] : false;

if (!is_domain($domain))
{
        echo "Make sure you've entered a valid domain name.";
        exit(0);
}

//action=install&domain=fc6.com&http=yes&path=email&https=yes&path2=email
if ($action == "install")
{

	//check for domain:
	$home = getenv("HOME");
	$domain_path = $home."/domains/".$domain;

	if (!file_exists($domain_path))
	{
		echo "cannot find $domain_path, aborting<br>\n";
		exit(0);
	}

	$count=0;

	if (isset($http) && $http == "yes")
	{
		if (!is_simple_path($path))
		{
			echo "please enter a valid path for http!<br>\n";
			exit(0);
		}

		create_to_path($domain_path."/public_html", $path, "http");
		$count++;
	}
        if (isset($https) && $https == "yes")
        {
                if (!is_simple_path($path2))
                {
                        echo "please enter a valid path for https!<br>\n";
                        exit(0);
                }

                create_to_path($domain_path."/private_html", $path2, "https");
                $count++;
        }

	if ($count == 0)
	{
		echo "Please select at least one protocol!  (http or https)<br>\n";
		exit(0);
	}
}
else
{
	echo "please enter a valid action!<br>\n";
	exit(0);
}



function create_to_path($dir_path, $endpath, $prot)
{
	if (!is_dir($dir_path))
	{
		echo "make sure $dir_path exists. Aborting.<br>\n";
		return;
	}
	
	$full_path = $dir_path."/".$endpath;
	if (!is_dir($full_path))
	{
		//ok, we'll be nice and try to create it.
		mkdir($full_path, 0755); //don't worry about the return value yet.
	}
	
	if (!is_dir($full_path))
	{
		echo "Could not find the directory $full_path.  Aborting.<br>\n";
		return;
	}

	//ok, so we have a path, now lets copy the data.
	global $plugin_path, $domain;
	$data = $plugin_path."/data";

	dir_copy($data, $full_path);

	echo "Data installed into ".$full_path."!<br>\n";

	echo "<a target=_blank href='".$prot."://".$domain."/".$endpath."/'>Click Here</a> to access it via the web.<br>\n";

	echo "<br><br>\n";
}
?>
