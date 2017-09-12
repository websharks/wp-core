<?php
/**
 * Notices.
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
 * Notices.
 *
 * @since 160524
 */
trait Notices
{
    /**
     * @since 160524 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Notices::enqueue()
     */
    public static function enqueueNotice(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Notices->enqueue(...$args);
    }

    /**
     * @since 160524 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Notices::userEnqueue()
     */
    public static function enqueueUserNotice(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Notices->userEnqueue(...$args);
    }

    /**
     * @since 161013 Recurring notices.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Notices::dequeue()
     */
    public static function dequeueNotice(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Notices->dequeue(...$args);
    }

    /**
     * @since 160524 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Notices::dismiss()
     */
    public static function dismissNotice(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Notices->dismiss(...$args);
    }

    /**
     * @since 161014 Trial routines.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Notices::dismissUrl()
     */
    public static function dismissNoticeUrl(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Notices->dismissUrl(...$args);
    }
}
