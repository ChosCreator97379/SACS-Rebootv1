<?php

namespace Sacs\Services;

use Sacs\Core\Database;
use Exception;

class TramitesServicio
{
    private $db;
    private $logFile;

    public function __construct()
    {
        $this->db = Database::connection();
        $this->logFile = __DIR__ . '/../../logs/tramites.log';
    }

    /**
     * Listar trámites
     */
    public function listarTramites(array $filtros = [], int $pagina = 1, int $porPagina = 10): array
    {
        $offset = ($pagina - 1) * $porPagina;

        $where = [];
        $params = [];

        if (!empty($filtros['busqueda'])) {
            $where[] = "(f29_numero LIKE :busqueda OR f29_descripcion LIKE :busqueda2 OR f29_nombres LIKE :busqueda3)";
            $busquedaParam = '%' . $filtros['busqueda'] . '%';
            $params[':busqueda'] = $busquedaParam;
            $params[':busqueda2'] = $busquedaParam;
            $params[':busqueda3'] = $busquedaParam;
        }

        if (!empty($filtros['tipo'])) {
            $where[] = "f29_tipo = :tipo";
            $params[':tipo'] = $filtros['tipo'];
        }

        if (!empty($filtros['estado'])) {
            $where[] = "f29_estado = :estado";
            $params[':estado'] = $filtros['estado'];
        }

        if (!empty($filtros['socio'])) {
            $where[] = "f29_socio = :socio";
            $params[':socio'] = $filtros['socio'];
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $this->log('Debug listarTramites', [
            'where' => $where,
            'params' => $params,
            'whereClause' => $whereClause
        ]);

        // Si se filtra por socio, ordenar por estado en orden específico
        if (!empty($filtros['socio'])) {
            $orderBy = "ORDER BY
                CASE f29_estado
                    WHEN 'REGISTRADO' THEN 1
                    WHEN 'PENDIENTE' THEN 2
                    WHEN 'APROBADO' THEN 3
                    WHEN 'DERIVADO' THEN 4
                    WHEN 'FINALIZADO' THEN 5
                    WHEN 'ELIMINADO' THEN 6
                    ELSE 7
                END,
                f29_fecha DESC,
                f29_codigo DESC";
        } else {
            $orderBy = "ORDER BY f29_codigo DESC";
        }

        $sql = "SELECT
                    f29_codigo as codigo,
                    f29_numero as numero,
                    f29_tipo as tipo,
                    f29_descripcion as descripcion,
                    f29_fisico as fisico,
                    f29_destino as destino,
                    f29_origen as origen,
                    f29_fecha as fecha,
                    f29_ruta as ruta,
                    f29_estado as estado,
                    f29_obs as obs,
                    f29_referencia as referencia,
                    f29_obs2 as obs2,
                    f29_respuesta as respuesta,
                    f29_nombres as nombres,
                    f29_obs3 as obs3,
                    f29_fechaF as fechaF,
                    f29_fechaE as fechaE,
                    f29_socio as socio,
                    f29_importe as importe,
                    f29_interno as interno
                FROM f29_tramite
                $whereClause
                $orderBy
                LIMIT :offset, :porPagina";

        $stmt = $this->db->prepare($sql);
        $allParams = array_merge($params, [
            ':offset' => $offset,
            ':porPagina' => $porPagina
        ]);
        $stmt->execute($allParams);

        $tramites = $stmt->fetchAll();

        // Contar total
        $sqlCount = "SELECT COUNT(*) as total FROM f29_tramite $whereClause";
        $stmtCount = $this->db->prepare($sqlCount);
        $stmtCount->execute($params);
        $total = $stmtCount->fetch()['total'];

        return [
            'tramites' => $tramites,
            'total' => $total,
            'pagina' => $pagina,
            'porPagina' => $porPagina,
            'totalPaginas' => (int) ceil($total / $porPagina)
        ];
    }

    /**
     * Obtener trámite por código
     */
    public function obtenerTramite(int $codigo): ?array
    {
        $sql = "SELECT
                    f29_codigo as codigo,
                    f29_numero as numero,
                    f29_tipo as tipo,
                    f29_descripcion as descripcion,
                    f29_fisico as fisico,
                    f29_destino as destino,
                    f29_origen as origen,
                    f29_fecha as fecha,
                    f29_ruta as ruta,
                    f29_ruta2 as ruta2,
                    f29_ruta3 as ruta3,
                    f29_estado as estado,
                    f29_obs as obs,
                    f29_referencia as referencia,
                    f29_obs2 as obs2,
                    f29_respuesta as respuesta,
                    f29_nombres as nombres,
                    f29_obs3 as obs3,
                    f29_fechaF as fechaF,
                    f29_fechaE as fechaE,
                    f29_socio as socio,
                    f29_importe as importe,
                    f29_cuotas as cuotas,
                    f29_interno as interno,
                    f29_hora_registro as hora_registro,
                    f29_hora_edicion as hora_edicion,
                    f29_adjunto as adjunto,
                    f29_cuenta as cuenta,
                    f29_tramitante as tramitante,
                    f29_socio_dni as socio_dni,
                    f29_tramitante_cel as tramitante_cel,
                    f29_tramitante_email as tramitante_email,
                    f29_tramitante_email2 as tramitante_email2,
                    f29_interes_prestamo as interes_prestamo,
                    f29_fecha_desembolso as fecha_desembolso,
                    f29_producto_prestamo as producto_prestamo,
                    f29_prestamo_codigo as prestamo_codigo
                FROM f29_tramite
                WHERE f29_codigo = :codigo";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['codigo' => $codigo]);

        $resultado = $stmt->fetch();
        return $resultado ?: null;
    }

    /**
     * Obtener tipos de documentos
     */
    public function obtenerTiposDocumentos(): array
    {
        $sql = "SELECT
                    F35_DOCUMENTO_ID,
                    F35_DOCUMENTO_DESC,
                    F35_DOCUMENTO_ABRE,
                    F35_DOCUMENTO_INTERNO,
                    F35_DOCUMENTO_ESTADO
                FROM f35_tipo_documento
                WHERE F35_DOCUMENTO_INTERNO IN ('1', '2')
                ORDER BY F35_DOCUMENTO_ID";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Obtener usuarios activos para el campo destino
     */
    public function obtenerUsuarios(): array
    {
        $sql = "SELECT
                    f06_id_usuario as id,
                    f06_nombre as nombre,
                    f06_perfil as perfil,
                    f06_estado as estado
                FROM f06_usuario
                WHERE f06_estado = 'A'
                ORDER BY f06_nombre";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Obtener correlativo por tipo de documento
     */
    public function obtenerCorrelativo(int $tipoDoc): ?array
    {
        $sql = "SELECT
                    CORRELATIVO_ID as id,
                    CORRELATIVO_TIPO_DOC as tipo_doc,
                    CORRELATIVO_SERIE as serie,
                    CORRELATIVO_NUMERO_INICIO as numero_inicio,
                    CORRELATIVO_NUMERO_FIN as numero_fin,
                    CORRELATIVO_NUMERO_ACTUAL as numero_actual,
                    CORRELATIVO_NUMERO_DEL as numero_del
                FROM f36_correlativos_documentos
                WHERE CORRELATIVO_TIPO_DOC = :tipo_doc";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['tipo_doc' => $tipoDoc]);

        $resultado = $stmt->fetch();
        return $resultado ?: null;
    }

    /**
     * Obtener todos los correlativos para JavaScript
     */
    public function obtenerTodosCorrelativos(): array
    {
        $sql = "SELECT
                    CORRELATIVO_TIPO_DOC as tipo_doc,
                    CORRELATIVO_SERIE as serie,
                    CORRELATIVO_NUMERO_INICIO as numero_inicio,
                    CORRELATIVO_NUMERO_FIN as numero_fin,
                    CORRELATIVO_NUMERO_ACTUAL as numero_actual
                FROM f36_correlativos_documentos";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Obtener trámites por socio
     */
    public function obtenerTramitesPorSocio(string $socioCodigo): array
    {
        $sql = "SELECT
                    f29_codigo as codigo,
                    f29_numero as numero,
                    f29_tipo as tipo,
                    f29_descripcion as descripcion,
                    f29_fecha as fecha,
                    f29_estado as estado,
                    f29_origen as origen,
                    f29_destino as destino
                FROM f29_tramite
                WHERE f29_socio = :socio
                ORDER BY f29_codigo DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['socio' => $socioCodigo]);

        return $stmt->fetchAll();
    }

    /**
     * Generar número de trámite correlativo por tipo
     */
    public function generarNumeroTramite(int $tipoDoc): array
    {
        try {
            $correlativo = $this->obtenerCorrelativo($tipoDoc);

            if (!$correlativo) {
                return [
                    'success' => false,
                    'message' => 'No existe correlativo para el tipo de documento especificado'
                ];
            }

            $anioActual = date('Y');
            $numeroActual = (int) $correlativo['numero_actual'];
            $numeroInicio = (int) $correlativo['numero_inicio'];
            $numeroFin = (int) $correlativo['numero_fin'];

            // Incrementar número actual
            $nuevoNumero = $numeroActual + 1;

            // Si excede el rango, reiniciar al inicio
            if ($nuevoNumero > $numeroFin) {
                $nuevoNumero = $numeroInicio;
            }

            // Tipos 22, 23, 24 necesitan formato con cero a la izquierda (4 dígitos)
            $tiposConCero = [22, 23, 24];
            if (in_array($tipoDoc, $tiposConCero)) {
                $numeroFormateado = str_pad($nuevoNumero, 4, '0', STR_PAD_LEFT);
            } else {
                $numeroFormateado = $nuevoNumero;
            }

            // Combinar número y año (formato: NUMERO-AÑO)
            $numeroTramite = $numeroFormateado . '-' . $anioActual;

            return [
                'success' => true,
                'numero' => $numeroTramite
            ];

        } catch (Exception $e) {
            $this->log('Error al generar número', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Error al generar número: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Actualizar correlativo después de registrar trámite
     */
    public function actualizarCorrelativo(int $tipoDoc, string $numeroTramite): bool
    {
        try {
            // Extraer número del trámite (formato: NUMERO-AÑO)
            $partes = explode('-', $numeroTramite);
            $numero = isset($partes[0]) ? (int) $partes[0] : 0;

            $sql = "UPDATE f36_correlativos_documentos
                    SET CORRELATIVO_NUMERO_ACTUAL = :numero
                    WHERE CORRELATIVO_TIPO_DOC = :tipo_doc";

            $stmt = $this->db->prepare($sql);
            $resultado = $stmt->execute([
                'numero' => $numero,
                'tipo_doc' => $tipoDoc
            ]);

            $this->log('Correlativo actualizado', ['tipo_doc' => $tipoDoc, 'numero' => $numero, 'filas_afectadas' => $resultado]);
            return $resultado;

        } catch (Exception $e) {
            $this->log('Error al actualizar correlativo', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Verificar si número de trámite ya existe
     */
    public function verificarTramiteExiste(string $numero, string $tipo): bool
    {
        $sql = "SELECT COUNT(*) as total FROM f29_tramite
                WHERE f29_numero = :numero AND f29_tipo = :tipo";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'numero' => $numero,
            'tipo' => $tipo
        ]);

        $resultado = $stmt->fetch();
        return (int) $resultado['total'] > 0;
    }

    /**
     * Registrar nuevo trámite
     */
    public function registrarTramite(array $datos, string $usuario = 'SISTEMA'): array
    {
        $this->log('Iniciando registro de trámite', $datos);

        try {
            $this->db->beginTransaction();

            // Verificar que el número no exista
            if ($this->verificarTramiteExiste($datos['numero'], $datos['tipo'])) {
                throw new Exception('El número de trámite ya existe para este tipo de documento');
            }

            // Obtener siguiente código
            $sqlMax = "SELECT MAX(f29_codigo) as max_codigo FROM f29_tramite";
            $stmtMax = $this->db->query($sqlMax);
            $maxCodigo = $stmtMax->fetch()['max_codigo'] ?? 0;
            $nuevoCodigo = $maxCodigo + 1;

            $sql = "INSERT INTO f29_tramite (
                        f29_codigo,
                        f29_numero,
                        f29_tipo,
                        f29_descripcion,
                        f29_fisico,
                        f29_destino,
                        f29_origen,
                        f29_fecha,
                        f29_ruta,
                        f29_ruta2,
                        f29_ruta3,
                        f29_estado,
                        f29_obs,
                        f29_referencia,
                        f29_obs2,
                        f29_respuesta,
                        f29_nombres,
                        f29_obs3,
                        f29_fechaF,
                        f29_fechaE,
                        f29_socio,
                        f29_importe,
                        f29_cuotas,
                        f29_interno,
                        f29_hora_registro,
                        f29_adjunto,
                        f29_tramitante,
                        f29_socio_dni,
                        f29_ref_docu,
                        f29_tramitante_cel,
                        f29_tramitante_email,
                        f29_tramitante_email2,
                        f29_cuenta,
                        f29_interes_prestamo,
                        f29_fecha_desembolso,
                        f29_producto_prestamo
                    ) VALUES (
                        :codigo,
                        :numero,
                        :tipo,
                        :descripcion,
                        :fisico,
                        :destino,
                        :origen,
                        :fecha,
                        :ruta,
                        :ruta2,
                        :ruta3,
                        'REGISTRADO',
                        :obs,
                        :referencia,
                        :obs2,
                        :respuesta,
                        :nombres,
                        :obs3,
                        :fechaF,
                        :fechaE,
                        :socio,
                        :importe,
                        :cuotas,
                        :interno,
                        :hora_registro,
                        :adjunto,
                        :tramitante,
                        :socio_dni,
                        :ref_docu,
                        :tramitante_cel,
                        :tramitante_email,
                        :tramitante_email2,
                        :cuenta,
                        :interes_prestamo,
                        :fecha_desembolso,
                        :producto_prestamo
                    )";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'codigo' => $nuevoCodigo,
                'numero' => $datos['numero'],
                'tipo' => $datos['tipo'],
                'descripcion' => $datos['descripcion'] ?? null,
                'fisico' => $datos['fisico'] ?? 'NO',
                'destino' => $datos['destino'] ?? null,
                'origen' => $usuario,
                'fecha' => $datos['fecha'] ?? date('Y-m-d'),
                'ruta' => $datos['ruta'] ?? null,
                'ruta2' => $datos['ruta2'] ?? null,
                'ruta3' => $datos['ruta3'] ?? null,
                'obs' => $datos['obs'] ?? null,
                'referencia' => $datos['referencia'] ?? null,
                'obs2' => $datos['obs2'] ?? null,
                'respuesta' => $datos['respuesta'] ?? null,
                'nombres' => $datos['nombres'] ?? null,
                'obs3' => $datos['obs3'] ?? null,
                'fechaF' => $datos['fechaF'] ?? null,
                'fechaE' => $datos['fechaE'] ?? null,
                'socio' => $datos['socio'] ?? null,
                'importe' => $datos['importe'] ?? null,
                'cuotas' => $datos['cuotas'] ?? null,
                'interno' => $datos['interno'] ?? '1',
                'hora_registro' => $datos['hora'] ?? date('H:i:s'),
                'adjunto' => $datos['adjunto'] ?? null,
                'tramitante' => $datos['tramitante'] ?? null,
                'socio_dni' => $datos['socio_dni'] ?? null,
                'ref_docu' => $datos['ref_documento'] ?? null,
                'tramitante_cel' => $datos['tramitante_cel'] ?? null,
                'tramitante_email' => $datos['tramitante_email'] ?? null,
                'tramitante_email2' => $datos['tramitante_email2'] ?? null,
                'cuenta' => $datos['cuenta'] ?? null,
                'interes_prestamo' => $datos['interes_prestamo'] ?? null,
                'fecha_desembolso' => $datos['fecha_desembolso'] ?? null,
                'producto_prestamo' => $datos['producto_prestamo'] ?? null
            ]);

            // Actualizar correlativo
            $this->actualizarCorrelativo((int) $datos['tipo'], $datos['numero']);

            // Registrar histórico del trámite (dentro de la transacción)
            $this->registrarHistorico(
                $nuevoCodigo,
                $usuario,
                $datos['destino'] ?? null,
                'REGISTRADO',
                $datos['obs'] ?? null
            );

            $this->db->commit();
            $this->log('Trámite registrado correctamente', ['codigo' => $nuevoCodigo, 'numero' => $datos['numero']]);

            return [
                'success' => true,
                'message' => 'Trámite registrado correctamente',
                'codigo' => $nuevoCodigo,
                'numero' => $datos['numero']
            ];

        } catch (Exception $e) {
            $this->db->rollBack();
            $this->log('Error al registrar trámite', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Error al registrar trámite: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Crear préstamo desde trámite
     */
    public function crearPrestamoDesdeTramite(int $codigoTramite, string $usuario = 'SISTEMA'): array
    {
        try {
            // Obtener datos del trámite
            $tramite = $this->obtenerTramite($codigoTramite);
            if (!$tramite) {
                return ['success' => false, 'message' => 'Trámite no encontrado'];
            }

            // Verificar que el trámite tenga datos de préstamo
            if (empty($tramite['socio']) || empty($tramite['importe']) || empty($tramite['cuotas'])) {
                return ['success' => false, 'message' => 'El trámite no tiene datos de préstamo suficientes'];
            }

            // Preparar datos del préstamo
            $datosPrestamo = [
                'cliente' => $tramite['socio'],
                'importe' => $tramite['importe'],
                'cuotas' => $tramite['cuotas'],
                'interes' => $tramite['interes_prestamo'] ?? 0,
                'fecha_desembolso' => $tramite['fecha_desembolso'] ?? date('Y-m-d'),
                'producto' => $tramite['producto_prestamo'] ?? '',
                'garante' => '',
                'garante2' => '',
                'garante3' => '',
                'garante4' => '',
                'tramite' => $tramite['numero'] ?? ''
            ];

            // Crear préstamo usando el servicio de préstamos
            $prestamosServicio = new PrestamosServicio();
            $resultadoPrestamo = $prestamosServicio->registrarPrestamo($datosPrestamo, $usuario);

            if (!$resultadoPrestamo['success']) {
                // Actualizar trámite con error (solo actualizar obs)
                $nuevaObs = ($tramite['obs'] ?? '') . "\n\nError al crear préstamo: " . $resultadoPrestamo['message'];
                $sqlUpdate = "UPDATE f29_tramite SET f29_obs = :obs WHERE f29_codigo = :codigo";
                $stmtUpdate = $this->db->prepare($sqlUpdate);
                $stmtUpdate->execute(['obs' => $nuevaObs, 'codigo' => $codigoTramite]);
                return ['success' => false, 'message' => 'Error al crear préstamo: ' . $resultadoPrestamo['message']];
            }

            // Actualizar trámite con el código del préstamo creado (solo actualizar prestamo_codigo y obs)
            $codigoPrestamo = $resultadoPrestamo['codigo'];
            $nuevaObs = ($tramite['obs'] ?? '') . "\n\nPréstamo #$codigoPrestamo creado exitosamente";
            $sqlUpdate = "UPDATE f29_tramite SET f29_prestamo_codigo = :prestamo_codigo, f29_obs = :obs WHERE f29_codigo = :codigo";
            $stmtUpdate = $this->db->prepare($sqlUpdate);
            $stmtUpdate->execute(['prestamo_codigo' => $codigoPrestamo, 'obs' => $nuevaObs, 'codigo' => $codigoTramite]);

            // Registrar histórico
            $this->registrarHistorico(
                $codigoTramite,
                $usuario,
                $tramite['destino'] ?? null,
                'REGISTRADO',
                "Préstamo #$codigoPrestamo creado automáticamente desde trámite"
            );

            return [
                'success' => true,
                'message' => 'Préstamo creado exitosamente',
                'codigo_prestamo' => $codigoPrestamo
            ];

        } catch (Exception $e) {
            $this->log('Error al crear préstamo desde trámite', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Error al crear préstamo: ' . $e->getMessage()];
        }
    }

    /**
     * Actualizar trámite existente
     */
    public function actualizarTramite(int $codigo, array $datos, string $usuario = 'SISTEMA'): array
    {
        $this->log('Iniciando actualización de trámite', ['codigo' => $codigo, 'datos' => $datos]);

        try {
            $this->db->beginTransaction();

            $sql = "UPDATE f29_tramite SET
                        f29_descripcion = :descripcion,
                        f29_fisico = :fisico,
                        f29_destino = :destino,
                        f29_ruta = :ruta,
                        f29_ruta2 = :ruta2,
                        f29_ruta3 = :ruta3,
                        f29_obs = :obs,
                        f29_referencia = :referencia,
                        f29_obs2 = :obs2,
                        f29_respuesta = :respuesta,
                        f29_nombres = :nombres,
                        f29_obs3 = :obs3,
                        f29_fechaF = :fechaF,
                        f29_fechaE = :fechaE,
                        f29_importe = :importe,
                        f29_cuotas = :cuotas,
                        f29_adjunto = :adjunto,
                        f29_tramitante = :tramitante,
                        f29_socio_dni = :socio_dni,
                        f29_ref_docu = :ref_docu,
                        f29_tramitante_cel = :tramitante_cel,
                        f29_tramitante_email = :tramitante_email,
                        f29_tramitante_email2 = :tramitante_email2,
                        f29_cuenta = :cuenta,
                        f29_hora_edicion = :hora_edicion,
                        f29_estado = :estado,
                        f29_interes_prestamo = :interes_prestamo,
                        f29_fecha_desembolso = :fecha_desembolso,
                        f29_producto_prestamo = :producto_prestamo,
                        f29_prestamo_codigo = :prestamo_codigo
                    WHERE f29_codigo = :codigo";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'codigo' => $codigo,
                'descripcion' => $datos['descripcion'] ?? null,
                'fisico' => $datos['fisico'] ?? 'NO',
                'destino' => $datos['destino'] ?? null,
                'ruta' => $datos['ruta'] ?? null,
                'ruta2' => $datos['ruta2'] ?? null,
                'ruta3' => $datos['ruta3'] ?? null,
                'obs' => $datos['obs'] ?? null,
                'referencia' => $datos['referencia'] ?? null,
                'obs2' => $datos['obs2'] ?? null,
                'respuesta' => $datos['respuesta'] ?? null,
                'nombres' => $datos['nombres'] ?? null,
                'obs3' => $datos['obs3'] ?? null,
                'fechaF' => $datos['fechaF'] ?? null,
                'fechaE' => $datos['fechaE'] ?? null,
                'importe' => $datos['importe'] ?? null,
                'cuotas' => $datos['cuotas'] ?? null,
                'adjunto' => $datos['adjunto'] ?? null,
                'tramitante' => $datos['tramitante'] ?? null,
                'socio_dni' => $datos['tramitante_dni'] ?? null,
                'ref_docu' => $datos['ref_docu'] ?? null,
                'tramitante_cel' => $datos['tramitante_cel'] ?? null,
                'tramitante_email' => $datos['tramitante_email'] ?? null,
                'tramitante_email2' => $datos['tramitante_email2'] ?? null,
                'cuenta' => $datos['cuenta'] ?? null,
                'hora_edicion' => date('H:i:s'),
                'estado' => $datos['estado'] ?? 'REGISTRADO',
                'interes_prestamo' => $datos['interes_prestamo'] ?? null,
                'fecha_desembolso' => $datos['fecha_desembolso'] ?? null,
                'producto_prestamo' => $datos['producto_prestamo'] ?? null,
                'prestamo_codigo' => $datos['prestamo_codigo'] ?? null
            ]);

            $this->db->commit();
            $this->log('Trámite actualizado correctamente', ['codigo' => $codigo]);

            // Registrar histórico del trámite
            $this->registrarHistorico(
                $codigo,
                $usuario,
                $datos['destino'] ?? null,
                'ACTUALIZADO',
                $datos['obs'] ?? null
            );

            return [
                'success' => true,
                'message' => 'Trámite actualizado correctamente'
            ];

        } catch (Exception $e) {
            $this->db->rollBack();
            $this->log('Error al actualizar trámite', ['codigo' => $codigo, 'error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Error al actualizar trámite: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Eliminar trámite (lógico - cambiar estado a ELIMINADO)
     */
    public function eliminarTramite(int $codigo, string $usuario = 'SISTEMA'): array
    {
        $this->log('Iniciando eliminación de trámite', ['codigo' => $codigo]);

        try {
            $this->db->beginTransaction();

            // Obtener el destino del trámite antes de eliminar
            $sqlSelect = "SELECT f29_destino FROM f29_tramite WHERE f29_codigo = :codigo";
            $stmtSelect = $this->db->prepare($sqlSelect);
            $stmtSelect->execute(['codigo' => $codigo]);
            $tramite = $stmtSelect->fetch(\PDO::FETCH_ASSOC);
            $destino = $tramite['f29_destino'] ?? '';

            $sql = "UPDATE f29_tramite
                    SET f29_estado = 'ELIMINADO',
                        f29_fechaE = :fechaE
                    WHERE f29_codigo = :codigo";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'codigo' => $codigo,
                'fechaE' => date('Y-m-d')
            ]);

            $this->db->commit();
            $this->log('Trámite eliminado (lógico)', ['codigo' => $codigo]);

            // Registrar histórico del trámite
            $this->registrarHistorico(
                $codigo,
                $usuario,
                $destino,
                'ELIMINADO',
                'Trámite eliminado lógicamente'
            );

            return [
                'success' => true,
                'message' => 'Trámite eliminado correctamente'
            ];

        } catch (Exception $e) {
            $this->db->rollBack();
            $this->log('Error al eliminar trámite', ['codigo' => $codigo, 'error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Error al eliminar trámite: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Directorio para almacenar archivos adjuntos
     */
    private function obtenerDirectorioArchivos(): string
    {
        $directorio = __DIR__ . '/../../public/uploads/tramites';
        
        // Crear directorio si no existe
        if (!is_dir($directorio)) {
            mkdir($directorio, 0755, true);
        }
        
        return $directorio;
    }

    /**
     * Cargar archivo adjunto
     */
    public function cargarArchivo(array $archivo, string $tipoRuta = 'ruta1'): array
    {
        $this->log('Iniciando carga de archivo', ['tipo_ruta' => $tipoRuta, 'nombre' => $archivo['name'] ?? '']);

        try {
            // Validaciones
            if (!isset($archivo['tmp_name']) || !is_uploaded_file($archivo['tmp_name'])) {
                throw new Exception('Archivo no válido');
            }

            // Validar tamaño (máximo 10MB)
            $tamanoMaximo = 10 * 1024 * 1024; // 10MB
            if ($archivo['size'] > $tamanoMaximo) {
                throw new Exception('El archivo excede el tamaño máximo permitido (10MB)');
            }

            // Validar tipo de archivo
            $tiposPermitidos = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            if (!in_array($archivo['type'], $tiposPermitidos)) {
                throw new Exception('Tipo de archivo no permitido');
            }

            // Generar nombre único
            $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
            $nombreUnico = uniqid() . '_' . time() . '.' . $extension;
            
            // Mover archivo al directorio de uploads
            $directorio = $this->obtenerDirectorioArchivos();
            $rutaDestino = $directorio . '/' . $nombreUnico;

            if (!move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
                throw new Exception('Error al mover el archivo al servidor');
            }

            // Retornar ruta relativa para guardar en base de datos
            $rutaRelativa = 'uploads/tramites/' . $nombreUnico;

            $this->log('Archivo cargado correctamente', ['ruta' => $rutaRelativa]);

            return [
                'success' => true,
                'ruta' => $rutaRelativa,
                'nombre' => $nombreUnico
            ];

        } catch (Exception $e) {
            $this->log('Error al cargar archivo', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Error al cargar archivo: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Eliminar archivo adjunto del servidor
     */
    public function eliminarArchivo(string $ruta): bool
    {
        $this->log('Iniciando eliminación de archivo', ['ruta' => $ruta]);

        try {
            $rutaCompleta = __DIR__ . '/../../public/' . $ruta;

            if (file_exists($rutaCompleta)) {
                if (unlink($rutaCompleta)) {
                    $this->log('Archivo eliminado correctamente', ['ruta' => $ruta]);
                    return true;
                } else {
                    $this->log('Error al eliminar archivo', ['ruta' => $ruta]);
                    return false;
                }
            }

            // El archivo no existe, pero consideramos exitoso
            return true;

        } catch (Exception $e) {
            $this->log('Error al eliminar archivo', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Registrar histórico de trámite
     */
    public function registrarHistorico(int $codigoTramite, string $origen, ?string $destino, string $estado, ?string $obs = null): array
    {
        $this->log('Iniciando registro de histórico', [
            'codigo_tramite' => $codigoTramite,
            'origen' => $origen,
            'destino' => $destino,
            'estado' => $estado
        ]);

        try {
            $fecha = date('Y-m-d');
            $hora = date('H:i');

            $sql = "INSERT INTO f30_historico (f30_codigo, f30_origen, f30_destino, f30_estado, f30_fecha, f30_obs, f30_hora)
                    VALUES (:codigo, :origen, :destino, :estado, :fecha, :obs, :hora)";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':codigo' => $codigoTramite,
                ':origen' => $origen,
                ':destino' => $destino,
                ':estado' => $estado,
                ':fecha' => $fecha,
                ':obs' => $obs,
                ':hora' => $hora
            ]);

            $this->log('Histórico registrado correctamente', ['codigo_tramite' => $codigoTramite]);

            return [
                'success' => true,
                'message' => 'Histórico registrado correctamente'
            ];

        } catch (Exception $e) {
            $this->log('Error al registrar histórico', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Error al registrar histórico: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtener histórico de un trámite
     */
    public function obtenerHistorico(int $codigoTramite): array
    {
        try {
            $sql = "SELECT * FROM f30_historico WHERE f30_codigo = :codigo ORDER BY f30_fecha DESC, f30_hora DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':codigo' => $codigoTramite]);

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            $this->log('Error al obtener histórico', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Log de operaciones
     */
    private function log(string $mensaje, array $contexto = []): void
    {
        $fecha = date('Y-m-d H:i:s');
        $contextoStr = is_array($contexto) ? json_encode($contexto) : $contexto;
        $logLine = "[$fecha] $mensaje | Contexto: $contextoStr\n";
        file_put_contents($this->logFile, $logLine, FILE_APPEND);
    }
}
