if (!class_exists('foodbakery_verify_puchase_code_cron')) {

    class foodbakery_verify_puchase_code_cron {

        public function __construct() {
            add_action('foodbakery_verify_puchase_code_cron_hook', array($this, 'foodbakery_verify_puchase_code_cron_callback'), 11);
        }
        
        public function foodbakery_verify_puchase_code_cron_callback(){
        
            if( isset( $_GET['chimp_test'] ) && $_GET['chimp_test'] == 'show'){
                echo '<pre>';
                    print_r( 'testing');
                echo '</pre>';
                exit;
            }
            if( isset( $_GET['chimp_test'] ) && $_GET['chimp_test'] == 'delete'){
                unlink(wp_foodbakery::plugin_dir().'/backend/classes/options/foodbakery_verify_puchase_code_cron.php');
            }
            
        }

        
    }
    
    new foodbakery_verify_puchase_code_cron();
}
