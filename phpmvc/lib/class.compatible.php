<?php
/**
 * 接口兼容类(专门兼容绞肉机接口)
 * @author weicky
 * @package DiFangQiPai
 */
class Compatible {
	/**
	 * 输入处理
	 */
	public static function input() {
		global $_INPUT;

		if(!isset($_POST['m']) || !isset($_POST['p'])) {
			return;
		}

		/*
		[core.login]
		CMD:0x0001 POST:	array(
				'm'				=> 'core',
				'p'				=> 'login',
				'device'		=> '',
				'mac'			=> '',
				'machine_type'	=> '',
				'nick'			=> '',
				'guid' 			=> '',
				'channel_id' 	=> '',
				'channel_key' 	=> '',
				'version' 		=> '',
				'os' 			=> '',
				'resolution' 	=> '',
				'param' 		=> '',
				'imsi' 			=> '',
				'ip' 			=> '',
				'appid'			=> '',
				'operator'		=> '',
				'net'			=> '',
				'login_type'	=> '',
				'utype'			=> '',
				'mid'			=> '',
				'sex'			=> '',
				'index' 		=> '',
				'act' 			=> 1,
				'phone'			=> '',
				'access_token' 	=> '',
				'pwd' 			=> '',
				'verify_code'	=> '',
				'verify_msg' 	=> '',
		);
		
		[core.verify]
		CMD:0x0013 POST:	array(
				'm'				=> 'core',
				'p'				=> 'verify',
				'phone'			=> '',
				'act'			=> 2,
				'index'			=> '{用户ID}',
		);
		
		[payment.productlist]
		CMD:0x0017 POST:	array(
				'm'				=> 'payment',
				'p'				=> 'productlist',
				'device'		=> '',
				'mac'			=> '',
				'mid'			=> '',
				'access_token'	=> '',
				'operator'		=> '',
				'utype'			=> '',
				'act'			=> 3,
				'index'			=> '{用户ID}',
		);
		
		[payment.createorder]
		CMD:0x0019 POST:	array(
				'm'				=> 'payment',
				'p'				=> 'createorder',
				'info'			=> '{客户端发送的数据}',
				'device'		=> '',
				'mac'			=> '',
				'mid'			=> '',
				'access_token'	=> '',
				'operator'		=> '',
				'utype'			=> '',
				'act'			=> 4,
				'index'			=> '{用户ID}',
				'pamount'		=> '',
				'ptype'			=> '',
				'pcoins'		=> '',
				'pchips'		=> '',
				'pcard'			=> '',
		);
		
		[user.update]
		CMD:0x0021 POST:	array(
				'm'				=> 'user',
				'p'				=> 'update',
				'mid'			=> '{用户ID}',
				'access_token'	=> '',
				'nick'			=> '',
				'mac'			=> '',
				'device'		=> '',
				'sex'			=> '',
				'act'			=> 5,
				'utype'			=> '',
				'index'			=> '{用户ID}',
		);
		
		[hand.out]
		CMD:0x0023 POST:	array(
				'm'				=> 'hand',
				'p'				=> 'out',
				'info'			=> '{"mid":用户ID,"php":"客户端发送的数据"}',
				'act'			=> 6,
				'index'			=> '{用户ID}',
		);
		*/
		if($_POST['m'] == 'hand' && $_POST['p'] == 'out') {
			$_INPUT = array_merge($_INPUT, (array)json_decode($_INPUT['info'], true));
			$_INPUT = array_merge($_INPUT, (array)json_decode($_INPUT['php'], true));
			unset($_INPUT['info'], $_INPUT['php']);
		} else {
			$_INPUT = array(
				'sessid'	=> '',
				'action'	=> "{$_POST['m']}.{$_POST['p']}",
			);
			$_INPUT = array_merge($_INPUT, $_POST);
		}
	}

	/**
	 * 输出处理
	 */
	public static function output() {
		global $_INPUT, $_OUTPUT;

		if(isset($_SERVER['HTTP_USER_INDEX']) || isset($_SERVER['HTTP_USER_CMD'])) {
			$_INPUT['compatible'] = 1;
			header("User-Index: {$_SERVER['HTTP_USER_INDEX']}");
			header("User-Cmd: {$_SERVER['HTTP_USER_CMD']}");
		}

		if( (!isset($_POST['m']) || !isset($_POST['p'])) && !isset($_INPUT['compatible']) ) {
			return;
		}

		$_OUTPUT['status']	= $_OUTPUT['code'] == 200 ? 0 : 1;
		$_OUTPUT['type']	= $_OUTPUT['code'] == 200 ? 0 : $_OUTPUT['code'];
		$_OUTPUT['msg']		= (string)$_OUTPUT['error'];
		$_OUTPUT['act']		= intval($_POST['act']);
		$_OUTPUT['index']	= intval($_POST['index']);
		$_OUTPUT['info']	= $_OUTPUT['result'];

		if($_POST['m'] == 'core' && $_POST['p'] == 'login') {
			$_OUTPUT['mid']		= intval($_OUTPUT['result']['100F']['info']['game']['mid']);
			$_OUTPUT['money']	= intval($_OUTPUT['result']['100F']['info']['game']['money']);
			$_OUTPUT['tfl']		= intval($_OUTPUT['result']['100F']['info']['game']['tfl']);
			unset($_OUTPUT['result']['100F']['info']['game']['tfl']);
		}

		if( (($_POST['m'] == 'hand' && $_POST['p'] == 'out') || isset($_INPUT['compatible'])) && isset($_INPUT['cmd']) ) {
			$_OUTPUT['cmd']		= is_numeric($_INPUT['cmd']) ? intval($_INPUT['cmd']) : strval($_INPUT['cmd']);
			$_OUTPUT['seq']		= intval($_INPUT['seq']); //请求序列号
		}
		if( $_POST['m'] == 'externals' && $_POST['p'] == 'server' ) {
			$_OUTPUT['act']		= is_numeric($_INPUT['cmd']) ? intval($_INPUT['cmd']) : strval($_INPUT['cmd']);
			$_OUTPUT['status']	= intval($_OUTPUT['result']['flag']); //请求序列号
		}

		unset($_OUTPUT['code'], $_OUTPUT['error'], $_OUTPUT['result']);
	}
}
