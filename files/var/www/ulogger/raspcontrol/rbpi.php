<?php

namespace raspcontrol;

class Rbpi {

  const EXT_IP_SERVER = 'http://checkip.dyndns.org';

  public static function distribution() {
    $distroTypeRaw = exec("cat /etc/*-release | grep PRETTY_NAME=", $out);
    $distroTypeRawEnd = str_ireplace('PRETTY_NAME="', '', $distroTypeRaw);
    $distroTypeRawEnd = str_ireplace('"', '', $distroTypeRawEnd);
    return $distroTypeRawEnd;
  }

  public static function kernel() {
    return exec("uname -mrs");
  }

  public static function firmware() {
    return exec("uname -v");
  }

  public static function hostname($full = false) {
    return $full ? exec("hostname -f") : gethostname();
  }

  public static function webServer() {
    return $_SERVER['SERVER_SOFTWARE'];
  }

  public static function ip() {
    return $_SERVER['SERVER_ADDR'];
    //$ip_address = shell_exec("/sbin/ifconfig eth0 | grep 'inet addr' | awk -F: '{print $2}' | awk '{print $1}'");
  }

  public static function subnet() {
    return shell_exec("/sbin/ifconfig eth0 | grep 'inet addr' | awk -F: '{print $4}' | awk '{print $1}'");
  }

  public static function gateway() {
    return shell_exec("/sbin/route -n | grep '^0.0.0.0' | awk '{print $2}'");;
  }

  public static function extIp() {
    if ($extip = @file_get_contents(Rbpi::EXT_IP_SERVER)) {
      $extip = strip_tags($extip);
      return trim(substr($extip, strpos($extip, ':')+1));
    }
    else {
      return FALSE;
    }
  }

  public static function ifconfig() {
    return shell_exec('/sbin/ifconfig');
  }

  public static function route() {
    return shell_exec('/sbin/route -n');
  }

}

?>