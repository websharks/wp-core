<?php
declare (strict_types = 1);
namespace WebSharks\WpSharks\Core\Classes;

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

/**
 * App (plugins must extend).
 *
 * @since 160524 Initial release.
 */
class App extends CoreClasses\App
{
    /**
     * Version.
     *
     * @since 160524
     *
     * @type string Version.
     */
    const VERSION = '160710.28269'; //v//

    /**
     * ReST action API version.
     *
     * @since 160625 ReST actions.
     *
     * @type string API version.
     */
    const REST_ACTION_API_VERSION = '1.0';

    /**
     * Core container slug.
     *
     * @since 160702 Core container.
     *
     * @type string Core container slug.
     */
    const CORE_CONTAINER_SLUG = 'wp-sharks';

    /**
     * Core container var.
     *
     * @since 160702 Core container.
     *
     * @type string Core container var.
     */
    const CORE_CONTAINER_VAR = 'wp_sharks';

    /**
     * Core container name.
     *
     * @since 160702 Core container.
     *
     * @type string Core container name.
     */
    const CORE_CONTAINER_NAME = 'WP Sharks';

    /**
     * Constructor.
     *
     * @since 160524 Initial release.
     *
     * @param array            $instance_base Instance base.
     * @param array            $instance      Instance args.
     * @param Classes\App|null $Parent        Parent app (optional).
     * @param array            $args          Any additional behavioral args.
     */
    public function __construct(array $instance_base = [], array $instance = [], Classes\App $Parent = null, array $args = [])
    {
        # Establish arguments.

        $default_args = [
            '©use_server_cfgs' => false,
            '§validate_brand'  => true,
        ];
        $args = array_merge($default_args, $args);

        # Define a few reflection-based properties.

        $this->Reflection = new \ReflectionClass($this);

        $this->class     = $this->Reflection->getName();
        $this->namespace = $this->Reflection->getNamespaceName();

        $this->file              = $this->Reflection->getFileName();
        $this->base_dir          = dirname($this->file, 4);
        $this->base_dir_basename = basename($this->base_dir);

        # Establish specs & brand for parent constructor.

        if ($this->class === self::class) {
            $specs = array_merge(
                [
                    '§is_pro'          => false,
                    '§in_wp'           => false,
                    '§is_network_wide' => false,

                    '§type' => 'plugin',
                    '§file' => $this->base_dir.'/plugin.php',
                ],
                $instance_base['§specs'] ?? [],
                $instance['§specs'] ?? []
            );
            $brand = array_merge(
                [
                    '©name'    => 'WP Sharks Core',
                    '©acronym' => 'WPS Core',

                    '©text_domain' => 'wp-sharks-core',

                    '©slug' => 'wp-sharks-core',
                    '©var'  => 'wp_sharks_core',

                    '©short_slug' => 'wps-core',
                    '©short_var'  => 'wps_core',

                    '§domain'           => 'wpsharks.com',
                    '§domain_path'      => '/product/core',
                    '§domain_short_var' => 'wps',

                    '§api_domain'           => 'api.wpsharks.com',
                    '§api_domain_path'      => '/',
                    '§api_domain_short_var' => 'wps',

                    '§cdn_domain'           => 'cdn.wpsharks.com',
                    '§cdn_domain_path'      => '/',
                    '§cdn_domain_short_var' => 'wps',

                    '§stats_domain'           => 'stats.wpsharks.io',
                    '§stats_domain_path'      => '/',
                    '§stats_domain_short_var' => 'wps',
                ],
                $instance_base['©brand'] ?? [],
                $instance['©brand'] ?? []
            );
        } else {
            if (!isset($GLOBALS[self::class])) {
                throw new Exception('Missing core instance.');
            }
            $Parent = $Parent ?? $GLOBALS[self::class];

            $specs = array_merge(
                [
                    '§is_pro'          => null,
                    '§in_wp'           => null,
                    '§is_network_wide' => false,

                    '§type' => '',
                    '§file' => '',
                ],
                $instance_base['§specs'] ?? [],
                $instance['§specs'] ?? []
            );
            if (!isset($specs['§in_wp'])) {
                $specs['§in_wp'] = false;
            }
            if (!isset($specs['§is_pro'])) {
                $specs['§is_pro'] = mb_stripos($this->namespace, '\\Pro\\') !== false;
            }
            if (!$specs['§type'] || !$specs['§file']) {
                if (is_file($this->base_dir.'/plugin.php')) {
                    $specs['§type'] = 'plugin';
                    $specs['§file'] = $this->base_dir.'/plugin.php';
                } elseif (is_file($this->base_dir.'/style.css')) {
                    $specs['§type'] = 'theme';
                    $specs['§file'] = $this->base_dir.'/style.css';
                } elseif (is_file($this->base_dir.'/src/wp-content/mu-plugins/site.php')) {
                    $specs['§type'] = 'mu-plugin';
                    $specs['§file'] = $this->base_dir.'/src/wp-content/mu-plugins/site.php';
                } else { // Hard failure in this unexpected case.
                    throw new Exception('Unable to determine `§type`/`§file`.');
                } // The app will need to give its §type/§file explicitly.
            }
            $brand = array_merge(
                [
                    '©name'    => '',
                    '©acronym' => '',

                    '©text_domain' => '',

                    '©slug' => '',
                    '©var'  => '',

                    '©short_slug' => '',
                    '©short_var'  => '',

                    '§domain'           => '',
                    '§domain_path'      => '',
                    '§domain_short_var' => '',

                    '§api_domain'           => '',
                    '§api_domain_path'      => '',
                    '§api_domain_short_var' => '',

                    '§cdn_domain'           => '',
                    '§cdn_domain_path'      => '',
                    '§cdn_domain_short_var' => '',

                    '§stats_domain'           => '',
                    '§stats_domain_path'      => '',
                    '§stats_domain_short_var' => '',
                ],
                $instance_base['©brand'] ?? [],
                $instance['©brand'] ?? []
            );
            // Check slug first. It's the basis for others.

            if (!$brand['©slug']) {
                $brand['©slug'] = $Parent->c::nameToSlug($this->base_dir_basename);
                $brand['©slug'] = preg_replace('/[_\-]+(?:lite|pro)/ui', '', $brand['©slug']);
            } elseif ($args['§validate_brand'] && preg_match('/[_\-]+(?:lite|pro)$/ui', $brand['©slug'])) {
                throw new Exception('Please remove `lite|pro` suffix from ©slug.');
            }
            if (!$brand['©name']) {
                $brand['©name'] = $Parent->c::slugToName($brand['©slug']);
            } elseif ($args['§validate_brand'] && preg_match('/\s+(?:Lite|Pro)$/ui', $brand['©name'])) {
                throw new Exception('Please remove `Lite|Pro` suffix from ©name.');
            }
            if (!$brand['©acronym']) {
                $brand['©acronym'] = $Parent->c::nameToAcronym($brand['©name']);
            } elseif ($args['§validate_brand'] && preg_match('/(?:LITE|PRO)$/ui', $brand['©acronym'])) {
                throw new Exception('Please remove `LITE|PRO` suffix from ©acronym.');
            }
            if (!$brand['©text_domain']) {
                $brand['©text_domain'] = $brand['©slug'];
            } elseif ($args['§validate_brand'] && preg_match('/[_\-]+(?:lite|pro)$/ui', $brand['©text_domain'])) {
                throw new Exception('Please remove `lite|pro` suffix from ©text_domain.');
            }
            if (!$brand['©var']) {
                $brand['©var'] = $Parent->c::slugToVar($brand['©slug']);
            } elseif ($args['§validate_brand'] && preg_match('/[_\-]+(?:lite|pro)$/ui', $brand['©var'])) {
                throw new Exception('Please remove `lite|pro` suffix from ©var.');
            }
            if (!$brand['©short_slug']) {
                $brand['©short_slug'] = strlen($brand['©slug']) <= 10 ? $brand['©slug'] : 's'.substr(md5($brand['©slug']), 0, 9);
            } elseif ($args['§validate_brand'] && preg_match('/[_\-]+(?:lite|pro)$/ui', $brand['©short_var'])) {
                throw new Exception('Please remove `lite|pro` suffix from ©short_var.');
            } elseif ($args['§validate_brand'] && strlen($brand['©short_slug']) > 10) {
                throw new Exception('Please fix ©short_slug; must be <= 10 bytes.');
            }
            if (!$brand['©short_var']) {
                $brand['©short_var'] = strlen($brand['©var']) <= 10 ? $brand['©var'] : 's'.substr(md5($brand['©slug']), 0, 9);
            } elseif ($args['§validate_brand'] && preg_match('/[_\-]+(?:lite|pro)$/ui', $brand['©short_var'])) {
                throw new Exception('Please remove `lite|pro` suffix from ©short_var.');
            } elseif ($args['§validate_brand'] && strlen($brand['©short_var']) > 10) {
                throw new Exception('Please fix ©short_var; must be <= 10 bytes.');
            }
            if (!$brand['§domain']) {
                $brand['§domain']           = $Parent->Config->©brand['§domain'];
                $brand['§domain_path']      = '/product/'.$brand['©slug'];
                $brand['§domain_short_var'] = $Parent->Config->©brand['§domain_short_var'];
            }
        }
        # Collect essential WordPress config values.

        $wp_is_multisite  = is_multisite();
        $wp_debug         = defined('WP_DEBUG') && WP_DEBUG;
        $wp_debug_edge    = $wp_debug && defined('WP_DEBUG_EDGE') && WP_DEBUG_EDGE;
        $wp_debug_log     = $wp_debug && defined('WP_DEBUG_LOG') && WP_DEBUG_LOG;
        $wp_debug_display = $wp_debug && defined('WP_DEBUG_DISPLAY') && WP_DEBUG_DISPLAY;

        // NOTE: These are not compatible with `switch_to_blog()`.
        // These represent values for the initial/current site.

        if (!($wp_tmp_dir = rtrim(get_temp_dir(), '/'))) {
            throw new Exception('Failed to acquire a temp directory.');
        }
        if (!($wp_salt_key = hash('sha256', wp_salt().$brand['©slug']))) {
            throw new Exception('Failed to generate a unique salt/key.');
        }
        if (!($wp_site_url_option = parse_url(get_option('siteurl')))) {
            throw new Exception('Failed to parse site URL option.');
        }
        if (!($wp_site_default_scheme = $wp_site_url_option['scheme'] ?? 'http')) {
            throw new Exception('Failed to parse site URL option scheme.');
        }
        if ($specs['§type'] === 'plugin') {
            if (!($wp_app_url = parse_url(plugin_dir_url($specs['§file'])))) {
                throw new Exception('Failed to parse plugin dir URL.');
            }
        } elseif ($specs['§type'] === 'theme') {
            if (!($wp_app_url = parse_url(get_template_directory_uri()))) {
                throw new Exception('Failed to parse theme dir URL.');
            }
        } elseif ($specs['§type'] === 'mu-plugin') {
            if (!($wp_app_url = parse_url(site_url('/')))) {
                throw new Exception('Failed to parse site URL.');
            }
        } else { // Unexpected application `§type` in this case.
            throw new Exception('Failed to parse URL for unexpected `§type`.');
        }
        if (!($wp_app_url_host = $wp_app_url['host'] ?? (string) ($_SERVER['HTTP_HOST'] ?? ''))) {
            throw new Exception('Failed to parse app URL host.');
        }
        if (!($wp_app_url_root_host = implode('.', array_slice(explode('.', $wp_app_url_host), -2)))) {
            throw new Exception('Failed to parse app URL root host.');
        }
        $wp_app_url_base_path = rtrim($wp_app_url['path'] ?? '', '/');
        $wp_app_url_base_path .= in_array($specs['§type'], ['theme', 'plugin'], true) ? '/src' : '';
        $wp_app_url_base_path .= '/'; // Always; i.e., this is a directory location.

        # Build the core/default instance base.

        $default_instance_base = [
            '©debug' => [
                '©enable' => $wp_debug,
                '©edge'   => $wp_debug_edge,

                '©log'          => $wp_debug_log,
                '©log_callback' => false, // For extenders.

                '©er_enable'     => false, // WP handles this.
                '©er_display'    => false, // WP handles this.
                '©er_assertions' => false, // Developer must enable.
                // WordPress itself may handle assertions in the future.
            ],
            '©handle_exceptions' => false, // Never in a shared codespace.

            '©di' => [
                '©default_rule' => [
                    'new_instances' => [
                        Classes\SCore\MenuPageForm::class,
                    ],
                ],
            ],

            '©sub_namespace_map' => [
                'SCore' => [
                    '©utils'   => '§',
                    '©facades' => 's',
                ],
            ],

            '§specs' => [
                '§is_pro'          => false,
                '§in_wp'           => false,
                '§is_network_wide' => false,

                '§type' => '',
                '§file' => '',
            ],

            '©brand' => [
                '©name'    => '',
                '©acronym' => '',

                '©text_domain' => '',

                '©slug' => '',
                '©var'  => '',

                '©short_slug' => '',
                '©short_var'  => '',

                '§domain'           => '',
                '§domain_path'      => '',
                '§domain_short_var' => '',

                '§api_domain'           => '',
                '§api_domain_path'      => '',
                '§api_domain_short_var' => '',

                '§cdn_domain'           => '',
                '§cdn_domain_path'      => '',
                '§cdn_domain_short_var' => '',

                '§stats_domain'           => '',
                '§stats_domain_path'      => '',
                '§stats_domain_short_var' => '',
            ],

            '©urls' => [
                '©hosts' => [
                    '©app' => $wp_app_url_host,
                    '©cdn' => 'cdn.'.$wp_app_url_root_host,

                    '©roots' => [
                        '©app' => $wp_app_url_root_host,
                        '©cdn' => $wp_app_url_root_host,
                    ],
                ],
                '©base_paths' => [
                    '©app' => $wp_app_url_base_path,
                    '©cdn' => '/',
                ],
                '©cdn_filter_enable' => false,
                '©default_scheme'    => $wp_site_default_scheme,
                '©sig_key'           => $wp_salt_key,
            ],

            '§setup' => [ // On (or after): `plugins_loaded`.
                // Default is `after_setup_theme` for flexibility.
                '§hook'          => 'after_setup_theme',
                '§hook_priority' => 0, // Very early.

                // Other setup flags.
                '§enable_hooks' => true,

                // Systematic setup flags.
                '§complete' => false,
            ],

            '§database' => [
                '§tables_dir'   => $this->base_dir.'/src/includes/mysql/tables',
                '§indexes_dir'  => $this->base_dir.'/src/includes/mysql/indexes',
                '§triggers_dir' => $this->base_dir.'/src/includes/mysql/triggers',
            ],

            '©fs_paths' => [
                '©logs_dir'                 => $wp_tmp_dir.'/.'.$this::CORE_CONTAINER_SLUG.'/'.$brand['©slug'].'/logs',
                '©cache_dir'                => $wp_tmp_dir.'/.'.$this::CORE_CONTAINER_SLUG.'/'.$brand['©slug'].'/cache',
                '§templates_theme_base_dir' => $this::CORE_CONTAINER_SLUG.'/'.$brand['©slug'],
                '©templates_dir'            => $this->base_dir.'/src/includes/templates',
                '©errors_dir'               => '', '©config_file' => '', // N/A.
            ],

            '©encryption' => [
                '©key' => $wp_salt_key,
            ],
            '©cookies' => [
                '©encryption_key' => $wp_salt_key,
            ],
            '©hash_ids' => [
                '©hash_key' => $wp_salt_key,
            ],
            '©passwords' => [
                '©hash_key' => $wp_salt_key,
            ],

            '§conflicts' => [
                '§plugins' => [
                    /*
                        '[slug]'  => '[name]',
                    */
                ],
                '§themes' => [
                    /*
                        '[slug]'  => '[name]',
                    */
                ],
                '§deactivatable_plugins' => [
                    /*
                        '[slug]'  => '[name]',
                    */
                ],
            ],
            '§dependencies' => [
                '§plugins' => [
                    /*
                        '[slug]' => [
                            'name'        => '',
                            'url'         => '',
                            'archive_url' => '',
                            'in_wp'       => true,
                            'test'        => function(string $slug) {},

                            A test function is optional.
                            A successful test must return nothing.
                            A failed test must return an array with:
                                - `reason`      = One of: `needs-upgrade|needs-downgrade`.
                                - `min_version` = Min version, if `reason=needs-upgrade`.
                                - `max_version` = Max version, if `reason=needs-downgrade`.
                        ],
                    */
                ],
                '§themes' => [
                    /*
                        '[slug]' => [
                            'name'        => '',
                            'url'         => '',
                            'archive_url' => '',
                            'in_wp'       => true,
                            'test'        => function(string $slug) {},

                            A test function is optional.
                            A successful test must return nothing.
                            A failed test must return an array with:
                                - `reason`      = One of: `needs-upgrade|needs-downgrade`.
                                - `min_version` = Min version, if `reason=needs-upgrade`.
                                - `max_version` = Max version, if `reason=needs-downgrade`.
                        ],
                    */
                ],
                '§others' => [
                    /*
                        '[arbitrary key]' => [
                            'name'        => '', // Short plain-text name; i.e., '[name]' Required
                            'description' => '', // Brief rich-text description; i.e., It requires [description].
                            'test'        => function(string $key) {},

                            A test function is required.
                            A successful test must return nothing.
                            A failed test must return an array with:
                                - `how_to_resolve` = Brief rich-text description; i.e., → To resolve, [how_to_resolve].
                                - `cap_to_resolve` = Cap required to satisfy; e.g., `manage_options`.
                        ],
                    */
                ],
            ],

            '§caps' => [
                '§manage' => $wp_is_multisite && $specs['§is_network_wide']
                    ? 'manage_network_plugins' : 'activate_plugins',
            ],

            '§pro_option_keys' => [
                '§license_key' => '', // Pro-only.
            ],
            '§default_options' => [
                '§license_key' => '', // Pro-only.
            ],
            '§options' => [], // Filled automatically (see below).

            '§uninstall' => false, // Perform uninstall?
        ];
        if ($specs['§type'] === 'plugin') {
            $lp_conflicting_name          = $brand['©name'].($specs['§is_pro'] ? ' Lite' : ' Pro');
            $lp_conflicting_basename_slug = preg_replace('/[_\-]+(?:lite|pro)/ui', '', $this->base_dir_basename).($specs['§is_pro'] ? '' : '-pro');

            $default_instance_base['§conflicts']['§plugins'][$lp_conflicting_basename_slug]               = $lp_conflicting_name;
            $default_instance_base['§conflicts']['§deactivatable_plugins'][$lp_conflicting_basename_slug] = $lp_conflicting_name;
        }
        # Build collective instance base & instance, then run parent constructor.

        $instance_base                              = $this->mergeConfig($default_instance_base, $instance_base);
        $instance_base['§specs']                    = &$specs; // Already established (in full) above.
        $instance_base['©brand']                    = &$brand; // Already established (in full) above.
        $instance_base['©fs_paths']['©config_file'] = ''; // Do not allow the use of a config file.
        // Not allowed because we pre-parse config values above to build `§specs` and `©brand`.

        unset($instance['§specs'], $instance['©brand'], $instance['©fs_paths']['©config_file']);
        $instance = apply_filters($brand['©var'].'_instance', $instance, $instance_base);

        parent::__construct($instance_base, $instance, $Parent, $args);

        # Merge site owner options (highest precedence).

        if ($this->Config->§specs['§is_network_wide'] && $wp_is_multisite) {
            if (!is_array($site_owner_options = get_network_option(null, $this->Config->©brand['©var'].'_options'))) {
                update_network_option(null, $this->Config->©brand['©var'].'_options', $site_owner_options = []);
            }
        } elseif (!is_array($site_owner_options = get_option($this->Config->©brand['©var'].'_options'))) {
            update_option($this->Config->©brand['©var'].'_options', $site_owner_options = []);
        }
        $this->Config->§options = $this->s::mergeOptions($this->Config->§default_options, $this->Config->§options);
        $this->Config->§options = $this->s::mergeOptions($this->Config->§options, $site_owner_options);
        $this->Config->§options = $this->s::applyFilters('options', $this->Config->§options);

        # After-construct sub-routines are run now.

        // Sanity check; must be on (or after) `plugins_loaded` hook.
        // When uninstalling, must be on (or after) `init` hook.

        if (!did_action('plugins_loaded')) {
            throw new Exception('`plugins_loaded` action not done yet.');
        } elseif ($this->Config->§uninstall && !did_action('init')) {
            throw new Exception('`init` action not done yet.');
        }
        // Check for any known conflicts.

        if ($this->s::conflictsExist()) {
            return; // Stop here.
        }
        // Check for any unsatisfied dependencies.

        if ($this->s::dependenciesUnsatisfied()) {
            return; // Stop here.
        }
        // No known conflicts; load plugin text-domain.

        load_plugin_textdomain($this->Config->©brand['©text_domain']);

        // The rest of our sub-routines are based on the setup hook.

        if (did_action($this->Config->§setup['§hook'])) {
            $this->onSetup(); // Run setup immediately.
        } else { // Delay setup routines; i.e., attach to hook.
            add_action($this->Config->§setup['§hook'], [$this, 'onSetup'], $this->Config->§setup['§hook_priority']);
        }
    }

    /**
     * Run setup routines.
     *
     * @since 160524 Initial release.
     */
    public function onSetup() // Public hook.
    {
        if ($this->Config->§setup['§complete']) {
            return; // Already complete.
        }
        $this->Config->§setup['§complete'] = true;

        // Maybe setup early hooks.
        // e.g., Install/uninstall hooks.

        if ($this->Config->§setup['§enable_hooks']) {
            $this->onSetupEarlyHooks();
        }
        // Maybe uninstall (and stop here).

        if ($this->Config->§uninstall) {
            $this->s::maybeUninstall();
            return; // Stop here.
        }
        // Maybe install (or reinstall).

        $this->s::maybeInstall();

        // Make global access var available.

        $GLOBALS[$this->Config->©brand['©var']] = $this;

        // Plugin available hook.
        // i.e., Global is available.

        if ($this->Config->§setup['§enable_hooks']) {
            $this->s::doAction('available', $this);
        }
        // Maybe setup other hooks.
        // i.e., Functionality hooks/filters.

        if ($this->Config->§setup['§enable_hooks']) {
            $this->onSetupOtherHooks();
        }
        // Setup fully complete hook.
        // e.g., For theme/plugin add-ons.

        if ($this->Config->§setup['§enable_hooks']) {
            $this->s::doAction('setup_complete', $this);
        }
    }

    /**
     * Early hook setup handler.
     *
     * @since 160524 Initial release.
     *
     * @note Only runs when appropriate.
     */
    protected function onSetupEarlyHooks()
    {
        // Nothing in core at this time.
    }

    /**
     * Other hook setup handler.
     *
     * @since 160524 Initial release.
     *
     * @note Only runs when appropriate.
     */
    protected function onSetupOtherHooks()
    {
        $is_admin = is_admin();

        add_action('wp_loaded', [$this->Utils->§RestAction, 'onWpLoaded']);
        add_action('wp_loaded', [$this->Utils->§TransientShortlink, 'onWpLoaded']);

        if ($is_admin) {
            if ($this->class === self::class) {
                add_action('admin_menu', [$this->Utils->§DbMenuPage, 'onAdminMenu']);
                add_action('admin_enqueue_scripts', [$this->Utils->§StylesScripts, 'enqueueMenuPageLibs']);
            }
            add_filter('admin_body_class', [$this->Utils->§MenuPage, 'onAdminBodyClass']);
            add_action('all_admin_notices', [$this->Utils->§Notices, 'onAllAdminNotices']);
        }
        if ((!$this->Config->§specs['§is_network_wide'] || !is_multisite() || is_main_site()) && in_array($this->Config->§specs['§type'], ['theme', 'plugin'], true)) {
            add_filter('site_transient_update_'.$this->Config->§specs['§type'].'s', [$this->Utils->§Updater, 'onGetSiteTransientUpdate'.$this->Config->§specs['§type'].'s']);
        }
        if ($this->class === self::class) {
            add_action('upgrader_process_complete', [$this->Utils->§Updater, 'onUpgraderProcessComplete']);
        }
    }
}
