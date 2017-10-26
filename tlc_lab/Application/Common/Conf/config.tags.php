<?php
return array(
    'app_init' => array(
        ini_set("session.save_handler", "memcache"),
        ini_set("session.save_path", 'tcp://127.0.0.1:'.MEMCACHE_PORT),
        ini_set("session.gc_maxlifetime", 43200),
        ini_set('session.cookie_domain', C('COOKIE_FULLE_DOMAIN')),
        ini_set('session.cookie_lifetime', C('COOKIE_EXPIRE')),
    )
);
?>
