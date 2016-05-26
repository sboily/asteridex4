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

Docker
------

You can use docker

    docker build -t asteridex4 .
    docker run -p 80:80 -t asteridex4

It's possible to import all of this variable

- XIVO_HOST
- XIVO_HOST_DB
- XIVO_API_USER
- XIVO_API_PWD
- XIVO_BACKEND_USER

    docker run -p 80:80 -e XIVO_HOST=192.168.1.124 \
                        -e XIVO_HOST_DB=192.168.1.124 \
                        -e XIVO_API_USER=sylvain \
                        -e XIVO_API_PWD=sylvain \
                        -e XIVO_BACKEND_USER=xivo_user \
                        -t asteridex4

Screenshots
-----------

![login screenshot](/screenshots/login.png?raw=true "login")

![main screenshot](/screenshots/main.png?raw=true "main")
