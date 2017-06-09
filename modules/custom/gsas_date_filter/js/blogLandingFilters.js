(function ($) {
  'use strict';

  var initDateDropdown = function initDateDropdown(block, select, from_str, to_str) {
    $(select).on('change', function () {
      var rangeText = $('option:selected', select).text();
      var $fromDate = $(from_str, block);
      var $thruDate = $(to_str, block);

      if (rangeText === 'Today') {
        $fromDate.val(moment().startOf('day').format('MM/DD/YYYY HH:mm'));
        $thruDate.val(moment().endOf('day').format('MM/DD/YYYY HH:mm'));

      }
      else if (rangeText === 'This Week') {
        $fromDate.val(moment().startOf('week').format('MM/DD/YYYY HH:mm'));
        $thruDate.val(moment().endOf('week').format('MM/DD/YYYY HH:mm'));

      }
      else if (rangeText === 'Last Week') {
        $fromDate.val(moment().subtract(1, 'weeks').startOf('week').format('MM/DD/YYYY HH:mm'));
        $thruDate.val(moment().subtract(1, 'weeks').endOf('week').format('MM/DD/YYYY HH:mm'));

      }
      else if (rangeText === 'Last Month') {
        $fromDate.val(moment().subtract(1, 'months').startOf('week').format('MM/DD/YYYY HH:mm'));
        $thruDate.val(moment().subtract(1, 'months').endOf('week').format('MM/DD/YYYY HH:mm'));

      }
      else if (rangeText === 'This Year') {
        $fromDate.val(moment().startOf('year').format('MM/DD/YYYY HH:mm'));
        $thruDate.val(moment().endOf('year').format('MM/DD/YYYY HH:mm'));

      }
      else if (rangeText === 'Last Year') {
        $fromDate.val(moment().subtract(1, 'years').startOf('year').format('MM/DD/YYYY HH:mm'));
        $thruDate.val(moment().subtract(1, 'years').endOf('year').format('MM/DD/YYYY HH:mm'));

      }
      else {
        $fromDate.val(null);
        $thruDate.val(null);
      }
    });
  };

  Drupal.behaviors.eventsLandingFilters = {
    attach: function (context) {
      // Attach date range filter to the blog list view.
      $(context).find('#views-exposed-form-blog-landing-list-block-1').once('eventsLandingFilters').each(function () {
        initDateDropdown(
          this,
          $('.form-item-gsas-events-date-select select', this),
          '.form-item-created-start input',
          '.form-item-created-thru input'
        );
      });
    }
  };
}(jQuery));
