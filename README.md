# Swapp_backend

In this repository you will find the server for the project Swapp. It contain the entities for the dummy database you can find in the [Swapp repository](https://github.com/PabloLeth/swapp.git).

# Getting started

To get started you should clone this respository on your local directory
* `$git clone https://github.com/PabloLeth/swapp_backend.git`

Then in your project directory get composer installed (remember using sudo if you are a linux user)
* `apt install composer`


Next get the ORM pack for symfony:
* `composer require symfony/orm-pack`

Because this server uses jwt as a token generator we need to generte the keys:

* `$ php bin/console lexik:jwt:generate-keypair`

And to run the server we will use port 8000:
* `$ php -S localhost:8000 -t public/`
