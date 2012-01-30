<?php

namespace Bundle\ChessBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ChessRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */

 class ChessRepository extends EntityRepository {

	/*public function getGameId() {
		$qb = $this -> createQueryBuilder('i') 
					-> select('*')
					-> from('game')
					-> where('gameid = '. $gameid);

		return $qb -> getQuery() -> getResult();
	}
*/	
	public function getGameid() {
		$qb = $this -> createQueryBuilder('Game') 
					-> select('Game.gameid')
					-> getQuery() 
					;

		$result = $qb -> getResult();
		//print_r($result);
		$result = count($result);
		return $result;
	}
	
	public function getGame($gameid) {
		$qb = $this -> createQueryBuilder('Game') 
					-> select('Game')
					-> where('Game.gameid = :gameid')
					-> setParameter('gameid', $gameid)
					-> getQuery() 
					;

		$result = $qb -> getResult();
		//print_r($result[0]);
		return $result[0];
		
		/*
		$result1[0] = unserialize($result[0]['gameboard']);
		$result1[1] = $result[0]['turn'];
		return $result1;
		 * */
	}

	public function updateGameboard($gameid,$gameboard) {
		$qb = $this -> createQueryBuilder('Game') 
					-> update('Game.gameboard', $gameboard)
					//-> from('Game','Game.gameboard')
					//-> where('Game.gameid = ?'. $gameid)
					;
		//$qb -> update('Game.gameboard', $gameboard);
		
	}

	public function getTurn($gameid2) {
		$qb2 = $this -> createQueryBuilder('Game') 
					 -> select('Game.turn')
					-> where('Game.gameid = :gameid')
					-> setParameter('gameid', $gameid2)
					-> getQuery() 
					;

		$result2 = $qb2 -> getResult();
		//print_r($result2);
		$result2 = $result2[0]['turn'];
		//	echo "HÄÄÄÄÄÄÄÄÄÄÄÄÄÄÄR!".$result2;
		return $result2;
	}

	public function updateTurn($turn){
		//skicka in $turn i db
	}
}