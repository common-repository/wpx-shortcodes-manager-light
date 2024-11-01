<?php

namespace WPXShortcodesManagerLight\Http\Controllers\Dashboard;

use WPXShortcodesManagerLight\Http\Controllers\Controller;
use WPXShortcodesManagerLight\Models\Shortcode;
use WPXShortcodesManagerLight\Http\Controllers\Dashboard\ShortcodesListTable;
use WPXShortcodesManagerLight\PureCSSTabs\PureCSSTabsProvider;
use WPXShortcodesManagerLight\PureCSSSwitch\PureCSSSwitchProvider;

class DashboardController extends Controller
{
    public function load()
    {
        $args = [
          'label'   => 'Items number',
          'default' => 5,
          'option'  => 'wpxsm_shortcodes_per_page'
        ];

        add_screen_option('per_page', $args);

        global $wp_shortcode_manager_list_table;

        $wp_shortcode_manager_list_table = new ShortcodesListTable();
    }

    public function index()
    {
        $view = $this->request->get('view', false);

        if ($view) {
            if (method_exists($this, $view)) {
                return $this->$view();
            }
        }

        PureCSSSwitchProvider::enqueueStyles();

        $table = new ShortcodesListTable();

        return WPXShortcodesManagerLight()
          ->view('dashboard.index')
          ->withAdminStyles('wpxsm-admin')
          ->withAdminScripts('wpxsm-admin')
          ->with('table', $table);
    }

    public function post()
    {
        $action = $this->request->get('action', false);

        if ($action) {
            if (method_exists($this, $action)) {
                return $this->$action();
            }
        }

        $destroy = $this->request->get('destroy', false);

        if ($destroy) {
            return $this->destroy($destroy);
        }
    }

    public function edit()
    {

        PureCSSTabsProvider::enqueueStyles();

        $shortcode = Shortcode::find($this->request->get('shortcode_id'));

        if ($shortcode) {
            return WPXShortcodesManagerLight()
              ->view('dashboard.edit')
              ->withAdminStyles('wpxsm-admin')
              ->withAdminScripts('wpxsm-admin')
              ->with('shortcode', $shortcode);
        }
    }

    public function create()
    {
        PureCSSTabsProvider::enqueueStyles();

        return WPXShortcodesManagerLight()
          ->view('dashboard.create')
          ->withAdminStyles('wpxsm-admin')
          ->withAdminScripts('wpxsm-admin');
    }

    public function update()
    {
        $result = Shortcode::update(
            [
            Shortcode::COLUMN_NAME => $this->request->get('shortcode_name'),
            Shortcode::COLUMN_HTML => $this->request->get('html'),
            Shortcode::COLUMN_ID   => $this->request->get('shortcode_id'),
      ]
        );

        if ($result) {
            set_transient('feedback', _('Shortocde update!'), 2);
        } else {
            set_transient('feedback', _('Error while updating shortocde!'), 2);
        }

        $this->redirect();

    }

    public function store()
    {
        $result = Shortcode::create(
            [
            Shortcode::COLUMN_NAME => $this->request->get('shortcode_name'),
            Shortcode::COLUMN_HTML => $this->request->get('html'),
      ]
        );

        if ($result) {
            set_transient('feedback', _('Shortocde created!'), 2);
        } else {
            set_transient('feedback', _('Error while creating shortocde!'), 2);
        }

        $this->redirect();
    }

    public function destroy($destroy)
    {

        $result = Shortcode::destroy($destroy);

        $feedback = __('Shortcode not found');

        if ($result) {
            $feedback = __('Shortcode removed!');
        }

        set_transient('feedback', $feedback, 2);

        $this->redirect();
    }

}
