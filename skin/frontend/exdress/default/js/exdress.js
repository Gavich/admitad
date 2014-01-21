/**
 * Medusa for Magento 1.7.0.0
 * Design and Development by creative-d2 design&development (http://www.creative-d2.de)
 * Distributed by ThemeForest (http://themeforest.net)
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @author     �mer Bildirici jun.
 * @package    medusa_default
 * @copyright  Copyright 2012 �mer Bildirici jun. (http://www.creative-d2.de)
 * @license    All rights reserved.
 * @version    1.1
 */

(function() {

    // CDD.tools {{{
    if (typeof BLANK_IMG == 'undefined')
        var BLANK_IMG = '';

    // declare namespace() method
    String.prototype.namespace = function(separator) {
        this.split(separator || '.').inject(window, function(parent, child) {
            var o = parent[child] = {};
            return o;
        });
    };

    'CDD.tools'.namespace();

    // EXDRESS {{{
    function loadmenu() {
        var count = 0;
        // count ul li
        $$(".menu").each(function(elem) {
            $count = (elem.childElements()).length;
        });
        if ($count != 0) {
            $$(".menu").each(function(elem) {
                $i = 0;
                elem.childElements().each(function(li) {
                    li.getElementsBySelector('[class="column"]').each(function(e) {
                        if (e.childNodes.length != 0) {
                            check_column = e.hasClassName('column');
                            $set = $count - $i - 1;
                            if (check_column == true) {
                                if ($i == 0) {
                                    e.addClassName("dropdown_3columns");
                                } else {
                                    if ($i == $count - 1) {
                                        e.addClassName("dropdown_3columns align_right last");
                                        li.addClassName(" right");
                                    } else
                                        e.addClassName("dropdown_" + $set + "columns");
                                    if ($set == 1) {
                                        e.childElements().each(function(f) {
                                            if (typeof f.down('ul') != 'undefined') {
                                                f.down('ul').addClassName('levels');
                                            }
                                        });
                                    }
                                }
                            }
                        }
                    });

                    li.getElementsBySelector('[class="full"]').each(function(e) {
                        if (e.childNodes.length != 0) {
                            check_full = e.hasClassName('full');
                            if (check_full == true && $i == 0 && $i < $count - 1)
                                e.addClassName(" dropdown_fullwidth_first");
                            if (check_full == true && $i == 1 && $i < $count - 1)
                                e.addClassName(" dropdown_fullwidth_item2");
                            if (check_full == true && $i == 2 && $i < $count - 1)
                                e.addClassName(" dropdown_fullwidth_item3");
                            if (check_full == true && $i == 3 && $i < $count - 1)
                                e.addClassName(" dropdown_fullwidth_item4");
                            if (check_full == true && $i == 4 && $i < $count - 1)
                                e.addClassName(" dropdown_fullwidth_item5");
                            if (check_full == true && $i == 5 && $i < $count - 1)
                                e.addClassName(" dropdown_fullwidth_item6");
                            if (check_full == true && $i == $count - 1 && $i != 0)
                                e.addClassName(" dropdown_fullwidth_item_right");
                        }
                    });
                    $i++;
                });

            });
            /*
		if($$(".menu .column")!=""){
			$$(".menu").each(function(elem) {
				alert(elem.down('li'));	
			});
		}
	*/
            }
    }
    
    function decorateBrandsslider() {
        var $$li = $$('#brandsslider ul li');
        if ($$li.length > 0) {

            // reset UL's width
            var ul = $$('#brandsslider ul')[0];
            var w = 0;
            $$li.each(function(li) {
                w += li.getWidth();
            });
            ul.setStyle({
                'width': w + 'px'
            });

            // private variables
            var previous = $$('#brandsslider a.previous')[0];
            var next = $$('#brandsslider a.next')[0];
            var num = 1;
            var width = ul.down().getWidth() * num;
            var slidePeriod = 15;
            // seconds
            var manualSliding = false;

            // next slide
            function nextSlide() {
                new Effect.Move(ul, {
                    x: -width,
                    mode: 'relative',
                    queue: 'end',
                    duration: 1.0,
                    //transition: Effect.Transitions.sinoidal,
                    afterFinish: function() {
                        for (var i = 0; i < num; i++)
                            ul.insert({
                            bottom: ul.down()
                            });
                        ul.setStyle('left:0');
                    }
                });
            }

            // previous slide
            function previousSlide() {
                new Effect.Move(ul, {
                    x: width,
                    mode: 'relative',
                    queue: 'end',
                    duration: 1.0,
                    //transition: Effect.Transitions.sinoidal,
                    beforeSetup: function() {
                        for (var i = 0; i < num; i++)
                            ul.insert({
                            top: ul.down('li:last-child')
                            });
                        ul.setStyle({
                            'position': 'relative',
                            'left': -width + 'px'
                        });
                    }
                });
            }

            function startSliding() {
                sliding = true;
            }

            function stopSliding() {
                sliding = false;
            }

            // bind next button's onlick event
            next.observe('click', function(event) {
                Event.stop(event);
                manualSliding = true;
                nextSlide();
            });

            // bind previous button's onclick event
            previous.observe('click', function(event) {
                Event.stop(event);
                manualSliding = true;
                previousSlide();
            });

            // auto run brandsslider
            new PeriodicalExecuter(function() {
                if (!manualSliding)
                    previousSlide();
                manualSliding = false;
            }, slidePeriod);

        }
    }

    showshopping = function() {
        $$("a.top-link-cart").invoke("observe", "mouseover", function() {
            $('block-cart').addClassName(' display-cart');
            $('topcartlink').addClassName(' shop');
        });
    }

    function productdetail() {

        if ($$('.box-reviews .customer-reviews') == "") {
            if ($('customer-reviews') != null) {
                $('customer-reviews').addClassName('full-reviews');
            }
        } else {
            if ($('customer-reviews') != null) {
                $('customer-reviews').removeClassName('full-reviews');
            }
        }

        if ($$('.product-view-group .block-related') == "" && $$('.product-view-group .box-tags') == "" || $$('.product-view-group .box-tags') == "" && $$('.product-view-group .box-up-sell') == "" || $$('.product-view-group .block-related') == "" && $$('.product-view-group .box-up-sell') == "") {
            $$('.product-view-group').each(function(e) {
                e.addClassName('full-box-tags');
                });
        } else {
            $$('.product-view-group').each(function(e) {
                e.removeClassName('full-box-tags');
                });
        }

    }
    display = function() {
        $$('.block-cart').invoke('observe', 'mouseover', function() {
            $('block-cart').addClassName('display-cart');
            $('topcartlink').addClassName(' shop');
        });
        $$('.block-cart').invoke('observe', 'mouseout', function() {
            $('block-cart').removeClassName('display-cart');
            $('topcartlink').removeClassName(' shop');
        });

        }

    window.menuBuild = function() {
        var Width_ul = 0;
        var Width_li = 0;
        var Width_before = 0;
        var Width_div = 0;
        var Width = 0;
        $$(".menu").each(function(elem) {
            Width_ul = elem.getWidth();
            elem.childElements().each(function(li) {
                Width_li = li.getWidth();
                Width = Width_ul - Width_before;
                Width_before += Width_li;
                $div = li.select('div')[0];
                if (typeof $div != 'undefined') {
                    Width_div = $div.getWidth();
                    sub = Width_div - Width;
                    if (sub > 0) {
                        $div.addClassName(' position-right');
                            li.addClassName('position-right-li');
                        }
                }
            });

        });
    }
    
    // e.g. slideshow products slider homepage
    function decorateProductslider() {
        var slideshow = $('productslider');
        if (slideshow) {

            // private variables
            var ul = slideshow.select('.slideshow-box ul')[0];
            var $$li = ul.select('li');
            var width = ul.down('li').getWidth();
            var slidePeriod = 6;
            // seconds
            var manualSliding = false;
            var currentIdx = 0;

            // reset slideshow UL's width
            ul.setStyle({
                width: width * $$li.length + 10 + 'px'
            });

            // store slideshow image index into LI
            for (var i = 0; i < $$li.length; i++) {
                $$li[i].slideshowIdx = i;
                //$$li[i].setAttribute('id', 'productslider_'+i);
                }

            // generate Navigation
            var nav = slideshow.select('.navigation')[0];
            nav.insert('<ul></ul>');
            var nav_ul = nav.down('ul');
            for (var i = 0; i < $$li.length; i++) {
                var attr = '';
                if (i == 0)
                    var attr = 'class="active"';
                nav_ul.insert('<li><a href="#' + i + ' " ' + attr + '>' + (i + 1) + '</a></li>');
            }

            // bind onClick event on navigation A element
            var $$nav_li = nav_ul.childElements();
            nav_ul.select('a').each(function(a) {
                a.observe('click', function(event) {
                    Event.stop(event);
                    if (a.hasClassName('active'))
                        return;

                    manualSliding = true;

                    var current = a.up('li');
                    var active = nav_ul.select('a.active')[0].up('li');
                    var idx_current = $$nav_li.indexOf(current);
                    var idx_active = $$nav_li.indexOf(active);

                    if (idx_current > idx_active)
                        nextSlide(idx_current - idx_active);
                    else
                        previousSlide(idx_active - idx_current);
                }.bind(a));
            }.bind(this));

            // next slide
            function nextSlide(n) {
                if (typeof n == 'undefined')
                    n = 1;

                new Effect.Move(ul, {
                    x: -width * n,
                    mode: 'relative',
                    //queue: 'end',
                    duration: 1.0,
                    transition: Effect.Transitions.sinoidal,
                    beforeSetup: function() {
                        // set current slide indicator
                        nav_ul.select('a.active')[0].removeClassName('active');
                        nav_ul.down('li', ul.down().next(n - 1).slideshowIdx).down('a').addClassName('active');
                    },
                    afterFinish: function() {
                        for (var i = 0; i < n; i++)
                            ul.insert({
                            bottom: ul.down()
                            });
                        ul.setStyle('left:0');
                    }
                });
            }

            // previous slide
            function previousSlide(n) {
                if (typeof n == 'undefined')
                    n = 1;
                new Effect.Move(ul, {
                    x: width * n,
                    mode: 'relative',
                    //queue: 'end',
                    duration: 1.0,
                    transition: Effect.Transitions.sinoidal,
                    beforeSetup: function() {
                        // set current slide indicator
                        nav_ul.select('a.active')[0].removeClassName('active');
                        var li = ul.down('li:last-child');
                        if (n > 1)
                            li = li.previous(n - 2);
                        nav_ul.down('li', li.slideshowIdx).down('a').addClassName('active');

                        for (var i = 0; i < n; i++)
                            ul.insert({
                            top: ul.down('li:last-child')
                            });
                        ul.setStyle({
                            'position': 'relative',
                            'left': -width * n + 'px'
                        });
                    }
                });
            }

            // auto run slideshow
            /*new PeriodicalExecuter(function() {
				if (!manualSliding) nextSlide();
				manualSliding = false;
			}, slidePeriod);*/

            }
    }
    
    // e.g. slideshow product-view related products
    function decorateRelatedslider() {
        var $$li = $$('#relatedslider ul li');
        if ($$li.length > 0) {

            // reset UL's width
            var ul = $$('#relatedslider ul')[0];
            var w = 0;
            $$li.each(function(li) {
                w += li.getWidth();
            });
            ul.setStyle({
                'width': w + 'px'
            });

            // private variables
            var previous = $$('#relatedslider a.previous')[0];
            var next = $$('#relatedslider a.next')[0];
            var num = 1;
            var width = ul.down().getWidth() * num;
            var slidePeriod = 10;
            // seconds
            var manualSliding = false;

            // next slide
            function nextSlide() {
                new Effect.Move(ul, {
                    x: -width,
                    mode: 'relative',
                    queue: 'end',
                    duration: 1.0,
                    //transition: Effect.Transitions.sinoidal,
                    afterFinish: function() {
                        for (var i = 0; i < num; i++)
                            ul.insert({
                            bottom: ul.down()
                            });
                        ul.setStyle('left:0');
                    }
                });
            }

            // previous slide
            function previousSlide() {
                new Effect.Move(ul, {
                    x: width,
                    mode: 'relative',
                    queue: 'end',
                    duration: 1.0,
                    //transition: Effect.Transitions.sinoidal,
                    beforeSetup: function() {
                        for (var i = 0; i < num; i++)
                            ul.insert({
                            top: ul.down('li:last-child')
                            });
                        ul.setStyle({
                            'position': 'relative',
                            'left': -width + 'px'
                        });
                    }
                });
            }

            function startSliding() {
                sliding = true;
            }

            function stopSliding() {
                sliding = false;
            }

            // bind next button's onlick event
            next.observe('click', function(event) {
                Event.stop(event);
                manualSliding = true;
                nextSlide();
            });

            // bind previous button's onclick event
            previous.observe('click', function(event) {
                Event.stop(event);
                manualSliding = true;
                previousSlide();
            });

            // auto run slideshow
            new PeriodicalExecuter(function() {
			if (!manualSliding) previousSlide();
			manualSliding = false;
		}, slidePeriod);

            }
    }
    
    function decorateUpsellslider() {
        var $$li = $$('#slideshow ul li');
        if ($$li.length > 0) {

            // reset UL's width
            var ul = $$('#slideshow ul')[0];
            var w = 0;
            $$li.each(function(li) {
                w += li.getWidth();
            });
            ul.setStyle({
                'width': w + 'px'
            });

            // private variables
            var previous = $$('#slideshow a.previous')[0];
            var next = $$('#slideshow a.next')[0];
            var num = 1;
            var width = ul.down().getWidth() * num;
            var slidePeriod = 10;
            // seconds
            var manualSliding = false;

            // next slide
            function nextSlide() {
                new Effect.Move(ul, {
                    x: -width,
                    mode: 'relative',
                    queue: 'end',
                    duration: 1.0,
                    //transition: Effect.Transitions.sinoidal,
                    afterFinish: function() {
                        for (var i = 0; i < num; i++)
                            ul.insert({
                            bottom: ul.down()
                            });
                        ul.setStyle('left:0');
                    }
                });
            }

            // previous slide
            function previousSlide() {
                new Effect.Move(ul, {
                    x: width,
                    mode: 'relative',
                    queue: 'end',
                    duration: 1.0,
                    //transition: Effect.Transitions.sinoidal,
                    beforeSetup: function() {
                        for (var i = 0; i < num; i++)
                            ul.insert({
                            top: ul.down('li:last-child')
                            });
                        ul.setStyle({
                            'position': 'relative',
                            'left': -width + 'px'
                        });
                    }
                });
            }

            function startSliding() {
                sliding = true;
            }

            function stopSliding() {
                sliding = false;
            }

            // bind next button's onlick event
            next.observe('click', function(event) {
                Event.stop(event);
                manualSliding = true;
                nextSlide();
            });

            // bind previous button's onclick event
            previous.observe('click', function(event) {
                Event.stop(event);
                manualSliding = true;
                previousSlide();
            });

            // auto run slideshow
            new PeriodicalExecuter(function() {
			if (!manualSliding) previousSlide();
			manualSliding = false;
		}, slidePeriod);

            }
    }

    function decorateCrosssellslider() {
        var $$li = $$('#crosssellslider ul li');
        if ($$li.length > 0) {

            // reset UL's width
            var ul = $$('#crosssellslider ul')[0];
            var w = 0;
            $$li.each(function(li) {
                w += li.getWidth();
            });
            ul.setStyle({
                'width': w + 'px'
            });

            // private variables
            var previous = $$('#crosssellslider a.previous')[0];
            var next = $$('#crosssellslider a.next')[0];
            var num = 1;
            var width = ul.down().getWidth() * num;
            var slidePeriod = 3;
            // seconds
            var manualSliding = false;

            // next slide
            function nextSlide() {
                new Effect.Move(ul, {
                    x: -width,
                    mode: 'relative',
                    queue: 'end',
                    duration: 1.0,
                    //transition: Effect.Transitions.sinoidal,
                    afterFinish: function() {
                        for (var i = 0; i < num; i++)
                            ul.insert({
                            bottom: ul.down()
                            });
                        ul.setStyle('left:0');
                    }
                });
            }

            // previous slide
            function previousSlide() {
                new Effect.Move(ul, {
                    x: width,
                    mode: 'relative',
                    queue: 'end',
                    duration: 1.0,
                    //transition: Effect.Transitions.sinoidal,
                    beforeSetup: function() {
                        for (var i = 0; i < num; i++)
                            ul.insert({
                            top: ul.down('li:last-child')
                            });
                        ul.setStyle({
                            'position': 'relative',
                            'left': -width + 'px'
                        });
                    }
                });
            }

            function startSliding() {
                sliding = true;
            }

            function stopSliding() {
                sliding = false;
            }

            // bind next button's onlick event
            next.observe('click', function(event) {
                Event.stop(event);
                manualSliding = true;
                nextSlide();
            });

            // bind previous button's onclick event
            previous.observe('click', function(event) {
                Event.stop(event);
                manualSliding = true;
                previousSlide();
            });

            // auto run slideshow
            /*new PeriodicalExecuter(function() {
			if (!manualSliding) previousSlide();
			manualSliding = false;
		}, slidePeriod);*/

            }
    }

    document.observe("dom:loaded", function() {
        //menu();
        //loadmenu();
        decorateBrandsslider();
        showshopping();
        display();
        productdetail();
        decorateProductslider();
        decorateRelatedslider();
        decorateUpsellslider();
        decorateCrosssellslider();
        });

    // }}}
    })();
