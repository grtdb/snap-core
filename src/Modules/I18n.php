<?php

namespace Snap\Modules;

use Snap\Hookable;

class I18n extends Hookable
{
    /**
     * Filters to add on init.
     *
     * @var array
     */
    protected $filters = [
        // register the snap text domain
        'after_setup_theme' => 'load_theme_textdomain',
    ];
    
    /**
     * Register the snap text domain
     */
    public function load_theme_textdomain()
    {
        load_theme_textdomain('snap', get_stylesheet_directory() . '/languages');
    }
}
