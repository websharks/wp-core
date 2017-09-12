<?php
/**
 * Jetpack.
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
 * Jetpack.
 *
 * @since 160720
 */
trait Jetpack
{
    /**
     * @since 160720 Jetpack utils.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Jetpack::markdown()
     */
    public static function jetpackMarkdown(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Jetpack->markdown(...$args);
    }

    /**
     * @since 170126.83164 Jetpack utils.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Jetpack::markdownEnabled()
     */
    public static function jetpackMarkdownEnabled(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Jetpack->markdownEnabled(...$args);
    }

    /**
     * @since 160720 Jetpack utils.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Jetpack::canMarkdown()
     */
    public static function jetpackCanMarkdown(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Jetpack->canMarkdown(...$args);
    }

    /**
     * @since 160720 Jetpack utils.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Jetpack::canLatex()
     */
    public static function jetpackCanLatex(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Jetpack->canLatex(...$args);
    }
}
