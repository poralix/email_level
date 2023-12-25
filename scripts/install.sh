#!/bin/sh
#
# Updated By Poralix // www.poralix.com
#

echo "Plugin Installed!";
cd /usr/local/directadmin/plugins/email_level;

echo "<?php
\$host='"$(/usr/local/directadmin/directadmin c | grep ^servername= | cut -d= -f2)"';
\$port='"$(/usr/local/directadmin/directadmin c | grep ^port= | cut -d= -f2)"';
\$ssl=true;

\$show_roundcube=true;
\$show_squirrelmail=false;
\$show_uebimiau=false;

?>" > /usr/local/directadmin/plugins/email_level/data/include/config.php;

for dir in hooks user; do
{
        chmod 755 $dir/*
        chown diradmin:diradmin $dir;
}
done;

exit 0;
