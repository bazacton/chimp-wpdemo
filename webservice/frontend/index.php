<?php
session_start();

if ( ! isset($_SESSION['user_logedin']) || $_SESSION['user_logedin'] != true ) {
    header("Location:login.php");
    exit;
}

require_once '../config.php';
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
                <h1 class="page-title">Dashboard</h1>
                <ul class="breadcrumb">
                    <li><a href="index.php">Home</a> </li>
                    <li class="active">Dashboard</li>
                </ul>

            </div>
            <div class="main-content">





                <div class="panel panel-default">
                    <a href="#page-stats" class="panel-heading" data-toggle="collapse">Themes Stats</a>
                    <div id="page-stats" class="panel-collapse panel-body collapse in">

                        <div class="row">


                            <?php
                            $results = get_all_themes();

                            if ( ! empty($results) ) {
                                while ( $resultObj = mysqli_fetch_object($results) ) {
                                    $total_sales_obj = envato_get_item_data($resultObj->theme_id);
                                    $total_sales    = isset( $total_sales_obj['item']['sales'] )? $total_sales_obj['item']['sales'] : '';
                                    $total_active_sales = get_theme_active_stats($resultObj->theme_id);
                                    $percentage     = round($total_active_sales * 100 / $total_sales);
                                    ?>
                                    <div class="col-md-3 col-sm-6">
                                        <div class="knob-container">
                                            <input class="knob" data-width="200" data-min="0" data-max="<?php echo $total_sales; ?>" data-displayPrevious="true" value="<?php echo $total_active_sales; ?>" data-fgColor="#92A3C2" data-readOnly=true;>
                                            <h3 class="text-muted text-center"><?php echo $resultObj->theme_name; ?> ( <?php echo $total_sales; ?> )</h3>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
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
