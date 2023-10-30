(function ($, Drupal, drupalSettings) {

    'use strict';
  
    Drupal.behaviors.mybehavior = {
      attach: function (context, settings) {
        const newsroomTeasers = document.querySelectorAll('.newsroom-teaser');

        newsroomTeasers.forEach(function(teaser) {
            teaser.addEventListener('click', function() {
                const linkHref = $(this).find('.newsroom-teaser__title a').attr('href');

                if (linkHref !== '') {
                    window.location = linkHref;
                    return false;
                } else {
                    return false;
                }
            });
        });
      }
    };
  
  })(jQuery, Drupal, drupalSettings);
  