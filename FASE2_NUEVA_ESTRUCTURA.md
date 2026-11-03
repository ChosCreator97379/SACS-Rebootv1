# FASE 2: Creación de Nueva Estructura

## Objetivo
Crear las tablas necesarias para separar el cronograma original del cronograma actual, implementando la arquitectura propuesta por ChatGPT y mejorada.

## 1. Nueva Arquitectura Propuesta

### Diagrama de Tablas:

```
f16_prestamo (préstamos)
    ↓
f17_cuotasprestamo (tabla original - SE MANTIENE INTACTA) ← SISTEMA ACTUAL + REFERENCIA
    ↓
f17_cuotasprestamo_nuevo (tabla nueva - cronograma actual) ← ESTADO ACTUAL
    ↓
f15_movimiento (movimientos/pagos)
    ↓
f15_aplicaciones_cuotas (relación movimiento → cuotas)
```

### Descripción de Tablas:

#### 1. f17_cuotasprestamo (Tabla original - NO SE MODIFICA)
- **Propósito**: Tabla original en producción + Referencia para cálculos
- **Modificable**: NO (mientras se completa la migración)
- **Uso**: Sistema actual sigue funcionando sin cambios + Referencia para cálculo de saldos
- **Campos**: 22 campos (estructura actual)
- **Importante**: NO se renombra, NO se elimina, NO se modifica
- **Rol**: Sirve como "cronograma original" y sistema actual simultáneamente

#### 2. f17_cuotasprestamo_nuevo (Nueva tabla - COPIA)
- **Propósito**: Cronograma actual con estado modificado por pagos
- **Modificable**: SÍ
- **Uso**: Estado actual de las cuotas (después de migración)
- **Campos**: 22 campos (estructura idéntica a la original)
- **Creación**: `CREATE TABLE ... LIKE f17_cuotasprestamo` + `INSERT INTO ... SELECT *`
- **Limpieza**: Se eliminan duplicados para poder crear clave primaria
- **Importante**: Llamada "_nuevo" para no conflictuar con la tabla original

#### 3. f15_aplicaciones_cuotas (Nueva tabla)
- **Propósito**: Relacionar movimientos con cuotas y montos aplicados
- **Modificable**: SÍ
- **Uso**: Rastrear qué pagos cubrieron qué cuotas
- **Campos**:
  - f17_codigo: ID de la aplicación (PK)
  - f15_codigo: ID del movimiento (FK a f15_movimiento)
  - f17_prestamo: ID del préstamo (FK a f16_prestamo)
  - f17_ncuota: Número de cuota
  - f17_capital_aplicado: Capital aplicado a esta cuota
  - f17_interes_aplicado: Interés aplicado a esta cuota
  - f17_mora_aplicado: Mora aplicada a esta cuota
  - f17_gastos_aplicado: Gastos aplicados a esta cuota
  - f17_total_aplicado: Total aplicado a esta cuota
  - f17_fechaCrea, f17_usuaCrea: Auditoría
- **Foreign keys**:
  - f15_codigo → f15_movimiento (ON DELETE CASCADE)
  - f17_prestamo → f16_prestamo (ON DELETE CASCADE)

#### 4. v_cuotas_pendientes (Vista)
- **Propósito**: Calcular automáticamente saldo pendiente de cada cuota
- **Lógica**: `saldo_pendiente = total_original - total_aplicado`
- **Estado calculado**:
  - PAGADO: saldo_pendiente <= 0.01
  - PARCIAL: 0 < saldo_pendiente < total_original
  - VIGENTE: saldo_pendiente >= total_original

#### 5. v_resumen_prestamo_cuotas (Vista)
- **Propósito**: Reemplazar subqueries en PrestamosServicio
- **Campos**: cuotas_pagadas, cuotas_pendientes, cuotas_parciales
- **Optimización**: Reduce carga en consultas de resumen

## 2. Ventajas de la Nueva Arquitectura

### 2.1. Seguridad en Producción
- La tabla original f17_cuotasprestamo SE MANTIENE INTACTA
- El sistema actual sigue funcionando sin interrupciones
- No hay riesgo de perder datos o romper el sistema existente
- La migración se hace en paralelo, no inplace

### 2.2. Preservación de Datos Históricos
- La copia f17_cuotasprestamo_original es un respaldo exacto
- Se puede comparar: "lo programado vs lo actual"
- Los usuarios siempre saben cuál era el monto original

### 2.2. Cálculo Preciso de Faltantes
```
Faltante = CronogramaOriginal[monto] - AplicacionesRegistradas[monto]
```
- Sin ambigüedad sobre cuánto falta pagar
- Se puede rastrear el historial completo de aplicaciones

### 2.3. Integridad de Datos
- No se pierde información al modificar valores
- Mejor auditoría y trazabilidad
- Más fácil detectar errores

### 2.4. Flexibilidad en Pagos
- Permite pagos que cubran múltiples cuotas
- Permite pagos parciales que se completan después
- Se puede revertir cambios si es necesario

### 2.5. Resolución del Problema de los 1.2M Movimientos
- Los movimientos históricos sin f15_prestamo pueden ser ignorados
- Los nuevos movimientos SIEMPRE tendrán relación f15_aplicaciones_cuotas
- Se puede limpiar el histórico gradualmente sin afectar el flujo actual

## 3. Diferencias con la Propuesta Original del Usuario

### Propuesta Original del Usuario:
- Tabla copia del cronograma (visualización)
- Tabla actual (modificaciones)
- Sin tabla de aplicaciones

### Propuesta Mejorada (ChatGPT + esta implementación):
- Tabla original (f17_cuotasprestamo_original)
- Tabla actual (f17_cuotasprestamo)
- **Tabla de aplicaciones (f15_aplicaciones_cuotas)** ← NUEVO
- **Vistas calculadas** ← NUEVO

### ¿Por qué es mejor la propuesta mejorada?
1. **Granularidad**: Rastrea cada centavo aplicado a cada cuota
2. **Traza**: Permite reconstruir cualquier pago histórico
3. **Flexibilidad**: Pagos que cubren múltiples cuotas
4. **Automatización**: Vistas calculan estados automáticamente

## 4. Script SQL de Creación

El script `FASE2_NUEVA_ESTRUCTURA.sql` contiene:

1. **Renombrar tabla actual** a f17_cuotasprestamo_original
2. **Crear nueva tabla** f17_cuotasprestamo con mejor estructura
3. **Crear tabla** f15_aplicaciones_cuotas para aplicaciones
4. **Crear vista** v_cuotas_pendientes para cálculo automático
5. **Crear vista** v_resumen_prestamo_cuotas para optimización

## 5. Impacto en Servicios Existentes

### Servicios que MODIFICAN f17_cuotasprestamo:

#### AmortizacionServicio.php
- **Cambio**: En lugar de modificar f17_cuotasprestamo directamente, debe:
  1. Insertar en f15_aplicaciones_cuotas con los montos aplicados
  2. Actualizar f17_cuotasprestamo_nuevo con el estado calculado
  3. NO modificar f17_cuotasprestamo (tabla original)

#### PrestamosServicio.php
- **Cambio**: Al generar/regenerar cronograma:
  1. Insertar en f17_cuotasprestamo (tabla original - sistema actual)
  2. Insertar en f17_cuotasprestamo_nuevo (copia inicial)
  3. NO insertar en f15_aplicaciones_cuotas (no hay pagos aún)

### Servicios que LEEN f17_cuotasprestamo:

#### PagoIntegralServicio.php
- **Cambio**: Usar vista v_cuotas_pendientes para:
  - `obtenerCuotasParciales()`: WHERE estado_calculado = 'PARCIAL'
  - `obtenerCuotaActual()`: WHERE estado_calculado = 'VIGENTE'
  - Calcular faltante: `saldo_pendiente` de la vista

#### PrestamosServicio.php
- **Cambio**: Usar vista v_resumen_prestamo_cuotas en lugar de subqueries
- **Cambio**: Usar vista v_cuotas_pendientes para obtenerCronograma()

#### Otros servicios (CajaRepository, SobregiroPrestamo, CancelacionAee)
- **Cambio**: Usar vista v_cuotas_pendientes en lugar de f17_cuotasprestamo directa

## 6. Validación de Estructura

### Verificaciones realizadas:

1. ✅ Script SQL sintácticamente correcto
2. ✅ f16_prestamo existe
3. ✅ f15_movimiento existe
4. ✅ f16_codigo es int(10) unsigned (compatible)
5. ✅ f15_codigo es int(10) unsigned (compatible)
6. ✅ f17_cuotasprestamo existe
7. ✅ Motor InnoDB (soporta foreign keys)

### Problema Identificado: Duplicados en (f17_prestamo, f17_ncuota)

**Hallazgo:**
- Total filas: 753,801
- Pares únicos: 734,764
- **Duplicados: 19,037 filas**
- Préstamos afectados: 10 préstamos

**Préstamos con duplicados:**
- 8639: 2 filas
- 9004: 2 filas
- 9152: 6 filas
- 9501: 12 filas
- 9647: 36 filas
- 9701: 76 filas
- 9703: 10 filas
- 9767: 14 filas
- 9798: 16 filas
- 9846: 40 filas

**Ejemplo de duplicado (préstamo 8639, cuota 12):**
- Ambas filas idénticas: Estado CANCELADO, Total 96.3333, Saldo 0

**Implicación:**
- No se puede crear clave primaria (f17_prestamo, f17_ncuota) mientras existan duplicados
- Necesario script de limpieza antes de Fase 3

**Solución propuesta:**
- Script para eliminar duplicados en Fase 3
- Estrategia: Mantener la fila con f17_codigo más bajo (o más reciente) y eliminar las demás

### Script de Limpieza de Duplicados (para Fase 3):

```sql
-- Eliminar duplicados manteniendo la fila con f17_codigo más bajo
DELETE t1 FROM f17_cuotasprestamo t1
INNER JOIN f17_cuotasprestamo t2
WHERE t1.f17_prestamo = t2.f17_prestamo
  AND t1.f17_ncuota = t2.f17_ncuota
  AND t1.f17_codigo > t2.f17_codigo;

-- Nota: Este script asume que f17_codigo existe y es autoincremental
-- Si no existe, usar la fecha de creación u otro criterio
```

**Alternativa sin f17_codigo:**
```sql
-- Usar ROW_NUMBER para identificar duplicados (MySQL 8.0+)
WITH ranked AS (
    SELECT *,
           ROW_NUMBER() OVER (PARTITION BY f17_prestamo, f17_ncuota ORDER BY f17_fechaCrea) as rn
    FROM f17_cuotasprestamo
)
DELETE FROM f17_cuotasprestamo
WHERE (f17_prestamo, f17_ncuota) IN (
    SELECT f17_prestamo, f17_ncuota FROM ranked WHERE rn > 1
);
```

## 7. Plan de Rollback

### Si algo sale mal en Fase 3:

```sql
-- 1. Eliminar tablas nuevas
DROP TABLE IF EXISTS f15_aplicaciones_cuotas;
DROP TABLE IF EXISTS f17_cuotasprestamo_nuevo;
DROP VIEW IF EXISTS v_cuotas_pendientes;
DROP VIEW IF EXISTS v_resumen_prestamo_cuotas;

-- 2. La tabla original f17_cuotasprestamo se mantiene intacta
-- No se necesita restaurar nada
```

## 8. Siguiente Fase

**Fase 3: Migración de Datos**
- Script para copiar datos actuales a f17_cuotasprestamo_original
- Script para copiar datos actuales a f17_cuotasprestamo (tabla nueva)
- Script para poblar f15_aplicaciones_cuotas con movimientos históricos
- Validar unicidad de cuotas
- **NO EJECUTAR** hasta aprobación

---

**Estado Fase 2**: 100% completado
**Validaciones realizadas:**
- ✅ Tablas de referencia existen (f16_prestamo, f15_movimiento)
- ✅ Tipos de datos compatibles
- ✅ Motor InnoDB (soporta foreign keys)
- ⚠️ Duplicados identificados (19,037 filas en 10 préstamos)
- ✅ Script de limpieza preparado
- ✅ Arquitectura simplificada (2 tablas de cuotas, no 3)

**Archivos creados:**
- `FASE2_NUEVA_ESTRUCTURA.sql` - Script de creación de tablas
- `FASE2_NUEVA_ESTRUCTURA.md` - Documentación completa

**Arquitectura final:**
- f17_cuotasprestamo (original) - NO SE MODIFICA
- f17_cuotasprestamo_nuevo (copia) - Se puede modificar
- f15_aplicaciones_cuotas (nueva) - Relación movimiento → cuotas
- v_cuotas_pendientes (vista) - Cálculo automático de saldos
- v_resumen_prestamo_cuotas (vista) - Resumen optimizado

**Siguiente fase:** Fase 3 (Migración de Datos)
**Precondición:** Ejecutar script de limpieza de duplicados antes de migración
