<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Vendor\cosmicjs\CosmicJS;
use GuzzleHttp\Client;

class IndexController extends Controller {

    private $locations_cosmic;
    private $items_cosmic;
    private $bucket_slug = '';
    private $read_key = '';
    private $write_key = '';

    public function __construct() {
        //initialize cosmicjs php instance for fetching all locations
        $this->bucket_slug = config('cosmic.slug');
        $this->read_key = config('cosmic.read');
        $this->write_key = config('cosmic.write');
        $this->locations_cosmic = new CosmicJS($this->bucket_slug, 'locations', $this->read_key, $this->write_key);
        $this->items_cosmic = new CosmicJS($this->bucket_slug, 'items', $this->read_key, $this->write_key);
    }

    public function index($location = null) {
        return $bucket_slug;
        //get objects with cosmic-js php
        $locations = $this->locations_cosmic->getObjectsType("locations", "disney-land");

        //set locations and bucket_slug variable to be passed to view
        $data['locations'] = $locations->objects;
        $data['bucket_slug'] = $this->bucket_slug;
        
        //if location slug was passed in url, pass it to view as well
        if ($location) {
            $data['location_slug'] = $location;
        } else {
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
        //flash message
        $request->session()->flash('status', 'The location"'.$title.'" was successfully deleted');
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
        //flash message
        $request->session()->flash('status', 'The Item "'.$name.'" was successfully created');
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
        //set picture if passed into request
        if ($request->input('image')) {
            $picture_data['key'] = "picture";
            $picture_data['type'] = 'file';
            $picture_data['value'] = $request->input('image');
            array_push($metafields, $picture_data);
        }
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
        //flash message
        $request->session()->flash('status', 'The Item was successfully edited!');
        //return result body
        return $result->getBody();
    }
    
    public function deleteItem(Request $request,$slug)
    {
        //create new client and delete item
        $client = new Client();
        $result = $client->delete('https://api.cosmicjs.com/v1/'. $this->bucket_slug .'/'.$slug, [
            'headers' => [
                'Content-type' => 'application/json',
            ]
        ]);
        
        //flash message
        $request->session()->flash('status', 'The Item was successfully deleted!');
        return $result;
    }

}
