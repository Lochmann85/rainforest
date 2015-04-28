/* global gridViewport, gridContainerLeft, gridContainerTop, in viewport.js, gridLineWidth, diffX, diffY, squareContainer, in index.html.twig */

var marker = $('#marker');
var markerContext = marker[0].getContext('2d');

var offsetLeftViewport = gridViewport.offset().left;
var offsetTopViewport = gridViewport.offset().top;
function moveMarkerTo(coordX, coordY) {
    var offsetLeft = offsetLeftViewport + gridLineWidth + (coordX - 0.5 - gridContainerLeft) * diffX - marker[0].width / 2;
    var offsetTop = offsetTopViewport + gridLineWidth + (coordY - 0.25 - gridContainerTop) * diffY - marker[0].height;

    clearMarker();

    marker.attr('style',
        'left:' + offsetLeft + 'px;' + 
        'top:' + offsetTop + 'px;' +
        'display:block;'
    );

    animateMarker();
};
function moveSquareContainerTo(coordX, coordY) {
    var offsetLeft = squareXToPixel(coordX);
    var offsetTop = squareYToPixel(coordY);

    squareContainer.attr('style',
        'left:' + offsetLeft + 'px;' + 
        'top:' + offsetTop + 'px;'
    );
};
var widthViewport = gridViewport.outerWidth();
function squareXToPixel(coordX) {
    var width = $('#square-container').outerWidth();
    var offsetLeft = offsetLeftViewport + (coordX - gridContainerLeft) * diffX + marker[0].width / 2;
    if (offsetLeft >= offsetLeftViewport + widthViewport - width && offsetLeft > width + marker[0].width) {
       offsetLeft = offsetLeft - width - marker[0].width;
    }
    return offsetLeft;
};
function squareYToPixel(coordY) {
    var height = $('#square-container').outerHeight();
    var offsetTop = offsetTopViewport + (coordY - 0.5 - gridContainerTop) * diffY - height - marker[0].height;
    if (offsetTop <= offsetTopViewport) {
       offsetTop = offsetTop + height + marker[0].height + 0.5 * diffY;
    }
    return offsetTop;
};

function clearMarker() {
    markerContext.clearRect(0, 0, marker[0].width, marker[0].height);
};

var x_middle = parseInt(marker[0].width * 0.5),
    y_start = marker[0].height - 5;
function animateMarker() {
    var time = 0,
        endTime = 20,
        begin_width_2 = parseInt(marker[0].width * 0.5 * 0.7) - 1.5,
        end_width_2 = parseInt(marker[0].width * 0.5) - 1.5,
        diff_width_2 = (end_width_2 - begin_width_2) / (endTime / 3),
        diff_y_top = (parseInt(marker[0].height * 0.3) - end_width_2 - 1.5) / (endTime / 3),
        offset_y_jump = parseInt(marker[0].height * 0.1) / (endTime / 3);
    var y_bottom = y_start,
        y_top = y_start - parseInt(marker[0].height * 0.5),
        width_2 = begin_width_2;
    repeat();
    function repeat() {
        clearMarker();
        drawMarkerCircle(y_start);
        if (time < (endTime / 3)) {
            width_2 = width_2 + diff_width_2;
            y_top = y_top - diff_y_top;
        }
        else if (time < 2 * (endTime / 3)) {
            y_bottom = y_bottom - offset_y_jump;
            y_top = y_top - offset_y_jump;
        }
        else {
            y_bottom = y_bottom + offset_y_jump;
            y_top = y_top + offset_y_jump;
        }
        drawMarker(y_bottom, y_top, width_2);

        ++time;
        if (time < endTime) {
            window.requestAnimFrame(repeat);
        }
    };
};
function drawMarkerCircle(y_start) {
    drawCircle(x_middle, y_start, 4, "#E1072A", "#F82345");
};
function drawMarker(y_bottom, y_top, width_2) {
    markerContext.beginPath();
    markerContext.moveTo(x_middle, y_bottom);
    markerContext.bezierCurveTo(x_middle - width_2 * 0.5, y_top * 1.3, x_middle - width_2, y_top * 1.6, x_middle - width_2, y_top);
    markerContext.arc(x_middle, y_top, width_2, Math.PI, 0);
    markerContext.bezierCurveTo(x_middle + width_2, y_top * 1.6, x_middle + width_2 * 0.5, y_top * 1.3, x_middle, y_bottom);
    markerContext.lineWidth = 1.5;
    markerContext.strokeStyle = "#E1072A";
    markerContext.fillStyle = "#F82345";
    markerContext.fill();
    markerContext.stroke();

    drawCircle(x_middle, y_top, width_2 * 0.2, "#000", "#000");
};
function drawCircle(x, y, radius, line_color, fill_color) {
    markerContext.beginPath();
    markerContext.arc(x, y, radius, 0, 2*Math.PI);
    markerContext.lineWidth = 1.5;
    markerContext.strokeStyle = line_color;
    markerContext.fillStyle = fill_color;
    markerContext.fill();
    markerContext.stroke();
};

window.requestAnimFrame = function() {
    return window.requestAnimationFrame ||
           window.webkitRequestAnimationFrame ||
           window.mozRequestAnimationFrame ||
           window.msRequestAnimationFrame ||
           window.oRequestAnimationFrame ||
           function( callback ) {
              window.setTimeout(callback, 1000/60);
           };
}();

