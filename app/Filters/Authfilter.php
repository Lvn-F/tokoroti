<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * AuthFilter
 * ----------------------------------------------------------------------
 * Two jobs:
 *   1. Block access entirely if nobody is logged in (-> redirect to /login).
 *   2. If an argument is passed (one or more roles), only allow users
 *      whose session role is in that list. Anyone else gets a 403.
 *
 * Usage in Routes.php:
 *
 *      // Just require login, any role:
 *      $routes->get('dashboard', 'Dashboard::index', ['filter' => 'auth']);
 *
 *      // Require login AND restrict to specific roles:
 *      $routes->get('produk', 'Produk::index', ['filter' => 'auth:admin,kasir']);
 *
 * Or grouped:
 *
 *      $routes->group('produk', ['filter' => 'auth:admin'], function ($routes) {
 *          $routes->get('/', 'Produk::index');
 *      });
 */
class AuthFilter implements FilterInterface
{
    /**
     * @param array<int, string>|null $arguments Allowed roles, e.g. ['admin', 'kasir'].
     *                                            If null/empty, only login is required.
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (! $session->get('isLoggedIn')) {
            return redirect()->to('/login')->with('loginError', 'Silakan login terlebih dahulu.');
        }

        if (! empty($arguments)) {
            $role = $session->get('role');

            if (! in_array($role, $arguments, true)) {
                return service('response')
                    ->setStatusCode(403)
                    ->setBody('403 - Anda tidak memiliki akses ke halaman ini.');
            }
        }

        return null;
    }

    /**
     * @param array<int, string>|null $arguments
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nothing to do after the request.
    }
}