<?php

if (!defined('ABSPATH')) {
    exit;
}
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.facebook.com/moicsmarkez
 * @since      1.0.0
 *
 * @package    Fs_geek
 * @subpackage Fs_geek/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Fs_geek
 * @subpackage Fs_geek/includes
 * @author     moicsmarkez <moicsmarkez@gmail.com>
 */
class Fs_geek {

    /**
     * @var Singleton The reference the *Singleton* instance of this class
     */
    private static $instance;

    /**
     * Returns the *Singleton* instance of this class.
     *
     * @return Singleton The *Singleton* instance.
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Fs_geek_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * @var array Plugin $options
     */
    private static $options;

    /**
     * @var Reference to logging class.
     */
    private static $log;

    /**
     * Private clone method to prevent cloning of the instance of the
     * *Singleton* instance.
     *
     * @return void
     */
    private function __clone() {
        
    }

    /**
     * Private unserialize method to prevent unserializing of the *Singleton*
     * instance.
     *
     * @return void
     */
    private function __wakeup() {
        
    }

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    protected function __construct() {
        self::$options = get_option('woocommerce_geekyguards_fastspring_settings', array());
        add_action('init', array($this, 'on_product_type'));

        if (defined('FS_GEEK_VERSION')) {
            $this->version = FS_GEEK_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'fs_geek';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
    }


    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Fs_geek_Loader. Orchestrates the hooks of the plugin.
     * - Fs_geek_i18n. Defines internationalization functionality.
     * - Fs_geek_Admin. Defines all hooks for the admin area.
     * - Fs_geek_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-fs_geek-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-fs_geek-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-fs_geek-admin.php';

        $this->loader = new Fs_geek_Loader();
    }

    /* private function carga_manejador() {
      $manejador = new WC_Gateway_FastSpring_Handler();
      } */

    public static function log($message) {

        // Static function so we need to get options another way
        $options = get_option('woocommerce_geekyguards_fastspring_settings', array());

        if ($options['logging'] || defined('WP_DEBUG') && WP_DEBUG) {
            if (empty(self::$log)) {
                self::$log = new WC_Logger();
            }
            self::$log->add('woocommerce-gateway-fastspring', $message);
        }
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Fs_geek_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {

        $plugin_i18n = new Fs_geek_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    public function on_product_type() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-fs_geeK-product-type.php';
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {

        $plugin_admin = new Fs_geek_Admin($this->get_plugin_name(), $this->get_version());
        //$plugin_admin->compatibilidad_de_entorno();

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('woocommerce_product_options_general_product_data', $plugin_admin, 'fs_geek_woocommerce_product_write_panels');
        $this->loader->add_filter('woocommerce_payment_gateways', $plugin_admin, 'add_gateways');
        $this->loader->add_filter('script_loader_tag', $plugin_admin, 'modificar_etiqueta_scripts', 20, 2);
        $this->loader->add_filter('product_type_selector', $plugin_admin, 'geekyguard_product_type');
        $this->loader->add_filter('woocommerce_product_class', $plugin_admin, 'geekyguard_fs_product_class', 10, 2);
        $this->loader->add_action('woocommerce_process_product_meta_fastspring_product', $plugin_admin, 'save_producto_fs_geek');
        $this->loader->add_action('admin_notices', $plugin_admin, 'admin_notices');
        $this->loader->add_filter( 'plugin_action_links_'.plugin_basename(GEEK_BASE_MAIN_FILE), $plugin_admin, 'plugin_vinculo_opciones');
        $this->loader->add_action('wp_ajax_fastspring_product_update', $plugin_admin, 'get_action_fs_geek_product');
    }

    /**
     * Fetch plugin option
     *
     * @param $o Option key
     * @return mixed option value
     */
    static public function get_fs_option($o) {
        return isset(self::$options[$o]) ? (self::$options[$o] === 'yes' ? true : (self::$options[$o] === 'no' ? false : self::$options[$o])) : null;
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Fs_geek_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

}
