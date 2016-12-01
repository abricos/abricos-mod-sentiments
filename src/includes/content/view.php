<?php
/**
 * @package Abricos
 * @subpackage Sentiments
 * @copyright 2012-2016 Alexander Kuzmin
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @author Alexander Kuzmin <roosit@abricos.org>
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

$var = &$brick->param->var;

$var['title'] = Brick::ReplaceVar($var['title'], "val", $row['tl']);
$var['date'] = Brick::ReplaceVar($var['date'], "val", $row['dp'] > 0 ? rusDateTime(date($row['dp'])) : $brick->param->var['notpub']);
$var['intro'] = Brick::ReplaceVar($var['intro'], "val", $row['intro']);
$var['body'] = Brick::ReplaceVar($var['body'], "val", $row['body']);

$var['source'] = '';
$var['image'] = '';
