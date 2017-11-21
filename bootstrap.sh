#!/bin/sh

sudo su
apt-get update

#utilities
apt-get install curl
apt-get install git

#php7.1
add-apt-repository ppa:ondrej/php -y
apt-get update
apt-get install php7.1 -q -y
apt-get install php7.1-xdebug -q -y

#for factorio
apt-get install php7.1-xml -q -y
apt-get install php7.1-mbstring -q -y
apt-get install zip -q -y
apt-get install unzip -q -y
apt-get install php7.1-zip -q -y
apt-get install php7.1-gd -q -y
apt-get install php7.1-mcrypt -q -y

#composer
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer

#TODO config mail, twig debug