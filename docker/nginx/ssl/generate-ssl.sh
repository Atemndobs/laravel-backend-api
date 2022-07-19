#openssl genrsa -out "/etc/nginx/ssl/default.key" 2048
openssl genrsa -out "./default.key" 2048
chmod 644 "./default.key"
#openssl req -new -key "/etc/nginx/ssl/default.key" -out "/etc/nginx/ssl/default.csr" -subj "/CN=default/O=default/C=UK"
openssl req -new -key "./default.key" -out "./default.csr" -subj "/CN=default/O=default/C=UK"
chmod 644 "./default.csr"
#openssl x509 -req -days 365 -in "/etc/nginx/ssl/default.csr" -signkey "/etc/nginx/ssl/default.key" -out "/etc/nginx/ssl/default.crt"
openssl x509 -req -days 365 -in "./default.csr" -signkey "./default.key" -out "./default.crt"
