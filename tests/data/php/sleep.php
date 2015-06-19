<?php

$duration = (float) $_REQUEST['duration'];
usleep(1000000 * $duration);
