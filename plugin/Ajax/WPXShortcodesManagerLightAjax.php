<?php

namespace WPXShortcodesManagerLight\Ajax;

use WPXShortcodesManagerLight\WPBones\Foundation\WordPressAjaxServiceProvider;
use WPXShortcodesManagerLight\Models\Shortcode;
use WPXShortcodesManagerLight\Http\Controllers\Dashboard\ShortcodesListTable;

final class WPXShortcodesManagerLightAjax extends WordPressAjaxServiceProvider
{
    /**
     * List of the ajax actions executed only by logged in users.
     * Here you will used a methods list.
     *
     * @var array
     */
    protected $logged = [
      'wpxsm_action_disable',
      'wpxsm_action_enable',
      'wpxsm_action_hide_in_content',
      'wpxsm_action_show_in_content',
    ];

    public function wpxsm_action_disable()
    {
        $shortocde_id = $this->request->get('shortcode_id');

        if ($shortocde_id) {
            $result = Shortcode::disable($shortocde_id);

            wp_send_json_success(
                [
                'row'          => ShortcodesListTable::getSingleRow($shortocde_id),
                'shortcode_id' => $shortocde_id
        ]
            );
        }
    }

    public function wpxsm_action_enable()
    {
        $shortocde_id = $this->request->get('shortcode_id');

        if ($shortocde_id) {
            $result = Shortcode::enable($shortocde_id);

            wp_send_json_success(
                [
                'row'          => ShortcodesListTable::getSingleRow($shortocde_id),
                'shortcode_id' => $shortocde_id
        ]
            );
        }
    }

    public function wpxsm_action_hide_in_content()
    {
        $shortocde_id = $this->request->get('shortcode_id');

        if ($shortocde_id) {
            $result = Shortcode::hideInContent($shortocde_id);

            wp_send_json_success(
                [
                'row'          => ShortcodesListTable::getSingleRow($shortocde_id),
                'shortcode_id' => $shortocde_id
        ]
            );
        }
    }

    public function wpxsm_action_show_in_content()
    {
        $shortocde_id = $this->request->get('shortcode_id');

        if ($shortocde_id) {
            $result = Shortcode::showInContent($shortocde_id);

            wp_send_json_success(
                [
                'row'          => ShortcodesListTable::getSingleRow($shortocde_id),
                'shortcode_id' => $shortocde_id
        ]
            );
        }
    }

}
