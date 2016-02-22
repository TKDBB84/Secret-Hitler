<?php
/**
 * Created by PhpStorm.
 * User: Jeffrey
 * Date: 2/15/2016
 * Time: 9:04 PM
 */

namespace Game;

use \SplEnum as SplEnum;

class Deck
{
    /** @var Card[] */
    private $drawPile = [];
    /** @var Card[] */
    private $discardPile = [];

    public function __construct($numberPlayers = 5) {
        for($i = 0 ; $i < 6 ; $i++) {
            $this->drawPile[] = new Card(Faction::Liberal);
        }
        for ($i = 0 ; $i < 11 ; $i++) {
            $this->drawPile[] = new Card(Faction::Fascist);
        }
        $this->shuffle();
    }

    private function shuffle(){
        $this->drawPile = array_merge($this->drawPile,$this->discardPile);
        shuffle($this->drawPile);
    }

    /**
     * @param $num
     * @return Card[]
     */
    public function draw($num) {
        if(count($this->drawPile) <= $num) {
            $this->shuffle();
        }
        $cards = [];
        while($num--) {
            $cards[] = array_shift($this->drawPile);
        }
        SecretHitler::sendCardCount();
        return $cards;
    }

    /**
     * @param $num
     * @return Card[]
     */
    public function peak($num) {
        $peaks = [];
        if(count($this->drawPile) <= $num) {
            $this->shuffle();
        }

        for($i = 0 ; $i < $num ; $i++) {
            $peaks[] = $this->drawPile[$i];
        }

        return $peaks;
    }

    public function discard(Card $card) {
        $this->discardPile[] = $card;
        SecretHitler::sendCardCount();
    }

    /**
     * @return int
     */
    public function getDrawCount(){
        return count($this->drawPile);
    }

    public function getDiscardCount(){
        return count($this->discardPile);
    }

}