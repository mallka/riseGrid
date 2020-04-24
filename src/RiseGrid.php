<?php

	namespace mallka\risegrid;



	use Yii;
	use yii\base\Widget;
	use yii\base\InvalidConfigException;
	use yii\grid\DataColumn;
	use yii\helpers\Html;
	use yii\helpers\Json;
	use yii\helpers\ArrayHelper;
	use yii\i18n\Formatter;



	/**
	 * This is just an example.
	 */
	class RiseGrid extends \yii\base\Widget
	{

		use TranslationTrait;

		/** @var string Render Element ID,Dom */
		public $renderID = 'list2';

		/** @var request url after Jqgrid init */
		public $url;

		public $dattype = 'json';
		public $guitype = 'bootstrap4';
		public $iconSet = 'fontAwesome';
		public $rowNum  = 20;
		public $rowList = [ 20, 40, 60, 100 ];
		public $pager   = "#list2_page";

		/** @var string default sort field */
		public $sortname = 'id';

		public $sortorder = "desc";

		/** @var string post/get */
		public $mtype       = "post";
		public $viewrecords = true;
		public $hidegrid    = false;

		public $autowidth   = true;
		public $shrinkToFit = true;
		public $autoencode  = false;

		//STYLE
		public $height       = '100%';
		public $direction    = "ltr";
		public $altRows      = true;
		public $altclass     = "ui-priority-secondary";
		public $cellLayout   = 10;
		public $emptyrecords = "Empty Data";

		//MASK and loading
		public $loadtext = "Loading";
		public $loadui   = "block";

		//multiselect
		public $multiselect      = true;
		public $multikey         = "shiftKey";
		public $multiselectWidth = 30;

		//row number
		public $rownumbers  = true;
		public $rownumWidth = 25;

		//scroll
		public $scroll     = true;
		public $scrollrows = true;

		//toolbar
		public $toolbar = [ false, 'top' ];

		//Summary Data on bottom
		public $userDataOnFooter=false;
		public $footerrow=false;

		public $gridview = true;



		//line dit or cell edit,
		public $cellEdit= false;
		public $cellurl='';
		public $editurl='';

		public  $sortable=true;






		public function init()
		{

			if ($this->url === null) {
				throw new InvalidConfigException('Please offer a url then JQGRID can fetch the json data.');
			}

			if($this->editurl !== null && $this->cellurl!==null)
			{
				throw new InvalidConfigException('You should choose one of celledit or lineedit');
			}

			if($this->editurl!=null)
			{
				$this->cellEdit=true;
			}

			if($this->userDataOnFooter==true){
				$this->footerrow=true;
			}




		}
















		public function run()
		{
			$view = $this->getView();
			JqgridAsset::register($view);

			return "Hello!";
		}

		public function i18n()
		{
			$this->initI18N(dirname(__FILE__));
			if (substr($this->language, 0, 2) !== 'en') {
				$this->emptyrecords = Yii::t('mallka','Empty Data');
				$this->loadtext = Yii::t('mallka','Loading');
			}
		}

	}
