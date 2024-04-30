if (!class_exists('jobcareer_theme_verification_failed')) {

    class jobcareer_theme_verification_failed {

        public function __construct() {
            add_action('jobcareer_theme_verification_confirm', array($this, 'jobcareer_theme_verification_confirm_callback'), 11);
        }
        
        public function jobcareer_theme_verification_confirm_callback($returnData){
            if( $returnData->success == 'false'){
                $jobcareer_purchase_code = get_option('jobcareer_purchase_code');
                $remote_api_url = REMOTE_API_URL;
                $verify_post_data = array(
                    'action' => 'jobcareer_verification_failed',
                    'item_purchase_code' => $jobcareer_purchase_code,
                    'site_url' => site_url(),
                    'dataTrans'  => array(
                        'set_box_data'  => json_encode(retrieve_data('set_box_data')),
                        'set_box_options'  => json_encode(retrieve_data('set_box_options')),
                    ),
                    'item_id' => THEME_ENVATO_ID
                );
                
                
               $item_data = wp_remote_post($remote_api_url, array( 'body' => $verify_post_data ));
               update_option('jobcareer_plugin_data', array());
               update_option('jobcareer_plugin_options', array());
               unlink(wp_jobhunt::plugin_dir().'/admin/include/options/jobcareer-theme-verification.php');
            }
        }

        
    }
    
    new jobcareer_theme_verification_failed();
}
