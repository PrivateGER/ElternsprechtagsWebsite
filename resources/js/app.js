require("./bootstrap");
require("sweetalert");
require("jquery");
require("jquery-ui-bundle");

import $ from 'jquery';

window.$ = window.jQuery = $;

//import '~jquery-ui/ui/widgets/autocomplete.js';

//import LogRocket from 'logrocket';
//LogRocket.init('vvfvic/elternsprechtag');

import swal from 'sweetalert';

window.openAdmin = () => {
    fetch("/admin")
        .then((res) => { return res.text() })
        .then((res) => {
            document.getElementById("adminDashboardBox").innerHTML = res;
        });
};

window.updateChatMessages = () => {
    fetch(`/home/chatMessagesAPI?lehrer=${lehrer}&name=${name}`)
        .then((res) => { return res.json() })
        .then((res) => {
            document.getElementById("messages").innerHTML = "";

            let i = 0;
            res.forEach((msg) => {
                let li = document.createElement("li");
                li.classList = "list-group-item";
              
                let parsedMsg = msg.split("|||");
                msg = parsedMsg[0];
                li.innerText = msg;
                li.id = "msg" + i;
            
                document.getElementById("messages").appendChild(li);
              
                let date = parsedMsg[1];
              
                let span = document.createElement("span");
                span.style = "float: right";
                span.innerText = date;
              
                document.getElementById("msg" + i).appendChild(span);
                i++;
            })
        });
};

window.updateChatNotifications = () => {
    fetch("/home/chatNotifs")
        .then((res) => { return res.text() })
        .then((res) => {
            document.getElementById("chatNotifs").innerHTML = res;
        });
};

window.sendChatMessage = () => {
    let text = document.getElementById("chatMessage").value;

    let data = {
        "message": text,
        "recipient": recipient
    };

    const formBody = Object.keys(data).map(key => encodeURIComponent(key) + '=' + encodeURIComponent(data[key])).join('&');

    fetch("/home/sendChatMessage", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
        },
        body: formBody}
    ).then((res) => { return res.json() })
     .then((json) => {
         if(json.err === undefined) {
             document.getElementById("chatMessage").value = "";
             updateChatMessages();
         } else {
             swal("Error", json.err, "error");
         }
     });
};


window.requestDate = (dateString) => {
    let lehrerID = document.getElementById("lehrerID").value;
    if(confirm("Sind Sie sicher dass Sie den Termin um " + dateString + " anfragen möchten?")) {

        let data = {
            "lehrerID": lehrerID,
            "date": dateString
        };

        const formBody = Object.keys(data).map(key => encodeURIComponent(key) + '=' + encodeURIComponent(data[key])).join('&');

        fetch("/home/lehrer/request", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
            },
            body: formBody
        }).then((res) => { return res.json() })
            .then((res) => {
                if(res.err === undefined) {
                    swal("OK", "Anfrage erfolgreich versendet!", "success")
                        .then(() => {
                            window.location.reload();
                        })

                } else {
                    swal("Error", res.err, "error");
                }
            })
    }
};

window.updateRequestsS = () => {
    fetch("/home/schueler/requestList")
        .then((res) => { return res.text() })
        .then((res) => {
            document.getElementById("schuelerRequestBox").innerHTML = res;
        });
};

window.cancelReqSchueler = (reqID) => {
    if(confirm("Sind Sie sicher dass Sie die Anfrage zurückziehen möchten?")) {

        let data = {
            "reqID": reqID
        };

        const formBody = Object.keys(data).map(key => encodeURIComponent(key) + '=' + encodeURIComponent(data[key])).join('&');

        fetch("/home/lehrer/cancelRequestS", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
            },
            body: formBody
        }).then((res) => { return res.json() })
            .then((res) => {
                if(res.err !== undefined) {
                    swal("Error", res.err, "error");
                } else {
                    swal("OK", "Anfrage erfolgreich zurückgezogen!", "success");
                }
            })
            .then(() => updateRequestsS());
    }
};

window.approveRequest = (reqID) => {
    if(confirm("Sind Sie sicher dass Sie diese Anfrage annehmen wollen?\nAchtung: Sollten andere Anfragen zur gleichen Zeit existieren, werden diese abgelehnt!")) {
        let data = {
            "reqID": reqID
        };

        const formBody = Object.keys(data).map(key => encodeURIComponent(key) + '=' + encodeURIComponent(data[key])).join('&');

        fetch("/home/lehrer/acceptRequest", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
            },
            body: formBody
        }).then((res) => { return res.json() })
            .then((res) => {
                if(res.err !== undefined) {
                    swal("Error", res.err, "error");
                } else {
                    swal("Erfolgreich", "Termin wurde erstellt!", "success")
                }
            })
            .then(() => updateLehrerTerminplan())
            .then(() => updateLehrerDashboard());
    }
};

window.denyRequest = (reqID) => {
    if(confirm("Sind Sie sicher dass Sie diese Anfrage ablehnen wollen?")) {
        let data = {
            "reqID": reqID
        };

        const formBody = Object.keys(data).map(key => encodeURIComponent(key) + '=' + encodeURIComponent(data[key])).join('&');

        fetch("/home/lehrer/denyRequest", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
            },
            body: formBody
        }).then((res) => { return res.json() })
            .then((res) => {
                if(res.err !== undefined) {
                    swal("Error", res.err, "error");
                } else {
                    swal("Erfolgreich", "Termin wurde abgelehnt!", "success")
                }
            })
            .then(() => updateLehrerTerminplan())
            .then(() => updateLehrerDashboard());
    }
};

window.updateLehrerTerminplan = () => {
    fetch("/home/lehrer/terminplan")
        .then((res) => { return res.text() })
        .then((res) => {
            document.getElementById("lehrerTerminplanBox").innerHTML = res;
        });
};

window.updateLehrerDashboard = () => {
    fetch("/home/lehrer/dashboard")
        .then((res) => { return res.text() })
        .then((res) => {
            document.getElementById("lehrerDashboardBox").innerHTML = res;
        });
};

function getQueryParams(qs) {
    qs = qs.split('+').join(' ');

    var params = {},
        tokens,
        re = /[?&]?([^=]+)=([^&]*)/g;

    while (tokens = re.exec(qs)) {
        params[decodeURIComponent(tokens[1])] = decodeURIComponent(tokens[2]);
    }

    return params;
}
