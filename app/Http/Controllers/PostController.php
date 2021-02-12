<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Comment;

class PostController extends Controller
{
	function comment (Request $request, $post_name) {
		$server   = $_ENV ['DB_HOST'];
		$username = $_ENV ['DB_USERNAME'];
		$password = $_ENV ['DB_PASSWORD'];
		$dbname   = $_ENV ['DB_DATABASE'];

		$conn = new \mysqli($server, $username, $password, $dbname);
		
		if ($conn->connect_error) {
			die ("Connection failed: " . $conn->connect_error);
		}
		
		if ($conn->query("INSERT INTO vulpin (Link, Name, Content) VALUES ('" . $post_name . '\', \'' . $request->input('name') . '\', \'' . $request->input('content') . '\');') !== TRUE) {
			return "Error" . $conn->error;
		}
		
		$conn->close();

		return redirect($post_name);
	}
	
	function post ($post_name) {
		$server   = $_ENV ['DB_HOST'];
		$username = $_ENV ['DB_USERNAME'];
		$password = $_ENV ['DB_PASSWORD'];
		$dbname   = $_ENV ['DB_DATABASE'];
		
		$conn = new \mysqli($server, $username, $password, $dbname);
		
		if ($conn->connect_error) {
			die ("Connection failed: " . $conn->connect_error);
		}
		
		$comments = array();

		$result = $conn->query("SELECT * FROM vulpin WHERE Link='" . $post_name . "' ORDER BY Datetime");

		if (!empty($result) && $result->num_rows > 0) {
			$i = 0;
			
			while ($row = $result->fetch_assoc ()) {
				$comments[$i] = new Comment($row['Name'], $row['Content']);
				
				$i += 1;
			}
		}

		$conn->close();
		
		return view('posts.' . $post_name)->with('comments', $comments);
	}
}
