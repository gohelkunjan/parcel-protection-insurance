(function ($) {
  "use strict";

  jQuery(function ($) {
    $(".add-repeater-text-field").click(function () {
      var $lastRow = $(".repeater-text-fields-table tbody tr:last");
      var $newRow = $lastRow.clone();
      $newRow.find('input[type="number"]').val("");
      $newRow.find(".remove-repeater-text-field").click(function () {
        $(this).closest("tr").remove();
      });
      $newRow.insertAfter($lastRow);
    });

    $(document).on("click", ".remove-repeater-text-field", function () {
      $(this).closest("tr").remove();
    });

    if ($("#calculation_method option:selected").val() == "standalone") {
      $(".standalone").show();
      $(".dynamic").hide();
    } else if ($("#calculation_method option:selected").val() == "dynamic") {
      $(".dynamic").show();
      $(".standalone").hide();
    } else {
      $(".standalone").show();
      $(".dynamic").hide();
    }

    $("#calculation_method").change(function () {
      if ($(this).val() == "standalone") {
        $(".dynamic").hide();
        $(".standalone").show();
        $("#insurance_frequency").prop("required", false);
        $("#insurance_cost").prop("required", false);
        $("#standalone_insurance_frequency").prop("required", true);
        $("#standalone_insurance_cost").prop("required", true);
      }
      if ($(this).val() == "dynamic") {
        $("#standalone_insurance_frequency").prop("required", false);
        $("#standalone_insurance_cost").prop("required", false);
        $("#insurance_frequency").prop("required", true);
        $("#insurance_cost").prop("required", true);
        $(".standalone").hide();
        $(".dynamic").show();
      }
    });
  });
})(jQuery);
