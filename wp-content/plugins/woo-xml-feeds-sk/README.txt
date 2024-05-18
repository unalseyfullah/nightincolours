=== Woo XML Feed ===
Requires at least: 3.8
Tested up to: 4.1
Stable tag: 1.1.0
License: 
License URI: 

== Popis ==
* Plugin generuje xml feedy pro porovnávače zboží. 

== Instalace ==
* Plugin je možné nainstalovat pomocí FTP, nebo nahráním Zip souboru v administraci

= Minimální požadavky =
* WordPress 3.8 nebo vyšší
* PHP version 5.2.4 nebo vyšší
* MySQL version 5.0 nebo vyšší

== Changelog ==

= 2.0.5 =
* Upraveny chyby v generování feedů a nastavení

= 2.0.4 =
* Přidáno nastavení a generování xml feedu pro 123 Nákup.sk
* Přidáno nastavení a generování xml feedu pro Google nákupy

= 2.0.3 =
* Úprava generování Pricemaina feedu
* Oprava generování Najnakup feedu

= 2.0.2 =
* Oprava špatného ukládání dat u varianty produktu

= 2.0.1 =
* Opravena chyba v detekování bazarové položky
* Opravena chyba ukládání zahrnutých kategorií
* Opravena chyba stránkování kategorií
* Opravena chyba generování feedu Heuréka.cz
* Opravena chyba generování feedu Heuréka.sk

= 2.0.0 =
* Změna použité technologie pro generování xml souborů, umožňující generovat feed i o desetitisících produktech
* Možnost vypnutí jQuery našeptávače Heuréka kategorií
* Možnost skrytí bazarových položek z Heuréka feed (problém se zařazením do systému Heuréky)
* Přidáno stránkování pro přiřazení kategorií, při větším množství kategorií docházelo k zahcení paměti.
* Přidány dvě nápovědní stránky s výpisem kategorií Heuréka.cz a Heuréka.sk
* Přidán data manager, umožňující editaci všech položek důležitých pro feedy, pro produkty i jejich varianty

= 1.1.3 =
* Úprava nastavení položka skladem u Heuréky a její provázání s nastavením skladu u produktu


= 1.1.2 =
* Změna přesměrování v administraci - místo home_url použito admin_url pro zohlednění umístění instalace Wordpressu v podsložce rootu 

= 1.1.1 =
* 
 
= 1.1.0 =
* Kompletní přepracování systému pro generování feedů

= 1.0.0 =
* Vydání pluginu