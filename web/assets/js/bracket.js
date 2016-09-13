/**
 * Created by Pudzian on 26.08.2016.
 */
var Data = {};
var Id, g_api;

function view() {
    $('#view').bracket({
        init: Data
    })
}

function edit(api,id) {
    Id = id;
    g_api=api;
    var container = $('div#save');
    container.bracket({
        init: Data,
        save: saveFn,
        userData: api
    });
    var data = container.bracket('data');
}

function saveFn(data, userData) {
    console.log(data);

    Data = data;
    var str_data ='{"data":{"teams":[["Team 1","Team 2"],["Team 3","Team 4"]],"results":[[[[4,6],[5,7]],[[7,9],[4,3]]]]},"id":"3"}';

    $.post(g_api,
        {
            data: Data,
            id: Id
        },
        function(data, status){
            console.log("Data: " + data + "\nStatus: " + status);
        });
}
