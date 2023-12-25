<?php

$plugin_path="/usr/local/directadmin/plugins/email_level";

function is_domain($domain)
{
        if (strlen($domain) > 60) return false;
	return preg_match('/^(([a-z0-9\-])+\.)+([a-zA-Z0-9]{2,10})$/', $domain);
}

function is_simple_path($line)
{
	return preg_match('/^([a-zA-Z0-9\/\ _-])+$/', $line);
}


//referenced:
//http://www.php.net/copy
function dir_copy( $source, $target )
{
	if ( is_dir( $source ) )
        {
            @mkdir( $target );
            
            $d = dir( $source );
            
            while ( FALSE !== ( $entry = $d->read() ) ) 
            {
                if ( $entry == '.' || $entry == '..' )
                {
                    continue;
                }
                
                $Entry = $source . '/' . $entry;            
                if ( is_dir( $Entry ) )
                {
                    dir_copy( $Entry, $target . '/' . $entry );
                    continue;
                }
                copy( $Entry, $target . '/' . $entry );
            }
            
            $d->close();
        }else
        {
            copy( $source, $target );
        }
}


?>
