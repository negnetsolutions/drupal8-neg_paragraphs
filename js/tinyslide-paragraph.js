(function ($) {
  window.initTinySlide = function(){
    initTiny();
  }
  function initTiny(){
    $(".paragraph-row.layout_slider").each(function(){

      // Set the aspect ratio of the slider automatically based on first image.
      var first_image = $(".grid > figure img", this).eq(0);
      if (first_image.length > 0) {
        var width = first_image.data('width');
        var height = first_image.data('height');
        if (width > 0 && height > 0) {
          //calculate padding-top;
          var ptop = ( height*100/width ) + "%";
          $('>.grid', this).css('padding-top', ptop);
        }
      }

      var tiny = $(this).tiny({
        slides: ">.grid",
        slide: ">figure",
        animationDuration: 0,
        autoplay: 3500,
        slider: false,
        hoverPause: false,
        showNavigator: false,
      }).data('api_tiny');

      $(".change",$(this)).click(function(){
        tiny[$(this).data('direction')]();
      });

    });
  }
  $(document).ready(function(){
    initTiny();
  });
}(jQuery));
