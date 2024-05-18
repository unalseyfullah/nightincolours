<?php
/** 
 * Zakladna konfiguracia pre WordPress.
 *
 * Tento subor ma nasledujuce konfiguracie: nastavenia MySQL, predpony tabulky,
 * tajne kluce, jazyk WordPress, a ABSPATH. Mozete sa dozvediet viac informacii
 * navstivenim {@link http://codex.wordpress.org/Editing_wp-config.php Uprava
 * wp-config.php} Codex Stranky. Nastavenia MySQL mozete zuskat z vasho hostingu.
 *
 * Tento subor je pouzity vytvaracim skriptom pre wp-config.php pocas
 * instalacie. Nemali by ste ho pouzivat na stranke, staci nakopirovat jeho obsah
 * do "wp-config.php" a vyplnit hodnoty.
 *
 * @package WordPress
 */

// ** Nastavenia MySQL - Tieto informacie mozete ziskat od vasho hostingu ** //
/** Meno databazy pre WordPress */
//define('DB_NAME', 'wwwnight_badehomewp');
define('DB_NAME', 'nightincolours');

/** Uzivatel databazy MySQL */
//define('DB_USER', 'wwwnight');
define('DB_USER', 'root');

/** Heslo databazy MySQL */
//define('DB_PASSWORD', 'Bouchee-Lait@Milk35');
define('DB_PASSWORD', '');

/** Umiestnenie databazy MySQL */
//define('DB_HOST', 'localhost');
define('DB_HOST', 'localhost');

/** Kodvanie databazy pouzivane pri tvorbe databazovych tabuliek. */
define('DB_CHARSET', 'utf8');

/** Databazova kolekcia. Nemente, pokial si nieste isty. */
define('DB_COLLATE', '');

/**#@+
 * Autorizacia unikatnych klucov.
 *
 * Zmente tieto na rozne unikatne frazy! 
 * Mozete ich vytvarat pomocou {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * Tieto mozete zmenit kedykolvek to uznate za vhodne, aby ste znehodnotili vsetky existujuce cookies. Toto donuti vsetkych uzivatelov sa znova prihlasit.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '|+t#d*QT/0wA!|FXfj{#D#g>;gwbEX1GTvT`n+S<.b^5my_1qH+p9*XlA186Z20,');
define('SECURE_AUTH_KEY',  ',S)*XT~GnB2:`8}N=1<30pEx@1| 3`{H[&D|(ZZF~hGq_9qAAJ.;+/9t2|)L-F. ');
define('LOGGED_IN_KEY',    '#m;T*MPW:OoHb-@c{sW]B%D&oR]~<Ogow).JsomWje|l?$vAbYqDA,c J938GUZY');
define('NONCE_KEY',        'e5fn5s9Cd_9JXbdQ?W m`K%_z(R!)a0SIexg:&J(g:5<~2<hiDa?`U6]IrT]RpjO');
define('AUTH_SALT',        '`cpz5`*!+D|10Mu@{K|A/urcPVhp:Fs7 A3/@}1ksx=}CQPJS-#?;4 d3Ho-,TxA');
define('SECURE_AUTH_SALT', 'zITaGX>Ye3aQ,GqZzEz}& ZS#+y%,2HqGK4j/Aj3H<$=j!l5u|6ll2R,S+sB]MoI');
define('LOGGED_IN_SALT',   'wGF6+d0nGdMUP(qDaPAJ3LB4*z80r^k=pZ7l+5G.2zu.a+4ju0#ZeZ~7W-qL7KPZ');
define('NONCE_SALT',       'R9=/Tbib;|IFJ^vw+BG5Qj80`3O$ez4nDdUd#M(ALdGI3fu}FBd:[ 8`^rM /rMu');

/**#@-*/

/**
 * Predpona databazovej tabulky WordPress.
 *
 * Mozete mat viacero instalacii v jednej databaze tym ak kazdej date unikatnu
 * predponu. Len cisla, pismena a podtrhnutia, prosim!
 */
$table_prefix  = 'badeh_';

/**
 * Lokalizacny jazyk WordPress, zakladne nastavenie Slovencina (sk_SK).
 *
 * Toto zmente pokial chcete zmenit jazyk WordPress.  Musi suhlasit s MO suborom pre vybrany
 * jazyk ktory musi byt instalovany do wp-content/languages. Ako priklad, instalovany subor
 * de.mo do wp-content/languages a nastavenie WPLANG na 'de' zapne podporu nemeckeho
 * jazyka. Pre anglictinu nechajte hodnotu prazdnu ''.
 */
define ('WPLANG', 'sk_SK');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

define('WP_MEMORY_LIMIT','128m');

/* To je vsetko, prestante upravovat! Vesele blogovanie. */

/** Absolutna cesta WordPress k priecinku WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Nastavenia premennych WordPress a vkladanych suborov. */
require_once(ABSPATH . 'wp-settings.php');
