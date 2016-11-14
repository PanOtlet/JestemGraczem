function Stream(url, apiKey) {

    this.url = url;
    this.apiKey = apiKey;

    this.list = [];
    this.beamList = [];
    this.twitchList = "";

    this.streams = 0;

    function sorter(first, second) {
        if (first['viewers'] == second['viewers'])
            return 0;
        if (first['viewers'] < second['viewers'])
            return -1;
        else
            return 1;
    }

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

    this.createBeamActiveList = function () {
        var beamList = [];
        for (var i = 0; i < this.list.length; i++) {
            if (this.list[i]['beampro'] != null)
                $.ajax({
                    type: 'GET',
                    url: 'https://beam.pro/api/v1/channels/' + this.list[i]['beampro'],
                    success: function (channel) {
                        if (channel['online'] == true)
                            beamList.push({
                                'viewers': channel['viewersCurrent'],
                                'name': channel['token'],
                                'url': 'https://beam.pro/embed/player/' + channel['token']
                            });
                    },
                    error: function () {
                        console.log("Error with Beam.pro API!");
                    },
                    async: false
                });
        }
        beamList.sort(sorter);
        return beamList;
    };

    this.createTwitchActiveList = function () {
        for (var i = 0; i < this.list.length; i++) {
            this.twitchNameList += this.list[i]["twitch"] + ",";
        }

        var twitchList = [];

        $.ajax({
            type: 'GET',
            url: 'https://api.twitch.tv/kraken/streams/?channel=' + this.twitchNameList,
            headers: {
                'Client-ID': this.apiKey
            },
            success: function (channel) {
                console.log(channel["status"]);
                // for (var i = 0; i < channel.streams; i++)
                //     // console.log(channel.streams);
                //     twitchList.push({
                //         'viewers': channel['streams'][i]['viewers'],
                //         'name': channel['streams'][i]['channel']['display_name'],
                //         'url': 'http://player.twitch.tv/?channel=' + channel['streams'][i]['channel']['name']
                //     })
            },
            error: function () {
                console.log("Error with Twitch API!");
            },
            async: false
        });
        return twitchList;
    };

    this.generateMainVideos = function (data) {
        for (var i = 0; i < data['beam'].lenght; i++){

        }
    };

    this.start = function () {
        this.list = this.getStreamerList();

        if (this.list === false) {
            console.log("Error with JGApp API!");
            return false;
        }

        this.beamList = this.createBeamActiveList();
        // this.twitchList = this.createTwitchActiveList();

        var data = {
            'beam': this.beamList,
            // 'twitch': this.twitchList
        };

        console.log(data);

        this.generateMainVideos(data)
    };
}