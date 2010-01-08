
var SamuraiDebug = {};
SamuraiDebug.opened_window = '';

/**
 * ウインドウを切り替える。
 * @access     public
 * @param      string  window_id   ウインドウのID
 */
SamuraiDebug.swapWindow = function(window_id)
{
    if(SamuraiDebug.opened_window != ''){
        document.getElementById(SamuraiDebug.opened_window).style.display = 'none';
    }
    if(window_id == SamuraiDebug.opened_window){
        SamuraiDebug.opened_window = '';
    } else {
        SamuraiDebug.opened_window = window_id;
        var samurai_window = document.getElementById(window_id);
        samurai_window.style.display  = 'block';
        samurai_window.style.zIndex   = 110;
        samurai_window.style.width    = '750px';
        samurai_window.style.position = 'absolute';
        samurai_window.style.top      = '50px';
        samurai_window.style.right    = '160px';
        samurai_window.style.marginBottom = '10px';
    }
}
