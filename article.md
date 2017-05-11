In this tutorial, we are going to be creating a simple inventory management application with laravel and vuejs as our frontend. This tutorial assumes you have a basic knowledge of object oriented php and javascript, and though we will be going through the basics of laravel and vuejs, it is recommended to have a basic understanding of their concepts. Now that we have that cleared, fire up your php server and let’s build something.

## Getting Started:
Since this is a laravel application you will need to create a new laravel project, ensure your server meets laravel’s requirements as stated here [Laravel Servrer Requirements](https://laravel.com/docs/5.4/installation#server-requirements), and also make sure you have [Composer](https://getcomposer.org/download/) installed on your server. Once you have composer installed open up your command line and cd into your server root then simply run
```
composer create-project --prefer-dist laravel/laravel inventory
```
This will setup a new laravel project in the directory `inventory` once it has been succesfully run.

After setting up the new project you will then run `npm install` or `npm install --no-bin-links` if you are devveloping on windows. This will download and setup all our javascript dependencies. To be able to view our empty laravel project simply run `php artisan serve` from our project's directory, this will fire up a php server from our project's root which you can then type `http://127.0.0.1:8000` in your browser url and you should be greeted with the default laravel screen.


## Setting up the cosmicjs php library
We will be using some functions from the [cosmicjs php library](https://github.com/cosmicjs/cosmicjs-php), download the repo into a separate folder, we will have to edit it a bit to work neatly with laravel. In the inventory/app/ folder, create a /Vendor/cosmicjs folder and copy all the contents of the cosmicjs-php library into it, such that for example the path for cosmicjs.php becoms app/Vendor/cosmicjs/cosmicjs.php. Then rename `app/Vendor/cosmicjs/curl` class to `app/Vendor/cosmicjs/cosmiccurl` and change this top part of the code:
```
class Curl {
  ...
  }
```
to 
```
namespace App\Vendor\cosmicjs;

class CosmicCurl {
  ....
}
```
What we did was add a namespace to the cosmiccurl file so we can import into laravel and change the class name to match the file name.
After doing that replace this section of cosmicjs.php
```
include("curl.php");
$curl = new Curl;
class CosmicJS {
  function __construct(){
    global $curl;
    global $config;
    $this->curl = $curl;
    $this->config = $config;
    $this->config->bucket_slug = $config->bucket_slug;
    $this->config->object_slug = $config->object_slug;
    $this->config->read_key = $config->read_key;
    $this->config->write_key = $config->write_key;
    $this->config->url = "https://api.cosmicjs.com/v1/" . $this->config->bucket_slug;
    $this->config->objects_url = $this->config->url . "/objects?read_key=" . $this->config->read_key;
    $this->config->object_url = $this->config->url . "/object/" . $this->config->object_slug . "?read_key=" . $this->config->read_key;
    $this->config->media_url = $this->config->url . "/media?read_key=" . $this->config->read_key;
    $this->config->add_object_url = $this->config->url . "/add-object?write_key=" . $this->config->write_key;
    $this->config->edit_object_url = $this->config->url . "/edit-object?write_key=" . $this->config->write_key;
    $this->config->delete_object_url = $this->config->url . "/delete-object?write_key=" . $this->config->write_key;
  }
```
with 
```
namespace App\Vendor\cosmicjs;

use App\Vendor\cosmicjs\CosmicCurl;

class CosmicJS {

    private $config;
    private $curl;
    function __construct($bucket_slug, $type_slug,$object_slug = "", $read_key = "", $write_key = "") {
        $this->curl = new CosmicCurl();
        $this->config = new \stdClass();
        //$this->config = $config;
        $this->config->bucket_slug = $bucket_slug;
        $this->config->object_slug = $object_slug;
        $this->config->type_slug = $type_slug;
        $this->config->read_key = $read_key;
        $this->config->write_key = $write_key;
        $this->config->url = "https://api.cosmicjs.com/v1/" . $this->config->bucket_slug;
        $this->config->objects_url = $this->config->url . "/objects?read_key=" . $this->config->read_key;
        $this->config->object_type_url = $this->config->url . "/object-type/" . $this->config->type_slug . "?read_key=" . $this->config->read_key;
        $this->config->object_url = $this->config->url . "/object/" . $this->config->object_slug . "?read_key=" . $this->config->read_key;
        $this->config->media_url = $this->config->url . "/media?read_key=" . $this->config->read_key;
        $this->config->add_object_url = $this->config->url . "/add-object?write_key=" . $this->config->write_key;
        $this->config->edit_object_url = $this->config->url . "/edit-object?write_key=" . $this->config->write_key;
        $this->config->delete_object_url = $this->config->url . "/delete-object?write_key=" . $this->config->write_key;
    }

```
what this bit of change does is that it imports the cosmiccurl using its namespace, and it enables us to create multiple instances of a cosmicjs object quickly within laravel, simply by initializing with constructor parameters instead of having to setup some config variables which can get messy when being used with larger applications.Finally add the following function to the cosmicjs.php file.
```
public function getByObjectSlug($key,$slug)
    {   
        $this->config->object_by_meta_object = $this->config->url ."/object-type/" . $this->config->type_slug ."/search?metafield_key=" . $key ."&metafield_object_slug=" .$slug;
        $data = json_decode($this->curl->get($this->config->object_by_meta_object));
        return $data;
    }
```

## Building our app
Now that we have our cosmicjs library setup in the `app/Vendor` folder, its time to actually build something. 
Since all requests will be handled by the `app/Http/Controller/IndexController.php` file open it up and copy and paste this code into it.
```
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Vendor\cosmicjs\CosmicJS;
use GuzzleHttp\Client;

class IndexController extends Controller {

    private $locations_cosmic;
	private $items_cosmic;
    private $bucket_slug = 'inventory';

    public function __construct() {
      //initialize cosmicjs php instance for fetching all locations
        $this->locations_cosmic = new CosmicJS($this->bucket_slug, 'locations');
		$this->items_cosmic = new CosmicJS($this->bucket_slug, 'items');
    }

    public function index($location = null) {
        //get objects with cosmic-js php
        $locations = $this->locations_cosmic->getObjectsType("locations", "disney-land");
       
       //set locations and bucket_slug variable to be passed to view
        $data['locations'] = $locations->objects;
        $data['bucket_slug'] = $this->bucket_slug;
        
        //if location slug was passed in url, pass it to view as well
        if($location)
        {
            $data['location_slug'] = $location;
        }
        else{
            $data['location_slug'] = '';
        }

        //load view
        return view('index', $data);
    }

    //fetch items for location based on slug
    public function itemsByLocation($slug) {
        //fetch items using the cosmicjs library's custom function
        $items = $this->items_cosmic->getByObjectSlug('location', $slug);
        
        //if the returned value has "object" property, pass it 
        if (property_exists($items, 'objects')) {
            //returning arrays in laravel automatically converts it to json string
            return $items->objects;
        } else {
            return 0;
        }
    }

    public function newLocation(Request $request) {
        //get passed input
        $title = $request->input('title');
        $address = $request->input('address');
        $picture = $request->input('image');

        //set data array
        $data['title'] = $title;
        $data['type_slug'] = "locations";
        $data['bucket_slug'] = $this->bucket_slug;
        $metafields = array();
        $address_data['key'] = "address";
        $address_data['type'] = 'textarea';
        $address_data['value'] = $address;
        if ($picture != '') {
            $picture_data['key'] = "picture";
            $picture_data['type'] = 'file';
            $picture_data['value'] = $picture;
            array_push($metafields, $picture_data);
        }
        array_push($metafields, $address_data);
        $data['metafields'] = $metafields;

        //create a new guzzle client
        $client = new Client();
        //create guzzle request with data array passed as json value
        $result = $client->post('https://api.cosmicjs.com/v1/' . $this->bucket_slug . '/add-object', [
            'json' => $data,
            'headers' => [
                'Content-type' => 'application/json',
            ]
        ]);
        //return result body
	return $result->getBody();
    }
    
    //create a new item
    public function newItem(Request $request) {
        //get data
        $name = $request->input('name');
        $count = $request->input('count');
        $location_id = $request->input('location');
        $picture = $request->input('image');

        //create data array to be passed
        $data['title'] = $name;
        $data['type_slug'] = "items";
        $data['bucket_slug'] = $this->bucket_slug;
        $count_metafield['key'] = "count";
        $count_metafield['value'] = $count;
        $count_metafield['type'] = "text";
        $location_meta['key'] = "location";
        $location_meta['object_type'] = "locations";
        $location_meta['type'] = "object";
        $location_meta['value'] = $location_id;
        $metafields = array();
        
        //set picture if passed into request
        if ($picture != '') {
            $picture_data['key'] = "picture";
            $picture_data['type'] = 'file';
            $picture_data['value'] = $picture;
            array_push($metafields, $picture_data);
        }
        array_push($metafields, $count_metafield);
        array_push($metafields, $location_meta);
        $data['metafields'] = $metafields;

        $client = new Client();
        $result = $client->post('https://api.cosmicjs.com/v1/' . $this->bucket_slug . '/add-object', [
            'json' => $data,
            'headers' => [
                'Content-type' => 'application/json',
            ]
        ]);
        //return result body
	return $result->getBody();
    }

    public function editItem(Request $request) {
        $name = $request->input('name');
        $count = $request->input('count');
        $slug = $request->input('slug');
        $location_id = $request->input('location_id');

        $data['title'] = $name;
        $data['slug'] = $slug;
        $count_meta['key'] = "count";
        $count_meta['value'] = $count;
        $count_meta['type'] = "text";
        $location_meta['key'] = "location";
        $location_meta['object_type'] = "locations";
        $location_meta['type'] = "object";
        $location_meta['value'] = $location_id;
        $metafields = array();
        array_push($metafields, $count_meta);
        array_push($metafields, $location_meta);
        $data['metafields'] = $metafields;

        $client = new Client();
        $result = $client->put('https://api.cosmicjs.com/v1/' . $this->bucket_slug . '/edit-object', [
            'json' => $data,
            'headers' => [
                'Content-type' => 'application/json',
            ]
        ]);
        //return result body
	return $result->getBody();
    }

}
```
The code above is preety self explanatory with the comments included
### Things to note:
1. We create a new cosmicjs instance for retreiving locations
2. We are setting up all functions our vuejs frontend will interact with
3. `$client = new CLient()` creates a new [guzzle](http://docs.guzzlephp.org/en/latest/) instance which we use to make calls to the cosmic api

Next we will create our routes in the routes/web.php file. Open up the file and copy and paste this code into it.
```
<?php
Route::get('/{location?}', 'IndexController@index');
Route::get('items/{slug}', 'IndexController@itemsByLocation');
Route::post('locations/new','IndexController@newLocation');
Route::post('items/new','IndexController@newItem');
Route::post('items/edit','IndexController@editItem');
```
### what are we doing
We are registering all our IndexCOntroller's function to routes so they can be accessible by the frontend.

## Building the frontend
Remember this code in our IndexController `return view('index', $data);`? well its time to create the view that will be loaded. Open up the /resources/views folder and open up the master.blade.php then copy and paste this into it.
```
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- Set Csrf token on all pages -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- Load Bootstrap-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <title>Inventory Manger</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="{{ asset('css/font-awesome/css/font-awesome.min.css')}}"/>
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
        <script src="https://use.fontawesome.com/682442a8be.js"></script>

        <!-- Set Csrf token to be used by javascript and axios-->
        <script>
	window.Laravel = <?php
	echo json_encode([
    	'csrfToken' => csrf_token(),
	]);
	?>
        </script>
        <!-- Styles -->
        <style>
            .location-tab{
                height:104px;
                padding-left: 150px;
            }
            
            .location-tab > img{
                position: absolute;
                left: 0;
                top: 0;
                height: 100%;
                width: auto;
                max-width: 130px;
            }
            
            .text-primary{
                color: #29ABE2 !important;
            }
            
            .panel-heading{
                background-color: #29ABE2 !important;
                color: white !important;
            }
            
            .panel{
                border-color: #29ABE2 !important;
            }
            
            .btn-primary{
                background-color: #29ABE2 !important;
                color: white !important;
                border-color: #29ABE2 !important;
                border-radius: 3px;
                margin: 10px 0;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div id="wrapper">
                @yield('content')
            </div>
        </div>
        <!-- Load Jquery, bootstrap js, and app.js which contains all our compiled frontend javascript-->
        <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
        <script src="{{ asset('/js/app.js')}}"></script>
        @yield('scripts')
    </body>
</html>
```
THe master.blade.php will serve as an extendable layout which we can then use as parent layouts for all our other views. Now create an index.blade.php file in the same folder and paste this into it.
```
@extends('master')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div style="float:left">
            <h1>Inventory Management</h1>
        </div>
        <div style="float:right;padding-top: 20px">
            <a class="btn btn-default"><i class="fa fa-github"></i> View on Github</a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div style="float: right; margin-bottom: 15px;"><a href="https://cosmicjs.com" target="_blank" style="text-decoration: none;"><img class="pull-left" src="https://cosmicjs.com/images/logo.svg" width="28" height="28" style="margin-right: 10px;"><span style="color: rgb(102, 102, 102); position: relative; top: 3px;">Proudly powered by Cosmic JS</span></a></div>
    </div>
</div>

<div class="row" style="font-size: 16px">
    <!-- Display vue component and set props from given data  -->
    <inventory :initial-locations="{{ json_encode($locations) }}" slug="{{ $bucket_slug }}" location-slug="{{ $location_slug }}"></inventory>
</div>
@endsection

@section('scripts')
<script>
</script>
@endsection
```
### Things to note
1. We created a master layout which has available sections for content, script and style that our other view can extend.
2. We added the vue component (which will be created in the next section), with props as the data given to the view by the controller

## Creating Our Controller
This section assumes you have fundamental knowledge of [Vuejs](http://vuejs.org), if not i recommend you brush up on it, as explaining how some vue functions works is out of the scope of this tutorial. Now to begin, open a command prompt and cd to the app's folder then run `npm run watch` to fire up [laravel mix](https://laravel.com/docs/5.4/mix), this will compile our assets whenever a change has been made to any of our files, alternatively you could type `npm run dev` whenever you need to compile the assets yourself.
Open the /resources/assets/js/app.js file and change this
```
Vue.component('example', require('./components/Example.vue'));
```
to
```
Vue.component('inventory', require('./components/Inventory.vue'));
```
Here we are replacing the default example component with a component called `inventory` which we will be creating.In the /resources/assets/js/components folder create and Inventory.vue file to house our [component](https://vuejs.org/v2/guide/components.html). In the newly created file copy and paste this code into it
```
<template>
    <div>
        <!---- ADD LOCATION FORM -->
        <div v-if="add_location">
            <button class="btn btn-primary" v-on:click="add_location=false"><span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Go back</button>
            <div class="panel panel-default">
                <div class="panel-heading">Add New Location</div>
                <div class="panel-body">
                    <form id="location_form" name="location">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" name="title" required="">
                            <label for="address">Address</label>
                            <input type="text" class="form-control" name="address" required="">
                            <label for="image">Image</label>
                            <input type="file" class="form-control media" name="media"/>
                        </div>
                        <button type="submit" class="btn btn-primary" :class="{disabled: isDisabled}" v-on:click.prevent="addLocation">Submit</button>
                    </form>
                </div>
            </div>
        </div>
        <div v-else>
            <!---- LOCATIONS LIST -->
            <div v-if="unselected">
                <button class="btn btn-primary pull-right" v-on:click="add_location = true"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>Add New</button>
                <ul class="list-group">
                    <button type="button" class="list-group-item location-tab text-primary" :class="{disabled: list_disable}" v-for="location in locations" v-on:click="fetchItems(location)"><img v-if="location.metadata.hasOwnProperty('picture')" :src="location.metadata.picture.url">{{ location.title }} - {{ location.metadata.address}}</button>
                </ul>
            </div>

            <div v-else>
                <!---- ADD ITEM FORM -->
                <div v-if="add_item">
                    <button class="btn btn-primary" v-on:click="add_item=false"><span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Go back</button>
                    <div class="panel panel-default">
                        <div class="panel-heading">Add New Item</div>
                        <div class="panel-body">
                            <form id="item_form">
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control" name="name">
                                </div>
                                <div class="form-group">
                                    <label for="count">Count</label>
                                    <input type="number" class="form-control" name="count">
                                </div>
                                <div>
                                    <label for="image">Image</label>
                                    <input type="file" class="form-control media" name="media"/>
                                </div>
                                <button type="submit" class="btn btn-primary" v-on:click.prevent="addItem">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!---- EDIT ITEM FORM -->
                <div v-else-if="edit_item">
                    <button class="btn btn-primary" v-on:click="edit_item=false"><span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Go back</button>
                    <div class="panel panel-default">
                        <div class="panel-heading">Edit {{ selected_item.title }}</div>
                        <div class="panel-body">
                            <form id="edit_item">
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control" name="name" :value="selected_item.title">
                                </div>
                                <div class="form-group">
                                    <label for="count">Count</label>
                                    <input type="number" class="form-control" name="count" :value="selected_item.metadata.count">
                                </div>
                                <button type="submit" class="btn btn-primary" v-on:click.prevent="editItem">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div v-else>
                    <!---- ITEMS LIST -->
                    <button class="btn btn-primary" v-on:click="unselected=true"><span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Go back</button>
                    <button class="btn btn-primary pull-right" v-on:click="add_item = true"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>Add New Item</button>
                    <div class="panel panel-default">
                        <div class="panel-heading">{{ selected_location.title }}</div>
                        <div class="panel-body">
                            <ul class="list-group">
                                <button type="button" class="list-group-item text-primary location-tab" :class="{disabled: isDisabled}" v-for="item in items"><img v-if="item.metadata.hasOwnProperty('picture')" :src="item.metadata.picture.url">{{ item.title }} - {{ item.metadata.count }} <span class="glyphicon glyphicon-pencil pull-right" aria-hidden="true" v-on:click.prevent="openEdit(item)"></span></button>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
    export default {
        mounted() {
            var self = this;
            //If location slug was passed show items for that location
            if (this.locationSlug)
            {
                this.unselected = false;
                //find location with slug
                var item = this.locations.filter(function(obj)
                {
                    console.log(obj.slug);
                    return obj.slug === self.locationSlug;
                });
                
                this.selected_location =  item[0];
                this.fetchItems(this.selected_location);
            }
        },
        props: ['initial-locations', 'slug', 'location-slug'],
        data: function () {
            return {
                edit_item: false,
                locations: this.initialLocations,
                isDisabled: false,
                list_disable: false,
                unselected: true,
                items: [],
                add_location: false,
                selected_location: [],
                selected_item: [],
                add_item: false
            };
        },
        methods: {
            fetchItems(location)
            {
                //disable the list and fetch items from laravel
                var self = this;
                this.list_disable = true;
                axios.get('items/' + location.slug).then(response => {
                    if (response.data.constructor === Array)
                    {
                        self.items = (response.data);
                        self.selected_location = location;
                        self.unselected = false;
                    } else {
                        self.selected_location = location;
                        self.items = [];
                        self.unselected = false;
                    }
                    self.list_disable = false;

                });
            },
            addLocation()
            {
                //disable button
                this.isDisabled = true;
                var image = '';
                var form = $("#location_form")[0];
                var data = new FormData(form);
                //Check if image is selected then upload image first
                if ($("#location_form .media").val() !== '')
                {
                    //delete X-csrf-token default header as it is not accepted by cosmic api
                    delete axios.defaults.headers.common["X-CSRF-TOKEN"];
                    axios.post('https://api.cosmicjs.com/v1/' + this.slug + '/media', data).then(function (response)
                    {
                        //set x-csrf-token again
                        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = window.Laravel.csrfToken;
                        //get image name, append to formdata and send form data to laravel to add location
                        image = response.data.media.name;
                        data.set('image', image);
                        axios.post('locations/new', data).then(response => {
                            location.reload(true);
                        });
                    });
                } else {
                    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = window.Laravel.csrfToken;
                    //send form data to laravel without image
                    axios.post('locations/new', data).then(response => {
                        location.reload(true);
                    });
                }

            },
            //set selected item and open edit item section
            openEdit(item)
            {
                this.selected_item = item;
                this.edit_item = true;
            },
            addItem() {
                var self = this;
                this.isDisabled = true;
                var form = $('#item_form')[0];
                var data = new FormData(form);
                data.append('location', this.selected_location._id);
                //Check if image is selected the upload image first
                if ($("#item_form .media").val() !== '')
                {
                    //delete X-csrf-token default header as it is not allowed by cosmic api and post
                    delete axios.defaults.headers.common["X-CSRF-TOKEN"];
                    axios.post('https://api.cosmicjs.com/v1/' + this.slug + '/media', data).then(function (response)
                    {
                        //set x-csrf-token again
                        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = window.Laravel.csrfToken;
                        //get image name, append to formdata and send form data to laravel to add location
                        var image = response.data.media.name;
                        data.set('image', image);
                        axios.post('items/new', data).then(response => {
                            //refresh page BUT pass location_slug, which then makes the app load into the passed location
                            window.location.href = "./" + self.selected_location.slug;
                        });
                    });
                } else {
                    //add header back after post
                    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = window.Laravel.csrfToken;
                    //send form data to laravel without image
                    axios.post('items/new', data).then(response => {
                        window.location.href = "./" + self.selected_location.slug;
                    });
                }
            },
            editItem()
            {
                //edit item, by sending data to IndexController's editItem() function
                var self = this;
                var form = $("#edit_item")[0];
                var data = new FormData(form);
                this.isDisabled = true;
                data.append('slug', this.selected_item.slug);
                data.append('location_id', this.selected_location._id);
                axios.post('items/edit', data).then(response => {
                    //refresh page BUT pass location_slug, which then makes the app load into the passed location
                    window.location.href = "./" + self.selected_location.slug;
                });
            }

        }
    }
</script>
```
## The big question, what is going on here (A Lot)?
A lot is going on in our vue components as that is where majority of the frontend is, to keep the tutorial simple, the main goal of sections and variables will be stated out, also the comments in the code explain what that section of the code is meant to accomplish
1. We begin by setting up a few boolean (edit_item, add_item, add_location, unselected), these help in switching thw views of our app wheenever a variable has been changed, e.g when edit_item is true, the view is switched to the edit item form
2. isDisable and list_disable are used to disable some ui elements sucha s buttons by binding their values to "disabled" class of bootstrap buttons
3. Code in the mounted function, simply checks if a location slug was passed and if so switch the view to the location's items
4. The methods created perform calls when actions have been taken, their main logic is stated in comments
5. We used axios to make all our calls to laravel backend
6. For image uploads, we first check if a file is selected, the upload the selected file first, before creating a new item with its picture metadata set as the returned picture name.

## Note
1. Make sure the assets were successfully compiled after the changes were made, and if you want to manually compile it simply run `npm run production` to compile into productin mode.
2. We create a self variable and set it to `this` at the start of most functions because, if we use this in some nested function calls, we begin to loose context for the `this` variable. More info can be found on this stack overflow [post](http://stackoverflow.com/documentation/vue.js/9350/using-this-in-vue#t=201705092314332093921)

# Conclusion
We were able to create and update items using vue, the cosmic Js php library, a bit of guzzle, and a lot of axios. You can feel free to tinker with the code and make your own modifications, a way to delete items, move to new locations, you name it, remember we only scratched the surface of the amazing use of cosmicjs with laravel. So go on and create something amazing





