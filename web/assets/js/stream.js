function Stream(url, apiKey) {

    this.url = url;
    this.apiKey = apiKey;

    this.list = [];
    this.beamList = [];
    this.twitchList = "";

    this.streams = 0;

    function sorter(first, second)
    {
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
                        console.log("Api Beam się zjebało!");
                    },
                    async: false
                });
        }
        beamList.sort(sorter);
        return beamList;
    };

    this.createTwitchActiveList = function () {
        for (var i = 0; i < this.twitchList.length; i++) {
            this.twitchNameList += this.twitchList[i]["twitch"] + ",";
        }
        $.ajax({
            type: 'GET',
            url: 'https://api.twitch.tv/kraken/streams/?channel=' + this.twitchNameList,
            headers: {
                'Client-ID': this.apiKey
            },
            success: function (channel) {
                // this.beamList.push(channel['token']);
            },
            error: function () {
                console.log("Api Twitch się zjebało!");
            }
        });
    };

    this.generateMainVideos = function (data) {
    };

    this.start = function () {
        this.list = this.getStreamerList();

        if (this.list === false) {
            console.log("Błąd z pobraniem listy streamerów!");
            return false;
        }

        this.beamList = this.createBeamActiveList();
        // this.createTwitchActiveList();

    };
}