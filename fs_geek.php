<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.facebook.com/moicsmarkez
 * @since             1.0.0
 * @package           Fs_geek
 *
 * @wordpress-plugin
 * Plugin Name:       Fastpring GeekandGuards
 * Plugin URI:        geekyguards.com
 * Description:       Paga con tarjeta de credito, paypal, amazon, etc... en Woocommerce.
 * Version:           1.0.45
 * Author:            moicsmarkez
 * Author URI:        https://www.facebook.com/moicsmarkez
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       fs_geek
 * Domain Path:       /languages
 * GRACIAS A:         El desarrollo de https://enradia.com/ que fue de mucha ayuda!
 */
// If this file is called directly, abort. 
if (!defined('WPINC')) {
    die;
}
 
/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('FS_GEEK_VERSION', '1.0.48');
define('GEEK_WC_FASTSPRING_LIB', 'https://d1f8f9xcsvx3ha.cloudfront.net/sbl/0.7.5/fastspring-builder.min.js'); //libreria fastspring
define('GEEK_BASE_MAIN_FILE', __FILE__); // ruta base del plugin
define('PLUGIN_MIN_PHP_VER', '5.6.0');
define('PLUGIN_MIN_WC_VER', '3.0.0');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-fs_geek-activator.php
 */
function activate_fs_geek() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-fs_geek-activator.php';
    Fs_geek_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-fs_geek-deactivator.php
 */
function deactivate_fs_geek() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-fs_geek-deactivator.php';
    Fs_geek_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_fs_geek');
register_deactivation_hook(__FILE__, 'deactivate_fs_geek');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-fs_geek.php';

add_action('plugins_loaded', 'iniciar_clase_pasarela', 11);

function iniciar_clase_pasarela() {
    if (!class_exists('WC_Payment_Gateway')) {
        return;
    } else {
        require_once plugin_dir_path(__FILE__) . 'includes/class-fs_geek-gateway.php';
    }
    require_once plugin_dir_path(__FILE__) . 'includes/class-fs_geek-manejador.php';
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_fs_geek() {

    $plugin = Fs_geek::get_instance();
    $plugin->run();
}

run_fs_geek();



