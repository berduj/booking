git pull
bin/console doct:migr:migr --no-interaction  --allow-no-migration
bin/console cache:clear --env=prod
bin/console asset-map:compile

