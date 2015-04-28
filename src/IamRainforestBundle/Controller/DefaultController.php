<?php

namespace IamRainforestBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use IamRainforestBundle\Component\MapWidget;
use IamRainforestBundle\Entity\Square;

class DefaultController extends Controller {
    /**
     * @Route("/", name="_rainforest")
     */
    public function indexAction(Request $request) {
        if ($request->query->has('map')) {
            return $this->createSiteWithMapFrom($request->query);
        }
        else throw new \Exception('Wrong site called, check for slug');
    }
    
    private function createSiteWithMapFrom($get) {
        $mapManager = $this->get('mapManager');
        
        $mapEntity = $mapManager->findMapBySlug($get->get('map'));

        if (isset($mapEntity)) {
            return $this->renderSiteForVisitor($get, $mapManager, $mapEntity);
        }
        else throw new \Exception('The searched map: ' . $get->get('map') . ' does not exist.');
    }
    
    private function renderSiteForVisitor($get, $mapManager, $mapEntity) {
        $squareEntities = $mapManager->findAllSquaresIn($mapEntity);

        $squares = $this->convertSquaresToJavaArray($squareEntities);
        $map = new MapWidget($mapEntity, $squareEntities);

        return $this->render('IamRainforestBundle:Default:index.html.twig', array(
            'mapEntity' => $mapEntity,
            'map' => $map,
            'squares' => $squares,
            'userId' => $get->get('user')
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
        
        $mapManager = $this->get('mapManager');
        $mapEntity = $mapManager->findMapById($post->get('mapId'));

        $squareEntity = $mapManager->findSquareAt(
            $mapEntity,
            $post->get('coordX'),
            $post->get('coordY')
        );
        if (isset($squareEntity)) {
            $message = $this->renderContentForBought($squareEntity);
        }
        else {
            $result = $this->renderContentForVisitor($mapEntity, $mapManager, $post);
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

    private function renderContentForVisitor($mapEntity, $mapManager, $post) {
        $message = null;
        $userCanBuyThisSquare = false;

        $user = $this->decryptUserIdFrom($post->get('userId'));

        if (isset($user)) {
            $squareEntity = $mapManager->findSquareWithUserId($mapEntity, $user);
            $result = $this->renderContentForLoggedInUser($squareEntity, $post);
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
    
    private function decryptUserIdFrom($userIdPost) { //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! encrypt decrypt user id !!!!!!!!!!!!!!
        if (isset($userIdPost) && $userIdPost !== '') {
            return $userIdPost;
        }
        else {
            return null;
        }
    }
    
    private function renderContentForLoggedInUser($squareEntity, $post) {
        $message = null;
        
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
            $result = $this->findSquareForRegisteredUser($request->request);
        }
        return new JsonResponse($result);
    }
    
    private function findSquareForRegisteredUser($post) {
        $message = null;
        $coordX = null;
        $coordY = null;

        $mapManager = $this->get('mapManager');
        $mapEntity = $mapManager->findMapById($post->get('map-id'));

        $user = $this->decryptUserIdFrom($post->get('user-id'));
        
        $squareEntity = $mapManager->findSquareWithUserId($mapEntity, $user);
        if (isset($squareEntity)) {
            $message = $this->renderContentForBought($squareEntity);
            $coordX = $squareEntity->getCoordX();
            $coordY = $squareEntity->getCoordY();
            $userHasBoughtSquare = true;
        }
        else {
            $message = $this->renderContentForFreeSquare(null);
            $userHasBoughtSquare = false;
        }
        return array(
            'success' => true,
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
        $mapManager = $this->get('mapManager');
        $mapEntity = $mapManager->findMapById($post->get('mapId'));
        $user = $this->decryptUserIdFrom($post->get('userId'));
        
        $squareEntity = new Square();

        $squareEntity->setMap($mapEntity);
        $squareEntity->setUserId($user);
        $squareEntity->setCoordX($post->get('coordX'));
        $squareEntity->setCoordY($post->get('coordY'));
        $squareEntity->setPersonalText($post->get('personalText'));

        $mapManager->saveNew($squareEntity);
        
        return 'Danke, dass du uns mit der Rettung des Regenwaldes unterstützt!';
    }
    
    
    /**
     * @Route("/updateCounter", name="_updateCounter")
     */
    public function updateCounterAction(Request $request) {
        $result = null;
        if ($request->request->get('updateCounter')) {
            $result = $this->manageCounterReset($request->request);
        }
        return new JsonResponse($result);
    }
    
    private function manageCounterReset($post) {
        $mapManager = $this->get('mapManager');
        $mapEntity = $mapManager->findMapById($post->get('mapId'));

        $squareEntities = $mapManager->findAllSquaresIn($mapEntity);

        $map = new MapWidget($mapEntity, $squareEntities);

        return array('numberOfRemainingSquares' => $map->getRemainingFreeSquares());
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
