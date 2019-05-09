require("./bootstrap");
require("./jquery-ui");
require("sweetalert");

import swal from 'sweetalert';

window.setupLehrersuche = () => {
    document.getElementById("lehrerInput").addEventListener('input', function (evt) {
        processLehrerSearch(this.value);
    });

    document.getElementById("lehrerInput").addEventListener('paste', function (evt) {
        processLehrerSearch(this.value);
    });

    document.getElementById("lehrerInput").addEventListener('select', function (evt) {
        processLehrerSearch(this.value);
    });

    window.processLehrerSearch = (name) => {
        if(name.length > 4) {
            document.getElementById("lehrerSearchResult").innerHTML = "";
            console.log(name)
        }
    }
};

window.requestDate = (dateString) => {
    let lehrerID = document.getElementById("lehrerID").value;
    if(confirm("Sind sie sicher dass sie den Termin um " + dateString + " anfragen möchten?")) {

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
                    swal("OK", "Anfrage erfolgreich versendet!", "success");
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
    if(confirm("Sind sie sicher dass sie die Anfrage zurückziehen möchten?")) {

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
    if(confirm("Sind sie sicher dass sie diese Anfrage annehmen wollen?\nAchtung: Sollten andere Anfragen zur gleichen Zeit existieren, werden diese abgelehnt!")) {
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
    if(confirm("Sind sie sicher dass sie diese Anfrage ablehnen wollen?")) {
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
}