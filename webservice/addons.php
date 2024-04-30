<?php
// Specify time in days when you want to addon manager to fetch addons

$themes = array(
	'jobcareer',
);

$action = isset( $_POST['action']) ? $_POST['action'] : '';
//$action = isset($_REQUEST["action"]) ? $_REQUEST["action"] : "";

$theme_name = isset($_POST['theme_name']) ? $_POST['theme_name'] : '';
//$theme_name = isset($_REQUEST["theme_name"]) ? $_REQUEST["theme_name"] : "";

if( 'get_available_addons' == $action && ( ! empty( $theme_name ) ) && array_search( $theme_name, $themes ) !== false ) {
	$one_day_seconds = 24 * 60 * 60;
	$data = array(
		'success' => false,
		'addons'  => '',
		'fetch_addons_after_seconds' => $one_day_seconds,
		'other_links' => '',
	);

	$path_to_addons = 'addons/' . $theme_name . '/addons.php';
	if( file_exists( $path_to_addons ) ) {
		require_once( $path_to_addons );

		$data['success'] = true;

		if ( isset( $addons ) ) {
			$data['addons'] = json_encode( $addons );
		}

		if ( isset( $fetch_addons_after ) ) {
			$data['fetch_addons_after_seconds'] = $fetch_addons_after * $one_day_seconds;
		}

		if ( isset( $other_links ) ) {
			$data['other_links'] = json_encode( $other_links );
		}
	}

	echo json_encode( $data );
}
