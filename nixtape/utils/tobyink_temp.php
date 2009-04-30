<?php

require_once '../database.php';

$mdb2->query('ALTER TABLE Users ADD COLUMN laconica_profile VARCHAR(255);');

$mdb2->query('ALTER TABLE Users ADD COLUMN journal_rss VARCHAR(255);');

