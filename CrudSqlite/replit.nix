{ pkgs }: {
	deps = [
		pkgs.php
		pkgs.phpExtensions.pdo_sqlite
		pkgs.sqlite
	];
}