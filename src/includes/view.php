<?php
/**
 * @version $Id: view.php 1413 2012-02-02 09:09:11Z roosit $
 * @package Abricos
 * @subpackage Sentiments
 * @copyright Copyright (C) 2010 Abricos All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @author Alexander Kuzmin (roosit@abricos.org)
 */

$brick = Brick::$builder->brick;

$param = $brick->param;
$manager = Abricos::GetModule('sentiments')->GetManager();

$sentimentsid = intval(Abricos::$adress->dir[1]);

$row = $manager->Sentiments($sentimentsid, true);
if (empty($row)){
	$brick->content = $brick->param->var['notfound'];
	return;
}

/*
if ($manager->IsAdminRole()){
	Brick::$builder->AddJSModule('sentiments', 'api.js');
	$t = $brick->param->var['fedit'];
	$t = str_replace("{v#id}", $sentimentsid, $t);
	$brick->param->var['feditbody'] = $t;
}
/**/

$var = &$brick->param->var;

$var['title'] = Brick::ReplaceVar($var['title'], "val", $row['tl']);
$var['date'] = Brick::ReplaceVar($var['date'], "val", $row['dp']>0 ? rusDateTime(date($row['dp'])) : $brick->param->var['notpub']);
$var['intro'] = Brick::ReplaceVar($var['intro'], "val", $row['intro']);
$var['body'] = Brick::ReplaceVar($var['body'], "val", $row['body']);

$var['source'] = '';
$var['image'] = '';

/*
if (empty($row['srcnm']) || empty($row['srclnk'])){
	$brick->param->var['source'] = '';
}else{
	$t = str_replace('#srclnk#', $row['srclnk'], $brick->param->var['source']);
	$t = str_replace('#srcnm#', $row['srcnm'], $t);
	$brick->param->var['source'] = $t;
}

if (empty($row['img'])){
	$brick->param->var['image'] = '';
}else{
	$brick->param->var['image'] = str_replace('#id#', $row['img'], $brick->param->var['image']);
}
Brick::$contentId = $row['contentid'];
/**/

?>