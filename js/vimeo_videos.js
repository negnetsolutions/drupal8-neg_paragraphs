(function() {

  var video_paragraphs = document.querySelectorAll(".paragraph-video.vimeo");
  Array.prototype.forEach.call(video_paragraphs, function(el) {
    var iframe = el.querySelector('iframe');
    var player = new Vimeo.Player(iframe);
    var poster = el.querySelector('.poster');
    var playbtn = el.querySelector('.poster button');

    var setPosterVisible = function(visible,showPoster){
      if(visible == true){
        poster.classList.add('visible');
      }
      else {
        poster.classList.remove('visible');
      }

      if(showPoster == false){
        poster.classList.add('transparent');
      }
      else {
        poster.classList.remove('transparent');
      }
    }

    player.on('play', function () {
      setPosterVisible(false);
    });

    player.on('pause', function () {
      setPosterVisible(true,false);
    });

    player.on('ended', function () {
      setPosterVisible(true,true);
    });

    poster.addEventListener('click', function () {
      player.play();
    });

    setPosterVisible(true,true);
  });
})();
