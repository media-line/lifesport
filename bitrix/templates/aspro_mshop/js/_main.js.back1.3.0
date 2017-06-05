var basketTimeoutSlide;
var resizeEventTimer;

var funcDefined = function(func){
	try {
		if (typeof func == 'function') {
			return true;
		} else {
			return typeof window[func] === "function";
		}
	} catch (e) {
		return false;
	}
}

if(!funcDefined('setLocationSKU')){
	function setLocationSKU(ID){
		var objUrl = parseUrlQuery(),
			j = 0,
			prefix = '',
			query_string = '',
			url = '';
		if('oid' in objUrl)
		{
			if(parseInt(objUrl.oid)>0)
			{
				objUrl.oid = ID;
				for(var i in objUrl)
				{
					if(parseInt(j)>0)
						prefix = '&';
					query_string = query_string + prefix + i + '='+ objUrl[i];
					j++;
				}
				if(query_string)
				{
					url = location.pathname+'?'+query_string;
				}
				try {
					history.pushState(null, null, url);
					return;
				} catch(e) {}
				location.hash = '#' + url.substr(1)
			}
		}
	}
}

if(!funcDefined('trimPrice')){
	var trimPrice = function trimPrice(s){
		s=s.split(" ").join("");
		s=s.split("&nbsp;").join("");
		return s;
	}
}

if(!funcDefined('markProductRemoveBasket')){
	var markProductRemoveBasket = function markProductRemoveBasket(id){
		$('.in-cart[data-item='+id+']').hide();
		$('.to-cart[data-item='+id+']').show();
		$('.to-cart[data-item='+id+']').closest('.button_block').removeClass('wide');
		$('.to-cart[data-item='+id+']').closest('.counter_wrapp').find('.counter_block').show();
		$('.counter_block[data-item='+id+']').show();
		$('.in-subscribe[data-item='+id+']').hide();
		$('.to-subscribe[data-item='+id+']').show();
		$('.wish_item[data-item='+id+']').removeClass("added");
		$('.wish_item[data-item='+id+'] .value:not(.added)').show();
		$('.wish_item[data-item='+id+'] .value.added').hide();
	}
}

if(!funcDefined('markProductAddBasket')){
	var markProductAddBasket = function markProductAddBasket(id){
		$('.to-cart[data-item='+id+']').hide();
		$('.to-cart[data-item='+id+']').closest('.counter_wrapp').find('.counter_block').hide();
		$('.to-cart[data-item='+id+']').closest('.button_block').addClass('wide');
		$('.in-cart[data-item='+id+']').show();
		$('.wish_item[data-item='+id+']').removeClass("added");
		$('.wish_item[data-item='+id+'] .value:not(.added)').show();
		$('.wish_item[data-item='+id+'] .value.added').hide();
	}
}

if(!funcDefined('markProductDelay')){
	var markProductDelay = function markProductDelay(id){
		$('.in-cart[data-item='+id+']').hide();
		$('.to-cart[data-item='+id+']').show();
		$('.to-cart[data-item='+id+']').closest('.counter_wrapp').find('.counter_block').show();
		$('.to-cart[data-item='+id+']').closest('.button_block').removeClass('wide');
		$('.wish_item[data-item='+id+']').addClass("added");
		$('.wish_item[data-item='+id+'] .value:not(.added)').hide();
		// $('.wish_item[data-item='+id+'] .value.added').show();
		$('.wish_item[data-item='+id+'] .value.added').css('display','inline-block');
	}
}

if(!funcDefined('markProductSubscribe')){
	var markProductSubscribe = function markProductSubscribe(id){
		$('.to-subscribe[data-item='+id+']').hide();
		$('.in-subscribe[data-item='+id+']').css('display','block');
	}
}

if(!funcDefined('basketFly')){
	var basketFly = function basketFly(action){
		$.post( arMShopOptions['SITE_DIR']+"ajax/reload_basket_fly.php", "PARAMS="+$("#basket_form").find("input#fly_basket_params").val(), $.proxy(function( data ){
			var small=$('.opener .basket_count').hasClass('small'),
				basket_count=$(data).find('.basket_count').find('.items div').text();
			$('#basket_line .basket_fly').html(data);
			if(parseInt(basket_count)){
				$('#basket_line .basket_fly').removeClass('basket_empty');
			}else{
				$('#basket_line .basket_fly').addClass('basket_empty');
			}

			if (action=='open') {
				if(small){
					if(arMShopOptions['THEME']['SHOW_BASKET_ONADDTOCART'] !== 'N'){
						$('.opener .basket_count').click();
					}
				}else{
					$('.opener .basket_count').removeClass('small')
					$('.tabs_content.basket li[item-section="AnDelCanBuy"]').addClass('cur');
					$('#basket_line ul.tabs li[item-section="AnDelCanBuy"]').addClass('cur');
				}
			} else if (action=='wish') {
				if(small){
					if(arMShopOptions['THEME']['SHOW_BASKET_ONADDTOCART'] !== 'N'){
						$('.opener .wish_count').click();
					}
				}else{
					$('.opener .wish_count').removeClass('small')
					$('.tabs_content.basket li[item-section="DelDelCanBuy"]').addClass('cur');
					$('#basket_line ul.tabs li[item-section="DelDelCanBuy"]').addClass('cur');
				}
			} else {
				if(arMShopOptions['THEME']['SHOW_BASKET_ONADDTOCART'] !== 'N'){
					$('.opener .basket_count').click();
				}
			}

		}));
	}
}



if(!funcDefined('initSelects')){
	function initSelects(target){
		var iOS = ( navigator.userAgent.match(/(iPad|iPhone|iPod)/g) ? true : false );
		if ( iOS ) return;
		if($("#bx-soa-order").length)
			return;
		// SELECT STYLING
		$(target).find('.wrapper select:visible').ikSelect({
			syntax: '<div class="ik_select_link"> \
						<span class="ik_select_link_text"></span> \
						<div class="trigger"></div> \
					</div> \
					<div class="ik_select_dropdown"> \
						<div class="ik_select_list"> \
						</div> \
					</div>',
			dynamicWidth: true,
			ddMaxHeight: 112,
			customClass: 'common_select',
			//equalWidths: true,
			onShow: function(inst){
				inst.$dropdown.css('top', (parseFloat(inst.$dropdown.css('top'))-5)+'px');
				if ( inst.$dropdown.outerWidth() < inst.$link.outerWidth() ){
					inst.$dropdown.css('width', inst.$link.outerWidth());
				}
				if ( inst.$dropdown.outerWidth() > inst.$link.outerWidth() ){
					inst.$dropdown.css('width', inst.$link.outerWidth());
				}
				var count=0,
					client_height=0;
				inst.$dropdown.css('left', inst.$link.offset().left);
				$(inst.$listInnerUl).find('li').each(function(){
					if(!$(this).hasClass('ik_select_option_disabled')){
						++count;
						client_height+=$(this).outerHeight();
					}
				})
				if(client_height<112){
					inst.$listInner.css('height', 'auto');
				}else{
					inst.$listInner.css('height', '112px');
				}
				inst.$link.addClass('opened');
				inst.$listInner.addClass('scroller');
			},
			onHide: function(inst){
				inst.$link.removeClass('opened');
			}
		});
		// END OF SELECT STYLING

		var timeout;
		$(window).on('resize', function(){
			clearTimeout(timeout);
			timeout = setTimeout(function(){
				//$('select:visible').ikSelect('redraw');
				var inst='';
				if(inst=$('.common_select-link.opened + select').ikSelect().data('plugin_ikSelect')){
					inst.$dropdown.css('left', inst.$link.offset().left+'px');
				}
			}, 20);
		});
	}
}

if(!funcDefined('initHoverBlock')){
	function initHoverBlock(target){
		$(target).find('.catalog_item.item_wrap').on('mouseenter', function(){
			$(this).addClass('hover');
		})
		$(target).find('.catalog_item.item_wrap').on('mouseleave', function(){
			$(this).removeClass('hover');
		})
	}
}

if(!funcDefined('setStatusButton')){
	function setStatusButton(){
		$.ajax({
			url: arMShopOptions["SITE_DIR"]+'ajax/get_basket_count.php',
			type: 'POST',
			success: function(data){
				if(data.ITEMS || data.DELAY_ITEMS || data.SUBSCRIBE_ITEMS) {
					if(data.ITEMS){
						for( var i in data.ITEMS ){
							$('.to-cart[data-item='+data.ITEMS[i].PRODUCT_ID+']').hide();
							$('.counter_block[data-item='+data.ITEMS[i].PRODUCT_ID+']').hide();
							$('.in-cart[data-item='+data.ITEMS[i].PRODUCT_ID+']').show();
							$('.in-cart[data-item='+data.ITEMS[i].PRODUCT_ID+']').closest('.button_block').addClass('wide');
						}
					}
					if(data.DELAY_ITEMS){
						for( var i in data.DELAY_ITEMS ){
							$('.wish_item.to[data-item='+data.DELAY_ITEMS[i].PRODUCT_ID+']').hide();
							$('.wish_item.in[data-item='+data.DELAY_ITEMS[i].PRODUCT_ID+']').show();
							if ($('.wish_item[data-item='+data.DELAY_ITEMS[i].PRODUCT_ID+']').find(".value.added").length) {
								$('.wish_item[data-item='+data.DELAY_ITEMS[i].PRODUCT_ID+']').addClass("added");
								$('.wish_item[data-item='+data.DELAY_ITEMS[i].PRODUCT_ID+']').find(".value").hide();
								$('.wish_item[data-item='+data.DELAY_ITEMS[i].PRODUCT_ID+']').find(".value.added").show();
							}
						}
					}
					if(data.SUBSCRIBE_ITEMS){
						for( var i in data.SUBSCRIBE_ITEMS ){
							$('.to-subscribe[data-item='+data.SUBSCRIBE_ITEMS[i].PRODUCT_ID+']').hide();
							$('.in-subscribe[data-item='+data.SUBSCRIBE_ITEMS[i].PRODUCT_ID+']').show();
						}
					}
				}
			}
		});
		$.ajax({
			url: arMShopOptions["SITE_DIR"]+'ajax/get_compare_count.php',
			type: 'POST',
			success: function(data){
				if(data.ITEMS) {
					if(data.ITEMS){
						for( var i in data.ITEMS ){
							$('.compare_item.to[data-item='+data.ITEMS[i]+']').hide();
							$('.compare_item.in[data-item='+data.ITEMS[i]+']').show();
							if ($('.compare_item[data-item='+data.ITEMS[i]+']').find(".value.added").length){
								$('.compare_item[data-item='+data.ITEMS[i]+']').find(".value").hide();
								$('.compare_item[data-item='+data.ITEMS[i]+']').find(".value.added").show();
							}
						}
					}
				}
			}
		})
	}
}

/*countdown start*/
if(!funcDefined('initCountdown')){
	var initCountdown = function initCountdown(){
		if( $('.view_sale_block').size() ){
			$('.view_sale_block').each(function(){
				var activeTo=$(this).find('.active_to').text(),
					dateTo= new Date(activeTo.replace(/(\d+)\.(\d+)\.(\d+)/, '$3/$2/$1'));
				$(this).find('.countdown').countdown({until: dateTo, format: 'dHMS', padZeroes: true, layout: '{d<}<span class="days item">{dnn}<div class="text">{dl}</div></span>{d>} <span class="hours item">{hnn}<div class="text">{hl}</div></span> <span class="minutes item">{mnn}<div class="text">{ml}</div></span> <span class="sec item">{snn}<div class="text">{sl}</div></span>'}, $.countdown.regionalOptions['ru']);
			})
		}
	}
}
/*countdown end*/

$.fn.getMaxHeights = function( outer, classNull, minHeight ){
	var maxHeight = this.map( function( i, e ){
		var calc_height=0;

		$(e).css('height', '');

		if( outer == true ){
			calc_height=$(e).actual('outerHeight');
		}else{
			calc_height=$(e).actual('height');
		}
		return calc_height;
	}).get();
	for(var i = 0, c = maxHeight.length; i < c; ++i){
		if(maxHeight[i] % 2){
			--maxHeight[i];
		}
	}
	return Math.max.apply( this, maxHeight );
}

if(!funcDefined('onLoadjqm')){
	var onLoadjqm = function(name, hash, requestData, selector, requestTitle, isButton, thButton){
		hash.w.addClass('show').css({
			'margin-left': ($(window).width() > hash.w.outerWidth() ? '-' + hash.w.outerWidth() / 2 + 'px' : '-' + $(window).width() / 2 + 'px'),
			'top': $(document).scrollTop() + (($(window).height() > hash.w.outerHeight() ? ($(window).height() - hash.w.outerHeight()) / 2 : 10))   + 'px',
			'opacity': 1
		});
		if(typeof(requestData) == 'undefined'){
			requestData = '';
		}
		if(typeof(selector) == 'undefined'){
			selector = false;
		}
		var width = $('.'+name+'_frame').width();
		$('.'+name+'_frame').css('margin-left', '-'+width/2+'px');

		if(name=='order-popup-call') {
		}
		else if(name=='order-button') {
			$(".order-button_frame").find("div[product_name]").find("input").val(hash.t.title).attr("readonly", "readonly").css({"overflow": "hidden", "text-overflow": "ellipsis"});
		}
		else if(name == "to-order" && selector){
			$(".to-order_frame").find('[data-sid="PRODUCT_NAME"]').val($(selector).data('name')).attr("readonly", "readonly").css({"overflow": "hidden", "text-overflow": "ellipsis"});
			$(".to-order_frame").find('[data-sid="PRODUCT_ID"]').val($(selector).attr('data-item'));
		}
		else if(name == "services" && selector) {
			$(".services_frame").find('[data-sid="SERVICE"]').val($(selector).attr('data-title'));
		}
		else if(name == "resume" && selector) {
			if($(selector).attr('data-jobs')){
				$(".resume_frame").find('[data-sid="POST"]').attr("readonly", "readonly").val($(selector).attr('data-jobs'));
			}
		}
		else if(name=='basket_error')
		{
			$(".basket_error_frame .pop-up-title").text(requestTitle);
			$(".basket_error_frame .ajax_text").html(requestData);
			// $('.basket_error_frame .ajax_text select').ikSelect('redraw');
			initSelects(document);
			if(isButton=="Y" && thButton){
				$("<div class='popup_button_basket_wr'><span class='popup_button_basket big_btn button' data-item="+thButton.data("item")+"><span>"+BX.message("ERROR_BASKET_BUTTON")+"</span></span></div>").insertAfter($(".basket_error_frame .ajax_text"));
			}
		}
		else if( name == 'one_click_buy') {
			$('#one_click_buy_form_button').on("click", function() {
				if(!$(this).hasClass("clicked")){
					if($('#one_click_buy_form').valid()){
						$(this).addClass("clicked");
						$("#one_click_buy_form").submit(); //otherwise don't works
					}
				}
			});

			$('#one_click_buy_form').submit( function() {
				if($('.'+name+'_frame form input.error').length || $('.'+name+'_frame form textarea.error').length) {
					return false
				}
				else{
					$.ajax({
						url: $(this).attr('action'),
						data: $(this).serialize(),
						type: 'POST',
						dataType: 'json',
						error: function(data) {
							alert('Error connecting server');
						},
						success: function(data) {
							if(data.result == 'Y'){
								if(arMShopOptions['COUNTERS']['USE_1CLICK_GOALS'] !== 'N'){
									var eventdata = {goal: 'goal_1click_success'};
									BX.onCustomEvent('onCounterGoals', [eventdata])
								}
								$('.one_click_buy_result').show();
								$('.one_click_buy_result_success').show();
								purchaseCounter(data.message, arMShopOptions["COUNTERS"]["TYPE"]["ONE_CLICK"]);
							}
							else{
								$('.one_click_buy_result').show();
								$('.one_click_buy_result_fail').show();
								if(('err' in data) && data.err)
									data.message=data.message+' \n'+data.err;
								$('.one_click_buy_result_text').html(data.message);
							}
							$('.one_click_buy_modules_button', self).removeClass('disabled');
							$('#one_click_buy_form').hide();
							$('#one_click_buy_form_result').show();
						}
					});
				}
				return false;
			});
		}
		else if( name == 'one_click_buy_basket') {
			$('#one_click_buy_form_button').on("click", function() {
				if(!$(this).hasClass("clicked")){
					if($('#one_click_buy_form').valid()){
						$(this).addClass("clicked");
						$("#one_click_buy_form").submit(); //otherwise don't works
					}
				}
			});

			$('#one_click_buy_form').on("submit", function(){
				if($('.'+name+'_frame form input.error').length || $('.'+name+'_frame form textarea.error').length) {
					return false
				}
				else{
					$.ajax({
						url: $(this).attr('action'),
						data: $(this).serialize(),
						type: 'POST',
						dataType: 'json',
						error: function(data) {
							window.console&&console.log(data);
						},
						success: function(data) {
							if(data.result == 'Y') {
								if(arMShopOptions['COUNTERS']['USE_FASTORDER_GOALS'] !== 'N'){
									var eventdata = {goal: 'goal_fastorder_success'};
									BX.onCustomEvent('onCounterGoals', [eventdata])
								}
								$('.one_click_buy_result').show();
								$('.one_click_buy_result_success').show();
								purchaseCounter(data.message, arMShopOptions["COUNTERS"]["TYPE"]["QUICK_ORDER"]);
							}
							else{
								$('.one_click_buy_result').show();
								$('.one_click_buy_result_fail').show();
								if(('err' in data) && data.err)
									data.message=data.message+' \n'+data.err;
								$('.one_click_buy_result_text').text(data.message);
							}
							$('.one_click_buy_modules_button', self).removeClass('disabled');
							$('#one_click_buy_form').hide();
							$('#one_click_buy_form_result').show();
						}
					});
				}
				return false;
			});
		}

		$('.'+name+'_frame').show();
	}
}

if(!funcDefined('onHidejqm')){
	var onHidejqm = function(name, hash){
		if (hash.w.find('.one_click_buy_result_success').is(':visible') && name=="one_click_buy_basket") {
			window.location.href = window.location.href;
		}
		hash.w.css('opacity', 0).hide();
		hash.w.empty();
		hash.o.remove();
		hash.w.removeClass('show');
	}
}

if(!funcDefined("oneClickBuy")) {
	var oneClickBuy = function (elementID, iblockID, that) {
		var name = 'one_click_buy';
		var elementQuantity = 1;
		var offerProps = false;
		var buy_btn=$(that).closest('.buy_block').find('.to-cart');
		var buy_btn2=$(that).closest('tr').find('.to-cart');

		if(typeof(that) !== 'undefined'){
			elementQuantity = $(that).attr('data-quantity');
			offerProps = $(that).attr('data-props');
		}

		if(elementQuantity < 0){
			elementQuantity = 1;
		}

		var tmp_props=buy_btn.data("props"),
			tmp_props2=buy_btn2.data("props"),
			props='',
			part_props='',
			add_props='N',
			fill_prop={},
			iblockid = buy_btn.data('iblockid'),
			item = buy_btn.attr('data-item');

		if(tmp_props){
			props=tmp_props.split(";");
		}else if(tmp_props2){
			props=tmp_props2.split(";");
		}
		if(buy_btn.data("part_props")){
			part_props=buy_btn.data("part_props");
		}
		if(buy_btn.data("add_props")){
			add_props=buy_btn.data("add_props");
		}

		fill_prop=fillBasketPropsExt(buy_btn, 'prop', buy_btn.data('bakset_div'));
		fill_prop.iblockID=iblockid;
		fill_prop.part_props=part_props;
		fill_prop.add_props=add_props;
		fill_prop.props=JSON.stringify(props);
		fill_prop.item=item;
		fill_prop.ocb_item="Y";

		$('body').find('.'+name+'_frame').remove();
		$('body').append('<div class="'+name+'_frame popup"></div>');
		$('.'+name+'_frame').jqm({trigger: '.'+name+'_frame.popup', onHide: function(hash) { onHidejqm(name,hash); }, toTop: false, onLoad: function( hash ){ onLoadjqm(name, hash ); }, ajax: arMShopOptions["SITE_DIR"]+'ajax/one_click_buy.php?ELEMENT_ID='+elementID+'&IBLOCK_ID='+iblockID+'&ELEMENT_QUANTITY='+elementQuantity+'&OFFER_PROPS='+fill_prop.props});
		$('.'+name+'_frame.popup').click();
	}
}

if(!funcDefined("oneClickBuyBasket")) {
	var oneClickBuyBasket = function () {
		name = 'one_click_buy_basket'
		$('body').find('.'+name+'_frame').remove();
		$('body').append('<div class="'+name+'_frame popup"></div>');
		$('.'+name+'_frame').jqm({trigger: '.'+name+'_frame.popup', onHide: function(hash) { onHidejqm(name,hash) }, onLoad: function( hash ){ onLoadjqm( name, hash ); }, ajax: arMShopOptions["SITE_DIR"]+'ajax/one_click_buy_basket.php'});
		$('.'+name+'_frame.popup').click();
	}
}

if(!funcDefined("scroll_block")) {
	function scroll_block(block){
		var topPos = block.offset().top,
			headerH = $('header').outerHeight(true,true);
		if($(".stores_tab").length){
			$(".stores_tab").addClass("current").siblings().removeClass("current");
		}else{
			$(".prices_tab").addClass("current").siblings().removeClass("current");
			if($(".prices_tab .opener").length && !$(".prices_tab .opener .opened").length){
				var item = $(".prices_tab .opener").first();
				item.find(".opener_icon").addClass("opened");
				item.parents("tr").addClass("nb")
				item.parents("tr").next(".offer_stores").find(".stores_block_wrap").slideDown(200);
			}
		}
		$('html,body').animate({'scrollTop':topPos-30},150);
	}
}

if(!funcDefined("jqmEd")) {
	var jqmEd = function (name, form_id, open_trigger, requestData, selector, requestTitle, isButton, thButton){
		if(typeof(requestData) == "undefined"){
			requestData = '';
		}
		if(typeof(selector) == "undefined"){
			selector = false;
		}
		$('body').find('.'+name+'_frame').remove();
		$('body').append('<div class="'+name+'_frame popup"></div>');
		if(typeof open_trigger == "undefined" ){
			$('.'+name+'_frame').jqm({trigger: '.'+name+'_frame.popup', onHide: function(hash) { onHidejqm(name,hash); }, onLoad: function( hash ){ onLoadjqm( name , hash , requestData, selector); }, ajax: arMShopOptions["SITE_DIR"]+'ajax/form.php?form_id='+form_id+(requestData.length ? '&' + requestData : '')});
		}else{
			if(name == 'enter'){
				$('.'+name+'_frame').jqm({trigger: open_trigger, onHide: function(hash) { onHidejqm(name,hash)}, onLoad: function( hash ){ onLoadjqm( name , hash , requestData, selector); }, ajax: arMShopOptions["SITE_DIR"]+'ajax/auth.php'});
			}else if(name=='basket_error'){
				$('.'+name+'_frame').jqm({trigger: open_trigger, onHide: function(hash) { onHidejqm(name,hash)}, onLoad: function( hash ){ onLoadjqm( name , hash , requestData, selector, requestTitle, isButton, thButton); }, ajax: arMShopOptions["SITE_DIR"]+'ajax/basket_error.php'});
			}else{
				$('.'+name+'_frame').jqm({trigger: open_trigger,  onHide: function(hash) { onHidejqm(name,hash)}, onLoad: function( hash ){ onLoadjqm( name , hash , requestData, selector); }, ajax: arMShopOptions["SITE_DIR"]+'ajax/form.php?form_id='+form_id+(requestData.length ? '&' + requestData : '')});
			}
			$(open_trigger).dblclick(function(){return false;})
		}
		return true;
	}
}

if (!funcDefined("replaceBasketPopup")){
	function replaceBasketPopup (hash){
		if(typeof hash != "undefined"){
			hash.w.hide();
			hash.o.hide();
		}
	}
}

if(!funcDefined("waitLayer")){
	function waitLayer(delay, callback){
		if((typeof dataLayer !== 'undefined') && (typeof callback === 'function')){
			callback();
		}
		else{
			setTimeout(function() {
				waitLayer(delay, callback);
			}, delay);
		}
	}
}

if(!funcDefined("waitCounter")){
	waitCounter = function(idCounter, delay, callback){
		var obCounter = window['yaCounter' + idCounter];
		if(typeof obCounter == 'object'){
			if(typeof callback == 'function'){
				callback();
			}
		}
		else{
			setTimeout(function(){
				waitCounter(idCounter, delay, callback);
			}, delay);
		}
	}
}

if(!funcDefined("checkCounters")){
	function checkCounters(name){
		if(typeof name !== "undefined"){
			if(name == "google" && (arMShopOptions["COUNTERS"]["GOOGLE_ECOMERCE"] == "Y" && arMShopOptions["COUNTERS"]["GOOGLE_COUNTER"] > 0)){
				return true;
			}
			else if(name == "yandex" && (arMShopOptions['COUNTERS']['USE_YA_COUNTER'] == 'Y' && arMShopOptions["COUNTERS"]["YANDEX_ECOMERCE"] == "Y" && arMShopOptions["COUNTERS"]["YANDEX_COUNTER"] > 0)){
				return true;
			}
			else{
				return false;
			}
		}
		else if((arMShopOptions['COUNTERS']['USE_YA_COUNTER'] == 'Y' && arMShopOptions["COUNTERS"]["YANDEX_ECOMERCE"] == "Y" && arMShopOptions["COUNTERS"]["YANDEX_COUNTER"] > 0) || (arMShopOptions["COUNTERS"]["GOOGLE_ECOMERCE"] == "Y" && arMShopOptions["COUNTERS"]["GOOGLE_COUNTER"] > 0)) {
			return true;
		}
		else{
			return false;
		}
	}
}

if(!funcDefined("addBasketCounter")){
	function addBasketCounter(id){
		if(arMShopOptions['COUNTERS']['USE_BASKET_GOALS'] !== 'N'){
			var eventdata = {goal: 'goal_basket_add', params: {id: id}};
			BX.onCustomEvent('onCounterGoals', [eventdata]);
		}
		if(checkCounters()){
			$.ajax({
				url:arMShopOptions['SITE_DIR'] + "ajax/goals.php",
				dataType: "json",
				type: "POST",
				data: {"ID": id},
				success: function(item){
					if(!!item && !!item.ID){
						waitLayer(100, function() {
							dataLayer.push({
								"event": arMShopOptions["COUNTERS"]['GOOGLE_EVENTS']['ADD2BASKET'],
							    "ecommerce": {
							    	"currencyCode": item.CURRENCY,
							        "add": {
							            "products": [{
						                    "id": item.ID,
						                    "name": item.NAME,
						                    "price": item.PRICE,
						                    "brand": item.BRAND,
						                    "category": item.CATEGORY,
						                    "quantity": item.QUANTITY
						                }]
							        }
							    }
							});
						});
					}
				}
			});
		}
	}
}

if(!funcDefined("purchaseCounter")){
	function purchaseCounter(order_id, type, callback){
		if(checkCounters()){
			$.ajax({
				url:arMShopOptions['SITE_DIR'] + "ajax/goals.php",
				dataType: "json",
				type: "POST",
				data: {"ORDER_ID": order_id, "TYPE": type},
				success: function(order){
					var products = [];
					if(order.ITEMS){
						for(var i in order.ITEMS){
							products.push({
								"id": order.ITEMS[i].ID,
								"sku": order.ITEMS[i].ID,
			                    "name": order.ITEMS[i].NAME,
			                    "price": order.ITEMS[i].PRICE,
			                    "brand": order.ITEMS[i].BRAND,
			                    "category": order.ITEMS[i].CATEGORY,
			                    "quantity": order.ITEMS[i].QUANTITY
							});
						}
					}
					if(order.ID){
						waitLayer(100, function() {
							dataLayer.push({
							    "ecommerce": d = {
							    	"purchase": {
								    	"actionField":{
								    		"id": order.ACCOUNT_NUMBER,
								    		"shipping": order.PRICE_DELIVERY,
								    		"tax": order.TAX_VALUE,
								    		"list": type,
								    		"revenue": order.PRICE
								    	},
							            "products": products
							        }
							    }
							});

							if(typeof callback !== 'undefined'){
								callback(d);
							}
						});
					}
					else{
						if(typeof callback !== 'undefined'){
							callback(false);
						}
					}
				},
				error: function(){
					if(typeof callback !== 'undefined'){
						callback(false);
					}
				}
			});
		}
		else{
			if(typeof callback !== 'undefined'){
				callback();
			}
		}
	}
}

if(!funcDefined("viewItemCounter")){
	function viewItemCounter(id, price_id){
		if(checkCounters()){
			$.ajax({
				url:arMShopOptions['SITE_DIR'] + "ajax/goals.php",
				dataType: "json",
				type: "POST",
				data: {"PRODUCT_ID": id, "PRICE_ID": price_id},
				success: function(item){
					if(item.ID){
						waitLayer(100, function() {
							dataLayer.push({
								//"event": "",
								"ecommerce": {
									"detail": {
										"products": [{
											"id": item.ID,
											"name": item.NAME,
											"price": item.PRICE,
											"brand": item.BRAND,
											"category": item.CATEGORY
										}]
									}
								}
							});
						});
					}
				}
			});
		}
	}
}

if(!funcDefined("checkoutCounter")){
	function checkoutCounter(step, option, callback){
		if(checkCounters('google')){
			$.ajax({
				url:arMShopOptions['SITE_DIR'] + "ajax/goals.php",
				dataType: "json",
				type: "POST",
				data: {"BASKET": "Y"},
				success: function(basket){
					var products = [];
					if(basket.ITEMS){
						for(var i in basket.ITEMS){
							products.push({
								"id": basket.ITEMS[i].ID,
			                    "name": basket.ITEMS[i].NAME,
			                    "price": basket.ITEMS[i].PRICE,
			                    "brand": basket.ITEMS[i].BRAND,
			                    "category": basket.ITEMS[i].CATEGORY,
			                    "quantity": basket.ITEMS[i].QUANTITY
							});
						}
					}
					if(products){
						waitLayer(100, function() {
							dataLayer.push({
								"event": arMShopOptions["COUNTERS"]['GOOGLE_EVENTS']['CHECKOUT_ORDER'],
							    "ecommerce": {
							    	"actionField":{
							    		"step": step,
							    		"option": option
							    	},
							        "products": products
							    },
							    /*"eventCallback": function() {
							    	if((typeof callback !== 'undefined') && (typeof callback === 'function')){
							    		callback();
							    	}
							   }*/
							});
						});
					}
				}
			});
		}
	}
}

if(!funcDefined("delFromBasketCounter")){
	function delFromBasketCounter(id, callback){
		if(checkCounters()){
			$.ajax({
				url:arMShopOptions['SITE_DIR'] + "ajax/goals.php",
				dataType: "json",
				type: "POST",
				data: {"ID": id},
				success: function(item){
					if(item.ID){
						waitLayer(100, function() {
							dataLayer.push({
								"event": arMShopOptions["COUNTERS"]['GOOGLE_EVENTS']['REMOVE_BASKET'],
							    "ecommerce": {
							        "remove": {
							            "products": [{
						                    "id": item.ID,
						                    "name": item.NAME,
						                    "category": item.CATEGORY
						                }]
							        }
							    }
							});
							if(typeof callback == 'function'){
								callback();
							}
						});
					}
				}
			});
		}
	}
}

if(!funcDefined("setHeightCompany")){
	function setHeightCompany(){
		$('.md-50.img').height($('.md-50.big').outerHeight()-35);
	}
}

if(!funcDefined("initSly")){
	function initSly(){
		var $frame  = $(document).find('.frame');
		var $slidee = $frame.children('ul').eq(0);
		var $wrap   = $frame.parent();

		$frame.sly({
			horizontal: 1,
			itemNav: 'basic',
			smart: 1,
			mouseDragging: 0,
			touchDragging: 0,
			releaseSwing: 0,
			startAt: 0,
			scrollBar: $wrap.find('.scrollbar'),
			scrollBy: 1,
			speed: 300,
			elasticBounds: 0,
			easing: 'swing',
			dragHandle: 1,
			dynamicHandle: 1,
			clickBar: 1,

			// Buttons
			forward: $wrap.find('.forward'),
			backward: $wrap.find('.backward'),
		});
		$frame.sly('reload');
	}
}

if(!funcDefined("createTableCompare")){
	function createTableCompare(originalTable, appendDiv, cloneTable){

		try{
			var clone = originalTable.clone().removeAttr('id').addClass('clone');
			if(cloneTable.length){
				cloneTable.remove();
				appendDiv.html('');
				appendDiv.html(clone);
			}else{
				appendDiv.append(clone);
			}
		}
		catch(e){}
		finally{

		}
	}
}

if(!funcDefined('fillBasketPropsExt')){
	fillBasketPropsExt = function(that, prop_code, basket_prop_div){
		var
			i = 0,
			propCollection = null,
			foundValues = false,
			basketParams = {},
			obBasketProps = null;

		// obBasketProps = that.closest('.catalog_detail').find('.basket_props_block');
		obBasketProps = BX(basket_prop_div);

		if (!!obBasketProps)
		{
			propCollection = obBasketProps.getElementsByTagName('select');
			if (!!propCollection && !!propCollection.length)
			{
				for (i = 0; i < propCollection.length; i++)
				{
					if (!propCollection[i].disabled)
					{
						switch(propCollection[i].type.toLowerCase())
						{
							case 'select-one':
								basketParams[propCollection[i].name] = propCollection[i].value;
								foundValues = true;
								break;
							default:
								break;
						}
					}
				}
			}
			propCollection = obBasketProps.getElementsByTagName('input');
			if (!!propCollection && !!propCollection.length)
			{
				for (i = 0; i < propCollection.length; i++)
				{
					if (!propCollection[i].disabled)
					{
						switch(propCollection[i].type.toLowerCase())
						{
							case 'hidden':
								basketParams[propCollection[i].name] = propCollection[i].value;
								foundValues = true;
								break;
							case 'radio':
								if (propCollection[i].checked)
								{
									basketParams[propCollection[i].name] = propCollection[i].value;
									foundValues = true;
								}
								break;
							default:
								break;
						}
					}
				}
			}
		}
		if (!foundValues)
		{
			basketParams[prop_code] = [];
			basketParams[prop_code][0] = 0;
		}
		return basketParams;
	}
}
if(!funcDefined('showBasketError')){
	showBasketError = function(mess, title, addButton, th){
		var title_set=(title ? title : BX.message("ERROR_BASKET_TITLE")),
			isButton="N",
			thButton="";
		if(typeof addButton!==undefined){
			isButton="Y";
		}
		if(typeof th!==undefined){
			thButton=th;
		}
		$("body").append("<span class='add-error-bakset' style='display:none;'></span>");
		jqmEd('basket_error', 'error-bakset', '.add-error-bakset', mess, this, title_set, isButton, thButton);
		$("body .add-error-bakset").click();
		$("body .add-error-bakset").remove();
	}
}

if(!funcDefined("isRealValue")){
	function isRealValue(obj){
		return obj && obj !== "null" && obj!== "undefined";
	}
}

if(!funcDefined("rightScroll")){
	function rightScroll(prop, id){
		var el = BX('prop_' + prop + '_' + id);
		if (el) {
			var curVal = parseInt(el.style.marginLeft);
			if (curVal >= 0) el.style.marginLeft = curVal - 20 + '%';
		}
	}
}

if(!funcDefined("leftScroll")){
	function leftScroll(prop, id){
		var el = BX('prop_' + prop + '_' + id);
		if (el) {
			var curVal = parseInt(el.style.marginLeft);
			if (curVal < 0) el.style.marginLeft = curVal + 20 + '%';
		}
	}
}

if(!funcDefined("InitOrderCustom")){
	InitOrderCustom = function () {
		if(!$('.ps_logo .image').length)
			$('.ps_logo img').wrap('<div class="image"></div>');

		$('#bx-soa-order .radio-inline').each(function() {
			if ($(this).find('input').attr('checked') == 'checked') {
				$(this).addClass('checked');
			}
		});

		$('#bx-soa-order .checkbox input[type=checkbox]').each(function() {
			if ($(this).attr('checked') == 'checked')
				$(this).parent().addClass('checked');
		});

		$('#bx-soa-order .bx-authform-starrequired').each(function() {
			var html = $(this).html();
			$(this).closest('label').append('<span class="bx-authform-starrequired"> '+ html + '</span>');
			$(this).detach();
		});
		$('.bx_ordercart_coupon').each(function() {
			if ($(this).find('.bad').length)
				$(this).addClass('bad');
			else if ($(this).find('.good').length)
				$(this).addClass('good');
		});
	}
}

$.fn.getFloatWidth = function(){
	var width = 0

	if($(this).length){
		var rect = $(this)[0].getBoundingClientRect()
		if(!(width = rect.width)){
			width = rect.right - rect.left
		}
	}

	return width
}

$.fn.equalizeHeights = function( outer, classNull, minHeight ){
	var maxHeight = this.map( function( i, e ){
		var minus_height=0,
			calc_height=0;
		if(classNull!==false){
			minus_height=parseInt($(e).find(classNull).actual('outerHeight'));
		}
		if(minus_height)
			minus_height+=12;
		$(e).css('height', '');
		if( outer == true ){
			calc_height=$(e).actual('outerHeight')-minus_height;
		}else{
			calc_height=$(e).actual('height')-minus_height;
		}
		if(minHeight!==false){
			if(calc_height<minHeight){
				calc_height+=(minHeight-calc_height);
			}
			if(window.matchMedia('(max-width: 520px)').matches){
				calc_height=300;
			}
			if(window.matchMedia('(max-width: 400px)').matches){
				calc_height=200;
			}
		}
		return calc_height;
	}).get();

	for(var i = 0, c = maxHeight.length; i < c; ++i){
		if(maxHeight[i] % 2){
			--maxHeight[i];
		}
	}
	return this.height( Math.max.apply( this, maxHeight ) );
}

$.fn.sliceHeight = function( options ){
	function _slice(el){

		el.each(function() {
			$(this).css('line-height', '');
			$(this).css('height', '');
		});
		if(typeof(options.autoslicecount) == 'undefined' || options.autoslicecount !== false){
			var row=(typeof(options.row) !== 'undefined' && options.row.length) ?  el.first().parents(options.row).getFloatWidth() : el.first().parents('.items').getFloatWidth(),
				elw=(typeof(options.item) !== 'undefined' && options.item.length) ? $(options.item).first().getFloatWidth() : (el.first().hasClass('item') ? el.first().getFloatWidth() : el.first().parents('.item').getFloatWidth());
			if(!row){
				row = el.first().parents('.row').getFloatWidth();
			}
			if(row && elw){
				options.slice = Math.floor(row / elw);
			}
		}

		if(options.slice){
			for(var i = 0; i < el.length; i += options.slice){
				$(el.slice(i, i + options.slice)).equalizeHeights(options.outer, options.classNull, options.minHeight);
			}
		}
		if(options.lineheight){
			var lineheightAdd = parseInt(options.lineheight);
			if(isNaN(lineheightAdd)){
				lineheightAdd = 0;
			}
			el.each(function() {
				$(this).css('line-height', ($(this).actual('height') + lineheightAdd) + 'px');
			});
		}

	}

	var options = $.extend({
		slice: null,
		outer: false,
		lineheight: false,
		autoslicecount: true,
		classNull: false,
		minHeight: false,
		options: false,
		resize: true,
		row:false,
		item:false
	}, options);

	var el = $(this);
	_slice(el);

	if(options.resize){
		BX.addCustomEvent('onWindowResize', function(eventdata) {
			ignoreResize.push(true);
			_slice(el);
			ignoreResize.pop();
		});
	}
}

if(!funcDefined("clearViewedProduct")){
	function clearViewedProduct(){
		try{
			var siteID = arMShopOptions.SITE_ID;
			var localKey = 'MARKET_VIEWED_ITEMS_' + siteID;
			var cookieParams = {path: '/', expires: 30};
			if(typeof BX.localStorage !== 'undefined'){
				// remove local storage
				BX.localStorage.set(localKey, {}, 0);
			}
			// remove cookie
			$.removeCookie(localKey, cookieParams);
		}
		catch(e){
			console.error(e);
		}
	}
}

if(!funcDefined("setViewedProduct")){
	function setViewedProduct(id, arData){
		try{
			// save $.cookie option
			var bCookieJson = $.cookie.json;
			$.cookie.json = true;

			var siteID = arMShopOptions.SITE_ID;
			var localKey = 'MARKET_VIEWED_ITEMS_' + siteID;
			var cookieParams = {path: '/', expires: 30};

			if((typeof BX.localStorage !== 'undefined') && (typeof id !== 'undefined') && (typeof arData !== 'undefined')){
				var PRODUCT_ID = (typeof arData.PRODUCT_ID !== 'undefined') ? arData.PRODUCT_ID : id;
				var arViewedLocal = BX.localStorage.get(localKey) ? BX.localStorage.get(localKey) : {};
				var arViewedCookie = $.cookie(localKey) ? $.cookie(localKey) : {};

				// delete some items (sync cookie & local storage)
				for(var _id in arViewedLocal){
					arViewedLocal[_id].IS_LAST = false;
					if(typeof arViewedCookie[_id] === 'undefined'){
						delete arViewedLocal[_id];
					}
				}
				for(var _id in arViewedCookie){
					if(typeof arViewedLocal[_id] === 'undefined'){
						delete arViewedCookie[_id];
					}
				}

				// delete item if other item (offer) of that PRODUCT_ID is exists
				if(typeof arViewedLocal[PRODUCT_ID] !== 'undefined'){
					if(arViewedLocal[PRODUCT_ID].ID != id){
						delete arViewedLocal[PRODUCT_ID];
						delete arViewedCookie[PRODUCT_ID];
					}
				}
				var time = new Date().getTime();
				arData.ID = id;
				arData.ACTIVE_FROM = time;
				arData.IS_LAST = true;
				arViewedLocal[PRODUCT_ID] = arData;
				arViewedCookie[PRODUCT_ID] = [time.toString(), arData.PICTURE_ID];

				$.cookie(localKey, arViewedCookie, cookieParams);
				BX.localStorage.set(localKey, arViewedLocal, 2592000);  // 30 days
			}
		}
		catch(e){
			console.error(e);
		}
		finally{
			// restore $.cookie option
			$.cookie.json = bCookieJson;
		}
	}
}

if(!funcDefined("setBasketAspro")){
	function setBasketAspro(){
		if(typeof arBasketAspro !== 'undefined'){
			// ready
			if(typeof arBasketAspro.BASKET !== 'undefined'){
				for(var i in arBasketAspro.BASKET){
					var id = arBasketAspro.BASKET[i];
					$('.to-cart[data-item=' + id + ']').hide();
					$('.counter_block[data-item=' + id + ']').hide();
					$('.in-cart[data-item=' + id + ']').show();
					$('.in-cart[data-item=' + id + ']').closest('.button_block').addClass('wide');
				}
			}

			// delayed
			if(typeof arBasketAspro.DELAY !== 'undefined'){
				for(var i in arBasketAspro.DELAY){
					var id = arBasketAspro.DELAY[i];
					$('.wish_item.to[data-item=' + id + ']').hide();
					$('.wish_item.in[data-item=' + id + ']').show();
					if($('.wish_item[data-item=' + id + ']').find(".value.added").length){
						$('.wish_item[data-item=' + id + ']').addClass("added");
						$('.wish_item[data-item=' + id + ']').find(".value").hide();
						$('.wish_item[data-item=' + id + ']').find(".value.added").css('display', 'inline-block');
					}
				}
			}

			// subscribed
			if(typeof arBasketAspro.SUBSCRIBE !== 'undefined'){
				for(var i in arBasketAspro.SUBSCRIBE){
					var id = arBasketAspro.SUBSCRIBE[i];
					$('.to-subscribe[data-item=' + id + ']').hide();
					$('.in-subscribe[data-item=' + id + ']').show();
				}
			}

			// compared
			if(typeof arBasketAspro.COMPARE !== 'undefined'){
				for(var i in arBasketAspro.COMPARE){
					var id = arBasketAspro.COMPARE[i];
					$('.compare_item.to[data-item=' + id + ']').hide();
					$('.compare_item.in[data-item=' + id + ']').show();
					if ($('.compare_item[data-item=' + id + ']').find(".value.added").length){
						$('.compare_item[data-item=' + id + ']').addClass("added");
						$('.compare_item[data-item=' + id + ']').find(".value").hide();
						$('.compare_item[data-item=' + id + ']').find(".value.added").css('display','inline-block');
					}
				}
			}
		}
	}
}

checkPopupWidth = function(){
	$('.popup.show').each(function() {
		var width_form = $(this).actual('width');
		$(this).css({
			'margin-left': ($(window).width() > width_form ? '-' + width_form / 2 + 'px' : '-' + $(window).width() / 2 + 'px'),
		});
	});
}

checkCaptchaWidth = function(){
	$('.captcha-row').each(function() {
		var width = $(this).actual('width');
		if($(this).hasClass('b')){
			if(width > 320){
				$(this).removeClass('b');
			}
		}
		else{
			if(width <= 320){
				$(this).addClass('b');
			}
		}
	});
}

checkFormWidth = function(){
	$('.form .form_left').each(function() {
		var form = $(this).parents('.form');
		var width = form.actual('width');
		if(form.hasClass('b')){
			if(width > 417){
				form.removeClass('b');
			}
		}
		else{
			if(width <= 417){
				form.addClass('b');
			}
		}
	});
}

checkFormControlWidth = function(){
	$('.form-control').each(function() {
		var width = $(this).actual('width');
		var labelWidth = $(this).find('label:not(.error) > span').actual('width');
		var errorWidth = $(this).find('label.error').actual('width');
		if(errorWidth > 0){
			if($(this).hasClass('h')){
				if(width > (labelWidth + errorWidth + 5)){
					$(this).removeClass('h');
				}
			}
			else{
				if(width <= (labelWidth + errorWidth + 5)){
					$(this).addClass('h');
				}
			}
		}
		else{
			$(this).removeClass('h');
		}
	});
}

scrollToTop = function(){
	if(arMShopOptions['THEME']['SCROLLTOTOP_TYPE'] !== 'NONE'){
		var _isScrolling = false;
		// Append Button
		$('body').append($('<a />').addClass('scroll-to-top ' + arMShopOptions['THEME']['SCROLLTOTOP_TYPE'] + ' ' + arMShopOptions['THEME']['SCROLLTOTOP_POSITION']).attr({'href': '#', 'id': 'scrollToTop'}));
		$('#scrollToTop').click(function(e){
			e.preventDefault();
			$('body, html').animate({scrollTop : 0}, 500);
			return false;
		});
		// Show/Hide Button on Window Scroll event.
		$(window).scroll(function(){
			if(!_isScrolling) {
				_isScrolling = true;
				if($(window).scrollTop() > 150){
					$('#scrollToTop').stop(true, true).addClass('visible');
					_isScrolling = false;
				}
				else{
					$('#scrollToTop').stop(true, true).removeClass('visible');
					_isScrolling = false;
				}
				checkScrollToTop();
			}
		});
	}
}

checkScrollToTop = function(){
	var bottom = 55,
		scrollVal = $(window).scrollTop(),
		windowHeight = $(window).height(),
		footerOffset = $('footer').offset().top +70;

	if(arMShopOptions['THEME']['SCROLLTOTOP_POSITION'] == 'CONTENT'){
		warpperWidth = $('body > .wrapper > .wrapper_inner').width();
		$('#scrollToTop').css('margin-left', Math.ceil(warpperWidth / 2) + 23);
	}

	if(scrollVal + windowHeight > footerOffset){
		$('#scrollToTop').css('bottom', bottom  + scrollVal + windowHeight - footerOffset - 0);
	}
	else if(parseInt($('#scrollToTop').css('bottom')) > bottom){
		$('#scrollToTop').css('bottom', bottom);
	}
}

CheckObjectsSizes = function() {
	$('.container iframe,.container object,.container video').each(function() {
		var height_attr = $(this).attr('height');
		var width_attr = $(this).attr('width');
		if (height_attr && width_attr) {
			$(this).css('height', $(this).outerWidth() * height_attr / width_attr);
		}
	});
}

if(!funcDefined('reloadTopBasket')){
	var reloadTopBasket = function reloadTopBasket(action, basketWindow, speed, delay, slideDown, item){
		var obj={
				"PARAMS": $('#top_basket_params').val(),
				"ACTION": action
			};
		if(typeof item !== "undefined" ){
			obj.delete_top_item='Y';
			obj.delete_top_item_id=item.data('id');
		}
		$.post( arMShopOptions['SITE_DIR']+"ajax/show_basket_popup.php", obj, $.proxy(function( data ){
			$(basketWindow).html(data);
			if(typeof item !== "undefined" ){
				getActualBasket(item.closest('tr').attr('data-iblockid'));
			}
			if(arMShopOptions['THEME']['SHOW_BASKET_ONADDTOCART'] !== 'N'){
				if($(window).outerWidth() > 520){
					if(slideDown=="Y")
						$(basketWindow).find('.basket_popup_wrapp').stop(true,true).slideDown(speed);
					clearTimeout(basketTimeoutSlide);
					basketTimeoutSlide = setTimeout(function() {
						var _this = $('#basket_line').find('.basket_popup_wrapp');
						if (_this.is(':hover')) {
							_this.show();
						}else{
							$('#basket_line').find('.basket_popup_wrapp').slideUp(speed);
						}
					},delay);
				}
			}
		}))
	}
}

$(document).ready(function(){
	//ecommerce order
	if(arMShopOptions["PAGES"]["ORDER_PAGE"]){
		var arUrl = parseUrlQuery();
		if("ORDER_ID" in arUrl){
			var _id = arUrl["ORDER_ID"];
			if(arMShopOptions['COUNTERS']['USE_FULLORDER_GOALS'] !== 'N'){
				var eventdata = {goal: 'goal_fullorder_success', result: _id};
				BX.onCustomEvent('onCounterGoals', [eventdata])
			}

			if(checkCounters()){
				if(typeof BX.localStorage !== 'undefined'){
					var d = BX.localStorage.get('gtm_e_' + _id);
					if(typeof d === 'object'){
						dataLayer.push({"ecommerce": d});
					}

					if(typeof localStorage !== 'undefined'){
						localStorage.removeItem('gtm_e_' + _id);
					}
				}
			}
		}
	}

	// ya.metrika debug
	if(arMShopOptions['COUNTERS']['USE_DEBUG_GOALS'] === 'Y'){
		$.cookie('_ym_debug', 1, {path: '/',});
	}
	else{
		$.cookie('_ym_debug', null, {path: '/',});
	}

	scrollToTop();
	$('body').on( 'click', '.captcha_reload', function(e){
		var captcha = $(this).parents('.captcha-row');
		e.preventDefault();
		$.ajax({
			url: arMShopOptions['SITE_DIR'] + 'ajax/captcha.php'
		}).done(function(text){
			captcha.find('input[name=captcha_sid]').val(text);
			captcha.find('img').attr('src', '/bitrix/tools/captcha.php?captcha_sid=' + text);
			captcha.find('input[name=captcha_word]').val('').removeClass('error');
			captcha.find('.captcha_input').removeClass('error').find('.error').remove();
		});
	});

	$(window).resize(function(){
		checkScrollToTop();
		checkPopupWidth();
		checkCaptchaWidth();
		checkFormWidth();
		checkFormControlWidth();
		touchMenu('ul.menu:not(.opened) > li.menu_item_l1');
		touchBasket('.cart:not(.empty_cart) .basket_block .link');
		CheckObjectsSizes();

		initSly();

		// if ($(window).width()<=751) {
		if(window.matchMedia('(max-width: 768px)').matches){
			if($('.group_description_block.top').length){
				var top_pos=$('.adaptive_filter').position().top;
				$('.bx_filter.bx_filter_vertical').css({'top':top_pos+20});
			}
		}

		if(resizeEventTimer) {
			clearTimeout(resizeEventTimer);
		}

		resizeEventTimer = setTimeout(function(){
			if($(window).outerWidth()>600){
				$("#header ul.menu").removeClass("opened").css("display", "");

				if($(".authorization-cols").length){
					$('.authorization-cols').equalize({children: '.col .auth-title', reset: true});
					$('.authorization-cols').equalize({children: '.col .form-block', reset: true});
				}
			} else {
				$('.authorization-cols .auth-title').css("height", "");
				$('.authorization-cols .form-block').css("height", "");
			}


			if($("#basket_form").length && $(window).outerWidth()<=600){
				$("#basket_form .tabs_content.basket > li.cur td").each(function() { $(this).css("width","");});
			}


			if($("#header .catalog_menu").length && $("#header .catalog_menu").css("display")!="none"){
				if($(window).outerWidth()>600){
					reCalculateMenu();
				}
			}

			if($(".front_slider_wrapp").length){
				$(".extended_pagination li i").each(function(){
					$(this).css({"borderBottomWidth": ($(this).parent("li").outerHeight()/2), "borderTopWidth": ($(this).parent("li").outerHeight()/2)});
				});
			}

			setHeightCompany();
			$(".bx_filter_section .bx_filter_select_container").each(function(){
				var prop_id=$(this).closest('.bx_filter_parameters_box').attr('property_id');
				if($('#smartFilterDropDown'+prop_id).length){
					$('#smartFilterDropDown'+prop_id).css("max-width", $(this).width());
				}
			})
			$('.specials_slider_wrapp .tabs_content > li.cur, .tab_slider_wrapp .tabs_content > li.cur, .wrapper_block .wr').equalize({children: '.item-title'});
			$('.specials_slider_wrapp .tabs_content > li.cur, .tab_slider_wrapp .tabs_content > li.cur, .wrapper_block .wr').equalize({children: '.item_info'});
			$('.specials_slider_wrapp .tabs_content > li.cur, .tab_slider_wrapp .tabs_content > li.cur, .wrapper_block .wr').equalize({children: '.catalog_item'});
		},
		50, 0);
	});

	setTimeout(function() {
		$(window).resize();
		$(window).scroll();
	}, 400);

	$(".show_props").live('click', function(){
		$(this).next(".props_list_wrapp").stop().slideToggle(333);
		$(this).find("a").toggleClass("opened");
	});

	$('.see_more').live('click', function(e) {
		e.preventDefault();
		var see_more = ($(this).is('.see_more') ? $(this) : $(this).parents('.see_more').first());
		var see_moreText = (see_more.find('> a').length ? see_more.find('> a') : see_more);
		var see_moreHiden = see_more.parent().find('> .d');
		if(see_more.hasClass('open')){
			see_moreText.text(BX.message('CATALOG_VIEW_MORE'));
			see_more.removeClass('open');
			see_moreHiden.hide();
		}
		else{
			see_moreText.text(BX.message('CATALOG_VIEW_LESS'));
			see_more.addClass('open');
			see_moreHiden.show();
		}
		return false;
	});

	$('.avtorization-call.enter').live('click', function(e){
		e.preventDefault();
		$("body").append("<span class='evb-enter' style='display:none;'></span>");
		jqmEd('enter', 'auth', '.evb-enter', '', this);
		$("body .evb-enter").click();
		$("body .evb-enter").remove();
	});

	$('.button.faq_button').click(function(e){
		e.preventDefault();
		$(this).toggleClass('opened');
		$('.faq_ask .form').slideToggle();
	});

	$('.staff.list .staff_section .staff_section_title a').click(function(e) {
		e.preventDefault();
		$(this).parents('.staff_section').toggleClass('opened');
		$(this).parents('.staff_section').find('.staff_section_items').stop().slideToggle(600);
		$(this).parents('.staff_section_title').find('.opener_icon').toggleClass('opened');
	});

	$('.jobs_wrapp .item .name tr').click(function(e) {
		$(this).closest('.item').toggleClass('opened');
		$(this).closest('.item').find('.description_wrapp').stop().slideToggle(600);
		$(this).closest('.item').find('.opener_icon').toggleClass('opened');
	});

	$('.faq.list .item .q a').live('click', function(e){
		e.preventDefault();
		$(this).parents('.item').toggleClass('opened');
		$(this).parents('.item').find('.a').stop().slideToggle();
		$(this).parents('.item').find('.q .opener_icon').toggleClass('opened');
	});

	$('.opener_icon').click(function(e) {
		e.preventDefault();
		$(this).parent().find('a').trigger('click');
	});

	$('.to-order').live('click', function(e){
		e.preventDefault();
		$("body").append("<span class='evb-toorder' style='display:none;'></span>");
		jqmEd('to-order', arMShopOptions['FORM']['TOORDER_FORM_ID'], '.evb-toorder', '', this);
		$("body .evb-toorder").click();
		$("body .evb-toorder").remove();
	});

	/*menu open/close start*/
	$("ul.menu.adaptive .menu_opener").click(function(){
		$(this).parents(".menu.adaptive").toggleClass("opened");
		$("ul.menu.full").toggleClass("opened").slideToggle(200);
	});
	/*menu open/close end*/

	$(".counter_block:not(.basket) .plus").live("click", function(){
		if(!$(this).parents('.basket_wrapp').length){
			if($(this).parent().data("offers")!="Y"){
				var isDetailSKU2 = $(this).parents('.counter_block_wr').length;
					input = $(this).parents(".counter_block").find("input[type=text]")
					tmp_ratio = !isDetailSKU2 ? $(this).parents(".counter_wrapp").find(".to-cart").data('ratio') : $(this).parents('tr').first().find("td.buy .to-cart").data('ratio'),
					isDblQuantity = !isDetailSKU2 ? $(this).parents(".counter_wrapp").find(".to-cart").data('float_ratio') : $(this).parents('tr').first().find("td.buy .to-cart").data('float_ratio'),
					ratio=( isDblQuantity ? parseFloat(tmp_ratio) : parseInt(tmp_ratio, 10)),
					max_value='';
					currentValue = input.val();


				if(isDblQuantity)
					ratio = Math.round(ratio*arMShopOptions.JS_ITEM_CLICK.precisionFactor)/arMShopOptions.JS_ITEM_CLICK.precisionFactor;

				curValue = (isDblQuantity ? parseFloat(currentValue) : parseInt(currentValue, 10));

				curValue += ratio;
				if (isDblQuantity){
					curValue = Math.round(curValue*arMShopOptions.JS_ITEM_CLICK.precisionFactor)/arMShopOptions.JS_ITEM_CLICK.precisionFactor;
				}
				if(parseFloat($(this).data('max'))>0){
					if(input.val() < $(this).data('max')){
						if(curValue>$(this).data('max')){
							input.val($(this).data('max'));
						}else{
							input.val(curValue);
						}
						input.change();
					}
				}else{
					input.val(curValue);
					input.change();
				}
			}
		}
	});

	$(".counter_block:not(.basket) .minus").live("click", function(){
		if(!$(this).parents('.basket_wrapp').length){
			if($(this).parent().data("offers")!="Y"){
				var isDetailSKU2 = $(this).parents('.counter_block_wr').length;
					input = $(this).parents(".counter_block").find("input[type=text]")
					tmp_ratio = !isDetailSKU2 ? $(this).parents(".counter_wrapp").find(".to-cart").data('ratio') : $(this).parents('tr').first().find("td.buy .to-cart").data('ratio'),
					isDblQuantity = !isDetailSKU2 ? $(this).parents(".counter_wrapp").find(".to-cart").data('float_ratio') : $(this).parents('tr').first().find("td.buy .to-cart").data('float_ratio'),
					ratio=( isDblQuantity ? parseFloat(tmp_ratio) : parseInt(tmp_ratio, 10)),
					max_value='';
					currentValue = input.val();

				if(isDblQuantity)
					ratio = Math.round(ratio*arMShopOptions.JS_ITEM_CLICK.precisionFactor)/arMShopOptions.JS_ITEM_CLICK.precisionFactor;

				curValue = (isDblQuantity ? parseFloat(currentValue) : parseInt(currentValue, 10));

				curValue -= ratio;
				if (isDblQuantity){
					curValue = Math.round(curValue*arMShopOptions.JS_ITEM_CLICK.precisionFactor)/arMShopOptions.JS_ITEM_CLICK.precisionFactor;
				}

				if(parseFloat($(this).parents(".counter_block").find(".plus").data('max'))>0){
					if(currentValue > ratio){
						if(curValue<ratio){
							input.val(ratio);
						}else{
							input.val(curValue);
						}
						input.change();
					}
				}else{
					if(curValue > ratio){
						input.val(curValue);
					}else{
						if(ratio){
							input.val(ratio);
						}else if(currentValue > 1){
							input.val(curValue);
						}
					}
					input.change();
				}
			}
		}
	});

	$('.counter_block input[type=text]').numeric({allow:"."});
	$('.counter_block input[type=text]').live('focus', function(){
		$(this).addClass('focus');
	})
	$('.counter_block input[type=text]').live('blur', function(){
		$(this).removeClass('focus');
	})
	$('.counter_block input[type=text]').live('change', function(e){
		if(!$(this).parents('.basket_wrapp').length){
			var val = $(this).val(),
				tmp_ratio = $(this).parents(".counter_wrapp").find(".to-cart").data('ratio') ? $(this).parents(".counter_wrapp").find(".to-cart").data('ratio') : $(this).parents('tr').first().find("td.buy .to-cart").data('ratio'),
				isDblQuantity = $(this).parents(".counter_wrapp").find(".to-cart").data('float_ratio') ? $(this).parents(".counter_wrapp").find(".to-cart").data('float_ratio') : $(this).parents('tr').first().find("td.buy .to-cart").data('float_ratio'),
				ratio=( isDblQuantity ? parseFloat(tmp_ratio) : parseInt(tmp_ratio, 10));

			if(isDblQuantity)
				ratio = Math.round(ratio*arMShopOptions.JS_ITEM_CLICK.precisionFactor)/arMShopOptions.JS_ITEM_CLICK.precisionFactor;

			if($(this).hasClass('focus'))
				val -= (val % ratio);

			if(parseFloat($(this).parents(".counter_block").find(".plus").data('max'))>0){
				if(val>parseFloat($(this).parents(".counter_block").find(".plus").data('max'))){
					val=parseFloat($(this).parents(".counter_block").find(".plus").data('max'));
					val -= (val % ratio);
				}
			}
			if(val<ratio){
				val=ratio;
			}else if(!parseFloat(val)){
				val=1;
			}

			$(this).parents('.counter_block').parent().parent().find('.to-cart').attr('data-quantity', val);
			$(this).parents('.counter_block').parent().parent().find('.one_click').attr('data-quantity', val);
			$(this).val(val);
		}
	});

	/*slide cart*/
	//if(arMShopOptions['THEME']['SHOW_BASKET_ONADDTOCART'] !== 'N'){
		$(document).on('mouseenter', '#basket_line .basket_normal:not(.empty_cart):not(.bcart) .basket_block ', function() {
			$(this).closest('.basket_normal').find('.popup').addClass('block');
			$(this).closest('.basket_normal').find('.basket_popup_wrapp').stop(true,true).slideDown(150);
		});
		$(document).on('mouseleave', '#basket_line .basket_normal .basket_block ', function() {
			var th=$(this);
			$(this).closest('.basket_normal').find('.basket_popup_wrapp').stop(true,true).slideUp(150, function(){
				th.closest('.basket_normal').find('.popup').removeClass('block');
			});
		});
	//}

	$(document).on('click', '.popup_button_basket', function(){
		var th=$(".to-cart[data-item="+$(this).data("item")+"]");

		var val = th.attr('data-quantity');

		if(!val) $val = 1;

		var tmp_props=th.data("props"),
			props='',
			part_props='',
			add_props='N',
			fill_prop={},
			iblockid = th.data('iblockid'),
			offer = th.data('offers'),
			rid='',
			basket_props='',
			item = th.attr('data-item');

		if(offer!="Y"){
			offer = "N";
		}else{
			basket_props=th.closest('.prices_tab').find('.bx_sku_props input').val();
		}
		if(tmp_props){
			props=tmp_props.split(";");
		}
		if(th.data("part_props")){
			part_props=th.data("part_props");
		}
		if(th.data("add_props")){
			add_props=th.data("add_props");
		}
		if($('.rid_item').length){
			rid=$('.rid_item').data('rid');
		}else if(th.data('rid')){
			rid=th.data('rid');
		}

		fill_prop=fillBasketPropsExt(th, 'prop', 'bx_ajax_text');

		fill_prop.quantity=val;
		fill_prop.add_item='Y';
		fill_prop.rid=rid;
		fill_prop.offers=offer;
		fill_prop.iblockID=iblockid;
		fill_prop.part_props=part_props;
		fill_prop.add_props=add_props;
		fill_prop.props=JSON.stringify(props);
		fill_prop.item=item;
		fill_prop.basket_props=basket_props;

		$.ajax({
			type:"POST",
			url:arMShopOptions['SITE_DIR']+"ajax/item.php",
			data:fill_prop,
			dataType:"json",
			success:function(data){
				$('.basket_error_frame').jqmHide();
				if("STATUS" in data){
					if(data.STATUS === 'OK'){
						getActualBasket(fill_prop.iblockID);
						th.hide();
						th.closest('.counter_wrapp').find('.counter_block').hide();
						th.parents('tr').find('.counter_block_wr .counter_block').hide();
						th.closest('.button_block').addClass('wide');
						th.parent().find('.in-cart').show();

						addBasketCounter(item);
						$('.wish_item[data-item='+item+']').removeClass("added");
						$('.wish_item[data-item='+item+']').find(".value").show();
						$('.wish_item[data-item='+item+']').find(".value.added").hide();
						if($("#basket_line .basket_fly").length && $(window).outerWidth()>768){
							basketFly('open');
						}
						else if($("#basket_line .cart").length)
						{
							if($("#basket_line .cart").is(".empty_cart"))
							{
								$("#basket_line .cart").removeClass("empty_cart").find(".cart_wrapp a.basket_link").removeAttr("href").addClass("cart-call");
								$("#basket_line .cart").removeClass("ecart");
								touchBasket('.cart:not(.empty_cart) .basket_block .link');
							}

							reloadTopBasket('add', $('#basket_line'), 200, 5000, 'Y');

						}
					}else{
						showBasketError(BX.message(data.MESSAGE));
					}
				}else{
					showBasketError(BX.message("CATALOG_PARTIAL_BASKET_PROPERTIES_ERROR"));
				}
			}
		});
	})

	$(document).on( 'click', '.to-cart:not(.read_more)', function(e){
		e.preventDefault();
		var th=$(this);

		var val = $(this).attr('data-quantity');

		if(!val) $val = 1;

		var tmp_props=$(this).data("props"),
			props='',
			part_props='',
			add_props='N',
			fill_prop={},
			iblockid = $(this).data('iblockid'),
			offer = $(this).data('offers'),
			rid='',
			basket_props='',
			item = $(this).attr('data-item');

		if(offer!="Y"){
			offer = "N";
		}else{
			basket_props=$(this).closest('.prices_tab').find('.bx_sku_props input').val();
		}
		if(tmp_props){
			props=tmp_props.split(";");
		}
		if($(this).data("part_props")){
			part_props=$(this).data("part_props");
		}
		if($(this).data("add_props")){
			add_props=$(this).data("add_props");
		}
		if($('.rid_item').length){
			rid=$('.rid_item').data('rid');
		}else if($(this).data('rid')){
			rid=$(this).data('rid');
		}

		fill_prop=fillBasketPropsExt(th, 'prop', th.data('bakset_div'));

		fill_prop.quantity=val;
		fill_prop.add_item='Y';
		fill_prop.rid=rid;
		fill_prop.offers=offer;
		fill_prop.iblockID=iblockid;
		fill_prop.part_props=part_props;
		fill_prop.add_props=add_props;
		fill_prop.props=JSON.stringify(props);
		fill_prop.item=item;
		fill_prop.basket_props=basket_props;

		if(th.data("empty_props")=="N"){
			showBasketError($("#"+th.data("bakset_div")).html(), BX.message("ERROR_BASKET_PROP_TITLE"), "Y", th);
		}else{
			// $.get( arMShopOptions['SITE_DIR']+"ajax/item.php?item="+item+"&quantity="+val+"&rid="+rid+"&add_item=Y"+"&offers="+offer+"&iblockID="+iblockid+"&props="+JSON.stringify(props),
			$.ajax({
				type:"POST",
				url:arMShopOptions['SITE_DIR']+"ajax/item.php",
				data:fill_prop,
				dataType:"json",
				success:function(data){
					getActualBasket(fill_prop.iblockID);
					if(data !==null){
						if("STATUS" in data){
							if(data.MESSAGE_EXT===null)
								data.MESSAGE_EXT='';
							if(data.STATUS === 'OK'){
								th.hide();
								th.closest('.counter_wrapp').find('.counter_block').hide();
								th.parents('tr').find('.counter_block_wr .counter_block').hide();
								th.closest('.button_block').addClass('wide');
								th.parent().find('.in-cart').show();

								addBasketCounter(item);
								$('.wish_item[data-item='+item+']').removeClass("added");
								$('.wish_item[data-item='+item+']').find(".value").show();
								$('.wish_item[data-item='+item+']').find(".value.added").hide();


								if($("#basket_line .basket_fly").length){
									if($(window).outerWidth()>768){
										basketFly('open');
									}else{
										basketFly();
									}
								}
								else if($("#basket_line .cart").length)
								{
									if($("#basket_line .cart").is(".empty_cart"))
									{
										$("#basket_line .cart").removeClass("empty_cart").find(".cart_wrapp a.basket_link").removeAttr("href").addClass("cart-call");
										$("#basket_line .cart").removeClass("ecart");
										touchBasket('.cart:not(.empty_cart) .basket_block .link');
									}

									reloadTopBasket('add', $('#basket_line'), 200, 5000, 'Y');

								}
							}else{
								showBasketError(BX.message(data.MESSAGE)+' <br/>'+data.MESSAGE_EXT);
							}
						}else{
							showBasketError(BX.message("CATALOG_PARTIAL_BASKET_PROPERTIES_ERROR"));
						}
					}else{
						th.hide();
						th.closest('.counter_wrapp').find('.counter_block').hide();
						th.parents('tr').find('.counter_block_wr .counter_block').hide();
						th.closest('.button_block').addClass('wide');
						th.parent().find('.in-cart').show();

						addBasketCounter(item);
						$('.wish_item[data-item='+item+']').removeClass("added");
						$('.wish_item[data-item='+item+']').find(".value").show();
						$('.wish_item[data-item='+item+']').find(".value.added").hide();
						if($("#basket_line .basket_fly").length && $(window).outerWidth()>768){
							basketFly('open');
						}
						else if($("#basket_line .cart").length)
						{
							if($("#basket_line .cart").is(".empty_cart"))
							{
								$("#basket_line .cart").removeClass("empty_cart").find(".cart_wrapp a.basket_link").removeAttr("href").addClass("cart-call");
								$("#basket_line .cart").removeClass("ecart");
								touchBasket('.cart:not(.empty_cart) .basket_block .link');
							}

							reloadTopBasket('add', $('#basket_line'), 200, 5000, 'Y');

						}
					}
				}
			});
		}
	})

	$(document).on('click', '.to-subscribe', function(e){
		e.preventDefault();
		if($(this).is('.auth')){
			$('.avtorization-call.enter').click();
		}
		else{
			var item = $(this).attr('data-item'),
				iblockid = $(this).attr('data-iblockid');
			$(this).hide();
			$(this).parent().find('.in-subscribe').show();
			$.get(arMShopOptions['SITE_DIR'] + 'ajax/item.php?item=' + item + '&subscribe_item=Y',
				$.proxy(function(data){
					$('.wish_item[data-item=' + item + ']').removeClass('added');
					getActualBasket(iblockid);
					$.getJSON(arMShopOptions['SITE_DIR']+'ajax/get_basket_count.php', function(data){
					});
				})
			);
		}
	})

	$(document).on('click', '.in-subscribe', function(e){
		e.preventDefault();
		var item = $(this).attr('data-item'),
			iblockid = $(this).attr('data-iblockid');
		$(this).hide();
		$(this).parent().find('.to-subscribe').show();
		$.get(arMShopOptions['SITE_DIR'] + 'ajax/item.php?item=' + item + '&subscribe_item=Y',
			$.proxy(function(data){
				getActualBasket(iblockid);
				$.getJSON(arMShopOptions['SITE_DIR'] + 'ajax/get_basket_count.php', function(data){
				});
			})
		);
	})

	$(document).on('click', '.wish_item', function(e){
		e.preventDefault();
		var val = $(this).attr('data-quantity'),
			offer = $(this).data('offers'),
			iblockid = $(this).data('iblock'),
			tmp_props=$(this).data("props"),
			props='',
			item = $(this).attr('data-item');
		if(!val) $val = 1;
		if(offer!="Y") offer = "N";
		if(tmp_props){
			props=tmp_props.split(";");
		}

		if(!$(this).hasClass('text')){
			if($(this).hasClass('added')){
				$(this).hide();
				$(this).closest('.wish_item_button').find('.to').show();

				$('.like_icons').each(function(){
					if($(this).find('.wish_item.text[data-item="'+item+'"]').length){
						$(this).find('.wish_item.text[data-item="'+item+'"]').removeClass('added');
						$(this).find('.wish_item.text[data-item="'+item+'"]').find('.value').show();
						$(this).find('.wish_item.text[data-item="'+item+'"]').find('.value.added').hide();
					}
					if($(this).find('.wish_item_button').length){
						$(this).find('.wish_item_button').find('.wish_item[data-item="'+item+'"]').removeClass('added');
						$(this).find('.wish_item_button').find('.wish_item[data-item="'+item+'"]').find('.value').show();
						$(this).find('.wish_item_button').find('.wish_item[data-item="'+item+'"]').find('.value.added').hide();
					}
				})
			}
			else{
				$(this).hide();
				$(this).closest('.wish_item_button').find('.in').addClass('added').show();

				$('.like_icons').each(function(){
					if($(this).find('.wish_item.text[data-item="'+item+'"]').length){
						$(this).find('.wish_item.text[data-item="'+item+'"]').addClass('added');
						$(this).find('.wish_item.text[data-item="'+item+'"]').find('.value').hide();
						$(this).find('.wish_item.text[data-item="'+item+'"]').find('.value.added').css({"display":"inline-block"})
					}
					if($(this).find('.wish_item_button').length){
						$(this).find('.wish_item_button').find('.wish_item[data-item="'+item+'"]').addClass('added');
						$(this).find('.wish_item_button').find('.wish_item[data-item="'+item+'"]').find('.value').hide();
						$(this).find('.wish_item_button').find('.wish_item[data-item="'+item+'"]').find('.value.added').show();
					}
				})
			}
		}else{
			if(!$(this).hasClass('added')){
				$('.wish_item[data-item=' + item + ']').addClass('added');
				$('.wish_item[data-item=' + item + ']').find('.value').hide();
				$('.wish_item[data-item=' + item + ']').find('.value.added').css('display','inline-block');

				$('.like_icons').each(function(){
					if($(this).find('.wish_item_button').length){
						$(this).find('.wish_item_button').find('.wish_item[data-item="'+item+'"]').addClass('added');
						$(this).find('.wish_item_button').find('.wish_item[data-item="'+item+'"]').find('.value').hide();
						$(this).find('.wish_item_button').find('.wish_item[data-item="'+item+'"]').find('.value.added').show();
					}
				})
			}else{
				$('.wish_item[data-item=' + item + ']').removeClass('added');
				$('.wish_item[data-item=' + item + ']').find('.value').show();
				$('.wish_item[data-item=' + item + ']').find('.value.added').hide();

				$('.like_icons').each(function(){
					if($(this).find('.wish_item_button').length){
						$(this).find('.wish_item_button').find('.wish_item[data-item="'+item+'"]').removeClass('added');
						$(this).find('.wish_item_button').find('.wish_item[data-item="'+item+'"]').find('.value').show();
						$(this).find('.wish_item_button').find('.wish_item[data-item="'+item+'"]').find('.value.added').hide();
					}
				})
			}
		}

		$('.in-cart[data-item=' + item + ']').hide();
		$('.to-cart[data-item=' + item + ']').parent().removeClass('wide');
		$('.to-cart[data-item=' + item + ']').show();
		$('.counter_block[data-item=' + item + ']').show();
		if(!$(this).closest('.module-cart').size()){
			// $.get( arMShopOptions['SITE_DIR']+"ajax/item.php?item="+item+"&quantity="+val+"&wish_item=Y"+"&offers="+offer+"&iblockID="+iblockid+"&props="+JSON.stringify(props),
			$.ajax({
				type:"GET",
				url:arMShopOptions['SITE_DIR']+"ajax/item.php",
				data:"item="+item+"&quantity="+val+"&wish_item=Y"+"&offers="+offer+"&iblockID="+iblockid+"&props="+JSON.stringify(props),
				dataType:"json",
				success: function(data){
					getActualBasket(iblockid);
					if(data !==null){
						if(data.MESSAGE_EXT===null)
							data.MESSAGE_EXT='';
						if("STATUS" in data){
							if(data.STATUS === 'OK'){
								if(arMShopOptions['COUNTERS']['USE_BASKET_GOALS'] !== 'N'){
									var eventdata = {goal: 'goal_wish_add', params: {id: item}};
									BX.onCustomEvent('onCounterGoals', [eventdata]);
								}
								if($('.basket_fly').size()){
									basketFly('wish');
								}else{
									reloadTopBasket('wish', $('#basket_line'), 200, 5000, 'N');
								}
							}else{
								showBasketError(BX.message(data.MESSAGE)+' <br/>'+data.MESSAGE_EXT, BX.message("ERROR_ADD_DELAY_ITEM"));
							}
						}else{
							showBasketError(BX.message(data.MESSAGE)+' <br/>'+data.MESSAGE_EXT, BX.message("ERROR_ADD_DELAY_ITEM"));
						}
					}else{
						if($('.basket_fly').size()){
							basketFly('wish');
						}else{
							reloadTopBasket('wish', $('#basket_line'), 200, 5000, 'N');
						}
					}
				}
			});
		}
	})

	// $('.compare_item').live('click', function(e){
	$(document).on('click', '.compare_item', function(e){
		e.preventDefault();
		var item = $(this).attr('data-item');
		var iblockID = $(this).attr('data-iblock');
		if(!$(this).hasClass('text')){
			if($(this).hasClass('added')){
				$(this).hide();
				$(this).closest('.compare_item_button').find('.to').show();

				/*sync other button*/
				$('.like_icons').each(function(){
					if($(this).find('.compare_item.text[data-item="'+item+'"]').length){
						$(this).find('.compare_item.text[data-item="'+item+'"]').removeClass('added');
						$(this).find('.compare_item.text[data-item="'+item+'"]').find('.value').show();
						$(this).find('.compare_item.text[data-item="'+item+'"]').find('.value.added').hide();
					}
					if($(this).find('.compare_item_button').length){
						$(this).find('.compare_item_button').find('.compare_item[data-item="'+item+'"]').removeClass('added');
						$(this).find('.compare_item_button').find('.compare_item[data-item="'+item+'"]').find('.value').show();
						$(this).find('.compare_item_button').find('.compare_item[data-item="'+item+'"]').find('.value.added').hide();
					}
				})
			}
			else{
				$(this).hide();
				$(this).closest('.compare_item_button').find('.in').show();

				/*sync other button*/
				$('.like_icons').each(function(){
					if($(this).find('.compare_item.text[data-item="'+item+'"]').length){
						$(this).find('.compare_item.text[data-item="'+item+'"]').addClass('added');;
						$(this).find('.compare_item.text[data-item="'+item+'"]').find('.value').hide();
						$(this).find('.compare_item.text[data-item="'+item+'"]').find('.value.added').css({"display":"inline-block"});
					}
					if($(this).find('.compare_item_button').length){
						$(this).find('.compare_item_button').find('.compare_item[data-item="'+item+'"]').addClass('added');
						$(this).find('.compare_item_button').find('.compare_item[data-item="'+item+'"]').find('.value.added').show();
						$(this).find('.compare_item_button').find('.compare_item[data-item="'+item+'"]').find('.value').hide();
					}
				})
			}
		}else{
			if(!$(this).hasClass('added')){
				$('.compare_item[data-item=' + item + ']').addClass('added');
				$('.compare_item[data-item=' + item + ']').find('.value').hide();
				$('.compare_item[data-item=' + item + ']').find('.value.added').css('display','inline-block');

				/*sync other button*/
				$('.like_icons').each(function(){
					if($(this).find('.compare_item_button').length){
						$(this).find('.compare_item_button').find('.compare_item[data-item="'+item+'"]').addClass('added');
						$(this).find('.compare_item_button').find('.compare_item[data-item="'+item+'"]').find('.value.added').show();
						$(this).find('.compare_item_button').find('.compare_item[data-item="'+item+'"]').find('.value').hide();
					}
				})
			}else{
				$('.compare_item[data-item=' + item + ']').removeClass('added');
				$('.compare_item[data-item=' + item + ']').find('.value').show();
				$('.compare_item[data-item=' + item + ']').find('.value.added').hide();

				/*sync other button*/
				$('.like_icons').each(function(){
					if($(this).find('.compare_item_button').length){
						$(this).find('.compare_item_button').find('.compare_item[data-item="'+item+'"]').removeClass('added');
						$(this).find('.compare_item_button').find('.compare_item[data-item="'+item+'"]').find('.value').show();
						$(this).find('.compare_item_button').find('.compare_item[data-item="'+item+'"]').find('.value.added').hide();
					}
				})
			}
		}

		$.get(arMShopOptions['SITE_DIR'] + 'ajax/item.php?item=' + item + '&compare_item=Y&iblock_id=' + iblockID,
			$.proxy(function(data){
				getActualBasket(iblockID);
				jsAjaxUtil.InsertDataToNode(arMShopOptions['SITE_DIR'] + 'ajax/show_compare_preview_top.php', 'compare_line', false);
			})
		);
	});

	$('.fancy').fancybox({
		openEffect  : 'fade',
		closeEffect : 'fade',
		nextEffect : 'fade',
		prevEffect : 'fade',
		tpl:{
			closeBtn : '<a title="'+BX.message('FANCY_CLOSE')+'" class="fancybox-item fancybox-close" href="javascript:;"></a>',
			next     : '<a title="'+BX.message('FANCY_NEXT')+'" class="fancybox-nav fancybox-next" href="javascript:;"><span></span></a>',
			prev     : '<a title="'+BX.message('FANCY_PREV')+'" class="fancybox-nav fancybox-prev" href="javascript:;"><span></span></a>'
		},
	});

	/*search click*/
	$('.search_block .icon').on('click', function(){
		var th=$(this);
		if($(this).hasClass('open')){
			$(this).closest('.center_block').find('.search_middle_block').removeClass('active');
			$(this).removeClass('open');
			$(this).closest('.center_block').find('.search_middle_block').find('.noborder').hide();
		}else{
			setTimeout(function(){
				th.closest('.center_block').find('.search_middle_block').find('.noborder').show();
			},100);
			$(this).closest('.center_block').find('.search_middle_block').addClass('active');
			$(this).addClass('open');
		}
	})
	$(document).on('mouseenter', '.display_list .item_wrap', function(){
		$(this).prev().addClass('prev');
	});
	$(document).on('mouseleave', '.display_list .item_wrap', function(){
		$(this).prev().removeClass('prev');
	});
	$(document).on('mouseenter', '.catalog_block .item_wrap', function(){
		$(this).addClass('shadow_delay');
	});
	$(document).on('mouseleave', '.catalog_block .item_wrap', function(){
		$(this).removeClass('shadow_delay');
	});
	$(document).on('click', '.no_goods .button', function(){
		$('.bx_filter .smartfilter .bx_filter_search_reset').trigger('click');
	});

	$(document).on('click', '.more_text_ajax', function(){
		var url=$(this).closest('.right_block').find('.module-pagination .flex-direction-nav .flex-next').attr('href'),
			th=$(this);
		th.addClass('loading');

		$.ajax({
			url: url,
			data: {"ajax_get": "Y"},
			success: function(html){
				var new_html=$.parseHTML(html);
				th.removeClass('loading');
				if($('.display_list').length){
					//$('.display_list').append($(new_html).find('.display_list').html());
					$('.display_list').append(html);
				}else if($('.catalog_block').length){
					$('.catalog_block').append(html);
					touchItemBlock('.catalog_item a');
					$('.catalog_block').ready(function()
					{
						$('.catalog_block').equalize({children: '.catalog_item .cost', reset: true});
						$('.catalog_block').equalize({children: '.catalog_item .item-title', reset: true});
						$('.catalog_block').equalize({children: '.catalog_item .counter_block', reset: true});
						$('.catalog_block').equalize({children: '.catalog_item_wrapp', reset: true});
					})
				}else if($('.module_products_list').length){
					$('.module_products_list tbody').append(html);
				}
				setStatusButton();
				BX.onCustomEvent('onAjaxSuccess');
				$('.bottom_nav').html($(html).find('.bottom_nav').html());
			}
		})
	})

	$(document).on('click', '.bx_compare .tabs-head li', function(){
		var url=$(this).find('.sortbutton').data('href');
		BX.showWait(BX("bx_catalog_compare_block"));
		$.ajax({
			url: url,
			data: {'ajax_action': 'Y'},
			success: function(html){
				history.pushState(null, null, url);
				$('#bx_catalog_compare_block').html(html);
				BX.closeWait();
			}
		})
	})
	var hoveredTrs;
	$(document).on({
		mouseover: function(e){
			var _ = $(this);
			var tbodyIndex = _.closest('tbody').index()+1; //+1 for nth-child
			var trIndex = _.index()+1; // +1 for nth-child
			hoveredTrs = $(e.delegateTarget).find('.data_table_props')
				.children(':nth-child('+tbodyIndex+')')
				.children(':nth-child('+trIndex+')').addClass('hovered');
		},
		mouseleave: function(e){
			if(hoveredTrs)
				hoveredTrs.removeClass('hovered');
		}
	}, '.bx_compare .data_table_props tbody>tr');
	$(document).on('click', '.fancy_offer', function(e){
		e.preventDefault();
		var arPict=[];
		$('.sliders li').each(function(){
			if($(this).hasClass('current')){
				arPict.unshift($(this).data('big'));
			}else{
				arPict.push($(this).data('big'));
			}
		})
		$.fancybox(arPict, {
			openEffect  : 'fade',
			closeEffect : 'fade',
			nextEffect : 'fade',
			prevEffect : 'fade',
			//'type'              : 'image',
			tpl:{
				closeBtn : '<a title="'+BX.message('FANCY_CLOSE')+'" class="fancybox-item fancybox-close" href="javascript:;"></a>',
				next     : '<a title="'+BX.message('FANCY_NEXT')+'" class="fancybox-nav fancybox-next" href="javascript:;"><span></span></a>',
				prev     : '<a title="'+BX.message('FANCY_PREV')+'" class="fancybox-nav fancybox-prev" href="javascript:;"><span></span></a>'
			},
		});
	})

	/*tabs*/
	$(".tabs_section .tabs-head li").live("click", function(){
		if(!$(this).is(".current")){
			$(".tabs_section .tabs-head li").removeClass("current");
			$(this).addClass("current");
			$(".tabs_section ul.tabs_content li").removeClass("current");
			if($(this).attr("id") == "product_reviews_tab"){
				$(".shadow.common").hide(); $("#reviews_content").show();
			}
			else{
				$(".shadow.common").show();
				$("#reviews_content").hide();
				$(".tabs_section ul.tabs_content > li:eq("+$(this).index()+")").addClass("current");
			}
		}
	});
	/*open first section slide*/
	setTimeout(function() {
		$('.jobs_wrapp .item:first .name tr').trigger('click');
	}, 300);

	$('.buy_block .slide_offer').on('click', function(){
		scroll_block($('.tabs_section'));
	});
	$('.share_wrapp .text').on('click', function(){
		$(this).parent().find('.shares').fadeToggle();
	})
	$('html, body').live('mousedown', function(e) {
		e.stopPropagation();
		$('.shares').fadeOut();
		$('.search_middle_block').removeClass('active_wide');
	})
	$('.share_wrapp').find('*').live('mousedown', function(e) {
		e.stopPropagation();
	});
	$(document).on('click', '.reviews-collapse-link', function(){
		$('.reviews-reply-form').slideToggle();
	})
	//$('.form_mobile_block .search_middle_block').html($('.main-nav .search_middle_block').html());

	/*detail order show payments*/
	$('.sale-order-detail-payment-options-methods-info-change-link').on('click', function(){
		$(this).closest('.sale-order-detail-payment-options-methods-info').addClass('opened').siblings().addClass('opened');
	})

	/*touch event*/
	document.addEventListener('touchend', function(event) {
		if(!$(event.target).closest('.menu_item_l1').length){
			$('.menu .menu_item_l1 .child').css({'display':'none'});
			$('.menu_item_l1').removeClass('hover');
		}
		if(!$(event.target).closest('.basket_block').length){
			$('.basket_block .link').removeClass('hover');
			$('.basket_block .basket_popup_wrapp').slideUp();
		}
		if(!$(event.target).closest('.catalog_item').length){
			var tabsContentUnhoverHover = $('.tab:visible').attr('data-unhover') * 1;
			$('.tab:visible').stop().animate({'height': tabsContentUnhoverHover}, 100);
			$('.tab:visible').find('.catalog_item').removeClass('hover');
			$('.tab:visible').find('.catalog_item .buttons_block').stop().fadeOut(233);
			if($('.catalog_block').length){
				$('.catalog_block').find('.catalog_item').removeClass('hover');
				//$('.catalog_block').find('.catalog_item').blur();
			}
		}
	}, false);
	//touchItemBlock('.catalog_item a');
	$(document).on('keyup', '.coupon .input_coupon input', function(){
		if($(this).val().length){
			$(this).removeClass('error');
			$(this).closest('.input_coupon').find('.error').remove();
		}else{
			$(this).addClass('error');
			$("<label class='error'>"+BX.message("INPUT_COUPON")+"</label>").insertBefore($(this));
		}
	})
	// showPhoneMask('input[autocomplete=tel]');
	BX.addCustomEvent(window, "onAjaxSuccess", function(){
		initSelects(document);
		InitOrderCustom();
		// showPhoneMask('input[autocomplete=tel]');

		if($('.catalog_detail').length){
			$('.bx_filter').remove();
		}

		if(arMShopOptions["PAGES"]["ORDER_PAGE"]){
			orderActions();
		}
		/*if(arMShopOptions["PAGES"]["PERSONAL_PAGE"]){
			personalActions();
		}*/
	});
	BX.addCustomEvent(window, "onFrameDataRequestFail", function(response){
		console.log(response);
	});
});

if(!funcDefined('showPhoneMask')){
	showPhoneMask=function(className){
		$(className).inputmask('mask', {'mask': arMShopOptions['THEME']['PHONE_MASK'], 'showMaskOnHover':false });
	}
}

if(!funcDefined('parseUrlQuery')){
	parseUrlQuery=function() {
	    var data = {};
	    if(location.search) {
	        var pair = (location.search.substr(1)).split('&');
	        for(var i = 0; i < pair.length; i ++) {
	            var param = pair[i].split('=');
	            data[param[0]] = param[1];
	        }
	    }
	    return data;
	}
}

if(!funcDefined('getActualBasket')){
	getActualBasket=function(iblockID){
		var data='';
		if(typeof iblockID !=="undefined"){
			data={"iblockID":iblockID}
		}
		$.ajax({
			type:"GET",
			url:arMShopOptions['SITE_DIR']+"ajax/actualBasket.php",
			data:data,
			success: function(data){
				if(!$('.js_ajax').length)
					$('body').append('<div class="js_ajax"></div>');
				$('.js_ajax').html(data);
			}
		});
	}
}

function touchMenu(selector){
	if($(window).outerWidth()>600){
		$(selector).each(function(){
			var th=$(this);
			th.on('touchend', function(e) {
				if (th.find('.child').length && !th.hasClass('hover')) {
					e.preventDefault();
					e.stopPropagation();
					th.siblings().removeClass('hover');
					th.addClass('hover');
					$('.menu .child').css({'display':'none'});
					th.find('.child').css({'display':'block'});
				}
			})
		})
	}else{
		$(selector).off();
	}
}
function touchItemBlock(selector){
	$(selector).each(function(){
		var th=$(this),
			item=th.closest('.catalog_item');
		th.on('touchend', function(e) {
			if (!item.hasClass('hover')) {
				e.preventDefault();
				e.stopPropagation();
				item.siblings().removeClass('hover');
				item.siblings().blur();
				item.closest('.catalog_block').find('.catalog_item').removeClass('hover');
				item.addClass('hover');
				item.addClass('touch');

				var tabsContentHover = th.closest('.tab').attr('data-hover') * 1,
					tabsContentUnhoverHover = th.closest('.tab').attr('data-unhover') * 1;

				th.closest('.tab').stop().animate({'height': tabsContentUnhoverHover}, 100);
				th.closest('.catalog_item').siblings().find('.buttons_block').stop().fadeOut(233)

				th.closest('.tab').fadeTo(100, 1);
				th.closest('.tab').stop().css({'height': tabsContentHover});
				th.closest('.catalog_item').find('.buttons_block').fadeIn(450, 'easeOutCirc');
			}
		})
	})
}
function touchBasket(selector){
	if(arMShopOptions['THEME']['SHOW_BASKET_ONADDTOCART'] !== 'N'){
		if($(window).outerWidth()>600){
			$(document).find(selector).on('touchend', function(e) {
				if ($(this).parent().find('.basket_popup_wrapp').length && !$(this).hasClass('hover')) {
					e.preventDefault();
					e.stopPropagation();
					$(this).addClass('hover');
					$(this).parent().find('.basket_popup_wrapp').slideDown();
				}
			})
		}else{
			$(selector).off();
		}
	}
}
function initFull(){
	initSelects(document);
	initHoverBlock(document);
	//$(window).resize();
	touchItemBlock('.catalog_item a');
	InitOrderCustom();
	orderActions();
	basketActions();
	// personalActions();
}

if(!funcDefined('checkMinPrice')){
	checkMinPrice = function(){
		if(arMShopOptions["PAGES"]["BASKET_PAGE"]){
			var summ_raw=$('#allSum_FORMATED').text().replace(/[^0-9\.]/g,''),
				summ=parseFloat(summ_raw);
			if(!$('.catalog_back').length)
				$('.bx_ordercart_order_pay_center').prepend('<a href="/catalog/" class="catalog_back button transparent big_btn grey_br">'+BX.message("BASKET_CONTINUE_BUTTON")+'</a>');

			if(arMShopOptions["PRICES"]["MIN_PRICE"]){
				if(arMShopOptions["PRICES"]["MIN_PRICE"]>summ){
					if(!$('.icon_error_wrapper').length){
						$('.bx_ordercart_order_pay_center').prepend('<div class="icon_error_wrapper"><div class="icon_error_block">'+BX.message("MIN_ORDER_PRICE_TEXT").replace("#PRICE#", jsPriceFormat(arMShopOptions["PRICES"]["MIN_PRICE"]))+'</div></div>');
					}

					if($('.oneclickbuy.fast_order').length)
						$('.oneclickbuy.fast_order').remove();

					if($('.bx_ordercart_order_pay .checkout').length)
						$('.bx_ordercart_order_pay .checkout').remove();
				}else{
					if($('.icon_error_wrapper').length)
						$('.icon_error_wrapper').remove();

					if($('.bx_ordercart_order_pay .checkout').length)
						$('.bx_ordercart .bx_ordercart_order_pay .checkout').css('opacity','1');
					else
						$('.bx_ordercart_order_pay_center').append('<a href="javascript:void(0)" onclick="checkOut();" class="checkout" style="opacity: 1;">'+BX.message("BASKET_ORDER_BUTTON")+'</a>');
					if(!$('.oneclickbuy.fast_order').length && arMShopOptions["THEME"]["SHOW_ONECLICKBUY_ON_BASKET_PAGE"] == "Y")
						$('.bx_ordercart_order_pay_center').append('<span class="oneclickbuy button big_btn fast_order" onclick="oneClickBuyBasket()">'+BX.message("BASKET_QUICK_ORDER_BUTTON")+'</span>');


				}
			}else{
				$('.bx_ordercart .bx_ordercart_order_pay .checkout').css('opacity','1');
				if(!$('.oneclickbuy.fast_order').length && arMShopOptions["THEME"]["SHOW_ONECLICKBUY_ON_BASKET_PAGE"] == "Y")
					$('.bx_ordercart_order_pay_center').append('<span class="oneclickbuy button big_btn fast_order" onclick="oneClickBuyBasket()">'+BX.message("BASKET_QUICK_ORDER_BUTTON")+'</span>');
			}
		}
	}
}

if(!funcDefined('basketActions')){
	basketActions = function(){
		if(arMShopOptions["PAGES"]["BASKET_PAGE"]){
			checkMinPrice();

			$('.bx_sort_container').append('<div class="top_control basket_sort"><span class="delete_all button grey_br transparent remove_all_basket">'+BX.message("BASKET_CLEAR_ALL_BUTTON")+'</span></div>');
			if(arMShopOptions["THEME"]["SHOW_BASKET_PRINT"]=="Y"){
				// $('.bx_sort_container .top_control').prepend('<span class="basket_print button grey_br transparent">'+BX.message("BASKET_PRINT_BUTTON")+'</span>');
				$('<div class="basket_print"><i></i><div>'+BX.message("BASKET_PRINT_BUTTON")+'</div></div>').insertAfter($('.catalog_back'));
			}
			$('.bx_sort_container .top_control .delete_all').data("type",$('.bx_sort_container a.current').index());
			$('.bx_ordercart .bx_ordercart_coupon #coupon').wrap('<div class="input"></div>');

			$('.bx_sort_container > a').on('click', function() {
				$('.bx_sort_container .top_control .delete_all').data("type", $(this).index());
			});

			$('.basket_print').on('click', function() {
				 window.open(location.pathname+"?print=Y",'_blank');
			});

			$('.delete_all').on('click', function() {
				$.post( arMShopOptions['SITE_DIR']+"ajax/action_basket.php", "TYPE="+$(this).data('type')+"&CLEAR_ALL=Y", $.proxy(function( data ) {
					location.reload();
				}));
			});

			$('.bx_item_list_section .bx_catalog_item').sliceHeight({row:'.bx_item_list_slide', item:'.bx_catalog_item'});

			BX.addCustomEvent('onAjaxSuccess', function() {
				checkMinPrice();

				var errorText = $.trim($('#warning_message').text());
				$('#basket_items_list .error_text').detach();
				if (errorText != '') {
					$('#warning_message').hide().text('');
					$('#basket_items_list').prepend('<div class="error_text">' +errorText+ '</div>');
				}
			});

			// open delayed tab
			if(window.location.hash === '#tab_DelDelCanBuy'){
				if(typeof showBasketItemsList === 'function'){
					if($('#delayed_items>tbody>tr').length){
						showBasketItemsList(2);
					}
				}
			}
		}
	}
}

if(!funcDefined('orderActions')){
	orderActions = function(){
		if(arMShopOptions["PAGES"]["ORDER_PAGE"]){
			if($('.bx-soa-cart-total').length){
				if(!$('.change_basket').length)
					$('.bx-soa-cart-total').prepend('<div class="change_basket">'+BX.message("BASKET_CHANGE_TITLE")+'<a href="'+arMShopOptions["SITE_DIR"]+'basket/" class="change_link">'+BX.message("BASKET_CHANGE_LINK")+'</a></div>');
				if(typeof (BX.Sale.OrderAjaxComponent) == "object"){
					if(arMShopOptions['COUNTERS']['USE_FULLORDER_GOALS'] !== 'N'){
						if(typeof BX.Sale.OrderAjaxComponent.reachgoalbegin === 'undefined'){
							BX.Sale.OrderAjaxComponent.reachgoalbegin = true;
							var eventdata = {goal: 'goal_fullorder_begin'};
							BX.onCustomEvent('onCounterGoals', [eventdata])
						}
					}
					if(BX.Sale.OrderAjaxComponent.hasOwnProperty("params")){
						$('.bx-soa-cart-total .change_link').attr('href', BX.Sale.OrderAjaxComponent.params.PATH_TO_BASKET);
						if(arMShopOptions["PRICES"]["MIN_PRICE"]){
							if(arMShopOptions["PRICES"]["MIN_PRICE"]>Number(BX.Sale.OrderAjaxComponent.result.TOTAL.ORDER_PRICE)){
								$('<div class="fademask_ext"></div>').appendTo($('body'));
								location.href=BX.Sale.OrderAjaxComponent.params.PATH_TO_BASKET;
							}
						}
					}
					if(checkCounters() && typeof (BX.Sale.OrderAjaxComponent.oldSaveOrder) === "undefined" && typeof (BX.Sale.OrderAjaxComponent.saveOrder) !== 'undefined'){
						BX.Sale.OrderAjaxComponent.oldSaveOrder = BX.Sale.OrderAjaxComponent.saveOrder;
						BX.Sale.OrderAjaxComponent.saveOrder = function(result){
							var res = BX.parseJSON(result);
							if (res && res.order){
								if (!res.order.SHOW_AUTH){
									if (res.order.REDIRECT_URL && res.order.REDIRECT_URL.length && (!res.order.ERROR || BX.util.object_keys(res.order.ERROR).length < 1)){
										if((arMatch = res.order.REDIRECT_URL.match(/ORDER_ID\=[^&=]*/g)) && arMatch.length && (_id = arMatch[0].replace(/ORDER_ID\=/g, '', arMatch[0]))){
											$.ajax({
												url:arMShopOptions['SITE_DIR']+"ajax/check_order.php",
												dataType: "json",
												type: "POST",
												data: { "ID": _id },
												success: function(id){
													if(parseInt(id)){
														purchaseCounter(parseInt(id), BX.message('FULL_ORDER'), function(d){
															if(typeof d == 'object'){
																if(typeof BX.localStorage !== 'undefined'){
																	BX.localStorage.set('gtm_e_' + _id, d, 60);
																}
															}
															BX.Sale.OrderAjaxComponent.oldSaveOrder(result);
														});
													}
													else{
														BX.Sale.OrderAjaxComponent.oldSaveOrder(result);
													}
												},
												error: function(){
													BX.Sale.OrderAjaxComponent.oldSaveOrder(result);
												}
											})
										}
										else{
											BX.Sale.OrderAjaxComponent.oldSaveOrder(result);
										}
									}
									else{
										BX.Sale.OrderAjaxComponent.oldSaveOrder(result);
									}
								}
								else{
									BX.Sale.OrderAjaxComponent.oldSaveOrder(result);
								}
							}
							else{
								BX.Sale.OrderAjaxComponent.oldSaveOrder(result);
							}
						}
					}
				}
			}
		}
	}
}

if(!funcDefined('personalActions')){
	personalActions = function(){
		if(arMShopOptions["PAGES"]["PERSONAL_PAGE"]){
			if($('.row.col-md-12.col-sm-12').length){
				$('<div class="top_wrapper"></div>').appendTo($('.row.col-md-12.col-sm-12'));
				$('.row.col-md-12.col-sm-12 a').each(function(){
					$(this).appendTo($('.top_wrapper'));
				})
			}
		}
	}
}

var isFrameDataReceived = false;
if (typeof window.frameCacheVars !== "undefined")
{
	BX.addCustomEvent("onFrameDataReceived", function (json){
		initFull();
		isFrameDataReceived = true;
	});
} else {
	$( document ).ready(initFull);
}

BX.addCustomEvent('onCounterGoals', function(eventdata){
	if(arMShopOptions['COUNTERS']['USE_YA_COUNTER'] === 'Y'){
		var idCounter = arMShopOptions['COUNTERS']['YA_COUNTER_ID'];
		idCounter = parseInt(idCounter);

		if(typeof eventdata != 'object'){
			eventdata = {goal: 'undefined'};
		}
		if(typeof eventdata.goal != 'string'){
			eventdata.goal = 'undefined';
		}

		if(idCounter){
			try{
				waitCounter(idCounter, 50, function(){
					var obCounter = window['yaCounter' + idCounter];
					if(typeof obCounter == 'object'){
						obCounter.reachGoal(eventdata.goal);
					}
				});
			}
			catch(e){
				console.error(e)
			}
		}
		else{
			console.info('Bad counter id!', idCounter);
		}
	}
})