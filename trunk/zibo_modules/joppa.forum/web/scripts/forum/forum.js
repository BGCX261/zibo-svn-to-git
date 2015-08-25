function forumImagesResize() {
	width = 500;
	height = 500;
	$('#forum div.post div.message div.text img').each(function() {
//		alert(this.width + "; " + width + "; " + this.height + "; " + height);
		
		if (this.width > width || this.height > height) {
			var newWidth = width;			
			if (this.height / (this.width / width) > height) {
				newWidth = this.width / (this.height/ height);
			}
			var oldWidth = this.width;
			this.width = newWidth;

			var ratio = 100 - parseInt(newWidth / oldWidth * 100);

			var linkTag = "<a href=\"" + this.src + "\">"; 
			var link = linkTag + "This image is reduced by " + ratio + "%</a><br />";

			$(this).before(link);
			$(this).wrap(linkTag + "</a>");
		}
	});
}

(function($){$.fn.onImagesLoad=function(options){var self=this;self.opts=$.extend({},$.fn.onImagesLoad.defaults,options);self.bindEvents=function($imgs,container,callback){if($imgs.length===0){if(self.opts.callbackIfNoImagesExist&&callback){callback(container);}}else{var loadedImages=[];if(!$imgs.jquery){$imgs=$($imgs);}$imgs.each(function(i,val){$(this).bind('load',function(){if(jQuery.inArray(i,loadedImages)<0){loadedImages.push(i);if(loadedImages.length==$imgs.length){if(callback){callback(container);}}}}).each(function(){if(this.complete||this.complete===undefined){this.src=this.src;}});});}};var imgAry=[];self.each(function(){if(self.opts.itemCallback){var $imgs;if(this.tagName=="IMG"){$imgs=this;}else{$imgs=$('img',this);}self.bindEvents($imgs,this,self.opts.itemCallback);}if(self.opts.selectorCallback){if(this.tagName=="IMG"){imgAry.push(this);}else{$('img',this).each(function(){imgAry.push(this);});}}});if(self.opts.selectorCallback){self.bindEvents(imgAry,this,self.opts.selectorCallback);}return self.each(function(){});};$.fn.onImagesLoad.defaults={selectorCallback:null,itemCallback:null,callbackIfNoImagesExist:false};})(jQuery);