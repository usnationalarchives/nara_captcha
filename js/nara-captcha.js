(($, Drupal, drupalSettings) => {
  const $captchaZoomBox = $('#nara-captcha-zoom');

  $(document).ready(() => {
    $('#edit-captcha-response').append($captchaZoomBox);
  });

  $('.captcha-zoom').on('click', (event) => {
    event.preventDefault();
    $('#nara-captcha-zoom').removeClass('visually-hidden');
    const $clickedImage = $(event.target).closest('.captcha-response-item').children('.captcha-image').clone();
    $('#inner-zoom').empty();
    $('#inner-zoom').append($clickedImage);
  });

  $('#zoom-close').on('click', (event) => {
    event.preventDefault();
    $('#nara-captcha-zoom').addClass('visually-hidden');
    $('#inner-zoom').empty();
  });
})(jQuery, Drupal, drupalSettings);
