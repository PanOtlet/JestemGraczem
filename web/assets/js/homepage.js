function Stream(url) {

    this.dev = true;
    this.url = url;
    this.twitchApiKey = 'dd80eugsisvxdq2h689e6m8e3tqqzuw';
    this.twitchNameList = [];
    this.twitchList = '';
    this.streamList = null;
    this.list = []; //Wygenerowana lista streamerów
    this.activeList = []; //Aktywni
    this.templateStreamPosition = '<div class="swiper-slide">' +
        '<div class="view">' +
        '<img src="{{image}}" class="img-fluid" alt="{{name}}">' +
        '<div class="mask pattern-1 flex-center">' +
        '<a class="stream-title" href="{{url}}" target="_blank">{{domain}} {{name}}</a>' +
        '</div></div></div>';

    /**
     * Funkcja startowa
     * @param ads
     * @returns {null}
     */
    this.start = function (ads) {

        $('#stream-container').hide();

        if (!ads) {
            this.antyBlocker();
        }

        this.getStreamerList();
    };

    this.logg = function (message) {
        if (this.dev === true) {
            console.warn(message);
        }
    };

    this.antyBlocker = function () {
        $('h1').html('Prosimy o wyłączenie AdBlock!');
        $('h2').html('Prosimy o wyłączenie AdBlock!');
        $('h3').html('Prosimy o wyłączenie AdBlock!');
    };

    /**
     * Funkcja pobierająca JSON z wewnętrznego API
     * @returns {boolean}
     */
    this.getStreamerList = function () {
        $.ajax({
            url: stream.url,
            dataType: 'json',
            success: function (streamInfo) {
                console.debug(streamInfo);
            },
            error: function () {
                console.error('Błąd w API JestemGraczem.pl');
            },
            complete: function (streamInfo) {
                stream.streamList = streamInfo;
                stream.createActiveList(stream.streamList);
            }
        })
    };

    /**
     * Funkcja sprawdzająca, czy użytkownik streamujący na Beam.pro jest aktualnie online i zwraca tablicę zaraz
     * po próbie posortowania jej według ilości widzów
     * @returns {Array}
     */
    this.createActiveList = function (list) {
        var lista = list.responseJSON;
        for (var i = 0; i < lista.length; i++) {
            if (lista[i]['beampro'] != null) {
                this.beamChannelRequest(lista[i]['beampro']);
            }
            if (lista[i].twitch != null) {
                this.twitchList += lista[i].twitch + ',';
            }
        }

        this.twitchChannelRequest(this.twitchList);

        $(document).ajaxStop(function () {
            if (stream.activeList.length === 0) {
                $('#stream-container').remove();
            }

            stream.activeList.sort(sorter);
            stream.renderStreamList(stream.activeList);

            var mySwiper = new Swiper('.swiper-container', {
                loop: true,
                scrollbar: '.stream-scrollbar',
                slidesPerView: 4,
                paginationClickable: true,
                spaceBetween: 30
            });

            $('#stream-container').show();
        });
    };

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
     * Funkcja wysyłania requesta do serwera z prośbą o informacje na temat streamera w serwisie Beam.pro
     * @param name
     */
    this.beamChannelRequest = function (name) {
        $.ajax({
            type: 'GET',
            url: 'https://beam.pro/api/v1/channels/' + name,
            success: function (channel) {
                if (channel['online'] == true) {
                    stream.activeList.push({
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
            }
        });
    };

    /**
     * Funkcja wysyłania requesta do serwera z prośbą o informacje na temat streamera w serwisie Twitch.tv
     * @param name
     */
    this.twitchChannelRequest = function (name) {

        $.ajax({
            type: 'GET',
            url: 'https://api.twitch.tv/kraken/streams?channel=' + name.slice(0, -1),
            headers: {
                'Client-ID': stream.twitchApiKey
            },
            success: function (channel) {
                for (var i = 0; i < Object.keys(channel['streams']).length; i++)
                    stream.activeList.push({
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
            }
        });
    };

    this.renderStreamList = function (array) {
        for (var i = 0; i < Object.keys(array).length; i++)
            $('#stream-container .swiper-wrapper').append(Mustache.render(stream.templateStreamPosition, array[i]))
    }
}