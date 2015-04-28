<?php

namespace IamRainforestBundle\Component;

use Doctrine\ORM\EntityManager;
use IamRainforestBundle\Entity\Map;
use IamRainforestBundle\Entity\Square;

class MapManager {
    private $_em;
    
    public function __construct(EntityManager $entityManager) {
        $this->_em = $entityManager;
    }
    
    public function findMapBySlug($slug) {
        $mapRepository = $this->_em->getRepository('IamRainforestBundle:Map');
        return $mapRepository->findOneBy(array('slug' => $slug));
    }

    public function findMapById($id) {
        $mapRepository = $this->_em->getRepository('IamRainforestBundle:Map');
        return $mapRepository->findOneBy(array('id' => $id));
    }

    public function findAllSquaresIn(Map $map) {
        $squaresRepository = $this->_em->getRepository('IamRainforestBundle:Square');
        return $squaresRepository->findBy(array('map' => $map));
    }

    public function findSquareAt(Map $map, $coordX, $coordY) {
        $squaresRepository = $this->_em->getRepository('IamRainforestBundle:Square');
        return $squaresRepository->findOneBy(array(
            'map' => $map,
            'coordX' => $coordX,
            'coordY' => $coordY
        ));
    }
    
    public function findSquareWithUserId(Map $map, $id) {
        $squaresRepository = $this->_em->getRepository('IamRainforestBundle:Square');
        return $squaresRepository->findOneBy(array(
            'map' => $map,
            'userId' => $id
        ));
    }
    
    public function saveNew(Square $square) {
        $this->_em->persist($square);
        $this->_em->flush();
    }
}
