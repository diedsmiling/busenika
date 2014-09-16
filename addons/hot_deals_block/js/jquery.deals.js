// $Id: jquery.deals.js 8835 2010-02-11 11:19:07Z alexions $

function Deals(all_items, parent_elm, block_id)
{
	this.elements_count = 4;
	this.position = 0;
	this.speed = 70;
	this.max_loading_time = 70; // 7 sec
	this.obj_image = [];
	this.config = [];
	this.all_items = all_items;
	this.parent_elm = parent_elm;
	this.all_items_count = all_items.length;
	this.deal_items = this.all_items;
	this.deal_items_count = this.all_items_count;
	this.block_id = block_id;

	this.config['elements'] = 0;
	this.config['use_delay'] = true;
	this.config['direction'] = [];

	this.init();
};

Deals.prototype = {
	init: function()
	{
		var _deals = this;
		
		$('.cm-deals-left', this.parent_elm).click(function () {
			_deals.shift_left(_deals);
		});
		$('.cm-deals-right', this.parent_elm).click(function () {
			_deals.shift_right(_deals);
		});
		
		
		$('.cm-deals-category', this.parent_elm).click(function () {
			_deals.change_elements($(this).attr('name'));
		});
		
		this.change_elements(0);
	},
	
	delay: function(time, func)
	{
		setTimeout(func, time);
	},
	
	use_pagination: function(idx, direction)
	{
		this.position = idx;
		
		this.set_pagination(this.deal_items_count, (idx == 0) ? 0 : idx / this.elements_count);
		this.get_html_items(idx, this.deal_items, true, direction);
	},
	
	set_pagination: function(count, idx)
	{
		var pages = Math.round(count / this.elements_count);
		var ret = "";
		var add2end = 0;
		var add2start = 0;
		var output_class;
		
		var end_dif = this.elements_count - idx;
		if (end_dif <= this.elements_count && end_dif >= 0) {
			add2end += end_dif;
		}
		
		var start_dif = this.elements_count - (pages - idx - 1);
		if (start_dif <= this.elements_count && start_dif >= 0) {
			add2start += start_dif;
		}
		
		pagination_list = $(".cm-deals-pagination-list", this.parent_elm);
		
		if (pages == 1) {
			pagination_list.css('visibility', "hidden");
		} else {
			for (var i = 0; i < pages; i++) {
				if (i >= (idx - this.elements_count - add2start) && i <= (idx + this.elements_count + add2end)) {
					if (i == idx) {
						output_class = 'pagination-selected-page';
						href = i + 1;
					} else {
						output_class = '';
						href = "<a name='" + i*this.elements_count + "' class='cm-deals-pagination'>" + (i+1) + "</a>";
					}
					ret += "<span class='" + output_class + "'>" + href + "</span>&nbsp;";
				}
			}
			
			pagination_list.html(ret);
			pagination_list.css('visibility', "visible");
			
			var _deals = this;
			
			$('.cm-deals-pagination', this.parent_elm).click(function () {
				_deals.use_pagination(parseInt($(this).attr('name')), 'right');
			});
		}
	},
	
	add_spacers: function(items, count)
	{
		var differ;
		
		((count % this.elements_count) == 0) ? differ = 0 : differ = this.elements_count - (count % this.elements_count);
	
		for (i = count; i < count + (differ); i++) {
			items[i] = {name: '', link: '', image: images_dir + '/spacer.gif', width: 0, height: 0};
		}
	
		count += differ;
	
		return {items: items, count: count};
	},
	
	filter_elements: function(cat_id, items)
	{
		var elements = [];
	
		for (i in items) {
			if (items[i].cat_id == cat_id) {
				elements.push(items[i]);
			}
		}
	
		ret = this.add_spacers(elements, elements.length);
	
		return {items: ret.items, count: ret.count};
	},
	
	change_elements: function(cat_id)
	{
		if (cat_id == 0) {
			ret = this.add_spacers(this.all_items, this.all_items.length);
			this.deal_items = ret.items;
			this.deal_items_count = ret.count;
			
		} else {
			ret = this.filter_elements(cat_id, this.all_items);
			this.deal_items = ret.items;
			this.deal_items_count = ret.count;
		}
		
		this.mark_category(cat_id);
		
		if (this.deal_items_count <= this.elements_count) {
			$(".cm-deals-left", this.parent_elm).hide();
			$(".cm-deals-right", this.parent_elm).hide();
			
		} else {
			$(".cm-deals-left", this.parent_elm).show();
			$(".cm-deals-right", this.parent_elm).show();
		}
		
		this.position = 0;
		this.set_pagination(this.deal_items_count, 0);
		this.get_html_items(0, this.deal_items);
	},
	
	mark_category: function(cat_id)
	{
		$('.active', this.parent_elm).removeClass('active');
		
		$('.cm-deals-category', this.parent_elm).each(function(key, elem) {
			if (parseInt($(elem).attr('name')) == cat_id) {
				$(elem).addClass('active');
			}
		});
	},
	
	load_images: function()
	{
		use_delay = this.config['use_delay'];
		elements = this.config['elements'];
		direction = this.config['direction'];
		_deals = this;
		
		if (use_delay) {
			_deals.delay(_deals.speed,function(){
				$(".cm-deals-item-" + direction[0], _deals.parent_elm).html(elements[direction[0]]);
				_deals.delay(_deals.speed,function(){
					$(".cm-deals-item-" + direction[1], _deals.parent_elm).html(elements[direction[1]]);
					_deals.delay(_deals.speed,function(){
						$(".cm-deals-item-" + direction[2], _deals.parent_elm).html(elements[direction[2]]);
						_deals.delay(_deals.speed,function(){
							$(".cm-deals-item-" + direction[3], _deals.parent_elm).html(elements[direction[3]]);
						});
					});
				});
			});
			
		} else {
			for (i in elements) {
				$(".cm-deals-item-" + i, this.parent_elm).html(elements[i]);
			}
		}
		
		jQuery.toggleStatusBox('hide');
	},
	
	check_images: function(count)
	{
		var is_loaded = true;
		
		for (var j = 0; j <= this.elements_count - 1; j++) {
			if (!this.obj_image[j].complete) {
				is_loaded = false;
			}
		}
		
		count++;
		
		if (is_loaded || count > this.max_loading_time) {
			this.load_images();
		} else {
			var _deals = this;
			setTimeout(function () {
				_deals.check_images(count)
			}, 100);
		}
	},
	
	get_html_items: function(idx, items, use_delay, direction)
	{
		var elements = [];
		var direct = [];
		var preimages = [];
		
		loaded = 0;
		
		if (direction == "left") {
			direction = ['0', '1', '2', '3'];
		} else {
			direction = ['3', '2', '1', '0'];
		}
	
		jQuery.toggleStatusBox('show', lang.loading);
	
		if ($(".hot-deals-list", this.parent_elm)) {
			var j = 0;
			for (var i = idx; i <= idx + this.elements_count - 1; i++) {
				if (items[i].link == '') {
					elements.push('<div class="image-border center">&nbsp;</div>');
				} else {
					elements.push('<div class="image-border center"><a href="' + items[i].link + '"><img src="' + items[i].image + '" width="' + items[i].width + '" height="' + items[i].height + '" alt="' + items[i].name + '" border="0" /></a><br /><a href="' + items[i].link + '"><span class="deals-title">' + this.truncate(items[i].name) +'</span></a></div>');
				}
				preimages[j] = items[i].image;
				j++;
			}
		}
	
		this.config['elements'] = elements;
		this.config['direction'] = direction;
		this.config['use_delay'] = use_delay;
		
		for (j = 0; j <= this.elements_count - 1; j++) {
			this.obj_image[j] = new Image();
			this.obj_image[j].src = preimages[j];
		}
		
		this.check_images(0);
	},
	
	shift_left: function(deal_class)
	{
		deal_class.position = (deal_class.position == 0) ? deal_class.deal_items_count - this.elements_count : deal_class.position - this.elements_count;
		deal_class.use_pagination(deal_class.position, 'left');
	},
	
	shift_right: function(deal_class)
	{
		deal_class.position = ((deal_class.position + this.elements_count) == deal_class.deal_items_count) ? 0 : deal_class.position + this.elements_count;
		deal_class.use_pagination(deal_class.position, 'right');
	},
	
	truncate: function(str)
	{
		if (str) {
			var len = 24;
	
			if (str.length > len) {
				str = str.substring(0, len);
				str = str.replace(/\w+$/, '')+'...';
			}
			return str;
		}
	
		return '';
	}
};