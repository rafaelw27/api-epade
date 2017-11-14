<?php

namespace Epade\Controllers;

use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;
use Epade\Models\Users;
use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;

/**
 * Base controller
 *
 */
class IndexController extends \Baka\Http\Rest\CrudExtendedController
{

    /**
     * Index
     *
     * @method GET
     * @url /
     *
     * @return Phalcon\Http\Response
     */
    public function index($id = null): Response
    {

        print_r("Git test");
        return $this->response(['Hello World']);
    }

    /**
     * Refreshes Quickbooks token
     *
     * @return void
     */
    public function refreshToken(): Response
    {

        $OAuth2LoginHelper = $this->quickbooks->getOAuth2LoginHelper();
        
        $accessToken = $OAuth2LoginHelper->refreshToken();

        $error = $OAuth2LoginHelper->getLastError();

        if ($error != null) {
            echo "The Status code is: " . $error->getHttpStatusCode() . "\n";
            echo "The Helper message is: " . $error->getOAuthHelperError() . "\n";
            echo "The Response message is: " . $error->getResponseBody() . "\n";
        }


        $this->quickbooks->updateOAuth2Token(getenv('ACCESS_TOKEN_KEY'));
        $CompanyInfo = $this->quickbooks->getCompanyInfo();
        $error =  $this->quickbooks->getLastError();
        if ($error != null) {
            echo "The Status code is: " . $error->getHttpStatusCode() . "\n";
            echo "The Helper message is: " . $error->getOAuthHelperError() . "\n";
            echo "The Response message is: " . $error->getResponseBody() . "\n";
        } else {
            $nameOfCompany = $CompanyInfo->CompanyName;
            echo "Test for OAuth Complete. Company Name is {$nameOfCompany}. Returned response body:\n\n";
            $xmlBody = XmlObjectSerializer::getPostXmlFromArbitraryEntity($CompanyInfo, $somevalue);
            echo $xmlBody . "\n";
        }

        return $this->response("Token regenerated");
    }
}
