jQuery(document).ready(function ($) {
  // $(".table").basictable();

  if (window.location.href.indexOf("?delete=") > 0) {
    console.log("found it");
    let queryString = window.location.search;
    if (queryString != "") {
      setTimeout(function () {
        window.location.href = window.location.href.split("/?")[0];
      }, 100);
    }
  }

  $(".deleteData").on("click", function () {
    var id = $(this).attr("data-id");
    alertify.defaults.theme.ok = "btn btn-primary";
    alertify.defaults.theme.cancel = "btn btn-danger";
    alertify.defaults.theme.input = "form-control";
    alertify.confirm(
      "Delete",
      "Are you sure you want to delete?",
      function () {
        $.ajax({
          url: "https://api.sosinventory.com/api/v2/vendor/" + id,
          headers: {
            Authorization:
              "Bearer jN2a5hKiTNn_U7UMLpGr-YCDttmudk80wssItpQm1z8mnXrXuUpXaq2Ok2Wfi700U2n_vZkobNrAkMATrHk-1neZjAfwpNHiFXUPlxB6okPYSuonbSbD1cF3qrBuONBUJpQeS0h4oZyu5v5CkBL7meYSZeAWuhK62J39pyTiivS_fVVtgpSPB4IQXjX58OBs1K_gr9CFuBuQGPStr0DR97fPOYVl53M6-u2CC03FXgR496TF3a1m42InkAxsM572L9i9JZHTbMbvKDqspJUi3rs1Z1iE-Goqhe_GuxYhUUERvRdE",
            Host: "api.sosinventory.com",
            "Content-Type": "application/x-www-form-urlencoded",
          },
          type: "DELETE",
          success: function (response) {
            console.log("success", response);
            location.reload();
          },
          error: function (response) {
            console.log("error", response);
          },
        });
        alertify.success("Record successfully deleted");
      },
      function () {
        alertify.error("Cancel");
      }
    );
  });
});
