function Stream(url,apiKey) {

    this.url = url;
    this.apiKey = apiKey;

    this.twitchList = {};
    this.beamList = {};

    this.twitchNameList = "";

    this.streams = 0;

    this.setTwitchList = function (list) {
        this.twitchList = list;
    };

    this.setBeamList = function (list) {
        this.beamList = list;
    };

    this.getTwitchStatus = function () {
        for (var i = 0; i < this.twitchList.length; i++) {
            this.twitchNameList += this.twitchList[i]["twitch"] + ",";
        }
        $.ajax({
            type: 'GET',
            url: 'https://api.twitch.tv/kraken/streams/?channel=' + stream,
            headers: {
                'Client-ID': this.apiKey
            },
            success: function (channel) {
                if (channel["status"] != 400) {
                    if (channel["streams"].length != 0) {
                        if (count == 0) {
                            $('#loading').remove();
                            topStream(channel["streams"][count], 1);
                            setTimeout(function () {
                                $('#image' + channel["streams"][count]["channel"]["name"]).addClass("stream-active");
                            }, 3000);
                        }
                    }
                    for (count; count < channel["streams"].length && count < limit; count++) {
                        renderBottomStreamList(channel["streams"][count], apiKey);
                    }
                }

                setTimeout(function () {
                    count = 4;
                    $('#loading').remove();
                }, 5000);
            }, error: function () {
                console.log("Coś poszło nie tak podczas łączenia z api twitch");
            }
        });
    };

    this.getStreamerList = function () {
        var info = false;
        $.ajax({
            url: this.url,
            dataType: 'json',
            success: function (stream) {
                info = stream
            },
            error: function () {
                info = false
            },
            async: false
        });
        return info;
    };

    this.generateMainVideos = function (data) {
        console.log('WIN')
    };

    this.start = function () {
        this.list = this.getStreamerList();

        if (this.list === false) {
            console.log("Błąd z pobraniem listy streamerów!");
            return false;
        }

        this.setTwitchList(this.list['twitch']);
        this.setBeamList(this.list['beampro']);



    };
}