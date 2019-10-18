require('./bootstrap');
const Tutor = require('./tutorial');

$(function(){

    setTimeout(() => $("#motive").fadeIn(1000), 2000);

    $("#watch-preview").click(() => $(".main-message").fadeOut(2000));

    /*
    var typed = new Typed('.type-effect', {
        strings: [text],
        typeSpeed: 90,
        onComplete: () => {
            setTimeout(() => {
                typed.destroy();
                $(".type-effect").html(text+"&nbsp");
            }, 4000)

        }
    });
    */

    let tutor = new Tutor();
    tutor.start('#tutor-terminal-window', '#tutor-chat-window', '#tutor-chat-second-window');
})
