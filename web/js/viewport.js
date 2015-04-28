/* global diffX, diffY, numberX, numberY, gridLineWidth, squareContainer, in index.html.twig */

var gridViewport = $('#grid-viewport');
var gridContainer = $('#grid-container');
var gridContainerLeft = 0;
var gridContainerTop = 0;
var gridMaxOffsetX = 0;
var gridMaxOffsetY = 0;
var viewportWidthInSquares = 0;
var viewportHeightInSquares = 0;


window.addEventListener('resize', resizeGridViewportOnBrowserResize );
function resizeGridViewportOnBrowserResize() {
    resizeGridViewport();
    offsetLeftViewport = gridViewport.offset().left;
    offsetTopViewport = gridViewport.offset().top;
    widthViewport = gridViewport.outerWidth();
    clearViewportSelection();
    gridContainerLeft = 0;
    gridContainerTop = 0;
    gridContainer.removeAttr('style');
};
function clearViewportSelection() {
    clearMarker();
    squareContainer.fadeOut('fast');
};

resizeGridViewport();

function resizeGridViewport() {
    var bodyWidth = $('body').width();
    if (bodyWidth <= 767) {
        setUpGridViewport(bodyWidth * 0.95);
    }
    else if (bodyWidth <= 991) {
        setUpGridViewport(parseInt(bodyWidth * 0.9));
    }
    else if (bodyWidth <= 1199) {
        setUpGridViewport(parseInt(bodyWidth * 0.8));
    }
    else {
        setUpGridViewport(parseInt(bodyWidth * 0.7));
    }
    viewportWidthInSquares = Math.floor(gridViewport.width() / diffX);
    viewportHeightInSquares = Math.floor(gridViewport.height() / diffY);
    gridMaxOffsetX = numberX - viewportWidthInSquares;
    gridMaxOffsetY = numberY - viewportHeightInSquares;
};
function setUpGridViewport(width) {
    gridViewport.width(biggestGridSize(width, diffX, numberX));
    gridViewport.height(biggestGridSize(gridViewport.width(), diffY, numberY));
};
function biggestGridSize(width, squareSize, squareNumber) {
    var ratio = Math.floor(width / squareSize);
    if (ratio >= squareNumber) {
        ratio = squareNumber;
    }
    return ratio * squareSize + gridLineWidth;
};

$('#goLeft').click(function() {
    gridContainerLeft = raiseIfInsideBoundingBox(gridContainerLeft, gridMaxOffsetX);
    moveGridContainerToLeft(gridContainerLeft);
});
$('#goRight').click(function() {
    gridContainerLeft = decreaseIfInsideBoundingBox(gridContainerLeft, 0);
    moveGridContainerToLeft(gridContainerLeft);
});
$('#goDown').click(function() {
    gridContainerTop = raiseIfInsideBoundingBox(gridContainerTop, gridMaxOffsetY);
    moveGridContainerToTop(gridContainerTop);
});
$('#goUp').click(function() {
    gridContainerTop = decreaseIfInsideBoundingBox(gridContainerTop, 0);
    moveGridContainerToTop(gridContainerTop);
});
function raiseIfInsideBoundingBox(offset, border) {
    return offset + 1 > border ? offset : offset + 1;
};
function decreaseIfInsideBoundingBox(offset, border) {
    return offset - 1 < border ? offset : offset - 1;
};
function moveGridContainerToLeft(absoluteDistance) {
    var offsetInPixel = -absoluteDistance * diffX;
    gridContainer.css('left', offsetInPixel + 'px');
    clearViewportSelection();
};
function moveGridContainerToTop(absoluteDistance) {
    var offsetInPixel = -absoluteDistance * diffY;
    gridContainer.css('top', offsetInPixel + 'px');
    clearViewportSelection();
};