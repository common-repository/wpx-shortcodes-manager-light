<?php

namespace WPXShortcodesManagerLight\Models;

final class Shortcode
{
    // Columns
    public const COLUMN_ID              = 'shortcode_id';
    public const COLUMN_ENABLED         = 'enabled';
    public const COLUMN_SHOW_IN_CONTENT = 'show_in_content';
    public const COLUMN_CALLBACK        = 'callback';
    public const COLUMN_CUSTOM          = 'custom';
    public const COLUMN_STATUS          = 'status';
    public const COLUMN_NAME            = 'name';
    public const COLUMN_HTML            = 'html';

    // Internal
    public const COLUMN_CALLABLE = 'callable';

    // Statues
    public const STATUS_CUSTOM          = 'custom';
    public const STATUS_ENABLED         = 'enabled';
    public const STATUS_DISABLED        = 'disabled';
    public const STATUS_SHOW_IN_CONTENT = 'show_in_content';
    public const STATUS_HIDE_IN_CONTENT = 'hide_in_content';

    // Option keys for disabled shortcodes list
    public const OPTION_KEY_DISABLED_SHORTCODES_LIST        = 'wpxsm-shortcodes-disabled';
    public const OPTION_KEY_HIDE_IN_CONTENT_SHORTCODES_LIST = 'wpxsm-shortcodes-removed';
    public const OPTION_KEY_CUSTOM_SHORTCODES_LIST          = 'wpxsm-shortcodes-custom';

    protected $_items = [ ];

    public static function __callStatic($method, $parameters)
    {
        $instance = new self();

        return call_user_func_array([ $instance, $method ], $parameters);
    }

    protected function create($args)
    {
        $items = $this->getItems();

        // check for already exists
        if (in_array($args[ self::COLUMN_NAME ], array_keys($items))) {
            return false;
        }

        // get current custom list
        $custom = get_site_option(self::OPTION_KEY_CUSTOM_SHORTCODES_LIST, [ ]);

        $custom[ $args[ self::COLUMN_NAME ] ] = [
          self::COLUMN_NAME => $args[ self::COLUMN_NAME ],
          self::COLUMN_HTML => base64_encode($args[ self::COLUMN_HTML ])
        ];

        // Update for insert
        update_site_option(self::OPTION_KEY_CUSTOM_SHORTCODES_LIST, $custom);

        return $custom[ $args[ self::COLUMN_NAME ] ];
    }

    protected function update($args)
    {
        // get current custom list
        $custom = get_site_option(self::OPTION_KEY_CUSTOM_SHORTCODES_LIST, [ ]);

        if (isset($custom[ $args[ self::COLUMN_ID ] ])) {

            unset($custom[ $args[ self::COLUMN_ID ] ]);

            $custom[ $args[ self::COLUMN_NAME ] ] = [
              self::COLUMN_NAME => $args[ self::COLUMN_NAME ],
              self::COLUMN_HTML => base64_encode($args[ self::COLUMN_HTML ])
            ];

            // Update for insert
            update_site_option(self::OPTION_KEY_CUSTOM_SHORTCODES_LIST, $custom);

            return $custom[ $args[ self::COLUMN_NAME ] ];
        }

        return false;

    }

    protected function destroy($id)
    {
        // get current custom list
        $custom = get_site_option(self::OPTION_KEY_CUSTOM_SHORTCODES_LIST, [ ]);

        if (isset($custom[ $id ])) {
            unset($custom[ $id ]);
            update_site_option(self::OPTION_KEY_CUSTOM_SHORTCODES_LIST, $custom);

            return true;
        }

        return false;
    }

    protected function exists($id)
    {
        foreach($this->all() as $key => $value) {
            if($key == $id) {
                return true;
            }
        }

        return false;
    }

    protected function showInContent($id)
    {
        return $this->_showInContent($id, true);
    }

    protected function hideInContent($id)
    {
        return $this->_showInContent($id, false);
    }

    private function _showInContent($id, $bool)
    {

        // Get the content removed list
        $hide_in_content = get_site_option(self::OPTION_KEY_HIDE_IN_CONTENT_SHORTCODES_LIST, [ ]);

        if ($bool) {
            if (isset($hide_in_content[ $id ])) {
                unset($hide_in_content[ $id ]);
            }
        } else {
            $hide_in_content[ $id ] = $id;
        }

        $hide_in_content = array_unique($hide_in_content);

        update_site_option(self::OPTION_KEY_HIDE_IN_CONTENT_SHORTCODES_LIST, $hide_in_content);

        do_action('wpxsm_refresh_disabled');

        return true;

    }

    protected function enable($id)
    {
        return $this->_enable($id, true);
    }

    protected function disable($id)
    {
        return $this->_enable($id, false);
    }

    private function _enable($id, $bool)
    {
        // Get the content removed list
        $disable = get_site_option(self::OPTION_KEY_DISABLED_SHORTCODES_LIST, [ ]);

        if ($bool) {
            if (isset($disable[ $id ])) {
                unset($disable[ $id ]);
            }
        } else {
            $disable[ $id ] = $id;
        }

        $disable = array_unique($disable);

        update_site_option(self::OPTION_KEY_DISABLED_SHORTCODES_LIST, $disable);

        do_action('wpxsm_refresh_disabled');

        return true;
    }

    protected function find($id)
    {
        $item = $this->getItems([ self::COLUMN_ID => $id ]);

        return array_shift($item);
    }

    protected function all()
    {
        return $this->getItems();
    }

    protected function getItems($args = [ ])
    {

        global $shortcode_tags, $WPXSM_REMOVED_SHORTCODES;

        // Get the disabled list
        $disabled = get_site_option(self::OPTION_KEY_DISABLED_SHORTCODES_LIST, [ ]);

        // Get the content removed list
        $hide_in_content = get_site_option(self::OPTION_KEY_HIDE_IN_CONTENT_SHORTCODES_LIST, [ ]);

        // Get the custom list
        $custom = get_site_option(self::OPTION_KEY_CUSTOM_SHORTCODES_LIST, [ ]);

        $items = [ ];

        foreach ($shortcode_tags as $name => $function) {

            $item = [
              self::COLUMN_ID              => $name,
              self::COLUMN_NAME            => $name,
              self::COLUMN_CALLBACK        => '',
              self::COLUMN_CALLABLE        => false,
              self::COLUMN_CUSTOM          => in_array($name, array_keys($custom)),
              self::COLUMN_HTML            => in_array($name, array_keys($custom)) ? $custom[ $name ][ 'html' ] : '',
              self::COLUMN_ENABLED         => ! in_array($name, $disabled),
              self::COLUMN_SHOW_IN_CONTENT => ! in_array($name, $hide_in_content),
            ];

            // Extract addition info and callback
            if (is_string($function)) {
                $item[ self::COLUMN_CALLBACK ] = $function;
                $item[ self::COLUMN_CALLABLE ] = is_callable($function, true);
            }
            // Object class method
            elseif (is_array($function) && is_object($function[ 0 ]) && is_string($function[ 1 ])) {
                $item[ self::COLUMN_CALLBACK ] = sprintf('%s::%s()', get_class($function[ 0 ]), $function[ 1 ]);
                $item[ self::COLUMN_CALLABLE ] = is_callable($function, true);
            }
            // Object string class method
            elseif (is_array($function) && is_string($function[ 0 ]) && is_string($function[ 1 ])) {
                $item[ self::COLUMN_CALLBACK ] = sprintf('%s::%s()', $function[ 0 ], $function[ 1 ]);
                $item[ self::COLUMN_CALLABLE ] = is_callable($function, true);
            }

            // Single item
            if (! empty($args[ self::COLUMN_ID ]) && $name == $args[ self::COLUMN_ID ]) {
                $this->_items = [ $item ];

                return $this->_items;
            }

            $items[ $name ] = $item;

        }

        ksort($items);

        $this->_items = $items;

        return $this->_items;
    }
}
