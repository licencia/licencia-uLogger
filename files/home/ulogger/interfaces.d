#iface eth0 inet dhcp
iface eth0 inet static
address 192.168.1.100
gateway 192.168.1.1
netmask 255.255.255.0
dns-nameservers 192.168.1.1 8.8.8.8 8.8.4.4
