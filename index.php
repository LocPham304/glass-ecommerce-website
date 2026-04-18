<?php

declare(strict_types=1);

$target = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '/\\') . '/public/';

header('Location: ' . $target, true, 302);
exit;
