/*
Classes:
	CPageSwitcher
*/

function CPageSwitcher(skinName)
{
	this._skinName = skinName;
	this._mainCont = null;
	this._pagesCont = null;
	this._count = 0;
	this._perPage = 0;
}

CPageSwitcher.prototype = {
	Show: function (page, perPage, count, beginOnclick, endOnclick)
	{
		this._count = count;
		this._perPage = perPage;
		if (count > perPage) {
			var strPages = '';
			var pagesCount = Math.ceil(count/perPage);
			if (pagesCount > 4)
			{
				var firstPage = page - 2;
				if (firstPage < 1) firstPage = 1;
				var lastPage = firstPage + 4;
				if (lastPage > pagesCount)
				{
					lastPage = pagesCount;
					firstPage = lastPage - 4;
				}
			} else {
				var firstPage = 1;
				var lastPage = pagesCount;
			}
			if (firstPage != lastPage) {
				if (firstPage > 1){
					strPages += '<a href="#" onclick="' + beginOnclick + '1' + endOnclick + ' return false;"><img title="First Page" style="width: 8px; height: 9px;" src="skins/' + this._skinName + '/page_switchers/inbox_first_page.gif" /></a>';
					strPages += '<a href="#" onclick="' + beginOnclick + firstPage + endOnclick + ' return false;"><img title="Previous Page" style="width: 5px; height: 9px;" src="skins/' + this._skinName + '/page_switchers/inbox_prev_page.gif" /></a>';
				}
				for (var i = firstPage; i <= lastPage; i++)
				{
					if (page == i)
						strPages += '<font>' + i + '</font>';
					else
						strPages += '<a href="#" onclick="' + beginOnclick + i + endOnclick + ' return false;">' + i + '</a>';
				}
				if (pagesCount > lastPage){
					strPages += '<a href="#" onclick="' + beginOnclick + lastPage + endOnclick + ' return false;"><img title="Next Page" style="width: 5px; height: 9px;" src="skins/' + this._skinName + '/page_switchers/inbox_next_page.gif" /></a>';
					strPages += '<a href="#" onclick="' + beginOnclick + pagesCount + endOnclick + ' return false;"><img title="Last Page" style="width: 8px; height: 9px;" src="skins/' + this._skinName + '/page_switchers/inbox_last_page.gif" /></a>';
				}
				this._mainCont.className = 'wm_inbox_page_switcher';
				this._pagesCont.innerHTML = strPages;
			}
		}
	},
	
	GetLastPage: function (removeCount)
	{
		var count = this._count - removeCount;
		var perPage = this._perPage;
		var page = Math.ceil(count/perPage);
		if (page < 1) page = 1;
		return page;
	},
	
	Hide: function ()
	{
		this._mainCont.className = 'wm_hide';
	},

	Replace: function (obj)
	{
		var oBounds = GetBounds(obj);
		var ps = this._mainCont;
		ps.style.top = (oBounds.Top + 3) + 'px';
		ps.style.left = (oBounds.Left + oBounds.Width - ps.offsetWidth - 18) + 'px';
	},
	
	ChangeSkin: function (skinName)
	{
		this._skinName = skinName;
	},
	
	Build: function ()
	{
		var tbl = CreateChild(document.body, 'table');
		this._mainCont = tbl;
		tbl.className = 'wm_hide';
		var tr = tbl.insertRow(0);
		var td = tr.insertCell(0);
		td.className = 'wm_inbox_page_switcher_left';
		td = tr.insertCell(1);
		this._pagesCont = td;
		td.className = 'wm_inbox_page_switcher_pages';
		td = tr.insertCell(2);
		td.className = 'wm_inbox_page_switcher_right';
	}
}

