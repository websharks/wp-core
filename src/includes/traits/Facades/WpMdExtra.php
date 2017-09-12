<?php
/**
 * WP MD Extra.
 *
 * @author @jaswrks
 * @copyright WebSharks™
 */
declare(strict_types=1);
namespace WebSharks\WpSharks\Core\Traits\Facades;

use WebSharks\WpSharks\Core\Classes;
use WebSharks\WpSharks\Core\Interfaces;
use WebSharks\WpSharks\Core\Traits;
#
use WebSharks\Core\WpSharksCore\Classes as CoreClasses;
use WebSharks\Core\WpSharksCore\Interfaces as CoreInterfaces;
use WebSharks\Core\WpSharksCore\Traits as CoreTraits;
#
use WebSharks\Core\WpSharksCore\Classes\Core\Error;
use WebSharks\Core\WpSharksCore\Classes\Core\Base\Exception;
#
use function assert as debug;
use function get_defined_vars as vars;

/**
 * WP MD Extra.
 *
 * @since 170126.83164
 */
trait WpMdExtra
{
    /**
     * @since 170126.83164 WP MD Extra utils.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\WpMdExtra::transform()
     */
    public static function wpMdExtra(...$args)
    {
        return $GLOBALS[static::class]->Utils->§WpMdExtra->transform(...$args);
    }

    /**
     * @since 170126.83164 WP MD Extra utils.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\WpMdExtra::enabled()
     */
    public static function wpMdExtraEnabled(...$args)
    {
        return $GLOBALS[static::class]->Utils->§WpMdExtra->enabled(...$args);
    }

    /**
     * @since 170126.83164 WP MD Extra utils.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\WpMdExtra::canTransform()
     */
    public static function canWpMdExtra(...$args)
    {
        return $GLOBALS[static::class]->Utils->§WpMdExtra->canTransform(...$args);
    }
}
