(function ($, Drupal, drupalSettings) {

    'use strict';

    Drupal.behaviors.mybehavior = {
      attach: function (context, settings) {
        const elements = ['.newsroom-teaser', '.news-card article', '.featured-news__card']
        const newsroomTeasers = context.querySelectorAll(elements.toString());

        if (!newsroomTeasers.length > 0) return

        newsroomTeasers.forEach(function(teaser) {
            let link = $(teaser).find('h3 a')
            const loginRedirect = $(teaser).find('.ecc-restricted-content').find('a').attr('href');

            // replace links on card titles IF it is restricted
            loginRedirect && link.attr('href', loginRedirect)

            teaser.addEventListener('click', function() {
                window.location = link.attr('href');
            });
        });
      }
    };

  })(jQuery, Drupal, drupalSettings);
