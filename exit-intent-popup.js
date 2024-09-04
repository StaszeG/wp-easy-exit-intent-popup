jQuery(document).ready(function ($) {
  var exitIntentShown = false;

  $(document).mouseleave(function () {
    console.log("mouse leave registered");
    console.log(exitPopupData);
    if (
      !exitIntentShown &&
      typeof exitPopupData.popupImage !== "undefined" &&
      exitPopupData.popupImage !== ""
    ) {
      exitIntentShown = true;
      $("body").append(
        '<div id="exit-intent-popup"><img src="' +
          exitPopupData.popupImage +
          '" alt="Exit Intent Popup" /></div>'
      );
      $("#exit-intent-popup").css({
        position: "fixed",
        top: "50%",
        left: "50%",
        transform: "translate(-50%, -50%)",
        zIndex: 1000,
      });
    }
  });
});
