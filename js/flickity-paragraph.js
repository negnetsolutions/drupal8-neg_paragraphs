(function flickityParagraph() {
  const sliders = document.querySelectorAll(".paragraph-row.layout_slider");
  for (let i = 0; i < sliders.length; i++) {
    const sliderEl = sliders[i];

    (function Slider() {
      const _ = this;
      const grid = sliderEl.querySelector(".grid");
      const cols = grid.querySelectorAll(".col");

      if (cols.length === 0) {
        return;
      }

      var flkty = new Flickity( grid, {
        prevNextButtons: true,
        pageDots: false,
        wrapAround: true,
        autoPlay: false,
        arrowShape: 'M39.84 51.85h-.07l17.99 31.07 5.76-3.36-15.9-27.64 16.84-27.04-5.7-3.56-18.99 30.46.07.07z',
        pauseAutoPlayOnHover: false,
      })

    })();
  }

})();
