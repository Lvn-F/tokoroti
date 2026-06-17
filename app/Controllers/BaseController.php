<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Session\Session;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    protected Session $session;

    /**
     * Helpers available to all controllers extending this class.
     * 'activity' loads app/Helpers/activity_helper.php -> logActivity()
     *
     * @var list<string>
     */
    protected $helpers = ['activity'];

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Load here all helpers you want to be available in your controllers that extend BaseController.
        // Caution: Do not put the this below the parent::initController() call below.
        // $this->helpers = ['form', 'url'];

        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        $this->session = service('session');
    }

    /**
     * Convenience wrapper so controllers can call $this->logActivity(...)
     * instead of the global logActivity() function. Both behave
     * identically — use whichever reads better in context.
     */
    protected function logActivity(string $aktivitas, string $modul): bool
    {
        return logActivity($aktivitas, $modul);
    }

    /**
     * Shortcut to get the currently logged-in user's id from session.
     * Returns null if nobody is logged in.
     */
    protected function currentUserId(): ?int
    {
        $id = $this->session->get('id_user');

        return $id !== null ? (int) $id : null;
    }

    /**
     * Shortcut to get the currently logged-in user's role from session.
     * Returns null if nobody is logged in.
     */
    protected function currentUserRole(): ?string
    {
        return $this->session->get('role');
    }
}