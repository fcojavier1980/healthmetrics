<?php

// Saber si estamos trabajando de forma local o remota
if(!defined('IS_LOCAL')) define('IS_LOCAL'   , in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']));

// Definir el uso horario o timezone del sistema
date_default_timezone_set('Europe/Madrid');

// Lenguaje
if(!defined('LANG')) define('LANG'       , 'es');

// Ruta base de nuestro proyecto
if(!defined('BASEPATH')) define('BASEPATH'   , IS_LOCAL ? '../public_html_api/' : '../');

if(!defined('BASEPATHPUB')) define('BASEPATHPUB'   , IS_LOCAL ? '../public_html_api/' : '../');

if(!defined('CLASSES')) define('CLASSES'   , IS_LOCAL ? '../public_html_api/' : '../public_html');

if(!defined('IMAGES')) define('IMAGES'   , IS_LOCAL ? '../public_html_api/' : '../');	
//Raíz del index.php	
if(!defined('INDEXPATH')) define('INDEXPATH'   , IS_LOCAL ? '../public_html_api/' : '../');

if(!defined('WEB_LOCAL')) define('WEB_LOCAL'   , IS_LOCAL ? '../public_html_api/' : '../public_html/');	

// Puerto y la URL del sitio
if(!defined('PORT')) define('PORT'       , '7879');


if(!defined('URL')) define('URL' , IS_LOCAL ? 'http://localhost/proyectos/public_html_api/' : '../../../');

if(!defined('URLWEB')) define('URLWEB' , IS_LOCAL ? 'http://localhost/proyectos/public_html_api/' : 'http://http://healthmetrics.esy.es/');