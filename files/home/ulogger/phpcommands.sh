#!/bin/bash

case "$1" in
  restart_apache)
    apache2ctl restart
    ;;

  reload_apache)
    apachectl -k graceful
    ;;

  reboot)
    if pidof tcpdump > /dev/null
    then
      killall tcpdump
    fi    
    reboot
    ;;

  halt)
    if pidof tcpdump > /dev/null
    then
      killall tcpdump
    fi    
    halt
    ;;

  kill_tcpdump)
    killall tcpdump
    ;;

  restart_eth0)
    ifdown eth0 && ifup eth0
    ;;

  test)
    ls /
    ;;

  *)
    echo $"Usage: $0 {command}"
    exit 1

esac