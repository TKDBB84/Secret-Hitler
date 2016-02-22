
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Bootstrap 101 Template</title>

    <!-- Bootstrap -->
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
      <style>
          .card.active:before{
              content: ' ';
              background-image: url('/img/redX.png');
              background-size: cover;
              height: 130px;
              width: 100px;
              position: absolute;
              display: block;
          }

          .hitler {
              background-color: orangered;
          }
          .hitler::after{
              content: ' - Hitler';
          }
          .liberal {
              background-color: lightblue;
          }
          .liberal::after {
              content: ' - Liberal';
          }
          .fascist {
              background-color: darkorange;
          }
          .fascist::after {
              content: ' - Fascist';
          }

          span.appointment.candidate-president {
               background-color: #00AA88;
          }
          span.appointment.candidate-president::after {
              content: ' - Candidate President';
          }
          span.appointment.candidate-chancellor {
              background-color: #9999FF;
          }
          span.appointment.candidate-chancellor::after {
              content: ' - Candidate Chancellor';
          }

          span.appointment.elected-chancellor {
              background-color: #7a43b6;
          }
          span.appointment.elected-chancellor::after {
              content: ' - Chancellor';
          }
          span.appointment.elected-president {
              background-color: #7a43b6;
          }
          span.appointment.elected-president::after {
              content: ' - President';
          }

          span.appointment.previous-chancellor {
              background-color: #8eb60c;
          }
          span.appointment.previous-chancellor::after {
              content: ' - Previous Chancellor';
          }
          span.appointment.previous-president {
              background-color: #21b630;
          }
          span.appointment.previous-president::after {
              content: ' - Previous President';
          }

          span.modifier.dead::after {
              content: ' - DEAD, Not Hitler';
          }

          span.modifier.not-hitler::after {
              content: ' - Not Hilter';
          }



          .lPolicy.onBoard {
              position: relative;
              height: 142px;
              top: 60px;
              left: 118px
          }
          div.stalemateCircle{
              font-size: 3em;
              position: absolute;
              top: 210px;
              color: orange;
          }
          .stalmate0{
              left: 267px;
          }
          .stalmate1{
              left: 337px;
          }
          .stalmate2{
              left: 406px;
          }
          .stalmate3{
              left: 476px;
              color: red !important;
          }

          @-webkit-keyframes blinker { from {opacity:1.0;} to {opacity:0.0;} }
          @keyframes blinker { from {opacity:1.0;} to {opacity:0.0;} }

          .blink {
              text-decoration:blink;

              -webkit-animation-name:blinker;
              animation-name:blinker;
              -webkit-animation-iteration-count:infinite;
              animation-iteration-count:infinite;
              -webkit-animation-timing-function:cubic-bezier(1.0,0,0,1.0);
              animation-timing-function:cubic-bezier(1.0,0,0,1.0);
              -webkit-animation-duration:1s;
              animation-duration:1s;
          }
      </style>
  </head>
  <body data-user="" style="padding-bottom: 75px;">
    <div class="container">
        <div class="row" id="logo">
            <div class="col-lg-4"></div>
            <div class="col-lg-8" style="background-image: URL(img/logo.png);background-size: contain;background-repeat: no-repeat;height: 229px;">
                <h1>Shitty</h1>
            </div>
            <div class="col-lg-2"></div>
        </div>

        <div id="shittyLogin" class="row">
            <div class="col-lg-12">
              <label>Enter Your Name:</label>
              <input type="text" id='name' class="input-sm" />
              <button id="enterGame" type="button" class="btn">Enter Game</button>
            </div>
        </div>

        <div id="adminArea" class="row">
          <div class="col-lg-12" id="adminArea">
            <button class="btn" id="startGame">Start Game</button>
          </div>
        </div>
        <div class="row">
            <div class="col-lg-8">
                <div id="gameBoard">
                    Number Of Liberal Policies: <span id="lPolicy">0</span>
                    <div id="libPolicies" style="background-image: url('img/l_board.png'); background-size: contain; height: 302px; background-repeat: no-repeat;">
                        <div id="lPolicy1" style="display: inline-block;" class="BoardCard">
                            <img src="./img/l_policy.png" class="lPolicy onBoard" >
                        </div>
                        <div id="lPolicy2" style="display: inline-block;" class="BoardCard">
                            <img src="./img/l_policy.png" class="lPolicy onBoard" >
                        </div>
                        <div id="lPolicy3" style="display: inline-block;" class="BoardCard lPolicy">
                            <img src="./img/l_policy.png" class="lPolicy onBoard">
                        </div>
                        <div id="lPolicy4" style="display: inline-block;" class="BoardCard">
                            <img src="./img/l_policy.png" class="lPolicy onBoard" >
                        </div>
                        <div id="lPolicy5" style="display: inline-block;" class="BoardCard">
                            <img src="./img/l_policy.png" class="lPolicy onBoard" >
                        </div>
                        <div id="stalemateTracker" class="stalemateCircle stalmate0">
                            ●
                        </div>
                        <div id="stalemateTracker" class="stalemateCircle stalmate1">
                            ●
                        </div>
                        <div id="stalemateTracker" class="stalemateCircle stalmate2">
                            ●
                        </div>
                        <div id="stalemateTracker" class="stalemateCircle stalmate3 blink">
                            ●
                        </div>
                    </div>
                    <div id="deckCounts">
                        Drawpile Count: <span id="drawCount">10</span> - Discard Count: <span id="discardCount">10</span>
                    </div>
                    <br/>
                    Number Of Facist Policies: <span id="fPolicy">0</span>
                    <div id="facPolicies" style="background-image: url(img/board5to6.png); background-size: contain; height: 302px; background-repeat: no-repeat;">
                        <div id="fPolicy1" style="display: inline-block;" class="BoardCard">
                            <img src="./img/f_policy.png" style="position: relative;height: 142px;top: 65px;left: 71px;">
                        </div>
                        <div id="fPolicy2" style="display: inline-block;" class="BoardCard">
                            <img src="./img/f_policy.png" style="position: relative;height: 142px;top: 65px;left: 71px;" class="">
                        </div>
                        <div id="fPolicy3" style="display: inline-block;" class="BoardCard">
                            <img src="./img/f_policy.png" style="position: relative;height: 142px;top: 65px;left: 71px;" class="">
                        </div>
                        <div id="fPolicy4" style="display: inline-block;" class="BoardCard">
                            <img src="./img/f_policy.png" style="position: relative;height: 142px;top: 65px;left: 71px;" class="">
                        </div>
                        <div id="fPolicy5" style="display: inline-block;" class="BoardCard">
                            <img src="./img/f_policy.png" style="position: relative;height: 142px;top: 65px;left: 71px;" class="">
                        </div>
                        <div id="fPolicy6" style="display: inline-block;" class="BoardCard">
                            <img src="./img/f_policy.png" style="position: relative;height: 142px;top: 65px;left: 71px;" class="">
                        </div>
                    </div>

                </div>
            </div>
            <div class="col-lg-3">
                <h3>Player List:</h3>
                <ul id="playerList">
                </ul>
            </div>
        </div>
        <div class="row">
            <div id="playerArea" class="col-lg-12">
                <div id="pickChancellor">
                    <h3>Choose Chancellor: </h3>
                    <div class="btn-group" data-toggle="buttons" id="chancellorSelect">
                    </div>
                    <button class="btn btn-default" id="confirmChan">Confirm Chancellor</button>
                </div>
                <div id="presVote" style="display: none;">
                    <h3>Vote: </h3>
                    <div class="btn-group" data-toggle="buttons" id="voteSelect">
                        <label class="btn btn-success">
                            <input type="radio">  Ja
                        </label>
                        <label class="btn btn-danger">
                            <input type="radio">  Nein
                        </label>
                    </div>
                    <button class="btn btn-default" id="confirmVote">Vote</button>
                </div>
                <div id="voteResults" style="">
                    <h3>Last Vote Results: </h3>
                    <div id="voteContainer">

                    </div>
                </div>
                <div id="cardSelect" style="display: none;">
                    <h3>Choose Card To Discard: </h3>
                    <div id="cardContainer"> </div>
                    <button class="btn btn-default" id="discard">Discard Selected</button>
                </div>
                <div id="playerPeekArea">
                    <h3>Choose Role To See</h3>
                    <div id="peekSelectionArea" class="btn-group" data-toggle="buttons" >

                    </div>
                </div>
            </div>
        </div>

        <div class="card `" style="display: none; font-weight: bold;">
            <img src="./img/f_policy.png" style="height: 142px;">
        </div>
        <div class="card lCard cardTmpl" style="display: none; font-weight: bold;">
            <img src="./img/l_policy.png" style="height: 142px;">
        </div>
    </div>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <!-- Latest compiled and minified JavaScript -->
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/json3/3.3.2/json3.js"></script>
    <script type="text/javascript" src="game.js"></script>
    <script type="text/javascript">
        $(document).ready(function(e){
            $('.stalemateCircle').hide();
            $('.stalemateCircle.stalmate0').show();
            $('#adminArea').hide();
            $('#pickChancellor').hide();
            $('.BoardCard').hide();
            $('#voteResults').hide();
            $('#playerPeekArea').hide();
            $('#enterGame').click(function(e){
                <?php
                    $address = parse_url($_SERVER['HTTP_HOST']);
                ?>
              SecretHitler.init($('#name').val(),'<?php echo $address['host']; ?>');
            });

            $('#startGame').click(function(e){
              $('#startGame').hide();
              SecretHitler.startGame();
            });

            $('#cardSelect').on('click','div.card',function(e){
                $('div.card').removeClass('active');
                $(this).toggleClass('active');
            });

            $('#discard').click(function(e){
                var numCards = $('#cardSelect').find('div.card').length,
                $active = $('#cardSelect').find('div.active');
                if($active.hasClass('fCard')){
                    SecretHitler.discardCard('f');
                }else {
                    SecretHitler.discardCard('l');
                }
                $active.remove();
                if( numCards == 3){
                    var sendToChan1 = '',
                        sendToChan2 = '',
                        $passed = $('#cardSelect').find('div.card');
                    if($($passed[0]).hasClass('fCard')){
                        sendToChan1 = 'f';
                    } else {
                        sendToChan1 = 'l';
                    }

                    if($($passed[1]).hasClass('fCard')){
                        sendToChan2 = 'f';
                    } else {
                        sendToChan2 = 'l';
                    }
                    SecretHitler.passCards(sendToChan1,sendToChan2);
                } else {

                    var $passed = $('#cardSelect').find('div.card');
                    if($passed.hasClass('fCard')) {
                        SecretHitler.activateCard('f');
                    } else {
                        SecretHitler.activateCard('l')
                    }

                }
                $passed.remove();
                $('#cardSelect').hide();
            });

        });
    </script>
  </body>
</html>