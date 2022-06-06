<?php
	/*
	 * @author	Julio Cesar Garcés Rios
	 * @email	lider.desarrollo@gsc.com.co
	 */
	class Config  {
		public static $extensiones_permitidas = array("pdf", "jpg", "png", "xls", "xlsx", "doc", "docx");
		public static $DOMINIO = "http://intranet.celar.com.co/celrisk";
		public static $content_type = array(
			"png" => "image/png",
			"jpg" => "image/jpg",
			"pdf" => "application/pdf",
			"doc" => "application/octet-stream",
			"xls" => "application/octet-stream",
			"docx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
			"xlsx" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
		);

		public static $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');

		public static function getContentType($ext) {
			$ext = strtolower($ext);
			return isset(self::$extensiones_permitidas[$ext]) ? self::$extensiones_permitidas[$ext] : "application/octet-stream";  
		}

		public static function format_size($size) {
			$mod = 1024;
			for ($i = 0; $size > $mod; $i++)
				$size /= $mod;
			$endIndex = strpos($size, ".")+3;
			return substr( $size, 0, $endIndex) . self::$units[$i];
		}

		public static function foldersize($path) {
			$total_size = 0;
			$files = scandir($path);
			$cleanPath = rtrim($path, '/'). '/';
			foreach($files as $t) {
				if ($t <> "." && $t <> "..") {
					$currentFile = $cleanPath . $t;
					if (is_dir($currentFile)) {
						$size = self::foldersize($currentFile);
						$total_size += $size;
					}
					else {
						$size = filesize($currentFile);
						$total_size += $size;
					}
				}
			}
			return $total_size;
		}
	}
?>