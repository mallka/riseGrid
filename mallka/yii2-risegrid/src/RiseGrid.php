<?php

namespace mallka\risegrid;

/**
 * This is just an example.
 */
class RiseGrid extends \yii\base\Widget
{
    public function run()
    {
		$view = $this->getView();
		JqgridAsset::register($view);




        return "Hello!";
    }
}
