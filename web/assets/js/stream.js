/**
 * Generator streamów na stronie
 * @constructor
 * @param {string} url - Adres URL do API pobierającego dane o użyszkodnikach
 * @param {string} apiKey - Klucz API do streamów na Twitch
 */
function Stream(url, apiKey) {

    this.url = url;
    this.apiKey = apiKey;

    this.list = [];
    this.beamList = [];
    this.twitchList = "";
    this.twitchNameList = '';

    this.streams = 4;

    /**
     * Funkcja sortująca
     * @param {Array} first - pierwsza tablica nieposortowana
     * @param {Array} second - druga tablica posortowana
     * @returns {int} - zwraca status operacji
     */
    function sorter(first, second) {
        if (first['viewers'] == second['viewers'])
            return 0;
        if (first['viewers'] < second['viewers'])
            return -1;
        else
            return 1;
    }

    /**
     * Funkcja pobierająca JSON z wewnętrznego API
     * @returns {boolean}
     */
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

    /**
     * Funkcja sprawdzająca, czy użytkownik streamujący na Beam.pro jest aktualnie online i zwraca tablicę zaraz
     * po próbie posortowania jej według ilości widzów
     * @returns {Array}
     */
    this.createBeamActiveList = function () {
        var beamList = [];
        for (var i = 0; i < this.list.length; i++) {
            if (this.list[i]['beampro'] != null)
                $.ajax({
                    type: 'GET',
                    url: 'https://beam.pro/api/v1/channels/' + this.list[i]['beampro'],
                    success: function (channel) {
                        if (channel['online'] == true){
                            // var dupa = channel['type']["name"] === 'null' ? channel['type']['name'] : 'Nie ustalono';
                            beamList.push({
                                'viewers': channel['viewersCurrent'],
                                'name': channel['token'],
                                'url': 'https://beam.pro/embed/player/' + channel['token'],
                                'title': channel['name'],
                                'game': 'Dupa',
                                'image': channel['thumbnail']['url']
                            });
                        }
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

    /**
     * Funkcja sprawdzająca, czy użytkownik streamujący na Twitch.tv jest aktualnie online i zwraca tablicę
     * @returns {Array}
     */
    this.createTwitchActiveList = function () {
        for (var i = 0; i < this.list.length; i++) {
            this.twitchNameList += this.list[i]["twitch"] + ",";
        }

        var twitchList = [];
        stream = this.streams;
        $.ajax({
            type: 'GET',
            url: 'https://api.twitch.tv/kraken/streams?channel=' + this.twitchNameList,
            headers: {
                'Client-ID': this.apiKey
            },
            success: function (channel) {
                for (var i = 0; i < stream ; i++)
                    twitchList.push({
                        'viewers': channel['streams'][i]['viewers'],
                        'name': channel['streams'][i]['channel']['display_name'],
                        'url': 'http://player.twitch.tv/?channel=' + channel['streams'][i]['channel']['name'],
                        'title': channel['streams'][i]['channel']['status'],
                        'game': channel['streams'][i]['channel']['game'],
                        'image': channel['streams'][i]['preview']['medium']
                    })
            },
            error: function () {
                console.log("Error with Twitch API!");
            },
            async: false
        });
        return twitchList;
    };

    this.renderTopStream = function (array) {

    };

    this.renderBottomStream = function (array) {
        console.log('generuję ' + array['name']);
        $("#streams-container").append('<div class="col-sm-3" id="' + array["name"] + '"><div class=""><div class="v-title" id="' + array["name"] + '_title"></div><div class="v-img" id="' + array["name"] + '_img"></div><div class="v-bottom" id="' + array["name"] + '_bottom"></div></div></div>');
        $("#" + array["name"]).addClass('danger');
        $("#" + array["name"] + "_status").html('ONLINE').css('font-weight', 'bold');
        $("#" + array["name"] + "_game").html(array["game"]);
        $("#" + array["name"] + "_viewers").html(array["viewers"]);
        $("#" + array["name"] + "_img").html('<img onclick="stream.renderTopStream(\'' + JSON.stringify(array) + '\')" data-toggle="tooltip" title="' + array["name"] + '" class="img-responsive stream" src="' + array["image"] + '" id="image_' + array["name"] + '" alt="' + array["name"] + '">');
    };

    /**
     * Wybiórcze generowanie wybranych streamów
     * @param data
     */
    this.generateMainVideos = function (data) {
        var lenght;

        if (data.beam.length < 5) {
            lenght = data.beam.length;
        } else {
            lenght = 4;
        }

        for (var i = 0; i < lenght; i++) {
            this.renderBottomStream(data.beam[i]);
            this.streams--;
        }

        if (this.streams > 0) {
            for (i = 0; i < (4 - this.streams); i++) {
                this.renderBottomStream(data.twitch[i]);
            }
        }
        $('#loading').remove();

    };

    /**
     * Funkcja main, której zadaniem jest uruchamiać po kolei funkcje
     * @returns {boolean}
     */
    this.start = function () {
        this.list = this.getStreamerList();

        if (this.list === false) {
            console.log("Error with JGApp API!");
            return false;
        }

        this.beamList = this.createBeamActiveList();
        this.twitchList = this.createTwitchActiveList();

        var data = {
            'beam': this.beamList,
            'twitch': this.twitchList
        };

        this.generateMainVideos(data)
    };
}