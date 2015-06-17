<?php
/**
 * Модуль "Sentiments"
 *
 * @package Abricos
 * @subpackage Sentiments
 * @copyright Copyright (C) 2008 Abricos All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @author Alexander Kuzmin (roosit@abricos.org)
 */

/**
 * Модуль "Sentiments"
 *
 * @package Abricos
 * @subpackage Sentiments
 */
class SentimentsModule extends Ab_Module {

    /**
     * @var SentimentsModule
     */
    public static $instance;

    private $_manager = null;

    public function SentimentsModule(){
        SentimentsModule::$instance = $this;

        $this->version = "0.2.0";
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

    /**
     * Получить менеджер
     *
     * @return SentimentsManager
     */
    public function GetManager(){
        if (is_null($this->_manager)){
            require_once 'includes/manager.php';
            $this->_manager = new SentimentsManager($this);
        }
        return $this->_manager;
    }

    public function GetLink($sentimentsid){
        return Abricos::$adress->host."/".$this->takelink."/".$sentimentsid."/";
    }

    public function RSS_GetItemList(){
        $ret = array();

        $i18n = $this->GetI18n();

        $rows = $this->GetManager()->SentimentsList(1, 10);
        while (($row = $this->registry->db->fetch_array($rows))){
            $item = new RSSItem($row['tl'], $this->GetLink($row['id']), $row['intro'], $row['dp']);
            $item->modTitle = $i18n['title'];
            array_push($ret, $item);
        }
        return $ret;
    }

    public function RssMetaLink(){
        return Abricos::$adress->host."/rss/sentiments/";
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

Abricos::GetModule('comment');
Abricos::ModuleRegister(new SentimentsModule());

?>