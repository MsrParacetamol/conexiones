<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Configuración de la base de datos usando variables de entorno de Railway
$host = 'mysql.railway.internal';
$port = '3306';
$dbname = 'railway';
$username = 'root';
$password = 'FbrdpRqfuivFdzHfQCqatLVSPVSOrjj0';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    error_log("Error de conexión: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos']);
    exit;
}

// Manejar preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$action = $_GET['action'] ?? '';
error_log("API Action: " . $action);

switch($action) {
    case 'get_products':
        getProducts($pdo);
        break;
    case 'get_categories':
        getCategories($pdo);
        break;
    case 'add_product':
        addProduct($pdo);
        break;
    case 'update_product':
        updateProduct($pdo);
        break;
    case 'delete_product':
        deleteProduct($pdo);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Acción no válida: ' . $action]);
        break;
}

function getProducts($pdo) {
    try {
        $sql = "SELECT p.* 
                FROM productos p
                LEFT JOIN categorias c ON p.categoria_id = c.id_categoria
                WHERE c.id_categoria IS NULL OR LOWER(TRIM(c.nombre)) != 'servicios técnicos'
                ORDER BY p.id_producto DESC";
        $stmt = $pdo->query($sql);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    } catch(PDOException $e) {
        error_log("Error en getProducts: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error al obtener productos']);
    }
}

function getCategories($pdo) {
    try {
        $sql = "SELECT * FROM categorias 
                WHERE LOWER(TRIM(nombre)) != 'servicios técnicos'
                ORDER BY nombre";
        $stmt = $pdo->query($sql);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    } catch(PDOException $e) {
        error_log("Error en getCategories: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error al obtener categorías']);
    }
}

function addProduct($pdo) {
    try {
        $data = json_decode(file_get_contents("php://input"), true);
        if (!$data) throw new Exception('Datos inválidos');

        $sql = "INSERT INTO productos (nombre, descripcion, categoria_id, marca, modelo, tipo, especificaciones, imagen_url) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['nombre'] ?? '',
            $data['descripcion'] ?? '',
            $data['categoria_id'] ?? 1,
            $data['marca'] ?? '',
            $data['modelo'] ?? '',
            $data['tipo'] ?? '',
            $data['especificaciones'] ?? '',
            $data['imagen_url'] ?? ''
        ]);
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
    } catch(Exception $e) {
        error_log("Error en addProduct: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error al agregar producto']);
    }
}

function updateProduct($pdo) {
    try {
        $data = json_decode(file_get_contents("php://input"), true);
        if (!$data || !isset($data['id_producto'])) throw new Exception('Datos inválidos o ID faltante');

        $sql = "UPDATE productos SET nombre=?, descripcion=?, categoria_id=?, marca=?, modelo=?, tipo=?, especificaciones=?, imagen_url=? WHERE id_producto=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['nombre'] ?? '',
            $data['descripcion'] ?? '',
            $data['categoria_id'] ?? 1,
            $data['marca'] ?? '',
            $data['modelo'] ?? '',
            $data['tipo'] ?? '',
            $data['especificaciones'] ?? '',
            $data['imagen_url'] ?? '',
            $data['id_producto']
        ]);
        echo json_encode(['success' => true]);
    } catch(Exception $e) {
        error_log("Error en updateProduct: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error al actualizar producto']);
    }
}

function deleteProduct($pdo) {
    try {
        $data = json_decode(file_get_contents("php://input"), true);
        $id = $data['id'] ?? null;
        if (!$id) throw new Exception('ID no proporcionado');

        $stmt = $pdo->prepare("DELETE FROM productos WHERE id_producto=?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
    } catch(Exception $e) {
        error_log("Error en deleteProduct: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error al eliminar producto']);
    }
}
?>
