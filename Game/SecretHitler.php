<?php
	/**
	 * Created by PhpStorm.
	 * User: jeffrey
	 * Date: 1/17/16
	 * Time: 6:47 PM
	 */

	namespace Game;
	use Ratchet\MessageComponentInterface;
	use Ratchet\ConnectionInterface;
	use Ratchet\Server\IoServer;

	require __DIR__.'/Player.php';
    require __DIR__.'/Deck.php';
    require __DIR__.'/Card.php';
    require __DIR__.'/Request.php';
    require __DIR__.'/Faction.php';
    use Ratchet\WebSocket\Encoding\Validator;
    use \SplEnum as SplEnum;

	class SecretHitler implements MessageComponentInterface {

		/** @var Player[] */
		private static $players;
        /** @var Deck */
        private static $deck;

        /** @var int */
		private static $liberalPoliciesPast;
        /** @var int */
		private static $fascistPoliciesPast;
        /** @var int */
        private static $numStalemates = 0;

        /** @var bool */
        private static $userConnecting = false;
        /** @var bool */
        private static $gameInProgress = false;

        /** @var int */
        private static $originalNumPlayers = 0;


        //special Assignments
        private static $assignedPresidentActive = false;
        private static $assignedPresidentCandidateID = -1;

        // presidential data
        private static $currentElectedPresidentID = -1;
        private static $currentPresidentCandidateID = -1;
        private static $previousElectedPresidentID = -1;


        private static $currentElectedChancellorID = -1;
        private static $currentChancellorCandidateID = -1;
        private static $previousElectedChancellorID = -1;

        public static $votes = [];

		/**
		 * SecretHitler constructor.
		 */
		public function __construct($numberPlayers = 5) {

            self::$deck = new Deck();

			self::$liberalPoliciesPast = 0;
			self::$fascistPoliciesPast = 0;
            self::$numStalemates = 0;

            self::$players = [];

		}

        public function onOpen(ConnectionInterface $conn) {
            while(self::$userConnecting) {
                usleep(10);
            }
            self::$userConnecting = true;
            if(!self::$gameInProgress) {
                $player = new Player($conn);
                self::$players[] = $player;
                if (count(self::$players) == 1) {
                    $player->sendMessage('showAdmin',[]);
                }
                echo "New connection! ({$conn->resourceId})\n";
            }else {
                $conn->send('gameInProgress');
            }
        }

        public function onClose(ConnectionInterface $conn) {
            self::removePlayerByID($conn->resourceId);
        }

        public function onError(ConnectionInterface $conn, \Exception $e) {
            echo "An error has occurred: {$e->getMessage()}\n";
            $conn->close();
        }

        public function onMessage(ConnectionInterface $from, $msg) {
            echo 'Got Message: ',$msg,' From: ',$from->resourceId,PHP_EOL;
            $request = new Request($from,$msg);
            if($request->function == 'registerPlayer') {
                self::registerPlayer($from,$request->arguments[0]);
            }elseif($request->function == 'recordVote') {
                self::recordPlayerVote($from,$request->arguments[0]);
            }elseif($request->function == 'playerPeek'){
                self::playerpeek($from,$request->arguments[0]);
            }elseif($request->function == 'killPlayer') {
                self::killPlayer($from, $request->arguments[0]);
            }elseif($request->function == 'deckPeek'){
                self::deckPeek($from);
            }elseif( method_exists($this,$request->function)) {
                call_user_func_array([$this,$request->function],$request->arguments);
            }
        }

        public static function assignRoles() {
            switch (count(self::$players)) {
                case 10:
                case 9:
                    $numberOfFascists = 3;
                    break;
                case 8:
                case 7:
                    $numberOfFascists = 2;
                    break;
                case 6:
                case 5:
                    $numberOfFascists = 1;
                    break;
                default:
                    return false;
                    // can't start game
                    break;
            }
            /** @var int $roles */
            $roles = [];
            $roles[] = Faction::Hitler;
            for ($i = 0 ; $i < $numberOfFascists ; $i++) {
                $roles[] = Faction::Fascist;
            }
            while (count($roles) < count(self::$players)) {
                $roles[] = Faction::Liberal;
            }
            shuffle($roles);
            for($i = 0 ; $i < count($roles) ; $i++ ) {
                self::$players[$i]->setRole($roles[$i]);
            }
            return true;
        }

        public static function showKillSelection(){
            $president = self::getCurrentElectedPresident();

            $killablePlayers = [];
            foreach(self::$players as $player) {
                if($player->isAlive() && ($president->id !== $player->id) ){
                    $killablePlayers[] = [
                        'id' => $player->id,
                        'name' => $player->name
                    ];
                }
            }
            $president->sendMessage('showKillSelection',$killablePlayers);
        }

        public static function showSpecialPresidentSelection(){
            $president = self::getCurrentElectedPresident();

            $killablePlayers = [];
            foreach(self::$players as $player) {
                if($player->isAlive() && ($president->id !== $player->id) ){
                    $killablePlayers[] = [
                        'id' => $player->id,
                        'name' => $player->name
                    ];
                }
            }
            $president->sendMessage('showSpecialPresidentSelection',$killablePlayers);
        }

        public static function showPlayerPeekSelection(){
            $president = self::getCurrentElectedPresident();

            $killablePlayers = [];
            foreach(self::$players as $player) {
                if($player->isAlive() && ($president->id !== $player->id) ){
                    $killablePlayers[] = [
                        'id' => $player->id,
                        'name' => $player->name
                    ];
                }
            }
            $president->sendMessage('showPlayerPeekSelection',$killablePlayers);
        }

        public static function killPlayer(ConnectionInterface $from, $playerID) {
            if($from->resourceId == self::$currentElectedPresidentID) {
                for ($i = 0 ; $i < count(self::$players) ; $i++) {
                    if(self::$players[$i]->id == $playerID) {
                        self::$players[$i]->kill();
                        break;
                    }
                }
                self::sendDead($playerID);
            }
        }

        public static function sendRoles() {
            /** @var Player[] $fascistPlayers */
            $fascistPlayers = [];
            foreach (self::$players as $player) {
                if ($player->isFascist()) {
                    $fascistPlayers[] = $player;
                }
            }

            foreach(self::$players as $player) {
                echo 'sending to: '.$player->name,PHP_EOL;
                if( ($player->isHitler() && count(self::$players) >= 7) || $player->isLiberal() ) {
                    $player->sendMessage('setFaction', ['playerID' => $player->id, 'faction' => $player->getRole()]);

                } elseif ($player->isFascist()) {
                    foreach ($fascistPlayers as $fascist) {
                        $player->sendMessage('setFaction', ['playerID' => $fascist->id, 'faction' => $fascist->getRole()]);
                    }

                }
            }

        }

        /**
         * @param $id int
         * @return Player
         */
        public static function getPlayerByID($id){
            foreach (self::$players as $player) {
                if($player->id == $id) {
                    return $player;
                }
            }
            return new Player();
        }

        /**
         * @param $id
         * @return Player
         */
        public static function removePlayerByID($id){
            $indexToRemove = -1;
            for ($i = 0 ; $i < count(self::$players) ; $i++) {
                if(self::$players[$i]->id == $id) {
                    $indexToRemove = $i;
                    break;
                }
            }
            if($indexToRemove !== -1) {
                $result = self::$players[$indexToRemove];
                unset(self::$players[$indexToRemove]);
                self::$players = array_values(self::$players);
            } else {
                $result = new Player();
            }

            return $result;

        }

        public static function getCandidatePresident() {
            if (self::$assignedPresidentActive) {
               return self::getPlayerByID(self::$assignedPresidentCandidateID);
            } else {
                return self::getPlayerByID(self::$currentPresidentCandidateID);
            }
        }
        public static function getCandidateChancellor() {
            return self::getPlayerByID(self::$currentChancellorCandidateID);
        }

        public static function getCurrentElectedPresident() {
            return self::getPlayerByID(self::$currentElectedPresidentID);
        }
        public static function getCurrentElectedChancellor() {
            return self::getPlayerByID(self::$currentElectedChancellorID);
        }


        public static function assignPresident($playerID) {
            self::$previousElectedPresidentID = self::$currentElectedPresidentID;
            self::$currentElectedPresidentID = $playerID;
        }
        public static function assignChancellor($playerID) {
            self::$previousElectedChancellorID = self::$currentChancellorCandidateID;
            self::$currentElectedChancellorID = $playerID;
        }


        public static function assignPresidentCandidate($playerID) {
            self::$assignedPresidentCandidateID = $playerID;
            self::$assignedPresidentActive = true;
        }
        public static function assignCandidateChancellor($playerID) {
            self::$currentChancellorCandidateID = $playerID;
            self::sendCandidateChancellor();
            self::sendVoteActivation();
        }

        public static function incrementCandidatePresident() {
            if(self::$assignedPresidentActive) {
                self::$assignedPresidentActive = false;
                self::$assignedPresidentCandidateID = -1;
            }

            for($i = 0 ; $i < count(self::$players) ; $i++) {
                if(self::$players[$i]->id == self::$currentPresidentCandidateID) {
                    self::$currentPresidentCandidateID = self::$players[$i+1]->id;
                    break;
                }
            }

        }

        public static function playerPeek(ConnectionInterface $from,$playerID) {
            if($from->resourceId == self::$currentElectedPresidentID) {
                $president = self::getCurrentElectedPresident();
                $peek = self::getPlayerByID($playerID);
                $president->sendMessage('setFaction',['playerID' => $peek->id, 'faction' =>($peek->isFascist()?1:0) ]);
            }
        }

        public static function deckPeek(ConnectionInterface $from) {
            if($from->resourceId == self::$currentElectedPresidentID) {
                $president = self::getCurrentElectedPresident();
                $peeks = self::$deck->peak(3);
                $president->sendMessage('cardPeek',[$peeks[0],$peeks[1],$peeks[2]]);
            }
        }

        public static function numEligiblePlayersToVote() {
            $count = 0;
            foreach (self::$players as $player) {
                if($player->isAlive()) {
                    $count++;
                }
            }
            return $count;
        }

        public static function recordPlayerVote(ConnectionInterface $from, $vote) {
            $player = self::getPlayerByID($from->resourceId);
            echo 'Got Vote Of ', $vote,' From ',$player->name,PHP_EOL;

            self::$votes[] = ((strcasecmp($vote,'ja') === 0)?1:0);
            if (count(self::$votes) == self::numEligiblePlayersToVote()) {
               self::countVotes();
            }
        }

        public static function countVotes(){
            $voteSum = 0;
            foreach(self::$votes as $vote) {
                $voteSum += $vote;
            }
            $majority = floor(self::numEligiblePlayersToVote() / 2) + 1;
            if ($voteSum >= $majority) {
                self::assignPresident(self::$currentPresidentCandidateID);
                self::assignChancellor(self::$currentChancellorCandidateID);
                self::sendElectedPresident();
                self::sendElectedChancellor();
                self::sendPresidentCardSelection();

            } else {
                self::passTurn();
                self::$numStalemates++;
                if (self::$numStalemates == 3) {
                    self::autoPassPolicy();
                    //send board state before resetting stalemates
                    self::sendBoardState();
                    self::$numStalemates = 0;
                } else {
                    //send boardState
                    self::sendBoardState();
                }
            }
        }

        public static function passPolicy($card){
            if($card == 0) {
                self::$deck->discard(new Card(Faction::Liberal));
                self::$liberalPoliciesPast++;
                self::sendBoardState();
                self::passTurn();
            } else {
                self::$deck->discard(new Card(Faction::Liberal));
                self::$fascistPoliciesPast++;
                self::sendBoardState();
                self::doEvent();
            }
        }

        public static function doEvent(){
            if (self::$fascistPoliciesPast == 4 || self::$fascistPoliciesPast == 5) {
                self::showKillSelection();
            } else {
                switch (self::$originalNumPlayers) {
                    case 10:
                    case 9:
                        if (self::$fascistPoliciesPast == 1 || self::$fascistPoliciesPast == 2) {
                            self::showPlayerPeekSelection();
                        } elseif (self::$fascistPoliciesPast == 3) {
                            self::showSpecialPresidentSelection();
                        }
                        break;
                    case 8:
                    case 7:
                        if (self::$fascistPoliciesPast == 2) {
                            self::showPlayerPeekSelection();
                        } elseif (self::$fascistPoliciesPast == 3) {
                            self::showSpecialPresidentSelection();
                        }
                        break;
                    default:
                        if (self::$fascistPoliciesPast == 3) {
                            self::showCardPeek();
                        }
                        break;
                }
            }
        }

        public static function discardCard($card) {
            if($card == 0) {
                self::$deck->discard(new Card(Faction::Liberal));
            } else {
                self::$deck->discard(new Card(Faction::Fascist));
            }
        }

        public static function passCards($card1,$card2) {
            $chancellor = self::getCurrentElectedChancellor();
            $chancellor->sendMessage('showCards',[$card1,$card2]);
            if(self::isVetoActive()) {
                $chancellor->sendMessage('showVetoButton',[0]);
            }
        }

        public static function requestPresidentialVeto() {
            if(self::isVetoActive()) {
                $president = self::getCurrentElectedPresident();
                $president->sendMessage('showVetoButton',[1]);
            }
        }

        public static function forceChancellorDiscard(){
            $chancellor = self::getCurrentElectedChancellor();
            $chancellor->sendMessage('vetoDiscard',[]);
        }

        public static function denyVeto() {
            $chancellor = self::getCurrentElectedChancellor();
            $chancellor->sendMessage('denyVeto',[]);
        }

        public static function presidentialVeto() {
            self::forceChancellorDiscard();
            self::passTurn();
        }

        public static function passTurn() {
            self::incrementCandidatePresident();
            self::sendCandidatePresident();
            self::sendChancellorSelection();
        }

        public static function isVetoActive() {
            return self::$fascistPoliciesPast >= 5;
        }

        public static function sendPresidentCardSelection() {
            $cards = self::$deck->draw(3);
            $president = self::getCurrentElectedPresident();
            $president->sendMessage('showCards',[$cards[0],$cards[1],$cards[2]]);
        }

        public static function showCardPeek() {
            $cards = self::$deck->peak(3);
            $president = self::getCurrentElectedPresident();
            $president->sendMessage('showCardPeek',$cards);
        }

        public static function autoPassPolicy() {
            $policy = self::$deck->draw(1);
            if($policy == Faction::Liberal) {
                self::$liberalPoliciesPast++;
            } else {
                self::$fascistPoliciesPast++;
            }
        }

        public static function sendCardCount() {
            foreach(self::$players as $player) {
                $player->sendMessage('cardCounts',[
                    'draw' => self::$deck->getDrawCount(),
                    'discard' => self::$deck->getDiscardCount()
                ]);
            }
        }

        public static function sendBoardState() {
            //updateBoardState;
            $boardState = [
                'liberalCount' => self::$liberalPoliciesPast,
                'fascistCount' => self::$fascistPoliciesPast,
                'stalemateCount' => self::$numStalemates
            ];
            foreach(self::$players as $player) {
                $player->sendMessage('setBoardState',$boardState);
            }
        }

        public static function sendChancellorSelection() {
            $candidatePresident = self::getCandidatePresident();
            $eligiblePlayers = [];
            foreach (self::$players as $player) {
                $isEligible = $player->isAlive()
                    && $player->id !== self::$previousElectedChancellorID
                    && $player->id !== self::$previousElectedPresidentID;
                if($isEligible) {
                    $eligiblePlayers[] = [
                        'id' => $player->id,
                        'name' => $player->name
                    ];
                }
            }

            $candidatePresident->sendMessage('showChancellorSelection',$eligiblePlayers);

        }

        public static function sendCandidatePresident() {
            $president = self::getCandidatePresident();
            foreach(self::$players as $player) {
                $player->sendMessage('assignAppointment',['playerID' => $president->id, 'appointment' => 'candidate-president']);
            }
        }
        public static function sendCandidateChancellor() {
            $chancellor = self::getCandidateChancellor();
            foreach(self::$players as $player) {
                $player->sendMessage('assignAppointment',['playerID' => $chancellor->id, 'appointment' => 'candidate-chancellor']);
            }
        }

        public static function sendElectedPresident() {
            $president = self::getCurrentElectedPresident();
            foreach(self::$players as $player) {
                $player->sendMessage('removeAppointment',['playerID' => $president->id, 'appointment' => 'candidate-president']);
                $player->sendMessage('assignAppointment',['playerID' => $president->id, 'appointment' => 'elected-president']);
            }
        }

        public static function sendElectedChancellor() {
            $chancellor = self::getCurrentElectedChancellor();
            foreach(self::$players as $player) {
                $player->sendMessage('removeAppointment',['playerID' => $chancellor->id, 'appointment' => 'candidate-chancellor']);
                $player->sendMessage('assignAppointment',['playerID' => $chancellor->id, 'appointment' => 'elected-chancellor']);
                if(self::$fascistPoliciesPast >= 3) {
                    if(!$chancellor->isHitler()) {
                        self::sendNotHitler($chancellor->id);
                    } else {
                        self::endGame(Faction::Fascist);
                    }
                }
            }
        }
        public static function sendNotHitler($playerID) {
            foreach(self::$players as $player) {
                $player->sendMessage('assignModifier',['playerID' => $playerID, 'modifier' => 'not-hitler']);
            }
        }
        public static function sendDead($playerID) {
            foreach(self::$players as $player) {
                $player->sendMessage('assignModifier',['playerID' => $playerID, 'modifier' => 'dead']);
            }
        }

        public static function endGame($winner) {

        }

        public static function startGame() {
            self::$gameInProgress = true;
            if(self::assignRoles()) {
                self::sendRoles();
                self::$originalNumPlayers = self::numEligiblePlayersToVote();
                self::$currentPresidentCandidateID = self::$players[mt_rand(0, count(self::$players) - 1)]->id;
                self::sendCandidatePresident();
                self::sendChancellorSelection();
                self::setBoard();
                self::sendCardCount();
            } else {
                echo 'could not assign roles...',PHP_EOL;
                self::$gameInProgress = false;
                // @todo: send error message
            }
        }

        public static function setBoard() {
            $numPlayers = self::numEligiblePlayersToVote();
            switch($numPlayers) {
                case 10:
                case 9:
                    $boardURL = 'img/board9to10.png';
                    break;
                case 8:
                case 7:
                    $boardURL = 'img/board7to8.png';
                    break;
                default:
                    $boardURL = 'img/board5to6.png';
                    break;
            }

            foreach (self::$players as $player) {
                $player->sendMessage('setPlayBoard',[$boardURL]);
            }
        }

        public static function sendVoteActivation() {
            foreach (self::$players as $player) {
                $player->sendMessage('activateVote',[]);
            }
        }

        public static function registerPlayer(ConnectionInterface $from, $playerName) {
            for ($i = 0 ; $i < count(self::$players) ; $i++) {
                if(self::$players[$i]->id == $from->resourceId) {
                    self::$players[$i]->name = $playerName;
                }
            }
            self::sendPlayerList();
            self::$userConnecting = false;
        }

        public static function sendPlayerList(){
            $playerList = [];
            foreach(self::$players as $player) {
                $playerList[] = [
                    'id' => $player->id,
                    'name' => $player->name
                ];
            }
            foreach(self::$players as $player) {
                $player->sendMessage('playerList',$playerList);
            }
        }

        /**
         * @param Player $player
         * @return Player
         */
        public static function removePlayer(Player $player){
            self::removePlayerByID($player->id);
            self::sendPlayerList();
        }
	}

