# Speedometrics

Symfony 4 application for importing and aggregating data measured by stationary speedometers. 

## Installation

    $ cd ./app
    $ composer install
    $ mkdir ./var/store
    $ php bin/console doctrine:database:create
    $ php bin/console doctrine:migrations:migrate

## Installation with Docker Compose

    $ docker-compose up --build
    $ docker-compose run php composer install -d /app
    $ docker-compose run php mkdir /app/var/store
    $ docker-compose run php /app/bin/console -n doctrine:database:create
    $ docker-compose run php /app/bin/console -n doctrine:migrations:migrate

## Import Data

    $ php bin/console import:excel /path/to/excels \
      "street name" "house number" "zip code" "city name"

## Import Data with Docker Compose

To access your host filesystem we created a mountpoint of the host filesystem
root at `/docker-host/`. The path to your home directory therefore usually is
`/docker-host/home/your-username/`.

    $ docker-compose run php /app/bin/console import:excel \
      /docker-host/path/to/excels \
      "street name" "house number" "zip code" "city name"

## Frontend

- http://speedometricts.localhost/charts/average-speed-hour?from=2016-04-01&to=2016-05-01
- http://debug.speedometrics.localhost/charts/speed-categories?from=2016-04-01&to=2016-05-01
