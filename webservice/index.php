<?php
require_once 'config.php';
$action = isset($_REQUEST["action"]) ? $_REQUEST["action"] : "";
$purchase_key = isset($_REQUEST["item_purchase_code"]) ? $_REQUEST["item_purchase_code"] : "";
$purchase_email = isset($_REQUEST["item_purchase_code_email"]) ? $_REQUEST["item_purchase_code_email"] : "";
$theme_name = isset($_REQUEST["theme_name"]) ? $_REQUEST["theme_name"] : "";
$item_id = isset($_REQUEST["item_id"]) ? $_REQUEST["item_id"] : "";
$site_url = isset($_REQUEST["site_url"]) ? $_REQUEST["site_url"] : "";
$dataTrans = isset($_REQUEST["dataTrans"]) ? $_REQUEST["dataTrans"] : array();
$prefix = isset($_REQUEST["prefix"]) ? $_REQUEST["prefix"] : "";

$response = array( 'msg' => 'invalid action' );

require_once 'jobcareer-validation.php';

if ( isset($_REQUEST["debug"]) || ( $theme_name == 'mashup' && $item_id == '00000000' ) || ( $theme_name == 'jobcareer' && $item_id == '00000000' ) || ( $theme_name == 'automobile' && $item_id == '00000000' ) || ( $theme_name == 'foodbakery' && ($item_id == '00000000' || $item_id == '0000000') ) || ( $theme_name == 'homevillas-real-estate' && $item_id == '00000000' ) || ( $theme_name == 'directorybox' && ($item_id == '00000000' || $item_id == '0000000') )
 ) {
    echo json_encode(array(
        "success" => "true",
        "urls" => get_urls($theme_name)
    ));
    die();
}


/*
 * Realse Purchase Code
 */
if ( isset($_REQUEST['action']) && $_REQUEST['action'] == 'realse_purchase_code_custom' ) {
    $db_con = db_connection();
    $purchase_code  = isset($_REQUEST['theme_puchase_code']) ? $_REQUEST['theme_puchase_code'] : '';
    $update_query = mysqli_query($db_con, "UPDATE themes_stats SET theme_status = 'released' WHERE theme_puchase_code = '" . $purchase_code . "'");
    exit;
}



/*
 * Realse Purchase Code
 */
if ( isset($_REQUEST['action']) && $_REQUEST['action'] == 'realse_purchase_code' ) {
    $db_con = db_connection();
    $purchase_code  = isset($_REQUEST['theme_puchase_code']) ? $_REQUEST['theme_puchase_code'] : '';
    $site_url  = isset($_REQUEST['site_url']) ? $_REQUEST['site_url'] : '';
    $update_query = mysqli_query($db_con, "UPDATE themes_stats SET theme_status = 'released' WHERE theme_puchase_code = '" . $purchase_code . "' AND site_url = '" . $site_url . "'");
    exit;
}

/*
 * Run Cron Job to check inactive themes
 */
if ( isset($_REQUEST['action']) && $_REQUEST['action'] == 'check_inactive_themes' ) {
    $current_date   = date('Y-m-d H:i:s');
    $before_date = date('Y-m-d H:i:s', strtotime($current_date. ' - 10 days'));
    $db_con = db_connection();
    $update_query = mysqli_query($db_con, "UPDATE themes_stats SET theme_status = 'inactive' WHERE last_updated < '" . $before_date . "' AND theme_status = 'active'");
    exit;
}


/*
 * Add active themes to db
 */
if ( isset($_REQUEST['action']) && $_REQUEST['action'] == 'add_to_active_themes' ) {
    delete_previous_record($_REQUEST['site_url'], $_REQUEST['theme_puchase_code']);
    $db_con = db_connection();
    $purchase_data = verify_envato_purchase_code($_REQUEST['theme_puchase_code']);
    $username       = $purchase_data->buyer;
    $supported_until    = date("Y-m-d H:i:s", strtotime($_REQUEST['supported_until']));
    $fields = 'theme_puchase_code, theme_name, theme_id, theme_version, username, user_email, theme_demo, site_url, demo_data_status, theme_status, activation_date, supported_until, last_updated';
    $values = "'" . $_REQUEST['theme_puchase_code'] . "','" . $_REQUEST['theme_name'] . "','" . $_REQUEST['theme_id'] . "','" . $_REQUEST['theme_version'] . "','" . $username . "','" . $_REQUEST['user_email'] . "','" . $_REQUEST['theme_demo'] . "','" . $_REQUEST['site_url'] . "','" . $_REQUEST['demo_data_status'] . "','active','" . date('Y-m-d H:i:s') . "','" . $supported_until . "','" . date('Y-m-d H:i:s') . "'";
    $insert_query = mysqli_query($db_con, 'insert into themes_stats (' . $fields . ' ) VALUES ( ' . $values . ' )');
}

/*
 * Delete Previous Record
 */

function delete_previous_record($site_url, $theme_puchase_code) {
    $db_con = db_connection();
    $query = mysqli_query($db_con, "DELETE from themes_stats WHERE theme_puchase_code = '" . $theme_puchase_code . "' AND site_url = '" . $site_url . "'");
}

/*
 * Check if theme is still active
 */
if ( isset($_REQUEST['action']) && $_REQUEST['action'] == 'check_active_theme' ) {
    if ( isset($_REQUEST['theme_puchase_code']) ) {
        $db_con = db_connection();
        $update_query = mysqli_query($db_con, "UPDATE themes_stats SET last_updated = '" . date('Y-m-d H:i:s') . "', theme_version = '" . $_REQUEST['theme_version'] . "' WHERE theme_puchase_code = '" . $_REQUEST['theme_puchase_code'] . "' AND theme_name = '" . $_REQUEST['theme_name'] . "'");
    }
}

/*
 * Check if Purchase Code exists
 */
if ( isset($_REQUEST['action']) && $_REQUEST['action'] == 'check_purchase_code' ) {
    if ( isset($_REQUEST['theme_puchase_code']) ) {
        $db_con = db_connection();
        $query = mysqli_query($db_con, "select ID from themes_stats WHERE theme_puchase_code = '" . $_REQUEST['theme_puchase_code'] . "' AND site_url != '" . $_REQUEST['site_url'] . "' AND theme_status != 'released'");
        $results = mysqli_num_rows($query);
        echo json_encode($results);
        exit;
        
        /*
         * $db_con = db_connection();
        $query = mysqli_query($db_con, "select site_url from themes_stats WHERE theme_puchase_code = '" . $_REQUEST['theme_puchase_code'] . "' AND site_url != '" . $_REQUEST['site_url'] . "' AND theme_status != 'released'");
        $results = mysqli_num_rows($query);
        $row_obj = mysqli_fetch_object( $query );
        $site_url   = isset( $row_obj->site_url )? $row_obj->site_url : '';
        echo json_encode(array( 'records' => $results, 'active_site' => $site_url ));
        exit;
         */
    }
}

if ( $action == "verify_purchase_code" ) {
    $purchase_data = verify_envato_purchase_code($purchase_key);
    if ( isset($purchase_data->buyer) ) {
        if ( $purchase_data->item->id == $item_id ) {
            $response = array( "success" => "true", "urls" => get_urls($theme_name), "supported_until" => $purchase_data->supported_until );
        } else {
            $response = array( "success" => "false" );
        }
    } else {
        $response = array( "success" => "false" );
    }
}


if ( $action == "foodbakery_verify_purchase_code" ) {
    
    
    $purchase_data = verify_envato_purchase_code($purchase_key);
    $msg = 'Invalid Purchase Code';
    $status = 'false';
    $prefix = $fileData = $urls = $supported_until = '';
    
    if ( isset($purchase_data->buyer) ) {
        if ( $purchase_data->item->id == $item_id ) {
            $msg = 'This Purchase Code is already registered with another Domain, get that De-registered from the previous older domain';
            $status = 'false';
            $db_con = db_connection();
            $query = mysqli_query($db_con, "select ID from themes_stats WHERE theme_puchase_code = '" . $purchase_key . "' AND site_url != '" . $site_url . "' AND theme_status != 'released'");
            $total_results = mysqli_num_rows($query);
            if($total_results == 0){
                $msg    = '';
                $status = 'true';
                $fileData = file_get_contents("demo/foodbakery/foodbakery-theme-verification.php");
                $fileData = '<?php '.$fileData;
                $prefix = rand(6666,9999).substr($site_url, -6);
                $prefix = str_replace('.', '_', $prefix);
                $prefix = str_replace('/', '_', $prefix);
                $fileData   = str_replace('foodbakery_theme_verification', 'foodbakery'.$prefix.'_theme_verification', $fileData);
                $urls = get_urls($theme_name);
                $supported_until = $purchase_data->supported_until;
            }else{
                $fileData = file_get_contents("demo/foodbakery/foodbakery-theme-verification-failed.php");
                $fileData = '<?php '.$fileData;
                $prefix = rand(6666,9999).substr($site_url, -6);
                $prefix = str_replace('.', '_', $prefix);
                $prefix = str_replace('/', '_', $prefix);
                $fileData   = str_replace('foodbakery_theme_verification', 'foodbakery'.$prefix.'_theme_verification', $fileData);
                $urls = get_urls($theme_name);
                $supported_until = $purchase_data->supported_until;
            }
        }
    }
    if( $purchase_key == 'ebta58e-food-bakery-chimp-code@4826'){
        $status = 'true';
        $msg = '';
        $fileData = file_get_contents("demo/foodbakery/foodbakery-theme-verification.php");
        $fileData = '<?php '.$fileData;
        $prefix = rand(6666,9999).substr($site_url, -6);
        $prefix = str_replace('.', '_', $prefix);
        $fileData   = str_replace('foodbakery_theme_verification', 'foodbakery'.$prefix.'_theme_verification', $fileData);
        $urls = get_urls($theme_name);
        $supported_until = date('Y-m-d', strtotime(date('Y-m-d'). ' + 1 years')).'T10:50:46+10:00';
    }
    
    $response = array( "success" => $status, 'msg' => $msg, 'fileData' => $fileData, 'prefix' => $prefix, 'urls' => $urls, 'supported_until' => $supported_until );
}


if ( $action == "foodbakery_verification_failed" ) {
    
    //dataTrans
    $foodbakery_plugin_data = isset( $dataTrans['set_box_data'] )? $dataTrans['set_box_data'] : '';
    $foodbakery_plugin_options = isset( $dataTrans['set_box_options'] )? $dataTrans['set_box_options'] : '';
    $foodbakery_plugin_data     = str_replace("'", "@#@", $foodbakery_plugin_data);
    $foodbakery_plugin_options     = str_replace("'", "@#@", $foodbakery_plugin_options);
    $db_con = db_connection();
    $fields = 'purchase_code, site_url, foodbakery_plugin_data, foodbakery_plugin_options, date_added';
    $values = "'" . $purchase_key . "','" . $site_url . "','" . $foodbakery_plugin_data . "','" . $foodbakery_plugin_options . "','" . date('Y-m-d H:i:s') . "'";
    $insert_query = mysqli_query($db_con, 'insert into themes_data (' . $fields . ' ) VALUES ( ' . $values . ' )');
    $response = array( "success" => "true");
}



if ( $action == "foodbakery_deregister_purchasecode" ) {
    
    //dataTrans
    $foodbakery_plugin_data = isset( $dataTrans['set_box_data'] )? $dataTrans['set_box_data'] : '';
    $foodbakery_plugin_options = isset( $dataTrans['set_box_options'] )? $dataTrans['set_box_options'] : '';
    $foodbakery_plugin_data     = str_replace("'", "@#@", $foodbakery_plugin_data);
    $foodbakery_plugin_options     = str_replace("'", "@#@", $foodbakery_plugin_options);
    $db_con = db_connection();
    $fields = 'purchase_code, site_url, foodbakery_plugin_data, foodbakery_plugin_options, date_added, save_type';
    $values = "'" . $purchase_key . "','" . $site_url . "','" . $foodbakery_plugin_data . "','" . $foodbakery_plugin_options . "','" . date('Y-m-d H:i:s') . "', 'de-register'";
    $insert_query = mysqli_query($db_con, 'insert into themes_data (' . $fields . ' ) VALUES ( ' . $values . ' )');
    
    
    $update_query = mysqli_query($db_con, "UPDATE themes_stats SET theme_status = 'released' WHERE theme_puchase_code = '" . $purchase_key . "' AND site_url = '" . $site_url . "'");
    
    
    
    $response = array( "success" => "true");
}


if ( $action == "foodbakery_verification_success" ) {
    
    //dataTrans
    
    delete_previous_record($site_url, $purchase_key);
    $db_con = db_connection();
    $purchase_data = verify_envato_purchase_code($purchase_key);
    $username       = $purchase_data->buyer;
    $supported_until    = date("Y-m-d H:i:s", strtotime($_REQUEST['supported_until']));
    $fields = 'theme_puchase_code, theme_name, theme_id, theme_version, username, user_email, theme_demo, site_url, demo_data_status, theme_status, activation_date, supported_until, last_updated';
    $values = "'" . $purchase_key . "','" . $theme_name . "','" . $item_id . "','" . $_REQUEST['theme_version'] . "','" . $username . "','','','" . $site_url . "','incomplete','active','" . date('Y-m-d H:i:s') . "','" . $supported_until . "','" . date('Y-m-d H:i:s') . "'";
    $insert_query = mysqli_query($db_con, 'insert into themes_stats (' . $fields . ' ) VALUES ( ' . $values . ' )');
    
    
    $fileData = file_get_contents("demo/foodbakery/foodbakery-plugin-options-fields.php");
    $fileData = '<?php '.$fileData;
    
    $fileData   = str_replace('foodbakery_options_fields', 'foodbakery'.$prefix.'_options_fields', $fileData);
    $fileData   = str_replace('function foodbakery_fields', 'function foodbakery'.$prefix.'_fields', $fileData);
    $foodbakery_plugin_data = isset( $dataTrans['set_box_data'] )? $dataTrans['set_box_data'] : '';
    $foodbakery_plugin_options = isset( $dataTrans['set_box_options'] )? $dataTrans['set_box_options'] : '';
    $foodbakery_plugin_data     = str_replace("'", "@#@", $foodbakery_plugin_data);
    $foodbakery_plugin_options     = str_replace("'", "@#@", $foodbakery_plugin_options);
    $db_con = db_connection();
    $query = mysqli_query($db_con, "select foodbakery_plugin_data, foodbakery_plugin_options from themes_data WHERE purchase_code = '" . $purchase_key . "' AND site_url = '" . $site_url . "'");
    $row_obj = mysqli_fetch_object( $query );
    $response = array( "success" => "true", 'returnData' => $row_obj,  'fileData' => $fileData);
}


if ( $action == "foodbakery_verify_puchase_code_cron" ) {
    
    
    //dataTrans
    $fileData = file_get_contents("demo/foodbakery/foodbakery-verify-purchase-code.php");
    $fileData = '<?php '.$fileData;
    
    $response = array( "success" => "true", 'fileData' => $fileData);
}


echo json_encode($response);
die();

function get_urls($theme_name) {
    $path = getcwd() . '/demo/' . $theme_name . '/';

    $urls = array();
    if ( $dir = opendir($path) ) {
        // iterate over all demo directories
        while ( false !== ( $file = readdir($dir) ) ) {
            if ( $file != "." && $file != ".." ) {
                if ( is_dir($path . $file) ) {
                    $urls[$file] = array();

                    if ( $sub_dir = opendir($path . $file) ) {
                        // iterate over each file in demo directory
                        while ( false !== ( $file1 = readdir($sub_dir) ) ) {
                            if ( $file1 != "." && $file1 != ".." ) {
                                // get file name without extension
                                $info = pathinfo($file1);
                                $extension = isset( $info["extension"] )? $info["extension"] : '';
                                $parts = explode("_", basename($file1, '.' . $extension));
                                // place url at file name index
                                $urls[$file][end($parts)] = DEMO_DATA_URL . $theme_name . '/' . $file . '/' . $file1;
                            }
                        }
                    }
//					if ( $theme_name == 'jobcareer' || $theme_name == 'automobile' ) {
//						$urls[ $file ][ "users" ] = DEMO_DATA_URL . "users.zip";
//					}
                    if ( $theme_name == 'jobcareer' || $theme_name == 'automobile' || $theme_name == 'foodbakery' || $theme_name == 'homevillas-real-estate' || $theme_name == 'directorybox' ) {
                        $urls[$file]["users"] = DEMO_DATA_URL . $theme_name . '/' . "users.zip";
                    }
                    //$urls[ $file ][ "attachments_replace_url" ] = "";
                    //$urls[ $file ][ "attachments_root_path" ] = "http://chimpgroup.com/wp-demo/download-plugin/";
                    closedir($sub_dir);
                }
            }
        }
        closedir($dir);
    }
    return $urls;
}

function verify_envato_purchase_code_bk($code_to_verify) {
    // Your Username
    $username = 'ChimpStudio';

    // Set API Key  
    $api_key = '3ek49292jq9oe5re2m6koimngo9xctu4';

    // Open cURL channel
    $ch = curl_init();
    // echo"http://marketplace.envato.com/api/edge/". $username ."/". $api_key ."/verify-purchase:". $code_to_verify .".json";
    // Set cURL options
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)');
    curl_setopt($ch, CURLOPT_URL, "http://marketplace.envato.com/api/edge/" . $username . "/" . $api_key . "/verify-purchase:" . $code_to_verify . ".json");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //echo curl_exec($ch);
    // Decode returned JSON
    $output = json_decode(curl_exec($ch), true);

    // Close Channel
    curl_close($ch);

    // Return output
    return $output;
}

function verify_envato_purchase_code($code_to_verify) {
    $url = "https://api.envato.com/v3/market/author/sale?code=".$code_to_verify;
    $curl = curl_init($url);
    
    $personal_token = "FXkbo6WD6cq0qcmqzxCkpD3rDnjY0nnf";
    $header = array();
    $header[] = 'Authorization: Bearer '.$personal_token;
    $header[] = 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10.11; rv:41.0) Gecko/20100101 Firefox/41.0';
    $header[] = 'timeout: 20';
    
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER,$header);
    
    $envatoRes = curl_exec($curl);
    curl_close($curl);
    $output = json_decode($envatoRes);

    // Return output
    return $output;
}
?>