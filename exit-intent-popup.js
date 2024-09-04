jQuery(document).ready(function ($) {
  var exitIntentShown = false;

  $(document).mouseleave(function () {
    if (
      !exitIntentShown &&
      typeof exitPopupData.popupImage !== "undefined" &&
      exitPopupData.popupImage !== ""
    ) {
      exitIntentShown = true;
      $("#exit-intent-popup").css("display", "flex");
    }
  });

  $("#exit-intent-popup").on("click", function () {
    $("#exit-intent-popup").css("display", "none");
    exitIntentShown = false;
  });
});
