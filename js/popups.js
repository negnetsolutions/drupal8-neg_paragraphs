(function($) {
  $(document).ready(function() {
    $("a.image-lightbox").magnificPopup({
      type: "image",
      removalDelay: 160,
      preloader: false,
      fixedContentPos: false,
    });
    $("a.lightbox").magnificPopup({
      type: "iframe",
      mainClass: "mfp-fade",
      removalDelay: 160,
      preloader: false,
      fixedContentPos: false,
      iframe: {
        patterns: {
          youtue: {
            index: "youtu.be/",
            id: "/",
            src: "//www.youtube.com/embed/%id%?autoplay=1"
          },
          youtube: {
            index: "youtube.com/",
            id(url) {
              const m = url.match(/[\\?\\&]v=([^\\?\\&]+)/);
              if (!m || !m[1]) return null;
              return m[1];
            },
            src: "//www.youtube.com/embed/%id%?autoplay=1"
          },
          vimeo: {
            index: "vimeo.com/",
            id: "/",
            src: "//player.vimeo.com/video/%id%"
          }
        }
      }
    });
  });
})(jQuery);
