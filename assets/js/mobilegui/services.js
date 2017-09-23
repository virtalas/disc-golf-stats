myApp.services = {
  nextHole: function() {
    if (myApp.holeIndex == course.holes.length - 1) {
      return;
    }

    myApp.holeIndex++;
    $("#toolbarHoleNum").html(myApp.holeIndex + 1);
    $("#toolbarPar").html(course.holes[myApp.holeIndex].par);
  }
};
