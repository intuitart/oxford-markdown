// add target="_blank" attribute to links when target=_blank is added as a url parameter
jQuery(document).ready(function($) {
  $('.mdToHtml a[href$="target=_blank"]').each(function() {
    $(this).attr('href', $(this).attr('href').replace(/(\?|&|;)target=_blank$/, ''));
    $(this).attr('target', '_blank');
  });
});
