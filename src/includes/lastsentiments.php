<?php
/**
 * Список последних новостей
 *
 * @version $Id: lastsentiments.php 1475 2012-04-11 12:08:12Z roosit $
 * @package Abricos
 * @subpackage Sentiments
 * @copyright Copyright (C) 2008 Abricos All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @author Alexander Kuzmin (roosit@abricos.org)
 */

$brick = Brick::$builder->brick;
$mod = Abricos::GetModule('sentiments');
$manager = $mod->GetManager();

$limit = $brick->param->param['count'];
$hideintro = $brick->param->param['hideintro'];
if (empty($hideintro) && !empty($brick->parent)){
    $hideintro = $brick->parent->param->param['hideintro'];
}

$dateFormat = SentimentsModule::$instance->GetPhrases()->Get('date_format', "Y-m-d")->value;
$baseUrl = "/".$mod->takelink."/";

$lst = "";
$rows = $manager->SentimentsList(1, $limit);

$viewcount = 0;

while (($row = Abricos::$db->fetch_array($rows))){
    $viewcount++;
    $lst .= Brick::ReplaceVarByData($brick->param->var['row'], array(
        "date" => date($dateFormat, $row['dp']),
        "link" => $baseUrl.$row['id']."/",
        "title" => $row['tl'],
        "intro" => !empty($hideintro) ? '' : $row['intro']
    ));
}

$brick->viewcount = $viewcount;
$brick->param->var['result'] = $lst;


?>