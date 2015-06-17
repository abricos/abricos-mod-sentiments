<?php

/**
 * @package Abricos
 * @subpackage Sentiments
 * @copyright Copyright (C) 2010 Abricos. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @author Alexander Kuzmin (roosit@abricos.org)
 */
class SentimentsQuery {

    public static function SentimentsAppend(Ab_Database $db, $userid, $d){
        $contentid = Ab_CoreQuery::CreateContent($db, $d->body, 'sentiments');
        $sql = "
			INSERT INTO ".$db->prefix."ns_sentiments (
				userid, dateline, dateedit, published, 
				contentid, title, intro, imageid, source_name, source_link
			) VALUES (
				".bkint($userid).",
				".TIMENOW.",
				".TIMENOW.",
				'".bkint($d->dp)."',
				'".bkint($contentid)."',
				'".bkstr($d->tl)."',
				'".bkstr($d->intro)."',
				'".bkstr(isset($d->img) ? $d->img : "")."',
				'".bkstr($d->srcnm)."',
				'".bkstr($d->srclnk)."'
			)
		";
        $db->query_write($sql);
    }

    public static function SentimentsUpdate(Ab_Database $db, $d){

        $info = SentimentsQuery::SentimentsInfo($db, $d->id);
        Ab_CoreQuery::ContentUpdate($db, $info['ctid'], $d->body);
        $sql = "
			UPDATE ".$db->prefix."ns_sentiments
			SET 
				dateedit=".TIMENOW.",
				published=".bkint($d->dp).",
				title='".bkstr($d->tl)."',
				intro='".bkstr($d->intro)."',
				imageid='".bkstr(isset($d->img) ? $d->img : "")."',
				source_name='".bkstr($d->srcnm)."',
				source_link='".bkstr($d->srclnk)."'
			WHERE sentimentsid=".bkint($d->id)."
		";
        $db->query_write($sql);
    }

    public static function Sentiments(Ab_Database $db, $sentimentsid, $userid = 0, $retarray = false){
        $sql = "
			SELECT
				a.sentimentsid as id,
				a.userid as uid,
				a.dateline as dl,
				a.dateedit as de,
				a.published as dp,
				a.deldate as dd,
				a.contentid as ctid,
				b.body as body,
				a.title as tl,
				a.intro,
				a.imageid as img,
				a.source_name as srcnm,
				a.source_link as srclnk
			FROM ".$db->prefix."ns_sentiments a
			LEFT JOIN ".$db->prefix."content b ON a.contentid = b.contentid
			WHERE a.sentimentsid = ".bkint($sentimentsid)." AND 
				((a.deldate=0 AND a.published>0) OR a.userid=".bkint($userid).") 
			LIMIT 1
		";
        return $retarray ? $db->query_first($sql) : $db->query_read($sql);
    }

    public static function SentimentsInfo(Ab_Database $db, $sentimentsid){
        $sql = "
			SELECT 
				sentimentsid as id,
				userid as uid,
				contentid as ctid,
				dateline as dl,
				dateedit as de,
				published as dp,
				sentimentsid, userid, contentid, dateline, dateedit, published
			FROM ".$db->prefix."ns_sentiments 
			WHERE sentimentsid=".bkint($sentimentsid)."
		";
        return $db->query_first($sql);
    }

    /**
     * Список новостей
     *
     * @param Ab_Database $db
     * @param integer $limit
     * @param integer $page
     * @param boolean $full Если true, содержит удаленные, черновики
     * @return resource
     */
    public static function SentimentsList(Ab_Database $db, $userid = 0, $page = 1, $limit = 10){
        $from = $limit * (max($page, 1) - 1);
        $sql = "
			SELECT
				sentimentsid as id,
				userid as uid,
				dateline as dl,
				dateedit as de,
				published as dp,
				deldate as dd,
				contentid as ctid,
				title as tl,
				imageid as img,
				source_name as srcnm,
				source_link as srclnk,
				intro
			FROM ".$db->prefix."ns_sentiments
			WHERE (deldate=0 AND published>0) OR userid=".bkint($userid)." 
			ORDER BY dl DESC 
			LIMIT ".$from.",".bkint($limit)."
		";
        return $db->query_read($sql);
    }

    public static function SentimentsCount(Ab_Database $db, $userid = 0, $retvalue = false){
        $sql = "
			SELECT count( sentimentsid ) AS cnt
			FROM ".$db->prefix."ns_sentiments
			WHERE (deldate=0 AND published>0) OR userid=".bkint($userid)." 
			LIMIT 1 
		";
        if ($retvalue){
            $row = $db->query_first($sql);
            return $row['cnt'];
        } else {
            return $db->query_read($sql);
        }
    }

    public static function SentimentsRemove(Ab_Database $db, $sentimentsid){
        $sql = "
			UPDATE ".$db->prefix."ns_sentiments 
			SET deldate=".TIMENOW."
			WHERE sentimentsid=".bkint($sentimentsid)."
		";
        $db->query_write($sql);
    }

    public static function SentimentsRestore(Ab_Database $db, $sentimentsid){
        $sql = "
			UPDATE ".$db->prefix."ns_sentiments 
			SET deldate=0
			WHERE sentimentsid=".bkint($sentimentsid)."
		";
        $db->query_write($sql);
    }

    public static function SentimentsRecycleClear(Ab_Database $db, $userid){
        $sql = "
			DELETE FROM ".$db->prefix."ns_sentiments
			WHERE deldate > 0 AND userid=".bkint($userid)."
		";
        $db->query_write($sql);
    }

    public static function SentimentsPublish(Ab_Database $db, $sentimentsid){
        $sql = "
			UPDATE ".$db->prefix."ns_sentiments
			SET published='".TIMENOW."'
			WHERE sentimentsid=".bkint($sentimentsid)." 
		";
        $db->query_write($sql);
    }

}

?>