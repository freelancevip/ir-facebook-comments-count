jQuery(document).ready(function ($) {

    if (!$("#fb-comments-count-container").length) {
        return;
    }

    var active = true,
        timerSelector = "#time-start",
        curDate = $(timerSelector).val();
    
    $("#fb-continue").click(function (e) {
        e.preventDefault();
        active = true;
        parsing();
    });
    $("#fb-start").click(function (e) {
        e.preventDefault();
        $("#fb-message").append("<p>Парсинг начался</p>");
        active = true;
        parsing();
    });
    $("#fb-stop").click(function (e) {
        e.preventDefault();
        stop();
    });
    $(timerSelector).on("blur", function (e) {
        curDate = $(this).val() + " 00:00:00";
    });

    function stop(message) {
        message = message || "Парсинг остановлен";
        alert(message);
        active = false;
    }

    function parsing() {
        if (active === false) {
            return false;
        }
        $.ajax({
            url: ajaxurl,
            type: 'post',
            data: {
                action: 'fb_parser',
                date: curDate
            },
            success: function (request) {
                console.log(request);
                if (request.status === true) {
                    curDate = request.dateNextPost;
                    parsing();
                } else {
                    stop(request.message);
                }
            },
            error: function (e, txt) {
                console.error(e);
            }
        })
    }
});