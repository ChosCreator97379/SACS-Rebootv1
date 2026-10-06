<?php

declare(strict_types=1);

namespace Sacs\Services;

use Sacs\Core\Database;

class ExportacionServicio
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    /**
     * Generar CSV con headers UTF-8 BOM
     */
    private function generarCSV(array $datos, array $headers): string
    {
        $output = "\xEF\xBB\xBF"; // UTF-8 BOM para compatibilidad con Excel
        
        // Headers
        $output .= implode(';', array_map([$this, 'escaparCSV'], $headers)) . "\n";
        
        // Datos
        foreach ($datos as $fila) {
            $output .= implode(';', array_map([$this, 'escaparCSV'], $fila)) . "\n";
        }
        
        return $output;
    }

    /**
     * Escapar valor para CSV
     */
    private function escaparCSV($valor): string
    {
        $valor = (string) ($valor ?? '');
        // Si contiene comillas, punto y coma o salto de línea, encerrar en comillas y escapar
        if (str_contains($valor, '"') || str_contains($valor, ';') || str_contains($valor, "\n")) {
            $valor = '"' . str_replace('"', '""', $valor) . '"';
        }
        return $valor;
    }

    /**
     * Obtener datos de socios con filtros
     */
    private function obtenerSocios(array $filtros): array
    {
        $sql = "SELECT 
                    c.F02_ID_CLIENTE as codigo,
                    CONCAT(p.F01_NOMBRE, ' ', p.F01_APELLIDO_PAT, ' ', p.F01_APELLIDO_MAT) as nombre,
                    p.F01_NUM_DOC as documento,
                    p.F01_CENTRO as centro,
                    p.F01_CONDICION as condicion,
                    p.F01_STDO_PERSN as estado
                FROM f02_cliente c
                INNER JOIN f01_persona p ON c.F02_ID_PERSONA = p.F01_ID_PERSONA
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filtros['nombre'])) {
            $sql .= " AND CONCAT(p.F01_NOMBRE, ' ', p.F01_APELLIDO_PAT, ' ', p.F01_APELLIDO_MAT) LIKE :nombre";
            $params['nombre'] = '%' . $filtros['nombre'] . '%';
        }
        
        if (!empty($filtros['documento'])) {
            $sql .= " AND p.F01_NUM_DOC LIKE :documento";
            $params['documento'] = '%' . $filtros['documento'] . '%';
        }
        
        if (!empty($filtros['centro'])) {
            $sql .= " AND p.F01_CENTRO = :centro";
            $params['centro'] = $filtros['centro'];
        }
        
        if (!empty($filtros['condicion'])) {
            $sql .= " AND p.F01_CONDICION = :condicion";
            $params['condicion'] = $filtros['condicion'];
        }
        
        if (!empty($filtros['estado'])) {
            $sql .= " AND p.F01_STDO_PERSN = :estado";
            $params['estado'] = $filtros['estado'];
        }
        
        $sql .= " ORDER BY c.F02_ID_CLIENTE ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }

    /**
     * Obtener datos de familiares con filtros
     */
    private function obtenerFamiliares(array $filtros): array
    {
        $clienteCodigo = $filtros['cliente_codigo'] ?? '';
        $inactivos = $filtros['inactivos'] ?? false;
        
        $sql = "SELECT 
                    f.F12_ID_CLIENTE as codigo_cliente,
                    p.F01_ID_PERSONA as codigo_persona,
                    CONCAT(p.F01_NOMB_PRI, ' ', p.F01_APE_PAT, ' ', p.F01_APE_MAT) as nombre,
                    p.F01_NUM_DOC as documento,
                    f.F12_ID_TPO_PARENT as parentesco,
                    p.F01_STDO_PERSN as estado
                FROM f12_familiar f
                INNER JOIN f01_persona p ON f.F12_ID_PERSONA = p.F01_ID_PERSONA
                WHERE f.F12_ID_CLIENTE = :cliente_codigo";
        
        $params = ['cliente_codigo' => $clienteCodigo];
        
        if (!$inactivos) {
            $sql .= " AND p.F01_STDO_PERSN = 1";
        }
        
        $sql .= " ORDER BY p.F01_NOMB_PRI ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }

    /**
     * Obtener datos de aportes FONBES con filtros
     */
    private function obtenerAportesFonbes(array $filtros): array
    {
        $clienteCodigo = $filtros['cliente_codigo'] ?? '';
        
        $sql = "SELECT 
                    f22_socio as codigo_socio,
                    f22_periodo as periodo,
                    f22_aportacionP as aportacion_pendiente,
                    f22_aportacionD as aportacion_pagada,
                    f22_diferencia as diferencia
                FROM f22_aportes
                WHERE f22_socio = :cliente_codigo
                ORDER BY f22_periodo DESC";
        
        $params = ['cliente_codigo' => $clienteCodigo];
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }

    /**
     * Obtener datos de aportes COOPAM con filtros
     */
    private function obtenerAportesCoopam(array $filtros): array
    {
        $clienteCodigo = $filtros['cliente_codigo'] ?? '';
        
        $sql = "SELECT 
                    f27_socio as codigo_socio,
                    f27_periodo as periodo,
                    f27_aportacionP as aportacion_pendiente,
                    f27_aportacionD as aportacion_pagada,
                    f27_diferencia as diferencia
                FROM f27_aportes_cope
                WHERE f27_socio = :cliente_codigo
                ORDER BY f27_periodo DESC";
        
        $params = ['cliente_codigo' => $clienteCodigo];
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }

    /**
     * Obtener datos de beneficios con filtros
     */
    private function obtenerBeneficios(array $filtros): array
    {
        $clienteCodigo = $filtros['cliente_codigo'] ?? '';
        $fechaDesde = $filtros['fecha_desde'] ?? '';
        $fechaHasta = $filtros['fecha_hasta'] ?? '';
        
        $sql = "SELECT 
                    f21_codigo as codigo,
                    f21_socio as codigo_socio,
                    f21_tipo as tipo,
                    f21_importe as importe,
                    f21_parentesco as parentesco,
                    f21_fecha as fecha,
                    f21_estado as estado
                FROM f21_beneficios
                WHERE f21_socio = :cliente_codigo";
        
        $params = ['cliente_codigo' => $clienteCodigo];
        
        if (!empty($fechaDesde) && !empty($fechaHasta)) {
            $sql .= " AND f21_fecha >= :fecha_desde AND f21_fecha <= :fecha_hasta";
            $params['fecha_desde'] = $fechaDesde;
            $params['fecha_hasta'] = $fechaHasta;
        }
        
        $sql .= " ORDER BY f21_fecha DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }

    /**
     * Obtener datos de rifas con filtros
     */
    private function obtenerRifas(array $filtros): array
    {
        $clienteCodigo = $filtros['cliente_codigo'] ?? '';
        $fechaDesde = $filtros['fecha_desde'] ?? '';
        $fechaHasta = $filtros['fecha_hasta'] ?? '';
        
        $sql = "SELECT 
                    f64_codigo as codigo,
                    f64_socio as codigo_socio,
                    f64_periodo as periodo,
                    f64_rifap as rifa_propuesta,
                    f64_rifad as rifa_definitiva,
                    f64_rifar as rifa_sobregiro,
                    f64_fechaPago as fecha_pago,
                    f64_formaPago as forma_pago
                FROM f64_rifa
                WHERE f64_socio = :cliente_codigo";
        
        $params = ['cliente_codigo' => $clienteCodigo];
        
        if (!empty($fechaDesde) && !empty($fechaHasta)) {
            $sql .= " AND YEAR(f64_periodo) >= YEAR(:fecha_desde) AND YEAR(f64_periodo) <= YEAR(:fecha_hasta)";
            $params['fecha_desde'] = $fechaDesde;
            $params['fecha_hasta'] = $fechaHasta;
        }
        
        $sql .= " ORDER BY f64_periodo DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }

    /**
     * Obtener datos de multas con filtros
     */
    private function obtenerMultas(array $filtros): array
    {
        $clienteCodigo = $filtros['cliente_codigo'] ?? '';
        $fechaDesde = $filtros['fecha_desde'] ?? '';
        $fechaHasta = $filtros['fecha_hasta'] ?? '';
        
        $sql = "SELECT 
                    f63_codigo as codigo,
                    f63_socio as codigo_socio,
                    f63_periodo as periodo,
                    f63_multap as multa_propuesta,
                    f63_multad as multa_definitiva,
                    f63_multar as multa_sobregiro,
                    f63_fechaPago as fecha_pago,
                    f63_formaPago as forma_pago
                FROM f63_multa
                WHERE f63_socio = :cliente_codigo";
        
        $params = ['cliente_codigo' => $clienteCodigo];
        
        if (!empty($fechaDesde) && !empty($fechaHasta)) {
            $sql .= " AND YEAR(f63_periodo) >= YEAR(:fecha_desde) AND YEAR(f63_periodo) <= YEAR(:fecha_hasta)";
            $params['fecha_desde'] = $fechaDesde;
            $params['fecha_hasta'] = $fechaHasta;
        }
        
        $sql .= " ORDER BY f63_periodo DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }

    /**
     * Obtener datos de cheques con filtros
     */
    private function obtenerCheques(array $filtros): array
    {
        $fechaDesde = $filtros['fecha_desde'] ?? '';
        $fechaHasta = $filtros['fecha_hasta'] ?? '';
        
        $sql = "SELECT 
                    f23_codigo as codigo,
                    f23_nombre as nombre,
                    f23_monto as monto,
                    f23_montoLetras as monto_letras,
                    f23_fecha as fecha,
                    f23_cheque as numero_cheque,
                    f23_motivo as motivo,
                    f23_centro as centro,
                    f23_condicion as condicion
                FROM f23_cheque
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($fechaDesde) && !empty($fechaHasta)) {
            $sql .= " AND f23_fecha >= :fecha_desde AND f23_fecha <= :fecha_hasta";
            $params['fecha_desde'] = $fechaDesde;
            $params['fecha_hasta'] = $fechaHasta;
        }
        
        $sql .= " ORDER BY f23_fecha DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }

    // ==================== EXPORTACIÓN CSV ====================

    public function exportarSociosCSV(array $filtros): string
    {
        $datos = $this->obtenerSocios($filtros);
        $headers = ['Código', 'Nombre', 'Documento', 'Centro', 'Condición', 'Estado'];
        return $this->generarCSV($datos, $headers);
    }

    public function exportarFamiliaresCSV(array $filtros): string
    {
        $datos = $this->obtenerFamiliares($filtros);
        $headers = ['Código Cliente', 'Código Persona', 'Nombre', 'Documento', 'Parentesco', 'Estado'];
        return $this->generarCSV($datos, $headers);
    }

    public function exportarAportesFonbesCSV(array $filtros): string
    {
        $datos = $this->obtenerAportesFonbes($filtros);
        $headers = ['Código Socio', 'Periodo', 'Aportación Pendiente', 'Aportación Pagada', 'Diferencia'];
        return $this->generarCSV($datos, $headers);
    }

    public function exportarAportesCoopamCSV(array $filtros): string
    {
        $datos = $this->obtenerAportesCoopam($filtros);
        $headers = ['Código Socio', 'Periodo', 'Aportación Pendiente', 'Aportación Pagada', 'Diferencia'];
        return $this->generarCSV($datos, $headers);
    }

    public function exportarBeneficiosCSV(array $filtros): string
    {
        $datos = $this->obtenerBeneficios($filtros);
        $headers = ['Código', 'Código Socio', 'Tipo', 'Importe', 'Parentesco', 'Fecha', 'Estado'];
        return $this->generarCSV($datos, $headers);
    }

    public function exportarRifasCSV(array $filtros): string
    {
        $datos = $this->obtenerRifas($filtros);
        $headers = ['Código', 'Código Socio', 'Periodo', 'Rifa Propuesta', 'Rifa Definitiva', 'Rifa Sobregiro', 'Fecha Pago', 'Forma Pago'];
        return $this->generarCSV($datos, $headers);
    }

    public function exportarMultasCSV(array $filtros): string
    {
        $datos = $this->obtenerMultas($filtros);
        $headers = ['Código', 'Código Socio', 'Periodo', 'Multa Propuesta', 'Multa Definitiva', 'Multa Sobregiro', 'Fecha Pago', 'Forma Pago'];
        return $this->generarCSV($datos, $headers);
    }

    public function exportarChequesCSV(array $filtros): string
    {
        $datos = $this->obtenerCheques($filtros);
        $headers = ['Código', 'Nombre', 'Monto', 'Monto en Letras', 'Fecha', 'Número Cheque', 'Motivo', 'Centro', 'Condición'];
        return $this->generarCSV($datos, $headers);
    }

    // ==================== EXPORTACIÓN EXCEL ====================

    private function generarExcel(array $datos, array $headers, string $titulo): string
    {
        if (!class_exists('PhpOffice\PhpSpreadsheet\Spreadsheet')) {
            throw new \Exception('La librería PhpSpreadsheet no está instalada. Ejecute: composer install');
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $col = 1;
        foreach ($headers as $header) {
            $sheet->setCellValueByColumnAndRow($col, 1, $header);
            $sheet->getStyleByColumnAndRow($col, 1)->getFont()->setBold(true);
            $sheet->getStyleByColumnAndRow($col, 1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFE0E0E0');
            $col++;
        }

        // Datos
        $row = 2;
        foreach ($datos as $fila) {
            $col = 1;
            foreach ($fila as $valor) {
                $sheet->setCellValueByColumnAndRow($col, $row, $valor);
                $col++;
            }
            $row++;
        }

        // Auto-width columns
        foreach (range(1, count($headers)) as $col) {
            $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        return ob_get_clean();
    }

    public function exportarSociosExcel(array $filtros): string
    {
        $datos = $this->obtenerSocios($filtros);
        $headers = ['Código', 'Nombre', 'Documento', 'Centro', 'Condición', 'Estado'];
        return $this->generarExcel($datos, $headers, 'Socios');
    }

    public function exportarFamiliaresExcel(array $filtros): string
    {
        $datos = $this->obtenerFamiliares($filtros);
        $headers = ['Código Cliente', 'Código Persona', 'Nombre', 'Documento', 'Parentesco', 'Estado'];
        return $this->generarExcel($datos, $headers, 'Familiares');
    }

    public function exportarAportesFonbesExcel(array $filtros): string
    {
        $datos = $this->obtenerAportesFonbes($filtros);
        $headers = ['Código Socio', 'Periodo', 'Aportación Pendiente', 'Aportación Pagada', 'Diferencia'];
        return $this->generarExcel($datos, $headers, 'Aportes FONBES');
    }

    public function exportarAportesCoopamExcel(array $filtros): string
    {
        $datos = $this->obtenerAportesCoopam($filtros);
        $headers = ['Código Socio', 'Periodo', 'Aportación Pendiente', 'Aportación Pagada', 'Diferencia'];
        return $this->generarExcel($datos, $headers, 'Aportes COOPAM');
    }

    public function exportarBeneficiosExcel(array $filtros): string
    {
        $datos = $this->obtenerBeneficios($filtros);
        $headers = ['Código', 'Código Socio', 'Tipo', 'Importe', 'Parentesco', 'Fecha', 'Estado'];
        return $this->generarExcel($datos, $headers, 'Beneficios');
    }

    public function exportarRifasExcel(array $filtros): string
    {
        $datos = $this->obtenerRifas($filtros);
        $headers = ['Código', 'Código Socio', 'Periodo', 'Rifa Propuesta', 'Rifa Definitiva', 'Rifa Sobregiro', 'Fecha Pago', 'Forma Pago'];
        return $this->generarExcel($datos, $headers, 'Rifas');
    }

    public function exportarMultasExcel(array $filtros): string
    {
        $datos = $this->obtenerMultas($filtros);
        $headers = ['Código', 'Código Socio', 'Periodo', 'Multa Propuesta', 'Multa Definitiva', 'Multa Sobregiro', 'Fecha Pago', 'Forma Pago'];
        return $this->generarExcel($datos, $headers, 'Multas');
    }

    public function exportarChequesExcel(array $filtros): string
    {
        $datos = $this->obtenerCheques($filtros);
        $headers = ['Código', 'Nombre', 'Monto', 'Monto en Letras', 'Fecha', 'Número Cheque', 'Motivo', 'Centro', 'Condición'];
        return $this->generarExcel($datos, $headers, 'Cheques');
    }

    // ==================== EXPORTACIÓN PDF ====================

    private function generarPDF(array $datos, array $headers, string $titulo): string
    {
        if (!class_exists('TCPDF')) {
            throw new \Exception('La librería TCPDF no está instalada. Ejecute: composer install');
        }

        $pdf = new \TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator('SACS Nuevo');
        $pdf->SetAuthor('SACS');
        $pdf->SetTitle($titulo);
        $pdf->SetHeaderData('', 0, $titulo, date('d/m/Y H:i'));
        $pdf->setHeaderFont(['helvetica', '', 10]);
        $pdf->setFooterData(['T' => 'Página '], ['P' => '{nb}']);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->AddPage();

        // Headers
        $colWidth = 190 / count($headers);
        $pdf->SetFont('helvetica', 'B', 10);
        foreach ($headers as $header) {
            $pdf->Cell($colWidth, 7, $header, 1, 0, 'C', 1);
        }
        $pdf->Ln();

        // Datos
        $pdf->SetFont('helvetica', '', 9);
        foreach ($datos as $fila) {
            foreach ($fila as $valor) {
                $pdf->Cell($colWidth, 6, (string) ($valor ?? ''), 1, 0, 'C', 0);
            }
            $pdf->Ln();
        }

        return $pdf->Output('', 'S');
    }

    public function exportarSociosPDF(array $filtros): string
    {
        $datos = $this->obtenerSocios($filtros);
        $headers = ['Código', 'Nombre', 'Documento', 'Centro', 'Condición', 'Estado'];
        return $this->generarPDF($datos, $headers, 'Reporte de Socios');
    }

    public function exportarFamiliaresPDF(array $filtros): string
    {
        $datos = $this->obtenerFamiliares($filtros);
        $headers = ['Código Cliente', 'Código Persona', 'Nombre', 'Documento', 'Parentesco', 'Estado'];
        return $this->generarPDF($datos, $headers, 'Reporte de Familiares');
    }

    public function exportarAportesFonbesPDF(array $filtros): string
    {
        $datos = $this->obtenerAportesFonbes($filtros);
        $headers = ['Código Socio', 'Periodo', 'Aportación Pendiente', 'Aportación Pagada', 'Diferencia'];
        return $this->generarPDF($datos, $headers, 'Reporte de Aportes FONBES');
    }

    public function exportarAportesCoopamPDF(array $filtros): string
    {
        $datos = $this->obtenerAportesCoopam($filtros);
        $headers = ['Código Socio', 'Periodo', 'Aportación Pendiente', 'Aportación Pagada', 'Diferencia'];
        return $this->generarPDF($datos, $headers, 'Reporte de Aportes COOPAM');
    }

    public function exportarBeneficiosPDF(array $filtros): string
    {
        $datos = $this->obtenerBeneficios($filtros);
        $headers = ['Código', 'Código Socio', 'Tipo', 'Importe', 'Parentesco', 'Fecha', 'Estado'];
        return $this->generarPDF($datos, $headers, 'Reporte de Beneficios');
    }

    public function exportarRifasPDF(array $filtros): string
    {
        $datos = $this->obtenerRifas($filtros);
        $headers = ['Código', 'Código Socio', 'Periodo', 'Rifa Propuesta', 'Rifa Definitiva', 'Rifa Sobregiro', 'Fecha Pago', 'Forma Pago'];
        return $this->generarPDF($datos, $headers, 'Reporte de Rifas');
    }

    public function exportarMultasPDF(array $filtros): string
    {
        $datos = $this->obtenerMultas($filtros);
        $headers = ['Código', 'Código Socio', 'Periodo', 'Multa Propuesta', 'Multa Definitiva', 'Multa Sobregiro', 'Fecha Pago', 'Forma Pago'];
        return $this->generarPDF($datos, $headers, 'Reporte de Multas');
    }

    public function exportarChequesPDF(array $filtros): string
    {
        $datos = $this->obtenerCheques($filtros);
        $headers = ['Código', 'Nombre', 'Monto', 'Monto en Letras', 'Fecha', 'Número Cheque', 'Motivo', 'Centro', 'Condición'];
        return $this->generarPDF($datos, $headers, 'Reporte de Cheques');
    }
}
