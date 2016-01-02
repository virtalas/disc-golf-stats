$(document).ready(function () {
  var city = $("#coursecity").text();
  var country = "fi";

  $.ajax({
    url: "http://api.openweathermap.org/data/2.5/weather?q=" + city + "," + country + "&units=metric&appid=076ac060620f271da6d88921f859c5dd"
  }).done(function(data) {
    console.log("description: " + data.weather[0].description);
    console.log("temperature: " + data.main.temp);
    console.log("wind: " + data.wind.speed);
    console.log("rain: " + data.rain); // object
    console.log("snow: " + data.snow); // object

    var temperature = data.main.temp.toFixed(1); // round to 1 decimal place
    var wind = Math.round(data.wind.speed);

    // Used for /course

    $("#description").text(data.weather[0].description);
    $("#temperature").text(temperature + " °C");
    $("#wind").text(wind + " m/s");

    // Used for /game/new

    var modified = false;

    if (wind >= 5) {
      $('#inputwindy').prop('checked', true);
      modified = true;
    }

    if (data.rain) {
      $("#inputrain").prop('checked', true);
      modified = true;
    }

    if (data.snow) {
      $("#inputsnow").prop('checked', true);
      modified = true;
    }

    if (modified) {
      $("#weathermessage").attr('class', 'alert alert-success');
      $("#weathermessage").text('Säätietoja valittiin automaattisesti. Tarkista valitut olosuhteet.')
    }
  });
});
