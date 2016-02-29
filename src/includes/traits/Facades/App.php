<?php
declare (strict_types = 1);
namespace WebSharks\WpSharks\Core\Traits\Facades;

use WebSharks\WpSharks\Core\Classes;
use WebSharks\WpSharks\Core\Interfaces;
use WebSharks\WpSharks\Core\Traits;
#
use WebSharks\Core\WpSharksCore\Classes as CoreClasses;
use WebSharks\Core\WpSharksCore\Classes\Core\Base\Exception;
use WebSharks\Core\WpSharksCore\Interfaces as CoreInterfaces;
use WebSharks\Core\WpSharksCore\Traits as CoreTraits;

trait App
{
    /**
     * @since 16xxxx App.
     */
    public static function applyFilters(...$args)
    {
        return $GLOBALS[static::class]->applyFilters(...$args);
    }

    /**
     * @since 16xxxx App.
     */
    public static function doAction(...$args)
    {
        return $GLOBALS[static::class]->doAction(...$args);
    }
}
