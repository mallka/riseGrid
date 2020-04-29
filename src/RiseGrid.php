<?php

	namespace mallka\risegrid;

	use Yii;
	use yii\base\InvalidConfigException;
	use yii\db\ActiveRecord;

	/**
	 * The wrapper of Jqgrid
	 *
	 * @feature
	 *         1. column search
	 *         2. column sort
	 *         3. multi select
	 *         4. formartter
	 *         5. inline edit both of cell & line
	 *         6. bulid-in button
	 *         7. custom button
	 *         8. group-flied
	 *
	 * @todo：
	 *      1. Advanced column Search with op  condition: jQuery("#{$this->render_id}").jqGrid('filterToolbar',{
	 *      searchOnEnter: true, enableClear: false,groupOp:"AND",operands:{ "eq" :"==", "ne":"!", "lt":"<", "le":"<=",
	 *      "gt":">", "ge":">="}});
	 *      2. Select下拉框表现异常，需要按空格才可以选
	 *      3. 底部Css风格尚未处理
	 *      4. 部分css支持、树表风格尚未。
	 *
	 *
	 *
	 */
	class RiseGrid extends \yii\base\Widget
	{

		use TranslationTrait;

		/** @var string Render Element ID,Dom */
		public $render_id = 'list2';

		/** @var request url after Jqgrid init */
		public $url;

		public $datatype = 'json';
		public $guitype  = 'bootstrap4';
		public $iconSet  = 'fontAwesome';
		public $rowNum   = 20;
		public $rowList  = [ 20, 40, 60, 100 ];
		public $pager    = "#list2_page";

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
		public $scroll     = false;
		public $scrollrows = true;

		//toolbar
		public $toolbar = [ false, 'top' ];

		//Summary Data on bottom
		public $userDataOnFooter = false;
		public $footerrow        = false;

		public $gridview = true;

		//line dit or cell edit,
		public $cellEdit = false;
		public $cellurl  = null;
		public $editurl  = null;

		public $sortable = true;

		public $mk_language      = "zh_CN";
		public $mk_debugfile     = false;
		public $mk_key           = 'id';
		public $mk_hidden_column = [];
		public $mk_remove_column = [];
		public $mk_model;
		public $mk_template      = "<table id=\"{render_id}\" class=\"table table-condensed table-hover table-bordered table-striped\"></table><div id=\"{page}\"></div>";

		// buildin buttons
		public $mk_button
			= [
				'edit'    => false,
				'add'     => false,
				'del'     => false,
				'search'  => false,
				'save'    => false,
				'refresh' => true
			];

		//custom button list
		public $mk_button_extra = [];

		public $mk_top_search = false;

		public $mk_lock_first_column = true;

		//行拖曳
		public $mk_row_drag = true;

		//add Html tag after jqgrid table and before pager
		public $mk_append = '';

		//extra js code for jqgrid build-in function
		public $mk_extra = null;


		//addon js,will render in $(function(){});
		public $mk_js=null;

		//add js, will add after $(function(){});
		public  $mk_js_outside=null;

		//addon css
		public $mk_css=null;


		//formatter function
		public $mk_formatter;

		public function init()
		{
			if ($this->url === null) {
				throw new InvalidConfigException('Please offer a url then JQGRID can fetch the json data.');
			}

			if ($this->editurl !== null && $this->cellurl !== null) {
				throw new InvalidConfigException('You should choose one of celledit or lineedit');
			}

			if ($this->editurl != null) {
				$this->cellEdit = false;
			}


			if ($this->cellurl != null) {
				$this->cellEdit = true;
			}

			if ($this->userDataOnFooter == true) {
				$this->footerrow = true;
			}

			if (!is_array($this->mk_hidden_column)) {
				$this->mk_hidden_column = [ $this->mk_hidden_column ];
			}
			if (!is_array($this->mk_remove_column)) {
				$this->mk_remove_column = [ $this->mk_remove_column ];
			}

			if (!is_string($this->mk_key)) {
				throw new InvalidConfigException('Please spec one parimary key for jgrid');
			}

			$this->i18n();

		}

		public function i18n()
		{
			$this->initI18N(dirname(__FILE__), 'mallka');
			if (substr($this->mk_language, 0, 2) !== 'en') {
				$this->emptyrecords = Yii::t('mallka', 'Empty Data');
				$this->loadtext     = Yii::t('mallka', 'Loading');
			}
		}

		public function run()
		{
			$view = $this->getView();
			JqgridAsset::register($view);

			$genContainer = str_replace([ '{render_id}', '{page}' ], [ $this->render_id, $this->pager ], $this->mk_template);
			$this->genJs();
			if($this->mk_css!=null)
			{
				$view->registerCss(Util::compressCss($this->mk_css));
			}

			return $genContainer;

		}

		public function genJs()
		{
			#gen
			$cfgStr      = $this->_genCfgStr();
			$columnStr   = $this->_genColumnStr();
			$buildinBtns = json_encode($this->mk_button);
			$topSearch   = ($this->mk_top_search === true) ? "jQuery(\"#{$this->render_id}\").filterToolbar({ refresh: true});" : '';
			$lockFc      = $this->mk_lock_first_column === true ? "jQuery(\"#{$this->render_id}\").jqGrid('setFrozenColumns');" : '';
			$append      = $this->mk_append != '' ? $this->mk_append : '';
			$extra       = $this->mk_extra != null ? "$.extend($.jgrid, { {$this->mk_extra->expression} });" : "";
			$this->mk_js == null?'':$this->mk_js;
			$this->mk_js_outside == null?'':$this->mk_js_outside;
			$this->mk_formatter == null?'':$this->mk_formatter;

			$buttonExtra = '';
			if (!empty($this->mk_button_extra)) {
				$buttonExtra = 'btnCtr';
				foreach($this->mk_button_extra as $item) {
					$buttonExtra .= ".navButtonAdd(\"#{$this->pager}\",{$item->expression})";
				}
				$buttonExtra .= ';';

			}

			$onSelectRow='';
			if($this->editurl!=null)
			{
				$onSelectRow=<<<EOF
//行编辑
//行编辑，与单元格编辑互斥
//基于事件的全行编辑功能，与单元格编辑互斥。启用需要将celledit设置为false
onSelectRow: function (id) {
	if (id && id !== {$this->render_id}_lastsel) {
		jQuery("#{$this->render_id}" ).jqGrid('restoreRow', {$this->render_id}_lastsel);
		{$this->render_id}_lastsel = id;
	}

	jQuery("#{$this->render_id}" ).jqGrid('editRow', {$this->render_id}_lastsel,
		{
			keys: true,
			//开始触发编辑
			oneditfunc: function () {
				// alert("edited");
			},
			//保存前
			beforeSaveRow: function (o, rowid) {
				var temp = false;
				// //实现保存回调
				jQuery("#{$this->render_id}" ).jqGrid('saveRow', {$this->render_id}_lastsel,
					{
						keys: true,
						successfunc: function (response) {
							var data = $.parseJSON(response.responseText);
							if (data.error == 1) {
								layer.msg(data.msg);
								return  false
							}
							else {
								return  true;
							}
						},

					});

				//return temp;
			},
			//保存后
			aftersavefunc: function (rowid) {
				return false;
			}

		});

},
EOF;

			}





			$js
				= <<< EOF
var {$this->render_id}_lastsel;
$(function(){
	/**init */
	var  {$this->render_id}_grid = jqGrid = jQuery("#{$this->render_id}" ).jqGrid({
		{$cfgStr}	
		{$columnStr}
		{$onSelectRow}
		
		/**loadError*/
		loadError: function (xhr, st, err) {
			alert(
				"Type: " + st + "; Response: " + xhr.status + " "
				+ xhr.statusText);
		},
		
		/**addon js */
    	{$this->mk_js}
	

	});    
	
	/**build-in button*/
	var btnCtr = jQuery("#{$this->render_id}").jqGrid('navGrid', "#{$this->pager}" ,$buildinBtns);
	
    /** addon buttons*/
    $buttonExtra
	
	/**append html tag after grid table and before pager*/
	jQuery("#{$this->render_id}").append('$append');
     
	/**Simple Search of column  */
	$topSearch
	
	/**resize on window resize*/
	 $(window).on("resize", function () {
        newWidth = jQuery("#{$this->render_id}").closest(".ui-jqgrid").parent().width();
        jQuery("#{$this->render_id}").setGridWidth(newWidth, true);
    });
    
	jQuery("#{$this->render_id}").jqGrid('sortableRows', {
         update: function (e, ui) {
             //console.log(e);
             //alert("item with id=" + ui.item[0].id + " is droped");
             
    
             //拿到排序后的ID队列，这里考虑一个问题，如果有分页情况下，我们的排序只是提交了一部分，那么后端该如何排序呢？
             var neworder = jQuery("#{$this->render_id}").jqGrid("getDataIDs").join(',');
             alert(neworder);
    
         }
     });
	
	 /**some extra*/
	 $extra
	 
});
     
{$this->mk_js_outside}
{$this->mk_formatter}

EOF;

			$this->getView()->registerJs($js, \yii\web\View::POS_END);
		}

		private function _genCfgStr()
		{
			#filter
			$ref  = new \ReflectionClass($this);
			$pArr = $ref->getProperties();
			$pArr = array_filter($pArr, function ($k) {
				if (
					$k->class == 'mallka\risegrid\RiseGrid' &&

					!in_array($k->name, [
						'_i18n',
						'i18n',
						'_msgCat',
					]) &&

					substr($k->name, 0, 3) != 'mk_'

				) {
					return $k->name;
				}
			});

			//default  render element
			$p = [ 'render_id',
				   'url',
				   'datatype',
				   'guitype',
				   'iconSet',
				   'rowNum',
				   'rowList',
				   'pager',
				   'sortname',
				   'sortorder',
				   'mtype',
				   'viewrecords',
				   'hidegrid',
				   'autowidth',
				   'shrinkToFit',
				   'autoencode',
				   'height',
				   'direction',
				   'altRows',
				   'altclass',
				   'cellLayout',
				   'emptyrecords',
				   'loadtext',
				   'loadui',
			];

			//addon render element
			foreach($pArr as $item) {
				$f = $this->{$item->name};
				if ($f == null) {
					continue;
				}elseif ($item->name == 'editurl' && $f) {
					$p[] = 'editurl';
				}elseif ($item->name == 'cellEdit' && $f) {
					$p[] = 'cellEdit';
					$p[] = 'cellurl';
				}elseif ($item->name == 'multiselect' && $f) {
					$p[] = 'multiselect';
					$p[] = 'multikey';
					$p[] = 'multiselectWidth';
				}elseif ($item->name == 'scroll' && $f) {
					$p[] = 'scroll';
				}elseif ($item->name == 'scrollrows' && $f) {
					$p[] = 'scrollrows';
				}elseif ($item->name == 'userDataOnFooter' || $item->name == 'footerrow' && $f) {
					$p[] = 'userDataOnFooter';
					$p[] = 'footerrow';
				}

			}

			$cfgStr = '';
			foreach($p as $name) {
				$v      = $this->_getString($this->$name);
				$cfgStr .= "'{$name}':$v,\r\n";
			}
			return $cfgStr;

		}

		private function _getString($t)
		{


			if (is_array($t)) {
				return json_encode($t);
			}elseif (is_bool($t)) {
				return $t ? 'true' : 'false';
			}elseif (is_int($t)) {
				return $t;
			}elseif (is_null($t)) {
				return "''";
			}else{
				return "'$t'";
			}


		}

		private function _genColumnStr()
		{
			#columns
			$pool  = [];
			$model = $this->mk_model;

			//AtiveRecord Instance
			if (is_object($model)) {
				/** @var ActiveRecord $model */
				$all   = $model->getAttributes();
				$label = $model->attributeLabels();
				foreach($all as $item => $value) {
					if (in_array($item, $this->mk_remove_column)) {
						continue;
					}

					$obj        = new RiseGridColumn();
					$obj->label = isset($label[$item]) ? $label[$item] : $item;
					$obj->name  = $item;
					$obj->index = $item;


					if (in_array($item, $this->mk_hidden_column)) {
						$obj->hidden  = true;
						$obj->hidedlg = true;
					}

					$pool[] = $obj;

				}

				//Config array
			}else if (is_array($model)) {
				foreach($model as $item) {
					if (in_array($item['name'], $this->mk_remove_column)) {
						continue;
					}

					$obj = new RiseGridColumn();
					foreach($item as $k => $v) {
						$obj->{$k} = $v;
					}
					$pool[] = $obj;

				}

			}else {
				throw new InvalidConfigException('The `mk_model` should be one of ActiviteRecord Instance or Column Config Array');
			}

			$header = [];
			$data   = [];
			/**
			 * @var RiseGridColumn $column
			 */
			foreach($pool as $column) {
				if ($this->mk_key != '' && $column->key != $this->mk_key) {
					$column->key = 'true';
				}else {
					$column->key = null;
				}

				$header[] = $column->getLabel();
				$data[]   = $column->getColumn();


			}

			$str = "	colNames:['" . implode("','", $header) . "'],\r\n";
			$str .= "	colModel:[" . implode(",", $data) . "],\r\n";

			return $str;

		}

		/**
		 * @param User $model
		 * @param      $field
		 */
		private function _getLabel($model, $field)
		{
			/**@var  ActiveRecord $model */
			return $model->getAttributeLabel($field);
		}

	}
