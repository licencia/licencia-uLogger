Licencia uLogger är ett enkelt verktyg för dig som behöver fjärrövervaka trafiken till din iPECS och passar utmärkt för loggning av t.ex. SIP-trafik. Poängen är att du kan starta, hämta och administrera pcap-loggar ute hos kund utan att behöva lämna en PC, allt som behövs är en uLogger och en switch med spegling.

### Grundläggande funktionalitet
- Starta, stoppa och ladda hem pcap-loggar
- Loggfilernas storlek kan begränsas eller så kan ringbuffer användas
- Filter för att t.ex. bara spara SIP-trafik stöds
- Fjärradministration via webbadmin
- Enkel VPN-koppling

### Access och lösenord till uLogger
- IP-adress: 192.168.1.100/255.255.255.0
- Webbadmin: admin/admin
- CLI: pi/ulogger
- VPN: ulogger

### Anslut till uLogger
- Montera SD-kortet (metallbläcken ska vara vända uppåt). Kortet ligger i ett vitt kuvert.
- Anslut uLogger till port 5 i switchen med en nätverkskabel.
- Anslut strömmen till uLogger.
- Starta en webbrowser och anslut mot 192.168.1.100. För att detta ska fungera måste din PC ligga i subnätet 192.168.1.x/255.255.255.0.

![img](/files/var/www/img/ulogger-01.png)
![img](/files/var/www/img/ulogger-02.png)
