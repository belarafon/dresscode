;(function ($) {
  // Single product attributes
  function variations_labels() {
    $('.variations_form select, .fami_variations_form select').each(function () {
      var _this = $(this);
      _this.find('option').each(function () {
        var _value = $(this).attr('value'),
          _name = $(this).data('name');

        if (_value !== '') {
          $('[data-value="' + _value + '"]').attr('title', _name);
        }
      });
    });
  }

  $(document).on('woocommerce_variation_has_changed wc_variation_form', function () {
    setTimeout(function () {
      variations_labels();
    },200)
  });

  $(document).ajaxComplete(function () {
    setTimeout(function () {
      variations_labels();
    },200)
  });z
})(jQuery);