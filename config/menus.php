<?php

/*
|--------------------------------------------------------------------------
| Plugin Menus routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the menu routes for a plugin.
| In this context the route are the menu link.
|
*/

return [
  'wpxsm_menu' => [
    "menu_title" => __('Shortcodes Manager', 'wpx-shortcodes-manager-light'),
    'capability' => 'manage_options',
    'icon'       => 'dashicons-editor-code',
    'items'      => [
      [
        "menu_title" => __('Manage', 'wpx-shortcodes-manager-light'),
        'route'      => [
          'load' => 'Dashboard\DashboardController@load',
          'get'  => 'Dashboard\DashboardController@index',
          'post' => 'Dashboard\DashboardController@post',
        ],
      ],
      [
        "menu_title" => __('Settings', 'wpx-shortcodes-manager-light'),
        'route'      => [
          'get'  => 'Settings\SettingsController@index',
          'post' => 'Settings\SettingsController@saveSettings',
        ],
      ],
    ]
  ]
];
