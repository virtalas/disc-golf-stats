$(document).ready(function () {
  var city = $("#coursecity").text();
  var country = "fi";

  $.ajax({
    url: "http://api.openweathermap.org/data/2.5/weather?q=" + city + "," + country + "&units=metric&appid=076ac060620f271da6d88921f859c5dd"
  }).done(function(data) {
    console.log("main: " + data.weather[0].main);
    console.log("description: " + data.weather[0].description);
    console.log("temperature: " + data.main.temp);
    console.log("wind: " + data.wind.speed);
    console.log("rain: " + data.rain); // object
    console.log("snow: " + data.snow); // object

    var date = new Date(data.sys.sunset*1000);
    var hours = date.getHours();
    console.log("sunset at: " + hours + " hours");

    var temperature = data.main.temp.toFixed(1); // round to 1 decimal place
    var wind = Math.round(data.wind.speed);

    // Used for /course

    $("#description").text(data.weather[0].description);
    $("#temperature").text(temperature + " °C");
    $("#wind").text(wind + " m/s");
    $("#icon").attr('src', "http://openweathermap.org/img/w/" + data.weather[0].icon + ".png");
    $("#icon").attr('title', data.weather[0].description);

    // Used for /game/new

    $("#inputtemp").attr('value', temperature);

    var modified = false;

    if (wind >= 5) {
      $('#inputwindy').prop('checked', true);
      modified = true;
    }

    if (data.rain) {
      $("#inputrain").prop('checked', true);
      modified = true;
    }

    if (data.weather[0].main == "Snow") {
      $("#inputsnow").prop('checked', true);
      modified = true;
    }

    if (new Date().getHours() > hours) {
      $("#inputdark").prop('checked', true);
      modified = true;
    }

    if (modified) {
      $("#weathermessage").attr('class', 'alert alert-success');
      $("#weathermessage").text('Säätietoja valittiin automaattisesti. Tarkista valitut olosuhteet.')
    }

    // Used for /mobile/new
    $("#animated_progress_bar").delay(2000).hide();
  });
});
