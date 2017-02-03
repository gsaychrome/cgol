<!DOCTYPE html>
<html lang="en" data-ng-app="Clab2.Application">
<head>
    <base href="/"/>
    <meta charset="utf-8">
    <meta http-equiv="Content-Language" content="hu">
    <title>Game of Life</title>

    <meta name="title" content="Game of life">
    <meta name="keywords" content="game, life">
    <meta name="description" content="">
    <meta name="copyright" content="">

    <!-- htmlbuild:css
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css"/>
    <!-- endbuild -->

    <!-- htmlbuild:remove
    <script src="assets/js/less.js" type="text/javascript"></script>
    <!-- endbuild -->

    <!-- külső függőségek -->
    <!-- htmlbuild:vendor vendor.min.js -->
    <script src="vendor/jquery/dist/jquery.min.js"></script>
    <script src="vendor/angular/angular.js"></script>
    <script src="vendor/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="vendor/angular-bootstrap/ui-bootstrap.min.js"></script>
    <script src="vendor/angular-bootstrap/ui-bootstrap-tpls.min.js"></script>
    <script src="vendor/toastr/toastr.min.js"></script>
    <script src="vendor/angular-toastr/dist/angular-toastr.tpls.min.js"></script>
    <script src="vendor/angular-route/angular-route.min.js"></script>
    <script src="vendor/angular-ui-router/release/angular-ui-router.min.js"></script>
    <script src="vendor/angular-sanitize/angular-sanitize.min.js"></script>
    <script src="vendor/angular-translate/angular-translate.min.js"></script>
    <script src="vendor/angular-translate-loader-static-files/angular-translate-loader-static-files.min.js"></script>
    <script src="vendor/ngDraggable/ngDraggable.js"></script>
    <script src="vendor/angular-animate/angular-animate.min.js"></script>
    <!-- endbuild -->

    <script type="text/javascript" src="https://vjs.zencdn.net/5.10.4/video.js"></script>

    <!-- saját modulok -->
    <script src="config.js.php" type="text/javascript"></script>

    <!-- htmlbuild:app application.min.js -->
    <script src="workbench/clab2-module/dist/clab2-module.js"></script>
    <script src="workbench/clab2-gol-module/dist/clab2-gol-module.js"></script>
    <script src="workbench/clab2-application-module/dist/clab2-application-module.js"></script>
    <!-- endbuild -->

</head>
<body>

<ng-include src="'assets/views/header.html'"></ng-include>

<div class="site" data-ui-view></div>

<ng-include src="'assets/views/footer.html'"></ng-include>

</body>
</html>