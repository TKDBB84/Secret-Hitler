<?php
/**
 * Created by PhpStorm.
 * User: Jeffrey
 * Date: 2/18/2016
 * Time: 7:37 PM.
 */
namespace Game;

use Ratchet\ConnectionInterface;

class Request
{
    /** @var string */
    public $function = '';
    /** @var string[] */
    public $arguments = [];
    /** @var Player */
    public $requester;

    /**
     * Request constructor.
     *
     * @param $from ConnectionInterface
     * @param $jsonString string
     */
    public function __construct(ConnectionInterface $from, $jsonString)
    {
        $jsonObject = json_decode($jsonString, true);
        $this->function = $jsonObject['function'];
        $this->arguments = $jsonObject['arguments'];
        $this->requester = SecretHitler::getPlayerByID($from->resourceId);
    }
}
