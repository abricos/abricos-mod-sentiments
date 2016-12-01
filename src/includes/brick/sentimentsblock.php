<?php
/**
 * @package Abricos
 * @subpackage Sentiments
 * @copyright 2012-2016 Alexander Kuzmin
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @author Alexander Kuzmin <roosit@abricos.org>
 */

$brick = Brick::$builder->brick;
$p = $brick->param->param;

if ($brick->child[0]->viewcount == 0 && !$p['showempty']){
    $brick->content = "";
    return;
}
