<?php

namespace App\Vendor\cosmicjs;

use App\Vendor\cosmicjs\CosmicCurl;

class CosmicJS {

    private $config;
    private $curl;
    //customized constructer to be accesible within laravel and added namespace for same reason
    function __construct($bucket_slug, $type_slug, $read_key = "", $write_key = "",$object_slug = "") {
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

    // Get all objects
    public function getObjects() {
        $data = json_decode($this->curl->get($this->config->objects_url));
        return $data;
    }
    
    public function getByObjectSlug($key,$slug)
    {   
        $this->config->object_by_meta_object = $this->config->url ."/object-type/" . $this->config->type_slug ."/search?metafield_key=" . $key ."&metafield_object_slug=" .$slug;
        $data = json_decode($this->curl->get($this->config->object_by_meta_object));
        return $data;
    }
    
    //Get all object types
    public function getObjectsType()
    {
        $data = json_decode($this->curl->get($this->config->object_type_url));
        return $data;
    }
    // Get all object
    public function getObject() {
        $data = json_decode($this->curl->get($this->config->object_url));
        return $data;
    }

    // Get media
    public function getMedia() {
        $data = json_decode($this->curl->get($this->config->media_url));
        return $data->media;
    }

    // Add object
    public function addObject($params) {
        $data = $this->curl->post($this->config->add_object_url, $params);
        return $data;
    }
    
    //Upload Media
    public function uploadMedia($path){
        
    }

    // Edit object
    public function editObject($params) {
        $data = $this->curl->put($this->config->edit_object_url, $params);
        return $data;
    }

    // Delete object
    public function deleteObject($params) {
        $data = $this->curl->delete($this->config->delete_object_url, $params);
        return $data;
    }
    
    

}
