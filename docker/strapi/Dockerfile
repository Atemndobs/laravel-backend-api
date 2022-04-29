FROM  pqm1989/strapi:v4.1.2-v1.0

RUN npm cache clean -f
RUN npm install -g n
RUN n stable
#RUN yarn global add strapi
#
#
## RUN mkdir /srv/app && chown 1000:1000 -R /srv/app
#
#WORKDIR /srv/app
#
#VOLUME /srv/app
## RUN delete ./node_modules // rm -rf ./node-modules
## npm install --ignore-scripts=false --verbose sharp
## RUN npm config set ignore-scripts false
#
## RUN npm install --ignore-scripts=false --verbose
#
#COPY docker-entrypoint.sh /usr/local/bin/
#ENTRYPOINT ["docker-entrypoint.sh"]
#
#CMD ["strapi", "develop"]
