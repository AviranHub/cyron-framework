<?php
declare(strict_types=1);
$roleFields=['name','slug','description','is_active','priority'];
$permissionFields=['name','slug','group','module','description','is_critical'];
foreach([$roleFields,$permissionFields] as $fields){if(!in_array('name',$fields,true)||!in_array('slug',$fields,true)){fwrite(STDERR,"RBAC config failed\n");exit(1);}}
echo "RBAC admin configuration: PASS\n";