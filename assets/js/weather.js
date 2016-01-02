/*
*  Fetch the temperature on a course
*/

$(document).ready(function () {
  var city = $("#coursecity").text();
  var country = "fi";

  $.ajax({
    url: "http://api.openweathermap.org/data/2.5/weather?q=" + city + "," + country + "&units=metric&appid=076ac060620f271da6d88921f859c5dd"
  }).done(function(data) {
    console.log("temperature: " + data.main.temp);
    var temperature = data.main.temp.toFixed(1); // round to 1 decimal place
    $("#temperature").text(temperature + " °C");
  });
});
