<?php session_start();
$is_login  = true;
$org_username   = 'admin';
$org_password   = 'Chimp@404';
$msg            = '';
if( isset( $_GET['logout'] ) && $_GET['logout'] == 'yes' ){
    unset($_SESSION['user_logedin']);
    unset($_SESSION['username']);
    unset($_SESSION['password']);
}

if( isset( $_POST['username'] ) ){
    
    $username   = isset( $_POST['username'] )? $_POST['username'] : '';
    $password   = isset( $_POST['password'] )? $_POST['password'] : '';
    if( $username == $org_username && $password == $org_password ){
        $_SESSION['user_logedin']   = true;
        $_SESSION['username']   = $username;
        $_SESSION['password']   = $password;
        header("Location:index.php");
        exit;
    }else{
        $msg = 'Username or Password is Incorect';
    }
}

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

        <div class="dialog">
            <div class="error" style="color:#ff0000;"><?php echo $msg; ?></div>
            <div class="panel panel-default">
                <p class="panel-heading no-collapse">Sign In</p>
                <div class="panel-body">
                    <form action="" method="POST">
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" class="form-control span12" name="username" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" class="form-control span12 form-control" name="password" required>
                        </div>
                        <input type="submit" class="btn btn-primary pull-right" value="Sign In">
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
        <script src="lib/bootstrap/js/bootstrap.js"></script>
    </body></html>