class Stream {

    url = "";
    twitchApiKey = "";
    streamerList = "";

    constructor(url, twitchApiKey) {
        this.url = url;
        this.twitchApiKey = twitchApiKey;
        this.start(ad);
        this.streamerList = this.getStreamerList();
        if (this.streamerList === false){
            Console.error('JestemGraczem.pl API ERROR')
        }
    };

    /**
     * Funkcja pobierająca JSON z wewnętrznego API
     * @returns {boolean}
     */
    getStreamerList = function () {
        info = false;
        try {
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
        } catch (e){
            Console.error(e);
        }
        return info;
    };
}