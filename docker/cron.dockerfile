#FROM php:7.4-fpm-alpine
#
#RUN docker-php-ext-install pdo pdo_mysql
#RUN apt-get update && apt-get install -y cron
#
#COPY crontab /etc/crontabs/root
#
#CMD ["crond", "-f"]


FROM php:7.4-cli-alpine

COPY crontab /crontab

RUN docker-php-ext-install pdo pdo_mysql
RUN mkdir /var/log/cron
RUN touch /var/log/cron/cron.log

RUN chown -R www-data:www-data /var/log/cron

RUN /usr/bin/crontab -u www-data /crontab

CMD ["crond", "-f", "-l", "8"]
