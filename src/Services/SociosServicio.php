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
    public function registrarSocio(array $datosPersona, array $datosCliente): array
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
                ':id_usua_crea' => $datosPersona['id_usua_crea'] ?? null
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
                ':id_usua_crea' => $datosCliente['id_usua_crea'] ?? null,
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
    public function actualizarSocio(int $idPersona, array $datosPersona): array
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
                ':id_usua_modi' => $datosPersona['id_usua_modi'] ?? null
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
        $sql = "SELECT p.*, c.F02_ID_CLIENTE, c.F02_STDO 
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
     * Listar socios con filtros
     */
    public function listarSocios(array $filtros = []): array
    {
        $where = ['c.F02_STDO = 1'];
        $params = [];

        if (!empty($filtros['criterio'])) {
            $where[] = "(p.F01_APE_PAT LIKE :criterio OR p.F01_APE_MAT LIKE :criterio OR p.F01_NOMB_PRI LIKE :criterio OR c.F02_ID_CLIENTE LIKE :criterio)";
            $params[':criterio'] = '%' . $filtros['criterio'] . '%';
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
                    p.F01_FECHA_FALLE
                FROM f02_cliente c
                INNER JOIN f01_persona p ON c.F02_ID_PERSONA = p.F01_ID_PERSONA
                WHERE " . implode(' AND ', $where) . "
                ORDER BY p.F01_APE_PAT, p.F01_APE_MAT, p.F01_NOMB_PRI
                LIMIT 50";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
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
