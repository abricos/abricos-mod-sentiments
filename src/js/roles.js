var Component = new Brick.Component();
Component.requires = {
    mod: [{name: 'user', files: ['permission.js']}]
};
Component.entryPoint = function(){

    var NS = this.namespace;

    NS.roles = new Brick.AppRoles('{C#MODNAME}', {
        isAdmin: 50,
        isWrite: 30,
        isView: 10
    });
};