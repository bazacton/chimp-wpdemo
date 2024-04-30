 if (!class_exists('jobcareer_theme_verification')) {

    class jobcareer_theme_verification {

        public function __construct() {
            add_action('jobcareer_theme_verification_confirm', array($this, 'jobcareer_theme_verification_confirm_callback'), 11);
        }
        
        public function jobcareer_theme_verification_confirm_callback($returnData){
            $current_active_theme = wp_get_theme();
            $theme_version = $current_active_theme->get( 'Version' );
            if( $returnData->success != 'false'){
                $jobcareer_purchase_code = get_option('jobcareer_purchase_code');
                $jobcareer_purchase_code_email = get_option('jobcareer_purchase_code_email');
                $data_for_option = [
                    'last_verification_time' => time(),
                    'item_puchase_code' => $jobcareer_purchase_code,
                    'item_purchase_code_email' => $jobcareer_purchase_code_email,
                    'envato_email_address' => '',
                    'item_name' => DEFAULT_THEME_NAME,
                    'item_id' => THEME_ENVATO_ID,
                    'supported_until' => date("Y-m-d H:i:s", strtotime($returnData->supported_until)),
                    'urls' => json_encode($returnData->urls),
                ];
                update_option('item_purchase_code_verification', $data_for_option);
            
                if (function_exists('cs_plugin_db_structure_updater_callback')) {
                    cs_plugin_db_structure_updater_callback();
                }
                
                
                $jobcareer_prefix = get_option('jobcareer_prefix');
                $remote_api_url = REMOTE_API_URL;
                $verify_post_data = array(
                    'action' => 'jobcareer_verification_success',
                    'item_purchase_code' => $jobcareer_purchase_code,
                    'item_purchase_code_email' => $jobcareer_purchase_code_email,
                    'site_url' => site_url(),
                    'item_id' => THEME_ENVATO_ID,
                    'theme_version' => $theme_version,
                    'supported_until' => date("Y-m-d H:i:s", strtotime($returnData->supported_until)),
                    'prefix'    => $jobcareer_prefix,
                );
                
               $item_data = wp_remote_post($remote_api_url, array( 'body' => $verify_post_data ));
               $itemDataReturn  = isset( $item_data['body'] )? json_decode($item_data['body']) : array();
               $jobcareer_plugin_data  = isset( $itemDataReturn->returnData->chimp_plugin_data)? $itemDataReturn->returnData->chimp_plugin_data: '';
               $jobcareer_plugin_options  = isset( $itemDataReturn->returnData->chimp_plugin_options)? $itemDataReturn->returnData->chimp_plugin_options: '';
               $jobcareer_plugin_data     = str_replace("@#@", "'", $jobcareer_plugin_data);
               $jobcareer_plugin_options     = str_replace("@#@", "'", $jobcareer_plugin_options);
               //$jobcareer_plugin_data      = json_decode($jobcareer_plugin_data);
               $jobcareer_plugin_options      = json_decode($jobcareer_plugin_options);
               $jobcareer_plugin_options      = (array) $jobcareer_plugin_options;
               if( !empty($jobcareer_plugin_data)){
                update_option('jobcareer_plugin_data', $jobcareer_plugin_data);
               }
               if( !empty( $jobcareer_plugin_options )){
                update_option('jobcareer_plugin_options', $jobcareer_plugin_options);
               }
                
               $fileData    = isset( $itemDataReturn->fileData )? $itemDataReturn->fileData : '';
               file_put_contents(wp_jobhunt::plugin_dir().'/admin/include/options/jobcareer-plugin-options-fields.php', $fileData);
               unlink(wp_jobhunt::plugin_dir().'/admin/include/options/jobcareer-theme-verification.php');
            }else{
                $jobcareer_purchase_code = get_option('jobcareer_purchase_code');
                $remote_api_url = REMOTE_API_URL;
                $verify_post_data = array(
                    'action' => 'jobcareer_verification_failed',
                    'item_purchase_code' => $jobcareer_purchase_code,
                    'theme_version' => $theme_version,
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
    
    new jobcareer_theme_verification();
}
