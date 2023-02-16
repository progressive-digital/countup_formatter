(function (window, $, Drupal, debounce, countUp)  {
  'use strict';

  /**
   * Attach the countUp.js behaviour to the expected elements.
   *
   * @type {Drupal~behavior}
   */
  Drupal.behaviors.countupformatter = {

    /**
     * The selector to be used for CountUp targets.
     *
     * @var string
     */
    stepSelector: '.countup-formatter',

    /**
     * {@inheritDoc}
     */
    attach: function attach() {
      if ($('body').once('countup').length) {
        $(window).on('DOMContentLoaded load resize scroll', debounce(this.checkVisibility.bind(this), 50));
      }
    },

    checkVisibility: function checkVisibility() {
      var o = this,
        $elements = $(this.stepSelector + ':not(.countup-processed)');

      $elements.each(function(index, element) {
        var rect = element.getBoundingClientRect();

        if ($(element).is(':visible') &&
          $(element).css('opacity') > 0 &&
          rect.top >= 0 &&
          rect.left >= 0 &&
          rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
          rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        ) {
          $(element).addClass('countup-processed');
          o.countup(element);
        }
      });
    },

    /**
     * Performs the countUp action.
     *
     * @param element
     */
    countup: function(element) {
      if (typeof countUp.CountUp !== 'function') {
        return;
      }

      var $element = $(element),
       options = $.extend({ start: 0, duration: 2.5, decimalPlaces: 0}, $element.data());

      if (typeof options.end === "undefined") {
        if ($.isNumeric($element.html())) {
          options.end = $element.html();
          $element.attr('data-end', $element.html());
        }
        else {
          // Nothing valid is defined for the "end" value, exit here...
          return;
        }
      }

      // Instantiate the plugin.
      var count = new countUp.CountUp(element, options.end, options);
      if (!count.error) {
        count.start();
      }
      else if (window.console) {
        window.console.error(count.error);
      }
    }

  };

})(window, jQuery, window.Drupal, window.Drupal.debounce, window.countUp);
