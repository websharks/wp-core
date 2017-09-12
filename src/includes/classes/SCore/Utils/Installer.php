<?php
/**
 * Install utils.
 *
 * @author @jaswrks
 * @copyright WebSharks™
 */
declare(strict_types=1);
namespace WebSharks\WpSharks\Core\Classes\SCore\Utils;

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
 * Install utils.
 *
 * @since 160524 Install utils.
 */
class Installer extends Classes\SCore\Base\Core
{
    /**
     * Trial days.
     *
     * @since 161013
     *
     * @type int
     */
    public $trial_days;

    /**
     * History.
     *
     * @since 160524
     *
     * @type array
     */
    protected $history;

    /**
     * Class constructor.
     *
     * @since 160524 Install utils.
     *
     * @param Classes\App $App Instance.
     */
    public function __construct(Classes\App $App)
    {
        parent::__construct($App);

        $default_history = [
            'first_time'   => 0,
            'last_time'    => 0,
            'last_version' => '',
            'versions'     => [],
        ];
        if (!is_array($this->history = $this->s::sysOption('install_history'))) {
            $this->history = $default_history; // Defaults.
        }
        $this->history = array_merge($default_history, $this->history);
        $this->history = array_intersect_key($this->history, $default_history);

        foreach ($default_history as $_key => $_default_history_value) {
            settype($this->history[$_key], gettype($_default_history_value));
        } // unset($_key, $_default_history_value);

        // Can be adjusted in special circumstances, but never > 90 days.
        $this->trial_days = min(90, $this->s::applyFilters('trial_days', 30));
    }

    /**
     * Maybe install.
     *
     * @since 160524 Install utils.
     */
    public function maybeInstall()
    {
        if ($this->App->Config->§uninstall) {
            return; // Sanity check.
        }
        if ($this->App->Config->§force_install // Forcing install (or reinstall)?
                || version_compare($this->history['last_version'], $this->App::VERSION, '<')
                || version_compare($this->s::getOption('§for_version'), $this->App::VERSION, '<')) {
            $this->install(); // Install (or reinstall).
        }
    }

    /**
     * Maybe expire trial.
     *
     * @since 161013 Install utils.
     *
     * @return bool True if trial is expired.
     */
    public function maybeExpireTrial(): bool
    {
        if (!$this->isTrialExpired()) {
            return false; // Not applicable.
        } elseif (!$this->App->Parent || !$this->App->Parent->is_core) {
            return false; // Not applicable.
        }
        $this->App->Parent->s::maybeRequestLicenseKeyViaNotice($this->App->Config->©brand['©slug']);

        return true; // Trial expired, yes.
    }

    /**
     * Is trial expired?
     *
     * @since 161013 Install utils.
     *
     * @return bool True if trial is expired.
     */
    public function isTrialExpired(): bool
    {
        return $this->trialDaysRemaining() === 0;
    }

    /**
     * Trial days remaining.
     *
     * @since 161013 Install utils.
     *
     * @return int Trial days remaining.
     */
    public function trialDaysRemaining(): int
    {
        if (($days = &$this->cacheKey(__FUNCTION__)) !== null) {
            return $days; // Cached already.
        }
        if ($this->App->is_core) {
            return $days = -1; // Not applicable.
        } elseif ($this->App->Config->§specs['§in_wp']) {
            return $days = -1; // Not applicable.
        } elseif (!$this->App->Config->§specs['§is_pro'] && !$this->App->Config->§specs['§is_elite']) {
            return $days = -1; // Not elite/pro version.
        } elseif ($this->App->Config->§options['§license_key']) {
            return $days = -1; // Have license key.
        } elseif (!in_array($this->App->Config->§specs['§type'], ['theme', 'plugin'], true)) {
            return $days = -1; // Only applies to themes & plugins.
        } elseif (!$this->App->Parent || !$this->App->Parent->is_core) {
            return $days = -1; // Not applicable.
        }
        if (!$this->history['first_time']) {
            return $days = $this->trial_days;
        }
        $time        = time(); // Current time.
        $exp_time    = strtotime('+'.$this->trial_days.' days', $this->history['first_time']);
        return $days = $exp_time > $time ? min($this->trial_days, (int) ceil($exp_time - $time)) : 0;
    }

    /**
     * Install (or reinstall).
     *
     * @since 160524 Install utils.
     */
    protected function install()
    {
        // Version-specific.
        $this->maybeRunVsUpgrades();

        // Misc installers.
        $this->createDbTables();
        $this->otherInstallRoutines();
        $this->doFlushRewriteRules();
        $this->maybeEnqueueNotices();

        // History/options.
        $this->updateHistory();
        $this->updateOptions();
    }

    /**
     * Version-specific upgrades.
     *
     * @since 160713 Install utils.
     */
    protected function maybeRunVsUpgrades()
    {
        if ($this->history['last_version']) {
            $this->s::doAction('vs_upgrades', $this->history);
        }
    }

    /**
     * Create DB tables.
     *
     * @since 160524 Install utils.
     */
    protected function createDbTables()
    {
        $this->s::createDbTables();
    }

    /**
     * Other install routines.
     *
     * @since 160524 Install utils.
     */
    protected function otherInstallRoutines()
    {
        $this->s::doAction('other_install_routines', $this->history);
    }

    /**
     * Flush rewrite rules.
     *
     * @since 160524 Install utils.
     */
    protected function doFlushRewriteRules()
    {
        if (!empty($GLOBALS['wp_rewrite'])) {
            flush_rewrite_rules();
        } else {
            add_action('setup_theme', 'flush_rewrite_rules');
        }
    }

    /**
     * Install (or reinstall) notices.
     *
     * @since 160524 Install utils.
     */
    protected function maybeEnqueueNotices()
    {
        if (($is_install = !$this->history['first_time'])) {
            $notice_template_file = 's-core/admin/notices/on-install.php';
        } else { // Reinstalling (notify about update).
            $notice_template_file = 's-core/admin/notices/on-reinstall.php';
        }
        $notice_Template = $this->c::getTemplate($notice_template_file);
        $notice_markup   = $notice_Template->parse(['history' => $this->history]);
        $this->s::enqueueNotice($notice_markup, ['type' => 'success', 'is_transient' => $is_install]);

        if ($this->App->Parent && $this->App->Parent->is_core) {
            $this->App->Parent->s::maybeRequestLicenseKeyViaNotice($this->App->Config->©brand['©slug']);
        }
    }

    /**
     * Update installed version.
     *
     * @since 160524 Install utils.
     */
    protected function updateHistory()
    {
        $time    = time();
        $version = $this->App::VERSION;

        if (!$this->history['first_time']) {
            $this->history['first_time'] = $time;
        }
        $this->history['last_time']          = $time;
        $this->history['last_version']       = $version;
        $this->history['versions'][$version] = $time;

        uksort($this->history['versions'], 'version_compare');
        $this->history['versions'] = array_reverse($this->history['versions'], true);

        $this->s::sysOption('install_history', $this->history);
    }

    /**
     * Update options version.
     *
     * @since 160713 Install utils.
     */
    protected function updateOptions()
    {
        $this->s::updateOptions(['§for_version' => $this->App::VERSION]);
    }
}
