<?php
require_once __DIR__ . '/config.php';

logout_user();
session_start();
set_flash('success', 'You have been logged out securely.');
redirect('index.php');
