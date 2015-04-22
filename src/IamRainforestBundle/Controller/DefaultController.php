<?php

namespace IamRainforestBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

use IamRainforestBundle\Component\MapWidget;
use IamRainforestBundle\Entity\Square;

class DefaultController extends Controller {
    /**
     * @Route("/", name="_rainforest")
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();
        
        $mapRepository = $em->getRepository('IamRainforestBundle:Map');
        $mapEntity = $mapRepository->find(1); //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! find map with id=1 !!!!!!!!!!!!!!!!!!!!!!!!!!!!

        $squaresRepository = $em->getRepository('IamRainforestBundle:Square');
        $squareEntities = $squaresRepository->findBy(array('map' => $mapEntity));
        
        $squares = $this->convertSquaresToJavaArray($squareEntities);
        $map = new MapWidget($mapEntity, $squareEntities);

        $isGuest = false; //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! is logged in !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        
        return $this->render('IamRainforestBundle:Default:index.html.twig', array(
            'mapEntity' => $mapEntity,
            'map' => $map,
            'squares' => $squares,
            'isGuest' => $isGuest
        ));
    }
    
    private function convertSquaresToJavaArray(array $squares = null) {
        $squaresAsString = '[';
        foreach ($squares as $key => $square) {
            if ($key < count($squares) - 1) {
                $squaresAsString .= $square->getCoordX() . ',' . $square->getCoordY() . ',';
            }
            else {
                $squaresAsString .= $square->getCoordX() . ',' . $square->getCoordY();
            }
        }
        return $squaresAsString . ']';
    }

    
    /**
     * @Route("/squareContent", name="_squareContent")
     */
    public function squareContentAction(Request $request) {
        $result = null;
        if ($request->request->get('squareSelected')) {
            $result = $this->manageClickedSquare($request->request);
        }
        return new JsonResponse($result);
    }
    
    private function manageClickedSquare($post) {
        $message = null;
        $userCanBuyThisSquare = false;
        
        $em = $this->getDoctrine()->getManager();

        $mapRepository = $em->getRepository('IamRainforestBundle:Map');
        $mapEntity = $mapRepository->find($post->get('mapId'));
        
        $squaresRepository = $em->getRepository('IamRainforestBundle:Square');
        $squareEntity = $squaresRepository->findOneBy(array(
            'map' => $mapEntity,
            'coordX' => $post->get('coordX'),
            'coordY' => $post->get('coordY')
        ));
        if (isset($squareEntity)) {
            $message = $this->renderContentForBought($squareEntity);
        }
        else {
            $result = $this->renderContentForVisitor($mapEntity, $em, $post);
            $message = $result['message'];
            $userCanBuyThisSquare = $result['userCanBuyThisSquare'];
        }

        return array(
            'message' => $message,
            'coordX' => $post->get('coordX'),
            'coordY' => $post->get('coordY'),
            'userCanBuyThisSquare' => $userCanBuyThisSquare
        );
    }

    function renderContentForVisitor($mapEntity, $em, $post) {
        $message = null;
        $userCanBuyThisSquare = false;

        $userEntity = $em->getRepository('IamRainforestBundle:User')->findOneBy(array('id' => 1));

        if (isset($userEntity)) {//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! && is logged in !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
             $result = $this->renderContentForLoggedIn($userEntity, $mapEntity, $em, $post);
             $message = $result['message'];
             $userCanBuyThisSquare = $result['userCanBuyThisSquare'];
        }
        else {
            $message = $this->renderRegistrationForm();
        }

        return array(
            'message' => $message,
            'userCanBuyThisSquare' => $userCanBuyThisSquare
        );
    }
    
    function renderContentForLoggedIn($userEntity, $mapEntity, $em, $post) {
        $message = null;
        
        $squaresRepository = $em->getRepository('IamRainforestBundle:Square');
        $squareEntity = $squaresRepository->findOneBy(array(
            'map' => $mapEntity,
            'user' => $userEntity
        ));
        
        if (isset($squareEntity)) {
            $message = $this->renderContentForUserWithBought($squareEntity);
            $userCanBuyThisSquare = false;
        }
        else {
            $message = $this->renderContentForFreeSquare($post);
            $userCanBuyThisSquare = true;
        }
        return array(
            'message' => $message,
            'userCanBuyThisSquare' => $userCanBuyThisSquare
        );
    }
    
    
    /**
     * @Route("/squareFinder", name="_squareFinder")
     */
    public function squareFinderAction(Request $request) {
        $result = null;
        if ($request->request->has('map-id')) {
            $result = $this->findSquareByEmail($request->request);
        }
        return new JsonResponse($result);
    }
    
    private function findSquareByEmail($post) {
        if ($this->eMailIsCorrect($post)) {
            return $this->findSquareForVisitor($post);
        }
        else {
            return array(
                'success' => false,
                'message' => 'Bitte gib eine richtige E-Mail Adresse ein.'
            );
        }
    }
    
    private function eMailIsCorrect($post) {
        $validator = $this->container->get('validator');
        $constraints = array(new Email(), new NotBlank());
        $errors = $validator->validate($post->get('email'), $constraints);
        return count($errors) === 0;
    }
    
    private function findSquareForVisitor($post) {
        $message = null;
        $coordX = null;
        $coordY = null;

        $em = $this->getDoctrine()->getManager();
        $userEntity = $em->getRepository('IamRainforestBundle:User')->findOneBy(array('email' => $post->get('email')));

        if (isset($userEntity)) {
            $square = $this->findSquareForRegistered($userEntity, $em, $post);

            $message = $square['message'];
            $coordX = $square['coordX'];
            $coordY = $square['coordY'];
            $userHasBoughtSquare = $square['userHasBoughtSquare'];
        }
        else {
            $message = $this->renderRegistrationForm();
            $userHasBoughtSquare = false;
        }
        return array(
            'success' => true,
            'message' => $message,
            'userHasBoughtSquare' => $userHasBoughtSquare,
            'coordX' => $coordX,
            'coordY' => $coordY
        );
    }
    
    private function findSquareForRegistered($userEntity, $em, $post) {
        $message = null;
        $coordX = null;
        $coordY = null;

        $mapRepository = $em->getRepository('IamRainforestBundle:Map');
        $mapEntity = $mapRepository->find($post->get('map-id'));

        $squaresRepository = $em->getRepository('IamRainforestBundle:Square');
        $squareEntity = $squaresRepository->findOneBy(array(
            'map' => $mapEntity,
            'user' => $userEntity
        ));
        if (isset($squareEntity)) {
            $message = $this->renderContentForBought($squareEntity);
            $coordX = $squareEntity->getCoordX();
            $coordY = $squareEntity->getCoordY();
            $userHasBoughtSquare = true;
        }
        else if (true){//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! && is logged in !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
            $message = $this->renderContentForFreeSquare(null);
            $userHasBoughtSquare = false;
        }
        else {
            $message = $this->renderRegistrationForm();
            $userHasBoughtSquare = false;
        }
        return array(
            'message' => $message,
            'coordX' => $coordX,
            'coordY' => $coordY,
            'userHasBoughtSquare' => $userHasBoughtSquare
        );
    }
    
    
    /**
     * @Route("/squareSaver", name="_squareSaver")
     */
    public function squareSaverAction(Request $request) {
        $result = null;
        if ($request->request->get('squareForm')) {
            $result = $this->manageUserBuysSquare($request->request);
        }
        return new JsonResponse($result);
    }
    
    private function manageUserBuysSquare($post) {
        try {
            $message = $this->saveSquareToUser($post);
            return array(
                'success' => true,
                'message' => $message,
                'coordX' => $post->get('coordX'),
                'coordY' => $post->get('coordY'));
        } catch (Exception $ex) {
            return array(
                'success' => false,
                'message' => 'Die Speicherung der Daten ist fehlgeschlagen. Melde dich am besten bei uns!');
        }
    }
    
    private function saveSquareToUser($post) {
        $em = $this->getDoctrine()->getManager();

        $mapRepository = $em->getRepository('IamRainforestBundle:Map');
        $mapEntity = $mapRepository->find($post->get('mapId'));
        $userEntity = $em->getRepository('IamRainforestBundle:User')->findOneBy(array('id' => 1));
        
        $squareEntity = new Square();

        $squareEntity->setMap($mapEntity);
        $squareEntity->setUser($userEntity);
        $squareEntity->setCoordX($post->get('coordX'));
        $squareEntity->setCoordY($post->get('coordY'));
        $squareEntity->setPersonalText($post->get('personalText'));

        $em->persist($squareEntity);
        $em->flush();
        
        return 'Danke, dass du uns mit der Rettung des Regenwaldes unterstützt!';
    }
    
    
    /**
     * @Route("/updateCounter", name="_updateCounter")
     */
    public function updateCounterAction() {
        $em = $this->getDoctrine()->getManager();
        
        $mapRepository = $em->getRepository('IamRainforestBundle:Map');
        $mapEntity = $mapRepository->find(1); //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! find map with id=1 !!!!!!!!!!!!!!!!!!!!!!!!!!!!

        $squaresRepository = $em->getRepository('IamRainforestBundle:Square');
        $squareEntities = $squaresRepository->findBy(array('map' => $mapEntity));

        $map = new MapWidget($mapEntity, $squareEntities);

        return new JsonResponse(array('numberOfRemainingSquares' => $map->getRemainingFreeSquares()));
    }
    
    private function renderRegistrationForm() {
        return 'Falls du einen Baum retten willst<br> !!! LINK iamgreen !!!';
    }
    
    private function renderContentForBought($squareEntity) {
        $text = '<div class="container-fluid"><div class="row"><div class="col-xs-12">';
        $text .= '<p>Dieser Baum wurde am ' . $squareEntity->getBoughtAt()->format('Y-m-d') . ' gerettet.<br><br>Des Retters Kommentar:</p>';
        $text .= '<p style="border: 1px solid #aaa; border-radius: 4px;">' . $squareEntity->getPersonalText() . '</p>';
        return $text . '</div></div></div>';
    }

    private function renderContentForFreeSquare($post) {
        $text = '<div class="container-fluid"><div class="row"><div class="col-xs-12">';
        if (isset($post)) {
            $text .= '<form id="square-form">'
                    . '<p>Rette diesen Baum und hinterlasse deine nachhaltige Meinung.<br><br>Dein Kommentar:</p>'
                    . '<input type="hidden" name="coordX" value="' . $post->get('coordX') . '">'
                    . '<input type="hidden" name="coordY" value="' . $post->get('coordY') . '">'
                    . '<textarea rows="3" maxlength="254" style="width: 100%;"></textarea>'
                    . '</form>';
        }
        else {
            $text .= '<p>Rette einen Baum!<br><br>Klicke auf ein freies Feld und hinterlasse deine nachhaltige Meinung.';
        }
        return $text . '</div></div></div>';
    }
    
    private function renderContentForUserWithBought($squareEntity) {
        $text = '<div class="container-fluid"><div class="row"><div class="col-xs-12">';

        $text .= '<p>Hallo du hast am ' . $squareEntity->getBoughtAt()->format('Y-m-d') . ' schon ein Baum gerettet.<br><br>Dein Kommentar:</p>';
        $text .= '<p style="border: 1px solid #aaa; border-radius: 4px;">' . $squareEntity->getPersonalText() . '</p>';

        return $text . '</div></div></div>';
    }
}
