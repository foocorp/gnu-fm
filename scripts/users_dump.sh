#!/bin/sh

USERNAME=
DATABASE=
HOST=
PASSWORD=

LIST=$(mysql --skip-column-names -h $HOST -u $USERNAME -p$PASSWORD $DATABASE < users.sql)

for I in $LIST; do 
    
    mysqldump -h $HOST -u $USERNAME -p$PASSWORD $DATABASE Scrobbles "--where=(username='$I')" > $I.dump
done

