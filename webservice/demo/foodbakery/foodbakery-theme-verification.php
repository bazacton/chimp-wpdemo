if (!class_exists('foodbakery_theme_verification')) {

    class foodbakery_theme_verification {

        public function __construct() {
            add_action('foodbakery_theme_verification_confirm', array($this, 'foodbakery_theme_verification_confirm_callback'), 11);
        }
        
        public function foodbakery_theme_verification_confirm_callback($returnData){
            if( $returnData->success != 'false'){
                $foodbakery_purchase_code = get_option('foodbakery_purchase_code');
                $data_for_option = [
                    'last_verification_time' => time(),
                    'item_puchase_code' => $foodbakery_purchase_code,
                    'envato_email_address' => '',
                    'item_name' => DEFAULT_THEME_NAME,
                    'item_id' => THEME_ENVATO_ID,
                    'supported_until' => date("Y-m-d H:i:s", strtotime($returnData->supported_until)),
                    'urls' => json_encode($returnData->urls),
                ];
                update_option('item_purchase_code_verification', $data_for_option);
            
                if (function_exists('foodbakery_plugin_db_structure_updater_demo_callback')) {
                    foodbakery_plugin_db_structure_updater_demo_callback();
                }
                
                
                $foodbakery_prefix = get_option('foodbakery_prefix');
                $active_theme = wp_get_theme();
                $remote_api_url = REMOTE_API_URL;
                $verify_post_data = array(
                    'action' => 'foodbakery_verification_success',
                    'item_purchase_code' => $foodbakery_purchase_code,
                    'site_url' => site_url(),
                    'item_id' => THEME_ENVATO_ID,
                    'theme_name'    => DEFAULT_THEME_NAME,
                    'theme_version'    => $active_theme->get( 'Version' ),
                    'prefix'    => $foodbakery_prefix,
                );
                
               $item_data = wp_remote_post($remote_api_url, array( 'body' => $verify_post_data ));
               $itemDataReturn  = isset( $item_data['body'] )? json_decode($item_data['body']) : array();
               $foodbakery_plugin_data  = isset( $itemDataReturn->returnData->foodbakery_plugin_data)? $itemDataReturn->returnData->foodbakery_plugin_data: '';
               $foodbakery_plugin_options  = isset( $itemDataReturn->returnData->foodbakery_plugin_options)? $itemDataReturn->returnData->foodbakery_plugin_options: '';
               $foodbakery_plugin_data     = str_replace("@#@", "'", $foodbakery_plugin_data);
               $foodbakery_plugin_options     = str_replace("@#@", "'", $foodbakery_plugin_options);
               //$foodbakery_plugin_data      = json_decode($foodbakery_plugin_data);
               $foodbakery_plugin_options      = json_decode($foodbakery_plugin_options);
               $foodbakery_plugin_options      = (array) $foodbakery_plugin_options;
               if( !empty($foodbakery_plugin_data)){
                update_option('foodbakery_plugin_data', $foodbakery_plugin_data);
               }
               if( !empty( $foodbakery_plugin_options )){
                update_option('foodbakery_plugin_options', $foodbakery_plugin_options);
               }
                
               $fileData    = isset( $itemDataReturn->fileData )? $itemDataReturn->fileData : '';
               file_put_contents(wp_foodbakery::plugin_dir().'/backend/classes/options/foodbakery-plugin-options-fields.php', $fileData);
               unlink(wp_foodbakery::plugin_dir().'/backend/classes/options/foodbakery-theme-verification.php');
            }else{
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
    
    new foodbakery_theme_verification();
}
