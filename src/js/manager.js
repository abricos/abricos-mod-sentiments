/**
* @version $Id: manager.js 1457 2012-04-08 06:13:49Z roosit $
* @package Abricos
* @copyright Copyright (C) 2008 Abricos All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/**
 * @module Sentiments
 * @namespace Brick.mod.sentiments
 */
var Component = new Brick.Component();
Component.requires = {
    mod:[
         {name: 'sys', files: ['data.js', 'container.js', 'widgets.js', 'wait.js']}
    ]
};
Component.entryPoint = function(NS){
	var Dom = YAHOO.util.Dom,
		E = YAHOO.util.Event,
		L = YAHOO.lang;

	var API = NS.API;
	
	if (!NS.data){
		NS.data = new Brick.util.data.byid.DataSet('sentiments');
	}
	var DATA = NS.data;
	
	var LW = Brick.widget.LayWait;
	
	var buildTemplate = this.buildTemplate;
	
	/**
	 * Панель "Список новостей".
	 * 
	 * @class SentimentsListPanel
	 */
	var SentimentsListPanel = function(){
		SentimentsListPanel.superclass.constructor.call(this);
	};
	YAHOO.extend(SentimentsListPanel, Brick.widget.Dialog, {
		initTemplate: function(){
			return buildTemplate(this, 'panel').replace('panel'); 
		},
		onLoad: function(){
			
			this.sentimentsListWidget = new NS.SentimentsListWidget(this._TM.getEl('panel.container'));
			
			var firstRender = true, __self = this;
			this.sentimentsListWidget.parentRender = this.sentimentsListWidget.render;
			this.sentimentsListWidget.render = function(){
				this.parentRender();
				if (firstRender){
					__self.center();
				}
				firstRender = false;
			};
		},
		destroy: function(){
			this.sentimentsListWidget.destroy();
			SentimentsListPanel.superclass.destroy.call(this);
		},
		onClick: function(el){
			if (el.id == this._TId['panel']['bclose']){
				this.close(); return true;
			}
			return false;
		}
	});
	
	NS.SentimentsListPanel = SentimentsListPanel;	
	
	var SentimentsListWidget = function(el){
		
		var TM = buildTemplate(this, 'widget,table,row,rowwait,rowdel,btnpub');
		
		var config = {
			rowlimit: 10,
			tables: {
				'list': 'sentimentslist',
				'count': 'sentimentscount'
			},
			tm: TM,
			paginators: ['widget.pagtop', 'widget.pagbot'],
			DATA: DATA
		};
		SentimentsListWidget.superclass.constructor.call(this, el, config);    
	};
	
    YAHOO.extend(SentimentsListWidget, Brick.widget.TablePage, {
    	initTemplate: function(){
    		return this._T['widget'];
    	},
    	renderTableAwait: function(){
    		this._TM.getEl("widget.table").innerHTML = this._TM.replace('table', {
    			'scb': '', 'rows': this._T['rowwait']
    		});
    	},
		renderRow: function(di){
    		return this._TM.replace(di['dd']>0 ? 'rowdel' : 'row', {
    			'dl': Brick.dateExt.convert(di['dl']),
    			'tl': di['tl'],
    			'dp': (di['dp']>0 ? Brick.dateExt.convert(di['dp']) : this._T['btnpub']),
    			'prv': '/sentiments/'+di['id']+'/',
    			'scb': '',
    			'id': di['id']
			});
    	},
    	renderTable: function(lst){
    		this._TM.getEl("widget.table").innerHTML = this._TM.replace('table', {
    			'scb': '', 'rows': lst
    		}); 
    	}, 
    	onClick: function(el){
    		var TM = this._TM, T = this._T, TId = this._TId;
    		
    		switch(el.id){
    		case TId['widget']['refresh']: this.refresh(); return true;
    		case TId['widget']['btnnew']: this.create(); return true;
    		case TId['widget']['rcclear']:
				this.recycleClear();
    			return true;
    		}
		
			var prefix = el.id.replace(/([0-9]+$)/, '');
			var numid = el.id.replace(prefix, "");
			
			switch(prefix){
			case (TId['rowdel']['restore']+'-'): this.restore(numid); return true;
			case (TId['row']['remove']+'-'): this.remove(numid); return true;
			case (TId['row']['edit']+'-'): this.edit(numid); return true;
			case (TId['btnpub']['id']+'-'): this.publish(numid); return true;
			}
			return false;
    	},
    	
		changeStatus: function(commentId){
    		var rows = this.getRows();
    		var row = rows.getById(commentId);
    		row.update({
    			'st': row.cell['st'] == 1 ? 0 : 1,
    			'act': 'status'
    		});
    		row.clearFields('st,act');
    		this.saveChanges();
		},
		_createWait: function(){
			return new LW(this._TM.getEl("widget.table"), true);
		},
		_ajax: function(data){
			var lw = this._createWait(), __self = this;
			Brick.ajax('sentiments',{
				'data': data,
				'event': function(request){
					lw.hide();
					__self.refresh();
				}
			});
		},
		create: function(){
			this.edit(0);
		},
		edit: function(sentimentsid){
			var lw = this._createWait();
			Brick.f('sentiments', 'editor', 'showEditorPanel', sentimentsid, function(){
				lw.hide();
			});
		},
		remove: function(sentimentsid){
			this._ajax({'type': 'sentiments', 'do': 'remove', 'id': sentimentsid});
		},
		restore: function(sentimentsid){
			this._ajax({'type': 'sentiments', 'do': 'restore', 'id': sentimentsid});
		},
		recycleClear: function(){
			this._ajax({'type': 'sentiments', 'do': 'rclear'});
		},
		publish: function(sentimentsid){
			this._ajax({'type': 'sentiments', 'do': 'publish', 'id': sentimentsid});
		}
    });

	NS.SentimentsListWidget = SentimentsListWidget;


	API.showSentimentsListPanel = function(){
		var widget = new NS.SentimentsListPanel();
		DATA.request(true);
		return widget;
	};
	
	/**
	 * Показать виджет "Список новостей"
	 * 
	 * @method showSentimentsListWidget
	 * @param {String | HTMLElement} container HTML элемент в котором будет отображен 
	 * виджет.
	 */
	API.showSentimentsListWidget = function(container){
		var widget = new NS.SentimentsListWidget(container);
		DATA.request(true);
		return widget;
	};

};
