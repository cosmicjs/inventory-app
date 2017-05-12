<?php
	return [
		'slug' => getenv('COSMIC_BUCKET'),
		'read' => getenv('COSMIC_READ_KEY'),
		'write' => getenv('COSMIC_WRITE_KEY'),
	];