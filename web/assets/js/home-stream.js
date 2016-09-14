var u_twitch = "", g_url = "", g_exitUrl = "";
var limit = 4, count = 0;

function getStream(url, exitUrl) {
    $.ajax({
        url: url,
        dataType: 'json',
        success: function (stream) {
            g_url = url;
            g_exitUrl = exitUrl;
            for (var i = 0; i < stream.length; i++) {
                u_twitch += stream[i]["twitch"] + ",";
            }
            getTwitch(u_twitch);
        },
        error: function () {
            console.log("Coś poszło nie tak podczas łączenia z API JestemGraczem.pl");
        }
    });
}


function getTwitch(stream) {
    $.ajax({
        url: 'https://api.twitch.tv/kraken/streams/?channel=' + stream,
        dataType: 'jsonp',
        success: function (channel) {
            if(channel["streams"].length!=0){
                if (count == 0) {
                    $('#loading').remove();
                    topStream(channel["streams"][count], 1);
                    setTimeout(function () {
                        $('#image' + channel["streams"][count]["channel"]["name"]).addClass("stream-active");
                    }, 3000);
                }
            }
            for (count; count < channel["streams"].length && count < limit; count++) {
                renderBottomStreamList(channel["streams"][count]);
            }

            setTimeout(function () {
                count = 4;
                $('#loading').remove();
            }, 5000);
        }, error: function () {
            console.log("Coś poszło nie tak podczas łączenia z api twitch");
        }
    });
}

function renderBottomStreamList(channel) {
    var name = channel["channel"]["name"];
    $("#streams-container").append('<div class="col-sm-3" id="' + name + '"><div class=""><div class="v-title" id="' + name + '_title"></div><div class="v-img" id="' + name + '_img"></div><div class="v-bottom" id="' + name + '_bottom"></div></div></div>');
    $("#" + name).addClass('danger');
    $("#" + name + "_status").html('ONLINE').css('font-weight', 'bold');
    $("#" + name + "_game").html(channel["game"]);
    $("#" + name + "_viewers").html(channel["viewers"]);
    $("#" + name + "_img").html('<img onclick="topStream(\'' + name + '\',0)" data-toggle="tooltip" title="' + name + '" class="img-responsive stream" src="https://static-cdn.jtvnw.net/previews-ttv/live_user_' + name + '-320x180.jpg" id="image' + name + '" alt="' + name + '">');
}

function topStream(channel, type) {
    if (channel != null && type === 1) {
        $.ajax({
            url: g_url + "/" + channel["channel"]["name"],
            dataType: 'json',
            success: function (description) {
                $('#description').hide().html(description[0]['description']).fadeIn(1000);
            },
            error: function () {
                console.log("Coś poszło nie tak podczas łączenia z api jestemgraczem");
            }
        });
        $('#topstream').hide().attr('src', "http://player.twitch.tv/?html5=true&channel=" + channel["channel"]["name"]).fadeIn(1000);
        $('#viewers').hide().html(channel["viewers"]).fadeIn(1000);
        $('#link').hide().html("www.twitch.tv/" + channel["channel"]["name"]).fadeIn(1000);
        $('#display_name').hide().html(channel["channel"]["display_name"]).fadeIn(1000);
        $('#profile').attr('href', g_exitUrl + "/" + channel["channel"]["name"]);
        $('#avatar').hide().attr('src', channel["channel"]["logo"]).fadeIn(1000);
        $('#game').hide().attr('src', "https://static-cdn.jtvnw.net/ttv-boxart/" + channel["game"] + "-50x50.jpg").fadeIn(1000);

        return 0;
    }
    $.ajax({
        url: 'https://api.twitch.tv/kraken/streams/?channel=' + channel,
        dataType: 'jsonp',
        success: function (channel) {
            if(channel["streams"].length!=0){
                $('.stream-active').addClass('stream').removeClass('stream-active');
                $('#image' + channel["streams"][0]["channel"]["name"]).addClass("stream-active");
                topStream(channel["streams"][0], 1);
            }
        }, error: function () {
            console.log("Błąd z działaniem funkcji");
        }
    });
}
