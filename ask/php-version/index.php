<?php
/**
 * Created by PhpStorm.
 * User: hercules
 * Date: 2/22/2019
 * Time: 10:04 AM
 *
 *
 * ask your question and click submit
 *
 * pop up lightning some kind of loading indicator ?
 *
 *  randomly select answer
 *  save question and answer
 *
 * Refresh page
 *  show question if available, else show general title
 *
 *  show image with answer and try again else show ask your question
 *
 *
 *
 */
include_once "../../utils.php";


$answers = array(
    //doggie Yes -answer
    'It is certain.',
    'It is decidedly so.',
    'Without a doubt.',
    'Yes.',
    'Yes - definitely.',
    'You may rely on it.',

    //doggie NO! -answer
    'Do not count on it.',
    'My reply is no.',
    'My sources say no.',
    'Very doubtful.',

    //doggie POWER! -answer
    'I will allow it!!',
    'Bow! Wow!!',
    'I forbid it!!',

    //doggie-notsure -distracted
    'Most likely.',
    'Yaaawn, I guess',
    'Ask me later.',
    //doggie-notsure -thinking

    //'Outlook seems good.',
    //'Signs point to yes.',
    //'Reply hazy, try again.',
    'Cannot predict now... must rest.',
    'Concentrate and ask again.',
    'Clouded... the future is.',


    //'As I see it, yes.',
    //'Ask again later.',
    //'Better not tell you now.',
    //'Outlook not so good.',

);

//set the answer and appropriate background
$answer = mt_rand(0, (count($answers) - 1));
$doggieBG = '.doggie-bg';

if ($answer < 10) {
    $doggieBG = 'doggie-bg-answer';
} else if ($answer < 13) {
    $doggieBG = 'doggie-bg-power';
} else if ($answer < 16) {
    $doggieBG = 'doggie-bg-distracted';
}else if ($answer > 15 ) {
    $doggieBG = 'doggie-bg-notsure';
}

$previous = 'Say or type your question';
$show = false;

if(!empty($_REQUEST['fromForm'])) {
    $show=true;
    //$previous = filter_var ( $_REQUEST['ask'] , FILTER_SANITIZE_STRING);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>What is your question?</title>
    <!-- import bootstrap and jquery -->
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

        <!-- Latest compiled and minified JavaScript -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>

        <![endif]-->
    <!-- done importing bootstrap and jquery-->
    <link href="eightstyle.css" rel="stylesheet">
    <script src="ask.js"></script>

</head>

    <div class="container">
        <div class="jumbotron doggie">
            <form action="index.php" method="post" class="askForm">
                <h1>Look into my eyes</h1>
                <p class="lead">Ask Your Question!</p>
                <p class=""><input type="hidden" name="fromForm" id="fromForm" value=true></p>
                <button class="btn-lg btn-block" type="submit" name="submit"> Ask Luna </button>
            </form>
        </div>

        <?php if($show) {

          echo"
           <div id=\"popup\" class=\"popupShell doggie {$doggieBG}\">
            <div id='doggieContainer' class=\"{$doggieBG}\">
                <span id=\"formChanges\" class=\"popupForm\">
                    <div id=\"answer\" class=\"text-uppercase text-center answer\">
                        $answers[$answer]
                    </div>
                </span>
            </div>
          </div>";
        } ?>


        <footer class="footer">
            <p><hr></p>
            <p>This site uses <a href="http://getbootstrap.com/" target="_blank">bootstrap</a>, and doggie power.
            <p><b>Bow to the Wow</b></p>
        </footer>

    </div> <!-- /container -->

    </body>
</html>

