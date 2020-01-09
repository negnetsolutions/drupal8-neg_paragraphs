(function() {
  const video_paragraphs = document.querySelectorAll(
    ".paragraph-video.vimeo.embedded"
  );
  for (let i = 0; i < video_paragraphs.length; i++) {
    const el = video_paragraphs[i];
    const iframe = el.querySelector("iframe");
    const player = new Vimeo.Player(iframe);
    const poster = el.querySelector(".poster");
    const playbtn = el.querySelector(".poster button");

    const setPosterVisible = function(visible, showPoster) {
      if (poster === null) {
        return;
      }

      if (visible == true) {
        poster.classList.add("visible");
      } else {
        poster.classList.remove("visible");
      }

      if (showPoster == false) {
        poster.classList.add("transparent");
      } else {
        poster.classList.remove("transparent");
      }
    };

    player.on("play", function() {
      setPosterVisible(false);
      iframe.classList.add('playing');
    });

    player.on("pause", function() {
      setPosterVisible(true, false);
    });

    player.on("ended", function() {
      setPosterVisible(true, true);
      iframe.classList.remove('playing');
    });

    if (poster !== null) {
      poster.addEventListener("click", function() {
        player.play();
      });
    }

    setPosterVisible(true, true);

  }
})();
