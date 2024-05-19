#!/bin/bash

# expircion de segudnos
EXPIRATION_TIME=30

# credenciales
MYSQL_USER="kafl"
MYSQL_PASSWORD="kafl"
DATABASE="KAFL"

# timepo en maquina
CURRENT_TIME=$(date +%s)

# Calculo de timepo
EXPIRATION_TIMESTAMP=$((CURRENT_TIME - EXPIRATION_TIME))

# borra cookies vijeas
QUERY="DELETE FROM user_cookies WHERE FECHA_caducida <= FROM_UNIXTIME($EXPIRATION_TIMESTAMP)"

# ejecutar pra elminar
mysql -u $MYSQL_USER -p$MYSQL_PASSWORD $DATABASE -e "$QUERY"

# errores
if [ $? -eq 0 ]; then
    echo "deu viejas cookies."
else
    echo "algo fallo."
fi
