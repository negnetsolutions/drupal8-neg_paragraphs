(function ($, window, document, undefined) {

  var defaults = {
    autoplay: 4000,
    animationDuration: 800,
    hoverPause: true,
    slide: ">figure",
    slides: ">aside",
    thumbs: false,
    thumb: ">figure",
    debug: false,
    showNavigator: true,
    infiniteCarousel: true,
    slider: true,
    beforeTransition: function(){},
    afterTransition: function(){}
  };


  var Tiny = function(container, options){
      var _ = this;

      this.options = $.extend({}, defaults, options),
      this.container = container,
      this.slideContainer = $(this.options.slides,this.container),
      this.slides = $(this.options.slide, this.slideContainer),
      this.thumbContainer = (this.options.thumbs == false) ? false : $(this.options.thumbs, this.container);
      this.thumbs = (this.thumbContainer == false || this.thumbContainer.size() == 0) ? false : $(this.options.thumb, this.thumbContainer);
      this.numSlides = this.slides.length,
      this.currentSlideIndex = 0,
      this.autoplayTimer,
      this.debounce,
      this.afterAnimationTimer,
      this.slideWidth,
      this.slideNavigator,
      this.slideNavigatorItems,
      this.$w = $(window),
      this.animating = false;

      //mobile
      this.dragThreshold = .10,
      this.dragStart = null,
      this.percentage = 0,
      this.dragStartSlidePosition = 0,
      this.dragTarget,
      this.previousDragTarget,
      this.delta = 0
    ;

    this.api = {
      getSlide: function(index){
        if(_.animating == false){
          _.currentSlideIndex = index;
          _.showSlide();
        }
      },

      nextSlide: function(){
        var index = _.currentSlideIndex;
        index++;
        index = (index >= _.numSlides) ? 0 : index;
        _.api.getSlide(index);
      },

      prevSlide: function(){
        var index = _.currentSlideIndex;
        index--;

        if(_.options.infiniteCarousel == true){
          index = (index < -1) ? _.numSlides - 1 : index;
        }
        else {
          index = (index < 0) ? _.numSlides - 1 : index;
        }

        _.api.getSlide(index);
      },

      play: function(){
        //disable autoplay if set to 0
        if( _.options.autoplay == 0 ){
          return;
        }

        _.autoplayTimer = setTimeout(function(){
          _.api.nextSlide();
        }, _.options.autoplay);
      },

      pause: function(){
        if(typeof _.autoplayTimer !== "undefined"){
          clearTimeout(_.autoplayTimer);
        }
      },

      unload: function(){
        _.api.pause();
        _.options.autoplay = 0;
        clearTimeout(_.autoplayTimer);
        clearTimeout(_.debounce);

        if(_.slideNavigator){
          _.slideNavigator.remove();
          _.container.removeClass('has-navigator');
        }

        _.slideContainer.css("width","");
        _.slides.css("width","");
        _.slideContainer.css(_.getPrefix()+'transition','');
        _.slideContainer.css(_.getPrefix()+'transform','');

        _.$w.off('resize',_.resize);

        _.slides.off({
          'touchstart': _.touchStart,
          'touchmove': _.touchMove,
          'touchend': _.touchEnd,
        });

        _.container.off({
          'mouseover': _.api.pause,
          'mouseout': _.api.play,
        });

        $.removeData(_.container.get(0),'api_tiny');

        delete _;
      }

    }

    this.touchStart = function(event){

      if (_.dragStart !== null) { return; }
      if (event.originalEvent.touches) {
        event = event.originalEvent.touches[0];
      }

      // where in the viewport was touched
      _.dragStart = {x: event.clientX, y: event.clientY};

      //get slide position at start
      _.dragStartSlidePosition = _.getCurrentSlidePosition();

      // make sure we're dealing with a slide
      _.dragTarget = _.slides.eq(_.currentSlideIndex)[0];

      _.previousDragTarget = _.slides.eq(_.currentSlideIndex-1)[0];

      _.api.pause();
      _.animating = false;
      _.pauseTransition();

    }

    this.touchMove = function(event){

      if (_.dragStart.x === null) { return; }

      if (event.originalEvent.touches) {
        event = event.originalEvent.touches[0];
      }

      _.delta = {x: _.dragStart.x - event.clientX, y: _.dragStart.y - event.clientY};
      _.percentage = _.delta.x / _.$w.width();

      //if we are mostly scrolling up or down, let browser do the work
      if( _.dragStartSlidePosition == _.getCurrentSlidePosition() && Math.abs(_.delta.y) > 2 ){
        return true;
      }

      //otherwise, let's scroll the slider
      if(_.numSlides > 1 && Math.abs(_.percentage) < 1){
        _.translate( _.dragStartSlidePosition - _.delta.x );
      }


      return false;
    }
    this.touchEnd = function(){

      _.dragStart = null;

      if (_.percentage >= _.dragThreshold) {
        _.api.nextSlide();
      }
      else if ( Math.abs(_.percentage) >= _.dragThreshold ) {
        _.api.prevSlide();
      }

      percentage = 0;
    }

    this.debugMsg = function(message){
      if(_.options.debug == true){
        console.log(message);
      }
    }

    this.setupInfinite = function(){

      if(_.options.slider != true || _.options.infiniteCarousel != true){
        _.options.infiniteCarousel = false;
        return false;
      }

      var first = _.slides.eq(0).clone(true).attr('data-index',_.numSlides).appendTo(_.slideContainer).addClass('cloned');
      var last = _.slides.eq(_.numSlides-1).clone(true).attr('data-index',-1).prependTo(_.slideContainer).addClass('cloned');
      _.slides = $(_.options.slide, _.slideContainer);
      _.numSlides = _.slides.length;
    }

    this.getSlideAtIndex = function(index){
      var slide = _.slideContainer.find("[data-index='"+index+"']");
      return slide;
    }

    this.init = function(){
      _.setupInfinite();
      _.dimensions();
      _.drawNavigator();
      _.setupThumbnailNavigator();

      if(_.thumbs !== false){
        _.thumbs.eq(0).addClass('active');
      }

      _.currentSlideIndex = 0;
      _.transitionNoAnimation();

      if(_.options.slider == true){
        _.slides.on({
          'touchstart': _.touchStart,
          'touchmove': _.touchMove,
          'touchend': _.touchEnd,
        });
      }

      if(document.addEventListener){
        document.addEventListener("visibilitychange", function(e) {
          if(document.visibilityState == 'hidden') {
            // page is hidden
            _.api.pause();
            _.pauseTransition();

          } else {
            // page is visible
            _.setTransition();
            _.api.play();
          }
        });
      }

      if(_.options.hoverPause == true){
        _.container.on('mouseover',_.api.pause);
        _.container.on('mouseout',_.api.play);
      }

      _.$w.resize(_.resize);

      _.api.play();

    }

    this.resize = function(){
      _.debounce && clearTimeout(_.debounce);
      _.debounce = setTimeout(function(){
        _.dimensions();
        _.showSlide();
        _.updateThumbnails();
      }
      , 20);
    }

    this.setupThumbnailNavigator = function(){
      if( this.thumbContainer == false){
        return;
      }

      _.thumbContainer.on("click",_.options.thumb,function(){
        _.api.getSlide( $(this).data('index') );
      });
    }

    this.drawNavigator = function() {

      if( _.options.showNavigator == false || _.numSlides < 2 ){
        return;
      }

      var output = "<div class='navigator'><ul>\n";
      for(var i=0; i < _.numSlides; i++){
        output += "<li data-index='"+i+"'><span>"+i+"</span></li>\n";
      }
      output += "</ul>";

      _.slideNavigator = $(output);
      _.container.append(_.slideNavigator);
      _.container.addClass('has-navigator');

      _.slideNavigatorItems = $("li",_.slideNavigator);
      $(_.slideNavigatorItems.get(0)).addClass("active");

      _.slideNavigator.on("click","li",function(){
        _.api.getSlide( $(this).data('index') );
      });
    }

    this.dimensions = function(){
      _.slideWidth = _.container.width();
      if(_.options.slider == true){
        _.slides.width(_.slideWidth);
        _.slideContainer.width( ( _.slideWidth * (_.numSlides)) );
      }
      else {
        _.slideContainer.width( _.slideWidth );
        _.slideContainer.height = _.container.height();
      }
    }

    this.pauseTransition = function(){
      if(_.options.slider == false){
        return false;
      }
      _.slideContainer.css(_.getPrefix()+'transition','none');
    }

    this.setTransition = function(){
      if(_.options.slider == false){
        return false;
      }
      var transition_style = _.getPrefix()+"transform";
      _.slideContainer.css(_.getPrefix()+'transition',transition_style+' '+_.options.animationDuration+'ms cubic-bezier(0.365, 0.84, 0.44, 1)');
    }

    this.getCurrentSlidePosition = function(){
      var matrix = _.slideContainer.css('transform').replace(/[^0-9\-.,]/g, '').split(',');
      return parseInt(matrix[12] || matrix[4]);
    }

    this.translate = function(x){
      _.slideContainer.css(_.getPrefix()+'transform','translate3d('+x+'px,0px,0px)');
    }
    this.getPrefix = function () {

			if (!window.getComputedStyle) return '';

			var styles = window.getComputedStyle(document.documentElement, '');
			return '-' + (Array.prototype.slice
				.call(styles)
				.join('')
				.match(/-(moz|webkit|ms)-/) || (styles.OLink === '' && ['', 'o'])
			)[1] + '-';

		}

    this.updateThumbnails = function(){

      if(_.thumbs === false){
        return;
      }

      var currentThumb = _.thumbs.eq(_.currentSlideIndex);

      var scrollLeft = _.thumbContainer.scrollLeft();
      var scrollWidth = _.thumbContainer.width();

      var left = currentThumb.position().left;
      var width = currentThumb.outerWidth(true);

      currentThumb.addClass('active')
        .siblings().removeClass('active');

      if((scrollLeft+scrollWidth) < (left+width)){
        _.animateThumbnailScroll(left+width);
      }

      if( (left) < (0) ){
        _.animateThumbnailScroll( scrollLeft - scrollWidth );
      }

    }

    this.animateThumbnailScroll = function(left){
       _.thumbContainer.animate({scrollLeft: left}, 300);
      return true;
    }

    this.transitionNoAnimation = function(){
      _.pauseTransition();

      var index = _.getCurrentSlideIndex();

      _.translate( -1 * ( index ) * _.slideWidth );
      _.slides.eq( index ).addClass('active')
        .siblings().removeClass('active');
    }

    this.showSlide = function(){

      _.animating = true;
      _.api.pause();
      _.setTransition();

      _.options.beforeTransition.call(this);

      var index = _.getCurrentSlideIndex();

      if(_.options.slider == true){
        _.translate( -1 * ( index ) * _.slideWidth );
      }
      else {
        _.crossFade(index);
      }

      if( _.options.showNavigator == true && _.numSlides > 1 ){
        _.slideNavigatorItems.eq(_.currentSlideIndex).addClass('active')
          .siblings().removeClass('active');
      }

      _.afterAnimation(function(){
        _.finishAnimation();
      });

      _.api.play();
    }

    this.crossFade = function(index){

      var previous = $('.active',_.slideContainer);
      previous.addClass('previous').siblings().removeClass('previous');
      _.slides.eq(index).addClass('active').removeClass('previous')
        .siblings().removeClass('active')
      ;
    }

    this.getCurrentSlideIndex = function(){
      var index = _.currentSlideIndex;

      if(_.options.infiniteCarousel == true){
        index++;
      }

      return index;
    }

    this.settleAnimation = function(){

      _.animating = false;
      var index = _.getCurrentSlideIndex();
      _.slides.eq(index).addClass('active')
        .siblings().removeClass('active');

    }

    this.finishAnimation = function(){

      //handle Infinite
      if(_.options.infiniteCarousel == true){
        if(_.currentSlideIndex == -1){
          _.currentSlideIndex = _.numSlides - 3;
          _.transitionNoAnimation();
        } else if(( _.currentSlideIndex+2 ) == _.numSlides){
          _.currentSlideIndex = 0;
          _.transitionNoAnimation();
        }
      }

      _.settleAnimation();

      _.options.afterTransition.call(this);

      _.updateThumbnails();

      _.afterAnimationTimer = false;
    }

    this.afterAnimation = function(callback){
      _.afterAnimationTimer = setTimeout(function(){
        callback();
      }, _.options.animationDuration);
    }

    this.init();

    return this.api;
  }

  $.fn["tiny"] = function(options) {
    return this.each(function () {
      if ( !$.data(this, 'api_tiny') ) {
        $.data(this, 'api_tiny',
         new Tiny($(this), options)
        );
      }
    });
  };

})(jQuery, window, document);
