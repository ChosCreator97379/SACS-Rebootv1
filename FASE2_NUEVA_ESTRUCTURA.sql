-- ============================================================================
-- FASE 2: CREACIÓN DE NUEVA ESTRUCTURA
-- ============================================================================
-- Objetivo: Crear tablas para separar cronograma original del cronograma actual
-- ============================================================================
-- NOTA: NO EJECUTAR ESTE SCRIPT DIRECTAMENTE
-- Este script es para revisión y aprobación antes de la Fase 3
-- ============================================================================

-- ----------------------------------------------------------------------------
-- 1. Crear tabla f17_cuotasprestamo_nuevo (COPIA de la tabla actual)
-- ----------------------------------------------------------------------------
-- Esta tabla es una COPIA de f17_cuotasprestamo (tabla original).
-- La tabla original f17_cuotasprestamo se mantiene intacta para producción.
-- Esta tabla nueva se puede modificar libremente con los pagos.

CREATE TABLE f17_cuotasprestamo_nuevo LIKE f17_cuotasprestamo;

INSERT INTO f17_cuotasprestamo_nuevo
SELECT * FROM f17_cuotasprestamo;

-- ----------------------------------------------------------------------------
-- 2. Limpiar duplicados en f17_cuotasprestamo_nuevo
-- ----------------------------------------------------------------------------
-- Eliminar duplicados manteniendo la fila con f17_fecha más antigua
-- NOTA: Solo en la tabla nueva, la tabla original se mantiene intacta

DELETE t1 FROM f17_cuotasprestamo_nuevo t1
INNER JOIN f17_cuotasprestamo_nuevo t2
WHERE t1.f17_prestamo = t2.f17_prestamo
  AND t1.f17_ncuota = t2.f17_ncuota
  AND t1.f17_fecha > t2.f17_fecha;

-- ----------------------------------------------------------------------------
-- 3. Crear tabla f15_aplicaciones_cuotas (aplicaciones de pagos a cuotas)
-- ----------------------------------------------------------------------------
-- Esta tabla relaciona cada movimiento con las cuotas que cubrió y cuánto
-- se aplicó a cada una. Permite pagos que cubran múltiples cuotas.

CREATE TABLE f15_aplicaciones_cuotas (
    f17_codigo INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID de la aplicación',
    f15_codigo INT(10) UNSIGNED NOT NULL COMMENT 'ID del movimiento (FK a f15_movimiento)',
    f17_prestamo INT(10) UNSIGNED NOT NULL COMMENT 'ID del préstamo (FK a f16_prestamo)',
    f17_ncuota INT(10) UNSIGNED NOT NULL COMMENT 'Número de cuota',
    f17_capital_aplicado FLOAT NOT NULL DEFAULT 0 COMMENT 'Capital aplicado a esta cuota',
    f17_interes_aplicado FLOAT NOT NULL DEFAULT 0 COMMENT 'Interés aplicado a esta cuota',
    f17_mora_aplicado FLOAT NOT NULL DEFAULT 0 COMMENT 'Mora aplicada a esta cuota',
    f17_gastos_aplicado FLOAT NOT NULL DEFAULT 0 COMMENT 'Gastos aplicados a esta cuota',
    f17_total_aplicado FLOAT NOT NULL DEFAULT 0 COMMENT 'Total aplicado a esta cuota',
    f17_fechaCrea DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creación',
    f17_usuaCrea VARCHAR(50) DEFAULT 'SISTEMA' COMMENT 'Usuario que creó',
    PRIMARY KEY (f17_codigo),
    INDEX idx_movimiento (f15_codigo),
    INDEX idx_prestamo_cuota (f17_prestamo, f17_ncuota),
    CONSTRAINT fk_aplicaciones_movimiento FOREIGN KEY (f15_codigo) REFERENCES f15_movimiento(f15_codigo) ON DELETE CASCADE,
    CONSTRAINT fk_aplicaciones_prestamo FOREIGN KEY (f17_prestamo) REFERENCES f16_prestamo(f16_codigo) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Aplicaciones de movimientos a cuotas individuales';

-- ----------------------------------------------------------------------------
-- 4. Vista para consultar saldo pendiente de cuotas
-- ----------------------------------------------------------------------------
-- Esta vista calcula automáticamente el saldo pendiente de cada cuota
-- basándose en la tabla original menos las aplicaciones registradas.

CREATE OR REPLACE VIEW v_cuotas_pendientes AS
SELECT
    o.f17_prestamo,
    o.f17_ncuota,
    o.f17_fecha,
    o.f17_capital AS capital_original,
    o.f17_interes AS interes_original,
    o.f17_total AS total_original,
    COALESCE(SUM(a.f17_total_aplicado), 0) AS total_aplicado,
    (o.f17_total - COALESCE(SUM(a.f17_total_aplicado), 0)) AS saldo_pendiente,
    CASE
        WHEN (o.f17_total - COALESCE(SUM(a.f17_total_aplicado), 0)) <= 0.01 THEN 'PAGADO'
        WHEN (o.f17_total - COALESCE(SUM(a.f17_total_aplicado), 0)) < o.f17_total THEN 'PARCIAL'
        ELSE 'VIGENTE'
    END AS estado_calculado
FROM f17_cuotasprestamo o
LEFT JOIN f15_aplicaciones_cuotas a ON o.f17_prestamo = a.f17_prestamo AND o.f17_ncuota = a.f17_ncuota
GROUP BY o.f17_prestamo, o.f17_ncuota, o.f17_fecha, o.f17_capital, o.f17_interes, o.f17_total;

-- ----------------------------------------------------------------------------
-- 5. Vista para consultar resumen de préstamo con cuotas
-- ----------------------------------------------------------------------------
-- Esta vista reemplaza las subqueries en PrestamosServicio::obtenerResumenPrestamo()

CREATE OR REPLACE VIEW v_resumen_prestamo_cuotas AS
SELECT
    f16_codigo,
    (SELECT COUNT(*) FROM v_cuotas_pendientes WHERE f17_prestamo = f16_codigo AND estado_calculado = 'PAGADO') AS cuotas_pagadas,
    (SELECT COUNT(*) FROM v_cuotas_pendientes WHERE f17_prestamo = f16_codigo AND estado_calculado = 'VIGENTE') AS cuotas_pendientes,
    (SELECT COUNT(*) FROM v_cuotas_pendientes WHERE f17_prestamo = f16_codigo AND estado_calculado = 'PARCIAL') AS cuotas_parciales
FROM f16_prestamo;

-- ============================================================================
-- FIN DEL SCRIPT FASE 2
-- ============================================================================
