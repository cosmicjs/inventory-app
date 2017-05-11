<?php

/*************CONFIGURE********************/
$config = new stdClass();
$config->bucket_slug = "inventory"; // bucket slug
$config->read_key = ""; // leave empty if not required
$config->write_key = ""; // leave empty if not required
$config->object_slug="";

include("cosmicjs.php");
