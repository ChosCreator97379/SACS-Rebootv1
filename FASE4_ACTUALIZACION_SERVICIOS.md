# FASE 4: Actualización Gradual de Servicios

## Objetivo
Actualizar los servicios PHP para usar la nueva estructura de base de datos (f17_cuotasprestamo_nuevo y f15_aplicaciones_cuotas).

## Servicios a Actualizar

### Prioridad Alta

#### 1. AmortizacionServicio.php
**Cambio necesario:**
- Agregar método `insertarAplicacionCuota()` para registrar en f15_aplicaciones_cuotas
- Modificar `actualizarCuota()` para actualizar f17_cuotasprestamo_nuevo en lugar de f17_cuotasprestamo
- Llamar a `insertarAplicacionCuota()` después de insertar el movimiento
- NO modificar f17_cuotasprestamo (tabla original)

**Flujo actual:**
```
1. Obtener préstamo
2. Obtener cuota
3. Calcular distribución
4. Generar código movimiento
5. Insertar diario contable
6. Insertar movimiento (f15_movimiento)
7. Actualizar préstamo (f16_prestamo)
8. Actualizar cuota (f17_cuotasprestamo) ← MODIFICAR
```

**Flujo nuevo:**
```
1. Obtener préstamo
2. Obtener cuota
3. Calcular distribución
4. Generar código movimiento
5. Insertar diario contable
6. Insertar movimiento (f15_movimiento)
7. Insertar aplicación (f15_aplicaciones_cuotas) ← NUEVO
8. Actualizar préstamo (f16_prestamo)
9. Actualizar cuota (f17_cuotasprestamo_nuevo) ← CAMBIAR TABLA
```

#### 2. PagoIntegralServicio.php
**Cambio necesario:**
- Modificar `obtenerCuotasParciales()` para usar vista v_cuotas_pendientes
- Modificar `obtenerCuotaActual()` para usar vista v_cuotas_pendientes
- Calcular faltante usando `saldo_pendiente` de la vista

**Consultas actuales:**
```php
// obtenerCuotasParciales()
SELECT * FROM f17_cuotasprestamo
WHERE f17_prestamo = ? AND f17_estado = 'PARCIAL'

// obtenerCuotaActual()
SELECT * FROM f17_cuotasprestamo
WHERE f17_prestamo = ? AND f17_estado = 'VIGENTE'
ORDER BY f17_fecha ASC LIMIT 1
```

**Consultas nuevas:**
```php
// obtenerCuotasParciales()
SELECT * FROM v_cuotas_pendientes
WHERE f17_prestamo = ? AND estado_calculado = 'PARCIAL'

// obtenerCuotaActual()
SELECT * FROM v_cuotas_pendientes
WHERE f17_prestamo = ? AND estado_calculado = 'VIGENTE'
ORDER BY f17_fecha ASC LIMIT 1
```

### Prioridad Media

#### 3. PrestamosServicio.php
**Cambio necesario:**
- Modificar `obtenerResumenPrestamo()` para usar vista v_resumen_prestamo_cuotas
- Modificar `obtenerCronograma()` para usar vista v_cuotas_pendientes
- Modificar métodos de cuotas (pagadas, pendientes, vencidas) para usar vista v_cuotas_pendientes

**Consultas actuales:**
```php
// obtenerResumenPrestamo()
SELECT ..., (SELECT COUNT(*) FROM f17_cuotasprestamo WHERE ...) as cuotas_pagadas
       , (SELECT COUNT(*) FROM f17_cuotasprestamo WHERE ...) as cuotas_pendientes
FROM f16_prestamo WHERE f16_codigo = ?
```

**Consultas nuevas:**
```php
// obtenerResumenPrestamo()
SELECT p.*, r.cuotas_pagadas, r.cuotas_pendientes, r.cuotas_parciales
FROM f16_prestamo p
LEFT JOIN v_resumen_prestamo_cuotas r ON p.f16_codigo = r.f16_codigo
WHERE p.f16_codigo = ?
```

### Prioridad Baja

#### 4. CajaRepository.php
**Cambio necesario:**
- Modificar consultas de cronograma para usar vista v_cuotas_pendientes

#### 5. SobregiroPrestamoServicio.php
**Cambio necesario:**
- Modificar consultas de cuotas para usar vista v_cuotas_pendientes

#### 6. CancelacionAeeServicio.php
**Cambio necesario:**
- Modificar consultas de cuotas para usar vista v_cuotas_pendientes

## Estrategia de Implementación

### Enfoque: Actualización gradual uno por uno

1. **AmortizacionServicio.php** (más crítico)
   - Agregar método de aplicaciones
   - Modificar actualizarCuota()
   - Probar con un pago real
   - Validar que se registre en f15_aplicaciones_cuotas

2. **PagoIntegralServicio.php** (segundo más crítico)
   - Modificar consultas de cuotas
   - Probar con pago integral
   - Validar cálculo de faltantes

3. **PrestamosServicio.php** (tercero)
   - Modificar consultas de resumen
   - Probar visualización de préstamos
   - Validar conteos de cuotas

4. **Otros servicios** (menos críticos)
   - Actualizar uno por uno
   - Probar cada uno
   - Validar funcionalidad

## Plan de Pruebas

### Prueba 1: Amortización simple
- Procesar un pago de amortización
- Verificar que se registre en f15_aplicaciones_cuotas
- Verificar que se actualice f17_cuotasprestamo_nuevo
- Verificar que NO se modifique f17_cuotasprestamo
- Verificar que la vista v_cuotas_pendientes muestre el estado correcto

### Prueba 2: Pago integral
- Procesar un pago integral con cuotas parciales
- Verificar que las cuotas parciales se detecten usando la vista
- Verificar que el faltante se calcule correctamente
- Verificar que se registren las aplicaciones

### Prueba 3: Visualización de préstamo
- Consultar un préstamo usando PrestamosServicio
- Verificar que use la vista v_resumen_prestamo_cuotas
- Verificar que los conteos de cuotas sean correctos

## Rollback

Si hay problemas:
- Revertir cambios en el servicio
- El sistema vuelve a usar f17_cuotasprestamo original
- No hay impacto en datos (f17_cuotasprestamo_nuevo se ignora)

---

**Estado Fase 4**: Planificación completada
**Próximo paso:** Actualizar AmortizacionServicio.php
