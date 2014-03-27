#!/bin/bash

# Base PRE Setup

GITDIR="/tmp/git"
ENGINEAPIGIT="https://github.com/wvulibraries/engineAPI.git"
ENGINEBRANCH="engineAPI-3.x"
ENGINEAPIHOME="/home/engineAPI"

SERVERURL="/home/www.libraries.wvu.edu"
DOCUMENTROOT="public_html/instruction/rooms"
SQLFILES="/vagrant/sqlFiles/migrations/*.sql"

yum -y install httpd httpd-devel httpd-manual httpd-tools
yum -y install mysql-connector-java mysql-connector-odbc mysql-devel mysql-lib mysql-server
yum -y install mod_auth_kerb mod_auth_mysql mod_authz_ldap mod_evasive mod_perl mod_security mod_ssl mod_wsgi 
yum -y install php php-bcmath php-cli php-common php-gd php-ldap php-mbstring php-mcrypt php-mysql php-odbc php-pdo php-pear php-pear-Benchmark php-pecl-apc php-pecl-imagick php-pecl-memcache php-soap php-xml php-xmlrpc 
yum -y install emacs emacs-common emacs-nox
yum -y install git

mv /etc/httpd/conf.d/mod_security.conf /etc/httpd/conf.d/mod_security.conf.bak
/etc/init.d/httpd start

mkdir -p $GITDIR
cd $GITDIR
git clone -b $ENGINEBRANCH $ENGINEAPIGIT

mkdir -p $SERVERURL/phpincludes/
ln -s $GITDIR/engineAPI/engine/ $SERVERURL/phpincludes/

# Application Specific

mkdir -p /home/www.libraries.wvu.edu/public_html/instruction

ln -s /vagrant/src $SERVERURL/$DOCUMENTROOT
ln -s $SERVERURL/phpincludes/engine/engineAPI/3.1 $SERVERURL/phpincludes/engine/engineAPI/latest

rm -f /etc/php.ini
rm -f /etc/httpd/conf/httpd.conf

ln -s /vagrant/serverConfiguration/php.ini /etc/php.ini
ln -s /vagrant/serverConfiguration/vagrant_httpd.conf /etc/httpd/conf/httpd.conf

# mkdir -p /home/mfcs.lib.wvu.edu/public_html/javascript/
# ln -s /tmp/git/engineAPI/engine/template/distribution/public_html/js /home/mfcs.lib.wvu.edu/public_html/javascript/distribution

mkdir -p /vagrant/serverConfiguration/serverlogs
touch /vagrant/serverConfiguration/serverlogs/error_log
/etc/init.d/httpd restart
chkconfig httpd on

# Base Post Setup

ln -s $SERVERURL $ENGINEAPIHOME
ln -s /tmp/git/engineAPI/public_html/engineIncludes $SERVERURL/$DOCUMENTROOT/engineIncludes

## Setup the EngineAPI Database

/etc/init.d/mysqld start
chkconfig mysqld on
mysql -u root < /tmp/git/engineAPI/sql/vagrantSetup.sql
mysql -u root EngineAPI < /tmp/git/engineAPI/sql/EngineAPI.sql

# application Post Setup

mysql -u root < /vagrant/sqlFiles/setup.sql
mysql -u root roomReservations < /vagrant/sqlFiles/baseSnapshot.sql

for f in $SQLFILES
do
  echo "Processing $f ..."
  mysql -u root roomReservations < $f
done
