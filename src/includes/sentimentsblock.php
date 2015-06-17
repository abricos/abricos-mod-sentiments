<?php
/**
 * Список последних новостей
 * 
 * @version $Id: sentimentsblock.php 1475 2012-04-11 12:08:12Z roosit $
 * @package Abricos
 * @subpackage Sentiments
 * @copyright Copyright (C) 2012 Abricos All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @author Alexander Kuzmin (roosit@abricos.org)
 */

$brick = Brick::$builder->brick;
$p = $brick->param->param;

if ($brick->child[0]->viewcount == 0 && !$p['showempty']){
	$brick->content = "";
	return;
}

$modRSS = Abricos::GetModule('rss');

$rss = "";
if (!empty($modRSS)){
	$rss = $brick->param->var['rss'];
}

$brick->content = Brick::ReplaceVarByData($brick->content, array(
	'rss' => $rss
));


?>