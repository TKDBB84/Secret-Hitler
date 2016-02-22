<?php
	set_time_limit(0);


	require_once __DIR__ .'/vendor/autoload.php';
	require __DIR__.'/Game/SecretHitler.php';

	use Game\SecretHitler;
	use Game\Player;
	use Ratchet\Http\HttpServer;
	use Ratchet\WebSocket\WsServer;

	use Ratchet\Server\IoServer;

	$server = IoServer::factory(
		new HttpServer(
			new WsServer(
				new SecretHitler()
			)
		),9124);

	$server->run();

