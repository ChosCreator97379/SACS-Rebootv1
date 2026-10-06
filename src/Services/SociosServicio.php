<?php

namespace Sacs\Services;

use Sacs\Core\Database;
use Exception;

class SociosServicio
{
    private $db;
    private $logFile;

    public function __construct()
    {
        $this->db = Database::connection();
        $this->logFile = __DIR__ . '/../../logs/socios.log';
    }

    /**
     * Registrar un nuevo socio
     */
    public function registrarSocio(array $datosPersona, array $datosCliente, string $usuario = 'SISTEMA'): array
    {
        $this->log('Iniciando registro de socio', $datosPersona);

        try {
            $this->db->beginTransaction();

            // Generar ID de persona de forma segura
            $idPersona = $this->generarIdPersona();
            $this->log('ID de persona generado: ' . $idPersona);

            // Insertar en f01_persona
            $sqlPersona = "INSERT INTO f01_persona (
                F01_ID_PERSONA, F01_TPO_DOC, F01_NUM_DOC, F01_APE_PAT, F01_APE_MAT,
                F01_NOMB_PRI, F01_NOMB_SEG, F01_SEXO, F01_FE_NAC, F01_STDO_CIVIL,
                F01_STDO_PERSN, F01_CTA_CORREO, F01_ID_NACIONALIDAD, F01_ID_INSTRUCCION,
                F01_ID_PROFESION, F01_DPTO_NAC, F01_PROV_NAC, F01_DISTRI_NAC,
                F01_TELF_FIJO, F01_TELF_CEL, F01_DESC_COMENT, F01_ZONA, F01_VIA,
                F01_INT, F01_EXT, F01_PISO, F01_DEPARTAMENTO, F01_PROVINCIA,
                F01_DISTRITO, F01_OBSERVACION, F01_EDAD, F01_CENTRO, F01_CONDICION,
                F01_CUENTA, F01_INGRESO, F01_APORTACION, F01_SALDO, F01_DOCUMENTO,
                F01_SITUACION, F01_FECHA_RETIRO, F01_FECHA_FALLE, F01_APORTA_BASE,
                F01_APORTA_SOBRE, F01_SOBREVI, F01_OBS, F01_FEC_REINGRESO,
                F01_APORTE_COPE, F01_OBS2, F01_TITULO, F01_FIRMO, F01_CARTA,
                F01_ID_USUA_CREA, F01_FE_USUA_CREA
            ) VALUES (
                :id_persona, :tpo_doc, :num_doc, :ape_pat, :ape_mat,
                :nomb_pri, :nomb_seg, :sexo, :fe_nac, :stdo_civil,
                :stdo_persn, :cta_correo, :id_nacionalidad, :id_instruccion,
                :id_profesion, :dpto_nac, :prov_nac, :distri_nac,
                :telf_fijo, :telf_cel, :desc_coment, :zona, :via,
                :int, :ext, :piso, :departamento, :provincia,
                :distrito, :observacion, :edad, :centro, :condicion,
                :cuenta, :ingreso, :aportacion, :saldo, :documento,
                :situacion, :fecha_retiro, :fecha_falle, :aporta_base,
                :aporta_sobre, :sobrevi, :obs, :fec_reingreso,
                :aporte_cope, :obs2, :titulo, :firma, :carta,
                :id_usua_crea, NOW()
            )";

            $stmtPersona = $this->db->prepare($sqlPersona);
            $stmtPersona->execute([
                ':id_persona' => $idPersona,
                ':tpo_doc' => $datosPersona['tpo_doc'] ?? null,
                ':num_doc' => $datosPersona['num_doc'] ?? null,
                ':ape_pat' => $datosPersona['ape_pat'] ?? null,
                ':ape_mat' => $datosPersona['ape_mat'] ?? null,
                ':nomb_pri' => $datosPersona['nomb_pri'] ?? null,
                ':nomb_seg' => $datosPersona['nomb_seg'] ?? null,
                ':sexo' => $datosPersona['sexo'] ?? null,
                ':fe_nac' => $datosPersona['fe_nac'] ?? null,
                ':stdo_civil' => $datosPersona['stdo_civil'] ?? null,
                ':stdo_persn' => $datosPersona['stdo_persn'] ?? 1,
                ':cta_correo' => $datosPersona['cta_correo'] ?? null,
                ':id_nacionalidad' => $datosPersona['id_nacionalidad'] ?? null,
                ':id_instruccion' => $datosPersona['id_instruccion'] ?? null,
                ':id_profesion' => $datosPersona['id_profesion'] ?? null,
                ':dpto_nac' => $datosPersona['dpto_nac'] ?? null,
                ':prov_nac' => $datosPersona['prov_nac'] ?? null,
                ':distri_nac' => $datosPersona['distri_nac'] ?? null,
                ':telf_fijo' => $datosPersona['telf_fijo'] ?? null,
                ':telf_cel' => $datosPersona['telf_cel'] ?? null,
                ':desc_coment' => $datosPersona['desc_coment'] ?? null,
                ':zona' => $datosPersona['zona'] ?? null,
                ':via' => $datosPersona['via'] ?? null,
                ':int' => $datosPersona['int'] ?? null,
                ':ext' => $datosPersona['ext'] ?? null,
                ':piso' => $datosPersona['piso'] ?? null,
                ':departamento' => $datosPersona['departamento'] ?? null,
                ':provincia' => $datosPersona['provincia'] ?? null,
                ':distrito' => $datosPersona['distrito'] ?? null,
                ':observacion' => $datosPersona['observacion'] ?? null,
                ':edad' => $datosPersona['edad'] ?? null,
                ':centro' => $datosPersona['centro'] ?? null,
                ':condicion' => $datosPersona['condicion'] ?? null,
                ':cuenta' => $datosPersona['cuenta'] ?? null,
                ':ingreso' => $datosPersona['ingreso'] ?? null,
                ':aportacion' => $datosPersona['aportacion'] ?? null,
                ':saldo' => $datosPersona['saldo'] ?? null,
                ':documento' => $datosPersona['documento'] ?? null,
                ':situacion' => $datosPersona['situacion'] ?? null,
                ':fecha_retiro' => $datosPersona['fecha_retiro'] ?? null,
                ':fecha_falle' => $datosPersona['fecha_falle'] ?? null,
                ':aporta_base' => !empty($datosPersona['aporta_base']) ? (int) $datosPersona['aporta_base'] : null,
                ':aporta_sobre' => !empty($datosPersona['aporta_sobre']) ? (float) $datosPersona['aporta_sobre'] : null,
                ':sobrevi' => $datosPersona['sobrevi'] ?? null,
                ':obs' => $datosPersona['obs'] ?? null,
                ':fec_reingreso' => $datosPersona['fec_reingreso'] ?? null,
                ':aporte_cope' => !empty($datosPersona['aporte_cope']) ? (int) $datosPersona['aporte_cope'] : 0,
                ':obs2' => $datosPersona['obs2'] ?? null,
                ':titulo' => $datosPersona['titulo'] ?? null,
                ':firma' => $datosPersona['firma'] ?? null,
                ':carta' => $datosPersona['carta'] ?? null,
                ':id_usua_crea' => $usuario
            ]);

            $this->log('Persona insertada correctamente: ' . $idPersona);

            // Generar código de cliente
            $idCliente = $this->generarIdCliente();
            $this->log('ID de cliente generado: ' . $idCliente);

            // Insertar en f02_cliente
            $sqlCliente = "INSERT INTO f02_cliente (
                F02_ID_CLIENTE, F02_ID_PERSONA, F02_STDO, F02_ID_USUA_CREA, F02_FE_USUA_CREA, F02_CLAVE
            ) VALUES (
                :id_cliente, :id_persona, :stdo, :id_usua_crea, NOW(), :clave
            )";

            $stmtCliente = $this->db->prepare($sqlCliente);
            $stmtCliente->execute([
                ':id_cliente' => $idCliente,
                ':id_persona' => $idPersona,
                ':stdo' => $datosCliente['stdo'] ?? 1,
                ':id_usua_crea' => $usuario,
                ':clave' => $datosCliente['clave'] ?? null
            ]);

            $this->db->commit();
            $this->log('Socio registrado exitosamente: Cliente ' . $idCliente . ', Persona ' . $idPersona);

            return [
                'success' => true,
                'id_cliente' => $idCliente,
                'id_persona' => $idPersona
            ];

        } catch (Exception $e) {
            $this->db->rollBack();
            $this->log('Error al registrar socio: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Actualizar un socio existente
     */
    public function actualizarSocio(int $idPersona, array $datosPersona, string $usuario = 'SISTEMA'): array
    {
        $this->log('Iniciando actualización de socio: ' . $idPersona, $datosPersona);

        try {
            $this->db->beginTransaction();

            // Actualizar f01_persona
            $sql = "UPDATE f01_persona SET
                F01_TPO_DOC = :tpo_doc,
                F01_NUM_DOC = :num_doc,
                F01_APE_PAT = :ape_pat,
                F01_APE_MAT = :ape_mat,
                F01_NOMB_PRI = :nomb_pri,
                F01_NOMB_SEG = :nomb_seg,
                F01_SEXO = :sexo,
                F01_FE_NAC = :fe_nac,
                F01_STDO_CIVIL = :stdo_civil,
                F01_STDO_PERSN = :stdo_persn,
                F01_CTA_CORREO = :cta_correo,
                F01_ID_NACIONALIDAD = :id_nacionalidad,
                F01_ID_INSTRUCCION = :id_instruccion,
                F01_ID_PROFESION = :id_profesion,
                F01_DPTO_NAC = :dpto_nac,
                F01_PROV_NAC = :prov_nac,
                F01_DISTRI_NAC = :distri_nac,
                F01_TELF_FIJO = :telf_fijo,
                F01_TELF_CEL = :telf_cel,
                F01_DESC_COMENT = :desc_coment,
                F01_ZONA = :zona,
                F01_VIA = :via,
                F01_INT = :int,
                F01_EXT = :ext,
                F01_PISO = :piso,
                F01_DEPARTAMENTO = :departamento,
                F01_PROVINCIA = :provincia,
                F01_DISTRITO = :distrito,
                F01_OBSERVACION = :observacion,
                F01_EDAD = :edad,
                F01_CENTRO = :centro,
                F01_CONDICION = :condicion,
                F01_CUENTA = :cuenta,
                F01_INGRESO = :ingreso,
                F01_APORTACION = :aportacion,
                F01_SALDO = :saldo,
                F01_DOCUMENTO = :documento,
                F01_SITUACION = :situacion,
                F01_ID_USUA_MODI = :id_usua_modi,
                F01_FE_USUA_MODI = NOW()
            WHERE F01_ID_PERSONA = :id_persona";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':id_persona' => $idPersona,
                ':tpo_doc' => $datosPersona['tpo_doc'] ?? null,
                ':num_doc' => $datosPersona['num_doc'] ?? null,
                ':ape_pat' => $datosPersona['ape_pat'] ?? null,
                ':ape_mat' => $datosPersona['ape_mat'] ?? null,
                ':nomb_pri' => $datosPersona['nomb_pri'] ?? null,
                ':nomb_seg' => $datosPersona['nomb_seg'] ?? null,
                ':sexo' => $datosPersona['sexo'] ?? null,
                ':fe_nac' => $datosPersona['fe_nac'] ?? null,
                ':stdo_civil' => $datosPersona['stdo_civil'] ?? null,
                ':stdo_persn' => $datosPersona['stdo_persn'] ?? null,
                ':cta_correo' => $datosPersona['cta_correo'] ?? null,
                ':id_nacionalidad' => $datosPersona['id_nacionalidad'] ?? null,
                ':id_instruccion' => $datosPersona['id_instruccion'] ?? null,
                ':id_profesion' => $datosPersona['id_profesion'] ?? null,
                ':dpto_nac' => $datosPersona['dpto_nac'] ?? null,
                ':prov_nac' => $datosPersona['prov_nac'] ?? null,
                ':distri_nac' => $datosPersona['distri_nac'] ?? null,
                ':telf_fijo' => $datosPersona['telf_fijo'] ?? null,
                ':telf_cel' => $datosPersona['telf_cel'] ?? null,
                ':desc_coment' => $datosPersona['desc_coment'] ?? null,
                ':zona' => $datosPersona['zona'] ?? null,
                ':via' => $datosPersona['via'] ?? null,
                ':int' => $datosPersona['int'] ?? null,
                ':ext' => $datosPersona['ext'] ?? null,
                ':piso' => $datosPersona['piso'] ?? null,
                ':departamento' => $datosPersona['departamento'] ?? null,
                ':provincia' => $datosPersona['provincia'] ?? null,
                ':distrito' => $datosPersona['distrito'] ?? null,
                ':observacion' => $datosPersona['observacion'] ?? null,
                ':edad' => $datosPersona['edad'] ?? null,
                ':centro' => $datosPersona['centro'] ?? null,
                ':condicion' => $datosPersona['condicion'] ?? null,
                ':cuenta' => $datosPersona['cuenta'] ?? null,
                ':ingreso' => $datosPersona['ingreso'] ?? null,
                ':aportacion' => $datosPersona['aportacion'] ?? null,
                ':saldo' => $datosPersona['saldo'] ?? null,
                ':documento' => $datosPersona['documento'] ?? null,
                ':situacion' => $datosPersona['situacion'] ?? null,
                ':id_usua_modi' => $usuario
            ]);

            $this->db->commit();
            $this->log('Socio actualizado exitosamente: ' . $idPersona);

            return [
                'success' => true,
                'id_persona' => $idPersona
            ];

        } catch (Exception $e) {
            $this->db->rollBack();
            $this->log('Error al actualizar socio: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Obtener datos de un socio por ID de cliente
     */
    public function obtenerSocioPorCliente(string $idCliente): ?array
    {
        $sql = "SELECT p.*, c.F02_ID_CLIENTE, c.F02_STDO,
                        p.F01_FIRMO, p.F01_CARTA, p.F01_FECHA_BCO, p.F01_OBS_CARTA
                FROM f01_persona p
                INNER JOIN f02_cliente c ON p.F01_ID_PERSONA = c.F02_ID_PERSONA
                WHERE c.F02_ID_CLIENTE = :id_cliente";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_cliente' => $idCliente]);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    /**
     * Generar ID de persona de forma segura
     */
    private function generarIdPersona(): int
    {
        // Usar FOR UPDATE para evitar condiciones de carrera
        $sql = "SELECT MAX(F01_ID_PERSONA) as max_id FROM f01_persona FOR UPDATE";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();

        $maxId = $result['max_id'] ?? 0;
        return $maxId + 1;
    }

    /**
     * Generar ID de cliente de forma segura
     */
    private function generarIdCliente(): string
    {
        // Usar FOR UPDATE para evitar condiciones de carrera
        $sql = "SELECT CAST(F02_ID_CLIENTE AS UNSIGNED) as max_id FROM f02_cliente FOR UPDATE";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();

        $maxId = $result['max_id'] ?? 0;
        return (string)($maxId + 1);
    }

    /**
     * Obtener listas de referencia (nacionalidades, profesiones, etc.)
     */
    public function obtenerListasReferencia(): array
    {
        $listas = [];

        // Nacionalidades
        $sql = "SELECT F11_ID_NACIONALIDAD, F11_DESC_NACIONALD FROM f11_nacionalidad WHERE F11_STDO_NACIONALD = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $listas['nacionalidades'] = $stmt->fetchAll();

        // Profesiones
        $sql = "SELECT F09_ID_PROFESION, F09_DESC_PROF FROM f09_profesion WHERE F09_STDO_PROF = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $listas['profesiones'] = $stmt->fetchAll();

        // Instrucciones
        $sql = "SELECT F10_ID_INSTRUCCION, F10_DESC_INSTRUCC FROM f10_instruccion WHERE F10_STDO_INSTRUCC = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $listas['instrucciones'] = $stmt->fetchAll();

        return $listas;
    }

    /**
     * Obtener información detallada de un socio
     */
    public function obtenerSocioDetalle(string $idCliente): array
    {
        $sql = "SELECT 
                    c.F02_ID_CLIENTE,
                    c.F02_STDO as cliente_estado,
                    p.F01_ID_PERSONA,
                    p.F01_TPO_DOC,
                    p.F01_NUM_DOC,
                    p.F01_APE_PAT,
                    p.F01_APE_MAT,
                    p.F01_NOMB_PRI,
                    p.F01_NOMB_SEG,
                    p.F01_SEXO,
                    p.F01_FE_NAC,
                    p.F01_STDO_CIVIL,
                    p.F01_STDO_PERSN,
                    p.F01_CTA_CORREO,
                    p.F01_TELF_FIJO,
                    p.F01_TELF_CEL,
                    p.F01_ZONA,
                    p.F01_VIA,
                    p.F01_INT,
                    p.F01_EXT,
                    p.F01_PISO,
                    p.F01_DEPARTAMENTO,
                    p.F01_PROVINCIA,
                    p.F01_DISTRITO,
                    p.F01_OBSERVACION,
                    p.F01_EDAD,
                    p.F01_CENTRO,
                    p.F01_CONDICION,
                    p.F01_CUENTA,
                    p.F01_INGRESO,
                    p.F01_APORTACION,
                    p.F01_APORTE_COPE,
                    p.F01_SALDO,
                    p.F01_DOCUMENTO,
                    p.F01_SITUACION,
                    p.F01_FECHA_RETIRO,
                    p.F01_FECHA_FALLE,
                    p.F01_ID_NACIONALIDAD,
                    p.F01_ID_INSTRUCCION,
                    p.F01_ID_PROFESION,
                    p.F01_DPTO_NAC,
                    p.F01_PROV_NAC,
                    p.F01_DISTRI_NAC,
                    p.F01_FIRMO,
                    p.F01_CARTA,
                    p.F01_FECHA_BCO,
                    p.F01_OBS_CARTA,
                    p.F01_OBS2,
                    p.F01_TITULO,
                    nac.F11_DESC_NACIONALD as nacionalidad_nombre,
                    ins.F10_DESC_INSTRUCC as instruccion_nombre,
                    prof.F09_DESC_PROF as profesion_nombre
                FROM f02_cliente c
                INNER JOIN f01_persona p ON c.F02_ID_PERSONA = p.F01_ID_PERSONA
                LEFT JOIN f11_nacionalidad nac ON p.F01_ID_NACIONALIDAD = nac.F11_ID_NACIONALIDAD
                LEFT JOIN f10_instruccion ins ON p.F01_ID_INSTRUCCION = ins.F10_ID_INSTRUCCION
                LEFT JOIN f09_profesion prof ON p.F01_ID_PROFESION = prof.F09_ID_PROFESION
                WHERE CAST(c.F02_ID_CLIENTE AS UNSIGNED) = CAST(:cliente AS UNSIGNED)
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':cliente' => $idCliente]);
        $socio = $stmt->fetch();
        
        if ($socio === false) {
            return [];
        }
        
        return $socio;
    }

    /**
     * Obtener historial de cambios de un socio
     */
    public function obtenerHistorialCambios(string $idCliente): array
    {
        $sql = "SELECT 
                    'CREACION' as tipo_accion,
                    p.F01_ID_USUA_CREA as usuario,
                    p.F01_FE_USUA_CREA as fecha,
                    'Registro inicial' as descripcion,
                    c.F02_ID_CLIENTE as cliente_codigo,
                    p.F01_APE_PAT as socio_nombre
                FROM f01_persona p
                INNER JOIN f02_cliente c ON p.F01_ID_PERSONA = c.F02_ID_PERSONA
                WHERE CAST(c.F02_ID_CLIENTE AS UNSIGNED) = CAST(:cliente1 AS UNSIGNED)
                
                UNION ALL
                
                SELECT 
                    'MODIFICACION' as tipo_accion,
                    p.F01_ID_USUA_MODI as usuario,
                    p.F01_FE_USUA_MODI as fecha,
                    'Actualizacion de datos' as descripcion,
                    c.F02_ID_CLIENTE as cliente_codigo,
                    p.F01_APE_PAT as socio_nombre
                FROM f01_persona p
                INNER JOIN f02_cliente c ON p.F01_ID_PERSONA = c.F02_ID_PERSONA
                WHERE CAST(c.F02_ID_CLIENTE AS UNSIGNED) = CAST(:cliente2 AS UNSIGNED)
                    AND p.F01_ID_USUA_MODI IS NOT NULL
                
                ORDER BY fecha DESC
                LIMIT 50";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':cliente1' => $idCliente, ':cliente2' => $idCliente]);
        return $stmt->fetchAll();
    }

    /**
     * Obtener todos los cambios de auditoría
     */
    public function obtenerTodosLosCambios(int $limite = 100): array
    {
        $sql = "SELECT 
                    'CREACION' as tipo_accion,
                    p.F01_ID_USUA_CREA as usuario,
                    p.F01_FE_USUA_CREA as fecha,
                    'Registro inicial' as descripcion,
                    c.F02_ID_CLIENTE as cliente_codigo,
                    p.F01_APE_PAT as socio_nombre
                FROM f01_persona p
                INNER JOIN f02_cliente c ON p.F01_ID_PERSONA = c.F02_ID_PERSONA
                WHERE p.F01_ID_USUA_CREA IS NOT NULL
                
                UNION ALL
                
                SELECT 
                    'MODIFICACION' as tipo_accion,
                    p.F01_ID_USUA_MODI as usuario,
                    p.F01_FE_USUA_MODI as fecha,
                    'Actualizacion de datos' as descripcion,
                    c.F02_ID_CLIENTE as cliente_codigo,
                    p.F01_APE_PAT as socio_nombre
                FROM f01_persona p
                INNER JOIN f02_cliente c ON p.F01_ID_PERSONA = c.F02_ID_PERSONA
                WHERE p.F01_ID_USUA_MODI IS NOT NULL
                
                ORDER BY fecha DESC
                LIMIT :limite";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limite', $limite, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Listar socios con filtros avanzados
     */
    public function listarSocios(array $filtros = [], int $offset = 0, int $limit = 10): array
    {
        $where = ['c.F02_STDO = 1'];
        $params = [];

        // Filtro de búsqueda general (nombre, apellido, código)
        if (!empty($filtros['criterio'])) {
            $where[] = "(p.F01_APE_PAT LIKE :criterio1 OR p.F01_APE_MAT LIKE :criterio2 OR p.F01_NOMB_PRI LIKE :criterio3 OR c.F02_ID_CLIENTE LIKE :criterio4)";
            $criterioParam = '%' . $filtros['criterio'] . '%';
            $params[':criterio1'] = $criterioParam;
            $params[':criterio2'] = $criterioParam;
            $params[':criterio3'] = $criterioParam;
            $params[':criterio4'] = $criterioParam;
        }

        // Filtro por departamento
        if (!empty($filtros['departamento'])) {
            $where[] = "p.F01_DEPARTAMENTO = :departamento";
            $params[':departamento'] = $filtros['departamento'];
        }

        // Filtro por provincia
        if (!empty($filtros['provincia'])) {
            $where[] = "p.F01_PROVINCIA = :provincia";
            $params[':provincia'] = $filtros['provincia'];
        }

        // Filtro por distrito
        if (!empty($filtros['distrito'])) {
            $where[] = "p.F01_DISTRITO = :distrito";
            $params[':distrito'] = $filtros['distrito'];
        }

        // Filtro por sexo
        if (!empty($filtros['sexo'])) {
            $where[] = "p.F01_SEXO = :sexo";
            $params[':sexo'] = $filtros['sexo'];
        }

        // Filtro por condición
        if (!empty($filtros['condicion'])) {
            $where[] = "p.F01_CONDICION = :condicion";
            $params[':condicion'] = $filtros['condicion'];
        }

        // Filtro por situación
        if (!empty($filtros['situacion'])) {
            $where[] = "p.F01_SITUACION = :situacion";
            $params[':situacion'] = $filtros['situacion'];
        }

        // Filtro por edad mínima
        if (!empty($filtros['edad_min'])) {
            $where[] = "p.F01_EDAD >= :edad_min";
            $params[':edad_min'] = (int) $filtros['edad_min'];
        }

        // Filtro por edad máxima
        if (!empty($filtros['edad_max'])) {
            $where[] = "p.F01_EDAD <= :edad_max";
            $params[':edad_max'] = (int) $filtros['edad_max'];
        }

        // Filtro por estado (activo/inactivo)
        if (isset($filtros['estado']) && $filtros['estado'] !== '') {
            $estado = (int) $filtros['estado'];
            $where[] = "c.F02_STDO = :estado";
            $params[':estado'] = $estado;
        }

        $sql = "SELECT 
                    c.F02_ID_CLIENTE,
                    p.F01_ID_PERSONA,
                    p.F01_APE_PAT,
                    p.F01_APE_MAT,
                    p.F01_NOMB_PRI,
                    p.F01_NOMB_SEG,
                    p.F01_NUM_DOC,
                    p.F01_TELF_FIJO,
                    p.F01_TELF_CEL,
                    p.F01_CTA_CORREO,
                    p.F01_CONDICION,
                    p.F01_SITUACION,
                    p.F01_FECHA_RETIRO,
                    p.F01_FECHA_FALLE,
                    p.F01_DEPARTAMENTO,
                    p.F01_PROVINCIA,
                    p.F01_DISTRITO,
                    p.F01_SEXO,
                    p.F01_EDAD
                FROM f02_cliente c
                INNER JOIN f01_persona p ON c.F02_ID_PERSONA = p.F01_ID_PERSONA
                WHERE " . implode(' AND ', $where) . "
                ORDER BY p.F01_APE_PAT, p.F01_APE_MAT, p.F01_NOMB_PRI
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Contar total de socios con filtros
     */
    public function contarSocios(array $filtros = []): int
    {
        $where = ['c.F02_STDO = 1'];
        $params = [];

        // Filtro de búsqueda general
        if (!empty($filtros['criterio'])) {
            $where[] = "(p.F01_APE_PAT LIKE :criterio1 OR p.F01_APE_MAT LIKE :criterio2 OR p.F01_NOMB_PRI LIKE :criterio3 OR c.F02_ID_CLIENTE LIKE :criterio4)";
            $criterioParam = '%' . $filtros['criterio'] . '%';
            $params[':criterio1'] = $criterioParam;
            $params[':criterio2'] = $criterioParam;
            $params[':criterio3'] = $criterioParam;
            $params[':criterio4'] = $criterioParam;
        }

        // Filtro por departamento
        if (!empty($filtros['departamento'])) {
            $where[] = "p.F01_DEPARTAMENTO = :departamento";
            $params[':departamento'] = $filtros['departamento'];
        }

        // Filtro por provincia
        if (!empty($filtros['provincia'])) {
            $where[] = "p.F01_PROVINCIA = :provincia";
            $params[':provincia'] = $filtros['provincia'];
        }

        // Filtro por distrito
        if (!empty($filtros['distrito'])) {
            $where[] = "p.F01_DISTRITO = :distrito";
            $params[':distrito'] = $filtros['distrito'];
        }

        // Filtro por sexo
        if (!empty($filtros['sexo'])) {
            $where[] = "p.F01_SEXO = :sexo";
            $params[':sexo'] = $filtros['sexo'];
        }

        // Filtro por condición
        if (!empty($filtros['condicion'])) {
            $where[] = "p.F01_CONDICION = :condicion";
            $params[':condicion'] = $filtros['condicion'];
        }

        // Filtro por situación
        if (!empty($filtros['situacion'])) {
            $where[] = "p.F01_SITUACION = :situacion";
            $params[':situacion'] = $filtros['situacion'];
        }

        // Filtro por edad mínima
        if (!empty($filtros['edad_min'])) {
            $where[] = "p.F01_EDAD >= :edad_min";
            $params[':edad_min'] = (int) $filtros['edad_min'];
        }

        // Filtro por edad máxima
        if (!empty($filtros['edad_max'])) {
            $where[] = "p.F01_EDAD <= :edad_max";
            $params[':edad_max'] = (int) $filtros['edad_max'];
        }

        // Filtro por estado
        if (isset($filtros['estado']) && $filtros['estado'] !== '') {
            $estado = (int) $filtros['estado'];
            $where[] = "c.F02_STDO = :estado";
            $params[':estado'] = $estado;
        }

        $sql = "SELECT COUNT(*) as total
                FROM f02_cliente c
                INNER JOIN f01_persona p ON c.F02_ID_PERSONA = p.F01_ID_PERSONA
                WHERE " . implode(' AND ', $where);

        $stmt = $this->db->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        $result = $stmt->fetch();
        return (int) ($result['total'] ?? 0);
    }

    /**
     * Obtener todos los datos de ubigeo para JavaScript
     */
    public function obtenerTodosUbigeos(): array
    {
        $sql = "SELECT 
                    SUBSTRING(ubigeo_codigo, 1, 2) as departamento_codigo,
                    ubigeo_departamento as departamento_nombre,
                    ubigeo_provincia as provincia,
                    ubigeo_distrito as distrito
                FROM f05_ubigeo 
                WHERE ubigeo_codigo IS NOT NULL
                ORDER BY ubigeo_codigo";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Obtener departamentos
     */
    public function obtenerDepartamentos(): array
    {
        $sql = "SELECT DISTINCT 
                    SUBSTRING(ubigeo_codigo, 1, 2) as codigo,
                    ubigeo_distrito as nombre
                FROM f05_ubigeo 
                WHERE ubigeo_distrito IS NOT NULL 
                ORDER BY ubigeo_distrito ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Obtener todas las provincias
     */
    public function obtenerTodasProvincias(): array
    {
        $sql = "SELECT DISTINCT 
                    SUBSTRING(ubigeo_codigo, 3, 2) as codigo,
                    ubigeo_provincia as nombre
                FROM f05_ubigeo 
                WHERE ubigeo_provincia IS NOT NULL
                ORDER BY ubigeo_provincia ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Obtener todos los distritos
     */
    public function obtenerTodosDistritos(): array
    {
        $sql = "SELECT DISTINCT 
                    SUBSTRING(ubigeo_codigo, 5, 2) as codigo,
                    ubigeo_departamento as nombre
                FROM f05_ubigeo 
                WHERE ubigeo_departamento IS NOT NULL
                ORDER BY ubigeo_departamento ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Obtener provincias por departamento
     */
    public function obtenerProvincias(string $departamento): array
    {
        $sql = "SELECT DISTINCT 
                    ubigeo_provincia as codigo,
                    ubigeo_provincia as nombre
                FROM f05_ubigeo 
                WHERE SUBSTRING(ubigeo_codigo, 1, 2) = :departamento
                    AND ubigeo_provincia IS NOT NULL
                    AND ubigeo_provincia != ''
                ORDER BY ubigeo_provincia ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['departamento' => $departamento]);
        $result = $stmt->fetchAll();
        
        // Debug: log para verificar
        $this->log('obtenerProvincias para departamento ' . $departamento, ['resultados' => count($result), 'datos' => $result]);
        
        return $result;
    }

    /**
     * Obtener distritos por departamento y provincia
     */
    public function obtenerDistritos(string $departamento, string $provincia): array
    {
        $sql = "SELECT DISTINCT 
                    ubigeo_departamento as codigo,
                    ubigeo_departamento as nombre
                FROM f05_ubigeo 
                WHERE SUBSTRING(ubigeo_codigo, 1, 2) = :departamento
                    AND ubigeo_provincia = :provincia
                    AND ubigeo_departamento IS NOT NULL
                    AND ubigeo_departamento != ''
                ORDER BY ubigeo_departamento ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['departamento' => $departamento, 'provincia' => $provincia]);
        $result = $stmt->fetchAll();
        
        // Debug: log para verificar
        $this->log('obtenerDistritos para departamento ' . $departamento . ' provincia ' . $provincia, ['resultados' => count($result), 'datos' => $result]);
        
        return $result;
    }

    /**
     * Obtener condiciones únicas
     */
    public function obtenerCondiciones(): array
    {
        $sql = "SELECT DISTINCT F01_CONDICION FROM f01_persona WHERE F01_CONDICION IS NOT NULL AND F01_CONDICION != '' ORDER BY F01_CONDICION";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Obtener situaciones únicas
     */
    public function obtenerSituaciones(): array
    {
        $sql = "SELECT DISTINCT F01_SITUACION FROM f01_persona WHERE F01_SITUACION IS NOT NULL AND F01_SITUACION != '' ORDER BY F01_SITUACION";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Obtener aportes FONBES de un socio
     */
    public function obtenerAportesFonbes(string $clienteCodigo, string $filtro = 'todos', int $limite = 24): array
    {
        $whereClause = "WHERE f22_socio = :cliente";
        $params = ['cliente' => $clienteCodigo];

        if ($filtro === 'pagados') {
            $whereClause .= " AND f22_aportacionD > 0 AND f22_aportacionD >= f22_aportacionP";
        } elseif ($filtro === 'pendientes') {
            $whereClause .= " AND (f22_aportacionD = 0 OR f22_aportacionD < f22_aportacionP)";
        }

        $sql = "SELECT 
                    f22_periodo AS periodo,
                    f22_aportacionP AS planificado,
                    f22_aportacionD AS pagado,
                    f22_fechaPago AS fecha_pago,
                    f22_diferencia AS diferencia
                FROM f22_aportes
                $whereClause
                ORDER BY f22_periodo DESC
                LIMIT :limite";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':cliente', $clienteCodigo);
        $stmt->bindValue(':limite', $limite, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Obtener aportes COOPAM de un socio
     */
    public function obtenerAportesCoopam(string $clienteCodigo, string $filtro = 'todos', int $limite = 24): array
    {
        $whereClause = "WHERE f27_socio = :cliente";
        $params = ['cliente' => $clienteCodigo];

        if ($filtro === 'pagados') {
            $whereClause .= " AND f27_aportacionD > 0 AND f27_aportacionD >= f27_aportacionP";
        } elseif ($filtro === 'pendientes') {
            $whereClause .= " AND (f27_aportacionD = 0 OR f27_aportacionD < f27_aportacionP)";
        }

        $sql = "SELECT 
                    f27_periodo AS periodo,
                    f27_aportacionP AS planificado,
                    f27_aportacionD AS pagado,
                    f27_fechaPago AS fecha_pago,
                    f27_diferencia AS diferencia
                FROM f27_aportes_cope
                $whereClause
                ORDER BY f27_periodo DESC
                LIMIT :limite";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':cliente', $clienteCodigo);
        $stmt->bindValue(':limite', $limite, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Obtener resumen de aportes FONBES de un socio
     */
    public function obtenerResumenAportesFonbes(string $clienteCodigo): array
    {
        $sql = "SELECT 
                    COALESCE(SUM(f22_aportacionP), 0) as total_planificado,
                    COALESCE(SUM(f22_aportacionD), 0) as total_pagado,
                    COALESCE(SUM(f22_diferencia), 0) as total_pendiente,
                    COUNT(f22_periodo) as total_periodos
                FROM f22_aportes
                WHERE f22_socio = :cliente";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['cliente' => $clienteCodigo]);
        return $stmt->fetch();
    }

    /**
     * Obtener resumen de aportes COOPAM de un socio
     */
    public function obtenerResumenAportesCoopam(string $clienteCodigo): array
    {
        $sql = "SELECT 
                    COALESCE(SUM(f27_aportacionP), 0) as total_planificado,
                    COALESCE(SUM(f27_aportacionD), 0) as total_pagado,
                    COALESCE(SUM(f27_diferencia), 0) as total_pendiente,
                    COUNT(f27_periodo) as total_periodos
                FROM f27_aportes_cope
                WHERE f27_socio = :cliente";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['cliente' => $clienteCodigo]);
        return $stmt->fetch();
    }

    /**
     * Obtener estado actual del socio
     */
    public function obtenerEstadoSocio(string|int $idPersona): array
    {
        $idPersona = (string) $idPersona;
        $sql = "SELECT 
                    F01_STDO_PERSN as estado,
                    F01_FECHA_RETIRO as fecha_retiro,
                    F01_FEC_REINGRESO as fecha_reingreso,
                    F01_FEC_ULT_APORTE as fecha_ultimo_aporte,
                    F01_VISIBLE as visible
                FROM f01_persona
                WHERE F01_ID_PERSONA = :id_persona";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_persona' => $idPersona]);
        $resultado = $stmt->fetch();

        if ($resultado === false) {
            return ['estado' => null, 'descripcion' => 'No encontrado'];
        }

        $estados = [
            1 => 'ACTIVO',
            0 => 'ELIMINADO',
            2 => 'ELIMINADO FINAL',
            5 => 'ELIMINADO DEUDA',
            7 => 'ELIMINADO CASTIGO',
            8 => 'ELIMINADO FALLE',
            9 => 'ELIMINADO PARCIAL',
        ];

        $resultado['descripcion'] = $estados[$resultado['estado']] ?? 'DESCONOCIDO';
        return $resultado;
    }

    /**
     * Eliminar socio según tipo
     */
    public function eliminarSocio(string $idPersona, string $tipo, string $usuario = 'SISTEMA', ?string $fechaUltimoAporte = null): array
    {
        $this->log('Iniciando eliminación de socio', ['id_persona' => $idPersona, 'tipo' => $tipo, 'usuario' => $usuario]);

        try {
            $this->db->beginTransaction();

            $tipo = strtoupper($tipo);
            $stmt = null;

            switch ($tipo) {
                case 'NORMAL':
                    $sql = "UPDATE f01_persona SET 
                                F01_STDO_PERSN = 0,
                                F01_FECHA_RETIRO = NOW(),
                                F01_APORTA_BASE = 0,
                                F01_APORTACION = 0,
                                F01_APORTE_COPE = 0,
                                F01_ID_USUA_MODI = :usuario,
                                F01_FE_USUA_MODI = NOW()
                            WHERE F01_ID_PERSONA = :id_persona";
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute(['id_persona' => $idPersona, 'usuario' => $usuario]);
                    break;

                case 'FALLE':
                    $sql = "UPDATE f01_persona SET 
                                F01_STDO_PERSN = 8,
                                F01_FECHA_RETIRO = NOW(),
                                F01_APORTA_BASE = 0,
                                F01_APORTACION = 0,
                                F01_APORTE_COPE = 0,
                                F01_ID_USUA_MODI = :usuario,
                                F01_FE_USUA_MODI = NOW()
                            WHERE F01_ID_PERSONA = :id_persona";
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute(['id_persona' => $idPersona, 'usuario' => $usuario]);
                    break;

                case 'DEUDA':
                    $sql = "UPDATE f01_persona SET 
                                F01_STDO_PERSN = 5,
                                F01_FECHA_RETIRO = NOW(),
                                F01_APORTA_BASE = 0,
                                F01_APORTACION = 0,
                                F01_APORTE_COPE = 0,
                                F01_ID_USUA_MODI = :usuario,
                                F01_FE_USUA_MODI = NOW()
                            WHERE F01_ID_PERSONA = :id_persona";
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute(['id_persona' => $idPersona, 'usuario' => $usuario]);
                    break;

                case 'PARCIAL':
                    if ($fechaUltimoAporte === null) {
                        throw new Exception('La eliminación parcial requiere fecha de último aporte');
                    }
                    $sql = "UPDATE f01_persona SET 
                                F01_STDO_PERSN = 9,
                                F01_FEC_ULT_APORTE = :fecha_ultimo_aporte,
                                F01_ID_USUA_MODI = :usuario,
                                F01_FE_USUA_MODI = NOW()
                            WHERE F01_ID_PERSONA = :id_persona";
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute([
                        'id_persona' => $idPersona,
                        'fecha_ultimo_aporte' => $fechaUltimoAporte,
                        'usuario' => $usuario
                    ]);
                    break;

                case 'CASTIGO':
                    $sql = "UPDATE f01_persona SET 
                                F01_STDO_PERSN = 7,
                                F01_FECHA_RETIRO = NOW(),
                                F01_APORTA_BASE = 0,
                                F01_APORTACION = 0,
                                F01_APORTE_COPE = 0,
                                F01_ID_USUA_MODI = :usuario,
                                F01_FE_USUA_MODI = NOW()
                            WHERE F01_ID_PERSONA = :id_persona";
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute(['id_persona' => $idPersona, 'usuario' => $usuario]);
                    break;

                case 'FINAL':
                    $sql = "UPDATE f01_persona SET 
                                F01_STDO_PERSN = 2,
                                F01_APORTA_BASE = 0,
                                F01_APORTACION = 0,
                                F01_ID_USUA_MODI = :usuario,
                                F01_FE_USUA_MODI = NOW()
                            WHERE F01_ID_PERSONA = :id_persona";
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute(['id_persona' => $idPersona, 'usuario' => $usuario]);
                    break;

                default:
                    throw new Exception('Tipo de eliminación no válido: ' . $tipo);
            }

            $this->db->commit();
            $this->log('Socio eliminado correctamente', ['id_persona' => $idPersona, 'tipo' => $tipo]);
            return ['success' => true, 'message' => 'Socio eliminado correctamente'];

        } catch (Exception $e) {
            $this->db->rollBack();
            $this->log('Error al eliminar socio', ['id_persona' => $idPersona, 'error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Error al eliminar socio: ' . $e->getMessage()];
        }
    }

    /**
     * Activar socio
     */
    public function activarSocio(string $idPersona, string $usuario = 'SISTEMA'): array
    {
        $this->log('Iniciando activación de socio', ['id_persona' => $idPersona, 'usuario' => $usuario]);

        try {
            $this->db->beginTransaction();

            $sql = "UPDATE f01_persona SET 
                        F01_STDO_PERSN = 1,
                        F01_FEC_REINGRESO = NOW(),
                        F01_ID_USUA_MODI = :usuario,
                        F01_FE_USUA_MODI = NOW()
                    WHERE F01_ID_PERSONA = :id_persona";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id_persona' => $idPersona, 'usuario' => $usuario]);

            $this->db->commit();
            $this->log('Socio activado correctamente', ['id_persona' => $idPersona]);
            return ['success' => true, 'message' => 'Socio activado correctamente'];

        } catch (Exception $e) {
            $this->db->rollBack();
            $this->log('Error al activar socio', ['id_persona' => $idPersona, 'error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Error al activar socio: ' . $e->getMessage()];
        }
    }

    /**
     * Ocultar/mostrar socio
     */
    public function ocultarSocio(string $idPersona, int $visible, string $usuario = 'SISTEMA'): array
    {
        $this->log('Iniciando cambio de visibilidad', ['id_persona' => $idPersona, 'visible' => $visible, 'usuario' => $usuario]);

        try {
            $this->db->beginTransaction();

            $sql = "UPDATE f01_persona SET 
                        F01_VISIBLE = :visible,
                        F01_ID_USUA_MODI = :usuario,
                        F01_FE_USUA_MODI = NOW()
                    WHERE F01_ID_PERSONA = :id_persona";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id_persona' => $idPersona, 'visible' => $visible, 'usuario' => $usuario]);

            $this->db->commit();
            $this->log('Visibilidad actualizada correctamente', ['id_persona' => $idPersona, 'visible' => $visible]);
            return ['success' => true, 'message' => $visible === 0 ? 'Socio ocultado' : 'Socio visible'];

        } catch (Exception $e) {
            $this->db->rollBack();
            $this->log('Error al cambiar visibilidad', ['id_persona' => $idPersona, 'error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Error al cambiar visibilidad: ' . $e->getMessage()];
        }
    }

    /**
     * Verificar si socio tiene préstamos activos
     */
    public function tienePrestamosActivos(string $clienteCodigo): bool
    {
        $sql = "SELECT COUNT(*) as total 
                FROM f16_prestamo 
                WHERE f16_cliente = :cliente 
                AND f16_estado = 4";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['cliente' => $clienteCodigo]);
        $resultado = $stmt->fetch();
        
        return ($resultado['total'] ?? 0) > 0;
    }

    /**
     * Obtener beneficios de un socio
     */
    public function obtenerBeneficios(string $clienteCodigo, ?string $fechaDesde = null, ?string $fechaHasta = null): array
    {
        $sql = "SELECT 
                    f21_codigo as codigo,
                    f21_socio as socio,
                    f21_nombres as nombres,
                    f21_tipoPa as tipo_pa1,
                    f21_fechaFa as fecha_falle,
                    f21_beneficiario as beneficiario,
                    f21_tipoPa2 as tipo_pa2,
                    f21_fecha as fecha,
                    f21_importe as importe,
                    f21_estado as estado,
                    f21_dni1 as dni1,
                    f21_direccion1 as direccion1,
                    f21_dni2 as dni2,
                    f21_direccion2 as direccion2,
                    f21_tipo as tipo,
                    f21_obs as obs,
                    f21_acredita as acredita,
                    f21_aporta as aporta,
                    f21_lugar_falle as lugar_falle,
                    f21_tramite as tramite,
                    f21_vigencia as vigencia
                FROM f21_beneficios
                WHERE f21_socio = :cliente";
        
        $params = ['cliente' => $clienteCodigo];
        
        if ($fechaDesde !== null) {
            $sql .= " AND f21_fecha >= :fecha_desde";
            $params['fecha_desde'] = $fechaDesde;
        }
        
        if ($fechaHasta !== null) {
            $sql .= " AND f21_fecha <= :fecha_hasta";
            $params['fecha_hasta'] = $fechaHasta;
        }
        
        $sql .= " ORDER BY f21_fecha DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Obtener un beneficio específico
     */
    public function obtenerBeneficio(int $codigo): ?array
    {
        $sql = "SELECT 
                    f21_codigo as codigo,
                    f21_socio as socio,
                    f21_nombres as nombres,
                    f21_tipoPa as tipo_pa1,
                    f21_fechaFa as fecha_falle,
                    f21_beneficiario as beneficiario,
                    f21_tipoPa2 as tipo_pa2,
                    f21_fecha as fecha,
                    f21_importe as importe,
                    f21_estado as estado,
                    f21_dni1 as dni1,
                    f21_direccion1 as direccion1,
                    f21_dni2 as dni2,
                    f21_direccion2 as direccion2,
                    f21_tipo as tipo,
                    f21_obs as obs,
                    f21_acredita as acredita,
                    f21_aporta as aporta,
                    f21_lugar_falle as lugar_falle,
                    f21_tramite as tramite,
                    f21_vigencia as vigencia
                FROM f21_beneficios
                WHERE f21_codigo = :codigo";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['codigo' => $codigo]);
        $resultado = $stmt->fetch();
        
        return $resultado ?: null;
    }

    /**
     * Registrar nuevo beneficio
     */
    public function registrarBeneficio(array $datos, string $usuario = 'SISTEMA'): array
    {
        $this->log('Iniciando registro de beneficio', $datos);

        try {
            $this->db->beginTransaction();

            // Obtener siguiente código
            $sqlMax = "SELECT MAX(f21_codigo) as max_codigo FROM f21_beneficios";
            $stmtMax = $this->db->query($sqlMax);
            $maxCodigo = $stmtMax->fetch()['max_codigo'] ?? 0;
            $nuevoCodigo = $maxCodigo + 1;

            $sql = "INSERT INTO f21_beneficios (
                        f21_codigo, f21_socio, f21_nombres, f21_tipoPa, f21_fechaFa,
                        f21_beneficiario, f21_tipoPa2, f21_fecha, f21_importe, f21_estado,
                        f21_dni1, f21_direccion1, f21_dni2, f21_direccion2, f21_tipo,
                        f21_obs, f21_acredita, f21_aporta, f21_lugar_falle, f21_tramite, f21_vigencia
                    ) VALUES (
                        :codigo, :socio, :nombres, :tipo_pa1, :fecha_falle,
                        :beneficiario, :tipo_pa2, NOW(), :importe, 'REGISTRADO',
                        :dni1, :direccion1, :dni2, :direccion2, :tipo,
                        :obs, :acredita, :aporta, :lugar_falle, :tramite, :vigencia
                    )";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'codigo' => $nuevoCodigo,
                'socio' => $datos['socio'],
                'nombres' => $datos['nombres'],
                'tipo_pa1' => $datos['tipo_pa1'] ?? null,
                'fecha_falle' => $datos['fecha_falle'] ?? null,
                'beneficiario' => $datos['beneficiario'],
                'tipo_pa2' => $datos['tipo_pa2'] ?? null,
                'importe' => $datos['importe'],
                'dni1' => $datos['dni1'],
                'direccion1' => $datos['direccion1'],
                'dni2' => $datos['dni2'] ?? null,
                'direccion2' => $datos['direccion2'] ?? null,
                'tipo' => $datos['tipo'],
                'obs' => $datos['obs'] ?? null,
                'acredita' => $datos['acredita'] ?? 0,
                'aporta' => $datos['aporta'] ?? 0,
                'lugar_falle' => $datos['lugar_falle'] ?? null,
                'tramite' => $datos['tramite'] ?? null,
                'vigencia' => $datos['vigencia'] ?? null,
            ]);

            $this->db->commit();
            $this->log('Beneficio registrado correctamente', ['codigo' => $nuevoCodigo]);
            return ['success' => true, 'message' => 'Beneficio registrado correctamente', 'codigo' => $nuevoCodigo];

        } catch (Exception $e) {
            $this->db->rollBack();
            $this->log('Error al registrar beneficio', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Error al registrar beneficio: ' . $e->getMessage()];
        }
    }

    /**
     * Actualizar beneficio existente
     */
    public function actualizarBeneficio(int $codigo, array $datos, string $usuario = 'SISTEMA'): array
    {
        $this->log('Iniciando actualización de beneficio', ['codigo' => $codigo, 'datos' => $datos]);

        try {
            $this->db->beginTransaction();

            $sql = "UPDATE f21_beneficios SET
                        f21_nombres = :nombres,
                        f21_tipoPa = :tipo_pa1,
                        f21_fechaFa = :fecha_falle,
                        f21_beneficiario = :beneficiario,
                        f21_tipoPa2 = :tipo_pa2,
                        f21_importe = :importe,
                        f21_dni1 = :dni1,
                        f21_direccion1 = :direccion1,
                        f21_dni2 = :dni2,
                        f21_direccion2 = :direccion2,
                        f21_tipo = :tipo,
                        f21_obs = :obs,
                        f21_acredita = :acredita,
                        f21_aporta = :aporta,
                        f21_lugar_falle = :lugar_falle,
                        f21_tramite = :tramite,
                        f21_vigencia = :vigencia
                    WHERE f21_codigo = :codigo";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'codigo' => $codigo,
                'nombres' => $datos['nombres'],
                'tipo_pa1' => $datos['tipo_pa1'] ?? null,
                'fecha_falle' => $datos['fecha_falle'] ?? null,
                'beneficiario' => $datos['beneficiario'],
                'tipo_pa2' => $datos['tipo_pa2'] ?? null,
                'importe' => $datos['importe'],
                'dni1' => $datos['dni1'],
                'direccion1' => $datos['direccion1'],
                'dni2' => $datos['dni2'] ?? null,
                'direccion2' => $datos['direccion2'] ?? null,
                'tipo' => $datos['tipo'],
                'obs' => $datos['obs'] ?? null,
                'acredita' => $datos['acredita'] ?? 0,
                'aporta' => $datos['aporta'] ?? 0,
                'lugar_falle' => $datos['lugar_falle'] ?? null,
                'tramite' => $datos['tramite'] ?? null,
                'vigencia' => $datos['vigencia'] ?? null,
            ]);

            $this->db->commit();
            $this->log('Beneficio actualizado correctamente', ['codigo' => $codigo]);
            return ['success' => true, 'message' => 'Beneficio actualizado correctamente'];

        } catch (Exception $e) {
            $this->db->rollBack();
            $this->log('Error al actualizar beneficio', ['codigo' => $codigo, 'error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Error al actualizar beneficio: ' . $e->getMessage()];
        }
    }

    /**
     * Eliminar beneficio (lógico)
     */
    public function eliminarBeneficio(int $codigo, string $usuario = 'SISTEMA'): array
    {
        $this->log('Iniciando eliminación de beneficio', ['codigo' => $codigo, 'usuario' => $usuario]);

        try {
            $this->db->beginTransaction();

            $sql = "UPDATE f21_beneficios SET f21_estado = 'BAJA' WHERE f21_codigo = :codigo";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['codigo' => $codigo]);

            $this->db->commit();
            $this->log('Beneficio eliminado correctamente', ['codigo' => $codigo]);
            return ['success' => true, 'message' => 'Beneficio eliminado correctamente'];

        } catch (Exception $e) {
            $this->db->rollBack();
            $this->log('Error al eliminar beneficio', ['codigo' => $codigo, 'error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Error al eliminar beneficio: ' . $e->getMessage()];
        }
    }

    /**
     * Obtener tipos de beneficio
     */
    public function obtenerTiposBeneficio(): array
    {
        // Por ahora retornamos una lista estática, podría venir de una tabla de referencia
        return [
            'SOBREVIVENCIA' => 'Sobrevivencia',
            'FUNERARIO' => 'Funerario',
            'EDUCACION' => 'Educación',
            'SALUD' => 'Salud',
            'OTRO' => 'Otro',
        ];
    }

    /**
     * Obtener tipos de parentesco
     */
    public function obtenerTiposParentesco(): array
    {
        // Por ahora retornamos una lista estática, podría venir de una tabla de referencia
        return [
            '1' => 'Esposo/a',
            '2' => 'Hijo/a',
            '3' => 'Padre/Madre',
            '4' => 'Hermano/a',
            '5' => 'Cónyuge',
            '6' => 'Abuelo/a',
            '7' => 'Nieto/a',
            '8' => 'Tío/a',
            '9' => 'Sobrino/a',
            '10' => 'Primo/a',
            '11' => 'Otro',
        ];
    }

    /**
     * Obtener familiares de un socio
     */
    public function obtenerFamiliares(string $clienteCodigo, bool $inactivos = false): array
    {
        $sql = "SELECT 
                    p.F01_ID_PERSONA as id_persona,
                    p.F01_TPO_DOC as tipo_doc,
                    p.F01_NUM_DOC as documento,
                    p.F01_APE_PAT as ape_pat,
                    p.F01_APE_MAT as ape_mat,
                    p.F01_NOMB_PRI as nom_pri,
                    p.F01_NOMB_SEG as nom_seg,
                    p.F01_SEXO as sexo,
                    p.F01_FE_NAC as fecha_nac,
                    p.F01_STDO_PERSN as estado_persona,
                    f.F12_ID_TPO_PARENT as tipo_parentesco,
                    f.F12_STDO_PARENT as estado_parentesco
                FROM f01_persona p
                INNER JOIN f12_familiar f ON f.F12_ID_PERSONA = p.F01_ID_PERSONA
                WHERE f.F12_ID_CLIENTE = :cliente";
        
        if (!$inactivos) {
            $sql .= " AND p.F01_STDO_PERSN <> 2";
        } else {
            $sql .= " AND p.F01_STDO_PERSN IN (0, 5, 7, 8, 9)";
        }
        
        $sql .= " ORDER BY p.F01_APE_PAT, p.F01_NOMB_PRI";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['cliente' => $clienteCodigo]);
        return $stmt->fetchAll();
    }

    /**
     * Registrar familiar
     */
    public function registrarFamiliar(string $clienteCodigo, string $idPersona, string $tipoParentesco, string $usuario = 'SISTEMA'): array
    {
        $this->log('Iniciando registro de familiar', ['cliente' => $clienteCodigo, 'persona' => $idPersona, 'parentesco' => $tipoParentesco]);

        try {
            $this->db->beginTransaction();

            $sql = "INSERT INTO f12_familiar (F12_ID_CLIENTE, F12_ID_PERSONA, F12_ID_TPO_PARENT, F12_STDO_PARENT)
                    VALUES (:cliente, :persona, :parentesco, 1)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'cliente' => $clienteCodigo,
                'persona' => $idPersona,
                'parentesco' => $tipoParentesco,
            ]);

            $this->db->commit();
            $this->log('Familiar registrado correctamente');
            return ['success' => true, 'message' => 'Familiar registrado correctamente'];

        } catch (Exception $e) {
            $this->db->rollBack();
            $this->log('Error al registrar familiar', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Error al registrar familiar: ' . $e->getMessage()];
        }
    }

    /**
     * Actualizar parentesco de familiar
     */
    public function actualizarParentesco(string $clienteCodigo, string $idPersona, string $tipoParentesco, string $usuario = 'SISTEMA'): array
    {
        $this->log('Iniciando actualización de parentesco', ['cliente' => $clienteCodigo, 'persona' => $idPersona, 'parentesco' => $tipoParentesco]);

        try {
            $this->db->beginTransaction();

            $sql = "UPDATE f12_familiar SET F12_ID_TPO_PARENT = :parentesco
                    WHERE F12_ID_CLIENTE = :cliente AND F12_ID_PERSONA = :persona";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'parentesco' => $tipoParentesco,
                'cliente' => $clienteCodigo,
                'persona' => $idPersona,
            ]);

            $this->db->commit();
            $this->log('Parentesco actualizado correctamente');
            return ['success' => true, 'message' => 'Parentesco actualizado correctamente'];

        } catch (Exception $e) {
            $this->db->rollBack();
            $this->log('Error al actualizar parentesco', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Error al actualizar parentesco: ' . $e->getMessage()];
        }
    }

    /**
     * Eliminar familiar
     */
    public function eliminarFamiliar(string $clienteCodigo, string $idPersona, string $usuario = 'SISTEMA'): array
    {
        $this->log('Iniciando eliminación de familiar', ['cliente' => $clienteCodigo, 'persona' => $idPersona]);

        try {
            $this->db->beginTransaction();

            $sql = "DELETE FROM f12_familiar
                    WHERE F12_ID_CLIENTE = :cliente AND F12_ID_PERSONA = :persona";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'cliente' => $clienteCodigo,
                'persona' => $idPersona,
            ]);

            $this->db->commit();
            $this->log('Familiar eliminado correctamente');
            return ['success' => true, 'message' => 'Familiar eliminado correctamente'];

        } catch (Exception $e) {
            $this->db->rollBack();
            $this->log('Error al eliminar familiar', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Error al eliminar familiar: ' . $e->getMessage()];
        }
    }

    /**
     * Buscar persona por documento
     */
    public function buscarPersonaPorDocumento(string $documento): ?array
    {
        $sql = "SELECT 
                    F01_ID_PERSONA as id_persona,
                    F01_TPO_DOC as tipo_doc,
                    F01_NUM_DOC as documento,
                    F01_APE_PAT as ape_pat,
                    F01_APE_MAT as ape_mat,
                    F01_NOMB_PRI as nom_pri,
                    F01_NOMB_SEG as nom_seg,
                    F01_SEXO as sexo,
                    F01_FE_NAC as fecha_nac,
                    F01_STDO_PERSN as estado
                FROM f01_persona
                WHERE F01_NUM_DOC = :documento
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['documento' => $documento]);
        $resultado = $stmt->fetch();
        
        return $resultado ?: null;
    }

    /**
     * Obtener rifas de un socio
     */
    public function obtenerRifas(string $clienteCodigo, ?string $fechaDesde = null, ?string $fechaHasta = null): array
    {
        $sql = "SELECT * FROM f64_rifa WHERE f64_socio = :socio";
        
        if ($fechaDesde !== null && $fechaDesde !== '') {
            $ano = substr($fechaDesde, 0, 4);
            $sql .= " AND YEAR(f64_periodo) >= :anoDesde";
        }
        
        if ($fechaHasta !== null && $fechaHasta !== '') {
            $ano2 = substr($fechaHasta, 0, 4);
            $sql .= " AND YEAR(f64_periodo) <= :anoHasta";
        }
        
        $sql .= " ORDER BY f64_periodo DESC";
        
        $stmt = $this->db->prepare($sql);
        $params = ['socio' => $clienteCodigo];
        
        if ($fechaDesde !== null && $fechaDesde !== '') {
            $ano = substr($fechaDesde, 0, 4);
            $params['anoDesde'] = $ano;
        }
        
        if ($fechaHasta !== null && $fechaHasta !== '') {
            $ano2 = substr($fechaHasta, 0, 4);
            $params['anoHasta'] = $ano2;
        }
        
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Obtener una rifa específica
     */
    public function obtenerRifa(int $codigo): ?array
    {
        $sql = "SELECT * FROM f64_rifa WHERE f64_codigo = :codigo";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['codigo' => $codigo]);
        $resultado = $stmt->fetch();
        return $resultado ?: null;
    }

    /**
     * Registrar rifa
     */
    public function registrarRifa(array $datos, string $usuario = 'SISTEMA'): array
    {
        $this->log('Iniciando registro de rifa', $datos);

        try {
            $this->db->beginTransaction();

            $sql = "INSERT INTO f64_rifa (f64_socio, f64_periodo, f64_rifap, f64_rifad, f64_rifar, f64_fechaPago, f64_formaPago)
                    VALUES (:socio, :periodo, :rifap, :rifad, :rifar, :fechaPago, :formaPago)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'socio' => $datos['socio'],
                'periodo' => $datos['periodo'],
                'rifap' => $datos['rifap'] ?? 0,
                'rifad' => $datos['rifad'] ?? 0,
                'rifar' => $datos['rifar'] ?? 0,
                'fechaPago' => $datos['fechaPago'] ?? null,
                'formaPago' => $datos['formaPago'] ?? null,
            ]);

            $this->db->commit();
            $this->log('Rifa registrada correctamente');
            return ['success' => true, 'message' => 'Rifa registrada correctamente'];

        } catch (Exception $e) {
            $this->db->rollBack();
            $this->log('Error al registrar rifa', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Error al registrar rifa: ' . $e->getMessage()];
        }
    }

    /**
     * Actualizar rifa
     */
    public function actualizarRifa(int $codigo, array $datos, string $usuario = 'SISTEMA'): array
    {
        $this->log('Iniciando actualización de rifa', ['codigo' => $codigo, 'datos' => $datos]);

        try {
            $this->db->beginTransaction();

            $sql = "UPDATE f64_rifa SET f64_rifap = :rifap, f64_rifad = :rifad, f64_rifar = :rifar, f64_fechaPago = :fechaPago, f64_periodo = :periodo
                    WHERE f64_codigo = :codigo";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'rifap' => $datos['rifap'] ?? 0,
                'rifad' => $datos['rifad'] ?? 0,
                'rifar' => $datos['rifar'] ?? 0,
                'fechaPago' => $datos['fechaPago'] ?? null,
                'periodo' => $datos['periodo'],
                'codigo' => $codigo,
            ]);

            $this->db->commit();
            $this->log('Rifa actualizada correctamente');
            return ['success' => true, 'message' => 'Rifa actualizada correctamente'];

        } catch (Exception $e) {
            $this->db->rollBack();
            $this->log('Error al actualizar rifa', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Error al actualizar rifa: ' . $e->getMessage()];
        }
    }

    /**
     * Eliminar rifa
     */
    public function eliminarRifa(int $codigo, string $usuario = 'SISTEMA'): array
    {
        $this->log('Iniciando eliminación de rifa', ['codigo' => $codigo]);

        try {
            $this->db->beginTransaction();

            $sql = "DELETE FROM f64_rifa WHERE f64_codigo = :codigo";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['codigo' => $codigo]);

            $this->db->commit();
            $this->log('Rifa eliminada correctamente');
            return ['success' => true, 'message' => 'Rifa eliminada correctamente'];

        } catch (Exception $e) {
            $this->db->rollBack();
            $this->log('Error al eliminar rifa', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Error al eliminar rifa: ' . $e->getMessage()];
        }
    }

    /**
     * Obtener rifa pendiente de socio
     */
    public function obtenerRifaPendiente(string $clienteCodigo): ?array
    {
        $sql = "SELECT * FROM f64_rifa WHERE f64_socio = :socio ORDER BY f64_codigo DESC LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['socio' => $clienteCodigo]);
        $resultado = $stmt->fetch();
        return $resultado ?: null;
    }

    /**
     * Obtener multas de un socio
     */
    public function obtenerMultas(string $clienteCodigo, ?string $fechaDesde = null, ?string $fechaHasta = null): array
    {
        $sql = "SELECT * FROM f63_multa WHERE f63_socio = :socio";
        
        if ($fechaDesde !== null && $fechaDesde !== '') {
            $ano = substr($fechaDesde, 0, 4);
            $sql .= " AND YEAR(f63_periodo) >= :anoDesde";
        }
        
        if ($fechaHasta !== null && $fechaHasta !== '') {
            $ano2 = substr($fechaHasta, 0, 4);
            $sql .= " AND YEAR(f63_periodo) <= :anoHasta";
        }
        
        $sql .= " ORDER BY f63_periodo DESC";
        
        $stmt = $this->db->prepare($sql);
        $params = ['socio' => $clienteCodigo];
        
        if ($fechaDesde !== null && $fechaDesde !== '') {
            $ano = substr($fechaDesde, 0, 4);
            $params['anoDesde'] = $ano;
        }
        
        if ($fechaHasta !== null && $fechaHasta !== '') {
            $ano2 = substr($fechaHasta, 0, 4);
            $params['anoHasta'] = $ano2;
        }
        
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Obtener una multa específica
     */
    public function obtenerMulta(int $codigo): ?array
    {
        $sql = "SELECT * FROM f63_multa WHERE f63_codigo = :codigo";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['codigo' => $codigo]);
        $resultado = $stmt->fetch();
        return $resultado ?: null;
    }

    /**
     * Registrar multa
     */
    public function registrarMulta(array $datos, string $usuario = 'SISTEMA'): array
    {
        $this->log('Iniciando registro de multa', $datos);

        try {
            $this->db->beginTransaction();

            $sql = "INSERT INTO f63_multa (f63_socio, f63_periodo, f63_multap, f63_multad, f63_multar, f63_fechaPago, f63_formaPago)
                    VALUES (:socio, :periodo, :multap, :multad, :multar, :fechaPago, :formaPago)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'socio' => $datos['socio'],
                'periodo' => $datos['periodo'],
                'multap' => $datos['multap'] ?? 0,
                'multad' => $datos['multad'] ?? 0,
                'multar' => $datos['multar'] ?? 0,
                'fechaPago' => $datos['fechaPago'] ?? null,
                'formaPago' => $datos['formaPago'] ?? null,
            ]);

            $this->db->commit();
            $this->log('Multa registrada correctamente');
            return ['success' => true, 'message' => 'Multa registrada correctamente'];

        } catch (Exception $e) {
            $this->db->rollBack();
            $this->log('Error al registrar multa', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Error al registrar multa: ' . $e->getMessage()];
        }
    }

    /**
     * Actualizar multa
     */
    public function actualizarMulta(int $codigo, array $datos, string $usuario = 'SISTEMA'): array
    {
        $this->log('Iniciando actualización de multa', ['codigo' => $codigo, 'datos' => $datos]);

        try {
            $this->db->beginTransaction();

            $sql = "UPDATE f63_multa SET f63_multap = :multap, f63_multad = :multad, f63_multar = :multar, f63_fechaPago = :fechaPago, f63_periodo = :periodo
                    WHERE f63_codigo = :codigo";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'multap' => $datos['multap'] ?? 0,
                'multad' => $datos['multad'] ?? 0,
                'multar' => $datos['multar'] ?? 0,
                'fechaPago' => $datos['fechaPago'] ?? null,
                'periodo' => $datos['periodo'],
                'codigo' => $codigo,
            ]);

            $this->db->commit();
            $this->log('Multa actualizada correctamente');
            return ['success' => true, 'message' => 'Multa actualizada correctamente'];

        } catch (Exception $e) {
            $this->db->rollBack();
            $this->log('Error al actualizar multa', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Error al actualizar multa: ' . $e->getMessage()];
        }
    }

    /**
     * Eliminar multa
     */
    public function eliminarMulta(int $codigo, string $usuario = 'SISTEMA'): array
    {
        $this->log('Iniciando eliminación de multa', ['codigo' => $codigo]);

        try {
            $this->db->beginTransaction();

            $sql = "DELETE FROM f63_multa WHERE f63_codigo = :codigo";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['codigo' => $codigo]);

            $this->db->commit();
            $this->log('Multa eliminada correctamente');
            return ['success' => true, 'message' => 'Multa eliminada correctamente'];

        } catch (Exception $e) {
            $this->db->rollBack();
            $this->log('Error al eliminar multa', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Error al eliminar multa: ' . $e->getMessage()];
        }
    }

    /**
     * Obtener multa pendiente de socio
     */
    public function obtenerMultaPendiente(string $clienteCodigo): ?array
    {
        $sql = "SELECT * FROM f63_multa WHERE f63_socio = :socio ORDER BY f63_codigo DESC LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['socio' => $clienteCodigo]);
        $resultado = $stmt->fetch();
        return $resultado ?: null;
    }

    /**
     * Obtener cheques con paginación
     */
    public function obtenerCheques(?string $fechaDesde = null, ?string $fechaHasta = null, int $pagina = 1, int $porPagina = 10): array
    {
        $offset = ($pagina - 1) * $porPagina;
        
        $sql = "SELECT * FROM f23_cheque";
        
        if ($fechaDesde !== null && $fechaDesde !== '' && $fechaHasta !== null && $fechaHasta !== '') {
            $sql .= " WHERE f23_fecha >= :fechaDesde AND f23_fecha <= :fechaHasta";
        }
        
        $sql .= " ORDER BY f23_fecha DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        
        if ($fechaDesde !== null && $fechaDesde !== '' && $fechaHasta !== null && $fechaHasta !== '') {
            $stmt->execute(['fechaDesde' => $fechaDesde, 'fechaHasta' => $fechaHasta, 'limit' => $porPagina, 'offset' => $offset]);
        } else {
            $stmt->execute(['limit' => $porPagina, 'offset' => $offset]);
        }
        
        return $stmt->fetchAll();
    }

    /**
     * Contar total de cheques
     */
    public function contarCheques(?string $fechaDesde = null, ?string $fechaHasta = null): int
    {
        $sql = "SELECT COUNT(*) as total FROM f23_cheque";
        
        if ($fechaDesde !== null && $fechaDesde !== '' && $fechaHasta !== null && $fechaHasta !== '') {
            $sql .= " WHERE f23_fecha >= :fechaDesde AND f23_fecha <= :fechaHasta";
        }
        
        $stmt = $this->db->prepare($sql);
        
        if ($fechaDesde !== null && $fechaDesde !== '' && $fechaHasta !== null && $fechaHasta !== '') {
            $stmt->execute(['fechaDesde' => $fechaDesde, 'fechaHasta' => $fechaHasta]);
        } else {
            $stmt->execute();
        }
        
        $resultado = $stmt->fetch();
        return (int) ($resultado['total'] ?? 0);
    }

    /**
     * Obtener un cheque específico
     */
    public function obtenerCheque(int $codigo): ?array
    {
        $sql = "SELECT * FROM f23_cheque WHERE f23_codigo = :codigo";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['codigo' => $codigo]);
        $resultado = $stmt->fetch();
        return $resultado ?: null;
    }

    /**
     * Registrar cheque
     */
    public function registrarCheque(array $datos, string $usuario = 'SISTEMA'): array
    {
        $this->log('Iniciando registro de cheque', $datos);

        try {
            $this->db->beginTransaction();

            // Obtener el siguiente código
            $sqlMax = "SELECT MAX(f23_codigo) as max_codigo FROM f23_cheque";
            $stmtMax = $this->db->prepare($sqlMax);
            $stmtMax->execute();
            $maxResult = $stmtMax->fetch();
            $nuevoCodigo = ($maxResult['max_codigo'] ?? 0) + 1;

            $sql = "INSERT INTO f23_cheque (f23_codigo, f23_nombre, f23_monto, f23_montoLetras, f23_fecha, f23_cheque, f23_motivo, f23_centro, f23_condicion)
                    VALUES (:codigo, :nombre, :monto, :montoLetras, :fecha, :cheque, :motivo, :centro, :condicion)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'codigo' => $nuevoCodigo,
                'nombre' => $datos['nombre'],
                'monto' => $datos['monto'],
                'montoLetras' => $datos['montoLetras'],
                'fecha' => $datos['fecha'] ?? date('Y-m-d'),
                'cheque' => $datos['cheque'] ?? null,
                'motivo' => $datos['motivo'] ?? null,
                'centro' => $datos['centro'] ?? null,
                'condicion' => $datos['condicion'] ?? null,
            ]);

            $this->db->commit();
            $this->log('Cheque registrado correctamente');
            return ['success' => true, 'message' => 'Cheque registrado correctamente', 'codigo' => $nuevoCodigo];

        } catch (Exception $e) {
            $this->db->rollBack();
            $this->log('Error al registrar cheque', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Error al registrar cheque: ' . $e->getMessage()];
        }
    }

    /**
     * Actualizar cheque
     */
    public function actualizarCheque(int $codigo, array $datos, string $usuario = 'SISTEMA'): array
    {
        $this->log('Iniciando actualización de cheque', ['codigo' => $codigo, 'datos' => $datos]);

        try {
            $this->db->beginTransaction();

            $sql = "UPDATE f23_cheque SET f23_nombre = :nombre, f23_monto = :monto, f23_montoLetras = :montoLetras, f23_fecha = :fecha, f23_cheque = :cheque, f23_motivo = :motivo, f23_centro = :centro, f23_condicion = :condicion
                    WHERE f23_codigo = :codigo";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'nombre' => $datos['nombre'],
                'monto' => $datos['monto'],
                'montoLetras' => $datos['montoLetras'],
                'fecha' => $datos['fecha'] ?? null,
                'cheque' => $datos['cheque'] ?? null,
                'motivo' => $datos['motivo'] ?? null,
                'centro' => $datos['centro'] ?? null,
                'condicion' => $datos['condicion'] ?? null,
                'codigo' => $codigo,
            ]);

            $this->db->commit();
            $this->log('Cheque actualizado correctamente');
            return ['success' => true, 'message' => 'Cheque actualizado correctamente'];

        } catch (Exception $e) {
            $this->db->rollBack();
            $this->log('Error al actualizar cheque', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Error al actualizar cheque: ' . $e->getMessage()];
        }
    }

    /**
     * Eliminar cheque
     */
    public function eliminarCheque(int $codigo, string $usuario = 'SISTEMA'): array
    {
        $this->log('Iniciando eliminación de cheque', ['codigo' => $codigo]);

        try {
            $this->db->beginTransaction();

            $sql = "DELETE FROM f23_cheque WHERE f23_codigo = :codigo";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['codigo' => $codigo]);

            $this->db->commit();
            $this->log('Cheque eliminado correctamente');
            return ['success' => true, 'message' => 'Cheque eliminado correctamente'];

        } catch (Exception $e) {
            $this->db->rollBack();
            $this->log('Error al eliminar cheque', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Error al eliminar cheque: ' . $e->getMessage()];
        }
    }

    /**
     * Obtener observación actual del socio
     */
    public function obtenerObservacion(string $codigoPersona): array
    {
        $sql = "SELECT 
                    F01_OBS2 as observacion,
                    F01_TITULO as titulo
                FROM f01_persona
                WHERE F01_ID_PERSONA = :codigo_persona";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['codigo_persona' => $codigoPersona]);
        
        $resultado = $stmt->fetch();
        
        return $resultado ?: ['observacion' => '', 'titulo' => ''];
    }

    /**
     * Obtener historial de observaciones del socio
     */
    public function obtenerHistorialObservaciones(string $codigoPersona, int $pagina = 1, int $porPagina = 20): array
    {
        $offset = ($pagina - 1) * $porPagina;
        
        $sql = "SELECT 
                    f28_titulo as titulo,
                    f28_detalle as detalle,
                    f28_usuario as usuario,
                    f28_fecha as fecha
                FROM f28_obs_detalle
                WHERE f28_socio = :codigo_persona
                ORDER BY f28_fecha DESC
                LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':codigo_persona', $codigoPersona, \PDO::PARAM_STR);
        $stmt->bindValue(':limit', $porPagina, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Contar historial de observaciones
     */
    public function contarHistorialObservaciones(string $codigoPersona): int
    {
        $sql = "SELECT COUNT(*) as total 
                FROM f28_obs_detalle
                WHERE f28_socio = :codigo_persona";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['codigo_persona' => $codigoPersona]);
        
        $resultado = $stmt->fetch();
        
        return (int) ($resultado['total'] ?? 0);
    }

    /**
     * Actualizar observación del socio
     */
    public function actualizarObservacion(string $codigoPersona, string $titulo, string $observacion, string $usuario): array
    {
        try {
            $sql = "UPDATE f01_persona 
                    SET F01_OBS2 = :observacion,
                        F01_TITULO = :titulo,
                        F01_USR_ACT = :usuario,
                        F01_FE_USR_ACT = NOW()
                    WHERE F01_ID_PERSONA = :codigo_persona";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'observacion' => $observacion,
                'titulo' => $titulo,
                'usuario' => $usuario,
                'codigo_persona' => $codigoPersona
            ]);
            
            $this->log('Observación actualizada', ['codigo_persona' => $codigoPersona, 'usuario' => $usuario]);
            return ['success' => true, 'message' => 'Observación actualizada correctamente'];
            
        } catch (Exception $e) {
            $this->log('Error al actualizar observación', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Error al actualizar observación: ' . $e->getMessage()];
        }
    }

    /**
     * Registrar observación en historial
     */
    public function registrarObservacionHistorial(string $codigoPersona, string $titulo, string $observacion, string $usuario): array
    {
        try {
            $sql = "INSERT INTO f28_obs_detalle (
                        f28_socio,
                        f28_titulo,
                        f28_detalle,
                        f28_usuario,
                        f28_fecha
                    ) VALUES (
                        :socio,
                        :titulo,
                        :detalle,
                        :usuario,
                        NOW()
                    )";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'socio' => $codigoPersona,
                'titulo' => $titulo,
                'detalle' => $observacion,
                'usuario' => $usuario
            ]);
            
            $this->log('Observación registrada en historial', ['codigo_persona' => $codigoPersona, 'usuario' => $usuario]);
            return ['success' => true, 'message' => 'Observación registrada en historial'];
            
        } catch (Exception $e) {
            $this->log('Error al registrar observación en historial', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Error al registrar observación en historial: ' . $e->getMessage()];
        }
    }

    /**
     * Obtener carta actual del socio
     */
    public function obtenerCarta(string $codigoPersona): array
    {
        $sql = "SELECT 
                    F01_FIRMO as firmo,
                    F01_CARTA as carta,
                    F01_FECHA_BCO as fecha_banco,
                    F01_OBS_CARTA as obs_carta
                FROM f01_persona
                WHERE F01_ID_PERSONA = :codigo_persona";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['codigo_persona' => $codigoPersona]);
        
        $resultado = $stmt->fetch();
        
        return $resultado ?: ['firmo' => '', 'carta' => '', 'fecha_banco' => '', 'obs_carta' => ''];
    }

    /**
     * Actualizar carta del socio
     */
    public function actualizarCarta(string $codigoPersona, string $firmo, string $carta, ?string $fechaBanco, string $obsCarta, string $usuario): array
    {
        try {
            $sql = "UPDATE f01_persona 
                    SET F01_FIRMO = :firmo,
                        F01_CARTA = :carta,
                        F01_FECHA_BCO = :fecha_banco,
                        F01_OBS_CARTA = :obs_carta,
                        F01_USR_ACT = :usuario,
                        F01_FE_USR_ACT = NOW()
                    WHERE F01_ID_PERSONA = :codigo_persona";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'firmo' => $firmo,
                'carta' => $carta,
                'fecha_banco' => $fechaBanco ?: null,
                'obs_carta' => $obsCarta,
                'usuario' => $usuario,
                'codigo_persona' => $codigoPersona
            ]);
            
            $this->log('Carta actualizada', ['codigo_persona' => $codigoPersona, 'usuario' => $usuario]);
            return ['success' => true, 'message' => 'Carta actualizada correctamente'];
            
        } catch (Exception $e) {
            $this->log('Error al actualizar carta', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Error al actualizar carta: ' . $e->getMessage()];
        }
    }

    /**
     * Obtener sobregiro FONBES del socio
     */
    public function obtenerSobregiroFonbes(string $codigoPersona): array
    {
        $sql = "SELECT 
                    f22_socio as socio,
                    f22_periodo as periodo,
                    f22_aportacionP as aportacion_pendiente,
                    f22_aportacionD as aportacion_pagada,
                    (f22_aportacion - f22_aportacionD) as sobregiro,
                    f22_fechaPago as fecha_pago,
                    f22_aportacion as aportacion
                FROM f22_aportes
                WHERE (f22_aportacion - f22_aportacionD) > 0 
                AND f22_socio = :codigo_persona
                ORDER BY f22_periodo DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['codigo_persona' => $codigoPersona]);
        
        return $stmt->fetchAll();
    }

    /**
     * Obtener sobregiro COOPAM del socio
     */
    public function obtenerSobregiroCoopam(string $codigoPersona): array
    {
        $sql = "SELECT 
                    f27_socio as socio,
                    f27_periodo as periodo,
                    f27_aportacionP as aportacion_pendiente,
                    f27_aportacionD as aportacion_pagada,
                    (f27_aportacion - f27_aportacionD) as sobregiro,
                    f27_fechaPago as fecha_pago,
                    f27_aportacion as aportacion
                FROM f27_aportes_cope
                WHERE (f27_aportacion - f27_aportacionD) > 0 
                AND f27_socio = :codigo_persona
                ORDER BY f27_periodo DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['codigo_persona' => $codigoPersona]);
        
        return $stmt->fetchAll();
    }

    /**
     * Obtener total sobregiro FONBES del socio
     */
    public function obtenerTotalSobregiroFonbes(string $codigoPersona): float
    {
        $sql = "SELECT SUM(f22_aportacion - f22_aportacionD) as total
                FROM f22_aportes
                WHERE (f22_aportacion - f22_aportacionD) > 0 
                AND f22_socio = :codigo_persona";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['codigo_persona' => $codigoPersona]);
        
        $result = $stmt->fetch();
        
        return (float) ($result['total'] ?? 0);
    }

    /**
     * Obtener total sobregiro COOPAM del socio
     */
    public function obtenerTotalSobregiroCoopam(string $codigoPersona): float
    {
        $sql = "SELECT SUM(f27_aportacion - f27_aportacionD) as total
                FROM f27_aportes_cope
                WHERE (f27_aportacion - f27_aportacionD) > 0 
                AND f27_socio = :codigo_persona";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['codigo_persona' => $codigoPersona]);
        
        $result = $stmt->fetch();
        
        return (float) ($result['total'] ?? 0);
    }

    /**
     * Validar aportes de un periodo
     */
    public function validarAportes(string $codigoPersona, string $periodo): array
    {
        $sql = "SELECT 
                    f22_aportacion as aportacion,
                    f22_aportacionD as aportacion_pagada,
                    (f22_aportacion - f22_aportacionD) as sobregiro
                FROM f22_aportes
                WHERE f22_socio = :codigo_persona
                AND f22_periodo = :periodo";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'codigo_persona' => $codigoPersona,
            'periodo' => $periodo
        ]);
        
        $result = $stmt->fetch();
        
        return $result ?: [];
    }

    /**
     * Obtener fecha último aporte FONBES
     */
    public function obtenerFechaUltimoAporteFonbes(string $codigoPersona): ?string
    {
        $sql = "SELECT MAX(f22_fechaPago) as fecha_ultimo
                FROM f22_aportes
                WHERE f22_socio = :codigo_persona
                AND f22_fechaPago IS NOT NULL";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['codigo_persona' => $codigoPersona]);
        
        $result = $stmt->fetch();
        
        return $result['fecha_ultimo'] ?? null;
    }

    /**
     * Obtener total aportado FONBES
     */
    public function obtenerTotalAportadoFonbes(string $codigoPersona): float
    {
        $sql = "SELECT SUM(f22_aportacion) as total
                FROM f22_aportes
                WHERE f22_socio = :codigo_persona";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['codigo_persona' => $codigoPersona]);
        
        $result = $stmt->fetch();
        
        return (float) ($result['total'] ?? 0);
    }

    /**
     * Obtener total aportado COOPAM
     */
    public function obtenerTotalAportadoCoopam(string $codigoPersona): float
    {
        $sql = "SELECT SUM(f27_aportacion) as total
                FROM f27_aportes_cope
                WHERE f27_socio = :codigo_persona";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['codigo_persona' => $codigoPersona]);
        
        $result = $stmt->fetch();
        
        return (float) ($result['total'] ?? 0);
    }

    /**
     * Obtener aportaciones completas del socio
     */
    public function obtenerAportacionesCompletas(string $codigoPersona, int $pagina = 1, int $porPagina = 20): array
    {
        $offset = ($pagina - 1) * $porPagina;
        
        // Primero obtenemos los aportes FONBES
        $sqlFonbes = "SELECT 
                        'FONBES' as tipo,
                        f22_periodo as periodo,
                        f22_aportacion as aportacion,
                        f22_aportacionD as aportacion_pagada,
                        (f22_aportacion - f22_aportacionD) as sobregiro,
                        f22_fechaPago as fecha_pago
                      FROM f22_aportes
                      WHERE f22_socio = :codigo_persona
                      ORDER BY f22_periodo DESC
                      LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sqlFonbes);
        $stmt->bindValue(':codigo_persona', $codigoPersona, \PDO::PARAM_STR);
        $stmt->bindValue(':limit', $porPagina, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        
        $aportaciones = $stmt->fetchAll();
        
        return $aportaciones;
    }

    /**
     * Contar aportaciones completas
     */
    public function contarAportacionesCompletas(string $codigoPersona): int
    {
        $sql = "SELECT COUNT(*) as total
                FROM f22_aportes
                WHERE f22_socio = :codigo_persona";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['codigo_persona' => $codigoPersona]);
        
        $result = $stmt->fetch();
        
        return (int) ($result['total'] ?? 0);
    }

    /**
     * Logging
     */
    private function log(string $mensaje, array $contexto = []): void
    {
        $fecha = date('Y-m-d H:i:s');
        $linea = "[{$fecha}] {$mensaje}";
        if (!empty($contexto)) {
            $linea .= " | Contexto: " . json_encode($contexto);
        }
        $linea .= PHP_EOL;
        file_put_contents($this->logFile, $linea, FILE_APPEND);
    }
}
