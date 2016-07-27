/**
 * Created by Pudzian on 27.07.2016.
 */
var u_twitch = "";
getstream();

function getstream() {
    $.ajax({
        url: '{{ url("api.stream") }}',
        dataType: 'json',
        success: function (stream) {
        for (var i = 0; i < stream.length; i++) {
            u_twitch += stream[i]["twitch"] + ",";
        }
        get_twitch(u_twitch);
    },
    error: function () {
        console.log("Coś poszło nie tak podczas łączenia z api jestemgraczem");

    }
});
}

function render(channel) {
    var display = channel["channel"]["display_name"];
    var name = channel["channel"]["name"];
    $("#streams-container").append('<div class="col-md-6" id="' + name + '"><div class=""><div class="v-title" id="' + name + '_title"></div><div class="v-img" id="' + name + '_img"></div><div class="v-bottom" id="' + name + '_bottom"></div></div></div>');
    $("#" + name).addClass('danger');
    $("#" + name + "_status").html('ONLINE').css('font-weight', 'bold');
    $("#" + name + "_game").html(channel["game"]);
    $("#" + name + "_viewers").html(channel["viewers"]);
    $("#" + name + "_title").html('<a href="{{ url('stream.id') }}/' + name + '"><h2 class="m-b-0 m-t-1">' + display + '<span style="float: right;color: #8bc34a;font-size: medium;">' + channel["viewers"] + '</span></h2></a>');
    $("#" + name + "_img").html('<a href="{{ url('stream.id') }}/' + name + '"> <img class="img-responsive" src="https://static-cdn.jtvnw.net/previews-ttv/live_user_' + name + '-640x360.jpg"> </a>');
}

function get_twitch(stream) {
    $.ajax({
        url: 'https://api.twitch.tv/kraken/streams/?channel=' + stream,
        dataType: 'jsonp',
        success: function (channel) {
            document.getElementById("loading").remove();
            first(channel["streams"][0]);
            for (var i = 1; i < channel["streams"].length; i++) {
                render(channel["streams"][i]);
            }
        }, error: function () {
            console.log("Coś poszło nie tak podczas łączenia z api twitch");
        }
    });
}
function first(channel) {
    var display = channel["channel"]["display_name"];
    var name = channel["channel"]["name"];
    $("#streams-container").append('<div class="col-md-12" id="' + name + '"><div class=""><div class="v-title" id="' + name + '_title"></div><div class="v-img" id="' + name + '_img"></div><div class="v-bottom" id="' + name + '_bottom"></div></div></div>');
    $("#" + name).addClass('danger');
    $("#" + name + "_status").html('ONLINE').css('font-weight', 'bold');
    $("#" + name + "_game").html(channel["game"]);
    $("#" + name + "_viewers").html(channel["viewers"]);
    $("#" + name + "_title").html('<a href="{{ url('stream.id') }}/' + name + '"><h2 class="m-b-0 m-t-1">' + display + '<span style="float: right;color: #8bc34a;font-size: medium;">' + channel["viewers"] + '</span></h2></a>');
    $("#" + name + "_img").html('<a href="{{ url('stream.id') }}/' + name + '"> <img class="img-responsive" src="https://static-cdn.jtvnw.net/previews-ttv/live_user_' + name + '-1920x1080.jpg"> </a>');
}