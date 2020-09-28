<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Custom Trasnslation variables
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */
    'unauthorized' => 'Unauthorized',
    'forbidden'    => 'You dont have enough permission to access this resource.',
    'not_found'    => 'Not found',
    'info' => 'Info',
    'error' => 'Error',
    'warning' => 'Warning',
    'success' => 'Success',
    'connection_problem' => 'Connection problems with the provider!',
    'invalid_auth_config' => 'Authorization config file is invalid!',
    'password_change_success' => 'Password was successfully changed!',
    'report' => [
        'not_successful' => 'The report was not successfully retrieved',
        'not_complete' => 'The report is not finished yet!',
        'error' => 'There was a problem with the report!',
        'not_owner' => 'User isn\'t the owner of this report'
    ],
    'user' => [
        'old_password_wrong' => 'Old password is not correct!',
        'restore_error' => 'An error occured while restoring user!',
        'delete_error' => 'Error in user deletion!',
        'error_processing' => 'Could not process user!',
        'not_found' => 'User was not found!',
        'is_blocked' => 'User is blocked!',
        'has_no_role' => 'User has no role!',
        'above_hierarchy' => 'User is above hierarchy',
        'outside_ldap_groups' => 'LDAP User does not belong to any of the given groups!',
    ],
    'profile' => [
        'success' => 'Profile data were successfully changed',
    ],
    'file_no_exist' => 'The requested file does not exist!',
    'queue_start' => 'Export is on process, visit exports page when the file is ready to download!',
    'email_problem' => 'There was a problem with e-mail',
    'account_reuthenticated' => 'This account was successfully reauthenticated!',
    'url' => 'This url is not valid',
    'site_permission' => 'The selected account does not have permission on this url'

];
