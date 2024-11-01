<?php

/*
|--------------------------------------------------------------------------
| Plugin activation
|--------------------------------------------------------------------------
|
| This file is included when the plugin is activated the first time.
| Usually you will use this file to register your custom post types or
| to perform some db delta process.
|
*/

use WPXShortcodesManagerLight\Models\Shortcode;

if(! Shortcode::exists('wpxsm-example')) {
    Shortcode::create(
        [
        'name' => 'wpxsm-example',
        'html' => '<strong style="red">$content</strong>'
    ]
    );
}
