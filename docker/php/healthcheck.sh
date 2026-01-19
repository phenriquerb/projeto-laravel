#!/bin/sh
set -e

# Tenta verificar se o processo php-fpm está respondendo na porta 9000
# Usa o cgi-fcgi
if cgi-fcgi -bind -connect 127.0.0.1:9000; then
    exit 0 # Saudável
else
    exit 1 # Não saudável
fi