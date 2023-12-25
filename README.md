# About

This is a patched version of the plugin to work under PHP 8.x. No other issues are addressed. Original design and so on.

Original plugin: https://forum.directadmin.com/threads/email-level-plugin.22715/


# Installation

```
cd /usr/local/directadmin/plugins
git clone https://github.com/poralix/email_level.git
cd email_level
./scripts/install.sh
```

The installation script will overwrite `data/include/config.php` with a servername and port taken from Directadmin.
