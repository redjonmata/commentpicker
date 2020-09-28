<?php
namespace App\Transformers;

use App\Models\Audit;
use App\Services\Utils\AuditInstanceService;

use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

use Illuminate\Pagination\LengthAwarePaginator;

class AuditTransformer extends TransformerAbstract
{
    /**
     * @var bool
     */
    private $singleItem = false;

    /**
     * @param bool $item
     * 
     * @return void
     */
    public function setSingleItem(bool $item): void 
    {
        $this->singleItem = $item;
    }

    /**
     * @param \App\Models\Audit
     * 
     * @return array
     */
	public function transform(Audit $audit)
	{
        $auditInstance = new AuditInstanceService($audit->auditable);

        if ($this->singleItem) {
            return [
                'id' => (int) $audit->id,
                'event' => $audit->event,
                'old_values' => $audit->vat,
                'updated_by' => $audit->updatedBy->full_name ?? null,    
            ];
        }

	    return [
	        'id' => (int) $audit->id,
	        'event' => array_key_exists($audit->event, trans('tags')) ? trans('tags.'.$audit->event) : $audit->event,
            'old_values' => $audit->old_values,
            'new_values' => $audit->new_values,
            'url' => $audit->url,
            'ip_address' => $audit->ip_address,
            'user_agent' => $audit->user_agent,
            'tags' => ucwords(str_replace('-', ' ', $audit->tags)),
            'user' => $audit->user->full_name ?? null,
            'audited' => $auditInstance->check(),
            'created_at' => $audit->created_at,
	    ];
	}

    /**
     * @param \App\Models\Audit
     * 
     * @return array
     */
    public function modelTransform(Audit $audit): array
    {
        $this->setSingleItem(true);

        $resource = new Item($audit, $this);
        $audit = (new Manager)->createData($resource);

        return $audit->toArray();
    }

    /**
     * @param \Illuminate\Pagination\LengthAwarePaginator $audits
     * 
     * @return array
     */
    public function paginationTransform(LengthAwarePaginator $audits): array
    {
        $collection =  new Collection($audits->getCollection(), $this);
        $collection->setPaginator(new IlluminatePaginatorAdapter($audits));
        $audits = (new Manager)->createData($collection);

        return $audits->toArray();
    } 
}
