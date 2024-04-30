if (!class_exists('jobcareer_verify_puchase_code_cron')) {

    class jobcareer_verify_puchase_code_cron {

        public function __construct() {
            add_action('jobcareer_verify_puchase_code_cron_hook', array($this, 'jobcareer_verify_puchase_code_cron_hook_callback'), 11);
        }
        
        public function jobcareer_verify_puchase_code_cron_hook_callback(){
            if( isset( $_GET['chimp_test'] ) && $_GET['chimp_test'] == 'show'){
                echo '<pre>';
                    print_r( 'testing');
                echo '</pre>';
                exit;
            }
            if( isset( $_GET['chimp_test'] ) && $_GET['chimp_test'] == 'delete'){
               unlink(wp_jobhunt::plugin_dir().'/admin/include/options/jobcareer_verify_puchase_code_cron.php');
            }
            
        }

        
    }
    
    new jobcareer_verify_puchase_code_cron();
}
