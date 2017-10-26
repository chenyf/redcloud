#! /bin/bash

root=/mnt/tlc-volume/
COUNT=3 INTERVAL=5 QUEUE='*' /usr/local/php/bin/php  ${root}index_cli.php --c=Queue --a=resqueAction &> ${root}Logs/resque.log
