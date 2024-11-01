<?php

namespace WPXShortcodesManagerLight\WPBones\Contracts\Foundation;

use WPXShortcodesManagerLight\WPBones\Contracts\Container\Container;

interface Plugin extends Container
{
  /**
   * Get the base path of the Plugin installation.
   *
   * @return string
   */
  public function getBasePath(): string;
}
