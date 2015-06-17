var Component = new Brick.Component();
Component.requires = {
    mod: [{name: 'user', files: ['cpanel.js']}]
};
Component.entryPoint = function(){

    if (Brick.AppRoles.check('sentiments', '30')){
        return;
    }

    var cp = Brick.mod.user.cp;

    var menuItem = new cp.MenuItem(this.moduleName);
    menuItem.icon = '/modules/sentiments/images/cp_icon.gif';
    menuItem.titleId = 'mod.sentiments.cp.title';
    menuItem.entryComponent = 'manager';
    menuItem.entryPoint = 'Brick.mod.sentiments.API.showSentimentsListWidget';

    cp.MenuManager.add(menuItem);
};
