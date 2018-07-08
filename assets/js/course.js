$(document).ready(function(){
  $("#high_score_button").click(function(eventObject) {
    $("#high_score_button").removeClass("btn-default").addClass("btn-primary").addClass("disabled_link");
    $("#user_high_score_button").removeClass("btn-primary").addClass("btn-default").removeClass("disabled_link");
    $("#high_scores_by_user").hide();
    $("#high_scores").show();
  });

  $("#user_high_score_button").click(function(eventObject) {
    $("#user_high_score_button").removeClass("btn-default").addClass("btn-primary").addClass("disabled_link");
    $("#high_score_button").removeClass("btn-primary").addClass("btn-default").removeClass("disabled_link");
    $("#high_scores").hide();
    $("#high_scores_by_user").show();
  });
});
