Jqgrid widget
=============
对jqgrid的常用封装

已支持
------------
- 字段搜索
- 字段排序
- 表头分组
- 多选
- 单元格渲染
- 行编辑（待测试）
- jqrid标准支持
- 分组
- 汇总
- 滚动表格
  

安装
------------

Composer 安装

```
php composer.phar require --prefer-dist yiirise/yii2-risegrid "dev-master"
```

或在composer.json加入以下配置

```
"mallka/yii2-risegrid": "dev-master"
```

然后 composer update 一下


用法
-----
所有mk_开头的参数都是插件封装所需的开关之类的，非mk_开头的，直接映射为jqgrid的配置项




```php
<?= \mallka\risegrid\RiseGrid::widget([
   //渲染ID，随意取名 
  'render_id'=>'list2',
  //分页区域id，随意取名
  'pager'=>'list2_page',
  //ajax获取数据的网址
  'url'=>\yii\helpers\Url::to(['user-backend/ajax_search']),
  
  //单元格编辑后提交的网址，不给就开启
  'cellurl'=>\yii\helpers\Url::to(['user-backend/ajax_searchaaaa']),
  
  //行编辑后提交的网址，不给就不开启
  //'editurl'=>'a',
  

   //语言，内置好了en和zh-CN 
  'mk_language'=>'zh-CN',
  
  //ActiveRecord 实例或数组配置
  'mk_model'=>$userBackend,
  
  //具体数组配置
  //	'mk_model'=>[
  //		//full
  //		[
  //			'label'  => '测试以下',
  //
  //			//we suggest name = index,
  //			'name'  => '',
  //			'index' => '',
  //
  //			//default width is 40
  //			'width' => 40,
  //			'align' => 'left',
  //
  //			//
  //			'key'   => false,
  //
  //			 //hidden options, Boolean
  //			'hidden'=>false,
  //			'hidedlg'=>false,
  //
  //			//display render
  //			'formatter'=>"",
  //			'formatoptions'=>'',
  //
  //			//edit switch
  //			'editable' => false,
  //			'edittype'=>'',
  //			'editoptions'=>'',
  //		],
  //
  //		//base
  //		[
  //			'label'  => '基本',
  //			'name'  => 'base',
  //			'index' => 'base',
  //		],
  //
  //
  //	],





    
    
  'mk_key'=>'id',				 #额外指定key,It will orverride ml_model config opts
  //	'mk_hidden_column'=>['username'],		 #隐藏渲染的字段
  //	'mk_remove_column'=>['id'],  #不要渲染的字段

   //顶部搜索
  'mk_top_search' => false,

  //表格后追加内容
  'mk_append'=>"Hel\'lo",

  //替换jqgrid的方法区域
  'mk_extra'=>new \yii\web\JsExpression("
        //标题，提示，关闭按钮，model参数, 替换掉自带的提示组件。
        info_dialog: function (caption, content, c_b, modalopt) {
            layer.alert(content, {
                icon: 2,
                skin: 'layer-ext-moon'
            })
        },

  "),

  //加一些按钮          
  'mk_button_extra'=>[
          new \yii\web\JsExpression('{
                caption: "Adddd",           //按钮标题
                buttonicon: "ui-icon-add",  //icon的类名
                onClickButton: function () {  //响应方法
                    alert("Adding Row");
                },
                position: "last"            //位置
          }'),
          new \yii\web\JsExpression('{
                caption: "delete",
                buttonicon: "ui-icon-add",
                onClickButton: function () {
                    alert("Adding Row");
                },
                position: "last"
          }'),
          new \yii\web\JsExpression('{
                caption: "Hiii",
                buttonicon: "ui-icon-add",
                onClickButton: function () {
                    alert("Adding Row");
                },
                position: "last"
          }'),
  ],

//在jgrid后注入js函数或语句
'mk_js_outside'=>new \yii\web\JsExpression("
                    function ooo(){alert(1)};
                  
                  "),

//在jqgrid的实例化中注册方法，主要是为了扩展各类响应事件或未包括在内的配置
'mk_js'=>new \yii\web\JsExpression("
    onSelectRow: function(id){
      if(id && id!==list2_lastsel){
        alert(id);
      }
    },
  
"),

//jqgrid依赖bs4，但偶尔需要对他进行一些hack处理。
'mk_css'=>".table{background-color:red}",



]); ?>
```



单元格表单类型（内置）
----

文本框：

```editable : true```

文本框(带排序)：

```editable : true,sorttype : "date"```

文本框（大小限制）：

```editable : true,editoptions : {size : "20",maxlength : "30"}```

多选框(YES是选中的值，No是没选的，可以自定义)：

```editable : true,edittype : "checkbox",editoptions : {value : "Yes:No"}```


下拉框

```editable : true,edittype : "select",editoptions : {value : "FE:FedEx;IN:InTime;TN:TNT;AR:ARAMEX"}```

文本框

```editable : true,edittype : "textarea",editoptions : {rows : "2",cols : "10"}```

自定义组件渲染：
```
editable : true,edittype : "customr",
editoptions :
{
    custom_element:my_input,    //自定义输入控件，一个js函数，返回一个html
     custom_value:mycheck       //自定义获取值的方法，经常用来验证数据是否正确
 }
 
要额外注入所需函数
function my_input(value, options) {
 return $("<input type='text' size='10' style='background-color: red;' value='"+value+"'/>");
}
function my_value(value) {
 return "My value: "+value.val();
}
```
