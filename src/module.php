<?php
/**
 * @package Abricos
 * @subpackage Sentiments
 * @copyright 2012-2016 Alexander Kuzmin
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @author Alexander Kuzmin <roosit@abricos.org>
 */

/**
 * Модуль "Sentiments"
 *
 * @package Abricos
 * @subpackage Sentiments
 */
class SentimentsModule extends Ab_Module {

    /**
     * @deprecated
     */
    public static $instance;

    public function __construct(){
        SentimentsModule::$instance = $this;

        $this->version = "0.3.0";
        $this->name = "sentiments";
        $this->takelink = "sentiments";

        $this->permission = new SentimentsPermission($this);
    }

    /**
     * Получить имя кирпича контента
     *
     * @return string
     */
    public function GetContentName(){
        $adress = Abricos::$adress;

        if ($adress->level == 2 && substr($adress->dir[1], 0, 4) != 'page'){
            return "view";
        }
        return "index";
    }
}


class SentimentsAction {
    const VIEW = 10;
    const WRITE = 30;
    const ADMIN = 50;
}

class SentimentsPermission extends Ab_UserPermission {

    public function __construct(SentimentsModule $module){
        $defRoles = array(
            new Ab_UserRole(SentimentsAction::VIEW, Ab_UserGroup::GUEST),
            new Ab_UserRole(SentimentsAction::VIEW, Ab_UserGroup::REGISTERED),
            new Ab_UserRole(SentimentsAction::VIEW, Ab_UserGroup::ADMIN),

            new Ab_UserRole(SentimentsAction::WRITE, Ab_UserGroup::ADMIN),
            new Ab_UserRole(SentimentsAction::ADMIN, Ab_UserGroup::ADMIN)
        );
        parent::__construct($module, $defRoles);
    }

    public function GetRoles(){
        return array(
            SentimentsAction::VIEW => $this->CheckAction(SentimentsAction::VIEW),
            SentimentsAction::WRITE => $this->CheckAction(SentimentsAction::WRITE),
            SentimentsAction::ADMIN => $this->CheckAction(SentimentsAction::ADMIN)
        );
    }
}

Abricos::ModuleRegister(new SentimentsModule());
