/**
 * Generator streamów na stronie
 * @constructor
 * @param {string} url - Adres URL do API pobierającego dane o użyszkodnikach
 * @param {string} twitchApiKey - Klucz API do streamów na Twitch
 * @return {object}
 */
function Stream(url, twitchApiKey) {

    this.ad = '';

    this.url = url;
    this.apiKey = twitchApiKey;

    this.fullData = {};

    this.list = [];
    this.beamList = [];

    this.twitchList = "";
    this.twitchNameList = '';

    this.streams = 4;

    this.template = [];
    this.template['bottomStream'] = '<div class="col-sm-3" id="{{name}}">' +
        '<div class="">' +
        '<div class="v-title" id="{{name}}_title"></div>' +
        '<div class="v-img" id="{{name}}_img"></div>' +
        '<div class="v-bottom" id="{{name}}_bottom"></div>' +
        '</div>' +
        '</div>';

    /**
     * Funkcja sortująca
     * @param {Array} first - pierwsza tablica nieposortowana
     * @param {Array} second - druga tablica posortowana
     * @returns {int} - zwraca status operacji
     */
    function sorter(first, second) {
        if (first['viewers'] > second['viewers'])
            return -1;
        if (first['viewers'] < second['viewers'])
            return 1;
        else
            return 0;
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
                        console.error("Error with Beam.pro API!");
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

        if (this.twitchNameList == '')
            return twitchList;

        $.ajax({
            type: 'GET',
            url: 'https://api.twitch.tv/kraken/streams?channel=' + this.twitchNameList,
            headers: {
                'Client-ID': this.apiKey
            },
            success: function (channel) {
                for (var i = 0; i < Object.keys(channel['streams']).length; i++)
                    twitchList.push({
                        'domain': 'twitch.tv',
                        'platform': 'twitch',
                        'viewers': channel['streams'][i]['viewers'],
                        'name': channel['streams'][i]['channel']['display_name'],
                        'url': 'https://player.twitch.tv/?channel=' + channel['streams'][i]['channel']['name'],
                        'title': channel['streams'][i]['channel']['status'],
                        'image': channel['streams'][i]['preview']['medium']
                    })
            },
            error: function () {
                console.error("Error with Twitch API!");
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
        if (!this.ad) {
            this.antyTopStream()
        } else {
            if (typeof array != "undefined") {
                try {
                    if (Object.prototype.toString.call(array) === '[object String]') {
                        array = array.replace(/\\"/g, '"');
                        array = JSON.parse(array);
                    }
                    $('#topstream').hide().attr('src', array['url']).fadeIn(1000);
                    $('#viewers').hide().html(array["viewers"]).fadeIn(1000);
                    $('#link').hide().html(array['domain'] + "/" + array['name']).fadeIn(1000);
                    $('#display_name').hide().html(array['name']).fadeIn(1000);
                    $('#avatar').hide().attr('src', array['image']).fadeIn(1000);
                    $('#game').hide().attr('src', array['image']).fadeIn(1000);
                } catch (e) {
                    console.error(e);
                }
            }
        }
    };

    /**
     * A ja wam dam małę gnoje AdBlocka!
     */
    this.antyTopStream = function () {
        $('#topstream').hide().attr('src', 'https://www.youtube.com/embed/DLzxrzFCyOs?autoplay=true').fadeIn(1000);
        $('#streams-container').hide().html(
            '<div class="card card-block"><p class="card-text">' +
            'JestemGraczem.pl utrzymuje się między innymi z reklam.<br>' +
            'Wyłącz adBlock, byśmy mogli się dalej rozwijać!<br>' +
            'Ciebie to nic nie kosztuje, nie mamy natrętnych reklam i jest ich niewiele, ale nam pomogą w rozwoju!' +
            '</p></div>'
        ).fadeIn(1000);
        $('#viewers').hide().html('www.jestemgraczem.pl').fadeIn(1000);
        $('#display_name').hide().html('Rick Astley').fadeIn(1000);
        $('#avatar').hide().attr('src', 'https://jestemgraczem.pl/assets/img/troll.gif').fadeIn(1000);
    };

    /**
     * Funkcja odpowiedzialna za rendering miniatur na stronie głównej
     * @param array
     */
    this.renderBottomStream = function (array) {
        if (Object.prototype.toString.call(array) === '[object Object]') {
            try {
                var name = array['name'] + "_" + array['platform'];
                $("#streams-container").append('<div class="col-md-3" id="' + name + '"><div class="card" id="' + name + '_img"></div></div>');
                $("#" + name).addClass('danger');
                $("#" + name + "_status").html('ONLINE').css('font-weight', 'bold');
                $("#" + name + "_game").html(array["game"]);
                $("#" + name + "_viewers").html(array["viewers"]);
                var json = JSON.stringify(array);
                json = json.replace(/"/g, '\\"');
                $("#" + name + "_img").html("<img onclick='stream.renderTopStream(\"" + json + "\")' data-toggle='tooltip' title='" + name + "' class='img-fluid stream' src='" + array['image'] + "' id='image_" + name + "' alt='Live Stream " + name + "'>");
            } catch (e) {
                console.error(e);
            }
        }
    };

    /**
     * Funkcja odpowiedzialna za rendering miniatur gdzieś
     * @param array
     */
    this.renderStreamList = function (array) {
        var name = array['name'] + "_" + array['platform'];
        $("#streams-container").append(Mustache.render(this.template['bottomStream'], {name: name}));
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
        var lenght, beamLenght = data.beam.length;

        if (beamLenght < 5 && beamLenght > 0) {
            lenght = data.beam.length;
            this.renderTopStream(data.beam[0]);
        } else {
            lenght = 4;
        }

        if (beamLenght > 0)
            for (var i = 0; i < lenght; i++) {
                this.renderBottomStream(data.beam[i]);
                this.streams--;
            }

        if (this.streams == 4) {
            this.renderTopStream(data.twitch[0]);
        }

        for (i = 0; i < this.streams; i++) {
            this.renderBottomStream(data.twitch[i]);
            this.streams--;
        }
    };

    /**
     * Wybiórcze generowanie wybranych streamów
     * @param data
     */
    this.generateStreamListVideos = function (data) {

        if (data.beam.length > 0)
            for (var i = 0; i < data.beam.length; i++) {
                this.renderStreamList(data.beam[i]);
                this.streams--;
            }

        if (data.twitch.length > 0)
            for (i = 0; i < data.twitch.length; i++) {
                this.renderStreamList(data.twitch[i]);
            }
    };

    /**
     * Funkcja main, której zadaniem jest uruchamiać po kolei funkcje
     * @returns {boolean}
     */
    this.start = function (ad) {
        this.ad = ad;

        this.list = this.getStreamerList();

        if (this.list === false) {
            console.error("Error with JGApp API!");
            return false;
        }

        this.beamList = this.createBeamActiveList();
        this.twitchList = this.createTwitchActiveList();

        this.fullData = {
            'beam': this.beamList,
            'twitch': this.twitchList
        };

        this.generateMainVideos(this.fullData);

        var mySwiper = new Swiper ('.swiper-container', {
            loop: true,
            scrollbar: '.swiper-scrollbar',
            slidesPerView: 4,
            paginationClickable: true,
            spaceBetween: 30
        });
        $('#loading').remove();
    };

    /**
     * Funkcja main, której zadaniem jest uruchamiać po kolei funkcje
     * @returns {boolean}
     */
    this.startStreamList = function () {
        this.list = this.getStreamerList();

        if (this.list === false) {
            console.error("Error with JGApp API!");
        }

        this.generateStreamListVideos(this.createBeamActiveList());
        this.generateStreamListVideos(this.createTwitchActiveList());

        $('#loading').remove();
    };
}