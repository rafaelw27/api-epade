<?php

namespace Epade\Controllers;

use Epade\Models\Reports;
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
            $request = $this->request->getPost();

            $report = new Reports();
            $report->user_id = $request['user_id'];
            $report->title = $request['title'];
            $report->description = $request['description'];
            $report->created_at = date("Y-m-d h:i:s");

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

    public function getReports() : Response
    {
        $reports = $this->model::find();
        
        if (!$reports) {
            throw new \Exception("There are no Reports");
        }
        
            return $this->response($reports);
    }

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
}
