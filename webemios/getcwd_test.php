<?php
echo "getcwd: " . getcwd() . "\n";
echo "DIR: " . __DIR__ . "\n";
echo "DOCUMENT_ROOT: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'N/A') . "\n";
