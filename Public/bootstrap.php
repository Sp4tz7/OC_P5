<?php
/* Test if url "/" or "/admin/" loads correct application
** "/" expected: Frontend
** "/admin/" expected: Backend
*/
var_dump($_GET['app']);