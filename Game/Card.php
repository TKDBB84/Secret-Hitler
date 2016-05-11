<?php
/**
 * Created by PhpStorm.
 * User: Jeffrey
 * Date: 2/15/2016
 * Time: 9:06 PM.
 */
namespace Game;

class Card implements \JsonSerializable
{
    /** @var int */
    private $faction;

    /**
     * Card constructor.
     *
     * @param $cardType int
     */
    public function __construct($cardType)
    {
        $this->faction = $cardType;
    }

    /**
     * @return bool
     */
    public function isLiberal()
    {
        return $this->faction == 0;
    }

    /**
     * @return bool
     */
    public function isFascist()
    {
        return $this->faction == 1;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->faction;
    }

    public function jsonSerialize()
    {
        return $this->faction;
    }
}
