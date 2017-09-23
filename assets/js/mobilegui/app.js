window.myApp = {};

myApp.holeIndex = -1;

document.addEventListener('init', function(event) {
  var page = event.target;

  if (myApp.controllers.hasOwnProperty(page.id)) {
    myApp.controllers[page.id](page);
  }

  myApp.services.nextHole();
});
