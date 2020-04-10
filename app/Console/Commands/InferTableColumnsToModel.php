<?php

namespace App\Console\Commands;

use App\Item;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InferTableColumnsToModel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'detect-columns {model} {path?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    private $model;
    private $path;
    private $modelAbsolutePath;
    private $tableColumns;
    private $completeTraitTemplate;
    private $traitProductionPath;
    /**1
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $this->model = $this->argument('model');
        $this->path = $this->argument('path') ?? 'App\\';
        $this->modelAbsolutePath = $this->path.$this->model;
        $this->traitProductionPath = app_path('ProducedTraits/'.date('YmdHis'));
        #STEP 1: ottieni dal model le colonne della tabella a cui fa riferimento
        $this->setTableColumns();


        #STEP 2:
        $this->createTraitWithTableColumns();


        #STEP 3: metti queste colonne in un file di PERCORSO NOTO
        $this->saveTraitToDateTimeAppFolder();

        $traitName = $this->model.'Columns';
        $traitPath = $this->path.$this->model.'Columns';
        var_dump("Trait $traitPath created in $this->traitProductionPath");
        var_dump( "Don't forget to put the trait [$traitName] under the same path as the model!");

    }


    #step 1
    private function setTableColumns(){
        $modelInstance = new $this->modelAbsolutePath();
        $this->tableColumns = collect($modelInstance->getConnection()->
            getSchemaBuilder()->
            getColumnListing($modelInstance->getTable()));

        $this->tableColumns = $this->tableColumns->map(function ($tableColumn){
            return '$'.$tableColumn;
        });
    }


    #step 2
    private function createTraitWithTableColumns(){

        $traitTemplate = $this->getStub();
        $traitTemplate = str_replace('{{model_namespace}}',rtrim($this->path,'\\').';',$traitTemplate);
        $traitTemplate = str_replace('{{model_name}}',$this->model,$traitTemplate);
        $traitTemplate = str_replace('{{columns}}',$this->formatColumns(),$traitTemplate);
        $this->completeTraitTemplate = $traitTemplate;



    }



    private function saveTraitToDateTimeAppFolder(){
       # dd($this->traitProductionPath);
        if (!File::isDirectory($this->traitProductionPath))
            File::makeDirectory($this->traitProductionPath,0755,true);
        file_put_contents($this->traitProductionPath.'/'.$this->model.'Columns.php' ,$this->completeTraitTemplate);
    }



    private function getStub(){

        return file_get_contents(storage_path("stubs/model-columns-trait.stub"));
    }


    private function formatColumns(){

        $columnsAsPrivateMembersLine = implode(',',$this->tableColumns->toArray());
        return 'private '.$columnsAsPrivateMembersLine . ';';
    }



}
