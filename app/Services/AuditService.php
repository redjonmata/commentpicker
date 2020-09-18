<?php
namespace App\Services;

use App\Models\Audit;
use App\Services\Search\AuditSearchService;

use DB;
use Log;
use JWTAuth;

class AuditService
{
    use \OwenIt\Auditing\Auditable;

    /**
     * Get unique audit types (models) from audit table
     *
     * @return array
     */
    public function getAuditTypes(): array
    {
        $auditableTypes = DB::table('audits')
            ->distinct('auditable_type')
            ->pluck('auditable_type')
            ->toArray();

        $hasExportTag = DB::table('audits')
            ->where('tags', 'export')
            ->first();

        $formattedTypes = $this->formatTypes($auditableTypes);

        if (isset($hasExportTag)) {
            $formattedTypes[] = ['model' => 'Export', 'tag' => 'export'];
        }

        return $formattedTypes;
    }

    /**
     * Get unique events in audit table
     *
     * @return array
     */
    public function getAuditEvents(): array
    {
        return DB::table('audits')
            ->select('event')
            ->distinct('event')
            ->pluck('event')
            ->transform(function($item) {
                return [
                    'key' => $item,
                    'value' => array_key_exists($item, trans('tags')) ? trans('tags.'.$item) : $item,
                ];

            })
            ->toArray();
    }

    /**
     * Paginate and filter audit data
     *
     * @param array $filterData
     *
     * @return array
     */
    public function getAudits(array $filterData): array
    {
        $audits = Audit::with(['user:id,first_name,last_name', 'auditable']);

        $perPage = $filterData['per_page'] ?? 10;

        $loggedUser = JWTAuth::user();

        $searchService = new AuditSearchService($audits, $filterData, $loggedUser);

        $audits = $searchService->search();

        $auditsPaginated = $audits->orderBy('created_at', 'desc')->paginate($perPage);

        return (new AuditTransformer)->paginationTransform($auditsPaginated);
    }

    /**
     * Custom audits for events not predicted by the default package
     *
     * Url, ip address and user agent gotten from Auditable trait
     *
     * @param string $event
     * @param mixed $auditable
     * @param string $tag
     *
     * @return void
     */
    public function customAudit(string $event, $auditable = null, string $tag = ''): void
    {
        $user = isset($auditable) ? $auditable : JWTAuth::user();

        try {

            $userType = get_class($user);

            $auditableType = isset($auditable) ? get_class($auditable) : $userType;

            Audit::create([
                'user_id' => $user->id,
                'auditable_id' => $auditable->id ?? $user->id,
                'event' => $event,
                'user_type' => $userType,
                'auditable_type' => $auditableType,
                'url' => $this->resolveUrl(),
                'ip_address' => $this->resolveIpAddress(),
                'user_agent' => $this->resolveUserAgent(),
                'tags' => $tag,
            ]);

        } catch (\Exception $ex) {
            Log::error($ex);
            Log::error('Could not audit this action');
            Log::error('Event: '. $event.' | User ID: '.$user->id.' | Url: '.$this->resolveUrl());
        }
    }

    /**
     * Format audit types name (models)
     *
     * @param array $auditableTypes
     *
     * @return array
     */
    private function formatTypes(array $auditableTypes): array
    {
        $formattedAuditableTypes = [];

        $regex = '/(?<=[a-z])(?=[A-Z])/x';

        foreach ($auditableTypes as $type) {
            $typeArray = explode('\\', $type);
            $model = end($typeArray);
            $modelName = preg_split($regex, $model);

            $formattedAuditableTypes[] = [
                'model' => join($modelName, " "),
                'tag' => strtolower(join($modelName, "-"))
            ];
        }

        return $formattedAuditableTypes;
    }
}