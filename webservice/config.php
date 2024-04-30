<?php

define('WEBSERVICES_URL', 'http://chimpgroup.com/wp-demo/webservice/');
//define('WEBSERVICES_URL', 'http://localhost/webservice/');
define('DEMO_DATA_URL', WEBSERVICES_URL . 'demo/');
define('DEMO_IMAGES_URL', WEBSERVICES_URL . 'demo_images/');

function db_connection() {
    $servername = "localhost";
    $username = "group_webservice";
    $password = "T*BlN(POXt-2";
    $connection = mysqli_connect($servername, $username, $password, "group_webservice");
    /*
    $username = "root";
    $password = "";
    $connection = mysqli_connect($servername, $username, $password, "webservices");*/
    return $connection;
}

/*
 * Get All Theme
 */

function get_all_themes() {
    $db_con = db_connection();
    $results = mysqli_query($db_con, "select * from themes");
    return $results;
}

/*
 * Get Theme Data
 */

function get_theme_data($theme_id) {
    $db_con = db_connection();
    $results = mysqli_query($db_con, "select * from themes where theme_id = '" . $theme_id . "'");
    return mysqli_fetch_object($results);
}

/*
 * Get Theme Data
 */

function demo_total_active($theme_id, $demo_name) {
    $db_con = db_connection();
    $results = mysqli_query($db_con, "select * from themes_stats where theme_id = '" . $theme_id . "' AND theme_demo = '" . $demo_name . "' AND theme_status = 'active'");
    return mysqli_num_rows($results);
}

/*
 * Get Theme Active States
 */

function get_theme_active_stats($theme_id) {
    $db_con = db_connection();
    $results = mysqli_query($db_con, "select * from themes_stats where theme_id = '" . $theme_id . "' AND theme_status = 'active'");
    return mysqli_num_rows($results);
}

/*
 * Update User details
 */

function update_user_details($data_array = array()) {
    $db_con = db_connection();

    $results = mysqli_query($db_con, "select * from themes_stats where theme_puchase_code = '" . $data_array->theme_puchase_code . "'");
    $resultObj = mysqli_fetch_object($results);
    $theme_status = ( $resultObj->theme_name == $data_array->theme_name ) ? 'active' : 'inactive';
    $data_array->supported_until = date("Y-m-d H:i:s", strtotime($data_array->supported_until));

    $label_fields = "theme_puchase_code = '" . $data_array->theme_puchase_code . "', ";
    if ( $resultObj->theme_name == $data_array->theme_name ) {
        $label_fields .= "theme_name = '" . $data_array->theme_name . "', ";
        $label_fields .= "theme_id = '" . $data_array->theme_id . "', ";
        $label_fields .= "theme_version = '" . $data_array->theme_version . "', ";
        $label_fields .= "supported_until = '" . $data_array->supported_until . "', ";
        $label_fields .= "last_updated = '" . date('Y-m-d H:i:s') . "', ";
    }
    $label_fields .= "theme_status = '" . $theme_status . "'";
    $update_query = mysqli_query($db_con, "UPDATE themes_stats SET " . $label_fields . " WHERE theme_puchase_code = '" . $data_array->theme_puchase_code . "'");
}

/*
 * Count All records according to query
 */

function get_all_records_count($theme_id = '', $demo_name = '', $version = '', $status = 'active', $keywords = '') {
    $db_con = db_connection();
    $query_builder = ( $theme_id != '' ) ? " AND theme_id = '" . $theme_id . "'" : "";
    $query_builder .= ( $demo_name != '' ) ? " AND theme_demo = '" . $demo_name . "'" : "";
    $query_builder .= ( $version != '' ) ? " AND theme_version = '" . $version . "'" : "";
    $query_builder .= ( $keywords != '' ) ? " AND (theme_puchase_code = '" . $keywords . "' "
            . "OR username = '" . $keywords . "'"
            . " OR theme_name = '" . $keywords . "'"
            . " OR user_email = '" . $keywords . "'"
            . " OR site_url = '" . $keywords . "')" : "";
    $results = mysqli_query($db_con, "select COUNT(ID) as total_records from themes_stats where theme_status = '" . $status . "'" . $query_builder);
    return mysqli_fetch_object($results)->total_records;
}

/*
 * Get Theme Active Sales
 */

function get_all_records($theme_id = '', $demo_name = '', $version = '', $status = 'active', $keywords = '', $sort_by, $order_by, $page = 1, $records_per_page = 10) {
    $db_con = db_connection();
    $page = $page - 1;
    $page = $records_per_page * $page;
    $query_builder = ( $theme_id != '' ) ? " AND theme_id = '" . $theme_id . "'" : "";
    $query_builder .= ( $demo_name != '' ) ? " AND theme_demo = '" . $demo_name . "'" : "";
    $query_builder .= ( $version != '' ) ? " AND theme_version = '" . $version . "'" : "";
     $query_builder .= ( $keywords != '' ) ? " AND (theme_puchase_code = '" . $keywords . "' "
            . "OR username = '" . $keywords . "'"
            . " OR theme_name = '" . $keywords . "'"
            . " OR user_email = '" . $keywords . "'"
            . " OR site_url = '" . $keywords . "')" : "";
    $query_builder .=" ORDER BY " . $sort_by . " " . $order_by . " limit " . $page . ", " . $records_per_page . "";
    $results = mysqli_query($db_con, "select * from themes_stats where theme_status = '" . $status . "'" . $query_builder);
    return $results;
}

/*
 * API Request
 */

function api_get_request($url) {

    $curl_handle = curl_init();
    curl_setopt($curl_handle, CURLOPT_URL, $url);
    curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
    $buffer = curl_exec($curl_handle);
    curl_close($curl_handle);
    return $buffer;
}

if ( isset($_REQUEST['action']) && $_REQUEST['action'] == 'get_user_data' ) {
    $item_purchase_code = $_REQUEST['item_purchase_code'];
    $item_id = $_REQUEST['item_id'];
    $site_url = $_REQUEST['site_url'];
    $responseData = api_get_request($site_url . '/wp-admin/admin-ajax.php?action=reaload_user_data');
    $responseData = json_decode($responseData);
    $responseData = (array) $responseData;
    $item_data = api_get_request(WEBSERVICES_URL . 'index.php?action=verify_purchase_code&item_purchase_code=' . $item_purchase_code . '&item_id=' . $item_id);
    $item_data = json_decode($item_data);
    $responseData['supported_until'] = $item_data->supported_until;
    $responseData = (object) $responseData;
    update_user_details($responseData);
    echo json_encode(array( 'type' => 'success' ));
    exit;
}

/*
 * Update Theme Status
 */
if ( isset($_REQUEST['action']) && $_REQUEST['action'] == 'update_theme_status' ) {
    $db_con = db_connection();
    $purchase_code  = isset($_REQUEST['theme_puchase_code']) ? $_REQUEST['theme_puchase_code'] : '';
    $site_url  = isset($_REQUEST['site_url']) ? $_REQUEST['site_url'] : '';
    $status  = isset($_REQUEST['status']) ? $_REQUEST['status'] : '';
    $update_query = mysqli_query($db_con, "UPDATE themes_stats SET theme_status = '" . $status . "' WHERE theme_puchase_code = '" . $purchase_code . "' AND site_url = '" . $site_url . "'");
    exit;
}

function envato_get_item_data($item_id) {
    // Your Username
    $username = 'ChimpStudio';

    // Set API Key  
    $api_key = '3ek49292jq9oe5re2m6koimngo9xctu4';

    // Open cURL channel
    $ch = curl_init();
    // echo"http://marketplace.envato.com/api/edge/". $username ."/". $api_key ."/verify-purchase:". $code_to_verify .".json";
    // Set cURL options
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)');
    curl_setopt($ch, CURLOPT_URL, "http://marketplace.envato.com/api/edge/item:" . $item_id . ".json");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //echo curl_exec($ch);
    // Decode returned JSON
    $output = json_decode(curl_exec($ch), true);

    // Close Channel
    curl_close($ch);

    // Return output
    return $output;
}

?>