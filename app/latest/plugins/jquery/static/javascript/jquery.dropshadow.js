/**
 * Drop Shadow Plugin jQuery
 * http://sarfraznawaz.wordpress.com/
 * Author: Sarfraz Ahmed (sarfraznawaz2005@gmail.com)
 */

(function($){

    $.fn.dropshadow = function(settings){
        // Extend default settings
        var opts = $.extend({}, $.fn.dropshadow.defaults, settings);

        // Check if CSS3 is supported
        var style = $('div')[0].style;
        var isCSS3 = style.MozBoxShadow !== undefined || style.WebkitBoxShadow !== undefined || style.BoxShadow !== undefined;

        return this.each(function(settings){
           var options = $.extend({}, opts, $(this).data());
           var $this = $(this);

            if (!isCSS3){
                var styles = {
                    position: 'absolute',
                    width: $this.width() + 'px',
                    height: $this.height() + 'px',
                    backgroundColor: options.shadowColor,
                    zIndex: options.shadowLayer,
                    top: ($this.offset().top + parseInt(options.distance, 10)) + 'px',
                    left: ($this.offset().left + parseInt(options.distance, 10)) + 'px'
                };
            }
            else{

                var boxshadow = options.distance + ' ' + options.distance + ' ' + options.blur + ' ' + options.shadowColor;
                var styles = {
                    position: 'absolute',
                    width: $this.width() + 'px',
                    height: $this.height() + 'px',
                    backgroundColor: options.shadowColor,
                    zIndex: options.shadowLayer,
                    top: $this.offset().top + 'px',
                    left: $this.offset().left + 'px',
                    MozBoxShadow:boxshadow,
                    WebkitBoxShadow:boxshadow,
                    BoxShadow:boxshadow
                };
            }

            $('<div class="drop_shadow_layer">').appendTo($('body')).css(styles);

        });
    }

   // set default option values
  $.fn.dropshadow.defaults = {
    shadowColor: '#DFDFDF',
    shadowLayer: -1,
    distance:'5px',
    blur:'3px'
  }


})(jQuery);
