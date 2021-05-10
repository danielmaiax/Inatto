/* @robot_full_class Inatto\Web\Site\ScreenSize */
/* @robot_update true */

$(function () {

    function class__reportWindowSize() {
        let w = window.innerWidth;
        let h = window.innerHeight;
        let x = document.getElementById("_id_");
        x.innerHTML = "" + w + " x " + h + "";
    }

    window.onresize = class__reportWindowSize;
    class__reportWindowSize();

});