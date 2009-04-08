#!/bin/sh

USERNAME=turtle
DATABASE=turtle
HOST=localhost
PASSWORD=turtle

LIST=$(mysql --skip-column-names -h $HOST -u $USERNAME -p$PASSWORD $DATABASE < users.sql)

for I in $LIST; do

    cat /home/librefm/scripts/license > /home/librefm/turtle/data/$I.dump
    
    mysqldump -h $HOST -u $USERNAME -p$PASSWORD  $DATABASE Scrobbles "--where=(username='$I')" >> /home/librefm/turtle/data/$I.dump 
done

