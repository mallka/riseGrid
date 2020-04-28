<?php

	namespace mallka\risegrid;

	use yii\web\AssetBundle;

	class JqgridAsset extends AssetBundle
	{

		public $css
			= [
				'jqGrid-4.15.5/css/ui.jqgrid.css', #基础包，必要
				'jqGrid-4.15.5/dist/css/backup.css', #基础包，必要
				'jqGrid-4.15.5/dist/plugins/css/ui.multiselect.css', #基础包，必要

			];
		public $js
			= [
				'jqGrid-4.15.5/dist/plugins/jquery.contextmenu.js', #右键菜单
				'jqGrid-4.15.5/js/i18n/grid.locale-cn.js', #插件包多语言包
				'jqGrid-4.15.5/js/jquery.jqgrid.src.js', #插件包，必要
				'jqGrid-4.15.5/js/grid.queryui.js', #插件包，必要
			];

		public $depends
			= [
				'yii\web\JqueryAsset',
			];

		public function init()
		{
			$this->sourcePath = __DIR__ . '/resources';
			parent::init();
		}
	}
