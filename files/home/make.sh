#!/bin/bash

# Skapa instfil
# - Kopiera alla filer i ulogger/files till /home/install.make
# - sudo /home/make.sh
# Installera
# - sudo tar zxvfp /home/ulogger.1.x-dev.tar.gz -C /

sudo chown root:www-data /home/install.make/var/www/trace
sudo chown root:www-data /home/install.make/var/www/uploads
sudo chown root:www-data /home/install.make/home/ulogger/phpcommands.sh
sudo chown root:www-data /home/install.make/home/ulogger/interfaces.d
sudo chown root:www-data /home/install.make/etc/apache2/myports.conf
sudo chown root:www-data /home/install.make/etc/apache2/sites-available/default

sudo chmod 775 /home/install.make/var/www/trace
sudo chmod 775 /home/install.make/var/www/uploads
sudo chmod 750 /home/install.make/home/ulogger/phpcommands.sh
sudo chmod 660 /home/install.make/home/ulogger/interfaces.d
sudo chmod 660 /home/install.make/etc/apache2/myports.conf
sudo chmod 660 /home/install.make/etc/apache2/sites-available/default

VER=`cat /home/install.make/home/ulogger/version.info`
NAME="/home/ulogger.$VER.tar.gz"

cd /home/install.make
sudo tar -czvf $NAME --label="$VER" .

echo
echo -----------------------------------------------------------
echo -e "Creating file: $NAME (\033[33m$VER\033[0m)"

# Test
tar -tf $NAME #print comment and files
tar --test-label -f $NAME #print comment

#Kontrollerar om $VER = arkivets kommentar
if tar --test-label -f $NAME $VER; then
  echo -e "\033[32mOK: Arkiv skapat"
else
  echo -e "\033[31mNot OK: Version matchar inte arkiv."
fi
echo -e "\033[0m"-----------------------------------------------------------
echo