<?php
require_once __DIR__ . '/../bootstrap.php';
json_ok([
  'plans' => [
    ['name'=>'Basic',       'price'=>9.00,'features'=>['1 SIP Account','1 User','1,000 Call Minutes','5 GB Recordings','Basic Features']],
    ['name'=>'Professional','price'=>29.00,'popular'=>true,'features'=>['10 SIP Accounts','5 Users','5,000 Call Minutes','50 GB Recordings','Advanced Features','Priority Support']],
    ['name'=>'Enterprise',  'price'=>59.00,'features'=>['Unlimited SIP','Unlimited Users','Unlimited Minutes','200 GB Recordings','Advanced Features','Priority Support','Custom Integrations']],
  ],
]);
