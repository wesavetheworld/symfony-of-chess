<?php
// src/Bundle/ChessBundle/Controller/GameController.php

namespace Bundle\ChessBundle\Controller;

use Symfony\Component\Security\Core\SecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route; 
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Bundle\ChessBundle\Entity\Move;
use Bundle\ChessBundle\Entity\Game;
use Bundle\ChessBundle\Entity\Player;
use Bundle\ChessBundle\Entity\Friend;
use Bundle\ChessBundle\Form\EnquiryType;
use Bundle\ChessBundle\Form\EnquiryType2;

class GameController extends Controller
{
	private $current_move;
	private $gameid;
	
	public function indexAction($message1="",$message2=""){

	        $request = $this->getRequest();
	        $session = $request->getSession();
			
	
	        // get the login error if there is one
	        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
	            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
	        } else {
	            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
	        }
	
			$player = new Player();
		    $form = $this->createForm(new EnquiryType(), $player);
		    if ($request->getMethod() == 'POST') {
		        $form->bindRequest($request);
		        if ($form->isValid()) {
		            // Perform some action, such as sending an email
		
		            // Redirect - This is important to prevent users re-posting
		            // the form if they refresh the page
		            return $this->redirect($this->generateUrl('BundleChessBundle_loggedin'));
		        }
		    }

			$player2 = new Player();		
		    $form2 = $this->createForm(new EnquiryType(), $player2);
		    if ($request->getMethod() == 'POST') {
		        $form2->bindRequest($request);
		        if ($form2->isValid()) {
		            // Perform some action, such as sending an email
		
		            // Redirect - This is important to prevent users re-posting
		            // the form if they refresh the page
		            return $this->redirect($this->generateUrl('BundleChessBundle_loggedin'));
		        }
		    }

	    	return $this->render('BundleChessBundle::index.html.twig', array(
	        	'form' => $form->createView() ,
	        	'form2' => $form2->createView() , 
		        'error' => $error ,
		        'message1' => $message1 ,
		        'message2' => $message2
    		));

        //return $this->render('BundleChessBundle::index.html.twig');
    }
	
    public function multiplayerAction(){
    	//print_r($_POST);
		$gameid = $_POST['gameid'];
		$p2 = $_POST['player'];
		$color = 'b';
		
    	$em = $this -> getDoctrine()-> getEntityManager();
		$game = $em -> getRepository('BundleChessBundle:Game')
				    -> getGame($gameid);

		$game -> setPlayer2($p2);
		$p1 = $game -> getPlayer1();		
		$em -> persist($game);
		$em -> flush();
		
		$local = 0;    
		return $this->forward('BundleChessBundle:Game:render', array(
	    		'p1' => $p1,
	    		'p2' => $p2,
	    		'gameid' => $gameid,
	    		'color' => $color,
	    		'loggedinas' =>	$p2,
	    		'local' => $local
			));
	}
	
    public function gameAction(){
    	//print_r($_POST);
		if(isset($_POST['submitFriend'])){
			$p1 = $_POST['player1'];
			$p2 = $_POST['players']['player2'];
			$local = 1;
		}else if(isset($_POST['submitPending'])){
			$p1 = $_POST['player1'];
			$p2 = 'Waiting...';
			$local = 0;
		}
		
		$color = 'w';
		$current_game = new Game();
		$current_game -> createGame($p1, $p2); 

		$em = $this -> getDoctrine()-> getEntityManager();
		$em -> persist($current_game);
		$em -> flush();
        
        $this -> gameid = $current_game -> getGameid();
		
		$em = $this -> getDoctrine()-> getEntityManager();
		$playerdb = $em -> getRepository('BundleChessBundle:Player')
				        -> getPlayer($p1);
		
		$playerdb -> setPendinggame($this -> gameid);
		$playerdb -> setLoginstatus(2);
		$em -> persist($playerdb);
		$em -> flush();
		return $this->forward('BundleChessBundle:Game:render', array(
	    		'p1' => $p1,
	    		'p2' => $p2,
	    		'gameid' => $this -> gameid,
	    		'color' => $color,
	    		'loggedinas' =>	$p1,
	    		'local' => $local
			));
	}

	public function renderAction($p1,$p2,$gameid,$color,$loggedinas,$local){
    return $this -> render('BundleChessBundle:Game:index.html.twig', array(
        	'player1' => $p1,
        	'player2' => $p2,
        	'gameid' => $gameid,
        	'color' => $color,
        	'loggedinas' =>	$loggedinas,
        	'local' => $local
    	));    
	}
    
	
    public function moveAction($slug) {
		
		$gameid = substr($slug, 5); 
		$slug = substr($slug, 0, 5);

		$em = $this -> getDoctrine()-> getEntityManager();
		
		$game = $em -> getRepository('BundleChessBundle:Game')
				    -> getGame($gameid);
					
		$gameboard = $game -> getGameboard();
		$turn = $game -> getTurn(); 	 
		
		// move objekt, kolla moven	
		$this -> current_move = new Move($gameboard,$turn,$slug);
		//returnerar en kod som avgör hur slaget gått igenom
		$move_var = $this -> current_move -> move();
		//giltigt drag, 101 betyder krönt bonde
		if (($move_var === 100) || ($move_var === 101)) {
			
			//1. börja med att se om draget slår ut annan pjäs och lista den (för persistens)
			if($x_piece = $this -> current_move -> checkHit($slug)){ //kolla vilken pjäs som blir utslagen
				if(!$hitpieces = $game -> getHitpieces()){ //hämta ut hitpieces arrayen om finns
					$hitpieces = array(); //annars gör en ny array
				}
				
				$hitpieces[] = $x_piece;  //fyll på med den utslagna pjäsen
				$game -> setHitpieces($hitpieces);
			}				
			
			//2. byt färg och fyll på drag-listorna
			$slugx = $this -> current_move -> checkX($slug); 
			if($turn == 'w') {
				$turn = 'b';
				//Här under fyller vi på whitedraws-listan med det vita draget
				if(!$whitedraws = $game -> getWhitedraws()){
					$whitedraws = array(); //annars gör en ny array
				}
				$whitedraws[] = $this->current_move->getPiece($slug).$slugx;  //fyll på med pjäs och drag
				$game -> setWhitedraws($whitedraws);

			}else if($turn == 'b'){
				$turn = 'w';
				//Här under fyller vi på blackdraws-listan med det svarta draget
				if(!$blackdraws = $game -> getBlackdraws()){
					$blackdraws = array(); //annars gör en ny array
				}
				$blackdraws[] = $this->current_move->getPiece($slug).$slugx;  //fyll på med draget
				$game -> setBlackdraws($blackdraws);
			}
			
			//3. Uppdatera board-arrayen
			if($move_var == 101){
				$updated_gameboard = $this -> current_move ->updateBoardCrown($slug, $gameboard, $turn);
				$text = "101".$slug.$turn;
			}else if($move_var == 100){
				$updated_gameboard = $this -> current_move ->updateBoard($slug, $gameboard);
				$text = $slug;
			}		
			$game -> setGameboard($updated_gameboard);
			$game -> setTurn($turn);
			
			$em -> persist($game);
			$em -> flush();
			
		}else if($move_var > 200) {
			// kommer att returnera felkod
			$text = $move_var;
		} else {
			echo "Something is wrong, movevar is: ". $move_var;
		}
		
		//här är xml:en som skickas som svar till ajax-requestet
		$response = new Response();
		$response->setContent('<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
								<response>'.$text.'</response>');
		$response->setStatusCode(200);
		$response->headers->set('Content-Type', 'text/xml');
		// prints the XML headers followed by the content
		return $response; 
	} 

    public function oldGameAction($slug){
		
	    // get last game from database
	    $em = $this -> getDoctrine()-> getEntityManager();
		
		$game = $em -> getRepository('BundleChessBundle:Game')
				    -> getGame($slug);
		
		$gameboard = $game -> getGameboard();
		$turn = $game -> getTurn(); 	 
		$player1 =  $game -> getPlayer1(); 
		$player2 =  $game -> getPlayer2();
		$gameid =  $game -> getGameid(); 

		if(!$hitpieces =  $game -> getHitpieces()){
			$hitpieces = array();
		};
		if(!$whitedraws =  $game -> getWhitedraws()){
			$whitedraws  = array();
		};
		if(!$blackdraws =  $game -> getBlackdraws()){
			$blackdraws  = array();
		};
		
		$game = array(	"gameboard" => $gameboard
						, "turn" => $turn
						, "player1" => $player1
						, "player2" => $player2
						, "gameid" => $gameid
						, "hitpieces" => $hitpieces
						, "whitedraws" => $whitedraws
						, "blackdraws" => $blackdraws);
						
		$gameboard = array_change_key_case($gameboard); 
		// $gameboard är en array av det aktuella spelet 
        $text = json_encode($game);
        
		//här är json som skickas som svar till ajax-requestet
		$response = new Response();
		$response->setContent($text);
		$response->setStatusCode(200);
		$response->headers->set('Content-Type', 'text/javascript'); 
		// prints the javascript headers followed by the content
		return $response; 	    
	}


}



