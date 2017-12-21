/**
 * @file
 * A JavaScript file for the theme.
 *
 * In order for this JavaScript to be loaded on pages, see the instructions in
 * the README.txt next to this file.
 */

// JavaScript should be made compatible with libraries other than jQuery by
// wrapping it with an "anonymous closure". See:
// - http://drupal.org/node/1446420
// - http://www.adequatelygood.com/2010/3/JavaScript-Module-Pattern-In-Depth

(function ($, Drupal, window, document, undefined) {

	$.fn.disableSelection = function() {
		return this.attr('unselectable', 'on').css({'-moz-user-select':'none',
                    '-o-user-select':'none',
                    '-khtml-user-select':'none',
                    '-webkit-user-select':'none',
                    '-ms-user-select':'none',
                    'user-select':'none'});

	};

	Drupal.behaviors.rewiredGlobal = {
		attach: function(context, settings) {
			$('#content').once(function(){
				
			pagerfloatfix();
			drupalhashjumpfix();
			
			// picture resizer 
			function pictureresizer() {
				var kepekfield = document.getElementsByClassName('rtecenter'); //.field-items
				var kepekfield2 = document.getElementsByClassName('field-type-image');
		
				kepekfield = [].concat(Array.prototype.slice.call(kepekfield), Array.prototype.slice.call(kepekfield2));
	
				if ( kepekfield != null ) {
					var kepek = new Array();
					var clientWidth = document.getElementById('main-wrapper').clientWidth;
		
					if (document.getElementsByClassName('forum-post-content')[0]) {
						if (clientWidth < 801) { //mobile
							if (Drupal.settings.ultramobil != 1) {
								clientWidth = clientWidth - 51;
							}
						}
			
						if (clientWidth  >= 801) { //desktop
							clientWidth = clientWidth - 151;
						}
					}
		
					if (document.getElementsByClassName('privatemsg-message-column')[0]) {
						if (clientWidth < 801) { //mobile
							clientWidth = clientWidth - 40 - 2;
						}
			
						if (clientWidth  >= 801) { //desktop
							clientWidth = clientWidth - 40 - 2;
						}
					}

					var szelessegAspect = (Drupal.settings.szelesseg -20 ) / 800;
					var clientWidthAspect = (clientWidth - 20) / 800;
		
					for (var i = 0; i < kepekfield.length; i++) { 
						if (typeof kepekfield[i].getElementsByTagName('img') != 'undefined') {
							kepek.push(kepekfield[i].getElementsByTagName('img'));
							}
					
						for (var j = 0; j < kepek[i].length; j++) {
							if (kepek[i][j].style.width != '') kepekwidth = parseInt(kepek[i][j].style.width);
							else kepekwidth = parseInt(kepek[i][j].width);
						
							if (szelessegAspect < 1 && kepekwidth <= 400 || clientWidthAspect < 1)	{ // page max-width smaller than 800px && picture smaller than half page width || client max-width smaller than 800px = pictures need to be scaled down to fit in row

								if (szelessegAspect <= clientWidthAspect) {
									kepekwidth = szelessegAspect * kepekwidth * 0.95 ; //0.97 is just a workaround, need to fix
								}
								else {
									kepekwidth = clientWidthAspect * kepekwidth * 0.95;
								}
	
								kepekwidth = "width:" + kepekwidth + "px !important";
								kepek[i][j].style.cssText = kepekwidth;	
							}
						}
					}
				}
			}
			pictureresizer();
			
			// iframe picture resizer 
			function iframePictureresizer() {
				var imageWrappers = document.getElementsByClassName('hotlinkedImageWrapper');
		
				if ( imageWrappers != null ) {
					var clientWidth = document.getElementById('main-wrapper').clientWidth;
		
					if (document.getElementsByClassName('forum-post-content')[0]) {
						if (clientWidth < 801) { //mobile
							if (Drupal.settings.ultramobil != 1) {
								clientWidth = clientWidth - 51;
							}
						}
			
						if (clientWidth  >= 801) { //desktop
							clientWidth = clientWidth - 151;
						}
					}
		
					if (document.getElementsByClassName('privatemsg-message-column')[0]) {
						if (clientWidth < 801) { //mobile
							clientWidth = clientWidth - 40 - 2;
						}
			
						if (clientWidth  >= 801) { //desktop
							clientWidth = clientWidth - 40 - 2;
						}
					}

					clientWidth = clientWidth - 20; //minus left and right margin
					presetForumWidth = Drupal.settings.szelesseg - 20; //minus left and right margin
					
					if ( clientWidth < presetForumWidth ) {
						commentWidth = clientWidth;
					}
					else {
						commentWidth = presetForumWidth;
					}
					
					for (var i = 0; i < imageWrappers.length; i++) {
						iframeTag = imageWrappers[i].getElementsByTagName('iframe')[0];
						
						
						imageWidth = iframeTag.getAttribute('width');
						imageHeight = iframeTag.getAttribute('height');
						//iframeGIF = (/(href.li)/i).test(iframeTag.getAttribute('data_url'));
						
						if ( commentWidth < imageWidth ) { // image scaling needed
							scalingAspect = commentWidth / imageWidth;
							scalingString = 'scale(' + scalingAspect + ')';
							//imageWrappers[i].style.transform = scalingString;
							imageWrappers[i].style[transformProp] = scalingString;
							//imageWrappers[i].style.transformOrigin="0 0";
						}
						else scalingAspect = 1;
						
						paddingAspect = imageHeight / commentWidth * scalingAspect;
						paddingString = ( paddingAspect * 100 ) + '%';
						imageWrappers[i].style.paddingBottom = paddingString;	
					}
				}
			}
			iframePictureresizer();
			
			//picture viewer init
			pictureviewerinit();
			
			// legfrissebb bejegyzesek filter fix CALL
			LBFF();
			
			// newsImagePlacer function Call
			newsImagePlacer();
			
			//Rewired Settings link fix
			var menuitem = document.getElementById('main-menu-links').getElementsByClassName('last')[0].getElementsByTagName('a')[0];
			menuitem.href = "javascript:rewiredsettings();";
			menuitem.className = "rs_element";
			
			// comment box auto resizer 
			function resizer() {
				var text1 = document.getElementById('edit-comment-body-und-0-value');	

				if (!text1) {
					var text1 = document.getElementById('edit-field-profil-und-0-value');	
					if (!text1) {
						var text1 = document.getElementById('edit-body-value');
						if (!text1) {
							var text1 = document.getElementById('edit-body-und-0-value');
							if (!text1) {
								return;
							}
						}
					}
				}
		
				var observe;

				if (window.attachEvent) {
					observe = function (element, event, handler) {
						element.attachEvent('on'+event, handler);
					};
				}
				else {
					observe = function (element, event, handler) {
						element.addEventListener(event, handler, false);
					};
				}
		
				function delayedResize () {
					window.setTimeout(resize, 500);
				}
	
				observe(text1, 'change',  delayedResize);
				observe(text1, 'cut',     delayedResize);
				observe(text1, 'paste',   delayedResize);
				observe(text1, 'drop',    delayedResize);
				observe(text1, 'keydown', delayedResize);

				function isMobile() {
					try{ document.createEvent("TouchEvent"); return true; }
					catch(e){ return false; }
				}
	
				var isMobileTest = isMobile();
	
				function text1focus () {
					var fejlec = document.getElementsByClassName('rewired-main-divider')[0];
		
					if ( isMobileTest == true) {
						fejlec.style.display= "none";
					}
				}
	
				function text1blur () {
					var fejlec = document.getElementsByClassName('rewired-main-divider')[0];
		
					if ( isMobileTest == true ) {
						fejlec.style.display= "table";
					}
				}
		
				//for mobiles
				observe(text1, 'focus', text1focus);
				observe(text1, 'blur', text1blur);
				
				resize (); 
			}
			resizer (); //document.addEventListener("DOMContentLoaded", function(event) { } );
			
			//scroll detector for scroll based element animations
			function scrollDetector() {
				var logo = document.getElementsByClassName('rewired-logo')[0];
				var mainMenuHeight = document.getElementById('main-menu').offsetHeight;
				
				var originalLogoWidth = logo.offsetWidth;
				var originalLogoHeight = logo.offsetHeight;
				//document.addEventListener("scroll", function(){function scrollAnimation();});
				document.addEventListener("scroll", function(){scrollAnimation(originalLogoWidth, originalLogoHeight, mainMenuHeight)});
				scrollAnimation(originalLogoWidth, originalLogoHeight, mainMenuHeight);
			}
			scrollDetector();
			
			function scrollAnimation(originalLogoWidth, originalLogoHeight,mainMenuHeight) {
				var scrollPosition = $(window).scrollTop();
				var logo = document.getElementsByClassName('rewired-logo')[0];
				var logoA = document.getElementById('rewired-logo-link');
		
				aspectRatio =  originalLogoWidth / originalLogoHeight;
	
				newLogoHeight = originalLogoHeight - scrollPosition * 0.28;
				if (newLogoHeight <= mainMenuHeight) newLogoHeight = mainMenuHeight;
				
				newLogoWidth = newLogoHeight * aspectRatio;
				
				logo.style.height = newLogoHeight + 'px';
				logoA.style.height = newLogoHeight + 'px';
				
				logo.style.width = newLogoWidth + 'px';
				logoA.style.width = newLogoWidth + 'px';
			}
			
			//add 'hasIMG' class to url with images			
			$('a:has(img)').addClass('hasIMG');
			
			// Click event for media to start video
			mw = $('.media-wrapper:not(.mediatitle)');
			mw.on( "click", function() {
				var iframeTag = $(this).find('iframe');
				var videoTag = $(this).find('video');
				var gifTag = $(this).find('.rawgif');
				var gifHotTag = $(this).find('.rawgifHotlinkOK');
				//console.log("my object: %o", iframeTag[0]);

				//gif tag found
				if (typeof gifTag[0] != 'undefined') { 
					var imgTag = $(this).find('.gifImageContainer');
					var playIcon = $(this).find('.play-icon');

					if (playIcon.hasClass('element-invisible')) { //make it stop
						$(this).removeClass('playing');
						playIcon.removeClass('element-invisible');
					}
					else { //make it play
						playIcon.addClass('element-invisible');
						$(this).addClass('playing');
					}
					
					tempAttr = imgTag.attr('src');
					imgTag.attr('src', imgTag.attr('data_url'));
					imgTag.attr('data_url', tempAttr);
				}
				//hotlinkOK gif tag found
				else if (typeof gifHotTag[0] != 'undefined') { 
				
					var imgTag = $(this).find('.gifImageContainer');
					var playIcon = $(this).find('.play-icon');
					var iframeTag = $(this).find('.hotlinkedImageIframe');
					var wrapperTag = $(this).find('.hotlinkedImageWrapper');
					
					if (playIcon.hasClass('element-invisible')) { //make it stop
						$(this).removeClass('playing');
						playIcon.removeClass('element-invisible');
						imgTag.removeClass('element-invisible');
						iframeTag.addClass('element-invisible');
						wrapperTag.addClass('element-invisible');
						iframeTag.attr('src', 'about:blank');
					}
					else { //make it play
						$(this).addClass('playing');
						playIcon.addClass('element-invisible');
						imgTag.addClass('element-invisible');
						iframeTag.removeClass('element-invisible');
						wrapperTag.removeClass('element-invisible');

						//tempAttr = imgTag.attr('src');
						iframeTag.attr('src', imgTag.attr('data_url'));
						//imgTag.attr('data_url', tempAttr);
					}
				
				}
				//iframe tag found
				else if (typeof iframeTag[0] != 'undefined') { 
					$(this).addClass('playing');
					iframeTag.removeClass('element-invisible').attr('src', iframeTag.attr('data-url'));
					$(this).off("click"); //kill click event
				} 
				//video and source tag found
				else if (typeof videoTag[0] != 'undefined') { 
					$(this).addClass('playing');
					var imgTag = $(this).find('img');
					var pTag = $(this).find('p');
				
					//$(this).find('.rawVideoClick').css("cssText", "position: relative; padding-bottom: unset; cursor: unset; background: unset;");
					imgTag.remove();
					pTag.css("cssText", "display: inline !important;");
					
					videoTag.removeClass('element-invisible');
					videoTag.find('.mp4source').attr('src', videoTag.find('.mp4source').attr('data-url'));
					videoTag.find('.webmsource').attr('src', videoTag.find('.webmsource').attr('data-url'));
					videoTag[0].load();
					$(this).off("click"); //kill click event
				}
			});
			// bugfix: force blank src if the user navigates back to cached pages.
			var iframe = mw.find('iframe');
			iframe.attr({ src: "about:blank",	});
			
			}); //Drupal JS run once End
			
			//AJAX functions:
			$('#edit-tid').ajaxComplete(function() {
				LBFF();
				newsImagePlacer();
				var elem_LB_PAT_WK = document.getElementsByClassName('view-advanced-forum-active-topics')[0];
				var elem_LB_PK_WK = document.getElementsByClassName('view-kedvencek')[0];
				var elem_LB_PRH_WK = document.getElementsByClassName('view-rovidhirek')[0];
				var elem_LB_PC_WK = document.getElementsByClassName('view-cikkek')[0];
					
				elem_LB_PAT_WK.id = "forum-table-990";
				elem_LB_PK_WK.id = "forum-table-991";
				elem_LB_PRH_WK.id = "forum-table-992";
				elem_LB_PC_WK.id = "forum-table-993";
				//$(this).remove();
			});
		}
	} //Drupal JS End
})(jQuery, Drupal, this, this.document);

// global JS functions:

	//vendor prefixed transformOrigin
	var transformProp = (function(){
		var testEl = document.createElement('div');

		if(testEl.style.transform == null) {
			var vendors = ['Webkit', 'Moz', 'ms'];

			for(var vendor in vendors) {
				if(testEl.style[ vendors[vendor] + 'Transform' ] !== undefined) {
					return vendors[vendor] + 'Transform';
				}
			}
		}
	return 'transform';
	})();

	//resize function
	function resize () {
		var text1 = document.getElementById('edit-comment-body-und-0-value');	
		
		if (!text1) {
			var text1 = document.getElementById('edit-field-profil-und-0-value');	
			if (!text1) {
				var text1 = document.getElementById('edit-body-value');	
				if (!text1) {
					var text1 = document.getElementById('edit-body-und-0-value');
				}
			}
		}
	
		var textmargin = text1.style.height;
		
		//if (parseInt(text1.style.height, 10) >= 86) { }
		text1.style.marginBottom = textmargin;
		text1.style.height = "0px";
		
		if (text1.scrollHeight >= 86) {
			text1.style.height = text1.scrollHeight +2 +'px';
		}
		else {
			text1.style.height = 86 +'px';
		}
		text1.style.marginBottom = 0;
	}

	// legfrissebb bejegyzesek filter fix function
	function LBFF() {

		advancedForumBejegyzesek = document.getElementsByClassName('pane-advanced-forum-active-topics')[0];
		if ( advancedForumBejegyzesek != null ) {
			var numberOfBejegyzesek = advancedForumBejegyzesek.getElementsByTagName('select')[0];
			var numberOfRovidhirek = document.getElementsByClassName('pane-rovidhirek')[0].getElementsByTagName('select')[0];
			var numberOfCikkek = document.getElementsByClassName('pane-cikkek')[0].getElementsByTagName('select')[0];
			var filterFieldBejegyzesek = advancedForumBejegyzesek.getElementsByClassName('form-autocomplete')[0];

			numberOfBejegyzesek.title = "Kimutatott bejegyzések száma";
			numberOfRovidhirek.title = "Kimutatott rövidhírek száma";
			numberOfCikkek.title = "Kimutatott cikkek száma";
			filterFieldBejegyzesek.title = "Szia! Kata vagyok. Vesszővel elválasztva helyezd szorosan belém a fórumneveket amiket ki szeretnél zárni a listázásból!";
		}
		
	}

	// news images placer
	function newsImagePlacer() {
		var shortNews = document.getElementsByClassName('view-id-rovidhirek')[0];
		var longArticles = document.getElementsByClassName('view-id-cikkek')[0];
		
		if ( shortNews != null && !isHidden(shortNews)) {
			var shortNewsNidFields = shortNews.getElementsByClassName('views-field-created');
			var shortNewsNothing1Fields = shortNews.getElementsByClassName('views-field-nothing-1');
		
			for (i=0;i<shortNewsNidFields.length;i++) {
				var nidInnerHTML = shortNewsNidFields[i].getElementsByTagName('span')[0].innerHTML;
			
				nidInnerHTML = nidInnerHTML.trim();
				
				//regex
				var datePart = /([0-9]+)/i.exec(nidInnerHTML);
				var nidPart = /-([0-9]+)/i.exec(nidInnerHTML);
				//nidPart = parseInt(nidPart);

				shortNewsNothing1Fields[i].style.backgroundImage = "url('/sites/default/files/newsimages/node-" + nidPart[1] + "-" + datePart[1] + ".jpg')";
				shortNewsNothing1Fields[i].style.backgroundSize = "cover";
				shortNewsNothing1Fields[i].style.backgroundPosition = "center";
			}
		}
		
		if ( longArticles != null && !isHidden(longArticles)) {
			var longArticlesNidFields = longArticles.getElementsByClassName('views-field-created');
			var longArticlesNothing1Fields = longArticles.getElementsByClassName('views-field-nothing-1');
		
			for (i=0;i<longArticlesNidFields.length;i++) {
				var nidInnerHTML = longArticlesNidFields[i].getElementsByTagName('span')[0].innerHTML;
				
				nidInnerHTML = nidInnerHTML.trim();
				
				//regex
				var datePart = /([0-9]+)/i.exec(nidInnerHTML);
				var nidPart = /-([0-9]+)/i.exec(nidInnerHTML);
								
				longArticlesNothing1Fields[i].style.backgroundImage = "url('/sites/default/files/articlesimages/node-" + nidPart[1] + "-" + datePart[1] + ".jpg')";
				longArticlesNothing1Fields[i].style.backgroundSize = "cover";
				longArticlesNothing1Fields[i].style.backgroundPosition = "center";
			}
		}
	}

	// legfrissebb bejegyzesek collapsible function
	function bejegyzesekcollapsible() {
		
		var activeTopics = document.getElementsByClassName('pane-advanced-forum-active-topics')[0];
		if ( activeTopics != null ) {
			var elem_LB_PAT_PT = activeTopics.getElementsByClassName('pane-title')[0];
			var elem_LB_PAT_WK = activeTopics.getElementsByClassName('pane-content')[0];
			var elem_LB_PAT_C = document.createElement("span");

			elem_LB_PAT_C.id = "forum-collapsible-990";
			elem_LB_PAT_C.className = "forum-collapsible";
			elem_LB_PAT_WK.id = "forum-table-990";
		
			elem_LB_PAT_PT.appendChild(elem_LB_PAT_C);
			
			var rewiredMarkAll = document.getElementsByClassName('rewired_mark_all')[0];
			if ( rewiredMarkAll != null ) {
				elem_LB_PAT_PT.appendChild(rewiredMarkAll);
			}
			
			var rovidHirek = document.getElementsByClassName('pane-rovidhirek')[0];
			if ( rovidHirek != null ) {
				var elem_LB_PRH_PT  = rovidHirek.getElementsByClassName('pane-title')[0];
				var elem_LB_PRH_WK = rovidHirek.getElementsByClassName('pane-content')[0];
				var elem_LB_PRH_C = document.createElement("span");
				
				elem_LB_PRH_C.id = "forum-collapsible-992";
				elem_LB_PRH_C.className = "forum-collapsible";
				elem_LB_PRH_WK.id = "forum-table-992";
		
				elem_LB_PRH_PT.appendChild(elem_LB_PRH_C);
			}
			
			var cikkek = document.getElementsByClassName('pane-cikkek')[0];
			if ( cikkek != null ) {
				var elem_LB_PC_PT  = cikkek.getElementsByClassName('pane-title')[0];
				var elem_LB_PC_WK = cikkek.getElementsByClassName('pane-content')[0];
				var elem_LB_PC_C = document.createElement("span");
				
				elem_LB_PC_C.id = "forum-collapsible-993";
				elem_LB_PC_C.className = "forum-collapsible";
				elem_LB_PC_WK.id = "forum-table-993";
		
				elem_LB_PC_PT.appendChild(elem_LB_PC_C);
			}
	
			var kedvencek = document.getElementsByClassName('pane-kedvencek')[0];
			if ( kedvencek != null ) {
				var elem_LB_PK_PT = kedvencek.getElementsByClassName('pane-title')[0];
				var elem_LB_PK_WK = kedvencek.getElementsByClassName('pane-content')[0];
				var elem_LB_PK_C = document.createElement("span");
		
				elem_LB_PK_C.id = "forum-collapsible-991";
				elem_LB_PK_C.className = "forum-collapsible";
				elem_LB_PK_WK.id = "forum-table-991";
		
				elem_LB_PK_PT.appendChild(elem_LB_PK_C);
			}
			
			var clientWidth = document.getElementById('main-wrapper').clientWidth;
			if (Drupal.settings.hdm == 1 && clientWidth > 1500) {
				fooldalHDModeOn ();
			}
		}
	}
	
	//relocate elements for HD mode ON
	function fooldalHDModeOn () {
		var activeTopics = document.getElementsByClassName('pane-advanced-forum-active-topics')[0];
		
		if ( activeTopics != null ) {
			var rovidHirek = document.getElementsByClassName('pane-rovidhirek')[0];
			var cikkek = document.getElementsByClassName('pane-cikkek')[0];
			var mainId = document.getElementById('main');
			var contentId = document.getElementById('content');

			mainId.appendChild(rovidHirek);
			mainId.appendChild(contentId);
			mainId.appendChild(cikkek);
		}
	}
	
	//relocate elements for HD mode OFF
	function fooldalHDModeOff () {
		var paneHirekContainer = document.getElementsByClassName('panels-flexible-region-1-rovidhirek_1-inside')[0];
		if ( paneHirekContainer == null ) var paneHirekContainer = document.getElementsByClassName('panels-flexible-region-2-rovidhirek-inside')[0];
		
		var paneCikkekContainer = document.getElementsByClassName('panels-flexible-region-1-hirek_1-inside')[0];
		if (paneCikkekContainer == null ) var paneCikkekContainer = document.getElementsByClassName('panels-flexible-region-2-nagyhirek-inside')[0];
		
		if ( paneHirekContainer != null ) {
				var rovidHirek = document.getElementsByClassName('pane-rovidhirek')[0];
				paneHirekContainer.appendChild(rovidHirek);
		}
		
		if ( paneCikkekContainer != null ) {
				var cikkek = document.getElementsByClassName('pane-cikkek')[0];
				paneCikkekContainer.appendChild(cikkek);
		}
	}
	
	// background rendering function  
	function renderbackground() {
		var clientHeight = document.getElementById('main-wrapper').clientHeight;
		var clientWidth = document.getElementById('main-wrapper').clientWidth;
		var backgroundsrc = new Image();

		backgroundsrc.src = '/sites/all/files/bg.jpg';
		backgroundsrc.width = 1920;
		backgroundsrc.height = 1279;

		var Ycount=0;	
		var oddXcount=0;
		var evenXcount=0;
	    var extraXbuffer=0; 
		var extraYbuffer=2;

	    if (clientHeight <= backgroundsrc.height) {
			backgroundsrc.height = clientHeight;
		}

		for (Ycount=0; Ycount<=(clientHeight/backgroundsrc.height+extraYbuffer);Ycount++) {
			var div=document.createElement("div");
			document.getElementById('main-wrapper-background').appendChild(div);
			div.id="background-row";
			div.style.height = backgroundsrc.height + "px";
			
			if(Ycount & 1) {
				for (oddXcount=0; oddXcount<=(clientWidth/backgroundsrc.width+extraXbuffer);oddXcount++) {
					if (oddXcount & 1) {
						var img=document.createElement("IMG");
						document.getElementById('main-wrapper-background').lastChild.appendChild(img);
						img.src = backgroundsrc.src;
						img.className="background-xflip";
					}
					else {
						var img=document.createElement("IMG");
						document.getElementById('main-wrapper-background').lastChild.appendChild(img);
						img.src = backgroundsrc.src;
						img.className="background-normal";
					}
				}
			}
			else {		
				for (evenXcount=0; evenXcount<=(clientWidth/backgroundsrc.width+extraXbuffer);evenXcount++) {
					if(evenXcount & 1) {
						var img=document.createElement("IMG");
						document.getElementById('main-wrapper-background').lastChild.appendChild(img);
						img.src = backgroundsrc.src;
						img.className="background-xyflip";
					}
					else {
						var img=document.createElement("IMG");
						document.getElementById('main-wrapper-background').lastChild.appendChild(img);
						img.src = backgroundsrc.src;
						img.className="background-yflip";
					}
				}
			}
		}
	}
	
	// pager float fix function - bottom right of breadcrumb
	function pagerfloatfix() {
		var pager = document.getElementsByClassName('rewired-breadcrumb-pager')[0];
		var breadcrumb = document.getElementsByClassName('breadcrumb')[0];

		if (breadcrumb){
			breadcrumb.appendChild(pager);
		}
		else {
			var breadcrumb = document.getElementsByClassName('rewired-breadcrumb-container')[0];
			breadcrumb.appendChild(pager);
		}
	}
	
	// drupal hash jump position fix function
	function drupalhashjumpfix() {
		var commentoffset = 28; //28

		var elems4 = document.getElementById("content-gap");
		var crumbHeight = document.getElementsByClassName('rewired-breadcrumb-container')[0].offsetHeight || "28px"; 
	
		elems4.style.paddingTop = commentoffset + crumbHeight + "px";
	
		if (document.getElementById("forum-comments") != null ) {
			var elems1 = document.getElementById("forum-comments").querySelectorAll('a[id^="comment-"]');
			var elems2 = document.getElementById("forum-comments").querySelectorAll('div[id^="post-"]');
		
		for (var i = 0; i < elems1.length; i++) {

				var sp1 = document.createElement("div");
				sp1.id = elems1[i].id;
				sp1.className = elems1[i].id;
				
				var sp2 = elems2[i];
				var parentDiv = sp2.parentNode;
				parentDiv.insertBefore(sp1, sp2);
				
				var element = elems1[i];
				element.parentNode.removeChild(element);
	
				sp1.style.visibility = "hidden";
				sp1.style.marginTop = (Math.abs(commentoffset) * -1) - crumbHeight + "px";
				sp1.style.height = commentoffset + crumbHeight + "px";
			}
			
			if (document.getElementById("new") != null) {
				var elems3 = document.getElementById("new");
				var sp3 = document.createElement("div");

				sp3.id = elems3.id;
				elems3.parentNode.insertBefore(sp3, elems3.nextSibling);
				document.getElementById("new").parentNode.removeChild(document.getElementById("new"));
	
				sp3.style.visibility = "hidden";
				sp3.style.marginTop = (Math.abs(commentoffset) * -1) - crumbHeight + "px";
				sp3.style.height = commentoffset + crumbHeight + "px";
			}
		}
		
		// singleline and multiline text position on the floating bar
		var breadcrumbWidth = document.getElementsByClassName('rewired-breadcrumb-container')[0];
		var breadcrumbHeight = document.getElementsByClassName('breadcrumb')[0];
		
		if (breadcrumbHeight != null) breadcrumbHeight = breadcrumbHeight.offsetHeight;
		if (breadcrumbWidth != null) breadcrumbWidth = breadcrumbWidth.offsetWidth + 17;

		//console.log(clientWidth);
		//console.log(breadcrumbHeight);
		
		if (breadcrumbHeight <= 29) {
			document.body.classList.add('single-line');
		}
	}
	
	function checkIng() {
		return false;
	}
	
	
	// Rewired Settings Menu
	function rewiredsettings() {
		rs_settings = document.getElementById('rewired_settings');
		rs_settingsWrap = document.getElementById('rewired_settings_wrapper');
		rs_settings.style.display = "flex";
		rs_settingsWrap.style.display = "flex";
		rs_gfx = document.getElementsByName('gfx');
		rs_mfm = document.getElementsByName('mfm');
		rs_aie = document.getElementsByName('aie');
		rs_pke = document.getElementsByName('pke');
		rs_ape = document.getElementsByName('ape');
		rs_ult = document.getElementsByName('ult');
		rs_sze = document.getElementsByName('sze');
		rs_hdm = document.getElementsByName('hdm');
		
		rewired_cookie_values = getCookie(Drupal.settings.rewiredcookiename);
		rewired_cookie_values = decodeCookie(rewired_cookie_values);
		
		rs_gfx[(rewired_cookie_values.gfx-1)].checked = true;
		rs_mfm[(rewired_cookie_values.mfm-1)].checked = true;
		if (rewired_cookie_values.aie != 0) rs_aie[0].checked = true;
		if (rewired_cookie_values.pke != 0) rs_pke[0].checked = true;
		if (rewired_cookie_values.ape != 0) rs_ape[0].checked = true;
		if (rewired_cookie_values.ult != 0) rs_ult[0].checked = true;
		if (rewired_cookie_values.hdm != 0) rs_hdm[0].checked = true;
		rs_sze[0].value = rewired_cookie_values.sze;
		document.getElementById('sze_output').innerHTML = rewired_cookie_values.sze;
		
		setTimeout(openRewiredsettings, 100); //some delay hack for slide in animation
		
		listener = function (event) {
			if (event.target.classList.contains('rs_element') == false) {
				document.body.classList.remove('beallitasok');
				 closeRewiredsettings = setTimeout(function(){
					 rs_settings.style.display = 'none';
					 rs_settingsWrap.style.display = "none";
					document.removeEventListener('click', listener, false);
					 }, 600);
			}
			else clearTimeout(closeRewiredsettings);
		};

		document.addEventListener('click', listener, false);
		
		function closeRewiredsettings() {
			rs_settings.style.display = 'none';
			document.removeEventListener('click', listener, false);
		}

		function openRewiredsettings() {
			document.body.classList.add('beallitasok');
		}
	}
	
	function updateOutput(event) {
		rewired_cookie_values = getCookie(Drupal.settings.rewiredcookiename);
		rewired_cookie_values = decodeCookie(rewired_cookie_values);
		
		if (event.target.type == 'radio') {
			switch (event.target.name + event.target.value) {
				case "gfx1": 
					document.body.classList.remove('graph_high');
					document.body.classList.add('graph_low');
				break;
				case "gfx2": 
					document.body.classList.remove('graph_high');
					document.body.classList.remove('graph_low');
				break;
				case "gfx3": 
					document.body.classList.remove('graph_low');
					document.body.classList.add('graph_high');
				break;
				case "mfm1": 
					document.body.classList.remove('font_medium');
					document.body.classList.add('font_small');
				break;
				case "mfm2": 
					document.body.classList.remove('font_small');
					document.body.classList.add('font_medium');
				break;
				case "mfm3": 
					document.body.classList.remove('font_small');
					document.body.classList.remove('font_medium');
				break;
			}
				updateCookie(event.target.name, event.target.value);			
		}
		else if (event.target.type == 'checkbox') {
			switch (event.target.name + event.target.checked) {
				case "aietrue": 
					document.body.classList.add('hideas');
				break;
				case "aiefalse": 
					document.body.classList.remove('hideas');
				break;
				case "pketrue": 
					document.body.classList.add('hidepk');
				break;
				case "pkefalse": 
					document.body.classList.remove('hidepk');
				break;
				case "ulttrue": 
					document.body.classList.add('ultramobil');
				break;
				case "ultfalse": 
					document.body.classList.remove('ultramobil');
				break;
				case "hdmtrue": 
					document.body.classList.add('hdmode');
					fooldalHDModeOn ();
				break;
				case "hdmfalse": 
					document.body.classList.remove('hdmode');
					fooldalHDModeOff ();
				break;
			}
	
			updateCookie(event.target.name, event.target.checked);
		}
		else if (event.target.type == 'range') {
			document.getElementById('sze_output').innerHTML = event.target.value;
			
			updateCookie(event.target.name, event.target.value);
		}
		else if (event.target.type == 'button') {
			switch (event.target.name) {
			case "fpb": 
				uid = Drupal.settings.loggeduserid;
			
				if (uid !== 0) {
					document.location.href = "http://" + window.location.hostname + "/user/" + uid + "/edit";
				}
				else {
					document.location.href = "http://" + window.location.hostname + "/user/login";
				}
			break;
			case "fpk": 
				document.location.href = "http://" + window.location.hostname + "/user/logout";
			break;
			}
		}
		
	}

	function updateCookie(ename,evalue) {
		rewired_cookie_values = getCookie(Drupal.settings.rewiredcookiename);
		rewired_cookie_values = decodeCookie(rewired_cookie_values);
		
		if (evalue === true || evalue === false) evalue = evalue & 1; //if bool then convert to int
		evalue = parseInt(evalue); //converts any string value to integer
		
		rewired_cookie_values[ename] = evalue;
		
		rewired_cookie_values = encodeCookie(rewired_cookie_values);
		setCookie(Drupal.settings.rewiredcookiename, rewired_cookie_values, '365');
	}

	function getCookie(cname) {
		var name = cname + "=";
		var ca = document.cookie.split(';');
		for(var i=0; i<ca.length; i++) {
			var c = ca[i];
			while (c.charAt(0)==' ') c = c.substring(1);
			if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
		}
		return "";
	}
	
	function setCookie(cname, cvalue, exdays) {
		var d = new Date();
		d.setTime(d.getTime() + (exdays*24*60*60*1000));
		var expires = "expires="+d.toUTCString();
		document.cookie = cname + "=" + cvalue + "; " + expires + "; path=/";
	}
	
	function encodeCookie(cvalue) {
		cvalue = JSON.stringify(cvalue);
		cvalue = encodeURIComponent(cvalue);
		return cvalue;
	}
	
	function decodeCookie(cvalue) {
		cvalue = decodeURIComponent(cvalue);
		cvalue = JSON.parse(cvalue);
		
		keys = Object.keys(cvalue); 
		for(var i=0; i<keys.length; i++) {
			cvalue[keys[i]] = parseInt(cvalue[keys[i]]); //converts any string value to integer
		}

		return cvalue;
	}
	
	//pictureviewer init
	function pictureviewerinit() {
		
		function merge(target, source) { // Merges two (or more) objects, giving the last one precedence
			if ( typeof target !== 'object' ) {
				target = {};
			}
    
			for (var property in source) {
				if ( source.hasOwnProperty(property) ) {
					var sourceProperty = source[ property ];
					if ( typeof sourceProperty === 'object' ) { //object
						target[ property ] = merge( target[ property ], sourceProperty );
						continue;
					}
					target[ property ] = sourceProperty;
				}
			}
    
			for (var a = 2, l = arguments.length; a < l; a++) {
				merge(target, arguments[a]);
			}
    
			return target;
		};

		var kepekfield = document.getElementsByClassName('rtecenter'); //.field-items
		var kepekfield2 = document.getElementsByClassName('field-type-image');
		var nodeForumForm = document.getElementsByClassName('node-forum-form')[0];
		
	    kepekfield = [].concat(Array.prototype.slice.call(kepekfield), Array.prototype.slice.call(kepekfield2));
	
		if ( kepekfield != null && nodeForumForm == null ) {
			var kepek = new Array();
			var pictureviewerDiv = document.getElementById('picture_viewer');
			var pictureviewerPrev = document.getElementById('picture_viewer_prev');
			var pictureviewerNext = document.getElementById('picture_viewer_next');
			var pictureviewerLoading = document.getElementById('picture_viewer_loading');
			var pictureviewerText = document.getElementById('picture_viewer_text');
			var k1 = 0;
			var thumbIMG = {};	
			
			for (var i = 0; i < kepekfield.length; i++) {
				if (typeof kepekfield[i].getElementsByTagName('a')[0] != 'undefined' ) {

					for (var k = 0; k < kepekfield[i].getElementsByTagName('a').length; k++) {
						result = (/\.(?=gif|jpg|png|jpeg|gif|bmp)/gi).test(kepekfield[i].getElementsByTagName('a')[k].getAttribute('href'));
						if (result) {
							NodeFullTitle = kepekfield[i].parentNode.parentNode.parentNode.parentNode.parentNode.getElementsByClassName('forum-post-title')[0];
							if (NodeFullTitle) {
								NodeFullTitle = NodeFullTitle.innerHTML;
							}
							else {
								NodeTeaserTitle = kepekfield[i].parentNode.parentNode.getElementsByTagName('h2')[0]; //new linking style
								if (typeof NodeTeaserTitle == "object") {
									NodeTeaserTitle =  NodeTeaserTitle.getElementsByTagName('a')[0].innerHTML;
								}
								else {
									NodeTeaserTitle = kepekfield[i].parentNode.parentNode.parentNode.parentNode.parentNode.getElementsByTagName('h2')[0]; //old linking style
									if (typeof NodeTeaserTitle == "object") NodeTeaserTitle =  NodeTeaserTitle.getElementsByTagName('a')[0].innerHTML;
								}
							}
							
							Title = NodeFullTitle || NodeTeaserTitle || '';
							Title = Title.trim();
							Title = Title.replace(/['"]+/g, '');
							dataurl = {'t':Title};
							
							listenerOpenPictureviewer = function (event) {
								event.preventDefault();
								pictureviewerEventsDestroy();
								pictureviewerDiv.style.display = 'block';
								setTimeout(openPictureviewer, 100); //some delay hack for slide in animation
								pictureviewer(this,kepek);
							};
							
							kepek.push(kepekfield[i].getElementsByTagName('a')[k]);

							dataurl.p = k1+1;
							dataurl = JSON.stringify(dataurl);
							kepek[k1].setAttribute('data-url', dataurl);
							kepek[k1].addEventListener('click', listenerOpenPictureviewer, false);
							kepek[k1].classList.add('pv_element');
							kepekIMG = kepek[k1].getElementsByTagName('img')[0];
							if (typeof kepekIMG != 'undefined') kepekIMG.classList.add('pv_element');
							
							k1++;
						}
					}
				}
			}

			pictureviewerMouseHandler = function (event) {
				if (event.target.classList.contains('pv_element') == false || event.keyCode == 27) {
					document.body.classList.remove('pictureviewer');
					pictureviewerEventsDestroy();
					closePictureviewer = setTimeout(function(){
						pictureviewerDiv.style.display = 'none';
						document.removeEventListener('click', pictureviewerMouseHandler, false);
					},600);
				}
				else if (event.target.classList.contains('thumbIMG') == true) {
					for (var j = 0; j < kepek.length; j++) {
						thumbIMG[j].classList.remove('selected');
					}
					position = event.target.getAttribute('data-url');
					position = JSON.parse(position);
					position.p = parseInt(position.p);
					event.target.classList.remove('selected');

					pictureSwapper(kepek,position,bufferedImg,thumbIMG);
				}
				else if (event.target.id == "picture_viewer_next") {	listenerNext();}
				else if (event.target.id == "picture_viewer_prev") {	listenerPrev();}
				else if (typeof(closePictureviewer) == "number") clearTimeout(closePictureviewer);
				
				
			};
			
			function openPictureviewer() {
				document.body.classList.add('pictureviewer');
				document.addEventListener('click', pictureviewerMouseHandler, false);
				window.addEventListener('keydown', pictureviewerKeyboardHandler, false);
			}
			
			function pictureviewerEventsDestroy() {
				//destroy events to avoid duplicates
				if (typeof(pictureviewerMouseHandler) == "function") window.removeEventListener('click', pictureviewerMouseHandler, false); 
				if (typeof(pictureviewerKeyboardHandler) == "function") window.removeEventListener('keydown', pictureviewerKeyboardHandler, false);
			};
			
			function pictureviewerKeyboardHandler(e) {
				if(e.keyCode == 39 || e.keyCode == 32){	listenerNext(); } //ArrowRight and Space
				if(e.keyCode == 37){	listenerPrev();	} //ArrowLeft
				if(e.keyCode == 27){ pictureviewerMouseHandler(e); } //Escape
			};
			
			function pictureviewer(e,k) {
				pictureviewerText.innerHTML = '';
				startingImage = e.getAttribute('href');
				position = e.getAttribute('data-url');
				position = JSON.parse(position);
				position.p = parseInt(position.p);
				pictureviewerLoading.style.display = "block"; //throbber ON
				pictureviewerText.innerHTML = position.p + '/' + kepek.length + ' ' + (position.t || '');
				
				listenerPrev = function (event) { 
					thumbIMG[position.p-1].classList.remove('selected');
					position.p--;
					pictureSwapper(k,position,bufferedImg,thumbIMG); 
				};
				listenerNext = function (event) { 
					thumbIMG[position.p-1].classList.remove('selected'); 
					position.p++; 
					pictureSwapper(k,position,bufferedImg,thumbIMG); 
				};
				
				var bufferPosition = position.p - 1;
				var bufferPositionCounter = 0;
				var clientHeight = document.getElementById('main-wrapper').clientHeight;
				var clientWidth = document.getElementById('main-wrapper').clientWidth;
				
				if (typeof thumbIMGcontainer == 'undefined') {
					thumbIMGcontainer = document.createElement("div");
					thumbIMGcontainer.className = "thumbIMGcontainer";
					pictureviewerDiv.appendChild(thumbIMGcontainer);

					for (var j = 0; j < k.length; j++) {
						dataurl = {};
						dataurl.p = j + 1;
						dataurl = JSON.stringify(dataurl);
						thumbIMG[j] = document.createElement("div");
					
						thumbIMG[j].setAttribute('data-url', dataurl);
						thumbIMG[j].style.width = (clientWidth / k.length - 6 - (1 / k.length) ) + 'px';
						thumbIMG[j].className = "thumbIMG";
						thumbIMG[j].classList.add('pv_element');
						thumbIMGcontainer.appendChild(thumbIMG[j]);
					}
				}
				else {
					for (var j = 0; j < kepek.length; j++) {
						thumbIMG[j].classList.remove('selected');
					}
				}

				bufferedImg = {};
				pictureBuffer(k, position, bufferedImg, thumbIMG, bufferPosition, bufferPositionCounter);
			}
			
			function pictureBuffer(k, position, bufferedImg, thumbIMG, bufferPosition, bufferPositionCounter) {
				if (bufferPositionCounter - 1 < k.length ) {
					if (bufferPosition + 1 > k.length) {
						//console.log('buffer restart');
						bufferPosition = 0;
						pictureBuffer(k, position, bufferedImg, thumbIMG, bufferPosition, bufferPositionCounter);
					}
					else {
						bufferedImg[bufferPosition] = new Image();
						bufferedImg[bufferPosition].src = k[bufferPosition];
						bufferedImg[bufferPosition].onload = function() {
							if (position.p -1 == bufferPosition) {
								pictureSwapper(k,position,bufferedImg,thumbIMG);
							}
							thumbIMG[bufferPosition].style.backgroundImage = "url('" + bufferedImg[bufferPosition].src + "')";
							thumbIMG[bufferPosition].classList.add('loaded');
							bufferPosition++;
							bufferPositionCounter++;
							pictureBuffer(k, position, bufferedImg, thumbIMG, bufferPosition, bufferPositionCounter);
						}
					}
				}
			}
			
			function pictureSwapper(k,position,bufferedImg,thumbIMG) {
				if (position.p > k.length) position.p = 1;
				if (position.p < 1) position.p = k.length;
						
				pictureviewerLoading.style.display = "block"; //throbber ON
					
				if (typeof bufferedImg[(position.p - 1)] != 'undefined') {
					pictureviewerDiv.getElementsByTagName('img')[0].src = bufferedImg[(position.p - 1)].src;
						
					pictureviewerLoading.style.display = "none"; //throbber OFF
					t = k[position.p-1].getAttribute('data-url');
					t = JSON.parse(t);
					pictureviewerText.innerHTML = position.p + '/' + kepek.length + ' ' + (t.t || '');
					thumbIMG[position.p-1].classList.add('selected');
				}
			}
		}
	}
	
	//Where el is the DOM element you'd like to test for visibility
	function isHidden(el) {
		return (el.offsetParent === null)
	}
	