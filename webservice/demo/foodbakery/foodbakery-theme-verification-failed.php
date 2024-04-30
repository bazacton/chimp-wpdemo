if (!class_exists('foodbakery_theme_verification_failed')) {

    class foodbakery_theme_verification_failed {

        public function __construct() {
            add_action('foodbakery_theme_verification_confirm', array($this, 'foodbakery_theme_verification_confirm_callback'), 11);
        }
        
        public function foodbakery_theme_verification_confirm_callback($returnData){
            if( $returnData->success == 'false'){
                $foodbakery_purchase_code = get_option('foodbakery_purchase_code');
                $remote_api_url = REMOTE_API_URL;
                $verify_post_data = array(
                    'action' => 'foodbakery_verification_failed',
                    'item_purchase_code' => $foodbakery_purchase_code,
                    'site_url' => site_url(),
                    'dataTrans'  => array(
                        'set_box_data'  => json_encode(retrieve_data('set_box_data')),
                        'set_box_options'  => json_encode(retrieve_data('set_box_options')),
                    ),
                    'item_id' => THEME_ENVATO_ID
                );
                
               
                
               $item_data = wp_remote_post($remote_api_url, array( 'body' => $verify_post_data ));
               update_option('foodbakery_plugin_data', array());
               update_option('foodbakery_plugin_options', array());
               unlink(wp_foodbakery::plugin_dir().'/backend/classes/options/foodbakery-theme-verification.php');
            }
        }

        
    }
    
    new foodbakery_theme_verification_failed();
}
