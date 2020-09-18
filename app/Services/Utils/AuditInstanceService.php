<?php
namespace App\Services\Utils;

class AuditInstanceService
{
    /**
     * @var mixed
     */
    private $auditable;

    private $instances = [
        'App\Models\Client' => 'business_name',
        'App\Models\User' => 'first_name,last_name',
        'App\Models\Installation' => 'domain_name',
    ];

    /**
     * @param mixed $auditable
     *
     * @return void
     */
    public function __construct($auditable = null)
    {
        $this->auditable = $auditable;
    }

    /**
     * @param mixed $auditable
     *
     * @return void
     */
    public function setAuditable($auditable): void
    {
        $this->auditable = $auditable;
    }

    /**
     * Check instances and assign the columns
     *
     * @return array
     */
    public function check(): array
    {
        foreach ($this->instances as $instanceName => $instanceColumns) {
            if ($this->auditable instanceof $instanceName) {
                return $this->getAccessor($instanceColumns);
            }
        }

        return $this->getNameAccessor();
    }

    /**
     * Assign columns needed to diplay in audits based on the class
     * they belong to, if two columns needed to be displayed as a single string,
     * then we declare them separated by ",", and concatenate in a foreach loop
     *
     * @param string $columns
     *
     * @return array
     */
    private function getAccessor(string $columns): array
    {
        $name = '';
        $columnsArray = explode(',', $columns);

        $auditArray = [
            'id' => $this->auditable['id'],
            'name' => null
        ];

        if (count($columnsArray) > 1) {
            foreach ($columnsArray as $column) {
                $name = trim($name.' '.$this->auditable[$column]);
            }
        } else {
            $name = $this->auditable[$columns];
        }

        $auditArray['name'] = $name;

        return $auditArray;
    }

    /**
     * If the class is not declared return a default array
     *
     * @return array
     */
    private function getNameAccessor(): array
    {
        return [
            'id' => $this->auditable['id'],
            'name' => $this->auditable['name']
        ];
    }
}