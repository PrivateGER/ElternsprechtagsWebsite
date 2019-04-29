require("./bootstrap");
require("./jquery-ui");

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