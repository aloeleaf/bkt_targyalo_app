<?php
return [
    'ldap_server' => 'ldap://10.15.49.100',
    'ldap_domain' => 'BIROSAG',
    'ldap_base_dn' => 'OU=Users,OU=Torvenyszek,OU=BudapestKornyeki_Tvsz,OU=Szervezet,DC=birosagiad,DC=hu',
    //'ldap_group_dn' => 'CN=BKT_WebLoginGroup,OU=Groups,OU=Torvenyszek,OU=BudapestKornyeki_Tvsz,OU=Szervezet,DC=birosagiad,DC=hu',
    'ldap_required_groups' => ['BKT_WebLoginGroup', 'BKT_WebLoginGroupAdmin'], // Itt van az AD csoporttagság felsorolva, innen veszi a rendszer a jogosultságokat
    'db_host' => 'targyalo-db-1',
    'db_name' => 'bktAppdb',
    'db_user' => 'dbappuser',
    'db_pass' => 'p1ssw2rd'
];
