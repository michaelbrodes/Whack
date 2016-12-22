<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Whack!</title>
    <meta charset="UTF-8" />
    <meta name="viewport"
          content="width=device-width, initial-scale=1, maximum-scale=1"
    />

    <link rel="icon" href="/assets/images/mole.png" type="image/png">

    <link rel="stylesheet"
          href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u"
          crossorigin="anonymous" />

    <link rel="stylesheet"
          href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css"
          integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp"
          crossorigin="anonymous" />
    <link rel="stylesheet" href="/bower_components/csspin/csspin.css">
    <link rel="stylesheet" href="/assets/css/site.css" />
    <!-- required for html5mode in /assets/js/whack.js -->
    <base href="/" />
</head>
<body ng-app="whack">
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed"
                    data-toggle="collapse"
                    data-target="#menu"
                    aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#/">Whack</a>
        </div>
        <div class="collapse navbar-collapse" id="menu">
            <ul class="nav navbar-nav navbar-right" id="links">
                <li><a href="#/">Home</a></li>
                <!-- these will be filled in dynamically when the user logs on -->
                <li id="play-button"><a href="#/play"></a></li>
                <li class="vert-divide"><span class="center">|</span></li>
                <li id="login"><span class="center"></span></li>
                <li id="logout"><a href="/whack/management/logout.php"></a></li>
            </ul>
        </div>
    </div>
</nav>
<!-- rendered by /assets/js/whack.js/ -->
<div class="panel main-panel panel-default">
   <div class="panel-body">
       <noscript class="danger">
           It looks like you have JavaScript disabled. JavaScript is the core to
           functionality of this site, so, unfortunately, the site won't work
           without it.
           If you are afraid of malicious code, you can look at
           <a href="https://github.com/michaelbrodes/whack">
               the source code
           </a>.
           Sorry for the inconvenience!
       </noscript>
       <main id="site-content" ng-view></main>
   </div>
</div>
<script
    src="https://code.jquery.com/jquery-2.2.4.min.js"
    integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="
    crossorigin="anonymous"></script>
<script
    src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"
    integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU="
    crossorigin="anonymous"></script>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.7/angular.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.5.7/angular-route.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
        integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
        crossorigin="anonymous">
</script>
<script src="/assets/js/whack.js"></script>
</body>
</html>