$(document).ready(function(){
 
    /* ------------ PRELOADER ------------ */
    //$('body').addClass('preloader-site');
    /* ---------- END PRELOADER ---------- */

    /* --------- FULLSCREEN MODE --------- */
    /* ----- FullScreen Mode Handler ----- */
    var elem = document.documentElement;
    $("#fullscreen").one("click", handler1);
    function handler1() {
        if (elem.requestFullscreen) {
            elem.requestFullscreen();
        } else if (elem.mozRequestFullScreen) { /* Firefox */
            elem.mozRequestFullScreen();
        } else if (elem.webkitRequestFullscreen) { /* Chrome, Safari and Opera */
            elem.webkitRequestFullscreen();
        } else if (elem.msRequestFullscreen) { /* IE/Edge */
            elem.msRequestFullscreen();
        }
        $(this).one("click", handler2);
    }
    function handler2() {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.mozCancelFullScreen) { /* Firefox */
            document.mozCancelFullScreen();
        } else if (document.webkitExitFullscreen) { /* Chrome, Safari and Opera */
            document.webkitExitFullscreen();
        } else if (document.msExitFullscreen) { /* IE/Edge */
            document.msExitFullscreen();
        }
        $(this).one("click", handler1);
    }

    /* -----Fullscreen function----- */
    /* Get the documentElement (<html>) to display the page in fullscreen */
    /* View in fullscreen */
    function openFullscreen() {
        if (elem.requestFullscreen) {
            elem.requestFullscreen();
        } else if (elem.mozRequestFullScreen) { /* Firefox */
            elem.mozRequestFullScreen();
        } else if (elem.webkitRequestFullscreen) { /* Chrome, Safari and Opera */
            elem.webkitRequestFullscreen();
        } else if (elem.msRequestFullscreen) { /* IE/Edge */
            elem.msRequestFullscreen();
        }
    }
    /* Close fullscreen */
    function closeFullscreen() {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.mozCancelFullScreen) { /* Firefox */
            document.mozCancelFullScreen();
        } else if (document.webkitExitFullscreen) { /* Chrome, Safari and Opera */
            document.webkitExitFullscreen();
        } else if (document.msExitFullscreen) { /* IE/Edge */
            document.msExitFullscreen();
        }
    }
    /* ------- END FULLSCREEN MODE ------- */
});

$(window).on("load", function() {
    /* ------------ PRELOADER ------------ */
    $('.preloader-wrapper').fadeOut();
    $('body').removeClass('preloader-site');
    /* ---------- END PRELOADER ---------- */
});

