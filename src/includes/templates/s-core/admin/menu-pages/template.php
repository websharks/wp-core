<?php
declare (strict_types = 1);
namespace WebSharks\WpSharks\Core;

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

extract($this->vars); // Template variables.
?>
<div class="<?= esc_attr($cfg->class); ?>">
    <h1 class="-hidden" data-wp-notices-here></h1>

    <div class="-container">

        <?= $cfg->nav_tabs; ?>

        <div class="-content">
            <?= $this->get($cfg->template_file, [], $cfg->template_dir); ?>
        </div>

    </div>
</div>
