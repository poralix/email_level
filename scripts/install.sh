#!/bin/sh

echo "Plugin Installed!"; #NOT! :)
cd /usr/local/directadmin/plugins/email_level;

for dir in user reseller admin; do
{
        chmod 755 $dir/*
        chown diradmin:diradmin $dir;
}
done;

exit 0;
