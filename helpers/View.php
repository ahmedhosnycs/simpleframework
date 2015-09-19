<?php

class View {

	public static function Show($view_name, $args=array()) {
		if(empty($args)) {
			ob_start();
			include __DIR__.'/../views/'.$view_name.'.php';
			$render_view = ob_get_clean();
			return $render_view;
		}
		else {
			ob_start();
			extract($args);
			include __DIR__.'/../views/'.$view_name.'.php';
			$render_view = ob_get_clean();
			return $render_view;
		}
	}
}