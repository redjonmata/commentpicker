<?php
namespace App\Services\Exports;

use App\Jobs\AuditsExportJob;
use App\Contracts\Exportable;
use App\Services\AuditService;

use JWTAuth;
use ChannelLog as Log;

class AuditExportService implements Exportable
{
    /**
     * @var array
     */
    private $requestData;

    /**
     * @param array $requestData
     * 
     * @return void
     */
    public function setRequestData($requestData): void
    {
        $this->requestData = $requestData;
    }

    /**
     * @return void
     */
    public function export(): void
    {
        $loggedUser = JWTAuth::user();

        Log::info('audit', 'Audits CSV Export', ['author' => $loggedUser->id]);
            
        (new AuditService)->customAudit('audits-export', $loggedUser, 'export');

        $exportData = [
            'audit_name' => 'Audited', 
            'event' => 'Event', 
            'tags' => 'Type',
            'user.full_name' => 'Author',
            'ip_address' => 'IP Address',
            'user_agent' => 'User Browser Agent',
            'created_at' => 'Created',
        ];

        $name = 'audits-'.date('Y-m-d_His').'.csv';

        # the reason audits were dispatched in a different job than other exports is to make the 
        # audit query in the job class, since it might be time consuming
        dispatch(new AuditsExportJob($this->requestData, $exportData, $name, $loggedUser));
    }
}