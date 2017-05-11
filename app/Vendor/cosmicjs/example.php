<?php

/* !!!! CONFIGURE !!!!
==================================== */
$config = new stdClass();
$config->bucket_slug = "driver-example"; // bucket slug
$config->read_key = ""; // leave empty if not required
$config->write_key = ""; // leave empty if not required

$config->url = "https://api.cosmicjs.com/v1/" . $config->bucket_slug;
$config->objects_url = $config->url . "/objects";
$config->media_url = $config->url . "/media";
$config->add_object_url = $config->url . "/add-object";
$config->edit_object_url = $config->url . "/edit-object";
$config->delete_object_url = $config->url . "/delete-object";

include("cosmicjs.php");

/* Get Objects
================================= */
$objects = $cosmicjs->getObjects();

var_dump($objects);

?>
