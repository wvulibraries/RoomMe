#!/bin/bash

# Base PRE Setup

GITDIR="/tmp/git"
ENGINEAPIGIT="https://github.com/wvulibraries/engineAPI.git"
ENGINEBRANCH="master"
ENGINEAPIHOME="/home/engineAPI"

SERVERURL="/home/www.libraries.wvu.edu"
DOCUMENTROOT="public_html"
SITEROOT=$DOCUMENTROOT/services/rooms
SQLFILES="/vagrant/sqlFiles/migrations/*.sql"

yum -y install \
  httpd httpd-devel httpd-manual httpd-tools \
  mysql-connector-java mysql-connector-odbc mysql-devel mysql-lib mysql-server \
  mod_auth_kerb mod_auth_mysql mod_authz_ldap mod_evasive mod_perl mod_security mod_ssl mod_wsgi \
  php php-bcmath php-cli php-common php-gd php-ldap php-mbstring php-mcrypt php-mysql php-odbc php-pdo \
  php-pear php-pear-Benchmark php-pecl-apc php-pecl-imagick php-pecl-memcache php-soap php-xml php-xmlrpc \
  emacs emacs-common emacs-nox git

yum -y update

mv /etc/httpd/conf.d/mod_security.conf /etc/httpd/conf.d/mod_security.conf.bak
/etc/init.d/httpd start
chkconfig httpd on

mkdir -p $GITDIR
cd $GITDIR
git clone -b $ENGINEBRANCH $ENGINEAPIGIT
git clone https://github.com/wvulibraries/engineAPITemplates.git
git clone https://github.com/wvulibraries/engineAPI-Modules.git

mkdir -p $SERVERURL/phpincludes/
ln -s $GITDIR/engineAPITemplates/* $GITDIR/engineAPI/engine/template/
ln -s $GITDIR/engineAPI-Modules/src/modules/* $GITDIR/engineAPI/engine/engineAPI/latest/modules/
ln -s $GITDIR/engineAPI/engine/ $SERVERURL/phpincludes/

rm /tmp/git/engineAPI/engine/template/rooms2015
ln -s /vagrant/serverConfiguration/genericTemplate/ $GITDIR/engineAPI/engine/template/rooms2015

# ln -s /tmp/git/engineAPITemplates/library2012.1col/ /tmp/git/engineAPI/engine/template/library2012.1col
# ln -s /tmp/git/engineAPITemplates/library2012.2col/ /tmp/git/engineAPI/engine/template/library2012.2col
# ln -s /tmp/git/engineAPITemplates/library2012.2col.right/ /tmp/git/engineAPI/engine/template/library2012.2col.right
# ln -s /tmp/git/engineAPITemplates/library2012.3col/ /tmp/git/engineAPI/engine/template/library2012.3col
# ln -s /tmp/git/engineAPITemplates/default/ /tmp/git/engineAPI/engine/template/default
ln -s /tmp/git/engineAPITemplates/library2012.1col/templateIncludes/ /home/www.libraries.wvu.edu/public_html/templateIncludes

rm -f $GITDIR/engineAPI/engine/engineAPI/latest/config/defaultPrivate.php
ln -s /vagrant/serverConfiguration/defaultPrivate.php $GITDIR/engineAPI/engine/engineAPI/latest/config/defaultPrivate.php

rm -f /etc/hosts
ln -s /vagrant/serverConfiguration/hosts /etc/hosts

mkdir -p /home/www.libraries.wvu.edu/public_html/hours
ln -s /vagrant/serverConfiguration/rss.php /home/www.libraries.wvu.edu/public_html/hours/rss.php
mkdir -p /home/database.lib.wvu.edu/public_html/cgi-bin/
ln -s /vagrant/serverConfiguration/fines.pl /home/database.lib.wvu.edu/public_html/cgi-bin/fines.pl
chmod a+x /home/database.lib.wvu.edu/public_html/cgi-bin/fines.pl

# Application Specific

# set the timezone
ln -sf /usr/share/zoneinfo/America/New_York /etc/localtime

mkdir -p $SERVERURL/$DOCUMENTROOT/services

ln -s /vagrant/serverConfiguration/docroot_index.php $SERVERURL/$DOCUMENTROOT/index.php
ln -s /vagrant/src/ $SERVERURL/$SITEROOT
ln -s $SERVERURL/phpincludes/engine/engineAPI/latest $SERVERURL/phpincludes/engine/engineAPI/4.0

rm -f /etc/php.ini
rm -f /etc/httpd/conf/httpd.conf

ln -s /vagrant/serverConfiguration/php.ini /etc/php.ini
ln -s /vagrant/serverConfiguration/vagrant_httpd.conf /etc/httpd/conf/httpd.conf
mkdir -p /vagrant/serverConfiguration/serverlogs
touch /vagrant/serverConfiguration/serverlogs/error_log
/etc/init.d/httpd restart

mkdir -p $SERVERURL/phpincludes/databaseConnectors/
ln -s /vagrant/serverConfiguration/database.lib.wvu.edu.remote.php $SERVERURL/phpincludes/databaseConnectors/database.lib.wvu.edu.remote.php

# Template
mkdir -p $GITDIR/engineAPITemplates/library2012.2col/templateIncludes
ln -s /vagrant/serverConfiguration/templateHeader.php $GITDIR/engineAPITemplates/library2012.2col/templateIncludes/templateHeader.php
ln -s /vagrant/serverConfiguration/templateFooter.php $GITDIR/engineAPITemplates/library2012.2col/templateIncludes/templateFooter.php
ln -s $GITDIR/engineAPITemplates/library2012.1col/templateIncludes/2colHeaderIncludes.php $GITDIR/engineAPITemplates/library2012.2col/templateIncludes/2colHeaderIncludes.php

mkdir -p $GITDIR/engineAPITemplates/library2012.3col/templateIncludes
ln -s /vagrant/serverConfiguration/templateHeader.php $GITDIR/engineAPITemplates/library2012.3col/templateIncludes/templateHeader.php
ln -s /vagrant/serverConfiguration/templateFooter.php $GITDIR/engineAPITemplates/library2012.3col/templateIncludes/templateFooter.php
ln -s $GITDIR/engineAPITemplates/library2012.1col/templateIncludes/3colHeaderIncludes.php $GITDIR/engineAPITemplates/library2012.3col/templateIncludes/3colHeaderIncludes.php

#favicon
touch /home/www.libraries.wvu.edu/public_html/favicon.ico

# Base Post Setup

ln -s $SERVERURL $ENGINEAPIHOME
ln -s $GITDIR/engineAPI/public_html/engineIncludes/ $SERVERURL/$DOCUMENTROOT/engineIncludes

## Setup the EngineAPI Database

/etc/init.d/mysqld start
chkconfig mysqld on
mysql -u root < /tmp/git/engineAPI/sql/vagrantSetup.sql
mysql -u root EngineAPI < /tmp/git/engineAPI/sql/EngineAPI.sql

# application Post Setup

mysql -u root < /vagrant/sqlFiles/setup.sql
mysql -u root roomReservations < /vagrant/sqlFiles/baseSnapshot.sql

# import backup if exists
if [ -e /vagrant/sqlFiles/roomReservations.sql ]
then
    mysql -u root roomReservations < /vagrant/sqlFiles/roomReservations.sql
else
    for f in $SQLFILES
    do
      echo "Processing $f ..."
      mysql -u root roomReservations < $f
    done
fi

# mock authentication database setup
mysql -u root < /vagrant/sqlFiles/authenticationStructureOnly.sql
mysql -u root authentication < /vagrant/sqlFiles/authenticationSetup.sql
