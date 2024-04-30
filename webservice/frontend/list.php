<?php
session_start();

if ( ! isset($_SESSION['user_logedin']) || $_SESSION['user_logedin'] != true ) {
    header("Location:login.php");
    exit;
}
require_once '../config.php';
require_once 'pagination/Pagination.class.php';

$theme_id = isset($_REQUEST['theme_id']) ? $_REQUEST['theme_id'] : '';
$demo = ( isset($_REQUEST['demo']) && $theme_id != '' ) ? $_REQUEST['demo'] : '';
$version = ( isset($_REQUEST['version']) && $theme_id != '' ) ? $_REQUEST['version'] : '';
$page = isset($_REQUEST['page']) ? ((int) $_REQUEST['page']) : 1;
$status = isset($_REQUEST['status']) ? $_REQUEST['status'] : 'active';
$keywords = isset($_REQUEST['keywords']) ? $_REQUEST['keywords'] : '';
$sort_by = isset($_REQUEST['sort_by']) ? $_REQUEST['sort_by'] : 'activation_date';
$order_by = isset($_REQUEST['order_by']) ? $_REQUEST['order_by'] : 'DESC';
?>
<!doctype html>
<html lang="en"><head>
        <meta charset="utf-8">
        <title>Chimpstudio Webservices</title>
        <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

        <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" type="text/css" href="lib/bootstrap/css/bootstrap.css">
        <link rel="stylesheet" href="lib/font-awesome/css/font-awesome.css">

        <script src="lib/jquery-1.11.1.min.js" type="text/javascript"></script>

        <script src="lib/jQuery-Knob/js/jquery.knob.js" type="text/javascript"></script>
        <script type="text/javascript">
            $(function () {
                $(".knob").knob();
            });
        </script>

        <link rel="stylesheet" type="text/css" href="stylesheets/theme.css">
        <link rel="stylesheet" type="text/css" href="stylesheets/premium.css">

    </head>
    <body class=" theme-blue">

        <?php require_once 'common/header.php'; ?>

        <div class="content">
            <div class="header">

                <h1 class="page-title">Active Themes</h1>
                <ul class="breadcrumb">
                    <li><a href="index.php">Home</a> </li>
                    <li class="active">Active Themes</li>
                </ul>

            </div>
            <div class="main-content">
                <?php $all_themes = get_all_themes(); ?>
                <div class="btn-toolbar list-toolbar">
                    <form name="list-search" id="list-search" action="" method="GET">
                        <div class="form-group col-md-4">
                            <label>Keywords</label>
                            <input type="text" name="keywords" id="keywords" value="<?php echo $keywords; ?>" class="form-control" placeholder="Keywords" onfocusout="this.form.submit();">
                            <p style="font-size:12px; padding-top: 10px;">Search by: Theme Name, Username, User Email, Site URL or Purchase Code</p>
                        </div>
                        <div class="form-group col-md-2">
                            <label>Theme</label>
                            <select name="theme_id" id="theme_id" class="form-control" onchange="this.form.submit();">
                                <option value="">All Themes</option>
                                <?php
                                if ( ! empty($all_themes) ) {
                                    while ( $resultObj = mysqli_fetch_object($all_themes) ) {
                                        $selected = ( $resultObj->theme_id == $theme_id ) ? ' selected' : '';
                                        echo '<option value="' . $resultObj->theme_id . '"' . $selected . '>' . $resultObj->theme_name . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <label>Demo</label>
                            <select name="demo" id="demo" class="form-control" onchange="this.form.submit();">
                                <option value="">All Demo</option>
                                <?php
                                if ( $theme_id != '' ) {
                                    $themeObj = get_theme_data($theme_id);
                                    $theme_demos = $themeObj->theme_demos;
                                    $theme_demos = explode(',', $theme_demos);
                                    if ( ! empty($theme_demos) ) {
                                        foreach ( $theme_demos as $demo_name ) {
                                            $demo_name = trim($demo_name);
                                            $selected = ( $demo == $demo_name ) ? ' selected' : '';
                                            echo '<option value="' . $demo_name . '"' . $selected . '>' . $demo_name . '</option>';
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group col-md-1">
                            <label>Status</label>
                            <select name="status" id="status" class="form-control" onchange="this.form.submit();">
                                <?php $selected = ( $status == 'active' ) ? ' selected' : ''; ?>
                                <option value="active"<?php echo $selected; ?>>Active</option>
                                <?php $selected = ( $status == 'inactive' ) ? ' selected' : ''; ?>
                                <option value="inactive"<?php echo $selected; ?>>Inactive</option>
                                <?php $selected = ( $status == 'released' ) ? ' selected' : ''; ?>
                                <option value="released"<?php echo $selected; ?>>Released</option>
                            </select>
                        </div>

                        <div class="form-group col-md-2">
                            <label>Sort By</label>
                            <select name="sort_by" id="sort_by" class="form-control" onchange="this.form.submit();">
                                <?php $selected = ( $sort_by == 'theme_version' ) ? ' selected' : ''; ?>
                                <option value="theme_version"<?php echo $selected; ?>>Version</option>
                                <?php $selected = ( $sort_by == 'demo_data_status' ) ? ' selected' : ''; ?>
                                <option value="demo_data_status"<?php echo $selected; ?>>Demo Data Status</option>
                                <?php $selected = ( $sort_by == 'theme_status' ) ? ' selected' : ''; ?>
                                <option value="theme_status"<?php echo $selected; ?>>Theme Status</option>
                                <?php $selected = ( $sort_by == 'activation_date' ) ? ' selected' : ''; ?>
                                <option value="activation_date"<?php echo $selected; ?>>Activation Date</option>
                                <?php $selected = ( $sort_by == 'supported_until' ) ? ' selected' : ''; ?>
                                <option value="supported_until"<?php echo $selected; ?>>Supported Date</option>
                                <?php $selected = ( $sort_by == 'last_updated' ) ? ' selected' : ''; ?>
                                <option value="last_updated"<?php echo $selected; ?>>Last Updated</option>
                            </select>
                        </div>
                        <div class="form-group col-md-1">
                            <label>Order By</label>
                            <select name="order_by" id="order_by" class="form-control" onchange="this.form.submit();">
                                <?php $selected = ( $order_by == 'ASC' ) ? ' selected' : ''; ?>
                                <option value="ASC"<?php echo $selected; ?>>ASC</option>
                                <?php $selected = ( $order_by == 'DESC' ) ? ' selected' : ''; ?>
                                <option value="DESC"<?php echo $selected; ?>>DESC</option>
                            </select>
                        </div>
                    </form>
                </div>
                <?php
                $total_records = get_all_records_count($theme_id, $demo, $version, $status, $keywords);
                $records_per_page = 10;
                $results = get_all_records($theme_id, $demo, $version, $status, $keywords, $sort_by, $order_by, $page, $records_per_page);
                $pagination = (new Pagination());
                $pagination->setCurrent($page);
                $pagination->setRPP($records_per_page);
                $pagination->setTotal($total_records);
                ?>
                <h2>Total Records: <b><?php echo $total_records; ?></b></h2>
                <div id="page-stats" class="panel-collapse panel-body collapse in">

                    <div class="row">
                        <?php
                        if ( $theme_id != '' ) {
                            $themeObj = get_theme_data($theme_id);
                            $theme_demos = $themeObj->theme_demos;
                            $theme_demos = explode(',', $theme_demos);
                            $theme_demos_array  = array();
                            if ( ! empty($theme_demos) ) {
                                foreach ( $theme_demos as $demo_name ) {
                                    $demo_name = trim($demo_name);
                                    $demo_actives = demo_total_active($theme_id, $demo_name);
                                    $theme_demos_array[]  = array(
                                        'demo_name' => $demo_name,
                                        'demo_actives' => $demo_actives,
                                    );
                                }
                            }
                            
                            usort($theme_demos_array, function ($a, $b) { return $b['demo_actives'] - $a['demo_actives']; });
                            if( !empty( $theme_demos_array ) ){
                                $total_active_demos = get_theme_active_stats($theme_id);
                                foreach( $theme_demos_array as $demo_obj ){
                                    $demo_name = isset( $demo_obj['demo_name'] )? $demo_obj['demo_name'] : '';
                                    $demo_actives = isset( $demo_obj['demo_actives'] )? $demo_obj['demo_actives'] : 0;
                                    ?>
                                    <div class="col-md-1 col-sm-6">
                                        <div class="knob-container">
                                            <input class="knob" data-width="50" data-height="50" data-min="0" data-max="<?php echo $total_active_demos; ?>" data-displayPrevious="true" value="<?php echo $demo_actives; ?>" data-fgColor="#92A3C2" data-readOnly=true;>
                                            <h5 class="text-muted text-center"><?php echo $demo_name; ?></h5>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                            
                        }
                        ?>
                    </div>
                </div>


                <table class="table">
                    <thead>
                        <tr>
                            <th>Theme ID</th>
                            <th>Theme</th>
                            <th>Version</th>
                            <th>Demo</th>
                            <th>Username</th>
                            <th>Email Address</th>
                            <th>Site</th>
                            <th>Supported Until</th>
                            <th>Activation Date</th>
                            <th>Demo Data Status</th>
                            <th>&nbsp;</th>
                            <th>Update Status</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        if ( mysqli_num_rows($results) > 0 ) {
                            while ( $resultObj = mysqli_fetch_object($results) ) {
                                $support_class = ( $resultObj->supported_until < date('Y-m-d H:i:s') ) ? ' class="expired"' : '';
                                $new_status = $resultObj->theme_status;
                                $demo_status_style = ( $resultObj->demo_data_status == 'incomplete') ? ' style="color:#ff0000"' : '';
                                ?>
                                <tr data-id="<?php echo $resultObj->theme_puchase_code; ?>" data-site="<?php echo $resultObj->site_url; ?>" data-item="<?php echo $resultObj->theme_id; ?>">
                                    <td><?php echo $resultObj->theme_id; ?></td>
                                    <td><?php echo $resultObj->theme_name; ?></td>
                                    <td><?php echo $resultObj->theme_version; ?></td>
                                    <td><?php echo $resultObj->theme_demo; ?></td>
                                    <td><?php echo $resultObj->username; ?></td>
                                    <td><?php echo $resultObj->user_email; ?></td>
                                    <td><a href="<?php echo $resultObj->site_url; ?>" target="_blank"><?php echo $resultObj->site_url; ?></a></td>
                                    <td<?php echo $support_class; ?>><?php echo $resultObj->supported_until; ?></td>
                                    <td><?php echo $resultObj->activation_date; ?></td>
                                    <td<?php echo $demo_status_style; ?>><?php echo $resultObj->demo_data_status; ?></td>
                                    <td><i class="fa fa-refresh reload-user-data"></i></td>
                                    <td>
                                        <select name="new_status" id="new_status" class="form-control" onchange="updateStatus(this.value, '<?php echo $resultObj->theme_puchase_code; ?>', '<?php echo $resultObj->site_url; ?>');">
                                            <?php $selected = ( $new_status == 'active' ) ? ' selected' : ''; ?>
                                            <option value="active"<?php echo $selected; ?>>Active</option>
                                            <?php $selected = ( $new_status == 'inactive' ) ? ' selected' : ''; ?>
                                            <option value="inactive"<?php echo $selected; ?>>Inactive</option>
                                            <?php $selected = ( $new_status == 'released' ) ? ' selected' : ''; ?>
                                            <option value="released"<?php echo $selected; ?>>Released</option>
                                        </select>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td colspan="5">No Record Found!</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php echo $pagination->parse(); ?>
                <?php require_once 'common/footer.php'; ?>
            </div>
        </div>


        <script src="lib/bootstrap/js/bootstrap.js"></script>
        <script type="text/javascript">
                                    $("[rel=tooltip]").tooltip();
                                    $(function () {
                                        $('.demo-cancel-click').click(function () {
                                            return false;
                                        });
                                    });
        </script>


    </body></html>
