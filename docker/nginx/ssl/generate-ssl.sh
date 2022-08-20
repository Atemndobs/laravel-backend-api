#openssl genrsa -out "/etc/nginx/ssl/curator.key" 2048
#openssl genrsa -out "./curator.key" 2048
#chmod 644 "./curator.key"
#openssl req -new -key "/etc/nginx/ssl/curator.key" -out "/etc/nginx/ssl/curator.csr" -subj "/CN=curator/O=curator/C=UK"
#openssl req -new -key "./curator.key" -out "./curator.csr" -subj "/CN=curator/O=curator/C=UK"
#chmod 644 "./curator.csr"
#openssl x509 -req -days 365 -in "/etc/nginx/ssl/curator.csr" -signkey "/etc/nginx/ssl/curator.key" -out "/etc/nginx/ssl/curator.crt"
#openssl x509 -req -days 365 -in "./curator.csr" -signkey "./curator.key" -out "./curator.crt"

openssl genrsa -out "./curator.key" 2048
chmod 644 "./curator.key"
openssl req -x509 -new -nodes -key "./curator.key" -sha256 -days 3650 -out "./curator.pem"
chmod 644 "./curator.pem"
openssl req -new -sha256 -nodes -out "./curator.csr" -newkey rsa:2048 -keyout "./curator.key" -config <( cat "./curator.csr.cnf" )
#chmod 644 "./curator.csr"
openssl x509 -req -in "./curator.csr" -CA "./curator.pem" -CAkey "./curator.key" -CAcreateserial -out "./curator.crt" -days 3650 -sha256 -extfile "./v3.ext"
chmod 644 "./curator.crt"
