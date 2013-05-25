#!/bin/bash

# Skapa instfil
# - Kopiera alla filer i ulogger/files till /home/install.make
# - sudo mv /home/install.make/home/make.sh /home/make.sh
# - sudo chmod 755 /home/make.sh
# - sudo /home/make.sh
# Installera
# - sudo tar zxvfp /home/ulogger.1.0-beta1.tar.gz -C /


sudo chown root:www-data /home/install.make/var/www/FILES/trace
sudo chown root:www-data /home/install.make/var/www/FILES/uploads
sudo chown root:www-data /home/install.make/home/ulogger/phpcommands.sh
sudo chown root:www-data /home/install.make/home/ulogger/interfaces.d
sudo chown root:www-data /home/install.make/home/ulogger/resolv.conf
sudo chown root:www-data /home/install.make/etc/apache2/myports.conf
sudo chown root:www-data /home/install.make/etc/apache2/sites-available/default

sudo chmod 775 /home/install.make/var/www/FILES/trace
sudo chmod 775 /home/install.make/var/www/FILES/uploads
sudo chmod 750 /home/install.make/home/ulogger/phpcommands.sh
sudo chmod 664 /home/install.make/home/ulogger/interfaces.d
sudo chmod 664 /home/install.make/home/ulogger/resolv.conf
sudo chmod 664 /home/install.make/etc/apache2/myports.conf
sudo chmod 664 /home/install.make/etc/apache2/sites-available/default
sudo chmod 755 /home/install.make/etc/init.d/tightvncserver

VER=`cat /home/install.make/home/ulogger/version.info`
NAME="/home/ulogger.$VER.tar.gz"

cd /home/install.make
sudo tar -czvf $NAME --label="ulogger-update-$VER" .

echo
echo -----------------------------------------------------------
echo -e "Filnamn: $NAME (\033[33m$VER\033[0m)"

# Test
#tar -tf $NAME #print comment and files
#tar --test-label -f $NAME #print comment

#Kontrollerar om $VER = arkivets kommentar
if tar --test-label -f $NAME ulogger-update-$VER; then
  echo -e "\033[32mOK: Arkiv skapat"
else
  echo -e "\033[31mNot OK: Version matchar inte arkiv."
fi
echo -e "\033[0m"-----------------------------------------------------------
echo