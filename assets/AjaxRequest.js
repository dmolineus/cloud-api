/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2012 Leo Feyer
 * 
 * @package   cloud-api 
 * @author    David Molineus <http://www.netzmacht.de>
 * @license   GNU/LGPL 
 * @copyright Copyright 2012 David Molineus netzmacht creative 
 **/

/**
 * Toggle the cloud api file tree input field
 * modified contao function
 * 
 * @author Leo Feyer <http://contao.org>
 * @author    David Molineus <http://www.netzmacht.de>
 * @param object
 * @param string
 * @param string
 * @param string
 * @param integer
 * @return boolean
 * 
 */
AjaxRequest.toggleCloudFiletree = function (el, id, field, name, level)
{
	el.blur();
	var item = $(id);
	var image = $(el).getFirst('img');

	if (item) {
		if (item.getStyle('display') == 'none') {
			item.setStyle('display', 'inline');
			image.src = image.src.replace('folPlus.gif', 'folMinus.gif');
			$(el).store('tip:title', Contao.lang.collapse);
			new Request.Contao({field:el}).post({'action':'toggleCloudFiletree', 'id':id, 'state':1, 'REQUEST_TOKEN':Contao.request_token});
		} else {
			item.setStyle('display', 'none');
			image.src = image.src.replace('folMinus.gif', 'folPlus.gif');
			$(el).store('tip:title', Contao.lang.expand);
			new Request.Contao({field:el}).post({'action':'toggleCloudFiletree', 'id':id, 'state':0, 'REQUEST_TOKEN':Contao.request_token});
		}
		return false;
	}

	new Request.Contao({
		field: el,
		evalScripts: true,
		onRequest: AjaxRequest.displayBox(Contao.lang.loading + ' …'),
		onSuccess: function(txt, json) {
			var li = new Element('li', {
				'id': id,
				'class': 'parent',
				'styles': {
					'display': 'inline'
				}
			});

			var ul = new Element('ul', {
				'class': 'level_' + level,
				'html': txt
			}).inject(li, 'bottom');

			li.inject($(el).getParent('li'), 'after');
			$(el).store('tip:title', Contao.lang.collapse);
			image.src = image.src.replace('folPlus.gif', 'folMinus.gif');
			AjaxRequest.hideBox();

			// HOOK
			window.fireEvent('ajax_change');
		}
	}).post({'action':'loadCloudFiletree', 'id':id, 'level':level, 'field':field, 'name':name, 'state':1, 'REQUEST_TOKEN':Contao.request_token});

	return false;
}
