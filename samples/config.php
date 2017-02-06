<?php

// An example of loading your config from environment variables with optional defaults.
return array(
    'endpoint' => getenv('NETSUITE_ENDPOINT') ?: '2016_1',
    'host'     => getenv('NETSUITE_HOST')     ?: 'https://webservices.netsuite.com',
    'email'    => getenv('NETSUITE_EMAIL')    ?: 'jDoe@netsuite.com',
    'password' => getenv('NETSUITE_PASSWORD') ?: 'mySecretPwd',
    'role'     => getenv('NETSUITE_ROLE')     ?: '3',
    'account'  => getenv('NETSUITE_ACCOUNT')  ?: 'MYACCT1',
    'appid'    => getenv('NETSUITE_APP_ID')   ?: '9DB49F44-9854-44E9-8527-115AE98823A5',
    'logging'  => getenv('NETSUITE_LOGGING')  ?: false,
    'log_path' => getenv('NETSUITE_LOG_PATH') ?: '',
);
