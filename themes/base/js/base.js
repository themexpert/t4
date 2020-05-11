jQuery(document).ready(function($) {

    /**
     *monitor the element scroll to add class into body for style effect later
     */
    $(window).ready(function() {
        if (
            "IntersectionObserver" in window &&
            "IntersectionObserverEntry" in window &&
            "intersectionRatio" in window.IntersectionObserverEntry.prototype
        ) {
            var options = {
                root: null,
                rootMargin: '0px',
                threshold: 0
            };
            var sections = document.querySelectorAll(".t4-section");
            var maxIdx = 0;
            var sticky = document.querySelectorAll(".t4-sticky");

            function isValid(el) {
                return el.offsetTop < window.innerHeight && el.offsetTop + el.offsetHeight < window.innerHeight + 200;
            }

            function doChange(changes, observer) {
                changes.forEach( function(change){
                    var clientRect = change.boundingClientRect,
                        target = change.target;
                    if (clientRect.top <= -clientRect.height) {

                        document.body.setAttribute('data-top-' + target.id, 'over');
                        if (maxIdx == target.idx) document.body.classList.add ('top-away');
                    } else {
                        document.body.setAttribute('data-top-' + target.id, 'under');
                        if (maxIdx == target.idx) document.body.classList.remove ('top-away');
                    }

                })
            }

            var observer = new IntersectionObserver(doChange, options);

            for(var i=0; i<sections.length; i++) {
                var el = sections[i];
                if (isValid(el)) {
                    el.idx = i;
                    observer.observe(el);
                } else {
                    maxIdx = i - 1;
                    break;
                }
            }
            var top = 0;
            var zindex = 300;
            for(var i=0; i<sticky.length; i++) {
                var elSk = sticky[i];
                top += elSk.offsetHeight;
                if(typeof sticky[i+1] != 'undefined'){
                    $(elSk).css({'z-index':zindex});
                    zindex -= 1;
                    $(sticky[i+1]).css({top:top,'z-index':zindex});
                }
                
            }
            // monitor not at top
            var options2 = {
                root: null,
                rootMargin: '0px',
                threshold: 0
            };
            function doChange2(changes) {
                var clientRect = changes[0].boundingClientRect;
                if (clientRect.top < -100) {
                    document.body.classList.add ('not-at-top');
                } else {
                    document.body.classList.remove ('not-at-top');
                }
            }
            var observer2 = new IntersectionObserver(doChange2, options2);
            var anchorEl = $('<a name="top-anchor">').prependTo('.t4-content-inner');
            if(anchorEl.get(0)) observer2.observe(anchorEl.get(0));

        }
    });


    /**
     * Back-to-top action: scroll back to top
     */
    $('body').on('click','#back-to-top',function() {
        $('body,html,.t4-content').animate({
            scrollTop : 0
        }, 500);
        return false;
    });


    // fix for multilevel dropdown
    $('.dropdown-menu a.dropdown-toggle').on('click', function(e) {
      if (!$(this).next().hasClass('show')) {
        $(this).parents('.dropdown-menu').first().find('.show').removeClass('show');
      }
      var $subMenu = $(this).next('.dropdown-menu');
      $subMenu.toggleClass('show');

      return false;
    });

    $('li.nav-item.dropdown').on('hidden.bs.dropdown', function(e) {
        $(this).find('.show').removeClass('show');
    });
    
    // //check scroll get page y 
    $(document).find('.t4-content').scroll(function (event) {
        var scroll = $('.t4-content').scrollTop();
        localStorage.setItem("page_scroll", scroll);
       
    });
});
// Add missing Mootools when Bootstrap is loaded
(function($)
{
    $(document).ready(function(){
        var bootstrapLoaded = (typeof $().carousel == 'function');
        var mootoolsLoaded = (typeof MooTools != 'undefined');
        if (bootstrapLoaded && mootoolsLoaded) {
            Element.implement({
                hide: function () {
                    return this;
                },
                show: function (v) {
                    return this;
                },
                slide: function (v) {
                    return this;
                }
            });
        }
    });
    function refreshCurrentPage () {
        var page = window.location.href;
        var cur  = localStorage.getItem('page');
        if(cur == page){
            return true;
        }
        return false;
    }
    

    window.onload = function () {
        var check  = refreshCurrentPage();
        localStorage.setItem("page",window.location.href);
        if(document.getElementsByClassName("t4-content").length){
            if (check) {
                var match = localStorage.getItem("page_scroll");
                document.getElementsByClassName("t4-content")[0].scrollTop = match;
            }else{
                document.getElementsByClassName("t4-content")[0].scrollTop = 0;
            }
        }
    }
})(jQuery);