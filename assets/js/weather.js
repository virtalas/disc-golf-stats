/*
*  Fetch the temperature on a course
*/

$(document).ready(function () {
  var city = $("#coursecity").text();
  var country = "fi";

  $.ajax({
    url: "http://api.openweathermap.org/data/2.5/weather?q=" + city + "," + country + "&units=metric&appid=076ac060620f271da6d88921f859c5dd"
  }).done(function(data) {
    $("#temperature").text(data.main.temp + " °C");
  });
});
