<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\Request;
use App\Models\Dataset;

class ProcessGeocoder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $register_ids;
    private $id;
    private $classObject;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id, $register_ids = null)
    {
        $this->id = $id;
        $this->register_ids = $register_ids;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        echo("Executing job");
        $dataset = Dataset::find($this->id);
        $year = $dataset->year;
        $initial = $dataset->initial;
        $system = $dataset->system;
        $source = $dataset->source;

        $class = DataSet::getClass($source, $system, $initial);

        $object = new $class();
        $object->geocoder($this->id, $this->register_ids);

    }
}
