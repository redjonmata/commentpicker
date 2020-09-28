<?php
namespace App\Services;

use App\Models\Export;
use App\Contracts\Exportable;
use App\Transformers\ExportTransformer;

use JWTAuth;
use ChannelLog;
use Illuminate\Support\Facades\Storage;

class ExportService
{
    /**
     * @var string
     */
    protected $fileName;

    /**
     * @param \App\Contracts\Exportable $exportable
     * 
     * @return void
     */
    public static function export(Exportable $exportable, array $requestData): void
    {
        $exportable->setRequestData($requestData);
        $exportable->export();
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * Get exports
     * 
     * @param array $data
     */
    public function get(array $data)
    {
        $loggedUser = JWTAuth::user();

        $perPage = $data['per_page'] ?? 10;

        $exports = Export::where('user_id', $loggedUser->id)
                        ->orderBy('created_at', 'desc')
                        ->paginate($perPage);

        return (new ExportTransformer)->paginationTransform($exports);                
    }

    /**
     * @param int $id
     * 
     * @return string
     * 
     * @throws \Exception if file does not exist 
     */
    public function download(string $token): string
    {
        $export = Export::where('token', $token)->where(function($query){
            $query->where('status', 'completed')
                ->orWhere('status', 'downloaded');
        })->firstOrFail();
    
        if (!Storage::has('exports/'.$export->name)) {
            throw new \Exception(trans('messages.file_no_exist'));
        }

        $file = Storage::get('exports/'.$export->name);

        $this->fileName = $export->name;

        // $export->status = 'downloaded';
        // $export->save();

        return $file;
    }

    /**
     * Used in scheduler to delete downloaded files and records
     * Weekly
     * 
     * @return void
     */
    public function delete(): void
    {
        try {
            $exports = Export::fromLastWeek('downloaded');

            $count = $exports->count();

            $xportCollection = $exports->get();

            $fileNames = $xportCollection->pluck('name')->toArray();

            Storage::delete($fileNames);

            Export::fromLastWeek('failed')->delete();

            $exports->delete();

            ChannelLog::info('cron-jobs', 'Exports Deletion Successful', ['deleted' => $count]);

        } catch (\Exception $ex) {
            ChannelLog::error('cron-jobs', 'Error happened in Exports Deletion', ['error' => $ex->getMessage(), 'line' => $ex->getLine()]);
        }
    }
}