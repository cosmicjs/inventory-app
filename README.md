# inventory-cosmicjs
A simple inventory management application for the [cosmicjs api](https://cosmicjs.com) built with [Laravel](http://laravel.com) as it's backend and [Vue Js](http://vuejs.org) as its frontend.

To install you can git clone the repo or download the repo as a zip archive.

Once cloned, you will need to install laravel's php dependencies with composer.

## Installing laravel and the app's dependecies
To install the php dependencies you will need to have composer installed.
If you have composer installed globally simply run `composer install`.

if you dont have composer installed globally you can install it for the project use by running the following commands in the project root directory:
```
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('SHA384', 'composer-setup.php') === '669656bab3166a7aff8a7506b8cb2d1c292f042046c5a994c43155c0be6190fa0355160742ab2e1c88d40d5be660b410') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"
```
This will create a "composer.phar" file in the project root directory, you can then run `php composer.phar install` to install all required laravel dependencies.

## Install NPM Dependencies (Optional)
If you intend on making changes to the code then  you can follow these steps, as the app should run fine without it.
Make sure you have node and npm installed to the latest version. 
If you dont have them installed go [here](https://docs.npmjs.com/getting-started/installing-node) to learn how you can install them.

If you already have npm or once you have it installed, cd to the project root directory and run:
```
npm install
```
This will install all of laravel mix requirement as the project uses laravel-mix and Vue.js 

### Optional
To start up a server for the app run `php artisan serve` and navigate to 127.0.0.1:8000 in your browser

### Note
The bucket slug is currently hardcoded into the app's `IndexController.php` file in the construct method and will need to be changed to be used with a custom bucket
