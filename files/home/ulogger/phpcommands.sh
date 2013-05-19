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
  
  get_tar_comment)  
    tar --test-label -f $2
    ;;

  extract_tar)  
    #tar zxvfp $2 -C /tmp/slask #test
    tar zxvfp $2 -C /
    errorcode=$?
    if [[ $errorcode != 0 ]] ; then
        echo ulogger-tar-error $errorcode
    fi        
    ;;
    
  test)
    ls /
    ;;

  *)
    echo $"Usage: $0 {command}"
    exit 1

esac