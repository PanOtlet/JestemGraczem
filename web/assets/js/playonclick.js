$( ".gif" ).click(function() {
    if ($(this).find("img").attr("data-state") == "static") {
        $(this).find("img").attr("src", function() {
            return $(this).attr("data-entry-url")+".gif";
        });
        $(this).find("img").attr("data-state", "");
    } else {
        $(this).find("img").attr("src", function() {
            return $(this).attr("data-entry-url")+".png";
        });
        $(this).find("img").attr("data-state", "static");
    }
});