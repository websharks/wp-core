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
#
use function assert as debug;
use function get_defined_vars as vars;

trait StylesScripts
{
    /**
     * @since 160709 Sharkicon libs.
     */
    public static function enqueueSharkiconLibs(...$args)
    {
        return $GLOBALS[static::class]->Utils->§StylesScripts->enqueueSharkiconLibs(...$args);
    }

    /**
     * @since 160524 Initial release.
     */
    public static function enqueueMomentLibs(...$args)
    {
        return $GLOBALS[static::class]->Utils->§StylesScripts->enqueueMomentLibs(...$args);
    }

    /**
     * @since 160524 Initial release.
     */
    public static function enqueueJQueryPickadateLibs(...$args)
    {
        return $GLOBALS[static::class]->Utils->§StylesScripts->enqueueJQueryPickadateLibs(...$args);
    }

    /**
     * @since 160524 Initial release.
     */
    public static function enqueueJQueryChosenLibs(...$args)
    {
        return $GLOBALS[static::class]->Utils->§StylesScripts->enqueueJQueryChosenLibs(...$args);
    }

    /**
     * @since 160524 Initial release.
     */
    public static function enqueueJQueryJsGridLibs(...$args)
    {
        return $GLOBALS[static::class]->Utils->§StylesScripts->enqueueJQueryJsGridLibs(...$args);
    }

    /**
     * @since 160709 Menu page libs.
     */
    public static function enqueueMenuPageLibs(...$args)
    {
        return $GLOBALS[static::class]->Utils->§StylesScripts->enqueueMenuPageLibs(...$args);
    }
}
