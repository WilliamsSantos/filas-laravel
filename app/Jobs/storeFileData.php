<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\ImportQueue;
use App\Models\Document;

use \Carbon\Carbon;
use DB;

class storeFileData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue;

    private $importQueue;
    private $register;
    private $document;

    /**
     * Create a new job instance.
     */
    public function __construct($register=null)
    {
        $this->register = $register;
        $this->document = new Document;
        $this->importQueue = new ImportQueue;
    }

    public function handle()
    {
        $this->document->create($this->register);

        $this->importQueue->where('id', $this->register['id'])
        ->update([
            'status' => 'processed',
            'processed_at' => \Carbon\Carbon::now()
        ]);
    }
}
