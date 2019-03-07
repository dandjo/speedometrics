# Speedometrics

Symfony 4 application for importing and aggregating data measured by stationary speedometers. 

## Installation

    $ docker-compose run php composer install -d /app
    $ docker-compose run php mkdir /app/var/store
    $ docker-compose run php /app/bin/console -n doctrine:database:create
    $ docker-compose run php /app/bin/console -n doctrine:migrations:migrate

## Import Data

To access your host filesystem we created a mountpoint of the host filesystem
root at `/docker-host/`. The path to your home directory therefore usually is
`/docker-host/home/your-username/`.

    $ docker-compose run php /app/bin/console import:excel \
      /docker-host/path/to/excels \
      "street name" "house number" "zip code" "city name"

## Import random data

Random data gives you an idea of this product. Let's import 4 iterations.

    $ docker-compose run php /app/bin/console import:random 4

## Frontend

    $ docker-compose up

The frontend is available at http://speedometricts.localhost or with xdebug
extension enabled at http://debug.speedometrics.localhost.
