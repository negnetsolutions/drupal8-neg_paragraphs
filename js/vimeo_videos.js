(function($) {
  $(document).ready(function(){
    $('.paragraph-video.vimeo').each(function(){
      var iframe = $('iframe',this).get(0);
      var player = new Vimeo.Player(iframe);
      var poster = $('.poster', this);
      var playbtn = $('.poster button', this);

      var setPosterVisible = function(visible,showPoster){
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

      player.on('play', function(){
        setPosterVisible(false);
      });

      player.on('pause', function(){
        setPosterVisible(true,false);
      });

      player.on('ended', function(){
        setPosterVisible(true,true);
      });

      poster.on('click',function(){
        player.play();
      });

      setPosterVisible(true,true);

    });
  });
})(jQuery);
