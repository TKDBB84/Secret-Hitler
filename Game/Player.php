<?php
	/**
	 * Created by PhpStorm.
	 * User: jeffrey
	 * Date: 1/17/16
	 * Time: 6:52 PM
	 */

	namespace Game;

	use Ratchet\ConnectionInterface;
	use \SplEnum as SplEnum;

	class Player{
		/** @var ConnectionInterface | null */
		public $connection;
		/** @var string */
		public $name;

		/** @var int */
		public $id = -1;

		/** @var int */
		public $role;

		/** @var bool */
		public $isAlive = true;

		/**
		 * Player constructor.
		 * @param ConnectionInterface $connection
		 */
		public function __construct(ConnectionInterface &$connection = null)
		{
			$this->connection = $connection;
			if($connection !== null) {
				$this->id = $connection->resourceId;
			} else {
				$this->id = 0;
			}
		}


		public function kill(){
			$this->isAlive = false;
		}

		public function isAlive(){
			return $this->isAlive;
		}


		public function clearVote(){
			$this->vote = null;
		}

		public function getVote(){
			return $this->vote;
		}

		public function setVote($vote){
			$this->vote = $vote;
		}

		public function hasVoted(){
			return !is_null($this->vote);
		}

		public function setRole($role) {
			echo 'Assigning: ',$role,' Player: ',$this->id,PHP_EOL;
			$this->role = $role;
		}

		public function getRole() {
			return $this->role;
		}

		public function isFascist() {
			return $this->role >= Faction::Fascist;
		}

		public function isLiberal() {
			return $this->role == Faction::Liberal;
		}

		public function isHitler() {
			return $this->role == Faction::Hitler;
		}

		/**
		 * @param string $function
		 * @param array $args
		 */
		public function sendMessage($function,array $args){
			$data = json_encode(['func' => $function, 'args' => $args]);
			echo 'Sending: ',$data,' To: {',$this->id,', ',$this->name,'}',PHP_EOL;
			$this->connection->send($data);
		}

	}