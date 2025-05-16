<?php
require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Controllers\UserController;
use App\Controllers\ArmadaController;
use App\Controllers\TransaksiController;
use App\Controllers\PenjualanController;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\OrderController;

// Load environment variables
Dotenv::createImmutable(__DIR__ . '/..')->load();

/**
 * Simple Router class for basic routing and dispatch
 */
class Router {
    private $routes = [];

    public function add(string $method, string $pattern, callable $handler) {
        $this->routes[] = [
            'method'  => strtoupper($method),
            'pattern' => $pattern,
            'handler' => $handler,
        ];
    }

    public function dispatch(string $method, string $uri) {
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) continue;

            // Convert URI pattern into regex
            $pattern = preg_replace('#\{([^}]+)\}#', '(?P<\1>[^/]+)', $route['pattern']);
            $regex = "#^" . $pattern . "$#";

            if (preg_match($regex, $uri, $matches)) {
                $params = array_filter(
                    $matches,
                    fn($k) => !is_int($k),
                    ARRAY_FILTER_USE_KEY
                );
                return call_user_func_array($route['handler'], $params);
            }
        }

        http_response_code(404);
        echo "Not Found";
    }
}

// View helper
function renderView(string $view, bool $withLayout = true, array $data = []) {
    $viewPath = __DIR__ . '/../app/views/' . $view . '.php';
    
    if (!file_exists($viewPath)) {
        http_response_code(404);
        echo "Halaman tidak ditemukan.";
        return;
    }

    // Extract data array ke variable
    extract($data);

    if ($withLayout) {
        include __DIR__ . '/../app/views/layouts/header.php';
        echo '<main>';
    }

    include $viewPath;

    if ($withLayout) {
        echo '</main>';
        include __DIR__ . '/../app/views/layouts/footer.php';
    }
}

// Initialize router
$router = new Router();

// ==== Auth ====
$auth = new AuthController();
$router->add('GET',  '/login',    [$auth, 'showLogin']);
$router->add('POST', '/login',    [$auth, 'login']);
$router->add('GET',  '/logout',   [$auth, 'logout']);

// ==== Dashboard ====
$dashboard = new DashboardController();
$router->add('GET', '/dashboard', [$dashboard, 'index']);

// ==== Armada ====
$armada = new ArmadaController();
$router->add('GET',  '/dashboard/armada',                   [$armada, 'index']);
$router->add('POST', '/dashboard/armada/store',             [$armada, 'store']);
$router->add('POST', '/dashboard/armada/update/{id}',       [$armada, 'update']);
$router->add('POST', '/dashboard/armada/destroy/{id}',      [$armada, 'destroy']);
$router->add('GET', '/dashboard/armada/datatable',          [$armada, 'datatable']);

// ==== Transaksi ====
$transaksi = new TransaksiController();
$router->add('GET', '/dashboard/transaksi', [$transaksi, 'index']);
// $router->add('POST', '/dashboard/transaksi/update-status/{id}', [$transaksi, 'updateStatus']);
$router->add('POST', '/dashboard/transaksi/update-payment-status/{id}', [$transaksi, 'updatePaymentStatus']);
$router->add('POST', '/dashboard/transaksi/update-transaction-status/{id}', [$transaksi, 'updateTransactionStatus']);
$router->add('GET', '/dashboard/transaksi/datatable', [$transaksi, 'datatable']);
$router->add('GET', '/dashboard/transaksi/{id}', [$transaksi, 'show']);

// ==== Penjualan ====
$penjualan = new PenjualanController();
$router->add('GET',  '/dashboard/penjualan',                   [$penjualan, 'index']);
$router->add('POST', '/dashboard/penjualan/import',           [$penjualan, 'import']);
$router->add('GET',  '/dashboard/penjualan/datatable',        [$penjualan, 'datatable']);
$router->add('GET', '/dashboard/penjualan/chart-data', [$penjualan, 'chartData']);
$router->add('GET', '/dashboard/penjualan/monthly-chart-data', [$penjualan, 'monthlyChartData']);
$router->add('GET',  '/dashboard/penjualan/{id}',             [$penjualan, 'show']);

// ==== User ====
$user = new UserController();
$router->add('GET',  '/dashboard/users',                  [$user, 'index']);
$router->add('POST', '/dashboard/users/store',            [$user, 'store']);
$router->add('POST', '/dashboard/users/update/{id}',      [$user, 'update']);
$router->add('POST', '/dashboard/users/destroy/{id}',     [$user, 'destroy']);

// ==== Order Routes ====
$order = new OrderController();
$router->add('GET', '/order/{id}', [$order, 'index']);
$router->add('POST', '/order/{id}/store', [$order, 'store']);
$router->add('GET', '/payment/{reference}', [$order, 'paymentByReference']);
$router->add('POST', '/payment/{reference}/process', [$order, 'processPayment']);
$router->add('GET', '/order/invoice/{reference}', [$order, 'invoice']);

// ==== Static Page Routes ====
$router->add('GET', '/',                    fn() => renderView('home'));
$router->add('GET', '/about',              fn() => renderView('about'));
$router->add('GET', '/contact',            fn() => renderView('contact'));
$router->add('GET', '/tronton', [$armada, 'categoryTronton']);
$router->add('GET', '/trailer', [$armada, 'categoryTrailer']);
$router->add('GET', '/detail/{id}', function($id) use ($armada) {$armada->showDetail($id);});


// === Handle URI and Dispatch ===
$rawUri   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
$uri      = ($basePath && strpos($rawUri, $basePath) === 0)
            ? substr($rawUri, strlen($basePath))
            : $rawUri;

$method = $_SERVER['REQUEST_METHOD'];
$router->dispatch($method, '/' . trim($uri, '/'));
    