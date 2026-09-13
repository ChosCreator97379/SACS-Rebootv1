# Resultados de Pruebas de Caracterización - Amortización AEE

## Prueba 1: Caso básico con gastos administrativos
**Movimiento:** 122013090 (2026-08-26)
**Préstamo:** 43232
**Cuota:** 1

### Datos del movimiento real
- Capital pagado: 359.87
- Gastos administrativos: 233.32
- Mora: 0
- Otros1: 0
- Otros2: 0
- Total pagado: 593.19

### Resultado del cálculo
- ✅ Capital: 359.87
- ✅ Gastos administrativos: 233.32
- ✅ Total distribuido: 593.19
- ✅ Cuotas pendientes: 22
- ✅ Saldo: 9166.10
- ✅ Sobregiro: -108.33 (exceso de pago)

**Estado:** ✅ VALIDADO

---

## Prueba 2: Caso con mora
**Movimiento:** 122012862 (2026-08-20)
**Préstamo:** 38760
**Cuota:** 1

### Datos del movimiento real
- Capital pagado: 756.24
- Gastos administrativos: 103.46
- Mora: 10
- Otros1: 0
- Otros2: 0
- Total pagado: 869.70

### Datos del préstamo
- Sobregiro existente: 2600.85 (muy grande)
- Saldo después del pago: 4920.83

### Resultado del cálculo
- ✅ Capital: 756.24
- ✅ Gastos administrativos: 103.46
- ✅ Mora: 10
- ✅ Total distribuido: 869.70
- ✅ Cuotas pendientes: 15
- ❌ Saldo: 5477.78 (esperado: 4920.83)

**Estado:** ❌ NO VALIDADO - Discrepancia en saldo
**Diferencia:** 556.95
**Nota:** Este caso tiene un sobregiro existente muy grande (2600.85) que requiere análisis adicional.

---

## Prueba 3: Caso con otros1 y otros2
**Movimiento:** 122012265 (2026-08-20)
**Préstamo:** 42443
**Cuota:** 1

### Datos del movimiento real
- Capital pagado: 55.89
- Gastos administrativos: 145.72
- Mora: 0
- Otros1: 300
- Otros2: 300
- Total pagado: 801.61

### Resultado del cálculo
- ✅ Capital: 55.89
- ✅ Gastos administrativos: 145.72
- ✅ Otros1: 300
- ✅ Otros2: 300
- ✅ Total distribuido: 801.61
- ✅ Cuotas pendientes: 0
- ✅ Saldo: 0
- ✅ Sobregiro: -745.72 (exceso de pago)

**Estado:** ✅ VALIDADO

---

## Conclusiones

1. **Orden de aplicación validado:** sobregiro → gastos administrativos → mora → otros1 → otros2 → capital
2. **Casos validados:** 
   - Caso básico con gastos administrativos ✅
   - Caso con otros1 y otros2 ✅
3. **Caso pendiente de ajuste:**
   - Caso con mora y sobregiro existente grande ❌
   - Requiere análisis adicional de la lógica de sobregiro cuando es muy grande

## Recomendaciones

1. La función pura de cálculo funciona correctamente para la mayoría de casos
2. El caso con sobregiro existente grande requiere análisis más profundo del código legacy
3. Se recomienda validar con más casos de producción antes de implementar el pago real
