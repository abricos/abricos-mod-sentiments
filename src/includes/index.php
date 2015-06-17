<?php
/**
 * @package Abricos
 * @subpackage Sentiments
 * @copyright Copyright (C) 2008 Abricos All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @author Alexander Kuzmin (roosit@abricos.org)
 */

$brick = Brick::$builder->brick;

$tag = isset(Abricos::$adress->dir[1]) ? Abricos::$adress->dir[1] : "";
$page = intval(substr($tag, 4, strlen($tag) - 4));

$mod = Abricos::GetModule('sentiments');
$manager = $mod->GetManager();

$phrases = SentimentsModule::$instance->GetPhrases();


// кол-во новостей на странице
$limit = $phrases->Get('page_count', 10)->value;
$dateFormat = $phrases->Get('date_format', "Y-m-d")->value;

$baseUrl = "/".$mod->takelink."/";

$lst = "";
$rows = $manager->SentimentsList($page, $limit);

while (($row = Abricos::$db->fetch_array($rows))){
    $lst .= Brick::ReplaceVarByData($brick->param->var['row'], array(
        "date" => date($dateFormat, $row['dp']),
        "link" => $baseUrl.$row['id']."/",
        "title" => $row['tl'],
        "intro" => $row['intro']
    ));
}

$brick->param->var['lst'] = $lst;

$sentimentsCount = $manager->SentimentsCount(true);

// подгрузка кирпича пагинатора с параметрами
Brick::$builder->LoadBrickS('sitemap', 'paginator', $brick, array(
    "p" => array(
        "total" => $sentimentsCount,
        "page" => $page,
        "perpage" => $limit,
        "uri" => $baseUrl
    )
));


?>