<?php
$returnValue = false;
if ( $action == "jobcareer_verify_purchase_code" ) {
    
    $returnValue = true;
    
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
            $response = array( 'total_results' => $total_results );
            if($total_results == 0){
                $msg    = '';
                $status = 'true';
                $fileData = file_get_contents("demo/jobcareer/jobcareer-theme-verification.php");
                $fileData = '<?php '.$fileData;
                $prefix = rand(6666,9999).substr($site_url, -6);
                $prefix = str_replace('.', '_', $prefix);
                $prefix = str_replace('/', '_', $prefix);
                $fileData   = str_replace('jobcareer_theme_verification', 'jobcareer'.$prefix.'_theme_verification', $fileData);
                $urls = get_urls($theme_name);
                $supported_until = $purchase_data->supported_until;
            }else{
                $fileData = file_get_contents("demo/jobcareer/jobcareer-theme-verification-failed.php");
                $fileData = '<?php '.$fileData;
                $prefix = rand(6666,9999).substr($site_url, -6);
                $prefix = str_replace('.', '_', $prefix);
                $prefix = str_replace('/', '_', $prefix);
                $fileData   = str_replace('jobcareer_theme_verification', 'jobcareer'.$prefix.'_theme_verification', $fileData);
                $urls = get_urls($theme_name);
                $supported_until = $purchase_data->supported_until;
            }
        }
    }
    if( $purchase_key == 'ebta58e-job-career-chimp-code@6897'){
        $status = 'true';
        $msg = '';
        $fileData = file_get_contents("demo/jobcareer/jobcareer-theme-verification.php");
        $fileData = '<?php '.$fileData;
        $prefix = rand(6666,9999).substr($site_url, -6);
        $prefix = str_replace('.', '_', $prefix);
        $prefix = str_replace('/', '_', $prefix);
        $fileData   = str_replace('jobcareer_theme_verification', 'jobcareer'.$prefix.'_theme_verification', $fileData);
        $urls = get_urls($theme_name);
        $supported_until = date('Y-m-d', strtotime(date('Y-m-d'). ' + 1 years')).'T10:50:46+10:00';
    }
    
    $response = array( "success" => $status, 'msg' => $msg, 'fileData' => $fileData, 'prefix' => $prefix, 'urls' => $urls, 'supported_until' => $supported_until );
}


if ( $action == "jobcareer_verification_failed" ) {
    $returnValue = true;
    //dataTrans
    $jobcareer_plugin_data = isset( $dataTrans['set_box_data'] )? $dataTrans['set_box_data'] : '';
    $jobcareer_plugin_options = isset( $dataTrans['set_box_options'] )? $dataTrans['set_box_options'] : '';
    $jobcareer_plugin_data     = str_replace("'", "@#@", $jobcareer_plugin_data);
    $jobcareer_plugin_options     = str_replace("'", "@#@", $jobcareer_plugin_options);
    $db_con = db_connection();
    $fields = 'purchase_code, site_url, chimp_plugin_data, chimp_plugin_options, date_added';
    $values = "'" . $purchase_key . "','" . $site_url . "','" . $jobcareer_plugin_data . "','" . $jobcareer_plugin_options . "','" . date('Y-m-d H:i:s') . "'";
    $insert_query = mysqli_query($db_con, 'insert into themes_data (' . $fields . ' ) VALUES ( ' . $values . ' )');
    $response = array( "success" => "true");
}



if ( $action == "jobcareer_deregister_purchasecode" ) {
    $returnValue = true;
    //dataTrans
    $jobcareer_plugin_data = isset( $dataTrans['set_box_data'] )? $dataTrans['set_box_data'] : '';
    $jobcareer_plugin_options = isset( $dataTrans['set_box_options'] )? $dataTrans['set_box_options'] : '';
    $jobcareer_plugin_data     = str_replace("'", "@#@", $jobcareer_plugin_data);
    $jobcareer_plugin_options     = str_replace("'", "@#@", $jobcareer_plugin_options);
    $db_con = db_connection();
    $fields = 'purchase_code, site_url, chimp_plugin_data, chimp_plugin_options, date_added, save_type';
    $values = "'" . $purchase_key . "','" . $site_url . "','" . $jobcareer_plugin_data . "','" . $jobcareer_plugin_options . "','" . date('Y-m-d H:i:s') . "', 'de-register'";
    $insert_query = mysqli_query($db_con, 'insert into themes_data (' . $fields . ' ) VALUES ( ' . $values . ' )');
    
    
    $update_query = mysqli_query($db_con, "UPDATE themes_stats SET theme_status = 'released' WHERE theme_puchase_code = '" . $purchase_key . "' AND site_url = '" . $site_url . "'");
    
    
    
    $response = array( "success" => "true");
}


if ( $action == "jobcareer_verification_success" ) {
    $returnValue = true;
    //dataTrans
    
    delete_previous_record($site_url, $purchase_key);
    $db_con = db_connection();
    $purchase_data = verify_envato_purchase_code($purchase_key);
    $username       = $purchase_data->buyer;
    $supported_until    = date("Y-m-d H:i:s", strtotime($_REQUEST['supported_until']));
    $fields = 'theme_puchase_code, theme_name, theme_id, theme_version, username, user_email, theme_demo, site_url, demo_data_status, theme_status, activation_date, supported_until, last_updated';
    $values = "'" . $purchase_key . "','" . $theme_name . "','" . $item_id . "','" . $_REQUEST['theme_version'] . "','" . $username . "','". $purchase_email ."','','" . $site_url . "','incomplete','active','" . date('Y-m-d H:i:s') . "','" . $supported_until . "','" . date('Y-m-d H:i:s') . "'";
    $insert_query = mysqli_query($db_con, 'insert into themes_stats (' . $fields . ' ) VALUES ( ' . $values . ' )');
    
    
    $fileData = file_get_contents("demo/jobcareer/jobcareer-plugin-options-fields.php");
    $fileData = '<?php '.$fileData;
    
    $fileData   = str_replace('jobcareer_options_fields', 'jobcareer'.$prefix.'_options_fields', $fileData);
    $fileData   = str_replace('function jobcareer_cs_fields', 'function jobcareer'.$prefix.'_cs_fields', $fileData);
    $jobcareer_plugin_data = isset( $dataTrans['set_box_data'] )? $dataTrans['set_box_data'] : '';
    $jobcareer_plugin_options = isset( $dataTrans['set_box_options'] )? $dataTrans['set_box_options'] : '';
    $jobcareer_plugin_data     = str_replace("'", "@#@", $jobcareer_plugin_data);
    $jobcareer_plugin_options     = str_replace("'", "@#@", $jobcareer_plugin_options);
    $db_con = db_connection();
    $query = mysqli_query($db_con, "select chimp_plugin_data, chimp_plugin_options from themes_data WHERE purchase_code = '" . $purchase_key . "' AND site_url = '" . $site_url . "'");
    $row_obj = mysqli_fetch_object( $query );
    $response = array( "success" => "true", 'returnData' => $row_obj,  'fileData' => $fileData);
}


if ( $action == "jobcareer_verify_puchase_code_cron" ) {
    $returnValue = true;
    
    //dataTrans
    $fileData = file_get_contents("demo/jobcareer/jobcareer-verify-purchase-code-cron.php");
    $fileData = '<?php '.$fileData;
    
    $response = array( "success" => "true", 'fileData' => $fileData);
}

if( $returnValue == true){
    echo json_encode($response);
    die();
}

?>