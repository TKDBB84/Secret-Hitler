/**
 * Created by jeffrey on 1/17/16.
 */


//var webSocket = new WebSocket('hitler.test:8898');
var webSocket = null;
var $playerList = $('#playerList');
var $adminArea = $('#adminArea');
var $loginScreen = $('#shittyLogin');


var SecretHitler = {


    canVeto: false,
    myName: 'New Player',
    myID: -1,

	init: function (playerName,address) {
        SecretHitler.myName = playerName;
		if(webSocket == null) {
			webSocket = new WebSocket('ws://'+address+':9124');
			webSocket.onopen = function (e) {
				SecretHitler.registerMe(SecretHitler.myName);
                $loginScreen.hide();
			};
			webSocket.onmessage = function (e) {
                console.log('gotMessage: ' + e.data);
                SecretHitler.gotMessage(JSON.parse(e.data));
			};
		}

        $('#confirmChan').click(function(e) {
            $('#pickChancellor').hide();
            var selectedChancellorId = $('#chancellorSelect').find('label.active').data('id');
            $('label.active').removeClass('active');
            SecretHitler.sendJSONToSever('assignCandidateChancellor',[selectedChancellorId]);
        });

        $('#confirmVote').click(function(e) {
            SecretHitler.recordVote($.trim($('#voteSelect').find('label.active').text()));
            $('label.active').removeClass('active');
            $('#presVote').hide();
            $('#voteContainer').text('Waiting For All Votes...');
        });

	},

    showAdmin: function() {
        $('#adminArea').show();
    },

    gotMessage: function(object) {
        // 'func' => $function, 'args' => [args]

        executeFunctionByName(object.func,object.args);
    },

    startGame: function () {
        SecretHitler.sendJSONToSever('startGame',[]);
    },

    recordVote: function(vote) {
        SecretHitler.sendJSONToSever('recordVote',[vote]);
    },

    getPlayerElement: function (id) {
      return $playerList.find('li[data-id='+id+']');
    },

    registerMe: function(myName) {
        SecretHitler.sendJSONToSever('registerPlayer',[myName]);
    },

    setBoardState: function (counts) {
      var $staleCount = $('#staleCount'),
          $fPolicyCount = $('#fPolicy'),
          $fPolicyCard = $('#fPolicy'+ counts.fascistCount),
          $lPolicyCount = $('#lPolicy'),
          $lPolicyCard = $('#lPolicy'+ counts.liberalCount),
          $staleCircle = $('.stalemateCircle.stalmate'+counts.stalemateCount);

        $fPolicyCard.css('display','inline-block');
        $lPolicyCard.css('display','inline-block');
        $staleCircle.show();
        if (counts.stalemateCount == 3) {
            window.setTimeout(function(){
                $('.stalemateCircle').hide();
                $('.stalemateCircle.stalmate0').show();
            },5000);
        }

    },

    playerPeek: function(playerID) {
        SecretHitler.sendJSONToSever('playerPeek',[playerID]);
    },

    assignModifier: function(playerID, modifer) {
        var $playerLi = SecretHitler.getPlayerElement(playerID);
        $playerLi.find('span.modifier').replaceWith($('<span/>').addClass('modifier '+ modifer));
    },

    assignAppointment: function(obj) {
        // playerID, appointment
        var $playerLi = SecretHitler.getPlayerElement(obj.playerID);
        $playerLi.find('span.appointment').addClass(obj.appointment);
    },

    showCards: function(card1,card2,card3) {
        $('#cardContainer').html('');
        var cards = [card1,card2,card3];
        $.each(cards,function(index,value){
           var $card;
            if(value == 0) {
                $card = $($('div.lCard')[0]).clone();
            } else {
                $card = $($('div.fCard')[0]).clone();
            }
            $('#cardContainer').append($card.css({'display':'inline-block'}));
        });
        $('#cardSelect').show();
    },

    discardCard: function(faction) {
        if(faction == 'f') {
            SecretHitler.sendJSONToSever('discardCard',[1]);
        } else {
            SecretHitler.sendJSONToSever('discardCard',[0]);
        }
    },

    passCards: function (card1,card2) {
      SecretHitler.sendJSONToSever('passCards',[card1,card2]);
        $('#cardContainer').html('');
        $('#cardSelect').hide();
    },

    cardCounts: function(obj) {
        // draw,discard
        $('#drawCount').text(obj.draw);
        $('#discardCount').text(obj.discard);
    },

    showVetoButton: function(obj) {

        var isPresident = obj.isPresident;
        if(!isPresident) {
            var $vetoButton = $('<button/>').text('Veto');
            $vetoButton.on('click',function(e){
                SecretHitler.sendJSONToSever('requestPresidentialVeto',[]);
                $(this).remove();
                $('#discard').hide();
            });
            $('#cardContainer').append($vetoButton);
        } else {
            $vetoButton = $('<button/>').css({'background-color' : 'green','color' : 'white'});
            $noVetoButton = $('<button/>').css({'background-color' : 'red','color' : 'white'});
            $vetoButton.on('click',function(e){
                SecretHitler.sendJSONToSever('presidentialVeto',[]);
                $vetoButton.remove();
                $noVetoButton.remove();
            });

            $noVetoButton.on('click',function(e){
                SecretHitler.sendJSONToSever('denyVeto',[]);
                $vetoButton.remove();
                $noVetoButton.remove();
            });

            $('#playerArea').append($vetoButton,$noVetoButton);
        }

    },

    vetoDenied: function() {
        $('#discard').show();
    },

    vetoDiscard: function() {
        var $cardContainer = $('#cardContainer');
        var lCards = $cardContainer.find('.lCard').length;
        var fCards = $cardContainer.find('.fCard').length;
        var discards = [];
        for( var i = 0 ; i < lCards ; i++ ) {
            SecretHitler.sendJSONToSever('discardCard',[0]);
        }
        for( var i = 0 ; i < fCards ; i++ ) {
            SecretHitler.sendJSONToSever('discardCard',[1]);
        }
    },

    removeAppointment: function(obj) {
        // playerID, appointment
        var $playerLi = SecretHitler.getPlayerElement(obj.playerID);
        $('span.appointment').removeClass(obj.appointment);
    },

    showKillSelection: function(obj){
        var $chancellorSelectArea = $('#chancellorSelect'),
            $selectorDiv = SecretHitler.generatePlayerSelection(obj);
        $chancellorSelectArea.html('').append($selectorDiv);
        $chancellorSelectArea.parent().show();
        //@todo: write callbacks

    },
    showSpecialPresidentSelection: function(obj) {
        var $chancellorSelectArea = $('#chancellorSelect'),
            $selectorDiv = SecretHitler.generatePlayerSelection(obj);
        $chancellorSelectArea.html('').append($selectorDiv);
        $chancellorSelectArea.parent().show();
        //@todo: write callbacks
    },
    showPlayerPeekSelection: function(obj) {
        var $chancellorSelectArea = $('#chancellorSelect'),
            $selectorDiv = SecretHitler.generatePlayerSelection(obj);
        $chancellorSelectArea.html('').append($selectorDiv);
        $chancellorSelectArea.parent().show();
        //@todo: write callbacks
    },
    showCardPeek: function(obj) {
        // @todo: temp card peek + callbacks
    },

    generatePlayerSelection: function(eligiblePlayers) {
        var $selector = $('<div/>').addClass('btn-group').attr('data-toggle','buttons'),
            $button = $('<label />').addClass('btn btn-default');
        $.each(eligiblePlayers,function(index,object){
            var $appendMe = $button.clone();
            $appendMe.data('id',object.id).text(object.name).prepend($('<input type="radio"/>').val(object.id))
            $selector.append($appendMe);
        });
        return $selector;
    },

    showChancellorSelection: function(eligiblePlayers) {
        var $chancellorSelectArea = $('#chancellorSelect'),
            $selectorDiv = SecretHitler.generatePlayerSelection(eligiblePlayers);
        $chancellorSelectArea.html('').append($selectorDiv);
        $chancellorSelectArea.parent().show();
    },

    activateVote: function() {
        $('#presVote').show();
    },

    setPlayBoard: function(url) {
        $('#facPolicies').css('background-img','url(\''+url+'\')');
    },

    setFaction: function (obj) {
        // obj: playerID, faction
        // 0 = lib, 1 = fas, 2 = hitler
        $playerLi = SecretHitler.getPlayerElement(obj.playerID);
        switch (obj.faction) {
            case 0:
                $playerLi.addClass('liberal');
                //lib
                break;
            case 1:
                $playerLi.addClass('fascist');
                // fas
                break;
            case 2:
                $playerLi.addClass('hitler');
                // hitler
                break;
        }
    },

    playerList: function(args) {
        var players = [];
        args.sort(function(a,b){
            if(a.id == b.id) {
                return 0;
            }
            return a.id < b.id ? -1 : 1;
        });
        $playerList.html('');
        for(var i = 0 ; i < args.length ; i++) {
            $playerList.append(
                $('<li />').attr('data-id',args[i].id).text(args[i].name).append(
                    $('<span class="appointment"/>'),
                    $('<span class="modifier"/>')
                )
            );
        }
    },

    sendJSONToSever: function(func, args) {
        var data = {
            function: func,
            arguments: args
        };
        webSocket.send(JSON.stringify(data));
    },
};

function executeFunctionByName(functionName/*, args */) {
    var args = [].slice.call(arguments).splice(1);
    var namespaces = functionName.split(".");
    var func = namespaces.pop();
    for(var i = 0; i < namespaces.length; i++) {
        context = context[namespaces[i]];
    }
    return SecretHitler[func].apply(SecretHitler,args);
};

