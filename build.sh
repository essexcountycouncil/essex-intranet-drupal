#!/usr/bin/env bash

docker build -f Dockerfile-drupal -t drupal-fpm:latest -t ecccontainerregistry.azurecr.io/drupal-fpm:latest . && docker push ecccontainerregistry.azurecr.io/drupal-fpm:latest
docker build -f Dockerfile-nginx -t nginx-drupal:latest -t ecccontainerregistry.azurecr.io/nginx-drupal:latest . && docker push ecccontainerregistry.azurecr.io/nginx-drupal:latest
# az container create --resource-group ecc-drupal-dev --file containers.yaml