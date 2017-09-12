<?php
/**
 * Endpoint utils.
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
 * Endpoint utils.
 *
 * @since 17xxxx Endpoint utils.
 */
trait Endpoint
{
    /**
     * @since 17xxxx Endpoint utils.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Endpoint::is()
     */
    public static function isEndpoint(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Endpoint->is(...$args);
    }

    /**
     * @since 17xxxx Endpoint utils.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Endpoint::getVar()
     */
    public static function getEndpointVar(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Endpoint->getVar(...$args);
    }
}
