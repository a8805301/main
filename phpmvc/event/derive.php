<?php
/**
 * 派生事件处理类定义文件
 * @author weicky
 * @package GameFactory
 */

/**
 * 派生事件处理类(用于对象创建时动态改变类型以实现差异化)
 */
class Derive_Event {
	/**
	 * 获取子类化后的类名
	 * @param string $name 类名字
	 * @param string $base 基类
	 * @return string 子类名，失败返回false
	 */
	public static function getClassName($name, $base) {
		global $_INPUT;

		$base		= ucfirst($base); //基类
		$name		= ucfirst($name);
		$cls		= "{$name}_{$base}"; //子类名
		$app		= isset($_INPUT['app']) ? 'App' . intval($_INPUT['app']) : 0;
		$regionId	= $app ? get_region_id($_INPUT['app']) : 0;
		$region		= $regionId ? ucfirst(CFG('system', "regions.{$regionId}.name")) : '';
		$regionCls	= $region ? "{$region}_{$name}_{$base}" : ""; //地区层子类名
		/* ==== 目前注释以下代码以减少硬盘IO后续有差异化需求时再开启 ==== */
		//$appCls		= $region && $app ? "{$region}_{$app}_{$name}_{$base}" : ""; //应用层子类名

		/*if($appCls && class_exists($appCls)) {
			return $appCls;
		} else*/if($regionCls && class_exists($regionCls)) {
			return $regionCls;
		} elseif(class_exists($cls)) {
			return $cls;
		} else {
			return false;
		}
	}

	/**
	 * 获取子类化控制器类名
	 * @param string $name 控制器名
	 * @return string 子类名，失败返回false
	 */
	public static function controller($name) {
		global $_INPUT;
		if(isset($_INPUT['__admin'])){ //管理后台接口
			return 'Admin_' . ucfirst($name) . '_Controller';
		}elseif(isset($_INPUT['__api'])){ //棋牌管理后台接口
			return 'Api_' . ucfirst($name) . '_Controller';
		}elseif(isset($_INPUT['__cmsapi'])) {
			return 'Cmsapi_' . ucfirst($name) . '_Controller';
		}elseif(isset($_INPUT['df_partner'])){
			return 'Partner_' . ucfirst($name) . '_Controller';
		}elseif(isset($_INPUT['df_shangjia'])){
			return 'Shangjia_'.ucfirst($name) . '_Controller';
		}else{
			return self::getClassName($name, __FUNCTION__);
		}
	}

	/**
	 * 获取子类化数据模型类名
	 * @param string $name 模型名
	 * @return string 子类名，失败返回false
	 */
	public static function model($name) {
		return self::getClassName($name, __FUNCTION__);
	}

	/**
	 * 获取子类化视图文件名
	 * @param string $name 视图名
	 * @return string 视图文件路径
	 */
	public static function view($name) {
		global $_INPUT;

		$pos		= strrpos($name, ':');
		$prefix		= App::$path;
		if($pos !== false) {
			$prefix		= App::$path . str_replace(':', '/', substr($name, 0, $pos)) . '/';
			$name		= substr($name, $pos + 1);
		}
		$file		= "{$prefix}view/{$name}.php";
		$app		= isset($_INPUT['app']) ? 'App' . intval($_INPUT['app']) : 0;
		$regionId	= $app ? get_region_id($_INPUT['app']) : 0;
		$region		= $regionId ? CFG('system', "regions.{$regionId}.name") : '';
		$regionFile	= $region ? App::$path . "{$region}/view/{$name}.php" : "";
		/* ==== 目前注释以下代码以减少硬盘IO后续有差异化需求时再开启 ==== */
		//$appFile	= $app ? App::$path . "{$region}/{$app}/view/{$name}.php" : "";

		/*if($appFile && file_exists($appFile)) {
			return $appFile;
		} else*/if($regionFile && file_exists($regionFile)) {
			return $regionFile;
		} elseif(file_exists($file)) {
			return $file;
		} else {
			return false;
		}
	}

	/**
	 * 获取子类化配置文件列表
	 * @param string $name 配置名
	 * @return array 配置文件列表
	 */
	public static function config($name, $env='') {
		global $_INPUT;

		$pos		= strrpos($name, ':');
		$prefix		= App::$path;
		if($pos !== false) {
			$prefix		= App::$path . str_replace(':', '/', substr($name, 0, $pos)) . '/';
			$name		= substr($name, $pos + 1);
		}
		$name		= $name . ($env ? '.' . App::$env : '');
		$file		= "{$prefix}config/{$name}.php";
		$areaFile	= empty(App::$area) ? "" : "{$prefix}config/" . App::$area . "/{$name}.php";
		$app		= isset($_INPUT['app']) && $name != 'system' ? 'App' . intval($_INPUT['app']) : 0;
		$regionId	= $app ? get_region_id($_INPUT['app']) : 0;
		$region		= $regionId ? CFG('system', "regions.{$regionId}.name") : '';
		$regionFile	= $region ? App::$path . "{$region}/config/{$name}.php" : "";
		/* ==== 目前注释以下代码以减少硬盘IO后续有差异化需求时再开启 ==== */
		//$appFile	= $app ? App::$path . "{$region}/{$app}/config/{$name}.php" : "";
		$files		= array();
		if(file_exists($file)) {
			$files[] = $file;
		}
		if($areaFile && file_exists($areaFile)) {
			$files[] = $areaFile;
		}
		if($regionFile && file_exists($regionFile)) {
			$files[] = $regionFile;
		}
		/* ==== 目前注释以下代码以减少硬盘IO后续有差异化需求时再开启 ==== */
		/*if($appFile && file_exists($appFile)) {
			$files[] = $appFile;
		}*/

		return $files;
	}

	/**
	 * 获取子类化语言文件列表
	 * @return array 语言包文件列表
	 */
	public static function lang($name) {
		global $_INPUT;

		$lang		= App::$lang;
		$file		= App::$path . "lang/{$lang}/{$name}.php";
		$app		= isset($_INPUT['app']) ? 'App' . intval($_INPUT['app']) : 0;
		$regionId	= $app ? get_region_id($_INPUT['app']) : 0;
		$region		= $regionId ? CFG('system', "regions.{$regionId}.name") : '';
		$regionFile	= $region ? App::$path . "{$region}/lang/{$lang}/{$name}.php" : "";
		/* ==== 目前注释以下代码以减少硬盘IO后续有差异化需求时再开启 ==== */
		//$appFile	= $app ? App::$path . "{$region}/{$app}/lang/{$lang}/{$name}.php" : "";
		$files		= array();

		if(file_exists($file)) {
			$files[] = $file;
		}
		if($regionFile && file_exists($regionFile)) {
			$files[] = $regionFile;
		}
		/* ==== 目前注释以下代码以减少硬盘IO后续有差异化需求时再开启 ==== */
		/*if($appFile && file_exists($appFile)) {
			$files[] = $appFile;
		}*/

		return $files;
	}
}