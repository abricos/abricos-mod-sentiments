<?php
/**
 * @package Abricos
 * @subpackage Sentiments
 * @copyright 2012-2016 Alexander Kuzmin
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @author Alexander Kuzmin <roosit@abricos.org>
 */

$charset = "CHARACTER SET 'utf8' COLLATE 'utf8_general_ci'";
$updateManager = Ab_UpdateManager::$current; 
$db = Abricos::$db;
$pfx = $db->prefix;

if ($updateManager->isInstall()){
	$db->query_write("
		CREATE TABLE IF NOT EXISTS ".$pfx."ns_cat (
		  catid int(10) unsigned NOT NULL auto_increment,
		  parentcatid int(10) unsigned NOT NULL,
		  name varchar(250) NOT NULL,
		  phrase varchar(250) NOT NULL,
		  PRIMARY KEY  (catid)
		)".$charset
	);
	
	$db->query_write("
		CREATE TABLE IF NOT EXISTS ".$pfx."ns_sentiments (
		  sentimentsid int(10) unsigned NOT NULL auto_increment,
		  userid int(10) unsigned NOT NULL,
		  dateline int(10) unsigned NOT NULL default '0',
		  dateedit int(10) unsigned NOT NULL default '0',
		  deldate int(10) unsigned NOT NULL default '0',
		  contentid int(10) unsigned NOT NULL,
		  title varchar(200) NOT NULL,
		  intro text NOT NULL,
		  imageid varchar(8) default NULL,
		  published int(10) unsigned NOT NULL default '0',
		  source_name varchar(200) default NULL,
		  source_link varchar(200) default NULL,
		  PRIMARY KEY  (sentimentsid)
		)".$charset
	);
	
	if (Ab_UpdateManager::$isCoreInstall){
		// Идет инсталляция платформа
		
		$d = new stdClass();
		$d->tl = "Рождение сайта";
		$d->intro = "
<p>Уважаемые посетители!</p>		
<p>
	Мы рады сообщить Вам о запуске нашего сайта.
</p>
<p>
	Для работы сайта мы используем платформу <a href='http://abricos.org' title='Abricos - система управления сайтом (CMS), платформа интернет-приложений (WebOS)'>Абрикос</a>,
	потому что именно на этой платформе мы сможем реализовать для Вас 
	практически безграничные возможности.
</p>
		";
		$d->dp = TIMENOW;
		require_once 'dbquery.php';
		SentimentsQuery::SentimentsAppend($db, 1, $d);
	}
}
if ($updateManager->isUpdate('0.2.2')){
	Abricos::GetModule('sentiments')->permission->Install();
}
