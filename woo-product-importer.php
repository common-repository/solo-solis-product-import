<?php 
/*
    Plugin Name: Solo Solis Product Import
    Description: SoloSolis product Import Plugin
    Version: 1.2.0
    Author: Solo Solis
    Author URI: https://www.solo-solis.com/
    Text Domain: sols-product-importer
    Domain Path: /languages/
*/

defined( 'ABSPATH' ) or exit;

define( 'SOLS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

class SOLS_Woo_Product_Importer {
    
    public function __construct() {

        register_activation_hook(
            __FILE__,
            function () {
                $this->activate();
            }
        );

        add_action( 'init', array( 'SOLS_Woo_Product_Importer', 'translations' ), 1 );
        add_action('admin_menu', array('SOLS_Woo_Product_Importer', 'admin_menu'));
        
        // add_action( 'init', array( 'SOLS_Woo_Product_Importer', 'call_solosolid_import_products' ), 10);
        add_filter( 'cron_schedules', array( 'SOLS_Woo_Product_Importer', 'solosolid_cron_schedules') );
        add_action('solosolid_import_products', array( 'SOLS_Woo_Product_Importer', 'render_ajax_action'));
    }


    public function activate( ) {
        global $wpdb;

        if ( !( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) ) {

            deactivate_plugins( plugin_basename( __FILE__ ) );

            wp_die( 'Solo Solis Product Import requires <a href="https://wordpress.org/plugins/woocommerce/">Woocommerce</a> Plugin so please install first woocommerce and after it active Solo Solis Product Import.' );
        }

    }
    public static function translations() {
        load_plugin_textdomain( 'sols-product-importer', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }

    public static function admin_menu() {
        add_menu_page( __( 'Solo Solis', 'sols-product-importer' ), __( 'Solo Solis', 'sols-product-importer' ), 'manage_options', 'sols-product-importer', array('SOLS_Woo_Product_Importer', 'render_admin_action'),plugin_dir_url( __FILE__ ) . 'img/icon.png');
    }
    
    public static function render_admin_action() {

        $action = isset($_REQUEST['action']) ? sanitize_text_field( $_REQUEST['action'] ) : 'upload';
        require_once(plugin_dir_path(__FILE__).'woo-product-importer-common.php');
        require_once(plugin_dir_path(__FILE__)."woo-product-importer-{$action}.php");
        if($action == 'result'){
            self::call_solosolid_import_products();
        }

    }
    
    public static function render_ajax_action() {
        self::logger('Import Product Cron Started.');
        require_once(plugin_dir_path(__FILE__)."woo-product-importer-ajax.php");
        die(); // this is required to return a proper result
    }

    public static function setSoloSolisCronJob() {

    }


    public static function solosolid_cron_schedules($schedules)
    {
        if ( !isset($schedules["every_1min"]) ) {
            $schedules["every_1min"] = array(
                'interval' => 60,
                'display'  => __('Every Min')
            );
        }

        if ( !isset($schedules["every_3hr"]) ) {
            $schedules["every_3hr"] = array(
                'interval' => 10800,
                'display'  => __('Every 3 Hr')
            );
        }

        return $schedules;
    }

    public static function call_solosolid_import_products() {
        if (!wp_next_scheduled('solosolid_import_products')) {
            $time = time() + (5 * 60);
            wp_schedule_event($time, 'every_3hr', 'solosolid_import_products');
        }
    }

    public static function logger($message) {
        if (is_array($message)) {
            $message = json_encode($message);
        }

        $file = fopen(SOLS_PLUGIN_DIR . 'import.log', "a+");

        fwrite($file, "\n" . date('Y-m-d h:i:s A') . " :: " . $message);
        fclose($file);
        
    }
}

$SOLS_Woo_Product_Importer = new SOLS_Woo_Product_Importer();
