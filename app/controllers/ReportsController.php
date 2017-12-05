<?php

namespace Epade\Controllers;

use Epade\Models\Reports;
use Epade\Models\Users;
use Baka\Http\Rest\CrudExtendedController;
use Phalcon\Http\Response;
use QuickBooksOnline\API\Facades\Item;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;

class ReportsController extends \Baka\Http\Rest\CrudExtendedController
{


    /**
     * set objects
     *
     * @return void
     */
    public function onConstruct(): void
    {
        $this->model = new Reports();
    }

    /**
     * Creates a new Report
     *
     * @return Response
     */
    public function create(): Response
    {
        if ($this->request->isPost()) {

            $rawData = $this->request->getRawBody();
            $jsonData = json_decode($rawData);
            
            $user_id = $jsonData->user_id;
            $title = $jsonData->title;
            $description = $jsonData->description;
            $created_at = date("Y-m-d h:i:s");

            $report = new Reports();

            $report->user_id = $user_id ;
            $report->title = $title;
            $report->description = $description;
            $report->created_at = created_at;

            //If data comes from mobile app
            if($this->request->getContentType() == "application/x-www-form-urlencoded"){

                $request = $this->request->getPost();
                
                $report->user_id = $request['user_id'];
                $report->title = $request['title'];
                $report->description = $request['description'];
                $report->created_at = date("Y-m-d h:i:s");
                

            }


            if (!$report->save()) {
                throw new \Exception('Report could not be created');
            }

            return $this->response('Report Created');
        }
    }

    /**
     * Edits a  Report
     *
     * @return Response
     */
    public function edit($id): Response
    {
        if ($this->request->isPost()) {
            $request = $this->request->getPost();

            $report = $this->model::findFirst([
                "conditions" => "id = ?0",
                "bind" => [$id]
            ]);

            if ($report) {
                $report->title = $request['title'];
                $report->description = $request['description'];
                $report->updated_at = date("Y-m-d h:i:s");
    
                if (!$report->update()) {
                    throw new \Exception('Report could not be updated');
                }
    
                return $this->response($report);
            }
        }
    }

    public function delete($id) : Response
    {
        $report = $this->model::findFirst([
            "conditions" => "id = ?0",
            "bind" =>[$id],
        ]);
    
        if (!$report->delete()) {
            throw new \Exception("Report not found.");
        }
    
        return $this->response("Report Deleted.");
    }

    /**
     * Get all the reports from the database
     *
     * @return Response
     */
    public function getReports() : Response
    {
        $reportsArray = [];
        $reportArray= [];
        
        $reports = $this->model::find();
    
        if(!$reports){
            throw new \Exception("There are no trucks");
        }
    
        foreach ($reports as $report) {
            
            $user = Users::findFirst([
                "conditions" => "id = ?0",
                "bind" => [$report->user_id]
            ]);
            
            $reportArray['id'] = $report->id;
            $reportArray['user_id'] = $user->id;
            $reportArray['first_name'] = $user->first_name;
            $reportArray['last_name'] = $user->last_name;
            $reportArray['title'] = $report->title;
            $reportArray['description'] = $report->description;
            $reportArray['created_at'] = $report->created_at;
    
            
    
            $reportsArray[] = $reportArray;
        }
    
        return $this->response($reportsArray);
    }

    /**
     * Fetches a specific report by its id
     *
     * @param [type] $id
     * @return Response
     */
    public function getReport($id) : Response
    {
        $report = $this->model::findFirst([
            "conditions" => "id = ?0",
            "bind" =>[$id],
        ]);
    
        if (!$report) {
            throw new \Exception('Report not found');
        }
        
        return $this->response($report);
    }

    /**
     * Fetches the reports of a specific user
     *
     * @param [type] $id
     * @return void
     */
    public function getReportByUser($id){

        $reportsArray = [];
        $reportArray= [];
        
        $reports = $this->model::find([
            "conditions" => "user_id = ?0",
            "bind" => [$id]
        ]);
    
        if(!$reports){
            throw new \Exception("There are no trucks");
        }
    
        foreach ($reports as $report) {
            
            $user = Users::findFirst([
                "conditions" => "id = ?0",
                "bind" => [$report->user_id]
            ]);
            
            $reportArray['id'] = $report->id;
            $reportArray['user_id'] = $user->id;
            $reportArray['first_name'] = $user->first_name;
            $reportArray['last_name'] = $user->last_name;
            $reportArray['title'] = $report->title;
            $reportArray['description'] = $report->description;
            $reportArray['created_at'] = $report->created_at;
    
            
    
            $reportsArray[] = $reportArray;
        }
    
        return $this->response($reportsArray);

    }
}
