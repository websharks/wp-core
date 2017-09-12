<?php
/**
 * License key utils.
 *
 * @author @jaswrks
 * @copyright WebSharks™
 */
declare(strict_types=1);
namespace WebSharks\WpSharks\Core\Classes\SCore\Utils\CoreOnly;

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
 * License key utils.
 *
 * @since 160710 License key utils.
 */
class LicenseKeys extends Classes\SCore\Base\Core implements CoreInterfaces\SecondConstants
{
    /**
     * Class constructor.
     *
     * @since 160710 License key utils.
     *
     * @param Classes\App $App Instance.
     */
    public function __construct(Classes\App $App)
    {
        parent::__construct($App);

        if (!$this->App->is_core) {
            throw $this->c::issue('Core only.');
        }
    }

    /**
     * Request license key via dashboard notice.
     *
     * @since 160710 License key utils.
     *
     * @param string $app_slug App slug.
     */
    public function maybeRequestViaNotice(string $app_slug)
    {
        if (!($App = $this->s::getAppsBySlug()[$app_slug] ?? null)) {
            return; // Not possible.
        } elseif ($App->Config->§specs['§in_wp']) {
            return; // Not necessary.
        } elseif ($App->s::getOption('§license_key')) {
            return; // Already have a license key.
        } elseif (!in_array($App->Config->§specs['§type'], ['theme', 'plugin'], true)) {
            return; // Only applies to themes & plugins.
        }
        $is_trial_expired     = $App->s::isTrialExpired();
        $trial_days_remaining = $App->s::trialDaysRemaining();

        $App->s::enqueueNotice('', [
            'id'   => '§license-key-request',
            'type' => $is_trial_expired ? 'warning' : 'info',

            'is_persistent'  => true,
            'is_dismissable' => $is_trial_expired ? false : true,

            'recurs_every' => $trial_days_remaining >= 0 ? $this::SECONDS_IN_DAY * 7 : 0,
            'recurs_times' => $trial_days_remaining >= 0 ? PHP_INT_MAX : 0,

            'for_context' => $App->Config->§specs['§is_network_wide']
                && $this->Wp->is_multisite ? 'network' : 'admin',

            'is_applicable' => function (Classes\App $App) {
                return $App->Parent->s::licenseKeyRequestViaNoticeIsApplicable($App->Config->©brand['©slug']);
            },
            'markup' => function (Classes\App $App) {
                return $App->Parent->s::licenseKeyRequestViaNoticeMarkup($App->Config->©brand['©slug']);
            },
        ]);
    }

    /**
     * Request via notice is applicable?
     *
     * @since 160712 License key utils.
     *
     * @param string $app_slug App slug.
     *
     * @return null|bool Null = dequeue entirely.
     */
    public function requestViaNoticeIsApplicable(string $app_slug)
    {
        if (!($App = $this->s::getAppsBySlug()[$app_slug] ?? null)) {
            return null; // Dequeue entirely.
        } elseif ($App->s::getOption('§license_key')) {
            return null; // Dequeue entirely; got license key.
            //
        } elseif ($App->Config->§specs['§is_network_wide'] && $this->Wp->is_multisite && !$this->Wp->is_network_admin) {
            return false; // Should only be shown in the network admin area.
        } elseif (!$App->Config->§specs['§is_network_wide'] && $this->Wp->is_network_admin) {
            return false; // Should not be shown in network admin. Requires site-specific keys.
            //
        } elseif ($this->s::isOwnMenuPage()) {
            return false; // Not on core pages.
        } elseif (in_array($menu_page = $this->s::currentMenuPage(), ['update-core.php'], true)) {
            return false; // Not during core update.
        } elseif (in_array($menu_page, ['themes.php', 'plugins.php', 'update.php'], true) && !empty($_REQUEST['action'])) {
            return false; // Not during a plugin install/activate/update.
        }
        return true; // Is applicable; i.e., display notice.
    }

    /**
     * Request via notice markup.
     *
     * @since 160712 License key utils.
     *
     * @param string $app_slug App slug.
     *
     * @return string Empty = dequeue entirely.
     */
    public function requestViaNoticeMarkup(string $app_slug): string
    {
        if (!($App = $this->s::getAppsBySlug()[$app_slug] ?? null)) {
            return ''; // Dequeue entirely.
        }
        return $App->c::getTemplate('s-core/admin/notices/license-key-request.php')->parse();
    }

    /**
     * License key update handler.
     *
     * @since 160710 License key utils.
     */
    public function onRestActionUpdateLicenseKeys()
    {
        if (!current_user_can($this->App->Config->§caps['§manage'])) {
            $this->s::dieForbidden(); // Disallow.
        }
        $apps_by_slug = $this->s::getAppsBySlug();
        $data         = (array) $this->s::restActionData('', true);
        $license_keys = (array) ($data['license_keys'] ?? []);
        $Errors       = $this->c::error();

        foreach ($license_keys as $_app_slug => $_license_key) {
            if (!$_app_slug || !is_string($_app_slug) || !is_string($_license_key)) {
                continue; // Bypass; invalid request data.
            } elseif (!($_App = $apps_by_slug[$_app_slug] ?? null)) {
                continue; // App is no longer active.
            }
            $_existing_license_key = $_App->s::getOption('§license_key');

            if ($_license_key) { // Activate (or reactivate) a license key.
                if ($_existing_license_key && $_existing_license_key !== $_license_key) {
                    $this->deactivate($_app_slug, $_existing_license_key);
                } // ↑ Deactivate existing license key.

                if ($this->c::isError($_Error = $this->activate($_app_slug, $_license_key))) {
                    $Errors->add($_Error->slug(), '**'.$_App->Config->©brand['§product_name'].':** '.$_Error->message());
                    $_App->s::updateOptions(['§license_key' => '']); // Empty; problem w/ license key.
                } else {
                    $_App->s::updateOptions(['§license_key' => $_license_key]);
                }
            } elseif (!$_license_key && $_existing_license_key) { // Deactivation.
                $this->deactivate($_app_slug, $_existing_license_key);
                $_App->s::updateOptions(['§license_key' => '']);
            }
        } // unset($_app_slug, $_license_key, $_App, $_existing_license_key, $_Error);

        if ($Errors->exist()) { // Display a full list of all errors.
            $notice_heading = __('Problem updating %1$s™ license keys:', 'wp-sharks-core');
            $notice_heading = sprintf($notice_heading, esc_html($this->App::CORE_CONTAINER_NAME));
            $notice_markup  = $this->s::menuPageNoticeErrors($notice_heading, $Errors->messages());
            $this->s::enqueueUserNotice($notice_markup, ['type' => 'error']);
        } else {
            $notice_markup = sprintf(__('%1$s™ license keys updated successfully.', 'wp-sharks-core'), esc_html($this->App::CORE_CONTAINER_NAME));
            $this->s::enqueueUserNotice($notice_markup, ['type' => 'success']);
        }
        wp_redirect($this->s::menuPageUrl()).exit(); // Stop on redirection.
    }

    /**
     * Activate a license key.
     *
     * @since 160710 License key utils.
     *
     * @param string $app_slug    App slug.
     * @param string $license_key License key.
     *
     * @return bool|Error True on success, error on failure.
     */
    public function activate(string $app_slug, string $license_key)
    {
        if (!($App = $this->s::getAppsBySlug()[$app_slug] ?? null)) {
            return $this->c::error('missing-app');
        }
        $remote_post_url  = $this->s::coreBrandApiUrl();
        $remote_post_body = [ // API call leading back to core brand.
            $this->s::coreBrandApiUrlArg('action') => 'api-v1.0.activate-product-license-key',
            $this->s::coreBrandApiUrlArg('data')   => [
                'license_key' => $license_key,
                'site'        => $App->Config->§specs['§is_network_wide']
                    && $this->Wp->is_multisite ? network_site_url() : site_url(),
                'slug' => $App->Config->©brand['§product_slug'],
            ],
        ];
        $remote_response     = wp_remote_post($remote_post_url, ['body' => $remote_post_body]);
        $remote_api_response = is_wp_error($remote_response) ? null : json_decode($remote_response['body']);

        if (is_wp_error($remote_response)) {
            return $this->s::wpErrorConvert($remote_response);
        } elseif (!is_object($remote_api_response)) {
            return $this->c::error('non-object-response', __('Unknown error. Please try again later.', 'wp-sharks-core'));
        } elseif (!$remote_api_response->success) {
            return $this->c::error($remote_api_response->error->slug, $remote_api_response->error->message);
        }
        return true; // Success; i.e., no problems.
    }

    /**
     * Dectivate a license key.
     *
     * @since 160710 License key utils.
     *
     * @param string $app_slug    App slug.
     * @param string $license_key License key.
     *
     * @return bool|Error True on success, error on failure.
     */
    public function deactivate(string $app_slug, string $license_key)
    {
        if (!($App = $this->s::getAppsBySlug()[$app_slug] ?? null)) {
            return $this->c::error('missing-app');
        }
        $remote_post_url  = $this->s::coreBrandApiUrl();
        $remote_post_body = [ // API call leading back to core brand.
            $this->s::coreBrandApiUrlArg('action') => 'api-v1.0.deactivate-product-license-key',
            $this->s::coreBrandApiUrlArg('data')   => [
                'license_key' => $license_key,
                'site'        => $App->Config->§specs['§is_network_wide']
                    && $this->Wp->is_multisite ? network_site_url() : site_url(),
                'slug' => $App->Config->©brand['§product_slug'],
            ],
        ];
        $remote_response     = wp_remote_post($remote_post_url, ['body' => $remote_post_body]);
        $remote_api_response = is_wp_error($remote_response) ? null : json_decode($remote_response['body']);

        if (is_wp_error($remote_response)) {
            return $this->s::wpErrorConvert($remote_response);
        } elseif (!is_object($remote_api_response)) {
            return $this->c::error('non-object-response', __('Unknown error. Please try again later.', 'wp-sharks-core'));
        } elseif (!$remote_api_response->success) {
            return $this->c::error($remote_api_response->error->slug, $remote_api_response->error->message);
        }
        return true; // Success; i.e., no problems.
    }
}
