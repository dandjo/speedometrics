# Speedometrics

Symfony 4 application for importing and aggregating data measured by stationary speedometers. 

## Installation

    $ cd ./app
    $ composer install
    $ mkdir ./var/store
    $ php bin/console doctrine:database:create
    $ php bin/console doctrine:migrations:migrate

## Installation with Docker Compose

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

    $ php bin/console server:run
    
The frontend is available at http://127.0.0.1:8000.

## Frontend with Docker Compose

    $ docker-compose up

The frontend is available at http://speedometricts.localhost or with xdebug
extension enabled at http://debug.speedometrics.localhost.
