<?php

namespace IamRainforestBundle\Component;


class MapWidget {

    private $_mapAsString = '';
    
    private $_squareHeight = 0;
    
    private $_squareWidth = 0;
    
    private $_remainingFreeSquares = 0;
    
    public function __construct(\IamRainforestBundle\Entity\Map $map = null, array $squares = null) {
        $this->_squareWidth = floor($map->getWidth() / $map->getNumberX());
        $this->_squareHeight = floor($map->getHeight() / $map->getNumberY());

        for ($indexY = 1; $indexY <= $map->getNumberY(); ++$indexY ) {
            $this->_mapAsString .= '<div class="row no-margin">';
            for ($indexX = 1; $indexX <= $map->getNumberX(); ++$indexX) {
                $this->_mapAsString .= '<div id="' . $indexY . '-' . $indexX . '" class="col-base squareSize"></div>';
            }
            $this->_mapAsString .= '</div>';
        }
        
        $this->_remainingFreeSquares = $map->getNumberX() * $map->getNumberY() - count($squares);
    }
    
    public function getGridOfSquaresAsDivs() {
        return $this->_mapAsString;
    }
    
    public function getSquareWidth() {
        return $this->_squareWidth;
    }
    
    public function getSquareHeight() {
        return $this->_squareHeight;
    }
    
    public function getRemainingFreeSquares() {
        return $this->_remainingFreeSquares;
    }
}