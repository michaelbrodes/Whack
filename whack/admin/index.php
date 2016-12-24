<?php
namespace whack\admin;
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require 'admin.inc.php';
use whack\data\Account;
use whack\data\Phrase;
session_start();
# so we can identify the user
$identifier = isset($_SESSION['usr-id'])? $_SESSION['usr-id']: $_COOKIE['remember'];
if ( $identifier == null || !Account::verifyCookie($identifier) )
{
    header('Location: /#/error/forbidden/', true);
}
$uid = explode(":", $identifier)[0];
$user = Account::getUserById($uid);
$nonce = gen_nonce($user, $_SESSION);

if ( !$user->admin )
{
    header('Location: /#/error/forbidden/', true);
}
?>
<!DOCTYPE html>
<html lang="en" ng-app="admin">
<head>
    <meta charset="UTF-8">
    <title>Whack Administration</title>
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
</head>
<body ng-controller="mainController">
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
            <a class="navbar-brand" href="/#/">Whack</a>
        </div>
        <div class="collapse navbar-collapse" id="menu">
            <ul class="nav navbar-nav navbar-right">
                <li><a href="/#/">Home</a></li>
                <!-- these will be filled in dynamically when the user logs on -->
                <li id="play-button"><a href="/#/play">Play</a></li>
                <li class="vert-divide"><span class="center">|</span></li>
                <li id="login">
                    <span class="center">
                        Hello <?= $user->name ?>
                    </span>
                </li>
                <li id="logout"><a href="/whack/management/logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>
<!-- Modal -->
<div class="modal fade"
     id="new-phrase"
     tabindex="-1"
     role="dialog"
     aria-labelledby="phrase-lbl">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form enctype="multipart/form-data" action="add_phrase.php" method="post">
                <?php
                $max_image = Phrase::MAX_IMAGE;
                echo "<input type='hidden' 
                             name='MAX_FILE_SIZE' 
                             value='$max_image'>"
                ?>
                <div class="modal-header">
                    <button type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="phrase-lbl">Create a new Phrase</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="phrase-content">Phrase Text</label>
                        <textarea id="phrase-content"
                                  name="phrase-content"
                                  autocomplete="off"
                                  ng-model="phraseForm.phrase"
                                  class="form-control">
                        </textarea>
                    </div>
                    <div class="form-group">
                        <label for="author">Who came up with it?</label>
                        <input type="text"
                               class="form-control"
                               id="author"
                               name="author"
                               ng-model="phraseForm.author"
                        />
                    </div>
                    <div class="form-group">
                        <label for="origin">
                            Where did the phrase come from?
                        </label>
                        <input type="text"
                               class="form-control"
                               id="origin"
                               name="origin"
                               ng-model="phraseForm.origin"
                        />
                    </div>
                    <div class="form-group">
                        <label for="image">
                            Related image <span class="optional">(optional)</span>
                        </label>
                        <input type="file"
                               class="form-control"
                               name="image"
                               id="image"
                        />
                    </div>
                </div>
                <div class="modal-footer">
                    <?= "<input type='hidden' name='nonce' value='$nonce' />"?>
                    <input type='submit'
                            class='button btn-submit'
                            ng-disabled='phraseForm.phrase === "" ||
                                         phraseForm.origin === "" ||
                                         phraseForm.author === ""'
                    />
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade"
     id="new-admin"
     tabindex="-1"
     role="dialog"
     aria-labelledby="admin-lbl">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="admin-lbl">Make a new admin</h4>
            </div>
            <form action="mkadmin.php" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="admin">Select user</label>
                        <?php
                        $users = list_non_admins();
                        $user_len = count($users);
                        echo "<select class='form-control' 
                                  name='admin' 
                                  id='admin' 
                                  size='$user_len'
                                  ng-model='adminForm.admin'
                                  >";

                        foreach ($users as $user)
                        {
                            echo "\t<option value='$user'>$user</option>";
                        }

                        echo '</select>';
                        ?>
                    </div>
                    <div class="form-group">
                        <label for="password">Your Password</label>
                        <input class="form-control"
                               type="password"
                               id="password"
                               name="password"
                               ng-model="adminForm.pwd"
                        >
                    </div>
                </div>
                <div class="modal-footer">
                    <?= "<input type='hidden' name='nonce' value='$nonce' />"?>
                    <input type='submit'
                            class='button btn-submit'
                            ng-disabled='adminForm.pwd === "" || adminForm.admin === ""'
                    />
                </div>
            </form>
        </div>
    </div>
</div>
<div class="panel main-panel panel-default">
    <div class="panel-body">
        <h2>What would you like to do?</h2>
        <ul class="button-group" id="admin-ctrls">
            <li>
                <button class="button"
                        data-toggle="modal"
                        data-target="#new-phrase">
                    Add Phrase
                </button>
            </li>
            <li>
                <button class="button"
                        data-target="#new-admin"
                        data-toggle="modal"
                >
                    Create admin
                </button>
            </li>
        </ul>
    </div>
</div>

<script src="https://code.jquery.com/jquery-2.2.4.min.js"
        integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="
        crossorigin="anonymous"></script>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.7/angular.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
        integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
        crossorigin="anonymous">
</script>
<script src="/assets/js/account.js"></script>
<script src="/assets/js/admin.js"></script>
</body>
</html>