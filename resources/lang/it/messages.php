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
    'unauthorized' => 'Non autorizzato',
    'forbidden'    => 'Non disponi delle autorizzazioni necessarie per accedere alla risorsa.',
    'not_found'    => 'Non trovato',
    'info' => 'Info',
    'error' => 'Errore',
    'warning' => 'Attenzione',
    'success' => 'Success',
    'connection_problem' => 'Problemi di connessione con il provider!',
    'invalid_auth_config' => 'Configurazione di autorizzazione non valida!',
    'password_change_success' => 'La password è stata cambiata con successo!',
    'report' => [
        'not_successful' => 'Il rapporto non è stato recuperato correttamente',
        'not_complete' => 'Il rapporto non è ancora finito!',
        'error' => 'Si è verificato un errore nel rapporto!',
    ],
    'user' => [
        'old_password_wrong' => 'La vecchia password non è corretta!',
        'restore_error' => 'Errore nel ripristino dell\'utente!',
        'delete_error' => 'Error in user deletion!',
        'error_processing' => 'Impossibile elaborare l\'utente!',
        'not_found' => 'Utente non trovato nel sistema!',
        'is_blocked' => 'L\'utente è bloccato!',
        'has_no_role' => 'L\'utente non ha alcun ruolo!',
        'above_hierarchy' => 'L\'utente è al di sopra della gerarchia',
        'outside_ldap_groups' => 'L\'utente LDAP non appartiene a nessuno dei gruppi specificati!',
    ],
    'profile' => [
        'success' => 'I dati del profilo sono stati modificati con successo',
    ],
    'file_no_exist' => 'Il file non esiste!',
    'queue_start' => 'L\'esportazione è in corso, visita la pagina delle esportazioni quando il file è pronto per il download!',
    'email_problem' => 'Si è verificato un problema con la posta elettronica',
    'non_existing_tags' => 'The following tags don\'t exist: ',
    'account_reuthenticated' => 'Questo account è stato riautenticato con successo!',
    'url' => 'Questo URL non è valido',
    'site_permission' => 'L\'account selezionato non dispone dell\'autorizzazione per questo URL'
];
