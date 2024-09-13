(function ($) {
  "use strict";

  jQuery(document).on("click", ".parcel-toggle", function (e) {
    e.preventDefault();
    jQuery(".modal").toggleClass("is-visible");
  });
})(jQuery);