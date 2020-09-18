<?php
namespace App\Services\Search;

use App\Models\User;
use App\Contracts\Searchable;

use DB;
use Illuminate\Database\Eloquent\Builder;

class UserSearchService implements Searchable
{

    /**
     * @var \Illuminate\Database\Eloquent\Builder
     */
    protected $users;

    /**
     * @var array
     */
    protected $requestData;

    /**
     * @var \App\Models\User
     */
    protected $loggedUser;

    /**
     * Constructor
     *
     * @param \Illuminate\Database\Eloquent\Builder $users
     * @param array $requestData
     * @param \App\Models\User $loggedUser
     *
     * @return void
     */
    public function __construct(Builder $users, array $requestData, User $loggedUser)
    {
        $this->users = $users;
        $this->requestData = $requestData;
        $this->loggedUser = $loggedUser;
    }

    /**
     * Search logic for users
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function search(): Builder
    {
        if (isset($this->requestData['show_users'])) {
            $this->segmentUsers($this->requestData['show_users']);
        } else {
            $this->users->active();
        }

        $this->filter();

        if (isset($this->requestData['sort_by'])) {
            $this->sortByField($this->requestData['sort_by']);
        }

        return $this->users;
    }

    /**
     * Filter users based on search input
     *
     * @return void
     */
    private function filter(): void
    {
        if (isset($this->requestData['email'])) {
            $this->users->where('email', 'LIKE', '%'.$this->requestData['email'].'%');
        }

        if (isset($this->requestData['full_name'])) {
            $this->users->where(DB::raw('concat(first_name," ",last_name)') , 'LIKE' , '%'.$this->requestData['full_name'].'%');
        }
    }

    /**
     * Segment users in deleted, blocked, active, or all
     *
     * @param string $userTypes
     *
     * @return void
     */
    private function segmentUsers(string $userTypes): void
    {
        switch($userTypes) {
            case 'all': $this->users->withTrashed();
                break;
            case 'normal': $this->users->active();
                break;
            case 'blocked': $this->users->blocked();
                break;
            case 'deleted': $this->users->onlyTrashed();
                break;
            default: $this->users->active();
        }
    }

    /**
     * @param string $field
     *
     * @return void
     */
    private function sortByField(string $field): void
    {
        try {
            if ($field == 'name') {
                $this->users->nameOrdered();
            } else {
                $this->users->idOrdered();
            }
        } catch (\Exception $ex) {

        }
    }

}