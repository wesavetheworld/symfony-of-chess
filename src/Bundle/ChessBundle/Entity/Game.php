<?php

namespace Bundle\ChessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

// JOHANS MAMP Pro STRUL // date_default_timezone_set("Europe/Berlin");

/**
 * 
 * @ORM\Entity(repositoryClass="Bundle\ChessBundle\Repository\ChessRepository")
 * @ORM\Table(name="game")
 * @ORM\HasLifecycleCallbacks()
 */

class Game {

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $gameid;
	

	 /**
     * @ORM\ManyToOne(targetEntity="Player",inversedBy="games")
     * @ORM\JoinColumn(name="playerid", referencedColumnName="playerid")
     */
    protected $game;

	/**
	 * @ORM\Column(type="string", length="40")
	 */
	
	protected $player1;
	
	/**
	 * @ORM\Column(type="string", length="40")
	 */
	protected $player2;
	
	/**
	 * @ORM\Column(type="string", length="1")
	 */
	protected $turn;

	/**
	 * @ORM\Column(type="array")
	 */
	protected $gameboard;
	
	/**
	 * @ORM\Column(type="array")
	 */
	protected $whitedraws;
	
	/**
	 * @ORM\Column(type="array")
	 */
	protected $blackdraws;
	
	/**
	 * @ORM\Column(type="array")
	 */
	protected $hitpieces;
	
	/**
	 * @ORM\prePersist
	 * @ORM\Column(type="datetime")
	*/
	protected $started;

	/**
	 * @ORM\Column(type="datetime")
	*/
	protected $ended;
	
	public function __construct(){

	}
	
	public function createGame($p1,$p2){
		$this -> player1 = $p1;
		$this -> player2 = $p2;
		$this -> turn = "w";
		$this -> started = new \DateTime();
		$this -> ended = new \DateTime();
		
		$this -> gameboard = array(
			'a8' => 9820,
			'b8' => 9822,
			'c8' => 9821,
			'd8' => 9819,
			'e8' => 9818,
			'f8' => 9821,
			'g8' => 9822,
			'h8' => 9820,
			
			'a7' => 9823,
			'b7' => 9823,
			'c7' => 9823,
			'd7' => 9823,
			'e7' => 9823,
			'f7' => 9823,
			'g7' => 9823,
			'h7' => 9823,
			
			'a6' => 0,
			'b6' => 0,
			'c6' => 0,
			'd6' => 0,
			'e6' => 0,
			'f6' => 0,
			'g6' => 0,
			'h6' => 0,
			
			'a5' => 0,
			'b5' => 0,
			'c5' => 0,
			'd5' => 0,
			'e5' => 0,
			'f5' => 0,
			'g5' => 0,
			'h5' => 0,
			
			'a4' => 0,
			'b4' => 0,
			'c4' => 0,
			'd4' => 0,
			'e4' => 0,
			'f4' => 0,
			'g4' => 0,
			'h4' => 0,
			
			'a3' => 0,
			'b3' => 0,
			'c3' => 0,
			'd3' => 0,
			'e3' => 0,
			'f3' => 0,
			'g3' => 0,
			'h3' => 0,

			'a2' => 9817,
			'b2' => 9817,
			'c2' => 9817,
			'd2' => 9817,
			'e2' => 9817,
			'f2' => 9817,
			'g2' => 9817,
			'h2' => 9817,

			'a1' => 9814,
			'b1' => 9816,
			'c1' => 9815,
			'd1' => 9813,
			'e1' => 9812,
			'f1' => 9815,
			'g1' => 9816,
			'h1' => 9814,
		);
		
		
	}	

    /**
     * Get gameid
     *
     * @return integer 
     */
    public function getGameid()
    {
        return $this->gameid;
    }

    /**
     * Set player1
     *
     * @param string $player1
     */
    public function setPlayer1($player1)
    {
        $this->player1 = $player1;
    }

    /**
     * Get player1
     *
     * @return string 
     */
    public function getPlayer1()
    {
        return $this->player1;
    }

    /**
     * Set player2
     *
     * @param string $player2
     */
    public function setPlayer2($player2)
    {
        $this->player2 = $player2;
    }

    /**
     * Get player2
     *
     * @return string 
     */
    public function getPlayer2()
    {
        return $this->player2;
    }

    /**
     * Set gameboard
     *
     * @param array $gameboard
     */
    public function setGameboard($gameboard)
    {
        $this->gameboard = $gameboard;
    }

    /**
     * Get gameboard
     *
     * @return array 
     */
    public function getGameboard()
    {
        return $this->gameboard;
    }

    /**
     * Set whitedraws
     *
     * @param array $whitedraws
     */
    public function setWhitedraws($whitedraws)
    {
        $this->whitedraws = $whitedraws;
    }

    /**
     * Get whitedraws
     *
     * @return array 
     */
    public function getWhitedraws()
    {
        return $this->whitedraws;
    }

    /**
     * Set blackdraws
     *
     * @param array $blackdraws
     */
    public function setBlackdraws($blackdraws)
    {
        $this->blackdraws = $blackdraws;
    }

    /**
     * Get blackdraws
     *
     * @return array 
     */
    public function getBlackdraws()
    {
        return $this->blackdraws;
    }

    /**
     * Set started
     *
     * @param datetime $started
     */
    public function setStarted($started)
    {
        $this->started = $started;
    }

    /**
     * Get started
     *
     * @return datetime 
     */
    public function getStarted()
    {
        return $this->started;
    }

    /**
     * Set ended
     *
     * @param datetime $ended
     */
    public function setEnded($ended)
    {
        $this->ended = $ended;
    }

    /**
     * Get ended
     *
     * @return datetime 
     */
    public function getEnded()
    {
        return $this->ended;
    }

    /**
     * Set turn
     *
     * @param string $turn
     */
    public function setTurn($turn)
    {
        $this->turn = $turn;
    }

    /**
     * Get turn
     *
     * @return string 
     */
    public function getTurn()
    {
        return $this->turn;
    }

    /**
     * Set hitpieces
     *
     * @param array $hitpieces
     */
    public function setHitpieces($hitpieces)
    {
        $this->hitpieces = $hitpieces;
    }

    /**
     * Get hitpieces
     *
     * @return array 
     */
    public function getHitpieces()
    {
        return $this->hitpieces;
    }

    /**
     * Set game
     *
     * @param Bundle\ChessBundle\Entity\Blog $game
     */
    public function setGame(\Bundle\ChessBundle\Entity\Blog $game)
    {
        $this->game = $game;
    }

    /**
     * Get game
     *
     * @return Bundle\ChessBundle\Entity\Blog 
     */
    public function getGame()
    {
        return $this->game;
    }
}