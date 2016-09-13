/**
 * Created by Pudzian on 26.08.2016.
 */
var Data = {};
var Id, g_api;
Data = {
    teams: [
        ["Team 1", "Team 2"],
        ["Team 3", "Team 4"]

    ],
    results: [
        [[4, 6], [5, 7]],
        [[8, 9], [4, 3]]
    ]
};

function view() {
    $('#view').bracket({
        init: Data /* data to initialize the bracket with */
    })
}

function edit(api,id) {
    Id = id;
    g_api=api;
    var container = $('div#save');
    container.bracket({
        init: Data,
        save: saveFn,
        userData: api,
    });
    var data = container.bracket('data');
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
    var str_data ='{"data":{"teams":[["Team 1","Team 2"],["Team 3","Team 4"]],"results":[[[[4,6],[5,7]],[[7,9],[4,3]]]]},"id":"3"}';

    $.post(g_api,
        {
            data: Data,
            id: Id
        },
        function(data, status){
            console.log("Data: " + data + "\nStatus: " + status);
        });

    // jQuery.ajax({
    //     method: 'PUT',
    //     contentType: 'application/json',
    //     processData: false,
    //     data: JSON.stringify({
    //         name: 'John Smith',
    //         age: 34
    //     }),
    //     url: userData,
    //     // dataType:'json',
    //     // //dataType: 'text',
    //     // type: 'POST',
    //     // // data: {data: Data ,id: Id},
    //     // //data: "data="+Data+"&id="+Id,
    //     // data: JSON.stringify({data: Data ,id: Id}),
    //     // //data: str_data,
    //     // //processData: false,
    //     // contentType: "application/json; charset=UTF-8",
    //     success: function(data) {
    //         console.log("success");
    //         console.log( data);
    //         console.log(JSON.stringify({data: Data ,id: Id}));
    //     },
    //     error:function(data){
    //         console.log("error");
    //         console.log({data: Data ,id: Id});
    //         alert('response data = ' + data);
    //         console.log(JSON.stringify({data: Data ,id: Id}));
    //     }
    // });

}
