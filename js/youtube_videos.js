var YTdeferred = jQuery.Deferred();
window.onYouTubeIframeAPIReady = function() {
  YTdeferred.resolve(window.YT);
};(function($) {

  $(document).ready(function(){
    $('.paragraph-video.youtube').each(function(){
      var iframe = $('iframe',this).get(0);
      var player;
      var poster = $('.poster', this);
      var playbtn = $('.poster button', this);
      var _ = this;

      this.onPlayerStateChange = function(event) {
        console.log(event);
        switch(event) {
          case 0:
            //ended
            _.setPosterVisible(true,true);
            break;
          case 2:
            //paused
            _.setPosterVisible(true,false);
            break;
          default:
            //playing
            _.setPosterVisible(false);
            break;
        }
      }

      YTdeferred.done(function(YT) {
        player = new YT.Player(iframe, {
          events: {
            'onStateChange': _.onPlayerStateChange,
          }
        });

        poster.on('click',function(){
          player.playVideo();
        });

      });

      this.setPosterVisible = function(visible,showPoster){
        if(visible == true){
          poster.addClass('visible');
        }
        else {
          poster.removeClass('visible');
        }

        if(showPoster == false){
          poster.addClass('transparent');
        }
        else {
          poster.removeClass('transparent');
        }
      }

      _.setPosterVisible(true,true);

    });
  });
})(jQuery);
