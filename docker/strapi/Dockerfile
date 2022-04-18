FROM strapi/base:latest

RUN yarn global add strapi


RUN mkdir /srv/app && chown 1000:1000 -R /srv/app

WORKDIR /srv/app

VOLUME /srv/app
# RUN delete ./node_modules // rm -rf ./node-modules
# npm install --ignore-scripts=false --verbose sharp
RUN npm config set ignore-scripts false
RUN rm -rf /node_modules
RUN ls /srv/app/
RUN npm install --ignore-scripts=false --verbose

COPY docker-entrypoint.sh /usr/local/bin/
ENTRYPOINT ["docker-entrypoint.sh"]

CMD ["strapi", "develop"]
