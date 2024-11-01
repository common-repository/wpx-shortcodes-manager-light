<?php

namespace WPXShortcodesManagerLight\WPBones\Html;

class Html
{
    protected static $htmlTags = [
    'a'        => '\WPXShortcodesManagerLight\WPBones\Html\HtmlTagA',
    'button'   => '\WPXShortcodesManagerLight\WPBones\Html\HtmlTagButton',
    'checkbox' => '\WPXShortcodesManagerLight\WPBones\Html\HtmlTagCheckbox',
    'datetime' => '\WPXShortcodesManagerLight\WPBones\Html\HtmlTagDatetime',
    'fieldset' => '\WPXShortcodesManagerLight\WPBones\Html\HtmlTagFieldSet',
    'form'     => '\WPXShortcodesManagerLight\WPBones\Html\HtmlTagForm',
    'input'    => '\WPXShortcodesManagerLight\WPBones\Html\HtmlTagInput',
    'label'    => '\WPXShortcodesManagerLight\WPBones\Html\HtmlTagLabel',
    'optgroup' => '\WPXShortcodesManagerLight\WPBones\Html\HtmlTagOptGroup',
    'option'   => '\WPXShortcodesManagerLight\WPBones\Html\HtmlTagOption',
    'select'   => '\WPXShortcodesManagerLight\WPBones\Html\HtmlTagSelect',
    'textarea' => '\WPXShortcodesManagerLight\WPBones\Html\HtmlTagTextArea',
  ];

    public static function __callStatic($name, $arguments)
    {
        if (in_array($name, array_keys(self::$htmlTags))) {
            $args = (isset($arguments[ 0 ]) && ! is_null($arguments[ 0 ])) ? $arguments[ 0 ] : [];

            return new self::$htmlTags[ $name ]($args);
        }
    }
}
