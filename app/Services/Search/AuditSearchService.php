<?php

namespace App\Services\Search;

use App\Models\User;
use App\Libraries\Sanatize;
use App\Contracts\Searchable;

use DB;
use Illuminate\Database\Eloquent\Builder;

class AuditSearchService implements Searchable
{

    protected $audits;
    protected $requestData;
    protected $loggedUser;

    /**
     * Constructor
     *
     * @param \Illuminate\Database\Eloquent\Builder $audits
     * @param array $requestData
     * @param \App\Models\User $loggedUser
     *
     * @return void
     */
    public function __construct(Builder $audits, array $requestData, User $loggedUser)
    {
        $this->audits = $audits;
        $this->requestData = $requestData;
        $this->loggedUser = $loggedUser;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $audits
     * @param array $this->requestData
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function search(): Builder
    {

        if (isset($this->requestData['event'])) {
            $this->audits->where('event', $this->requestData['event']);
        }

        if (isset($this->requestData['tags'])) {
            $this->audits->where('tags', $this->requestData['tags']);
        }

        if (!empty($this->requestData['user'])) {
            // sanitize input content for search
            $userSearchTerm = $this->requestData['user'];
            $searchTerms = Sanatize::sanitize(urldecode($userSearchTerm));
            $this->audits->whereHas('user', function($query) use ($searchTerms) {
                // search for each term individually
                $words = explode(' ', $searchTerms);
                foreach ($words as $key => $word) {
                    if (!empty($word)) {
                        $word = str_replace(array('%', '_'), array('\\%', '\\_'), $word);
                        $start_search = '%';
                        $end_search = '%';

                        // build query string
                        $searchTerm = (($word[0] == '-') ? ($start_search . ltrim($word, '-') . $end_search)
                            : ($start_search . $word . $end_search)
                        );

                        $query->where(function($subQuery) use ($searchTerm){
                            $subQuery->where('first_name', 'like', $searchTerm)
                                ->orWhere('last_name', 'like', $searchTerm);
                            return $subQuery;
                        });
                    }  else {
                        unset($words[$key]);
                    }
                }
                return $query;
            });
        }

        return $this->audits;
    }

}
