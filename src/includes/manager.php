<?php
/**
 * @package Abricos
 * @subpackage Sentiments
 * @copyright Copyright (C) 2008 Abricos. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @author Alexander Kuzmin (roosit@abricos.org)
 */

require_once 'dbquery.php';

class SentimentsManager extends Ab_ModuleManager {

    /**
     *
     * @var SentimentsModule
     */
    public $module = null;

    public function __construct(SentimentsModule $module){
        parent::__construct($module);
    }

    /**
     * Роль администратора новостей: редактор всех новостей
     */
    public function IsAdminRole(){
        return $this->IsRoleEnable(SentimentsAction::ADMIN);
    }

    /**
     * Роль публикатора новостей: редактор только своих новостей
     */
    public function IsWriteRole(){
        if ($this->IsAdminRole()){
            return true;
        }
        return $this->IsRoleEnable(SentimentsAction::WRITE);
    }

    /**
     * Роль просмотра новостей: только просмотр опубликованных новостей
     */
    public function IsViewRole(){
        if ($this->IsWriteRole()){
            return true;
        }
        return $this->IsRoleEnable(SentimentsAction::VIEW);
    }

    public function IsSentimentsWriteAccess($newid){
        if (!$this->IsWriteRole()){
            return false;
        }
        if ($this->IsAdminRole()){
            return true;
        }

        $info = SentimentsQuery::SentimentsInfo($this->db, $newid);
        if (empty($info) || $info['uid'] != $this->userid){
            return false;
        }
        return true;
    }


    public function AJAX($d){
        if ($d->type == 'sentiments'){
            switch ($d->do){
                case "remove":
                    return $this->SentimentsRemove($d->id);
                case "restore":
                    return $this->SentimentsRestore($d->id);
                case "rclear":
                    return $this->SentimentsRecycleClear();
                case "publish":
                    return $this->SentimentsPublish($d->id);
            }
        }
        return -1;
    }

    public function DSProcess($name, $rows){
        switch ($name){
            case 'sentiments':
                foreach ($rows as $r){
                    if ($r->f == 'u'){
                        $this->SentimentsUpdate($r->d);
                    }
                    if ($r->f == 'a'){
                        $this->SentimentsAppend($r->d);
                    }
                }
                break;
        }
    }

    public function DSGetData($name, $rows){
        $p = $rows->p;

        switch ($name){
            case 'sentimentslist':
                return $this->SentimentsList($p->page, $p->limit);
            case 'sentimentscount':
                return $this->SentimentsCount();
            case 'sentiments':
                return $this->Sentiments($p->id);
            case 'online':
                return $this->SentimentsList(1, 3);
        }

        return null;
    }

    /* * * * * * * * * * * * Чтение новостей * * * * * * * * * * * */

    public function Sentiments($sentimentsid, $retarray = false){
        if (!$this->IsViewRole()){
            return;
        }
        return SentimentsQuery::Sentiments($this->db, $sentimentsid, $this->userid, $retarray);
    }

    public function SentimentsList($page = 1, $limit = 10){
        if (!$this->IsViewRole()){
            return;
        }
        return SentimentsQuery::SentimentsList($this->db, $this->userid, $page, $limit);
    }

    public function SentimentsCount($retvalue = false){
        if (!$this->IsViewRole()){
            return;
        }
        return SentimentsQuery::SentimentsCount($this->db, $this->userid, $retvalue);
    }

    /* * * * * * * * * * * * Управление новостями * * * * * * * * * * * */

    /**
     * Добавить настроение
     *
     * @param Object $d
     */
    public function SentimentsAppend($d){
        if (!$this->IsWriteRole()){
            return;
        }
        SentimentsQuery::SentimentsAppend($this->db, $this->userid, $d);
    }

    /**
     * Обновить настроение
     *
     * @param Object $d
     */
    public function SentimentsUpdate($d){
        if (!$this->IsSentimentsWriteAccess($d->id)){
            return;
        }
        SentimentsQuery::SentimentsUpdate($this->db, $d);
    }

    public function SentimentsRemove($id){
        if (!$this->IsSentimentsWriteAccess($id)){
            return;
        }
        SentimentsQuery::SentimentsRemove($this->db, $id);
    }

    public function SentimentsRestore($id){
        if (!$this->IsSentimentsWriteAccess($id)){
            return;
        }
        SentimentsQuery::SentimentsRestore($this->db, $id);
    }

    public function SentimentsRecycleClear(){
        if (!$this->IsWriteRole()){
            return;
        }
        SentimentsQuery::SentimentsRecycleClear($this->db, $this->userid);
    }

    public function SentimentsPublish($id){
        if (!$this->IsSentimentsWriteAccess($id)){
            return;
        }
        SentimentsQuery::SentimentsPublish($this->db, $id, $this->userid);
    }
}

?>