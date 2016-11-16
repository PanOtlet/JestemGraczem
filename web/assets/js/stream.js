/**
 * Generator streamów na stronie
 * @constructor
 * @param {string} url - Adres URL do API pobierającego dane o użyszkodnikach
 * @param {string} twitchApiKey - Klucz API do streamów na Twitch
 */
function Stream(url, twitchApiKey) {

    this.url = url;
    this.apiKey = twitchApiKey;

    this.fullData = {};

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
     * Funkcja pobierająca JSON z wewnętrznego API
     * @returns {boolean}
     */
    this.getFullStreamerList = function () {
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
                        if (channel['online'] == true) {
                            beamList.push({
                                'domain': 'beam.pro',
                                'platform': 'beam',
                                'viewers': channel['viewersCurrent'],
                                'name': channel['token'],
                                'url': 'https://beam.pro/embed/player/' + channel['token'],
                                'title': channel['name'],
                                'image': channel['thumbnail']['url'] === 'null' ? '#' : channel['thumbnail']['url']
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
        var streamCount = this.streams;
        $.ajax({
            type: 'GET',
            url: 'https://api.twitch.tv/kraken/streams?channel=' + this.twitchNameList,
            headers: {
                'Client-ID': this.apiKey
            },
            success: function (channel) {
                for (var i = 0; i < streamCount; i++)
                    twitchList.push({
                        'domain': 'twitch.tv',
                        'platform': 'twitch',
                        'viewers': channel['streams'][i]['viewers'],
                        'name': channel['streams'][i]['channel']['display_name'],
                        'url': 'http://player.twitch.tv/?channel=' + channel['streams'][i]['channel']['name'],
                        'title': channel['streams'][i]['channel']['status'],
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

    /**
     * Funkcja odpowiedzialna za render głównego streamu
     * @param array
     */
    this.renderTopStream = function (array) {
        if (typeof array !== 'object') {
            array = array.replace(/\\"/g, '"');
            array = JSON.parse(array);
        }
        $('#topstream').hide().attr('src', array['url']).fadeIn(1000);
        $('#viewers').hide().html(array["viewers"]).fadeIn(1000);
        $('#link').hide().html(array['domain'] + "/" + array['name']).fadeIn(1000);
        $('#display_name').hide().html(array['name']).fadeIn(1000);
        // $('#profile').attr('href', g_exitUrl + "/" + channel["channel"]["name"]);
        $('#avatar').hide().attr('src', array['image']).fadeIn(1000);
        $('#game').hide().attr('src', array['image']).fadeIn(1000);
    };

    /**
     * Funkcja odpowiedzialna za rendering miniatur na stronie głównej
     * @param array
     */
    this.renderBottomStream = function (array) {
        var name = array['name'] + "_" + array['platform'];
        $("#streams-container").append('<div class="col-sm-3" id="' + name + '"><div class=""><div class="v-title" id="' + name + '_title"></div><div class="v-img" id="' + name + '_img"></div><div class="v-bottom" id="' + name + '_bottom"></div></div></div>');
        $("#" + name).addClass('danger');
        $("#" + name + "_status").html('ONLINE').css('font-weight', 'bold');
        $("#" + name + "_game").html(array["game"]);
        $("#" + name + "_viewers").html(array["viewers"]);
        var json = JSON.stringify(array);
        json = json.replace(/"/g, '\\"');
        $("#" + name + "_img").html("<img onclick='stream.renderTopStream(\"" + json + "\")' data-toggle='tooltip' title='" + name + "' class='img-responsive stream' src='" + array['image'] + "' id='image_" + name + "' alt='" + name + "'>");
    };

    /**
     * Funkcja odpowiedzialna za rendering miniatur na stronie głównej
     * @param array
     */
    this.renderStreamList = function (array) {
        var name = array['name'] + "_" + array['platform'];
        $("#streams-container").append('<div class="col-sm-3" id="' + name + '"><div class=""><div class="v-title" id="' + name + '_title"></div><div class="v-img" id="' + name + '_img"></div><div class="v-bottom" id="' + name + '_bottom"></div></div></div>');
        $("#" + name).addClass('danger');
        $("#" + name + "_status").html('ONLINE').css('font-weight', 'bold');
        $("#" + name + "_game").html(array["game"]);
        $("#" + name + "_viewers").html(array["viewers"]);
    };

    /**
     * Wybiórcze generowanie wybranych streamów
     * @param data
     */
    this.generateMainVideos = function (data) {
        var lenght;

        if (data.beam.length < 5) {
            lenght = data.beam.length;
            this.renderTopStream(data.beam[0]);
        } else {
            lenght = 4;
        }

        for (var i = 0; i < lenght; i++) {
            this.renderBottomStream(data.beam[i]);
            this.streams--;
        }

        if (this.streams == 0) {
            this.renderTopStream(data.twitch[0]);
        }

        if (this.streams > 0) {
            for (i = 0; i < (4 - this.streams); i++) {
                this.renderBottomStream(data.twitch[i]);
            }
        }
        $('#loading').remove();

    };

    /**
     * Wybiórcze generowanie wybranych streamów
     * @param data
     */
    this.generateStreamListVideos = function (data) {

        for (var i = 0; i < data.beam.length; i++) {
            this.renderStreamList(data.beam[i]);
            this.streams--;
        }

        for (i = 0; i < data.twitch.length; i++) {
            this.renderStreamList(data.twitch[i]);
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

        this.fullData = {
            'beam': this.beamList,
            'twitch': this.twitchList
        };

        this.generateMainVideos(this.fullData);
    };

    /**
     * Funkcja main, której zadaniem jest uruchamiać po kolei funkcje
     * @returns {boolean}
     */
    this.startStreamList = function () {
        this.list = this.getFullStreamerList();

        if (this.list === false) {
            console.log("Error with JGApp API!");
            return false;
        }

        this.generateStreamListVideos(this.createBeamActiveList());
        this.generateStreamListVideos(this.createTwitchActiveList());
    };
}