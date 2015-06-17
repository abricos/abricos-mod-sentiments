/**
 * @module Sentiments
 * @namespace Brick.mod.sentiments
 */

var Component = new Brick.Component();
Component.requires = {
    yahoo: ['tabview', 'dragdrop'],
    mod: [
        {name: 'sentiments', files: ['roles.js']},
        {name: 'sys', files: ['editor.js', 'container.js', 'data.js', 'old-form.js']},
        {name: 'widget', files: ['calendar.js']}
    ]
};
Component.entryPoint = function(NS){

    var Dom = YAHOO.util.Dom,
        L = YAHOO.lang;

    var API = NS.API,
        R = NS.roles;

    NS.data = NS.data || new Brick.util.data.byid.DataSet('sentiments');

    var DATA = NS.data;

    var buildTemplate = this.buildTemplate;

    var EditorPanel = function(sentimentsId){
        this.sentimentsId = sentimentsId * 1 || 0;

        EditorPanel.superclass.constructor.call(this, {
            fixedcenter: true,
            width: '830px'
        });
    };
    YAHOO.extend(EditorPanel, Brick.widget.Dialog, {
        el: function(name){
            return Dom.get(this._TId['editor'][name]);
        },
        elv: function(name){
            return Brick.util.Form.getValue(this.el(name));
        },
        setelv: function(name, value){
            Brick.util.Form.setValue(this.el(name), value);
        },
        initTemplate: function(){
            buildTemplate(this, 'editor');
            return this._T['editor'];
        },
        onLoad: function(){

            var TM = this._TM, gel = function(n){
                return TM.getEl('editor.' + n);
            };
            new YAHOO.widget.TabView(gel('tabpage'));

            gel('bcancel').disabled = 'disabled';

            var Editor = Brick.widget.Editor;

            this.editorIntro = new Editor(gel('bodyint'), {
                'mode': Editor.MODE_VISUAL
            });

            this.editorBody = new Editor(gel('bodyman'), {
                'mode': Editor.MODE_VISUAL
            });

            this.pubDateTime = new Brick.mod.widget.DateInputWidget(gel('pdt'), {
                'date': null,
                'showTime': true
            });

            // менеджер файлов
            if (Brick.componentExists('filemanager', 'api')){
                this.el('fm').style.display = '';
                this.el('fmwarn').style.display = 'none';
            }

            if (this.sentimentsId > 0){
                this.initTables();
                if (DATA.isFill(this.tables)){
                    this.renderElements();
                }
                DATA.onComplete.subscribe(this.dsComplete, this, true);
            } else {
                this.renderElements();
            }
        },

        dsComplete: function(type, args){
            if (args[0].checkWithParam('sentiments', {id: this.sentimentsId})){
                this.renderElements();
            }
        },

        initTables: function(){
            this.tables = {'sentiments': DATA.get('sentiments', true)};
            this.rows = this.tables['sentiments'].getRows({id: this.sentimentsId});
        },

        renderElements: function(){

            var bsave = this.el('bsave');
            var bdraft = this.el('bdraft');
            var bpub = this.el('bpub');

            if (this.sentimentsId == 0){
                bsave.style.display = 'none';
            } else {
                var row = this.rows.getByIndex(0);
                var sentiments = row.cell;

                this.editorIntro.setContent(sentiments['intro']);
                this.editorBody.setContent(sentiments['body']);

                if (sentiments['dp'] > 0){
                    bpub.style.display = 'none';
                }

                bdraft.style.display = 'none';
                this.setelv('title', sentiments['tl']);
                this.setelv('srcname', sentiments['srcnm']);
                this.setelv('srclink', sentiments['srclnk']);
                this.setImage(sentiments['img']);

                this.pubDateTime.setValue(sentiments['dp'] == 0 ? null : new Date(sentiments['dp'] * 1000));
            }

            this.el('bcancel').disabled = '';
        },

        destroy: function(){
            this.editorIntro.destroy();
            this.editorBody.destroy();

            if (this.sentimentsId > 0){
                DATA.onComplete.unsubscribe(this.dsComplete);
            }
            EditorPanel.superclass.destroy.call(this);
        },

        onClick: function(el){
            var tp = this._TId['editor'];
            switch (el.id) {
                case tp['bcancel']:
                    this.close();
                    return true;
                case tp['bsave']:
                    this.save();
                    return true;
                case tp['bpub']:
                    this.publish();
                    return true;
                case tp['bdraft']:
                    this.draft();
                    return true;
                case tp['bimgsel']:
                    this.openImage();
                    return true;
                case tp['bimgdel']:
                    this.setImage('');
                    return true;
            }
        },
        setImage: function(imageid){
            this.imageid = imageid;
            var img = this.el('image');
            img.src = imageid ? '/filemanager/i/' + imageid + '/sentiments.gif' : '';
        },
        openImage: function(){
            var __self = this;

            Brick.Component.API.fire('filemanager', 'api', 'showFileBrowserPanel', function(result){
                __self.setImage(result.file.id);
            });
        },
        draft: function(){
            this.save('draft');
        },
        publish: function(){
            this.save('publish');
        },
        save: function(act){
            act = act || "";

            this.initTables();

            var dtp = this.pubDateTime.getValue();

            var tableSentiments = DATA.get('sentiments');
            var row = this.sentimentsId > 0 ? this.rows.getByIndex(0) : tableSentiments.newRow();
            row.update({
                'tl': this.elv('title'),
                'intro': this.editorIntro.getContent(),
                'body': this.editorBody.getContent(),
                'srcnm': this.elv('srcname'),
                'srclnk': this.elv('srclink'),
                'img': this.imageid
            });
            if (act == "publish"){
                if (L.isNull(dtp)){
                    dtp = new Date();
                }
                row.update({'dp': Math.round(dtp.getTime() / 1000)});
            } else if (act == "draft"){
                row.update({'dp': 0});
            } else {
                row.update({'dp': L.isNull(dtp) ? 0 : Math.round(dtp.getTime() / 1000)});
            }
            if (row.isNew()){
                this.rows.add(row);
            }
            if (!row.isNew() && !row.isUpdate()){
                this.close();
                return;
            }
            tableSentiments.applyChanges();
            var tableSentimentsList = DATA.get('sentimentslist');
            if (!L.isNull(tableSentimentsList)){
                tableSentimentsList.clear();
                DATA.get('sentimentscount').clear();
            }
            DATA.request();
            this.close();
        }
    });

    NS.EditorPanel = EditorPanel;

    API.showEditorPanel = function(sentimentsId){
        R.load(function(){
            new NS.EditorPanel(sentimentsId);
            DATA.request(true);
        });
    };

};
