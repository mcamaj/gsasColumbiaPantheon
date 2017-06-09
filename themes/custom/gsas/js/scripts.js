/**
 * @file
 * General JS file for gsas theme
 */


/*
header/main nav/search
*/

(function ($) {
  'use strict';

  Drupal.behaviors.globalHeader = {
    attach: function (context) {
      var namespace = 'globalHeader';
      var $header;
      var $mainNav;
      var $btnSearch;
      var smallScreen = false;
      var smallScreenInit = false;
      var largeScreen = false;
      var $doc = $(document);
      var $win = $(window);
      var winPos;
      var $activeLi = null;
      var mainNavActive = false;
      var mainNavOpen = false;
      var toMainNavEnter;
      var toMainNavLeave;

      var menuBtn = function () {
        $('<button type="button" class="btn-menu" />').html('Menu').on('click', function () {
          if ($mainNav.is(':animated')) {
            return false;
          }
          if (mainNavOpen) {
            $win.scrollTop(winPos);
            $header.removeClass('menu-active menu-open');
            $mainNav.slideUp('fast', function () {
              $mainNav.find('ul[style]').removeAttr('style');
              $mainNav.find('li.is-open').removeClass('is-open');
              mainNavOpen = false;
            });
          }
          else {
            winPos = $win.scrollTop();
            $header.removeClass('search-open').addClass('menu-active');
            $mainNav.slideDown('fast', function () {
              $header.addClass('menu-open').scrollTop(0);
              mainNavOpen = true;
            });
          }
        }).insertBefore($mainNav);
      };

      var searchBtn = function () {
        $btnSearch = $('<button type="button" class="btn-search" />').html('Menu').on('click', function () {
          $header.toggleClass('search-open');
          if ($header.hasClass('search-open')) {
            $header.find('#block-gsassearchblock input[type="search"]').focus();
          }
        }).insertAfter($mainNav);

        if (!Modernizr.touchevents) {
          $btnSearch.on('focus', function () {
            if (smallScreen && mainNavOpen) {
              $header.removeClass('menu-active menu-open');
              $mainNav.find('ul[style]').addBack().removeAttr('style');
              $mainNav.find('li.is-open').removeClass('is-open');
              mainNavOpen = false;
            }
            else if ($activeLi !== null) {
              $activeLi.removeClass('is-open').find('> ul').stop().slideUp('fast');
              $activeLi = null;
            }
          });
        }
      };

      var insertBtns = function () {
        $mainNav.find('> ul > li').each(function () {
          var $li = $(this);
          var $submenu = $li.find('ul');
          if ($submenu.length) {
            $('<button type="button" />').on('click', function () {
              if ($submenu.is(':animated')) {
                return false;
              }
              if ($li.hasClass('is-open')) {
                $submenu.slideUp();
                $li.removeClass('is-open');
              }
              else {
                $submenu.slideDown();
                $li.addClass('is-open');
              }
            }).insertAfter($li.find('> a'));
          }
        });
      };

      var bindHoverEvents = function () {
        $mainNav.on('mouseenter', function () {
          clearTimeout(toMainNavLeave);
          toMainNavEnter = window.setTimeout(function () {
            mainNavActive = true;
            if (!mainNavOpen) {
              $activeLi.addClass('is-open').find('> ul').stop().slideDown();
              mainNavOpen = true;
            }
          }, 200);
        });

        $mainNav.on('mouseleave', function () {
          clearTimeout(toMainNavEnter);
          if (mainNavOpen) {
            toMainNavLeave = window.setTimeout(function () {
              $activeLi.removeClass('is-open').find('> ul').stop().slideUp('fast');
              $activeLi = null;
              mainNavOpen = false;
              mainNavActive = false;
            }, 500);
          }
          else {
            $activeLi = null;
          }
        });

        $mainNav.on('mouseenter', '> ul > li', function () {
          var $thisItem = $(this);
          if ($activeLi !== null && !$thisItem.hasClass('is-open')) {
            $activeLi.removeClass('is-open').find('> ul').stop().slideUp('fast');
          }
          $activeLi = $thisItem;
          if (mainNavActive && !$thisItem.hasClass('is-open')) {
            $activeLi.addClass('is-open').find('> ul').stop().slideDown();
            mainNavOpen = true;
          }
        });
      };

      var bindKeyboardEvents = function () {
        $mainNav.on('focus', '> ul > li > a', function () {
          var $li = $(this).parent();
          if ($li.hasClass('is-open')) {
            $activeLi.removeClass('is-open').find('> ul').stop().slideUp('fast');
            $activeLi = null;
          }
          else {
            if ($activeLi !== null) {
              $activeLi.removeClass('is-open').find('> ul').stop().slideUp('fast');
            }
            $activeLi = $li;
            $activeLi.addClass('is-open').find('> ul').stop().slideDown();
          }
        });
      };

      var bindTouchEvents = function () {
        $mainNav.on('click', '> ul > li > a', function (e) {
          e.stopPropagation();
          var $li = $(this).parent();
          if (!$li.hasClass('is-open')) {
            e.preventDefault();
            if ($activeLi == null) {
              $doc.on('click.' + namespace, function () {
                $activeLi.removeClass('is-open').find('> ul').stop().slideUp('fast');
                $doc.off('click.' + namespace);
                $activeLi = null;
              });
            }
            else {
              $activeLi.removeClass('is-open').find('> ul').stop().slideUp('fast');
            }
            $activeLi = $li;
            $activeLi.addClass('is-open').find('> ul').stop().slideDown();
          }
        });
      };

      var init = function (header) {

        $header = $(header);
        $mainNav = $('nav[id^="block-mainnavigation-2"]');

        searchBtn();

        $.mediaquery('bind', 'mq-' + namespace, '(max-width: 959px)', {
          enter: function () {
            smallScreen = true;

            if (largeScreen) {
              if ($activeLi !== null) {
                $mainNav.find('ul[style]').removeAttr('style');
                $mainNav.find('li.is-open').removeClass('is-open');
                $activeLi = null;
              }

              $mainNav.off();

              if (Modernizr.touchevents) {
                $doc.off('click.' + namespace);
              }
              else {
                mainNavOpen = false;
                mainNavActive = false;
                clearTimeout(toMainNavEnter);
                clearTimeout(toMainNavLeave);
              }
              largeScreen = false;
            }

            if (!smallScreenInit) {
              menuBtn();
              insertBtns();
              smallScreenInit = true;
            }
          },

          leave: function () {
            largeScreen = true;

            if (smallScreen && mainNavOpen) {
              $header.removeClass('menu-active menu-open');
              $mainNav.find('ul[style]').addBack().removeAttr('style');
              $mainNav.find('li.is-open').removeClass('is-open');
              mainNavOpen = false;
            }

            smallScreen = false;

            if (Modernizr.touchevents) {
              bindTouchEvents();
            }
            else {
              bindHoverEvents();
              bindKeyboardEvents();
            }
          }
        });
      };


      $(context).find('header.hdr-global').once('globalHeader').each(function () {
        init(this);
      });
    }
  };


  /*
  sticky footer nav
  */
  Drupal.behaviors.stickyFooter = {
    attach: function (context) {

      var namespace = 'stickyFooter';
      var $footer;
      var resizeDelay;
      var smallScreen = false;

      var bindEvent = function () {
        $footer.find('.header').on('click', function () {
          if ($footer.is(':animated')) {
            return false;
          }
          if ($footer.hasClass('is-open')) {
            $footer.animate({
              bottom: $footer.data('offscreen')
            });
            $footer.removeClass('is-open');
          }
          else {
            $footer.animate({
              bottom: 0
            });
            $footer.addClass('is-open');
          }
        });
      };

      var setPos = function () {
        var offscreen = $footer.find('nav.nav-info').innerHeight() * -1;
        $footer.data('offscreen', offscreen);
        $footer.css('bottom', offscreen);
      };

      var windowEvents = function () {
        $(window).on('resize.' + namespace + ' orientationchange.' + namespace, function () {
          clearTimeout(resizeDelay);
          resizeDelay = setTimeout(function () {
            $footer.removeClass('is-open');
            setPos();
          }, 100);
        });
      };

      var init = function (footer) {

        $footer = $(footer);

        $.mediaquery('bind', 'mq-' + namespace, '(max-width: 959px)', {
          enter: function () {
            setPos();
            windowEvents();
            bindEvent();
            $footer.addClass('is-visible');
            smallScreen = true;
          },

          leave: function () {
            if (smallScreen) {
              clearTimeout(resizeDelay);
              $footer.removeClass('is-open').removeAttr('style');
              $footer.find('.header').off();
              $(window).off('.' + namespace);
              smallScreen = false;
            }
          }
        });
      };

      $(context).find('#block-audiences').once('stickyFooter').each(function () {
        init(this);
      });
    }
  };

  Drupal.behaviors.facesAndFacetsModule = {
    attach: function (context) {
      $(context).find('.faces-and-facets-container').each(function () {

        var init = function () {

          var imageSwiper = new Swiper('.faces-and-facets-container .swiper-container', {
            effect: 'fade',
            speed: 600,
            loop: true,
            fade: {
              crossFade: true
            }
          });

          new Swiper('.faces-and-facets-container .swiper-content-container', {
            effect: 'fade',
            speed: 600,
            loop: true,
            control: imageSwiper,
            nextButton: '.swiper-button-next',
            prevButton: '.swiper-button-prev',
            fade: {
              crossFade: true
            }
          });
        };
        init();
      });
    }
  };

  Drupal.behaviors.twitterModule = {
    attach: function (context) {
      $(context).find('.twitter-module-container').each(function () {

        var init = function () {

          new Swiper('.twitter-module-container .swiper-content-container', {
            effect: 'fade',
            speed: 300,
            loop: true,
            nextButton: '.swiper-button-next',
            prevButton: '.swiper-button-prev',
            fade: {
              crossFade: true
            }
          });
        };
        init();
      });
    }
  };

  Drupal.behaviors.subNavToggle = {
    attach: function (context) {
      $(context).find('#block-sectionheaderblock').each(function () {
        var init = function () {
          var $body = $('body');
          var $subNavTrigger = $('.select-sub-menu-trigger', '#block-sectionheaderblock');

          var $labelEl = $('label', '.select-sub-menu-trigger');

          if ($('#block-sectionheaderblock .menu-item--active-trail a').length > 0) {
            var htmlString = $('#block-sectionheaderblock .menu-item--active-trail a').html().replace('&amp;', '&');
            $labelEl.text(htmlString);
          }

          function toggleSubNav() {
            if (!$body.hasClass('sub-nav-is-open')) {
              $body.addClass('sub-nav-is-open');
              $subNavTrigger.addClass('is-open');
            }
            else {
              $body.removeClass('sub-nav-is-open');
              $subNavTrigger.removeClass('is-open');
            }
          }

          $subNavTrigger.on('click', toggleSubNav);

          $.mediaquery('bind', 'mq-key', '(min-width: 960px)', {
            leave: function () {
              if ($body.hasClass('sub-nav-is-open')) {
                $body.removeClass('sub-nav-is-open');
                $subNavTrigger.removeClass('is-open');
              }
            }
          });
        };

        init();
      });
    }
  };

  Drupal.behaviors.toolkitModule = {
    attach: function (context) {
      var $toolkit = $(context).find('.field--name-field-toolkit');
      var $controls = $toolkit.find('.tab-controls');
      var $tabs = $toolkit.find('.tabs');

      $controls.find('a').on('click', function (event) {
        event.preventDefault();
        var controlId = $(this).data('id');
        var queryString = '[data-id=\"' + controlId + '\"]';

        var $activeTab = $tabs.find('.active');
        var $activeControl = $controls.find('.active');

        if (!$(this).hasClass('active')) {
          $activeTab.removeClass('active');
          $activeControl.removeClass('active');

          $controls.find(queryString).addClass('active');
          $tabs.find(queryString).addClass('active');
        }
      });
    }
  };

  Drupal.behaviors.blogLandingDropdownCategory = {
    attach: function (context) {
      $('#views-exposed-form-blog-landing-list-block-1 .form-item-category select', context).dropdown({
        label: 'Open to'
      });

      if ($('#views-exposed-form-blog-landing-list-block-1 .form-item-category option:selected').val() === 'All') {
        $('#views-exposed-form-blog-landing-list-block-1 .form-item-category .fs-dropdown-selected', context).html('Category');
      }
    }
  };

  Drupal.behaviors.eventCalendarDropdownAccess = {
    attach: function (context) {
      $('.form-item-field-event-access-target-id select', context).dropdown({
        label: 'Open to'
      });

      if ($('.form-item-field-event-access-target-id option:selected').val() === 'Any') {
        $('.form-item-field-event-access-target-id .fs-dropdown-selected', context).html('Category');
      }
    }
  };

  Drupal.behaviors.eventCalendarDropdownOrganizer = {
    attach: function (context) {
      $('.form-item-field-event-organizer-target-id select', context).dropdown({
        label: 'Organizer'
      });

      if ($('.form-item-field-event-organizer-target-id option:selected').val() === 'All') {
        $('.form-item-field-event-organizer-target-id .fs-dropdown-selected', context).html('Organizer');
      }
    }
  };

  Drupal.behaviors.eventCalendarDropdownDate = {
    attach: function (context) {
      $('.form-item-gsas-events-date-select select', context).dropdown({
        label: 'Date'
      });

      if ($('.form-item-gsas-events-date-select option:selected').val() === 'All') {
        $('.form-item-gsas-events-date-select .fs-dropdown-selected', context).html('Date');
      }
    }
  };

  Drupal.behaviors.eventCalendarAutosubmit = {
    attach: function (context) {
      var $submitButton = $('.view-event-calendar.view-id-event_calendar .views-exposed-form input.form-submit[value="Apply"]', context);

      $(context).find('.view-event-calendar.view-id-event_calendar').once('eventCalendarAutosubmit').each(function () {
        $('.form-select', this)
        .on('change', function () {
          _.defer(function () {
            $submitButton.click();
          });
        });
      });
    }
  };

  Drupal.behaviors.blogLandingAutosubmit = {
    attach: function (context) {
      var $submitButton = $('.view-blog-landing-list.view-display-id-block_1 .views-exposed-form input.form-submit[value="Apply"]', context);

      $(context).find('.view-blog-landing-list.view-display-id-block_1').once('blogLandingAutosubmit').each(function () {
        $('.form-select', this)
        .on('change', function () {
          _.defer(function () {
            $submitButton.click();
          });
        });
      });
    }
  };

  Drupal.behaviors.blogDropdownCategory = {
    attach: function (context) {
      var $resultsForm = $('#views-exposed-form-search-results');
      var $formSelect = $resultsForm.find('.js-form-type-select');

      if (!$resultsForm.find('.radio-buttons').length) {
        $resultsForm.find('.js-form-type-select').append('<div class="radio-buttons"></div>');
        var $radioButtons = $resultsForm.find('.radio-buttons');

        $formSelect.find('select').dropdown({
          label: 'Category'
        });

        $formSelect.find('.fs-dropdown-selected').html('Category');
        $formSelect.appendTo($resultsForm);

        var $options = $formSelect.find('select option');
        var count = $options.length;

        $options.each(function (i) {
          var selected = $(this).attr('selected') === 'selected' ? 'selected' : '';
          $radioButtons.append('<div><span class="button ' + selected + '" data-id="' + $(this).val() +
            '"></span><span class="text">' + $(this).html() + '</span></div>');

          if (i === (count - 1)) {
            $resultsForm.addClass('loaded');
          }
        });

        $radioButtons.find('.button').click(function () {
          $radioButtons.find('.button').removeClass('selected');
          $(this).addClass('selected');
          var id = $(this).data('id');
          var query = 'select option[value="' + id + '"]';
          $formSelect.find(query).attr('selected', 'selected');
          $resultsForm.submit();
        });
      }
    }
  };

  Drupal.behaviors.blockEqualizer = {
    attach: function (context, settings) {
      // Enter 1280 so leave on 960 doesn't break it
      $.mediaquery('bind', 'blockEqualizer', '(min-width: 960px)', {
        enter: function () {

          $('#block-views-block-story-menu-content-block-1').equalize({
            target: '.views-row .group-text'
          });
        },
        leave: function () {
          $('.view-story-menu-content.view-display-id-block_1').equalize('destroy');

          $('#block-views-block-story-menu-content-block-1').equalize('destroy');
        }
      });
    }
  };

  Drupal.behaviors.storyMenuInfiniteScroll = {
    attach: function (context, settings) {
      var $storyMenu = $('.view-story-menu-content');
      var length = $storyMenu.find('.views-row').length;

      function checkReload() {
        if ($storyMenu.find('.views-row').length === length) {
          window.setTimeout(checkReload, 100);
        }
        else {
          $(window).trigger('resize');
        }
      }
      $storyMenu.find('.pager a').click(function () {
        checkReload();
      });
    }
  };

  Drupal.behaviors.dropdownItems = {
    attach: function (context, settings) {
      var $items = $('.paragraph--type--dropdown-item');

      if ($items.length) {
        $items.find('.field--name-field-title').click(function () {
          var $parent = $(this).closest('.paragraph--type--dropdown-item');
          $parent.find('.field--name-field-body').slideToggle();
          $(this).toggleClass('is-open');
        });
      }
    }
  };

  Drupal.behaviors.tableHeaders = {
    attach: function (context, settings) {
      var $table = $('.field--name-field-body table');

      if ($table.length) {
        $table.each(function () {
          var $headers = $(this).find('th');
          var $rows = $(this).find('tbody tr');

          $headers.each(function (index, value) {
            var header = $(this).html();
            $rows.each(function () {
              $(this).find('td').eq(index).prepend('<div class="header">' + header + '</div>');
            });
          });
        });
      }
    }
  };

  Drupal.behaviors.toolkitControls = {
    attach: function (context, settings) {
      var $controls = $('.field--name-field-toolkit .tab-controls > div');

      if ($controls.length && $controls.length < 3) {
        $controls.parent().css('width', '40%');
      }
    }
  };

})(jQuery);
