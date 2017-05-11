[![Cosmic JS Logo](https://cosmicjs.com/images/marketing/logo-w-brand.jpg)](https://cosmicjs.com/)
===

### Getting started
Go to [https://cosmicjs.com](https://cosmicjs.com), create an account and setup a bucket.

### Install
```
git clone https://github.com/cosmicjs/cosmicjs-php
```
### Usage
```php
<?php
/* !!!! CONFIGURE !!!!
==================================== */
$config = new stdClass();
$config->bucket_slug = "driver-example"; // bucket slug
$config->read_key = ""; // leave empty if not required
$config->write_key = ""; // leave empty if not required
include("cosmicjs.php");

/* Add
================================= */
$object_string = '{
  "type_slug": "pages",
  "title": "Test Title",
  "content": "here is some test content",
  "write_key": "' . $config->write_key . '"
}';
$object = $cosmicjs->addObject($object_string);

/* Edit
================================= */
$object_string = '{
  "slug": "test-title",
  "title": "New Title",
  "content": "here is some NEW test content",
  "write_key": "' . $config->write_key . '"
}';
$object = $cosmicjs->editObject($object_string);

/* Delete
================================= */
$object_string = '{
  "slug": "test-title",
  "write_key": "' . $config->write_key . '"
}';
$object = $cosmicjs->deleteObject($object_string);

/* Get Objects
================================= */
$objects = $cosmicjs->getObjects();

/* Get Media
================================= */
$media = $cosmicjs->getMedia();
?>
```
