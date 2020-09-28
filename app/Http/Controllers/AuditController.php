<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;

use App\Services\AuditService;
use App\Services\ExportService;
use App\Services\Exports\AuditExportService;

class AuditController extends Controller
{
    /**
     * @var \App\Services\AuditService
     */
    private $auditService;

    /**
     * Create a new controller instance.
     *
     * @param \App\Services\AuditService $auditService
     * 
     * @return void
     */
    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    /**
     * Store a newly created user in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAuditTypes()
    {
        try {

            $auditTypes = $this->auditService->getAuditTypes();

        } catch (Exception $ex) {
            
            return $this->errorResponse($ex);

        }

        return response()->json($auditTypes);
    }

    /**
     * Store a newly created user in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAuditEvents()
    {
        try {
            
            $auditEvents =  $this->auditService->getAuditEvents();

        } catch (Exception $ex) {

            return $this->errorResponse($ex);

        }

        return response()->json($auditEvents);
    }

    /**
     * Get all audits, with filters
     * 
     * @param  \Illuminate\Http\Request  $request
     * 
     * @return \Illuminate\Http\Response
     */
    public function getAudits(Request $request)
    {
        try {
        
            $audits = $this->auditService->getAudits($request->all());

        } catch (Exception $ex) {

            return $this->errorResponse($ex);

        }

        return response()->json($audits);
    }

    /**
     * Export Audits
     * 
     * @param  \Illuminate\Http\Request  $request
     * 
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request) 
    {
        try {

            ExportService::export(new AuditExportService, $request->all());
            
        } catch (Exception $ex) {
            
            return $this->errorResponse($ex);   

        }
    }

}
