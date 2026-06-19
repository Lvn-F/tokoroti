<?php

/**
 * Activity Log Helper
 * ----------------------------------------------------------------------
 * Provides a single, reusable function to record actions into the
 * `log_activity` table. Call this from any controller, anywhere you
 * want an audit trail entry (login, logout, CRUD, transaksi, etc).
 *
 * Usage:
 *      logActivity('Login ke sistem', 'Auth');
 *      logActivity('Menambah produk: Roti Tawar', 'Produk');
 *      logActivity('Menghapus bahan baku: Tepung Terigu', 'Bahan Baku');
 *
 * If no user is logged in (e.g. failed login attempt before session is
 * set), pass the user id manually as the 3rd argument, or it will be
 * skipped safely without throwing an error.
 */

use App\Models\M_LogActivity;

if (! function_exists('logActivity')) {
    /**
     * Record an activity log entry.
     *
     * @param string   $aktivitas Human-readable description of what happened.
     * @param string   $modul     Module/feature name, e.g. 'Auth', 'Produk', 'Penjualan'.
     * @param int|null $idUser    Override user id (defaults to session 'id_user').
     *
     * @return bool True if the log was saved, false if skipped or failed.
     */
    function logActivity(string $aktivitas, string $modul, ?int $idUser = null): bool
    {
        $session = session();
        $idUser ??= $session->get('id_user');

        // No known user (e.g. anonymous/failed login) — skip silently.
        // We don't want a missing user id to ever break the actual request.
        if (empty($idUser)) {
            return false;
        }

        $request = service('request');

        try {
            $model = new M_LogActivity();

            return (bool) $model->insert([
                'id_user'    => $idUser,
                'aktivitas'  => $aktivitas,
                'modul'      => $modul,
                'ip_address' => $request->getIPAddress(),
            ]);
        } catch (\Throwable $e) {
            // Logging must never break the main request flow.
            // Write to CI's normal error log instead, then move on.
            log_message('error', 'logActivity() failed: ' . $e->getMessage());

            return false;
        }
    }
}