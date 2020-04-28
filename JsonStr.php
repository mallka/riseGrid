<?php

	namespace mallka\risegrid;

	class JsonStr
	{

		public $value;

		public function __construct($str)
		{
			$this->value=$str;
		}

		/**
		 * @return mixed
		 */
		public function getJsonStr()
		{
			return $this->value;
		}


	}
