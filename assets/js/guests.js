// Needed on every page with forms.new_game() html macro

var guestCount = 0;

$(document).ready(function () {
    // Listeners

    $("#guestsCheckbok").click(function() {
        // When the "guests" is checked, disable send button and insert input fields.
        if ($(this).is(':checked')) {
            if (guestCount === 0) increaseGuestCount();
            sendButtonEnabled(false);
            $("#guestNameFields").css("display", "block");
            $("#guestNameField").children().focus();
        } else {
            sendButtonEnabled(true);
            $("#guestNameFields").css("display", "none");
        }
    });

    $("#addNewNameField").click(function() {
        increaseGuestCount();
        var newRow = $("<div class='row'></div>");
        $("#guestNameFields").append(newRow);
        $("#guestNameFields").append("<br>");
        $("#guestNameLabel").clone().appendTo(newRow);
        var newNameField = $("#guestNameField").clone();
        newNameField.prop("id", "guestNameField" + guestCount)
        newNameField.children().attr("name", "guest" + guestCount);
        newNameField.children().val("");
        newNameField.appendTo(newRow).children().focus();
    });

    $("#guestNameField").keyup(function() {
        console.log("gfdgd")
        if ($.trim($(this).children().val())) {
            // Text field contains letters
            sendButtonEnabled(true);
        } else {
            sendButtonEnabled(false)
        }
    });
});

function sendButtonEnabled(enabled) {
    $("#newGameButton").prop("disabled", !enabled);
}

function increaseGuestCount() {
    guestCount++;
    $("#guestCount").attr("value", guestCount);
}
