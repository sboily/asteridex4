# asteridex4

http://pbxinaflash.com/community/threads/viva-xivo-building-incredible-pbx.19238/page-7#post-121371

Installation
------------

Go on your xivo:

    wget https://github.com/sboily/asteridex4/archive/0.1.1.tar.gz
    mkdir -p /usr/share/xivo-web-interface/www/asteridex4/templates_c
    tar xfvz 0.1.1.tar.gz --strip-components=1 -C /usr/share/xivo-web-interface/www/asteridex4/
    chown www-data /usr/share/xivo-web-interface/www/asteridex4/templates_c
    apt-get install smarty3

Configuration
-------------

You need to add a webservice user with this ACLs :

- confd.#
- ctid-ng.#

And configure the config.inc.php.

Using asteridex4
----------------

Enabled your CTI login/password to your user and connect to asteridex4 with it.

This fork use the postgres database from xivo. If you want to add new entries, use the phonebook in the xivo web interface.

Open your browser to https://xivo_ip/asteridex4/

Screenshots
-----------

![login screenshot](/screenshots/login.png?raw=true "login")

![main screenshot](/screenshots/main.png?raw=true "main")
