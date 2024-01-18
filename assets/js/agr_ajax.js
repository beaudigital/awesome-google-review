jQuery(document).ready(function ($) {
  $("#agr_ajax_form").submit(function () {
    var place_id = $("#place_id").val().replace(/\s/g, "");
    var nonce = $("#awesome_google_review_nonce").val();

    if (place_id.trim() !== "" && !/\s/.test(place_id)) {
      // place_id = $("#place_id").val().replace(/\s/g, "");
      $.ajax({
        type: "POST",
        url: ajax_object.ajax_url,
        dataType: "json",
        data: {
          action: "our_ajax_action",
          place_id: place_id,
          nonce:nonce,
        },
        beforeSend: function () {
          $(".submit_btn .label").text("Loading...");
        },
        success: function (response) {},
        complete: function (response) {
          var response = response.responseJSON;
          if (response.success === 1) {
            setTimeout(function () {
              // explodePage();
              toastr.success("", response.msg);
              $(".submit_btn .label").text("Submit");
              $("#place_id").val(response.data.place_id);
            }, 350);
          } else {
            setTimeout(function () {
              toastr.error("", response.msg);
            }, 350);
          }
        },
      });
    } else {
      toastr.error("", "Place ID should not contain white spaces");
    }
    return false;
  });
});

function explodePage() {
  function random(max) {
    return Math.random() * (max - 0) + 0;
  }

  var particleContainer = document.createDocumentFragment();

  for (var i = 0; i < 500; i++) {
    var styles =
      "top: " +
      random(window.innerHeight) +
      "px; left: " +
      random(window.innerWidth) +
      "px; animation-delay: " +
      random(1000) +
      "ms;";

    var particle = document.createElement("div");
    particle.className = "particle";
    particle.style.cssText = styles.toString();
    particleContainer.appendChild(particle);
  }

  document.body.appendChild(particleContainer);
}

// Call explodePage function after the page has loaded
