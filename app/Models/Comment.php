<?php

namespace App\Models;

class Comment {
	public $name;
	public $content;
	
	public function __construct ($name, $content) {
		$this->name    = $name;
		$this->content = $content;
	}
}

