<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use \App\Models\Comment;

class PostController extends Controller
{
	function comment (Request $request, $post_name) {
		$server   = $_ENV ['DB_HOST'];
		$username = $_ENV ['DB_USERNAME'];
		$password = $_ENV ['DB_PASSWORD'];
		$dbname   = $_ENV ['DB_DATABASE'];

		Log::info('In ' . __METHOD__ . ' adding comment for ' . $request->input('name') . ' with content "' . $request->input('content') . '".');

		$conn = new \mysqli($server, $username, $password, $dbname);

		if ($conn->connect_error) {
			Log::error('In ' . __METHOD__ . ' connection failed: ' . $conn->connect_error);
			die ("Connection failed: " . $conn->connect_error);
		}

		if ($conn->query("INSERT INTO vulpin (Link, Name, Content) VALUES ('" . $post_name . '\', \'' . $request->input('name') . '\', \'' . $request->input('content') . '\');') !== TRUE) {
			Log::error('In ' . __METHOD__ . ' insertion was unsuccessful for ' . $request->input('name') . ' with content "' . $request->input('content') . '".');
			return "Error" . $conn->error;
		}

		Log::info('In ' . __METHOD__ . ' redirecting user to post ' . $post_name . '.');

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
			Log::error('In ' . __METHOD__ . ' connection failed: ' . $conn->connect_error);
			die ("Connection failed: " . $conn->connect_error);
		}

		Log::info('In ' . __METHOD__ . ' grabbing info for post ' . $post_name . '.');

		$comments = array();

		$result = $conn->query("SELECT * FROM vulpin WHERE Link='" . $post_name . "' ORDER BY Datetime");

		if (!empty($result) && $result->num_rows > 0) {
			Log::info('In ' . __METHOD__ . ' successfully grabbed comments for post');
			
			$i = 0;

			while ($row = $result->fetch_assoc ()) {
				$comments[$i] = new Comment($row['Name'], $row['Content']);

				$i += 1;
			}
		}
		
		else {
			Log::info('In ' . __METHOD__ . ' no comments were found for post ' . $post_name . '.');
		}

		Log::info('In ' . __METHOD__ . ' returning view for post ' . $post_name . ' with ' . (!empty($result) ? $result->num_rows : 0) . ' comments.');

		$conn->close();

		return view('posts.' . $post_name)->with('comments', $comments);
	}
}
