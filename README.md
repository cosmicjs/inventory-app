# Inventory App
![Inventory App](https://cosmicjs.com/uploads/3ecb9f80-396d-11e7-8b8c-299270efeba9-inventory-app.png)
A simple inventory management application powered by the [Cosmic JS API](https://cosmicjs.com) built with [Laravel](http://laravel.com) as it's backend and [Vue.js](http://vuejs.org) as its frontend.

## Getting Started
To install the php dependencies you will need to have composer installed.  If you do not have composer installed, you can follow steps to do so here: https://getcomposer.org/download/

## Setting bucket information
To set your Bucket slug simply run
```
php artisan bucket bucket-slug read-key write-key
```
Both read and write keys are optional

### Starting the Server
To start up a server for the app run `php artisan serve` and navigate to 127.0.0.1:8000 in your browser

## Install NPM Dependencies (Optional)
If you intend on making changes to the code then  you can follow these steps, as the app should run fine without it.
Make sure you have node and npm installed to the latest version. 
If you dont have them installed go [here](https://docs.npmjs.com/getting-started/installing-node) to learn how you can install them.

If you already have npm or once you have it installed, cd to the project root directory and run:
```
npm install
```
This will install all of laravel mix requirement as the project uses laravel-mix and Vue.js 
