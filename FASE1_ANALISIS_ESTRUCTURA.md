# FASE 1: Preparación y Análisis - Cronograma de Cuotas

## Objetivo
Documentar la estructura actual de `f17_cuotasprestamo` y analizar el impacto de la migración a una arquitectura de dos tablas (cronograma original + cronograma actual).

## 1. Estructura Actual de f17_cuotasprestamo

### Campos de la tabla:

| Campo | Tipo | Descripción Actual | Uso Actual |
|-------|------|-------------------|-------------|
| f17_prestamo | int(10) unsigned | ID del préstamo (FK) | Relación con f16_prestamo |
| f17_ncuota | int(10) unsigned | Número de cuota | Índice de la cuota |
| f17_fecha | date | Fecha de vencimiento | Programación de pagos |
| f17_capital | float | Capital de la cuota | **MODIFICADO** por pagos |
| f17_interes | float | Interés de la cuota | **MODIFICADO** por pagos |
| f17_total | float | Total de la cuota | **MODIFICADO** por pagos |
| f17_saldo | float | Saldo del préstamo después de la cuota | Saldo proyectado (NO es saldo pagado) |
| f17_cta | varchar(45) | Campo de referencia | No usado en análisis |
| f17_totalA | float | Total alternativo | No usado en análisis |
| f17_estado | varchar(45) | Estado (VIGENTE, PAGADO, PARCIAL) | **MODIFICADO** por pagos |
| f17_fechaCancela | date | Fecha de cancelación | Cancelación de cuota |
| f17_sobregiro | float | Sobregiro | Sobregiro de la cuota |
| f17_interesSG | float | Interes segundo grupo | Interes adicional |
| f17_mora | float | Mora | Mora de la cuota |
| f17_moraSG | float | Mora segundo grupo | Mora adicional |
| f17_pagoCaja | float | Pago en caja | Monto pagado |
| f17_tramite | float | Trámite | Número de trámite |
| f17_indice | int(10) unsigned | Índice | Índice interno |

### Problemas Identificados:

1. **Sin clave primaria**: La tabla no tiene clave primaria declarada
2. **Sin índice único**: `(f17_prestamo, f17_ncuota)` debería ser clave única pero no está declarado
3. **Modificación directa**: Los servicios modifican capital, interés, total y estado al procesar pagos
4. **f17_saldo malinterpretado**: Se usa como saldo pendiente de cuota pero es saldo proyectado del préstamo

## 2. Servicios que Usan f17_cuotasprestamo

### Archivos identificados (10):

1. **PagoIntegralServicio.php** (3 usos)
   - Línea 65: `obtenerCuotasParciales()` - Consulta cuotas PARCIAL
   - Línea 107: `obtenerCuotaActual()` - Consulta cuota VIGENTE
   - Línea 658: Procesamiento de cuotas parciales

2. **AmortizacionServicio.php** (4 usos)
   - Línea 115: Comentario sobre actualización
   - Línea 199: `obtenerCuota()` - SELECT *
   - Línea 285: `actualizarCuota()` - **MODIFICA estado, saldo, capital, interés, total**
   - Línea 300: UPDATE sobre f17_cuotasprestamo

3. **amortizacion.php** (vista) (1 uso)
   - Línea 65: Muestra cronograma al usuario

4. **SobregiroPrestamoServicio.php** (1 uso)
   - Línea 380: Consulta cuotas para sobregiro

5. **CajaRepository.php** (3 usos)
   - Línea 376: Resumen de caja
   - Línea 539: Cronograma de préstamo
   - Línea 584: Otra consulta de cronograma

6. **PrestamosServicio.php** (13 usos)
   - Línea 407: `generarCronograma()` - **INSERT** en f17_cuotasprestamo
   - Línea 672: `regenerarCronograma()` - **DELETE** en f17_cuotasprestamo
   - Línea 744: `refinanciarCronograma()` - **DELETE** en f17_cuotasprestamo
   - Línea 982: `obtenerCronograma()` - SELECT completo
   - Línea 998: `contarCuotas()` - COUNT
   - Línea 1022: `obtenerCuotasPagadas()` - SELECT PAGADO
   - Línea 1046: `obtenerCuotasPendientes()` - SELECT VIGENTE
   - Línea 1070: `obtenerCuotasVencidas()` - SELECT VIGENTE + fecha vencida
   - Línea 1120: `obtenerResumenPrestamo()` - Subqueries COUNT
   - Línea 1121: `obtenerResumenPrestamo()` - Subqueries COUNT

7. **CancelacionAeeServicio.php** (1 uso)
   - Línea 166: Consulta cuotas para cancelación

8. **legacy/SociosDAOImpl.java** (5 usos)
   - Código legacy Java (referencia)

9. **database/sacs_pruebas_50.sql** (7 usos)
   - Datos de prueba

10. **CONTEXTO_CONTINUIDAD_SACS.md** (8 usos)
    - Documentación

### Servicios que MODIFICAN f17_cuotasprestamo:

1. **AmortizacionServicio.php**
   - `actualizarCuota()` - UPDATE estado, saldo, capital, interés, total
   - **Impacto**: ALTO - Modifica el cronograma al procesar pagos

2. **PrestamosServicio.php**
   - `generarCronograma()` - INSERT nuevas cuotas
   - `regenerarCronograma()` - DELETE + INSERT
   - `refinanciarCronograma()` - DELETE + INSERT
   - **Impacto**: MEDIO - Modifica al crear/regenerar cronograma

### Servicios que LEEN f17_cuotasprestamo:

1. **PagoIntegralServicio.php**
   - `obtenerCuotasParciales()` - Consulta PARCIAL
   - `obtenerCuotaActual()` - Consulta VIGENTE
   - **Impacto**: MEDIO - Necesita adaptar a nueva estructura

2. **amortizacion.php** (vista)
   - Muestra cronograma
   - **Impacto**: BAJO - Solo lectura, necesita cambiar consulta

3. **SobregiroPrestamoServicio.php**
   - Consulta cuotas
   - **Impacto**: BAJO - Solo lectura, necesita cambiar consulta

4. **CajaRepository.php**
   - Consultas varias
   - **Impacto**: BAJO - Solo lectura, necesita cambiar consultas

5. **PrestamosServicio.php**
   - `obtenerCronograma()` - SELECT completo
   - `contarCuotas()` - COUNT
   - `obtenerCuotasPagadas()` - SELECT PAGADO
   - `obtenerCuotasPendientes()` - SELECT VIGENTE
   - `obtenerCuotasVencidas()` - SELECT VIGENTE + fecha
   - `obtenerResumenPrestamo()` - Subqueries COUNT
   - **Impacto**: MEDIO - Solo lectura, necesita cambiar consultas

6. **CancelacionAeeServicio.php**
   - Consulta cuotas
   - **Impacto**: BAJO - Solo lectura, necesita cambiar consulta

## 3. Análisis de Movimientos Históricos

### Hallazgo Crítico: Movimientos con f15_prestamo incorrecto

**Resultados de la consulta:**
- **Total de movimientos con f15_prestamo incorrecto**: 1,232,008
- **Ejemplos identificados**:
  - Movimientos con f15_prestamo NULL (operaciones 1, 2, 7, 8, 9)
  - Clientes con código 0 o NULL
  - Fechas inválidas (2205-12-31, 7200-04-20, 6200-06-20)

**Implicaciones:**
- El sistema legacy no registraba correctamente la relación movimiento-préstamo
- Sin esta relación, es imposible rastrear qué cuotas cubrió cada movimiento
- La nueva tabla `f15_aplicaciones_cuotas` sería esencial para corregir esto
- Los 1.2M de movimientos necesitan limpieza o migración manual

**Ejemplos de movimientos con datos incorrectos:**
```
Código: 14836 | Préstamo: NULL | Cliente: 70793 | Operación: 2
Código: 22340 | Préstamo: NULL | Cliente: 288926 | Operación: 2
Código: 34231 | Préstamo: NULL | Cliente: 280127 | Operación: 2
Código: 120377105 | Préstamo: NULL | Cliente: 140864 | Operación: 7
```

### Acción requerida futura:
- Script para limpiar o corregir los 1.2M de movimientos
- Posible migración basada en f15_fecha y f15_operación para inferir préstamo

## 4. Casos de Prueba a Validar

### Pruebas que fallaron según ChatGPT:

1. **Prueba básica**: Saldo calculado 9,057.77 vs esperado 9,166.10
2. **Prueba con mora**: Saldo calculado 4,969.87 vs esperado 4,920.83
3. **Prueba con otros conceptos**: Saldo calculado -745.72 vs esperado 0.00

**Acción requerida:**
- Revisar `resultados_pruebas_caracterizacion.md`
- Identificar por qué los saldos calculados no coinciden
- Validar que los datos de entrada representen correctamente el estado anterior al pago

## 5. Recomendaciones de la Fase 1

### Estado actual:
- ✅ Estructura de tabla documentada
- ⏳ Servicios que usan la tabla identificados
- ⏳ Movimientos históricos pendientes de revisión
- ⏳ Casos de prueba pendientes de revisión

### Próximos pasos:
1. Ejecutar consultas para identificar movimientos con f15_prestamo incorrecto
2. Documentar los resultados
3. Validar casos de prueba de caracterización
4. Completar documento de Fase 1
5. Presentar resultados para aprobación de Fase 2

## 6. Estrategia de Migración Propuesta (Resumen)

### Fase 2: Crear Nueva Estructura
- `f17_cuotasprestamo_original` (copia inmutable del cronograma)
- `f17_cuotasprestamo` (tabla actual renombrada o recreada)
- `f15_aplicaciones_cuotas` (nueva tabla de aplicaciones por movimiento)

### Fase 3: Migración de Datos
- Script para copiar datos actuales a `f17_cuotasprestamo_original`
- Script para poblar `f15_aplicaciones_cuotas` con movimientos históricos
- Validar unicidad de cuotas

### Fase 4: Actualización Gradual de Servicios
- Actualizar `AmortizacionServicio.php` (prioridad alta)
- Actualizar `PagoIntegralServicio.php` (prioridad alta)
- Actualizar `PrestamosServicio.php` (prioridad media)
- Actualizar otros servicios (prioridad baja)

### Fase 5: Pruebas y Validación
- Ejecutar pruebas de caracterización
- Validar cálculos de faltantes
- Probar pagos parciales reales
- Validar pagos que cubren múltiples cuotas

### Fase 6: Activación y Monitoreo
- Ejecutar migración de datos
- Activar nueva estructura
- Monitorear por periodo de prueba
- Rollback si es necesario

---

**Estado Fase 1**: 60% completado
**Próxima acción**: Ejecutar consultas para identificar movimientos con f15_prestamo incorrecto
