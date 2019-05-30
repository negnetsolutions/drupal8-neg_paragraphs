const YTdeferred = jQuery.Deferred();
window.onYouTubeIframeAPIReady = function() {
  YTdeferred.resolve(window.YT);
};
(function($) {
  $(document).ready(function() {
    $(".paragraph-video.youtube.embedded").each(function() {
      const iframe = $("iframe", this).get(0);
      let player;
      const poster = $(".poster", this);
      const playbtn = $(".poster button", this);
      const _ = this;

      this.onPlayerStateChange = function(event) {
        switch (event) {
          case 0:
            // ended
            _.setPosterVisible(true, true);
            iframe.classList.remove('playing');
            break;
          case 2:
            // paused
            _.setPosterVisible(true, false);
            break;
          default:
            // playing
            _.setPosterVisible(false);
            iframe.classList.add('playing');
            break;
        }
      };

      YTdeferred.done(function(YT) {
        player = new YT.Player(iframe, {
          events: {
            onStateChange: _.onPlayerStateChange
          }
        });

        poster.on("click", function() {
          player.playVideo();
        });
      });

      this.setPosterVisible = function(visible, showPoster) {
        if (visible == true) {
          poster.addClass("visible");
        } else {
          poster.removeClass("visible");
        }

        if (showPoster == false) {
          poster.addClass("transparent");
        } else {
          poster.removeClass("transparent");
        }
      };

      _.setPosterVisible(true, true);
    });
  });
})(jQuery);
