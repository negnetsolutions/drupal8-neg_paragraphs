(function() {
  const video_paragraphs = document.querySelectorAll(
    ".paragraph-video.cloudflare"
  );
  for (let i = 0; i < video_paragraphs.length; i++) {
    const el = video_paragraphs[i];
    const player = el.querySelector("stream");
    const playerApi = Stream(player.querySelector('iframe'));
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

    playerApi.addEventListener("play", function() {
      setPosterVisible(false);
      player.classList.add('playing');
    });

    playerApi.addEventListener("pause", function() {
      setPosterVisible(true, false);
    });

    playerApi.addEventListener("ended", function() {
      setPosterVisible(true, true);
      playerel.classList.remove('playing');
    });

    if (!player.hasAttribute("autoplay")) {
      player.addEventListener("click", function(e) {
        console.debug("there");
        console.debug(playerApi);
        playerApi.pause();
      });
    }

    if (poster !== null) {
      poster.addEventListener("click", function() {
        console.debug("here");
        playerApi.play();
      });
    }

    setPosterVisible(true, true);

  }
})();
