/**
 * Created by Pudzian on 26.08.2016.
 */
var Data = {};

Data = { //dummy data for testing
    teams: [
        ["Team 1", "Team 2"], /* first matchup */
        ["Team 3", "Team 4"]//,  /* second matchup */
        /* ["Team 5", "Team 6"],
         ["Team 7", "Team 8"],
         ["Team 9", "Team 10"],
         ["Team 11", "Team 12"],
         ["Team 13", "Team 14"],
         ["Team 15", "Team 16"]*/

    ],
    results: [
        [[4, 6], [5, 7]],
        [[8, 9], [4, 3]]
    ]
};

function add_in() {
    for (var i = 0; i < Data["teams"].length; i++) {
        Data["results"][0].push([parseInt(document.getElementById("r-" + Data["teams"][i][0]).value), parseInt(document.getElementById("r-" + Data["teams"][i][1]).value)]);
        console.log(Data);
    }

    view();
}


function api_Data() {
    $.ajax({
        url: url,
        dataType: 'json',
        success: function (bracket) {
            Data = bracket;
        },
        error: function () {
            console.log("Coś poszło nie tak podczas łączenia z API JestemGraczem.pl");
        }
    });
}

function view() {
    $('#view').bracket({
        init: Data /* data to initialize the bracket with */
    })
}

function edit(api) {
    var container = $('div#save');
    container.bracket({
        init: Data,
        save: saveFn,
        userData: api
    });

    /* You can also inquiry the current data */
    var data = container.bracket('data');
    //$('#dataOutput').text(jQuery.toJSON(data))
}


//black magic no idea what so ever
/* Called whenever bracket is modified
 *
 * data:     changed bracket object in format given to init
 * userData: optional data given when bracket is created.
 */
function saveFn(data, userData) {
    console.log(data);

    Data = data;
    view();
    //var json = jQuery.toJSON(data)
    // $('#saveOutput').text('POST '+userData+' '+json)
    // You probably want to do something like this
    jQuery.ajax("rest/" + userData, {
        contentType: 'application/json',
        dataType: 'json',
        type: 'post',
        data: Data
    });

}

