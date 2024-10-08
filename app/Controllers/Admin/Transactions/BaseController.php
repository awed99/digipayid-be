<?php

namespace App\Controllers\Admin\Transactions;

date_default_timezone_set('Asia/Jakarta');

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    public $helpers = ['my_helper'];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * Constructor.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);


        // header('Access-Control-Allow-Origin: *');
        // header('Access-Control-Allow-Origin: *');
        // // header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization, Token-Login');
        // // header("Access-Control-Allow-Headers: Authorization, authorization");
        // // header("Access-Control-Allow-Headers: *");
        // header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        // header("Access-Control-Allow-Credentials: true");
        // // header('Content-Type: application/json');
        // header('Connection: keep-alive');
        // header("HTTP/1.0 200 OK");

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = \Config\Services::session();
    }
}
