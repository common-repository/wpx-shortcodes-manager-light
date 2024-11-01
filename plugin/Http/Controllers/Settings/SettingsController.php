<?php

namespace WPXShortcodesManagerLight\Http\Controllers\Settings;

use WPXShortcodesManagerLight\Http\Controllers\Controller;
use WPXShortcodesManagerLight\PureCSSTabs\PureCSSTabsProvider;
use WPXShortcodesManagerLight\PureCSSSwitch\PureCSSSwitchProvider;

class SettingsController extends Controller
{
    public function index()
    {
        PureCSSTabsProvider::enqueueStyles();
        PureCSSSwitchProvider::enqueueStyles();

        return WPXShortcodesManagerLight()
          ->view('settings.index')
          ->withAdminStyles('wpxsm-admin');
    }

    public function saveSettings()
    {
        if ($this->request->verifyNonce('wpx-shortcodes-manager-settings')) {
            $resetTodefault = $this->request->get('reset-to-default', null);

            if (! is_null($resetTodefault)) {
                WPXShortcodesManagerLight()->options->reset();
                $feedback = __('Settings Reset to default successfully!', 'wpx-shortcodes-manager-light');
            } else {
                WPXShortcodesManagerLight()
                  ->options
                  ->update($this->request->getAsOptions());

                $feedback = __('Settings updated successfully!', 'wpx-shortcodes-manager-light');
            }

            PureCSSTabsProvider::enqueueStyles();
            PureCSSSwitchProvider::enqueueStyles();

            return WPXShortcodesManagerLight()
              ->view('settings.index')
              ->withAdminStyles('wpxsm-admin')
              ->with('feedback', $feedback);
        }
    }
}
