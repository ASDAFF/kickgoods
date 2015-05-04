
; /* Start:/bitrix/templates/s1_common/js/jquery.colorbox-min.js*/
// ColorBox v1.3.19.3 - jQuery lightbox plugin
// (c) 2011 Jack Moore - jacklmoore.com
// License: http://www.opensource.org/licenses/mit-license.php
(function(a,b,c){function Z(c,d,e){var g=b.createElement(c);return d&&(g.id=f+d),e&&(g.style.cssText=e),a(g)}function $(a){var b=y.length,c=(Q+a)%b;return c<0?b+c:c}function _(a,b){return Math.round((/%/.test(a)?(b==="x"?z.width():z.height())/100:1)*parseInt(a,10))}function ab(a){return K.photo||/\.(gif|png|jpe?g|bmp|ico)((#|\?).*)?$/i.test(a)}function bb(){var b,c=a.data(P,e);c==null?(K=a.extend({},d),console&&console.log&&console.log("Error: cboxElement missing settings object")):K=a.extend({},c);for(b in K)a.isFunction(K[b])&&b.slice(0,2)!=="on"&&(K[b]=K[b].call(P));K.rel=K.rel||P.rel||"nofollow",K.href=K.href||a(P).attr("href"),K.title=K.title||P.title,typeof K.href=="string"&&(K.href=a.trim(K.href))}function cb(b,c){a.event.trigger(b),c&&c.call(P)}function db(){var a,b=f+"Slideshow_",c="click."+f,d,e,g;K.slideshow&&y[1]?(d=function(){F.text(K.slideshowStop).unbind(c).bind(j,function(){if(K.loop||y[Q+1])a=setTimeout(W.next,K.slideshowSpeed)}).bind(i,function(){clearTimeout(a)}).one(c+" "+k,e),r.removeClass(b+"off").addClass(b+"on"),a=setTimeout(W.next,K.slideshowSpeed)},e=function(){clearTimeout(a),F.text(K.slideshowStart).unbind([j,i,k,c].join(" ")).one(c,function(){W.next(),d()}),r.removeClass(b+"on").addClass(b+"off")},K.slideshowAuto?d():e()):r.removeClass(b+"off "+b+"on")}function eb(b){U||(P=b,bb(),y=a(P),Q=0,K.rel!=="nofollow"&&(y=a("."+g).filter(function(){var b=a.data(this,e),c;return b&&(c=b.rel||this.rel),c===K.rel}),Q=y.index(P),Q===-1&&(y=y.add(P),Q=y.length-1)),S||(S=T=!0,r.show(),K.returnFocus&&a(P).blur().one(l,function(){a(this).focus()}),q.css({opacity:+K.opacity,cursor:K.overlayClose?"pointer":"auto"}).show(),K.w=_(K.initialWidth,"x"),K.h=_(K.initialHeight,"y"),W.position(),o&&z.bind("resize."+p+" scroll."+p,function(){q.css({width:z.width(),height:z.height(),top:z.scrollTop(),left:z.scrollLeft()})}).trigger("resize."+p),cb(h,K.onOpen),J.add(D).hide(),I.html(K.close).show()),W.load(!0))}function fb(){!r&&b.body&&(Y=!1,z=a(c),r=Z(X).attr({id:e,"class":n?f+(o?"IE6":"IE"):""}).hide(),q=Z(X,"Overlay",o?"position:absolute":"").hide(),s=Z(X,"Wrapper"),t=Z(X,"Content").append(A=Z(X,"LoadedContent","width:0; height:0; overflow:hidden"),C=Z(X,"LoadingOverlay").add(Z(X,"LoadingGraphic")),D=Z(X,"Title"),E=Z(X,"Current"),G=Z(X,"Next"),H=Z(X,"Previous"),F=Z(X,"Slideshow").bind(h,db),I=Z(X,"Close")),s.append(Z(X).append(Z(X,"TopLeft"),u=Z(X,"TopCenter"),Z(X,"TopRight")),Z(X,!1,"clear:left").append(v=Z(X,"MiddleLeft"),t,w=Z(X,"MiddleRight")),Z(X,!1,"clear:left").append(Z(X,"BottomLeft"),x=Z(X,"BottomCenter"),Z(X,"BottomRight"))).find("div div").css({"float":"left"}),B=Z(X,!1,"position:absolute; width:9999px; visibility:hidden; display:none"),J=G.add(H).add(E).add(F),a(b.body).append(q,r.append(s,B)))}function gb(){return r?(Y||(Y=!0,L=u.height()+x.height()+t.outerHeight(!0)-t.height(),M=v.width()+w.width()+t.outerWidth(!0)-t.width(),N=A.outerHeight(!0),O=A.outerWidth(!0),r.css({"padding-bottom":L,"padding-right":M}),G.click(function(){W.next()}),H.click(function(){W.prev()}),I.click(function(){W.close()}),q.click(function(){K.overlayClose&&W.close()}),a(b).bind("keydown."+f,function(a){var b=a.keyCode;S&&K.escKey&&b===27&&(a.preventDefault(),W.close()),S&&K.arrowKey&&y[1]&&(b===37?(a.preventDefault(),H.click()):b===39&&(a.preventDefault(),G.click()))}),a("."+g,b).live("click",function(a){a.which>1||a.shiftKey||a.altKey||a.metaKey||(a.preventDefault(),eb(this))})),!0):!1}var d={transition:"elastic",speed:300,width:!1,initialWidth:"600",innerWidth:!1,maxWidth:!1,height:!1,initialHeight:"450",innerHeight:!1,maxHeight:!1,scalePhotos:!0,scrolling:!0,inline:!1,html:!1,iframe:!1,fastIframe:!0,photo:!1,href:!1,title:!1,rel:!1,opacity:.9,preloading:!0,current:"image {current} of {total}",previous:"previous",next:"next",close:"close",xhrError:"This content failed to load.",imgError:"This image failed to load.",open:!1,returnFocus:!0,reposition:!0,loop:!0,slideshow:!1,slideshowAuto:!0,slideshowSpeed:2500,slideshowStart:"start slideshow",slideshowStop:"stop slideshow",onOpen:!1,onLoad:!1,onComplete:!1,onCleanup:!1,onClosed:!1,overlayClose:!0,escKey:!0,arrowKey:!0,top:!1,bottom:!1,left:!1,right:!1,fixed:!1,data:undefined},e="colorbox",f="cbox",g=f+"Element",h=f+"_open",i=f+"_load",j=f+"_complete",k=f+"_cleanup",l=f+"_closed",m=f+"_purge",n=!a.support.opacity&&!a.support.style,o=n&&!c.XMLHttpRequest,p=f+"_IE6",q,r,s,t,u,v,w,x,y,z,A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X="div",Y;if(a.colorbox)return;a(fb),W=a.fn[e]=a[e]=function(b,c){var f=this;b=b||{},fb();if(gb()){if(!f[0]){if(f.selector)return f;f=a("<a/>"),b.open=!0}c&&(b.onComplete=c),f.each(function(){a.data(this,e,a.extend({},a.data(this,e)||d,b))}).addClass(g),(a.isFunction(b.open)&&b.open.call(f)||b.open)&&eb(f[0])}return f},W.position=function(a,b){function i(a){u[0].style.width=x[0].style.width=t[0].style.width=a.style.width,t[0].style.height=v[0].style.height=w[0].style.height=a.style.height}var c=0,d=0,e=r.offset(),g,h;z.unbind("resize."+f),r.css({top:-9e4,left:-9e4}),g=z.scrollTop(),h=z.scrollLeft(),K.fixed&&!o?(e.top-=g,e.left-=h,r.css({position:"fixed"})):(c=g,d=h,r.css({position:"absolute"})),K.right!==!1?d+=Math.max(z.width()-K.w-O-M-_(K.right,"x"),0):K.left!==!1?d+=_(K.left,"x"):d+=Math.round(Math.max(z.width()-K.w-O-M,0)/2),K.bottom!==!1?c+=Math.max(z.height()-K.h-N-L-_(K.bottom,"y"),0):K.top!==!1?c+=_(K.top,"y"):c+=Math.round(Math.max(z.height()-K.h-N-L,0)/2),r.css({top:e.top,left:e.left}),a=r.width()===K.w+O&&r.height()===K.h+N?0:a||0,s[0].style.width=s[0].style.height="9999px",r.dequeue().animate({width:K.w+O,height:K.h+N,top:c,left:d},{duration:a,complete:function(){i(this),T=!1,s[0].style.width=K.w+O+M+"px",s[0].style.height=K.h+N+L+"px",K.reposition&&setTimeout(function(){z.bind("resize."+f,W.position)},1),b&&b()},step:function(){i(this)}})},W.resize=function(a){S&&(a=a||{},a.width&&(K.w=_(a.width,"x")-O-M),a.innerWidth&&(K.w=_(a.innerWidth,"x")),A.css({width:K.w}),a.height&&(K.h=_(a.height,"y")-N-L),a.innerHeight&&(K.h=_(a.innerHeight,"y")),!a.innerHeight&&!a.height&&(A.css({height:"auto"}),K.h=A.height()),A.css({height:K.h}),W.position(K.transition==="none"?0:K.speed))},W.prep=function(b){function g(){return K.w=K.w||A.width(),K.w=K.mw&&K.mw<K.w?K.mw:K.w,K.w}function h(){return K.h=K.h||A.height(),K.h=K.mh&&K.mh<K.h?K.mh:K.h,K.h}if(!S)return;var c,d=K.transition==="none"?0:K.speed;A.remove(),A=Z(X,"LoadedContent").append(b),A.hide().appendTo(B.show()).css({width:g(),overflow:K.scrolling?"auto":"hidden"}).css({height:h()}).prependTo(t),B.hide(),a(R).css({"float":"none"}),o&&a("select").not(r.find("select")).filter(function(){return this.style.visibility!=="hidden"}).css({visibility:"hidden"}).one(k,function(){this.style.visibility="inherit"}),c=function(){function s(){n&&r[0].style.removeAttribute("filter")}var b,c,g=y.length,h,i="frameBorder",k="allowTransparency",l,o,p,q;if(!S)return;l=function(){clearTimeout(V),C.hide(),cb(j,K.onComplete)},n&&R&&A.fadeIn(100),D.html(K.title).add(A).show();if(g>1){typeof K.current=="string"&&E.html(K.current.replace("{current}",Q+1).replace("{total}",g)).show(),G[K.loop||Q<g-1?"show":"hide"]().html(K.next),H[K.loop||Q?"show":"hide"]().html(K.previous),K.slideshow&&F.show();if(K.preloading){b=[$(-1),$(1)];while(c=y[b.pop()])q=a.data(c,e),q&&q.href?(o=q.href,a.isFunction(o)&&(o=o.call(c))):o=c.href,ab(o)&&(p=new Image,p.src=o)}}else J.hide();K.iframe?(h=Z("iframe")[0],i in h&&(h[i]=0),k in h&&(h[k]="true"),h.name=f+ +(new Date),K.fastIframe?l():a(h).one("load",l),h.src=K.href,K.scrolling||(h.scrolling="no"),a(h).addClass(f+"Iframe").appendTo(A).one(m,function(){h.src="//about:blank"})):l(),K.transition==="fade"?r.fadeTo(d,1,s):s()},K.transition==="fade"?r.fadeTo(d,0,function(){W.position(0,c)}):W.position(d,c)},W.load=function(b){var c,d,e=W.prep;T=!0,R=!1,P=y[Q],b||bb(),cb(m),cb(i,K.onLoad),K.h=K.height?_(K.height,"y")-N-L:K.innerHeight&&_(K.innerHeight,"y"),K.w=K.width?_(K.width,"x")-O-M:K.innerWidth&&_(K.innerWidth,"x"),K.mw=K.w,K.mh=K.h,K.maxWidth&&(K.mw=_(K.maxWidth,"x")-O-M,K.mw=K.w&&K.w<K.mw?K.w:K.mw),K.maxHeight&&(K.mh=_(K.maxHeight,"y")-N-L,K.mh=K.h&&K.h<K.mh?K.h:K.mh),c=K.href,V=setTimeout(function(){C.show()},100),K.inline?(Z(X).hide().insertBefore(a(c)[0]).one(m,function(){a(this).replaceWith(A.children())}),e(a(c))):K.iframe?e(" "):K.html?e(K.html):ab(c)?(a(R=new Image).addClass(f+"Photo").error(function(){K.title=!1,e(Z(X,"Error").html(K.imgError))}).load(function(){var a;R.onload=null,K.scalePhotos&&(d=function(){R.height-=R.height*a,R.width-=R.width*a},K.mw&&R.width>K.mw&&(a=(R.width-K.mw)/R.width,d()),K.mh&&R.height>K.mh&&(a=(R.height-K.mh)/R.height,d())),K.h&&(R.style.marginTop=Math.max(K.h-R.height,0)/2+"px"),y[1]&&(K.loop||y[Q+1])&&(R.style.cursor="pointer",R.onclick=function(){W.next()}),n&&(R.style.msInterpolationMode="bicubic"),setTimeout(function(){e(R)},1)}),setTimeout(function(){R.src=c},1)):c&&B.load(c,K.data,function(b,c,d){e(c==="error"?Z(X,"Error").html(K.xhrError):a(this).contents())})},W.next=function(){!T&&y[1]&&(K.loop||y[Q+1])&&(Q=$(1),W.load())},W.prev=function(){!T&&y[1]&&(K.loop||Q)&&(Q=$(-1),W.load())},W.close=function(){S&&!U&&(U=!0,S=!1,cb(k,K.onCleanup),z.unbind("."+f+" ."+p),q.fadeTo(200,0),r.stop().fadeTo(300,0,function(){r.add(q).css({opacity:1,cursor:"auto"}).hide(),cb(m),A.remove(),setTimeout(function(){U=!1,cb(l,K.onClosed)},1)}))},W.remove=function(){a([]).add(r).add(q).remove(),r=null,a("."+g).removeData(e).removeClass(g).die()},W.element=function(){return a(P)},W.settings=d})(jQuery,document,this);
/* End */
;
; /* Start:/bitrix/templates/s1_common/scripts.js*/
/*==========================*/
/* customStyle plugin */
/* By Adam Coulombe */
/* Modified version of https://github.com/adamcoulombe/jquery.customSelect */
/* Lightweight, unobtrusive, custom style select boxes with jQuery */
/*==========================*/

(function($){
$.fn.extend({
customStyle : function(options) {
  if(!$.browser.msie || ($.browser.msie&&$.browser.version>7)){
    return this.each(function() {
      var currentSelected = $(this).find(':selected');
      var html = currentSelected.html();
      if(!html){ html=(options && options.empty ? options.empty : '&nbsp;'); }
      $(this).after('<span class="custom-style-select-box"><span class="custom-style-select-box-inner">'+html+'</span></span>').css({position:'absolute', opacity:0,fontSize:$(this).next().css('font-size')});
      var selectBoxSpan = $(this).next();
      var selectBoxWidth = parseInt($(this).width()) - parseInt(selectBoxSpan.css('padding-left')) -parseInt(selectBoxSpan.css('padding-right'));
      var selectBoxSpanInner = selectBoxSpan.find(':first-child');
      selectBoxSpan.css({display:'inline-block'});
      selectBoxSpanInner.css({width:selectBoxWidth, display:'inline-block'});
      var selectBoxHeight = parseInt(selectBoxSpan.height()) + parseInt(selectBoxSpan.css('padding-top')) + parseInt(selectBoxSpan.css('padding-bottom'));
      $(this).width(selectBoxWidth+30).height(selectBoxHeight).change(function(){
      // selectBoxSpanInner.text($(this).val()).parent().addClass('changed');  This was not ideal
      selectBoxSpanInner.text($(this).find(':selected').text()).parent().addClass('changed');
      // Thanks to Juarez Filho & PaddyMurphy
      });
    });
  }
}
});
})(jQuery);

/*==========================*/
/* imagesLoaded plugin */
/*==========================*/

/*
 * jQuery imagesLoaded plugin v2.0.1
 * http://github.com/desandro/imagesloaded
 *
 * MIT License. by Paul Irish et al.
 */

(function(c,n){var k="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==";c.fn.imagesLoaded=function(l){function m(){var b=c(h),a=c(g);d&&(g.length?d.reject(e,b,a):d.resolve(e));c.isFunction(l)&&l.call(f,e,b,a)}function i(b,a){b.src===k||-1!==c.inArray(b,j)||(j.push(b),a?g.push(b):h.push(b),c.data(b,"imagesLoaded",{isBroken:a,src:b.src}),o&&d.notifyWith(c(b),[a,e,c(h),c(g)]),e.length===j.length&&(setTimeout(m),e.unbind(".imagesLoaded")))}var f=this,d=c.isFunction(c.Deferred)?c.Deferred():
0,o=c.isFunction(d.notify),e=f.find("img").add(f.filter("img")),j=[],h=[],g=[];e.length?e.bind("load.imagesLoaded error.imagesLoaded",function(b){i(b.target,"error"===b.type)}).each(function(b,a){var e=a.src,d=c.data(a,"imagesLoaded");if(d&&d.src===e)i(a,d.isBroken);else if(a.complete&&a.naturalWidth!==n)i(a,0===a.naturalWidth||0===a.naturalHeight);else if(a.readyState||a.complete)a.src=k,a.src=e}):m();return d?d.promise(f):f}})(jQuery);

/*!
 * jQuery Smooth Scroll Plugin v1.4.5
 *
 * Date: Sun Mar 11 18:17:42 2012 EDT
 * Requires: jQuery v1.3+
 *
 * Copyright 2012, Karl Swedberg
 * Dual licensed under the MIT and GPL licenses (just like jQuery):
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
*/
(function(b){function m(c){return c.replace(/(:|\.)/g,"\\$1")}var n=function(c){var e=[],a=false,d=c.dir&&c.dir=="left"?"scrollLeft":"scrollTop";this.each(function(){if(!(this==document||this==window)){var g=b(this);if(g[d]()>0)e.push(this);else{g[d](1);a=g[d]()>0;g[d](0);a&&e.push(this)}}});if(c.el==="first"&&e.length)e=[e.shift()];return e},o="ontouchend"in document;b.fn.extend({scrollable:function(c){return this.pushStack(n.call(this,{dir:c}))},firstScrollable:function(c){return this.pushStack(n.call(this,
{el:"first",dir:c}))},smoothScroll:function(c){c=c||{};var e=b.extend({},b.fn.smoothScroll.defaults,c),a=b.smoothScroll.filterPath(location.pathname);this.die("click.smoothscroll").live("click.smoothscroll",function(d){var g={},i=b(this),f=location.hostname===this.hostname||!this.hostname,h=e.scrollTarget||(b.smoothScroll.filterPath(this.pathname)||a)===a,k=m(this.hash),j=true;if(!e.scrollTarget&&(!f||!h||!k))j=false;else{f=e.exclude;h=0;for(var l=f.length;j&&h<l;)if(i.is(m(f[h++])))j=false;f=e.excludeWithin;
h=0;for(l=f.length;j&&h<l;)if(i.closest(f[h++]).length)j=false}if(j){d.preventDefault();b.extend(g,e,{scrollTarget:e.scrollTarget||k,link:this});b.smoothScroll(g)}});return this}});b.smoothScroll=function(c,e){var a,d,g,i;i=0;d="offset";var f="scrollTop",h={},k=false;g=[];if(typeof c==="number"){a=b.fn.smoothScroll.defaults;g=c}else{a=b.extend({},b.fn.smoothScroll.defaults,c||{});if(a.scrollElement){d="position";a.scrollElement.css("position")=="static"&&a.scrollElement.css("position","relative")}g=
e||b(a.scrollTarget)[d]()&&b(a.scrollTarget)[d]()[a.direction]||0}a=b.extend({link:null},a);f=a.direction=="left"?"scrollLeft":f;if(a.scrollElement){d=a.scrollElement;i=d[f]()}else{d=b("html, body").firstScrollable();k=o&&"scrollTo"in window}h[f]=g+i+a.offset;a.beforeScroll.call(d,a);if(k){g=a.direction=="left"?[h[f],0]:[0,h[f]];window.scrollTo.apply(window,g);a.afterScroll.call(a.link,a)}else{i=a.speed;if(i==="auto"){i=h[f]||d.scrollTop();i/=a.autoCoefficent}d.animate(h,{duration:i,easing:a.easing,
complete:function(){a.afterScroll.call(a.link,a)}})}};b.smoothScroll.version="1.4.4";b.smoothScroll.filterPath=function(c){return c.replace(/^\//,"").replace(/(index|default).[a-zA-Z]{3,4}$/,"").replace(/\/$/,"")};b.fn.smoothScroll.defaults={exclude:[],excludeWithin:[],offset:0,direction:"top",scrollElement:null,scrollTarget:null,beforeScroll:function(){},afterScroll:function(){},easing:"swing",speed:400,autoCoefficent:2}})(jQuery);

$(function(){

/*==========================*/
/* Global Variables */
/*==========================*/
var   twitterID = 'kickgoods',
      slideshow = true,
      slideshow_auto = true,
      slideshow_speed = 5000, 
      product_image_w_to_h_ratio = 1.5,
      shop_url = 'http://www.kickgoods.ru';

var   THE_BODY              = $('body'),
      HEADER                = $('#header'),
      FOOTER                = $('#footer'),
      IS_INDEX              = (THE_BODY.hasClass('template-index')) ? true : false,
      IS_COLLECTION         = (THE_BODY.hasClass('template-collection')) ? true : false,
      IS_COLLECTION_LISTING = ($('#collection-list').length > 0) ? true : false,
      IS_PRODUCT            = (THE_BODY.hasClass('template-product')) ? true : false,
      IS_BLOG               = (THE_BODY.hasClass('template-blog')) ? true : false,
      IS_ARTICLE            = (THE_BODY.hasClass('template-article')) ? true : false,
      IS_SEARCH             = (THE_BODY.hasClass('template-search')) ? true : false,
      IS_CART               = (THE_BODY.hasClass('template-cart')) ? true : false,
      HAS_LOGO              = (HEADER.hasClass('use-logo')) ? true : false,
      BE_WIDE               = (HEADER.hasClass('wide')) ? true : false,
      HAS_CURRENCIES        = (HEADER.hasClass('currencies')) ? true : false,
      HAS_TWITTER           = (FOOTER.hasClass('has-twitter')) ? true : false,
      IS_IE                 = ($.browser.msie) ? true : false,
      PRODUCT_IMAGE_W_TO_H_RATIO = product_image_w_to_h_ratio || 1,
      THREE_PER_ROW_W       = 268,
      FOUR_PER_ROW_W        = 191,
      THREE_PER_ROW_H       = parseInt(THREE_PER_ROW_W/PRODUCT_IMAGE_W_TO_H_RATIO,  10),
      FOUR_PER_ROW_H        = parseInt(FOUR_PER_ROW_W/PRODUCT_IMAGE_W_TO_H_RATIO,   10);

$('html').removeClass('no-js');

/* Global JS */
/*==========================*/

/* loadImages function */
/* elems are the images, and ch is the container height */
/* Sizes image appropriately to fill as much of the container as possible
   without cropping it, and making sure the image is vertically aligned */
var loadImages = function(elems, ch) {

    $(elems).each(function(){
        $(this).imagesLoaded( function() {
            var i_w = $(this).width();  // image width
            var i_h = $(this).height(); // image height
            var c_h = ch;               // container height
            var v_o = (c_h - i_h) / 2;  // vertical offset            
            if ( i_h > c_h ) {
                $(this).css('height',ch).css('width','auto');
            } else {
                $(this).css('margin-top',v_o);
            }   
            $(this).fadeTo(200,1); // reveals image with a 200 ms-lomg fade-in.
        });
    });
}

/* Custom Select Styling */
/*==========================*/
$('select').not('#product-select, .single-option-selector').addClass('special-select').customStyle();

/* Snippet JS */
/*==========================*/

/* Additional Products */
loadImages('.related-products-list img',FOUR_PER_ROW_H);

/* Layout JS */
/*==========================*/

/* Handle footer */
$('#footer-modules li:last-child').css('margin-right', 0)

/* Handle Cart Total */
var char_elem = $('#cart-price');
var char_count = char_elem.text().length;

if (char_count <= 5) { char_elem.css('opacity',1); }
if (char_count >= 6) { char_elem.css('font-size', '18px').css('opacity',1) }
if (char_count >= 7) { char_elem.css('font-size', '15px').css('opacity',1) }
if (char_count >= 8) { char_elem.css('font-size', '13px').css('opacity',1) }
if (char_count >= 9) { char_elem.css('font-size', '11px').css('opacity',1) }

/* Format Navigation */
/* Will the nav bar be on the right of the logo or site title,
   or will it be below and full width */
   
var logo_title = $('#logo,#title');
var nav_width = 0;
var max_nav_width = 592;
var site_width = 884;
var nav_elem = $('#nav');
var nav_item = $('#nav .nav-item');
var hidden_header_items = $('#nav, #title, #logo');

// Calculating the width of all the links.
nav_item.each(function(){
    nav_width += $(this).outerWidth();
});

// If we have enough links, then we will have the logo or title
// above the menu. End of story.
if ( BE_WIDE || (nav_width >= max_nav_width) ) {
    HEADER.addClass('wide');
    hidden_header_items.css('visibility','visible');
}
// If we need to know the width of the logo or site title.
else {
    // If we have a logo.
    if (HAS_LOGO) {
        // The logo image,
        var logo = $('#logo img');
        // Its width.
        var logo_width = logo.width();
        var logo_height = logo.height();
        // If the logo was cached, yay!
        if (logo_width > 0) {
            if ((nav_width + logo_width) >= site_width) {
                HEADER.addClass('wide');
            }
            else {
                $('#nav').css('marginTop', logo_height*0.45 +20);
            }
            hidden_header_items.css('visibility','visible');
        }
        // If not, we need to wait till it is loaded...
        else {
            // Waiting...
            logo.load(function() {
                var logo_width = $(this).width();
                var logo_height = $(this).height();
                if ((nav_width + logo_width) >= site_width) {
                    HEADER.addClass('wide');
                }
                else {
                    $('#nav').css('marginTop', logo_height*0.45 +20);
                }
                hidden_header_items.css('visibility','visible');
            });
        }
    }
    // If we have a site title.
    else {
        var title_width = logo_title.width();
        if ((nav_width + title_width) >= site_width) {
            HEADER.addClass('wide');
        }
        hidden_header_items.css('visibility','visible');    
    }
}

/* Twitter Formatting Functions */
function linkifyTweet(tweetText) {
    return tweetText
    .replace(/((ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&?!\-\/]))?)/gi,'<a href="$1">$1</a>')
    .replace(/(^|\s)#(\w+)/g,'$1<a href="http://search.twitter.com/search?q=$2">#$2</a>')
    .replace(/(^|\s)@(\w+)/g,'$1<a href="http://twitter.com/$2">@$2</a>');
}

function relativeTime(time_value) {
    var parsed_date = parseDate(time_value);
    var relative_to = (arguments.length > 1) ? arguments[1] : new Date();
    var delta = parseInt((relative_to.getTime() - parsed_date) / 1000);
    if(delta < 60) {
        return 'меньше минуты';
    } else if(delta < 120) {
        return 'минуту назад';
    } else if(delta < (45*60)) {
        return (parseInt(delta / 60)).toString() + ' минут назад';
    } else if(delta < (90*60)) {
        return 'час назад';
    } else if(parseInt(delta / 3600)<5) {
        return ' ' + (parseInt(delta / 3600)).toString() + ' часа назад';
    } else if(delta < (24*60*60)) {
        return ' ' + (parseInt(delta / 3600)).toString() + ' часов назад';  
    } else if(delta < (48*60*60)) {
        return '1 день назад';
    } else if(parseInt(delta / 86400)<5) {
        return (parseInt(delta / 86400)).toString() + ' дня назад'  
    } else {
        return (parseInt(delta / 86400)).toString() + ' дней назад';
    }
  
}

function parseDate(str) {
    var v=str.split(' '), year, time;
    // date string from tumblr's tweet data is slightly different from twitter timeline data
    if (/\+0000/.test(v[5])) {
        year = v[3];
        time = v[4]
    } else {
        year = v[5]
        time = v[3]
    }
    return new Date(Date.parse(v[1]+" "+v[2]+", "+year+" "+time+" UTC"));
}

/* Grab Twitter Feed */ 
function init_twitter() {

    $.getJSON('https://api.twitter.com/1/statuses/user_timeline.json?screen_name='+ twitterID +'&count=1&callback=?&include_rts=true', function(data){
        var tweet = data[0];
        var tweetID = tweet.id_str;
        var tweetText = linkifyTweet(tweet.text);
        var timeago = relativeTime(tweet.created_at);
        var permalink = 'http://twitter.com/pixelunion/status/' + tweetID;
        var fullname = tweet.user.screen_name;
        var output  = $('<p class="tweet">' + tweetText + '</p>');
        var timestamp = $('<a class="timestamp accent-text" href="' + permalink + '" time="' + tweet.created_at + '" target="_blank">' + timeago + '</a>');
        var user = $('<a href="http://www.twitter.com/'+ twitterID +'" class="twitter-name">'+ fullname +'</a>');
        var twitter_avatar = $('<img src="'+ tweet.user.profile_image_url_https +'">');
        
        output.appendTo('.tweet-area');
        user.appendTo('.twitter-names');
        timestamp.appendTo('.twitter-names');
        twitter_avatar.appendTo('.twitter-avatar');
        
    });
}

/* Initialize Twitter */
if ( HAS_TWITTER ) {
    init_twitter();
}


/* Index JS */
/*==========================*/

if (IS_INDEX) {

    if (slideshow) {
        /* Slideshow */

        var container_width = 884;
        var main_slider = $('#slides');
        var slide_count = $('.slide').length;
        var main_slider_width = slide_count * container_width;

        main_slider.css('width', main_slider_width);
        
        if (slide_count > 1) {
            for (i=1;i<=slide_count;i++) {
                $('<li>', {
                    id:'slide-control-' + i,
                    'class':'slide-control'
                    }).appendTo('#slideshow-controls').html('&#8226;');
            }
        } else {
            $('#slideshow-controls').remove();
        }      

        if (slideshow_auto && slide_count > 1 ) {
            /* Auto Slide */
            function autoSlide() {
                var current_slide = $('.slide-control.active');
                var current_slide_index = current_slide.index();

                if (current_slide_index != (slide_count - 1)) {
                    var elem = current_slide.next();
                    var slider_distance = (elem.index() * container_width) * -1;
                    $('#slides').animate({
                        marginLeft: slider_distance
                    });
                    $('.slide-control').removeClass('active');
                    elem.addClass('active');
                } else {
                    var elem = $('.slide-control').eq(0);
                    var slider_distance = (elem.index() * container_width) * -1;
                    $('#slides').animate({
                        marginLeft: slider_distance
                    });
                    $('.slide-control').removeClass('active');
                    elem.addClass('active');
                }
            }

            function init_auto_slide() {
                startAutoSlide = setInterval(autoSlide,slideshow_speed);
            };
            init_auto_slide();

        }
      
        if (slide_count > 1) {          

          $("#slide-prew-next .slide-prew-next-left-button").click(function(){
            if (slideshow_auto == true) {
              clearInterval(startAutoSlide);
            }
            var current_slide = $('.slide-control.active');
            var current_slide_index = current_slide.index();
                        
            if (current_slide_index != 0) {
                var elem = current_slide.prev();              
                var slider_distance = (elem.index() * container_width) * -1;
                $('#slides').animate({
                    marginLeft: slider_distance
                });
                $('.slide-control').removeClass('active');
                elem.addClass('active');
            } else {
                var elem = $('.slide-control').eq(slide_count - 1);             
                var slider_distance = (elem.index() * container_width) * -1;
                $('#slides').animate({
                    marginLeft: slider_distance
                });
                $('.slide-control').removeClass('active');
                elem.addClass('active');
            }
            return false;
          });
          
          $("#slide-prew-next .slide-prew-next-right-button").click(function(){
            if (slideshow_auto == true) {
              clearInterval(startAutoSlide);
            }
            var current_slide = $('.slide-control.active');
            var current_slide_index = current_slide.index();
                        
            if (current_slide_index != (slide_count - 1)) {
                var elem = current_slide.next();
                var slider_distance = (elem.index() * container_width) * -1;
                $('#slides').animate({
                    marginLeft: slider_distance
                });
                $('.slide-control').removeClass('active');
                elem.addClass('active');
            } else {
                var elem = $('.slide-control').eq(0);
                var slider_distance = (elem.index() * container_width) * -1;
                $('#slides').animate({
                    marginLeft: slider_distance
                });
                $('.slide-control').removeClass('active');
                elem.addClass('active');
            }
            return false;
          });
                   
        }else{
          $('#slide-prew-next').remove();
        }
      
        $('.slide-control').click(function(){

            if (slideshow_auto == true) {
                clearInterval(startAutoSlide);
            }

            var elem = $(this);
            var slider_distance = (elem.index() * container_width) * -1;
            $('#slides').animate({
                marginLeft: slider_distance
            });
            $('.slide-control').removeClass('active');
            elem.addClass('active');
        });

        $('#slide-control-1').addClass('active');

        /* Resize Video */
        var $allVideos = $("#slides iframe[src^='http://www.youtube.com'], #slides iframe[src^='http://player.vimeo.com']");
        var newHeight = 490;
        $allVideos.each(function(){
            var aspect_ratio = this.width / this.height;
            $(this)
            .removeAttr('height')
            .removeAttr('width')
            .height(newHeight)
            .width(newHeight * aspect_ratio);
        });
            
    } // END of if (slideshow)

    /* Product Slider */

    /*
    Pay careful attention to the difference between "mini_slider" 
    which is the entire slider, and "mini_slide" which denotes 
    an individual slide
    */

    var mini_slider = $('#mini-slides');
    var mini_slide_count = $('#mini-slides > li').length;
    var mini_slide_width = THREE_PER_ROW_W;
    var mini_slide_margin = 40;
    var mini_slide_total_width = mini_slide_width + mini_slide_margin;
    var mini_slider_distance = mini_slide_total_width * 3;
    var mini_slider_width = mini_slide_count * mini_slide_total_width;
    var current_position = 0;

    /* Set Slider Width */
    mini_slider.css('width', mini_slider_width);

    /* Preload and Format Images */
    loadImages('#mini-slides .three-per-row img',THREE_PER_ROW_H);
    loadImages('#mini-slides .four-per-row img',FOUR_PER_ROW_H);

    /* Next / Prev Function */

    var mini_slide_action = function(direction) {
        var animating = ($(mini_slider).filter(':animated').length) ? true : false;
        current_position = parseFloat(mini_slider.css('margin-left'));
        var more_to_load = ( (mini_slider_width - (current_position * -1)) > mini_slider_distance ) ? true : false;

        if (!animating && direction == 'next' && more_to_load) {
            mini_slider.animate({
                marginLeft: '-=' + mini_slider_distance
            },400, 'swing',function(){
                current_position = parseFloat(mini_slider.css('margin-left'));
                more_to_load = ( (mini_slider_width - (current_position * -1)) > mini_slider_distance ) ? true : false;

                $('#mini-slider-prev').fadeIn(200);
                if (more_to_load == false) {
                    $('#mini-slider-next').fadeOut(200);
                }
            });

        }
        if (!animating && direction == 'prev' && current_position != 0 ) {
            mini_slider.animate({
                marginLeft: '+=' + mini_slider_distance
            },400,'swing',function(){

                current_position = parseFloat(mini_slider.css('margin-left'));

                $('#mini-slider-next').fadeIn(200);
                if (current_position == 0) {
                    $('#mini-slider-prev').fadeOut(200);
                }
            });

        }
    }
    
    if ( mini_slide_count <= 3 ) {
        $('#mini-slider-next').hide();
    }
    
    /* Auto hide prev */
    $('#mini-slider-prev').hide();

    /* Next */
    $('#mini-slider-next').click(function(){
        mini_slide_action('next');
        return false;
    });

    /* Prev */
    $('#mini-slider-prev').click(function(){
        mini_slide_action('prev');
        return false;
    });


    /* Front Page Product List */

    /* Preload and Format Images */
    loadImages('#fp-product-list .four-per-row img', FOUR_PER_ROW_H);
    loadImages('#fp-product-list .three-per-row img', THREE_PER_ROW_H);

    /* Set equal row heights */
    var golden_height = 0;
    $('#fp-product-list li').each(function(i){
        if ($(this).height() > golden_height) {
            golden_height = $(this).height();
        }
    });

    $('#fp-product-list li').css('height',golden_height);

} // END of IS_INDEX



/* Product JS */
/*==========================*/
if (IS_PRODUCT) {

    // Activate "Add Medallion"
    $('#product-add-medallion').click(function(){
        $('#add').click();
    });

    // PRODUCT VIEWER

    // Format Thumbnails
    loadImages('.product-photo-thumb img',114);

    // Activate Colorbox
    $('a.gallery').colorbox( {
        rel:'gallery',
        maxWidth:"95%",
        maxHeight:"95%",
        scalePhotos:true} );

    var product_container = $('#product-photo-container');      

    // Initialize first photo
    product_container.find('img:first').imagesLoaded(function(){
        var elem = $(this);
        elem.addClass('active').fadeIn(100);
        product_container.css('height',elem.height());
        elem.parent().css( {'height':elem.height(), 'width':elem.width()} );
    });

    // Display new photo
    $('.product-photo-thumb').click(function(){

        var active_index = $(this).index();
        var photo_to_show = product_container.find('img').eq(active_index);
        var photo_to_hide = product_container.find('.active');

        photo_to_hide.fadeOut(100, function(){
            photo_to_hide.removeClass('active');
            photo_to_hide.parent().css( {'height':0, 'width':0} );
            var photo_to_show = product_container.find('img').eq(active_index);
            photo_to_show.imagesLoaded(function(){
                product_container.animate({height:photo_to_show.height()},200,function(){
                    photo_to_show.fadeIn(100, function(){
                        $(this).addClass('active');
                        $(this).parent().css( {'height':$(this).height(), 'width':$(this).width()} );
                    });
                });
            });
        });

    }); 

} // END of IS_PRODUCT

                    
/* Placeholder JS */
/*==========================*/

$('[placeholder]').each(function(){
    if ($(this).val() === '') {
        var hint = $(this).attr('placeholder');
        $(this).val(hint).addClass('hint');
    }
});

$('[placeholder]').focus(function() {
    if ($(this).val() === $(this).attr('placeholder')) {
        $(this).val('').removeClass('hint');
    }
}).blur(function() {
    if ($(this).val() === '') {
        $(this).val($(this).attr('placeholder')).addClass('hint');
    }
});                    

/* Form validation JS */
/*==========================*/

$('input.error, textarea.error').focus(function() {
    $(this).removeClass('error');
});

$('form :submit').click(function() {
    $(this).parents('form').find('input.hint, textarea.hint').each(function() {
        $(this).val('').removeClass('hint');
    });
    return true;
});

/* Cart.liquid */
/*==========================*/
                    
if (IS_CART) {
    
    /* This auto-saves cart attribute and cart note.
       This will save quantity edits too.
       See this: http://wiki.shopify.com/Ask_customer_for_additional_information#My_clients_fill_up_the_cart_attributes.2C_but_they_are_not_saved._When_they_leave_the_cart_page_and_come_back.2C_the_boxes_previously_filled-up_are_empty._How_can_I_fix_this.3F */
    $(window).unload(function() {
        var params = {
            type: 'POST',
            url: '/cart/update.js',
            data: $('form[action="/cart"]').serialize(),
            dataType: 'json',
            async: false
        };
        $.ajax(params);
    });

}

/* IE JS */
/*==========================*/
if (IS_IE) {
    $('#widgets li:last-child').css('border-bottom','none');
    $('#product-details li:last-child').css('border-bottom','none');
}

/* Multiple currencies */
/*==========================*/

if (HAS_CURRENCIES) {
    
    $('#currency-picker-toggle a').click(function() {
        $('#currency-picker-toggle').hide();
        $('#currencies-picker').fadeIn();
        return false;
    });

    $('#currencies-picker select').change(function() {
        $('#currencies-picker').hide();
        $('#currency-picker-toggle').fadeIn();
        return true;
    }).blur(function() {
        $('#currencies-picker').hide();
        $('#currency-picker-toggle').fadeIn();
        return true;
    });

}

/* Social sharing stats */
/*==========================*/

$('.share-stats').each(function(){
    var wrapper = $(this);
    var stats = '';
    var url = $(this).attr('data-url');
    $.when(
        $.getJSON('http://cdn.api.twitter.com/1/urls/count.json?callback=?&url=' + url),
        $.getJSON("https://api.facebook.com/method/fql.query?query=select%20total_count,like_count,comment_count,share_count,click_count%20from%20link_stat%20where%20url='" + url + "'&format=json")
    ).then(function(dataTwitter, dataFacebook) {
        var times = ' раз ';
        var tweetCount = dataTwitter[0].count;
        var facebookCount = dataFacebook[0][0].total_count;
        if (tweetCount > 0) {
            times = ( tweetCount == 1 ) ? ' раз ' : ' раз ';
            stats += 'Ретвитнуло '+tweetCount+times;
        }
        if (tweetCount > 0 && facebookCount > 0) {
            stats += ' и зашарено ';
        }
        else if (facebookCount > 0) {
            stats += ' Поделились ';
        }
        if (facebookCount > 0) {
            times = ( facebookCount == 1 ) ? ' раз ' : ' раз ';
            stats += facebookCount + times + 'на Фейсбуке';
        }
        wrapper.html(stats);
    });  
});

/* Lightbox ALL THE THINGS (images) in RTE-generated content */

if ($.isFunction($.fn.colorbox)) {
  // For all images added with the RTE that aren't linking to a page.
  /*$('.rte img').not('a > img').each(function() {
    // Matching images that aren't already shown in their original size.
    var re = /(_small)|(_compact)|(_medium)|(_large)|(_grande)/;
    var src = $(this).attr('src');
    if (re.test(src)) {
      // Determining the URL to the original image.
      var href = src.replace(re, '');
      // Activating lightbox.
      $(this).wrap('<a></a>')
        .parent()
        .attr('href', href)
        .addClass('lightbox')
        .colorbox( {
            maxWidth:"95%",
            maxHeight:"95%",
            scalePhotos:true} );
    }
  });*/
  $('.lightbox').colorbox( {
            maxWidth:"95%",
            maxHeight:"95%",
            scalePhotos:true} );
  $('.inline').colorbox( {
            maxWidth:"95%",
            maxHeight:"95%",
            inline:true} );
}

/* Smooth scrolling */
$('a').smoothScroll();

/* Follow along table of content */
/* http://css-tricks.com/scrollfollow-sidebar */

var $sidebar   = $(".follow-along"), 
    $window    = $(window),
    offset     = $sidebar.offset(),
    topPadding = 15;
    
    if ($sidebar.length) {
        $window.scroll(function() {
            if ($window.scrollTop() > offset.top) {
                $sidebar.stop().animate({
                    marginTop: $window.scrollTop() - offset.top + topPadding
                });
            } else {
                $sidebar.stop().animate({
                    marginTop: 0
                });
            }
        });    
    }
    
/* Open all external + PDF download links in a new browser tab */


});

/* End */
;
; /* Start:/bitrix/components/bitrix/search.title/script.js*/
function JCTitleSearch(arParams)
{
	var _this = this;

	this.arParams = {
		'AJAX_PAGE': arParams.AJAX_PAGE,
		'CONTAINER_ID': arParams.CONTAINER_ID,
		'INPUT_ID': arParams.INPUT_ID,
		'MIN_QUERY_LEN': parseInt(arParams.MIN_QUERY_LEN)
	};
	if(arParams.WAIT_IMAGE)
		this.arParams.WAIT_IMAGE = arParams.WAIT_IMAGE;
	if(arParams.MIN_QUERY_LEN <= 0)
		arParams.MIN_QUERY_LEN = 1;

	this.cache = [];
	this.cache_key = null;

	this.startText = '';
	this.running = false;
	this.currentRow = -1;
	this.RESULT = null;
	this.CONTAINER = null;
	this.INPUT = null;
	this.WAIT = null;

	this.ShowResult = function(result)
	{
		if(BX.type.isString(result))
		{
			_this.RESULT.innerHTML = result;
		}

		_this.RESULT.style.display = _this.RESULT.innerHTML !== '' ? 'block' : 'none';
		var pos = _this.adjustResultNode();

		//adjust left column to be an outline
		var res_pos;
		var th;
		var tbl = BX.findChild(_this.RESULT, {'tag':'table','class':'title-search-result'}, true);
		if(tbl)
		{
			th = BX.findChild(tbl, {'tag':'th'}, true);
		}

		if(th)
		{
			var tbl_pos = BX.pos(tbl);
			tbl_pos.width = tbl_pos.right - tbl_pos.left;

			var th_pos = BX.pos(th);
			th_pos.width = th_pos.right - th_pos.left;
			th.style.width = th_pos.width + 'px';

			_this.RESULT.style.width = (pos.width + th_pos.width) + 'px';

			//Move table to left by width of the first column
			_this.RESULT.style.left = (pos.left - th_pos.width - 1)+ 'px';

			//Shrink table when it's too wide
			if((tbl_pos.width - th_pos.width) > pos.width)
				_this.RESULT.style.width = (pos.width + th_pos.width -1) + 'px';

			//Check if table is too wide and shrink result div to it's width
			tbl_pos = BX.pos(tbl);
			res_pos = BX.pos(_this.RESULT);
			if(res_pos.right > tbl_pos.right)
			{
				_this.RESULT.style.width = (tbl_pos.right - tbl_pos.left) + 'px';
			}
		}

		var fade;
		if(tbl) fade = BX.findChild(_this.RESULT, {'class':'title-search-fader'}, true);
		if(fade && th)
		{
			res_pos = BX.pos(_this.RESULT);
			fade.style.left = (res_pos.right - res_pos.left - 18) + 'px';
			fade.style.width = 18 + 'px';
			fade.style.top = 0 + 'px';
			fade.style.height = (res_pos.bottom - res_pos.top) + 'px';
			fade.style.display = 'block';
		}
	};

	this.onKeyPress = function(keyCode)
	{
		var tbl = BX.findChild(_this.RESULT, {'tag':'table','class':'title-search-result'}, true);
		if(!tbl)
			return false;

		var i;
		var cnt = tbl.rows.length;

		switch (keyCode)
		{
		case 27: // escape key - close search div
			_this.RESULT.style.display = 'none';
			_this.currentRow = -1;
			_this.UnSelectAll();
		return true;

		case 40: // down key - navigate down on search results
			if(_this.RESULT.style.display == 'none')
				_this.RESULT.style.display = 'block';

			var first = -1;
			for(i = 0; i < cnt; i++)
			{
				if(!BX.findChild(tbl.rows[i], {'class':'title-search-separator'}, true))
				{
					if(first == -1)
						first = i;

					if(_this.currentRow < i)
					{
						_this.currentRow = i;
						break;
					}
					else if(tbl.rows[i].className == 'title-search-selected')
					{
						tbl.rows[i].className = '';
					}
				}
			}

			if(i == cnt && _this.currentRow != i)
				_this.currentRow = first;

			tbl.rows[_this.currentRow].className = 'title-search-selected';
		return true;

		case 38: // up key - navigate up on search results
			if(_this.RESULT.style.display == 'none')
				_this.RESULT.style.display = 'block';

			var last = -1;
			for(i = cnt-1; i >= 0; i--)
			{
				if(!BX.findChild(tbl.rows[i], {'class':'title-search-separator'}, true))
				{
					if(last == -1)
						last = i;

					if(_this.currentRow > i)
					{
						_this.currentRow = i;
						break;
					}
					else if(tbl.rows[i].className == 'title-search-selected')
					{
						tbl.rows[i].className = '';
					}
				}
			}

			if(i < 0 && _this.currentRow != i)
				_this.currentRow = last;

			tbl.rows[_this.currentRow].className = 'title-search-selected';
		return true;

		case 13: // enter key - choose current search result
			if(_this.RESULT.style.display == 'block')
			{
				for(i = 0; i < cnt; i++)
				{
					if(_this.currentRow == i)
					{
						if(!BX.findChild(tbl.rows[i], {'class':'title-search-separator'}, true))
						{
							var a = BX.findChild(tbl.rows[i], {'tag':'a'}, true);
							if(a)
							{
								window.location = a.href;
								return true;
							}
						}
					}
				}
			}
		return false;
		}

		return false;
	};

	this.onTimeout = function()
	{
		_this.onChange(function(){
			setTimeout(_this.onTimeout, 500);
		});
	};

	this.onChange = function(callback)
	{
		if (_this.running)
			return;
		_this.running = true;

		if(_this.INPUT.value != _this.oldValue && _this.INPUT.value != _this.startText)
		{
			_this.oldValue = _this.INPUT.value;
			if(_this.INPUT.value.length >= _this.arParams.MIN_QUERY_LEN)
			{
				_this.cache_key = _this.arParams.INPUT_ID + '|' + _this.INPUT.value;
				if(_this.cache[_this.cache_key] == null)
				{
					if(_this.WAIT)
					{
						var pos = BX.pos(_this.INPUT);
						var height = (pos.bottom - pos.top)-2;
						_this.WAIT.style.top = (pos.top+1) + 'px';
						_this.WAIT.style.height = height + 'px';
						_this.WAIT.style.width = height + 'px';
						_this.WAIT.style.left = (pos.right - height + 2) + 'px';
						_this.WAIT.style.display = 'block';
					}

					BX.ajax.post(
						_this.arParams.AJAX_PAGE,
						{
							'ajax_call':'y',
							'INPUT_ID':_this.arParams.INPUT_ID,
							'q':_this.INPUT.value,
							'l':_this.arParams.MIN_QUERY_LEN
						},
						function(result)
						{
							_this.cache[_this.cache_key] = result;
							_this.ShowResult(result);
							_this.currentRow = -1;
							_this.EnableMouseEvents();
							if(_this.WAIT)
								_this.WAIT.style.display = 'none';
							if (!!callback)
								callback();
							_this.running = false;
						}
					);
					return;
				}
				else
				{
					_this.ShowResult(_this.cache[_this.cache_key]);
					_this.currentRow = -1;
					_this.EnableMouseEvents();
				}
			}
			else
			{
				_this.RESULT.style.display = 'none';
				_this.currentRow = -1;
				_this.UnSelectAll();
			}
		}
		if (!!callback)
			callback();
		_this.running = false;
	};

	this.UnSelectAll = function()
	{
		var tbl = BX.findChild(_this.RESULT, {'tag':'table','class':'title-search-result'}, true);
		if(tbl)
		{
			var cnt = tbl.rows.length;
			for(var i = 0; i < cnt; i++)
				tbl.rows[i].className = '';
		}
	};

	this.EnableMouseEvents = function()
	{
		var tbl = BX.findChild(_this.RESULT, {'tag':'table','class':'title-search-result'}, true);
		if(tbl)
		{
			var cnt = tbl.rows.length;
			for(var i = 0; i < cnt; i++)
				if(!BX.findChild(tbl.rows[i], {'class':'title-search-separator'}, true))
				{
					tbl.rows[i].id = 'row_' + i;
					tbl.rows[i].onmouseover = function (e) {
						if(_this.currentRow != this.id.substr(4))
						{
							_this.UnSelectAll();
							this.className = 'title-search-selected';
							_this.currentRow = this.id.substr(4);
						}
					};
					tbl.rows[i].onmouseout = function (e) {
						this.className = '';
						_this.currentRow = -1;
					};
				}
		}
	};

	this.onFocusLost = function(hide)
	{
		setTimeout(function(){_this.RESULT.style.display = 'none';}, 250);
	};

	this.onFocusGain = function()
	{
		if(_this.RESULT.innerHTML.length)
			_this.ShowResult();
	};

	this.onKeyDown = function(e)
	{
		if(!e)
			e = window.event;

		if (_this.RESULT.style.display == 'block')
		{
			if(_this.onKeyPress(e.keyCode))
				return BX.PreventDefault(e);
		}
	};

	this.adjustResultNode = function()
	{
		var pos;
		var fixedParent = BX.findParent(_this.CONTAINER, BX.is_fixed);
		if(!!fixedParent)
		{
			_this.RESULT.style.position = 'fixed';
			_this.RESULT.style.zIndex = BX.style(fixedParent, 'z-index') + 2;
			pos = BX.pos(_this.CONTAINER, true);
		}
		else
		{
			_this.RESULT.style.position = 'absolute';
			pos = BX.pos(_this.CONTAINER);
		}
		pos.width = pos.right - pos.left;
		_this.RESULT.style.top = (pos.bottom + 2) + 'px';
		_this.RESULT.style.left = pos.left + 'px';
		_this.RESULT.style.width = pos.width + 'px';
		return pos;
	};

	this._onContainerLayoutChange = function()
	{
		if(_this.RESULT.style.display !== "none" && _this.RESULT.innerHTML !== '')
		{
			_this.adjustResultNode();
		}
	};
	this.Init = function()
	{
		this.CONTAINER = document.getElementById(this.arParams.CONTAINER_ID);
		BX.addCustomEvent(this.CONTAINER, "OnNodeLayoutChange", this._onContainerLayoutChange);

		this.RESULT = document.body.appendChild(document.createElement("DIV"));
		this.RESULT.className = 'title-search-result';
		this.INPUT = document.getElementById(this.arParams.INPUT_ID);
		this.startText = this.oldValue = this.INPUT.value;
		BX.bind(this.INPUT, 'focus', function() {_this.onFocusGain()});
		BX.bind(this.INPUT, 'blur', function() {_this.onFocusLost()});

		if(BX.browser.IsSafari() || BX.browser.IsIE())
			this.INPUT.onkeydown = this.onKeyDown;
		else
			this.INPUT.onkeypress = this.onKeyDown;

		if(this.arParams.WAIT_IMAGE)
		{
			this.WAIT = document.body.appendChild(document.createElement("DIV"));
			this.WAIT.style.backgroundImage = "url('" + this.arParams.WAIT_IMAGE + "')";
			if(!BX.browser.IsIE())
				this.WAIT.style.backgroundRepeat = 'none';
			this.WAIT.style.display = 'none';
			this.WAIT.style.position = 'absolute';
			this.WAIT.style.zIndex = '1100';
		}

		BX.bind(this.INPUT, 'bxchange', function() {_this.onChange()});
	};
	BX.ready(function (){_this.Init(arParams)});
}

/* End */
;; /* /bitrix/templates/s1_common/js/jquery.colorbox-min.js*/
; /* /bitrix/templates/s1_common/scripts.js*/
; /* /bitrix/components/bitrix/search.title/script.js*/
