#!/bin/bash

if [ ! -f /etc/nginx/ssl/default.crt ]; then
    openssl genrsa -out "/etc/nginx/ssl/default.key" 2048
    openssl req -new -key "/etc/nginx/ssl/default.key" -out "/etc/nginx/ssl/default.csr" -subj "/CN=default/O=default/C=UK"
    openssl x509 -req -days 365 -in "/etc/nginx/ssl/default.csr" -signkey "/etc/nginx/ssl/default.key" -out "/etc/nginx/ssl/default.crt"
    chmod 644 /etc/nginx/ssl/default.key
fi

# cron job to renew the certificate
#if [ ! -f /etc/cron.d/certificate-renewal ]; then
#    echo "30 2 * * * root /usr/local/bin/certificate-renewal.sh" > /etc/cron.d/certificate-renewal
#    chmod +x /usr/local/bin/certificate-renewal.sh
#fi

# cron to restart nginx server evers 6 hours
(crontab -l ; echo "0 0 */4 * * root nginx -s reload") | crontab -


# Start crond in background
crond -l 2 -b



# Start nginx in foreground
echo "Starting nginx... nginx will restart every 6 hours"
nginx
