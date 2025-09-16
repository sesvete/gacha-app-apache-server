<?php

header('Content-Type: application/json');

session_start();
session_unset();
session_destroy();

http_response_code(200);
echo json_encode(["status" => "success", "message" => "Logged out successfully."]);
die();