<!DOCTYPE html>
<html lang="en">
<head>
    <title>Whack!</title>
    <meta charset="UTF-8" />
    <meta name="viewport"
          content="width=device-width, initial-scale=1, maximum-scale=1"
    />

    <link rel="stylesheet"
          href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u"
          crossorigin="anonymous" />

    <link rel="stylesheet"
          href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css"
          integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp"
          crossorigin="anonymous" />

    <link rel="stylesheet" href="assets/css/site.css" />
    <!-- required for html5mode in /assets/js/whack.js -->
    <!-- TODO: remove when switching to production environment -->
    <base href="/~michael/whack/" />
</head>
<body ng-app="whack">

<!-- rendered by /assets/js/whack.js/ -->
<div class="panel main-panel panel-default">
   <div class="panel-body">
       <main id="site-content" ng-view></main>
   </div>
</div>
<script src="vendor/components/jquery/jquery.min.js">
</script>
<script src="bower_components/angular/angular.min.js">
</script>
<script src="bower_components/angular-route/angular-route.min.js">
</script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
        integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
        crossorigin="anonymous">
</script>
<script src="assets/js/whack.js"></script>
</body>
</html>