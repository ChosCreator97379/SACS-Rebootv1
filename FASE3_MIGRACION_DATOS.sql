-- ============================================================================
-- FASE 3: MIGRACIÓN DE DATOS
-- ============================================================================
-- Objetivo: Migrar datos históricos a la nueva estructura
-- ============================================================================
-- RECOMENDACIONES:
-- 1. Ejecutar en base de datos de prueba (NO producción)
-- 2. Hacer backup antes de ejecutar
-- 3. Revisar los resultados de las validaciones al final
-- 4. Tiempo estimado: 50-90 minutos
-- NOTA: NO se eliminan duplicados por precaución
-- ============================================================================

-- ----------------------------------------------------------------------------
-- 1. Crear tabla f17_cuotasprestamo_nuevo (COPIA de la tabla actual)
-- ----------------------------------------------------------------------------

CREATE TABLE f17_cuotasprestamo_nuevo LIKE f17_cuotasprestamo;

INSERT INTO f17_cuotasprestamo_nuevo
SELECT * FROM f17_cuotasprestamo;

-- ----------------------------------------------------------------------------
-- 2. Validar copia de datos
-- ----------------------------------------------------------------------------

SELECT
    COUNT(*) as total_filas_original
FROM f17_cuotasprestamo;

SELECT
    COUNT(*) as total_filas_nuevo
FROM f17_cuotasprestamo_nuevo;

-- Debería mostrar: total_filas_original = total_filas_nuevo

-- ----------------------------------------------------------------------------
-- 3. Crear tabla f15_aplicaciones_cuotas
-- ----------------------------------------------------------------------------

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
-- 4. Migrar movimientos históricos a f15_aplicaciones_cuotas
-- ----------------------------------------------------------------------------
-- NOTA: Solo se migran movimientos con f15_prestamo correcto
-- Los 1,232,008 movimientos con f15_prestamo incorrecto NO se migran (deuda técnica)

INSERT INTO f15_aplicaciones_cuotas (
    f15_codigo,
    f17_prestamo,
    f17_ncuota,
    f17_capital_aplicado,
    f17_interes_aplicado,
    f17_mora_aplicado,
    f17_gastos_aplicado,
    f17_total_aplicado,
    f17_fechaCrea,
    f17_usuaCrea
)
SELECT
    m.f15_codigo,
    m.f15_prestamo AS f17_prestamo,
    -- Determinar la cuota correspondiente basándose en la fecha del movimiento
    -- Se asume que el pago corresponde a la cuota más cercana a la fecha del movimiento
    (SELECT f17_ncuota
     FROM f17_cuotasprestamo
     WHERE f17_prestamo = m.f15_prestamo
       AND f17_fecha <= m.f15_fecha
     ORDER BY f17_fecha DESC
     LIMIT 1) AS f17_ncuota,
    COALESCE(m.f15_maprestamo, 0) AS f17_capital_aplicado,
    0 AS f17_interes_aplicado, -- No hay campo directo de interés en f15_movimiento
    0 AS f17_mora_aplicado, -- No hay campo directo de mora en f15_movimiento
    COALESCE(m.f15_gastosa, 0) + COALESCE(m.f15_gastosm, 0) + COALESCE(m.f15_gastos1, 0) + COALESCE(m.f15_gastos2, 0) AS f17_gastos_aplicado,
    COALESCE(m.f15_maprestamo, 0) + COALESCE(m.f15_gastosa, 0) + COALESCE(m.f15_gastosm, 0) + COALESCE(m.f15_gastos1, 0) + COALESCE(m.f15_gastos2, 0) AS f17_total_aplicado,
    m.f15_fecha AS f17_fechaCrea,
    'MIGRACION' AS f17_usuaCrea
FROM f15_movimiento m
WHERE m.f15_prestamo IS NOT NULL
  AND m.f15_prestamo != 0
  AND m.f15_prestamo != ''
  AND m.f15_maprestamo IS NOT NULL
  AND m.f15_maprestamo > 0;

-- ----------------------------------------------------------------------------
-- 5. Validar migración de aplicaciones
-- ----------------------------------------------------------------------------

-- Contar movimientos migrados
SELECT COUNT(*) as movimientos_migrados
FROM f15_aplicaciones_cuotas
WHERE f17_usuaCrea = 'MIGRACION';

-- Contar movimientos no migrados (con f15_prestamo incorrecto)
SELECT COUNT(*) as movimientos_no_migrados
FROM f15_movimiento
WHERE f15_prestamo IS NULL OR f15_prestamo = 0 OR f15_prestamo = '';

-- ----------------------------------------------------------------------------
-- 6. Crear vistas
-- ----------------------------------------------------------------------------

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

CREATE OR REPLACE VIEW v_resumen_prestamo_cuotas AS
SELECT
    f16_codigo,
    (SELECT COUNT(*) FROM v_cuotas_pendientes WHERE f17_prestamo = f16_codigo AND estado_calculado = 'PAGADO') AS cuotas_pagadas,
    (SELECT COUNT(*) FROM v_cuotas_pendientes WHERE f17_prestamo = f16_codigo AND estado_calculado = 'VIGENTE') AS cuotas_pendientes,
    (SELECT COUNT(*) FROM v_cuotas_pendientes WHERE f17_prestamo = f16_codigo AND estado_calculado = 'PARCIAL') AS cuotas_parciales
FROM f16_prestamo;

-- ----------------------------------------------------------------------------
-- 7. Validar vista de cuotas pendientes
-- ----------------------------------------------------------------------------

-- Verificar un préstamo específico
SELECT * FROM v_cuotas_pendientes WHERE f17_prestamo = 40641 ORDER BY f17_ncuota;

-- ============================================================================
-- FIN DEL SCRIPT FASE 3
-- ============================================================================
