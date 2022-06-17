#!/bin/sh
set -ea

if [ "$1" = "strapi" ]; then

  if [ ! -f "package.json" ]; then

    DATABASE_CLIENT=${DATABASE_CLIENT:-mysql}

    EXTRA_ARGS=${EXTRA_ARGS}

    echo "Using strapi $(strapi version)"
    echo "No project found at /srv/app. Creating a new strapi project"
    mysql -u root -p [root-password] -e "update user set authentication_string=password(''), plugin='mysql_native_password' where user='root';"

    DOCKER=true strapi new . \
      --dbclient=$DATABASE_CLIENT \
      --dbhost=$DATABASE_HOST \
      --dbport=$DATABASE_PORT \
      --dbname=$DATABASE_NAME \
      --dbusername=$DATABASE_USERNAME \
      --dbpassword=$DATABASE_PASSWORD \
      --dbssl=$DATABASE_SSL \
      $EXTRA_ARGS

  elif [ ! -d "node_modules" ] || [ ! "$(ls -qAL node_modules 2>/dev/null)" ]; then

    echo "Node modules not installed. Installing..."

    if [ -f "yarn.lock" ]; then

      yarn install
#      npm config set ignore-scripts fals
#      rm -rf /node_modules
#      npm install --ignore-scripts=false --verbose

    else

      npm install

    fi

  fi

fi

echo "Starting your app..."

exec "$@"

mysqldump --no-tablespaces --add-drop-table -u mage -p`cat /etc/psa/.psa.shadow` mage  > mage.sql
