var chuckles = {};

chuckles.gui = (function () {
    function show() {
        var chucklesElement = document.getElementById("quote");

        clear(chucklesElement);
        populate(chucklesElement);
    }

    function clear(element) {
        while (element.firstChild) {
            element.removeChild(element.firstChild);
        }
    }

    function populate(element) {
        var chuckle = chuckles.data.list();
        var jokeElement = document.createElement("p");
        jokeElement.className = "fancy";
        jokeElement.appendChild(document.createTextNode(chuckle));
        element.appendChild(jokeElement);
    }

    return {
        show: show
    };

})();

chuckles.data = (function (displayHook) {
    var joke;

    function load(url) {
        // tässä voi aloittaa datan lataamisesta
        var req = new XMLHttpRequest();

        req.onreadystatechange = function () {
            // jos tila ei ole valmis, ei käsitellä
            if (req.readyState !== this.DONE) {
                return false;
            }

            // jos statuskoodi ei ole 200 (ok), ei käsitellä
            if (req.status !== 200) {
                console.log("status " + req.status);
                return false;
            }

            var res = JSON.parse(req.responseText);
            joke = res.value.joke;
            // Correct quotes
            joke = joke.replace(/&quot;/g,'"');

            displayHook();
        }

        req.open("GET", url);
        req.send();
    }

    function list() {
        return joke;
    }

    return {
        load: load,
        list: list
    };

})(chuckles.gui.show);

function init() {
    var now = new Date();
    var start = new Date(now.getFullYear(), 0, 0);
    var diff = now - start;
    var oneDay = 1000 * 60 * 60 * 24;
    var day = Math.floor(diff / oneDay);

    var name = document.getElementById("firstname").innerHTML;

    var url = "http://api.icndb.com/jokes/" + day + "?firstName=" + name + "&lastName=";
    console.log("Fetching joke: " + url);

    chuckles.data.load(url);
}
