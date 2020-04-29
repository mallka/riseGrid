<?php

	namespace mallka\risegrid;

	class Util
	{

		public static function compressCss($buffer)
		{
			/* remove comments */
			$buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
			/* remove tabs, spaces, newlines, etc. */
			$buffer = str_replace([ "
		", "\r", "\n", "\t", '  ', '    ', '    ' ], '', $buffer);
			return $buffer;
		}



	}
