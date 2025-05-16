<?php
namespace App\Controllers;

use App\Models\TransaksiModel;
use App\Models\ArmadaModel;

class DashboardController {
    private ArmadaModel $armadaModel;

    public function __construct()
    {
        $this->armadaModel = new ArmadaModel();
    }

    public function index(): void {
        // session_start();
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // Ambil data tronton dan trailer
        $trontonList = $this->armadaModel->getTrontonArmada();
        $trailerList = $this->armadaModel->getTrailerArmada();
        $transaksi = [];

        include __DIR__ . '/../views/backsite/layouts/header.php';
        include __DIR__ . '/../views/backsite/layouts/sidebar.php';
        include __DIR__ . '/../views/backsite/layouts/topbar.php';

        echo '<main class="p-6">';
        $transaksi = (new TransaksiModel())->getAll();
        include __DIR__ . '/../views/backsite/dashboard/index.php';
        echo '</main>';

        include __DIR__ . '/../views/backsite/layouts/footer.php';
    }
}