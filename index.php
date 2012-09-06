<?php
include(__DIR__.'/lib/jsonrpcServer.php');
$server = new jsonrpcServer();


$server->register(function(){return '123';},'activity');
$server->listen();
