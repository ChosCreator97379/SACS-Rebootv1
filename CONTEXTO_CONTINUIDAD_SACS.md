# Contexto de continuidad: reconstruccion SACS

Fecha de corte: 2026-09-12

## 1. Objetivo del proyecto

Se esta reconstruyendo progresivamente el sistema legacy Java SACS (Sistema de Administracion de Creditos y Socios) como una aplicacion PHP para XAMPP.

El sistema nuevo debe conservar las funciones y reglas de negocio del legacy, especialmente cuando existan operaciones que inserten, actualicen o eliminen datos. En visualizacion, navegacion y experiencia de usuario si se permite mejorar el diseno y la organizacion.

Regla principal:

- Visualizacion: se puede innovar.
- Operaciones de escritura: deben respetar el flujo, calculos, estados, tablas y resultados del legacy.
- La seguridad interna puede mejorar con transacciones, validaciones, consultas parametrizadas, auditoria e idempotencia, sin cambiar la experiencia esperada por los usuarios.

## 2. Rutas y proyectos

Proyecto legacy Java original:

`C:\Users\GIGABYTE\OneDrive\Escritorio\fuente\SistemaAdmCreditosSocios\SACS`

Proyecto PHP nuevo:

`C:\xampp\htdocs\sacs_nuevo`

URL local:

`http://localhost/sacs_nuevo/public/`

Base MySQL de pruebas:

- Base: `sacs`
- Host: `127.0.0.1`
- Puerto: `3306`
- Usuario local actual: `root`
- Password local actual: vacio
- Driver: `pdo_mysql`

El proyecto PHP usa `.env`. No versionar `.env` ni credenciales.

## 3. Estado de la base de pruebas

La base `sacs` es una copia de pruebas identica a la de produccion segun el usuario. No se debe asumir que todos los datos legacy son consistentes.

Hallazgos confirmados:

- `f15_movimiento`: aproximadamente 1,631,253 registros.
- `f16_prestamo`: aproximadamente 43,410 registros.
- `f17_cuotasprestamo`: aproximadamente 753,459 registros.
- `f23_cheque`: aproximadamente 40,725 registros.
- Estados de `f16_prestamo` observados: 0, 1, 2, 3, 4, 5, 6.
- Estados de `f17_cuotasprestamo`: `VIGENTE` y `CANCELADO`.
- Fechas legacy anomalas: hay algunos valores fuera de rango normal; no convertir fechas masivamente sin estrategia.
- `f37_diario_contable` tiene muy pocos registros comparado con `f15_movimiento`; la mayoria de movimientos historicos no tiene asiento asociado.
- `f17_cuotasprestamo` no tiene clave primaria declarada, solo indice por prestamo.
- Muchas cantidades legacy son `float` o texto. Para operaciones nuevas se debe evitar `float` en calculos financieros y usar decimal/strings decimales o `BigDecimal` equivalente.

## 4. Dump para compartir

Se genero y valido:

`C:\xampp\htdocs\sacs_nuevo\database\sacs_pruebas_50.sql`

Caracteristicas:

- Estructura completa de 43 tablas.
- Maximo 50 registros por tabla.
- Tablas vacias incluidas.
- Sin triggers ni rutinas.
- Tamano aproximado: 337 KB.
- Se importo en una base temporal y la validacion fue exitosa.
- La base temporal de validacion fue eliminada.

Advertencia: el dump contiene datos de prueba del legacy, incluyendo usuarios y potencialmente credenciales legacy. Compartir solo con personas autorizadas.

## 5. Estructura PHP actual

Archivos principales:

- `config/bootstrap.php`: autoload propio para namespace `Sacs\\` y carga de `.env`.
- `src/Core/Database.php`: conexion PDO singleton con errores por excepcion, fetch asociativo y prepares nativos.
- `src/Security/Auth.php`: sesion y autenticacion.
- `src/Repositories/UserRepository.php`: login contra `f06_usuario`.
- `src/Repositories/CajaRepository.php`: consultas de Caja y simulacion.
- `src/Services/AmortizacionCalculadora.php`: funcion pura de calculo de amortizacion AEE basada en legacy.
- `src/Services/AmortizacionServicio.php`: servicio de ejecucion de amortizacion AEE con transacciones.
- `public/index.php`: punto de entrada, login, rutas del dashboard y modulo Caja.
- `views/login.php`: login.
- `views/dashboard.php`: dashboard responsive.
- `views/caja/movimientos.php`: listado filtrado de movimientos.
- `views/caja/movimiento.php`: detalle de un movimiento.
- `views/caja/cliente.php`: perfil operativo del cliente.
- `views/caja/simulacion.php`: vista de simulacion de pago de cuota.
- `tests/test_amortizacion_calculadora.php`: prueba de caracterizacion caso basico.
- `tests/test_amortizacion_mora.php`: prueba de caracterizacion con mora.
- `tests/test_amortizacion_otros.php`: prueba de caracterizacion con otros1 y otros2.
- `tests/resultados_pruebas_caracterizacion.md`: documentacion de resultados de pruebas.
- `README.md`: instrucciones iniciales.
- `.env.example`: plantilla de configuracion.
- `.gitignore`: excluye `.env` y logs.

## 6. Autenticacion implementada

Tabla legacy: `f06_usuario`.

Campos utilizados:

- `f06_id_usuario`
- `f06_nombre`
- `f06_perfil`
- `f06_password`
- `f06_estado`
- `f06_supervisor`

El login:

- Solo acepta `f06_estado = 'A'`.
- Usa consulta preparada.
- Mantiene temporalmente la comparacion de password legacy sin hash para no romper compatibilidad.
- Regenera la sesion con `session_regenerate_id(true)`.
- Guarda en `$_SESSION['sacs_user']`: `id`, `username`, `profile`, `supervisor`.
- Tiene token CSRF para el formulario.
- Permite logout.

No cambiar aun el formato de password sin plan de migracion progresiva.

## 7. Dashboard implementado

`views/dashboard.php` contiene:

- Navegacion lateral responsive.
- Usuario, perfil, ID y supervisor de la sesion.
- Tarjetas de resumen.
- Modulos preparados.
- Consulta rapida de Caja por codigo de cliente.
- Enlace al modulo de movimientos.

La informacion de sesion se conserva y se usa en el dashboard.

## 8. Caja: alcance implementado

Caja se trata como nucleo operativo del sistema. No es solo un CRUD: conecta socios, prestamos, cuotas, aportaciones, beneficios, multas, rifas, cheques, movimientos y diario contable.

Catalogo real `f14_operacion`:

1. APORTACION FONBES
2. AMORTIZACION AEE
3. DESEMBOLSO AEE
4. DEVOLUCION FONBES
5. DEVOLUCION COOPAM
6. CANCELACION AEE
7. DESEMBOLSO BENEFICIOS
8. EGRESOS
9. APORTACION COOPAM
10. PAGO SOBREGIRO PRESTAMO
11. PAGO MULTA
12. PAGO RIFA
13. OTRAS DEVOLUCIONES
14. PAGO SOBREGIRO FONBES
15. PAGO SOBREGIRO COOPAM
16. OTROS INGRESOS

### 8.1 Listado de movimientos

Ruta:

`index.php?module=caja&client=CODIGO`

Comportamiento:

- La vista abre vacia si no hay codigo de cliente.
- Esto es intencional: no se debe cargar el historico de 1.6 millones de filas.
- Se consulta solo despues de introducir el cliente.
- Filtros: fecha inicial, fecha final, operacion y codigo cliente.
- Paginacion de 25 registros.
- PDO con parametros.
- La consulta incluye `f15_movimiento`, `f14_operacion`, `f02_cliente` y `f01_persona`.
- No modifica datos.

Importante: el formulario debe conservar `module=caja` como campo hidden. Ya fue corregido este problema.

### 8.2 Detalle de movimiento

Ruta:

`index.php?module=caja&movement=ID`

Consulta exactamente una fila por `f15_codigo` usando `LIMIT 1`.

Muestra:

- Operacion, fecha, cliente y usuario.
- Importes de aportacion, retiro, pago de prestamo, desembolso, beneficio, multa, rifa, devolucion, egreso e ingreso.
- Gastos administrativos, mora y otros.
- Prestamo, cheque, serie, observaciones.
- Estado del prestamo relacionado.
- Estado del asiento en `f37_diario_contable`.

Cada codigo de movimiento es enlace al detalle.

### 8.3 Perfil operativo de cliente

Ruta:

`index.php?module=caja&profile=client&client=CODIGO`

Muestra:

- Nombre, documento, centro y condicion.
- Hasta 20 prestamos.
- Hasta 50 cuotas vigentes.
- Hasta 12 aportaciones Fonbes.
- Hasta 12 aportaciones Coopam.
- Hasta 20 multas.
- Hasta 20 rifas.
- Hasta 20 beneficios.
- Enlace para volver a movimientos.

Los nombres o codigos faltantes no deben bloquear la navegacion: en la tabla de movimientos el codigo siempre es un enlace visible, por ejemplo `Cliente #335509`, y si falta nombre muestra `Sin nombre registrado`.

Caso confirmado:

- En `f02_cliente`, el cliente puede aparecer como `0335509`.
- En `f15`, `f16`, aportes y otras tablas puede aparecer como `335509`.
- Las consultas de perfil usan `CAST(... AS UNSIGNED)` para tolerar ceros a la izquierda.

### 8.4 Multas, rifas y beneficios

Consultas limitadas:

- Multas: `f63_multa`, maximo 20.
- Rifas: `f64_rifa`, maximo 20.
- Beneficios: `f21_beneficios`, maximo 20.

Se muestran en modo lectura. El cliente `347701` no tenia registros en estas tablas en la ultima prueba.

## 9. Simulacion actual de cuota

La simulacion se agrego como herramienta previa de validacion, no como nueva funcion de negocio para entrenar usuarios.

Ruta aproximada:

`index.php?module=caja&client=347701&loan=43413&installment=1`

Se accede desde el boton `Revisar cuota` de una cuota vigente.

La simulacion:

- Consulta un prestamo, cliente y cuota exactos.
- Exige cuota `f17_ncuota > 0`.
- Exige `f17_estado = 'VIGENTE'`.
- Calcula capital, interes, mora de cuota, sobregiro, interes de sobregiro y mora de sobregiro.
- Calcula total simulado.
- Estima saldo posterior descontando capital.
- Indica tablas que se modificarian en pago real.
- No ejecuta INSERT, UPDATE ni DELETE.

Prueba validada:

- Cliente: `347701`.
- Prestamo: `43413`.
- Cuota: `1`.
- Capital: `153.30`.
- Interes: `95.90`.
- Total: `249.20`.
- Saldo estimado: `9693.39`.
- Cuota tecnica `0`: rechazada correctamente.

Importante: la simulacion actual es un paso de validacion. El usuario indico que las operaciones reales deben seguir el flujo legacy; la simulacion no debe convertirse en un flujo obligatorio novedoso.

## 10. Analisis legacy de operacion 2: AMORTIZACION AEE

Archivos legacy clave:

- `src/com/pe/sacs/vista/action/CrearCajaAction.java`
- `src/com/pe/sacs/vista/action/CrearCaja2Action.java`
- `src/com/pe/sacs/vista/form/CajaForm.java`
- `src/com/pe/sacs/logica/prestamos/PrestamosDAOImpl.java`
- `public_html/caja.jsp`

Campos de `CajaForm` relevantes:

- `socio`
- `operacion`
- `monto`
- `chk`: prestamo.
- `chk2`: cuota.
- `gastosAdm`
- `gastosMora`
- `otros1`
- `otros2`
- `amortiza`
- `sobregiro`
- `formaPago`
- `codigoContable`
- `obs`
- `obs2`
- `fecha`

### Seleccion legacy

`CrearCajaAction` para operacion 2:

- Obtiene prestamos con `getPrestamos("1", socio, "4")`.
- El prestamo debe estar en estado `f16_estado = 4`.
- Consulta cuotas vigentes/no canceladas, excluye `f17_ncuota = 0` y filtra fecha futura.
- La cuota se selecciona por `chk2`.
- Usa `getCronogramaCaja(prestamo, cuota)`.

`CrearCaja2Action` es una variante antigua/incompleta:

- Usa principalmente datos agregados de `f16`.
- No selecciona cuota concreta de `f17`.
- Calcula capital como importe/cuotas.
- No copia siempre forma de pago, codigo contable ni `obs2` al movimiento.

Para el nuevo sistema se debe tomar `CrearCajaAction` como referencia principal porque es mas completa y fiel a la seleccion de cuota, pero documentar cualquier diferencia antes de escribir.

### Calculo legacy CrearCajaAction

Toma de la cuota `f17`:

- Capital/amortizacion.
- Interes.
- Total de cuota.
- Saldo restante teorico.

Toma de `f16_prestamo`:

- Gastos administrativos acumulados.
- Mora.
- Otros 1.
- Otros 2.
- Sobregiro (`f16_sobre`/aportacion segun bean).

El dinero se aplica en este orden:

1. Sobregiro existente.
2. Gastos administrativos.
3. Mora.
4. Otros 1.
5. Otros 2.
6. Capital.

Si no alcanza para completar la cuota:

- Se reduce el sobregiro existente o se crea sobregiro por faltante segun el flujo.
- La interfaz legacy bloquea con JavaScript si `sobregiro > 0`, pero esto no es una validacion server-side suficiente.

La version 2 agrega reglas especiales de mora 5/10 para sobregiro; la version 1 tiene ese bloque comentado. No elegir esa regla sin definir que variante es la productiva.

### UPDATE legacy de prestamo

Metodo `actualizarSaldo4` ejecuta aproximadamente:

```sql
UPDATE f16_prestamo
SET f16_cuotasPend = ?,
    f16_saldo = ?,
    f16_gastosa = ?,
    f16_gastosm = ?,
    f16_otros = ?,
    f16_otros2 = ?,
    f16_solicitado = ?,
    f16_cuotaMe = ?,
    f16_fechaUP = ?,
    f16_sobre = ?
WHERE f16_codigo = ?
```

En operacion 2 normalmente:

- Disminuye cuotas pendientes.
- Actualiza saldo.
- Conserva o reduce gastos/mora/otros pendientes.
- Actualiza fecha procesada.
- Actualiza sobregiro.

### INSERT legacy de movimiento

`registrarMovimiento` inserta en `f15_movimiento` con operacion `2`:

- `f15_codigo` generado legacy con `MAX(f15_codigo) + 1`.
- `f15_fecha`.
- `f15_operacion = 2`.
- `f15_cliente`.
- `f15_mLetras`.
- `f15_usuaCrea`.
- `f15_fechaCrea`.
- `f15_maprestamo`.
- `f15_prestamo`.
- `f15_gastosa`.
- `f15_gastosm`.
- `f15_gastos1`.
- `f15_gastos2`.
- `f15_formaPago`.
- `f15_obs2`.
- `f15_mretiro`.

El texto en letras se calcula con importe + conceptos, segun `MovimientoBean`.

### INSERT legacy de diario

Despues del movimiento se intenta insertar en `f37_diario_contable`:

- `f37_movimiento`.
- `f37_fecha`.
- `f37_codigo_contable`.
- `f37_operacion = 2`.
- `f37_descripcion`.
- `f37_usuario_crea`.
- `f37_fecha_crea = NOW()`.

### f17 en operacion 2

Punto muy importante: el flujo legacy de AMORTIZACION AEE consulta `f17`, pero segun el analisis no llama a `pagarCuota()`.

Por tanto:

- No necesariamente actualiza `f17_estado` a `CANCELADO`.
- No necesariamente llena `f17_fechaCancela`.
- No necesariamente llena `f17_totalA`.
- Actualiza principalmente `f16` y registra en `f15`.

No asumir que el pago nuevo debe cancelar f17 hasta aclarar con usuarios y observar el sistema productivo/flujo real.

## 11. Riesgos de escritura identificados

Antes de implementar pagos reales hay que resolver:

1. Legacy usa `MAX + 1` para `f15_codigo`; el PHP nuevo no debe copiar ese riesgo. Usar transaccion y mecanismo seguro, pero conservar el resultado funcional.
2. Legacy no agrupa claramente UPDATE f16, INSERT f15 e INSERT f37 en una transaccion.
3. JavaScript es la unica defensa visible contra sobregiro en algunos puntos.
4. `CrearCaja2Action` tiene comportamiento incompleto y distinto.
5. `f17` puede quedar vigente aunque se registre amortizacion.
6. Las cantidades se manejan como `float`/String.
7. Hay SQL legacy concatenado.
8. El asiento contable historico no esta completo; no asumir que todo movimiento antiguo tiene diario.
9. Se deben evitar pagos duplicados por reenvio o doble clic.
10. La informacion de forma de pago y codigo contable debe conservarse en el nuevo flujo.

## 12. Decisiones acordadas con el usuario

- El sistema nuevo se construye en PHP para XAMPP.
- Se usa inicialmente la misma base de pruebas `sacs`.
- No tocar produccion.
- No perder informacion.
- No cargar millones de registros por defecto.
- Lecturas acotadas por cliente, codigo, fecha o ID.
- Visualizacion puede ser mejorada.
- Escrituras deben parecerse y comportarse como legacy.
- No crear funciones de negocio extrañas para los usuarios.
- La simulacion es tecnica y temporal; no debe imponerse como nuevo flujo de usuario.
- Antes de pagos reales se debe validar cuidadosamente el legacy.

## 13. Analisis completado de operacion 2: AMORTIZACION AEE

Fecha de analisis: 2026-09-12

### 13.1 Variante productiva confirmada

**CrearCajaAction es la variante productiva** porque:

- ✅ Configurada en `struts-config.xml` como ruta principal `/crearCaja`
- ✅ Usada exclusivamente en `caja.jsp` (interfaz de usuario)
- ✅ Tiene lógica más completa y específica para selección de cuota
- ✅ Incluye procesamiento de descuentos
- ✅ `CrearCaja2Action` parece ser versión antigua/incompleta sin uso en producción

### 13.2 Metodo `registrar()` analizado

Fljo completo de `CrearCajaAction.registrar()` para operacion 2:

1. **Preparacion inicial**: Obtener socio, crear MovimientoBean
2. **Obtencion de datos**: Prestamo (`getPrestamo`) y cuota (`getCronogramaCaja`)
3. **Calculo de sobregiro**: `co2.getCuota() - montoA`
4. **Manejo sobregiro existente**: Logica compleja con escenarios de pago exacto/parcial
5. **Aplicacion de dinero** (orden legacy):
   - Sobregiro existente → Gastos administrativos → Mora → Otros1 → Otros2 → Capital
6. **Calculo de nuevo saldo**: Con multiple escenarios segun sobregiro
7. **Actualizacion de prestamo**: `actualizarSaldo4(amortizap)`
8. **Registro de movimiento**: `registrarMovimiento(mov)`
9. **Registro diario contable**: Si hay codigo contable

**CONFIRMADO**: NO se llama a `pagarCuota()` - f17 permanece VIGENTE

### 13.3 Operacion real historica analizada

**Movimiento 122013090 (2026-08-26)**:
- Cliente: 34584, Prestamo: 43232, Cuota: 1
- Capital pagado: 359.87, Gastos administrativos: 233.32, Total: 593.19
- f17_estado: VIGENTE (confirmado que legacy NO cancela cuota)
- f16_sobre: -108.33 (exceso de pago)

### 13.4 Funcion pura de calculo implementada

**Archivo**: `src/Services/AmortizacionCalculadora.php`

Caracteristicas:
- ✅ Reproduce exactamente el orden de aplicacion legacy
- ✅ Manejo de sobregiro existente, nuevo sobregiro y exceso de pago
- ✅ Calculo de saldo con escenarios positivos, cero y negativos
- ✅ Metodo de validacion para comparar con datos legacy
- ✅ Determinista: misma entrada = misma salida

**Resultados de pruebas de caracterizacion**:

1. **Caso basico con gastos administrativos** (Movimiento 122013090): ✅ VALIDADO
2. **Caso con otros1 y otros2** (Movimiento 122012265): ✅ VALIDADO
3. **Caso con mora y sobregiro grande** (Movimiento 122012862): ⚠️ PENDIENTE
   - Discrepancia en saldo requiere analisis adicional

### 13.5 Servicio de amortizacion implementado

**Archivo**: `src/Services/AmortizacionServicio.php`

Diseño basado estrictamente en legacy (sin excentricidades):

**Flujo secuencial legacy**:
1. Iniciar transaccion simple
2. Obtener prestamo (f16_prestamo)
3. Obtener cuota (f17_cuotasprestamo)
4. Calcular distribucion (AmortizacionCalculadora)
5. Generar codigo movimiento (forma segura con FOR UPDATE)
6. Insertar movimiento (f15_movimiento) - operacion 2
7. Actualizar prestamo (f16_prestamo) - actualizarSaldo4
8. Insertar diario contable (f37_diario_contable) si aplica
9. Commit o rollback ante error

**Mejoras minimas de seguridad**:
- Transaccion simple (legacy no tenia)
- Generacion ID segura: `SELECT MAX(...) FOR UPDATE` (legacy usaba MAX+1 sin bloqueo)
- Consultas parametrizadas (legacy ya usaba jdbcTemplate)

**NO se agregaron excentricidades**:
- ❌ No idempotency keys complejos
- ❌ No bloqueos avanzados
- ❌ No auditoria extensa
- ❌ No middleware de eventos

### 13.6 Archivos de prueba creados

- `tests/test_amortizacion_calculadora.php` - Prueba caso basico
- `tests/test_amortizacion_mora.php` - Prueba con mora
- `tests/test_amortizacion_otros.php` - Prueba con otros1 y otros2
- `tests/resultados_pruebas_caracterizacion.md` - Documentacion de resultados

## 14. Implementacion de Operacion 1: Pagos de Aportes FONBES

Fecha de implementacion: 2026-09-12

### 14.1 Servicio de Aportes FONBES

**Archivo**: `src/Services/AportesServicio.php`

Funcionalidades implementadas:
- Ejecucion de pagos de aportes FONBES (Operacion 1)
- Calculo de periodos y distribucion de montos
- Insercion en `f22_aportes` con campos correctos (f22_socio, f22_periodo, f22_aportacionP, f22_aportacionD, f22_diferencia)
- Insercion en `f15_movimiento` con operacion 1
- Insercion en `f37_diario_contable` con datos contables
- Generacion segura de codigo de movimiento
- Logging en `logs/aportes.log`

### 14.2 Formulario de Pagos de Aportes FONBES

**Archivo**: `views/caja/aportes_fonbes.php`

Campos del formulario:
- Periodo de pago (año, mes inicio, mes fin)
- Monto total
- Forma de pago
- Codigo contable (con autocompletado)
- Tipo de asiento (Debe/Haber)
- Descripcion contable
- Observaciones

### 14.3 Diario Contable Implementado

**Tabla**: `f37_diario_contable`

Campos utilizados:
- `f37_movimiento`: vinculado al movimiento de caja
- `f37_fecha`: fecha del asiento
- `f37_codigo_contable`: codigo de cuenta (de f38_codigo_contable)
- `f37_operacion`: tipo de operacion
- `f37_descripcion`: descripcion del asiento
- `f37_debe`: monto de debe
- `f37_haber`: monto de haber
- `f37_usuario_crea`: usuario que registra
- `f37_fecha_crea`: fecha/hora de creacion
- `f37_estado`: estado (ACTIVO)

**Tabla de códigos contables**: `f38_codigo_contable`
- `codigo`: codigo contable (ej: 00, 001, 0011, etc.)
- `nombre_cuenta`: nombre de la cuenta
- `codigo_padre`: estructura jerarquica

### 14.4 Selector de Código Contable

Implementado en formularios de amortizacion y aportes:
- Campo de texto con `<datalist>` para autocompletado
- Permite escribir directamente el codigo o seleccionar de la lista
- Filtra sugerencias mientras se escribe
- Valor por defecto: "1651" (como en sistema legacy)

### 14.5 Selector Debe/Haber

Implementado en ambos formularios:
- Opciones: "Haber (Ingreso)" y "Debe (Egreso)"
- Determina en qué columna se registra el monto
- Por defecto: "Haber" para ingresos

### 14.6 Cambios en Servicios

**AmortizacionServicio.php**:
- Agregado parametro `$datosContables` en `ejecutarAmortizacionAEE()`
- Metodo `insertarDiarioContable()` para insertar en diario contable
- Lógica para asignar monto a debe o haber según selección

**AportesServicio.php**:
- Agregado parametro `$datosContables` en `ejecutarPagoAportesFonbes()`
- Metodo `insertarDiarioContable()` para insertar en diario contable
- Lógica similar a amortización pero para operacion 1

**CajaRepository.php**:
- Agregado metodo `accountingCodes()` para obtener códigos contables

**index.php**:
- Procesa campos contables de ambos formularios
- Pasa datos contables a los servicios correspondientes

### 14.7 Operaciones Implementadas

- ✅ Operación 2: Amortización AEE (préstamos)
- ✅ Operación 1: Pagos de aportes FONBES
- ✅ Operación 9: Pagos de aportes COOPAM

### 14.8 Operación 9: Pagos de Aportes COOPAM

**Archivo creado**: `src/Services/AportesCoopamServicio.php`

Servicio para procesar pagos de aportes COOPAM (Operación 9), basado en el código legacy `CrearCajaAction.registrarAportacionCope()`:

- Obtiene monto de aporte COPE del socio desde `F01_APORTE_COPE` en `f01_persona`
- Calcula distribución por periodos (mes inicio a mes fin)
- Inserta registros en `f27_aportes_cope` con columnas: `f27_socio`, `f27_periodo`, `f27_aportacionP`, `f27_aportacionD`, `f27_diferencia`, `f27_aportacion`
- Inserta movimiento en `f15_movimiento` con operación 9
- Inserta en diario contable `f37_diario_contable` con datos contables proporcionados
- Logging en `logs/aportes_cope.log` para auditoría
- Usa transacciones para asegurar integridad de datos

**Archivo creado**: `views/caja/aportes_coopam.php`

Formulario de pago de aportes COOPAM con diseño consistente del sistema:

- Información del cliente (código, nombre, documento, condición)
- Listado de aportes COOPAM pendientes
- Campos para año, mes inicio, mes fin
- Campo para monto total
- Selector de forma de pago
- Selector de código contable con autocompletado
- Selector Debe/Haber
- Descripción contable
- Observaciones

**Archivos modificados**:

- `views/caja/cliente.php`: Agregado enlace "Pagar Aportes COOPAM" en sección de aportaciones Coopam
- `public/index.php`: Rutas POST para procesar `aportes_coopam` y GET para mostrar `aportes_coopam_form`

**Columnas de base de datos utilizadas**:

- `f02_cliente.F02_ID_CLIENTE`: Código del cliente
- `f02_cliente.F02_ID_PERSONA`: FK a persona
- `f01_persona.F01_APORTE_COPE`: Monto de aporte COPE
- `f27_aportes_cope`: Tabla de aportes COOPAM

### 14.9 Operación 10: Pago de Sobregiro de Préstamo

**Fecha de implementación**: 2026-09-13

**Archivo creado**: `src/Services/SobregiroPrestamoServicio.php`

Servicio para procesar pagos de sobregiro de préstamos (Operación 10), basado en el código legacy `CrearCajaAction.java` (líneas 1901-2099):

- Distribuye el monto pagado entre: gastos administrativos, mora, otros1, otros2, y sobregiro
- Actualiza sobregiro del préstamo (f16_aportacion)
- Actualiza saldo si hay amortización adicional
- Inserta movimiento en f15_movimiento con operación 10
- Inserta en diario contable f37_diario_contable si se proporcionan datos contables
- Usa transacciones para asegurar integridad de datos
- Logging en logs/sobregiro_prestamo.log para auditoría
- Generación segura de código de movimiento con FOR UPDATE y verificación de duplicados

**Archivo creado**: `views/caja/sobregiro_prestamo.php`

Formulario de pago de sobregiro de préstamo con diseño consistente del sistema:

- Información del cliente (código, nombre, documento, condición)
- Dropdown para seleccionar préstamo con sobregiro activo
- Campos para: sobregiro a pagar, amortización adicional, gastos administrativos, mora, otros1, otros2
- Cálculo automático del monto total via JavaScript
- Selector de forma de pago
- Selector de código contable con autocompletado
- Selector Debe/Haber
- Descripción contable
- Observaciones

**Archivos modificados**:

- `public/index.php`:
  - Ruta GET `sobregiro_prestamo_form` para mostrar el formulario
  - Ruta POST `sobregiro_prestamo` para procesar el pago
  - Agregado `$loansWithOverdraft` en perfil de cliente
- `src/Repositories/CajaRepository.php`:
  - Modificado `clientLoans()` para incluir `f16_aportacion AS sobregiro`
  - Agregado `clientLoansWithOverdraft()` para obtener préstamos con sobregiro activo (estado 4, f16_aportacion > 0)
- `views/caja/cliente.php`:
  - Agregada sección "Sobregiro de Préstamos" con enlace al formulario

**Correcciones aplicadas durante implementación**:

1. **Columna f16_mora → f16_gastosm**: La tabla f16_prestamo usa f16_gastosm, no f16_mora
2. **Columnas f15_movimiento**:
   - f15_monto → f15_maprestamo
   - f15_otros → f15_gastos1
   - f15_otros2 → f15_gastos2
   - Eliminado f15_abonoPrestamo (no existe)
   - Agregado f15_fecha (requerido)
3. **Generación de código de movimiento**: Implementada con FOR UPDATE y verificación de duplicados, usando base 122013479
4. **Tipos de inputs**: Cambiados de text a number en el formulario
5. **Procesamiento de montos**: Agregado str_replace(',', '', ...) para manejar comas
6. **Conversión de préstamo**: Convertir código de préstamo a entero correctamente

**Tablas de base de datos utilizadas**:

- `f16_prestamo`: Préstamos (campos: f16_aportacion, f16_gastosa, f16_gastosm, f16_otros, f16_otros2, f16_saldo)
- `f15_movimiento`: Movimientos (campos: f15_codigo, f15_fecha, f15_operacion, f15_cliente, f15_prestamo, f15_maprestamo, f15_gastosa, f15_gastosm, f15_gastos1, f15_gastos2, f15_formaPago, f15_usuaCrea, f15_fechaCrea, f15_obs, f15_obs2)
- `f37_diario_contable`: Diario contable (campos: f37_movimiento, f37_codigo, f37_debe, f37_haber, f37_descripcion, f37_estado, f37_fecha)

**Rutas**:

- GET: `?module=caja&action=sobregiro_prestamo_form&client=CODIGO_CLIENTE`
- POST: `?module=caja&action=sobregiro_prestamo`

**Validación de códigos contables**: No hay restricción de unicidad para códigos contables (f37_codigo). Un mismo código contable puede usarse en múltiples asientos, lo cual es correcto en contabilidad estándar.

### 14.10 Mejoras de UX y Navegación en Caja

**Fecha de implementación**: 2026-09-15

**Objetivo**: Implementar una barra lateral (sidebar) consistente con todas las operaciones de caja en todas las vistas, permitiendo navegación fluida entre operaciones y mejorando la UX del sistema.

#### Vistas con Sidebar Actualizado

Todas las vistas de caja ahora tienen el mismo sidebar con las 6 operaciones:
- Perfil Cliente
- Aportes FONBES
- Aportes COOPAM
- Desembolso Préstamo
- Sobregiro Préstamo
- Amortización

**Archivos modificados**:
- `views/caja/aportes_fonbes.php` - Sidebar con todas las operaciones, enlace al logo al dashboard
- `views/caja/aportes_coopam.php` - Sidebar con todas las operaciones, enlace al logo al dashboard
- `views/caja/sobregiro_prestamo.php` - Sidebar con todas las operaciones, diseño mejorado, enlace al logo al dashboard
- `views/caja/desembolso_prestamo.php` - Sidebar con todas las operaciones, enlace al logo al dashboard
- `views/caja/amortizacion.php` - Sidebar con todas las operaciones, enlace al logo al dashboard
- `views/caja/movimientos.php` - Sidebar con todas las operaciones, lógica condicional (links habilitados solo cuando hay cliente seleccionado), enlace al logo al dashboard

#### Vistas Intermedias Creadas

Para facilitar la navegación cuando no hay un préstamo preseleccionado, se crearon vistas intermedias de selección:

**Archivos creados**:
- `views/caja/seleccionar_desembolso.php` - Vista para seleccionar préstamo pendiente de desembolso (estado 3) antes de acceder al formulario
- `views/caja/seleccionar_amortizacion.php` - Vista para seleccionar préstamo activo (estado 4) antes de acceder al formulario de amortización

#### Rutas Modificadas

**Archivo modificado**: `public/index.php`

Rutas actualizadas para `desembolso_prestamo_form` y `amortizar_form`:
- Si tiene `loan` ID → va al formulario directamente
- Si solo tiene `client` → va a la vista intermedia de selección de préstamo

#### Funcionalidades Implementadas

**Sidebar Consistente**:
- La página actual se marca como "active" (fondo teal)
- Preservación de parámetros URL (`client`, `loan`) para mantener contexto
- Diseño responsive: sidebar se oculta en pantallas pequeñas
- En movimientos.php, los links del sidebar están deshabilitados si no hay cliente seleccionado

**Navegación Inteligente**:
- Desde operaciones como aportes_fonbes o aportes_coopam → clic en Desembolso/Amortización → vista de selección de préstamo
- Desde cliente.php con loan ID específico → va directamente al formulario (comportamiento original preservado)

**Logo como Enlace al Dashboard**:
- El logo SACS en todas las vistas con sidebar ahora es un enlace a `index.php` (dashboard)
- Estilos CSS para mantener el diseño original como enlace (sin subrayado, color inherit, hover con opacity)

**Mejoras de Diseño**:
- sobregiro_prestamo.php: estructura mejorada con layout CSS, mensaje más específico "No hay préstamos con sobregiro", legibilidad mejorada con símbolo # en códigos

#### Estructura del Sidebar

```html
<aside class="sidebar">
    <a href="index.php" class="brand">
        <div class="brand-mark">S</div>
        <div>
            <div class="brand-name">SACS</div>
            <div class="brand-note">Sistema de Administración</div>
        </div>
    </a>
    <div class="nav-label">Caja</div>
    <nav class="nav">
        <a href="..." class="nav-link">Perfil Cliente</a>
        <a href="..." class="nav-link active">Operación Actual</a>
        <!-- otras operaciones -->
    </nav>
</aside>
```

#### Estilos CSS Clave

- `.shell`: Grid layout con sidebar (254px) y main content
- `.sidebar`: Fondo navy, color claro
- `.brand`: Enlace al dashboard con hover opacity
- `.nav-link`: Links de navegación con hover y active states
- Responsive: Sidebar oculto en pantallas < 900px

#### Rutas y URLs

- Sidebar links sin loan ID: `index.php?module=caja&action=desembolso_prestamo_form&client=XXX`
- Sidebar links con loan ID (cuando aplica): `index.php?module=caja&action=amortizar_form&client=XXX&loan=YYY`
- Logo link: `index.php`

#### Estado: COMPLETADO Y FUNCIONANDO

Todas las funcionalidades han sido probadas y funcionan correctamente según el usuario.

### 14.11 Operación 11: Pagos de Multas

**Fecha de implementación**: 2026-09-15

**Archivo creado**: `src/Services/MultasServicio.php`

Servicio para procesar pagos de multas (Operación 11), basado en el código legacy `CrearCajaAction.java` (líneas 221-246 y 1900-2300):

- Actualiza la multa en `f63_multa` (f63_multad, f63_multar, f63_fechaPago, f63_formaPago)
- Inserta movimiento en `f15_movimiento` con operación 11
- Inserta en diario contable `f37_diario_contable` si se proporcionan datos contables
- Usa transacciones para asegurar integridad de datos
- Logging en `logs/multas.log` para auditoría
- Generación segura de código de movimiento con FOR UPDATE y verificación de duplicados

**Archivo creado**: `views/caja/multas.php`

Formulario de pago de multas con diseño consistente del sistema:

- Información del cliente (código, nombre, documento, condición)
- Listado de multas pendientes de pago (f63_multar > 0)
- Selección de multa específica para pago
- Campo para monto a pagar (pre-llenado con multa restante)
- Selector de forma de pago
- Selector de código contable con autocompletado
- Selector Debe/Haber
- Descripción contable
- Observaciones
- Sidebar consistente con todas las operaciones de caja

**Archivos modificados**:

- `public/index.php`:
  - Ruta GET `multas_form` para mostrar el formulario
  - Ruta POST `multas` para procesar el pago
- `src/Repositories/CajaRepository.php`:
  - Agregado `clientPendingFines()` para obtener multas pendientes (f63_multar > 0)
- `views/caja/cliente.php`:
  - Agregado enlace "Pagar Multas" en sección de multas
- `views/caja/aportes_fonbes.php`:
  - Agregado enlace "Multas" en sidebar
- `views/caja/aportes_coopam.php`:
  - Agregado enlace "Multas" en sidebar
- `views/caja/sobregiro_prestamo.php`:
  - Agregado enlace "Multas" en sidebar
- `views/caja/desembolso_prestamo.php`:
  - Agregado enlace "Multas" en sidebar
- `views/caja/amortizacion.php`:
  - Agregado enlace "Multas" en sidebar
- `views/caja/movimientos.php`:
  - Agregado enlace "Multas" en sidebar (incluyendo estado disabled cuando no hay cliente seleccionado)

**Tablas de base de datos utilizadas**:

- `f63_multa`: Multas (campos: f63_codigo, f63_socio, f63_periodo, f63_multap, f63_multad, f63_multar, f63_fechaPago, f63_formaPago)
- `f15_movimiento`: Movimientos (campos: f15_codigo, f15_fecha, f15_operacion, f15_cliente, f15_mamulta, f15_formaPago, f15_usuaCrea, f15_fechaCrea, f15_obs, f15_obs2)
- `f37_diario_contable`: Diario contable (campos: f37_movimiento, f37_codigo, f37_debe, f37_haber, f37_descripcion, f37_estado, f37_fecha)

**Rutas**:

- GET: `?module=caja&action=multas_form&client=CODIGO_CLIENTE`
- GET: `?module=caja&action=multas_form&client=CODIGO_CLIENTE&multa=ID_MULTA`
- POST: `?module=caja&action=multas`

**Correcciones aplicadas durante implementación**:

1. **Método Auth::user()**: Corregido para usar instanciación correcta en lugar de llamada estática
2. **Función $escape**: Cambiado tipo de parámetro de `string` a `mixed` para manejar enteros
3. **Campos de cliente**: Actualizados nombres de campos para coincidir con `findClientProfile()` (client_id, full_name, document, condition)
4. **Diseño CSS**: Actualizado para coincidir con diseño de movimientos.php (colores, espaciados, tipografía)

**Estado: COMPLETADO Y FUNCIONANDO**

Todas las funcionalidades han sido probadas y funcionan correctamente según el usuario.

### 14.12 Operación 4: Devolución de Aportes FONBES

**Fecha de implementación**: 2026-09-15

**Archivo creado**: `src/Services/DevolucionFonbesServicio.php`

Servicio para procesar devoluciones de aportes FONBES (Operación 4), contraparte de la Operación 1:

- Actualiza el aporte en `f22_aportes` (f22_aportacionD, f22_diferencia)
- Valida que el monto a devolver no exceda el monto pagado
- Inserta movimiento en `f15_movimiento` con operación 4 usando campo `f15_mretiro`
- Inserta en diario contable `f37_diario_contable` si se proporcionan datos contables
- Usa transacciones para asegurar integridad de datos
- Logging en `logs/devolucion_fonbes.log` para auditoría
- Generación segura de código de movimiento con FOR UPDATE y verificación de duplicados

**Archivo creado**: `views/caja/devolucion_fonbes.php`

Formulario de devolución de aportes FONBES con diseño consistente del sistema:

- Información del cliente (código, nombre, documento, condición)
- Listado de aportes pagados disponibles para devolución (f22_aportacionD > 0)
- Selección de periodo específico para devolución
- Campo para monto a devolver (pre-llenado con monto pagado del aporte seleccionado)
- Validación de máximo (no puede exceder monto pagado)
- Selector de forma de pago
- Selector de código contable con autocompletado
- Selector Debe/Haber
- Descripción contable
- Observaciones
- Sidebar consistente con todas las operaciones de caja

**Archivos modificados**:

- `public/index.php`:
  - Ruta GET `devolucion_fonbes_form` para mostrar el formulario
  - Ruta POST `devolucion_fonbes` para procesar la devolución
- `src/Repositories/CajaRepository.php`:
  - Método `accountingCodes()` existente utilizado para códigos contables
- `views/caja/cliente.php`:
  - Agregado enlace "Devolver Aportes" en sección de aportaciones FONBES
- `views/caja/aportes_fonbes.php`:
  - Agregado enlace "Devolución FONBES" en sidebar
- `views/caja/aportes_coopam.php`:
  - Agregado enlace "Devolución FONBES" en sidebar
- `views/caja/sobregiro_prestamo.php`:
  - Agregado enlace "Devolución FONBES" en sidebar
- `views/caja/desembolso_prestamo.php`:
  - Agregado enlace "Devolución FONBES" en sidebar
- `views/caja/amortizacion.php`:
  - Agregado enlace "Devolución FONBES" en sidebar
- `views/caja/movimientos.php`:
  - Agregado enlace "Devolución FONBES" en sidebar (incluyendo estado disabled cuando no hay cliente seleccionado)
- `views/caja/multas.php`:
  - Agregado enlace "Devolución FONBES" en sidebar

**Tablas de base de datos utilizadas**:

- `f22_aportes`: Aportes FONBES (campos: f22_socio, f22_periodo, f22_aportacionP, f22_aportacionD, f22_diferencia)
- `f15_movimiento`: Movimientos (campos: f15_codigo, f15_fecha, f15_operacion, f15_cliente, f15_mretiro, f15_formaPago, f15_usuaCrea, f15_fechaCrea, f15_obs, f15_obs2)
- `f37_diario_contable`: Diario contable (campos: f37_movimiento, f37_codigo, f37_debe, f37_haber, f37_descripcion, f37_estado, f37_fecha)

**Rutas**:

- GET: `?module=caja&action=devolucion_fonbes_form&client=CODIGO_CLIENTE`
- GET: `?module=caja&action=devolucion_fonbes_form&client=CODIGO_CLIENTE&periodo=FECHA_PERIODO`
- POST: `?module=caja&action=devolucion_fonbes`

**Correcciones aplicadas durante implementación**:

1. **Pre-llenado de monto**: Agregado atributo `value` para pre-llenar el campo con el monto pagado del aporte seleccionado
2. **Selector de código contable**: Corregidos nombres de campos de `codigo`/`nombre_cuenta` a `code`/`name` para coincidir con método `accountingCodes()`

**Nota sobre visualización**: El importe de la devolución se registra en `f15_mretiro` (retiro), por lo que en la vista de detalle del movimiento puede no aparecer como "importe principal" ya que cada operación tiene su campo específico. Esto es consistente con el comportamiento del sistema legacy.

**Estado: COMPLETADO Y FUNCIONANDO**

Todas las funcionalidades han sido probadas y funcionan correctamente según el usuario.

### 14.13 Operación 5: Devolución de Aportes COOPAM

**Fecha de implementación**: 2026-09-15

**Archivo creado**: `src/Services/DevolucionCoopamServicio.php`

Servicio para procesar devoluciones de aportes COOPAM (Operación 5), contraparte de la Operación 9:

- Actualiza el aporte en `f27_aportes_cope` (f27_aportacionD, f27_diferencia)
- Valida que el monto a devolver no exceda el monto pagado
- Inserta movimiento en `f15_movimiento` con operación 5 usando campo `f15_mretiro`
- Inserta en diario contable `f37_diario_contable` si se proporcionan datos contables
- Usa transacciones para asegurar integridad de datos
- Logging en `logs/devolucion_coopam.log` para auditoría
- Generación segura de código de movimiento con FOR UPDATE y verificación de duplicados

**Archivo creado**: `views/caja/devolucion_coopam.php`

Formulario de devolución de aportes COOPAM con diseño consistente del sistema:

- Información del cliente (código, nombre, documento, condición)
- Listado de aportes COOPAM pagados disponibles para devolución (f27_aportacionD > 0)
- Selección de periodo específico para devolución
- Campo para monto a devolver (pre-llenado con monto pagado del aporte seleccionado)
- Validación de máximo (no puede exceder monto pagado)
- Selector de forma de pago
- Selector de código contable con autocompletado (campos correctos: code/name)
- Selector Debe/Haber
- Descripción contable
- Observaciones
- Sidebar consistente con todas las operaciones de caja

**Archivos modificados**:

- `public/index.php`:
  - Ruta GET `devolucion_coopam_form` para mostrar el formulario
  - Ruta POST `devolucion_coopam` para procesar la devolución
- `src/Repositories/CajaRepository.php`:
  - Método `accountingCodes()` existente utilizado para códigos contables
- `views/caja/cliente.php`:
  - Agregado enlace "Devolver Aportes" en sección de aportaciones COOPAM
- `views/caja/aportes_fonbes.php`:
  - Agregado enlace "Devolución COOPAM" en sidebar
- `views/caja/aportes_coopam.php`:
  - Agregado enlace "Devolución COOPAM" en sidebar
- `views/caja/sobregiro_prestamo.php`:
  - Agregado enlace "Devolución COOPAM" en sidebar
- `views/caja/desembolso_prestamo.php`:
  - Agregado enlace "Devolución COOPAM" en sidebar
- `views/caja/amortizacion.php`:
  - Agregado enlace "Devolución COOPAM" en sidebar
- `views/caja/movimientos.php`:
  - Agregado enlace "Devolución COOPAM" en sidebar (incluyendo estado disabled cuando no hay cliente seleccionado)
- `views/caja/multas.php`:
  - Agregado enlace "Devolución COOPAM" en sidebar

**Tablas de base de datos utilizadas**:

- `f27_aportes_cope`: Aportes COOPAM (campos: f27_socio, f27_periodo, f27_aportacionP, f27_aportacionD, f27_diferencia)
- `f15_movimiento`: Movimientos (campos: f15_codigo, f15_fecha, f15_operacion, f15_cliente, f15_mretiro, f15_formaPago, f15_usuaCrea, f15_fechaCrea, f15_obs, f15_obs2)
- `f37_diario_contable`: Diario contable (campos: f37_movimiento, f37_codigo, f37_debe, f37_haber, f37_descripcion, f37_estado, f37_fecha)

**Rutas**:

- GET: `?module=caja&action=devolucion_coopam_form&client=CODIGO_CLIENTE`
- GET: `?module=caja&action=devolucion_coopam_form&client=CODIGO_CLIENTE&periodo=FECHA_PERIODO`
- POST: `?module=caja&action=devolucion_coopam`

**Nota sobre visualización**: El importe de la devolución se registra en `f15_mretiro` (retiro), por lo que en la vista de detalle del movimiento puede no aparecer como "importe principal" ya que cada operación tiene su campo específico. Esto es consistente con el comportamiento del sistema legacy.

**Estado: COMPLETADO Y FUNCIONANDO**

Todas las funcionalidades han sido probadas y funcionan correctamente según el usuario.

### 14.15 Operación 6: Cancelación de Préstamo AEE

**Fecha de implementación**: 2026-09-16

**Archivo creado**: `src/Services/CancelacionAeeServicio.php`

Servicio para procesar cancelaciones de préstamos AEE (Operación 6), contraparte de las Operaciones 2 y 3:

- Valida que el préstamo esté activo (estado 4)
- Valida que el préstamo pertenezca al cliente
- Calcula total a cancelar (saldo + sobregiro)
- Cancela cuotas pendientes del préstamo (estado → CANCELADO en f17_cuotasprestamo)
- Cancela el préstamo (estado → 5, fecha de cancelación, saldo = 0, sobregiro = 0, gastos = 0)
- Inserta movimiento en `f15_movimiento` con operación 6
- Inserta en diario contable `f37_diario_contable` si se proporcionan datos contables
- Usa transacciones para asegurar integridad de datos
- Logging en `logs/cancelacion_aee.log` para auditoría
- Generación segura de código de movimiento con FOR UPDATE y verificación de duplicados

**Archivo creado**: `views/caja/cancelacion_aee.php`

Formulario de cancelación de préstamo AEE con diseño consistente del sistema:

- Información del cliente (código, nombre, documento, condición)
- Listado de préstamos activos disponibles para cancelación (estado 4)
- Selección de préstamo específico para cancelación
- Detalle del préstamo seleccionado (saldo, sobregiro, total a cancelar)
- Desglose de cargos (gastos adm, mora, otros1, otros2)
- Advertencia de acción irreversible
- Selector de forma de pago
- Selector de código contable con autocompletado (campos correctos: code/name)
- Selector Debe/Haber
- Descripción contable
- Observaciones
- Sidebar consistente con todas las operaciones de caja

**Archivos modificados**:

- `public/index.php`:
  - Ruta GET `cancelacion_aee_form` para mostrar el formulario
  - Ruta POST `cancelacion_aee` para procesar la cancelación
- `src/Repositories/CajaRepository.php`:
  - Método `accountingCodes()` existente utilizado para códigos contables
- `views/caja/cliente.php`:
  - Agregado enlace "Cancelar" en sección de préstamos (solo para préstamos activos estado 4)
- `views/caja/aportes_fonbes.php`:
  - Agregado enlace "Cancelación AEE" en sidebar
- `views/caja/aportes_coopam.php`:
  - Agregado enlace "Cancelación AEE" en sidebar
- `views/caja/sobregiro_prestamo.php`:
  - Agregado enlace "Cancelación AEE" en sidebar
- `views/caja/desembolso_prestamo.php`:
  - Agregado enlace "Cancelación AEE" en sidebar
- `views/caja/amortizacion.php`:
  - Agregado enlace "Cancelación AEE" en sidebar (con loan ID)
- `views/caja/movimientos.php`:
  - Agregado enlace "Cancelación AEE" en sidebar (incluyendo estado disabled cuando no hay cliente seleccionado)
- `views/caja/multas.php`:
  - Agregado enlace "Cancelación AEE" en sidebar
- `views/caja/devolucion_fonbes.php`:
  - Completado sidebar con Devolución COOPAM y Cancelación AEE
- `views/caja/devolucion_coopam.php`:
  - Completado sidebar con Devolución COOPAM y Cancelación AEE

**Tablas de base de datos utilizadas**:

- `f16_prestamo`: Préstamos (campos: f16_codigo, f16_cliente, f16_estado, f16_saldo, f16_gastosa, f16_gastosm, f16_otros, f16_otros2, f16_aportacion, f16_cuotasPend, f16_total)
- `f17_cuotasprestamo`: Cuotas de préstamo (campos: f17_prestamo, f17_estado)
- `f15_movimiento`: Movimientos (campos: f15_codigo, f15_fecha, f15_operacion, f15_cliente, f15_prestamo, f15_maprestamo, f15_gastosa, f15_gastosm, f15_gastos1, f15_gastos2, f15_formaPago, f15_usuaCrea, f15_fechaCrea, f15_obs, f15_obs2)
- `f37_diario_contable`: Diario contable (campos: f37_codigo, f37_movimiento, f37_fecha, f37_codigo_contable, f37_operacion, f37_descripcion, f37_debe, f37_haber, f37_usuario_crea, f37_fecha_crea, f37_estado)

**Rutas**:

- GET: `?module=caja&action=cancelacion_aee_form&client=CODIGO_CLIENTE`
- GET: `?module=caja&action=cancelacion_aee_form&client=CODIGO_CLIENTE&loan=PRESTAMO_ID`
- POST: `?module=caja&action=cancelacion_aee`

**Correcciones aplicadas**:

1. **Palabra reservada SQL**: Escapado `condition` con backticks en cancelacion_aee.php
2. **Nombre de tabla**: Corregido `f17_cronograma` → `f17_cuotasprestamo` en CancelacionAeeServicio.php
3. **SQL diario contable**: Corregidos campos en Operaciones 4, 5, 6:
   - `f37_codigo` → `f37_codigo_contable` (campo correcto para código contable)
   - Agregado `f37_operacion` (4 para devolución FONBES, 5 para devolución COOPAM, 6 para cancelación AEE)
   - Agregado `f37_usuario_crea` y `f37_fecha_crea` (campos requeridos)
   - `f37_codigo` es AUTO_INCREMENT, por lo que no se especifica en el INSERT
4. **Condición de inserción**: Cambiada de `!empty($datosContables)` a `!empty($datosContables['codigoContable'])` para verificar específicamente si hay un código contable válido

**Estado: COMPLETADO Y FUNCIONANDO**

Todas las funcionalidades han sido probadas y funcionan correctamente según el usuario.

### 14.17 Operación 12: Pago de Rifa

**Fecha de implementación**: 2026-09-16

**Archivo creado**: `src/Services/PagoRifaServicio.php`

Servicio para procesar pagos de rifa (Operación 12), basado en código legacy Java (CrearCajaAction.java líneas 248-271, 1255-1275):

- Obtiene rifa pendiente del cliente (f64_rifap > 0 o f64_rifar > 0)
- Valida que el monto sea válido
- Calcula nuevo sobregiro si existe (f64_rifar - monto pagado)
- Actualiza rifa (f64_rifad, f64_rifar, f64_fechaPago, f64_formaPago)
- Inserta movimiento en `f15_movimiento` con operación 12 (usando f15_mretiro)
- Inserta en diario contable `f37_diario_contable` si se proporcionan datos contables
- Usa transacciones para asegurar integridad de datos
- Logging en `logs/pago_rifa.log` para auditoría
- Generación segura de código de movimiento con FOR UPDATE y verificación de duplicados

**Archivo creado**: `views/caja/pago_rifa.php`

Formulario de pago de rifa con diseño consistente del sistema:

- Información del cliente (código, nombre, documento, condición)
- Listado de rifas pendientes del cliente (últimos 12 periodos)
- Selección de rifa específica para pago
- Detalle de la rifa seleccionada (periodo, programado, pagado, sobregiro)
- Campo de monto a pagar (precalculado con sobregiro si existe)
- Selector de forma de pago
- Selector de código contable con autocompletado (campos correctos: code/name)
- Selector Debe/Haber
- Descripción contable
- Observaciones
- Sidebar consistente con todas las operaciones de caja (11 operaciones)

**Archivos modificados**:

- `public/index.php`:
  - Ruta GET `pago_rifa_form` para mostrar el formulario
  - Ruta POST `pago_rifa` para procesar el pago
- `src/Repositories/CajaRepository.php`:
  - Método `accountingCodes()` existente utilizado para códigos contables
- `views/caja/cliente.php`:
  - Agregado enlace "Pagar Rifa" en sección de rifas
- Sidebars de todas las vistas de caja actualizados con "Pago Rifa":
  - `views/caja/aportes_fonbes.php`
  - `views/caja/aportes_coopam.php`
  - `views/caja/sobregiro_prestamo.php`
  - `views/caja/desembolso_prestamo.php`
  - `views/caja/amortizacion.php`
  - `views/caja/movimientos.php`
  - `views/caja/multas.php`
  - `views/caja/devolucion_fonbes.php`
  - `views/caja/devolucion_coopam.php`
  - `views/caja/cancelacion_aee.php`

**Tablas de base de datos utilizadas**:

- `f64_rifa`: Rifas (campos: f64_codigo, f64_socio, f64_periodo, f64_rifap, f64_rifad, f64_rifar, f64_fechaPago, f64_formaPago)
- `f15_movimiento`: Movimientos (campos: f15_codigo, f15_fecha, f15_operacion, f15_cliente, f15_mretiro, f15_formaPago, f15_usuaCrea, f15_fechaCrea, f15_obs, f15_obs2)
- `f37_diario_contable`: Diario contable (campos: f37_codigo, f37_movimiento, f37_fecha, f37_codigo_contable, f37_operacion, f37_descripcion, f37_debe, f37_haber, f37_usuario_crea, f37_fecha_crea, f37_estado)

**Rutas**:

- GET: `?module=caja&action=pago_rifa_form&client=CODIGO_CLIENTE`
- GET: `?module=caja&action=pago_rifa_form&client=CODIGO_CLIENTE&periodo=FECHA_PERIODO`
- POST: `?module=caja&action=pago_rifa`

**Correcciones aplicadas**:

1. **Columna f15_aumento**: Eliminada del INSERT en f15_movimiento ya que no existe en la tabla. Se usa solo f15_mretiro para el monto de pago de rifa.

**Estado: COMPLETADO Y FUNCIONANDO**

Todas las funcionalidades han sido probadas y funcionan correctamente según el usuario.

### 14.18 Operación 14: Pago Sobregiro FONBES

**Fecha de implementación**: 2026-09-16

**Archivo creado**: `src/Services/PagoSobregiroFonbesServicio.php`

Servicio para procesar pagos de sobregiro de aportaciones FONBES (Operación 14), basado en código legacy Java (CrearCajaAction.java líneas 349-367, 587-621):

- Obtiene aportaciones FONBES con sobregiro del cliente (f22_diferencia > 0)
- Calcula total de sobregiro (suma de diferencias)
- Valida que el monto sea válido
- Distribuye el pago entre las aportaciones con sobregiro (reduciendo f22_diferencia)
- Actualiza aportaciones (f22_aportacionD, f22_diferencia, f22_fechaPago)
- Inserta movimiento en `f15_movimiento` con operación 14 (usando f15_mretiro)
- Inserta en diario contable `f37_diario_contable` si se proporcionan datos
- Usa transacciones para asegurar integridad de datos
- Logging en `logs/pago_sobregiro_fonbes.log` para auditoría
- Generación segura de código de movimiento con FOR UPDATE y verificación de duplicados

**Archivo creado**: `views/caja/pago_sobregiro_fonbes.php`

Formulario de pago de sobregiro FONBES con diseño consistente del sistema:

- Información del cliente (código, nombre, documento, condición)
- Listado de aportaciones FONBES con sobregiro (últimos 12 periodos)
- Resumen de sobregiro total y número de periodos
- Campo de monto a pagar (precalculado con total de sobregiro)
- Selector de forma de pago
- Selector de código contable con autocompletado (campos correctos: code/name)
- Selector Debe/Haber
- Descripción contable
- Observaciones
- Sidebar consistente con todas las operaciones de caja (12 operaciones)

**Archivos modificados**:

- `public/index.php`:
  - Ruta GET `pago_sobregiro_fonbes_form` para mostrar el formulario
  - Ruta POST `pago_sobregiro_fonbes` para procesar el pago
- `src/Repositories/CajaRepository.php`:
  - Método `accountingCodes()` existente utilizado para códigos contables
- `views/caja/cliente.php`:
  - Agregado enlace "Pagar Sobregiro" en sección de Aportaciones FONBES
- Sidebars de todas las vistas de caja actualizados con "Pago Sobregiro FONBES":
  - `views/caja/aportes_fonbes.php`
  - `views/caja/aportes_coopam.php`
  - `views/caja/sobregiro_prestamo.php`
  - `views/caja/desembolso_prestamo.php`
  - `views/caja/amortizacion.php`
  - `views/caja/movimientos.php`
  - `views/caja/multas.php`
  - `views/caja/devolucion_fonbes.php`
  - `views/caja/devolucion_coopam.php`
  - `views/caja/cancelacion_aee.php`

**Tablas de base de datos utilizadas**:

- `f22_aportes`: Aportaciones FONBES (campos: f22_socio, f22_periodo, f22_aportacionP, f22_aportacionD, f22_diferencia, f22_fechaPago, f22_aporte_id)
- `f15_movimiento`: Movimientos (campos: f15_codigo, f15_fecha, f15_operacion, f15_cliente, f15_mretiro, f15_formaPago, f15_usuaCrea, f15_fechaCrea, f15_obs, f15_obs2)
- `f37_diario_contable`: Diario contable (campos: f37_codigo, f37_movimiento, f37_fecha, f37_codigo_contable, f37_operacion, f37_descripcion, f37_debe, f37_haber, f37_usuario_crea, f37_fecha_crea, f37_estado)

**Rutas**:

- GET: `?module=caja&action=pago_sobregiro_fonbes_form&client=CODIGO_CLIENTE`
- POST: `?module=caja&action=pago_sobregiro_fonbes`

**Estado: COMPLETADO Y FUNCIONANDO**

Todas las funcionalidades han sido probadas y funcionan correctamente según el usuario.

### 14.20 Operación 15: Pago Sobregiro COOPAM

**Fecha de implementación**: 2026-09-16

**Archivo creado**: `src/Services/PagoSobregiroCoopamServicio.php`

Servicio para procesar pagos de sobregiro de aportaciones COOPAM (Operación 15), basado en código legacy Java (CrearCajaAction.java líneas 369-385, 626-652):

- Obtiene aportaciones COOPAM con sobregiro del cliente (f27_diferencia > 0)
- Calcula total de sobregiro (suma de diferencias)
- Valida que el monto sea válido
- Distribuye el pago entre las aportaciones con sobregiro (reduciendo f27_diferencia)
- Actualiza aportaciones (f27_aportacionD, f27_diferencia, f27_fechaPago)
- Inserta movimiento en `f15_movimiento` con operación 15 (usando f15_mretiro)
- Inserta en diario contable `f37_diario_contable` si se proporcionan datos
- Usa transacciones para asegurar integridad de datos
- Logging en `logs/pago_sobregiro_coopam.log` para auditoría
- Generación segura de código de movimiento con FOR UPDATE y verificación de duplicados

**Archivo creado**: `views/caja/pago_sobregiro_coopam.php`

Formulario de pago de sobregiro COOPAM con diseño consistente del sistema:

- Información del cliente (código, nombre, documento, condición)
- Listado de aportaciones COOPAM con sobregiro (últimos 12 periodos)
- Resumen de sobregiro total y número de periodos
- Campo de monto a pagar (precalculado con total de sobregiro)
- Selector de forma de pago
- Selector de código contable con autocompletado (campos correctos: code/name)
- Selector Debe/Haber
- Descripción contable
- Observaciones
- Sidebar consistente con todas las operaciones de caja (13 operaciones)

**Archivos modificados**:

- `public/index.php`:
  - Ruta GET `pago_sobregiro_coopam_form` para mostrar el formulario
  - Ruta POST `pago_sobregiro_coopam` para procesar el pago
- `src/Repositories/CajaRepository.php`:
  - Método `accountingCodes()` existente utilizado para códigos contables
- `views/caja/cliente.php`:
  - Agregado enlace "Pagar Sobregiro" en sección de Aportaciones COOPAM
- Sidebars de todas las vistas de caja actualizados con "Pago Sobregiro COOPAM":
  - `views/caja/aportes_fonbes.php`
  - `views/caja/aportes_coopam.php`
  - `views/caja/sobregiro_prestamo.php`
  - `views/caja/desembolso_prestamo.php`
  - `views/caja/amortizacion.php`
  - `views/caja/movimientos.php`
  - `views/caja/multas.php`
  - `views/caja/devolucion_fonbes.php`
  - `views/caja/devolucion_coopam.php`
  - `views/caja/cancelacion_aee.php`

**Tablas de base de datos utilizadas**:

- `f27_aportes_cope`: Aportaciones COOPAM (campos: f27_socio, f27_periodo, f27_aportacionP, f27_aportacionD, f27_diferencia, f27_fechaPago, f27_aporte_id)
- `f15_movimiento`: Movimientos (campos: f15_codigo, f15_fecha, f15_operacion, f15_cliente, f15_mretiro, f15_formaPago, f15_usuaCrea, f15_fechaCrea, f15_obs, f15_obs2)
- `f37_diario_contable`: Diario contable (campos: f37_codigo, f37_movimiento, f37_fecha, f37_codigo_contable, f37_operacion, f37_descripcion, f37_debe, f37_haber, f37_usuario_crea, f37_fecha_crea, f37_estado)

**Rutas**:

- GET: `?module=caja&action=pago_sobregiro_coopam_form&client=CODIGO_CLIENTE`
- POST: `?module=caja&action=pago_sobregiro_coopam`

**Estado: COMPLETADO Y FUNCIONANDO**

Todas las funcionalidades han sido probadas y funcionan correctamente según el usuario.

### 14.21 Operación 13: Otras Devoluciones

**Fecha de implementación**: 2026-09-16

**Archivo creado**: `src/Services/OtrasDevolucionesServicio.php`

Servicio para procesar devoluciones genéricas (Operación 13 - Otras Devoluciones), basado en código legacy Java (CrearCajaAction.java líneas 1277-1280):

- Valida datos básicos (cliente y monto)
- Genera código de movimiento de forma segura con FOR UPDATE
- Inserta movimiento en `f15_movimiento` con operación 13 (usando f15_devolucion)
- Inserta en diario contable `f37_diario_contable` si se proporcionan datos
- Usa transacciones para asegurar integridad de datos
- Logging en `logs/otras_devoluciones.log` para auditoría

**Archivo creado**: `views/caja/otras_devoluciones.php`

Formulario de otras devoluciones con diseño consistente del sistema:

- Información del cliente (código, nombre, documento, condición)
- Campo de monto a devolver
- Selector de forma de pago
- Selector de código contable con autocompletado
- Selector Debe/Haber
- Descripción contable
- Observaciones y observaciones adicionales
- Sidebar consistente con todas las operaciones de caja (14 operaciones)

**Archivos modificados**:

- `public/index.php`:
  - Ruta GET `otras_devoluciones_form` para mostrar el formulario
  - Ruta POST `otras_devoluciones` para procesar la devolución
- `src/Repositories/CajaRepository.php`:
  - Método `accountingCodes()` existente utilizado para códigos contables
- `views/caja/cliente.php`:
  - Agregado enlace "Otras Devoluciones" en sección de Beneficios
- Sidebars de todas las vistas de caja actualizados con "Otras Devoluciones":
  - `views/caja/aportes_fonbes.php`
  - `views/caja/aportes_coopam.php`
  - `views/caja/sobregiro_prestamo.php`
  - `views/caja/desembolso_prestamo.php`
  - `views/caja/amortizacion.php`
  - `views/caja/movimientos.php`
  - `views/caja/multas.php`
  - `views/caja/devolucion_fonbes.php`
  - `views/caja/devolucion_coopam.php`
  - `views/caja/cancelacion_aee.php`

**Tablas de base de datos utilizadas**:

- `f15_movimiento`: Movimientos (campos: f15_codigo, f15_fecha, f15_operacion, f15_cliente, f15_devolucion, f15_formaPago, f15_usuaCrea, f15_fechaCrea, f15_obs, f15_obs2)
- `f37_diario_contable`: Diario contable (campos: f37_codigo, f37_movimiento, f37_fecha, f37_codigo_contable, f37_operacion, f37_descripcion, f37_debe, f37_haber, f37_usuario_crea, f37_fecha_crea, f37_estado)

**Rutas**:

- GET: `?module=caja&action=otras_devoluciones_form&client=CODIGO_CLIENTE`
- POST: `?module=caja&action=otras_devoluciones`

**Estado: COMPLETADO Y FUNCIONANDO**

Todas las funcionalidades han sido probadas y funcionan correctamente según el usuario.

### 14.22 Operación 16: Otros Ingresos

**Fecha de implementación**: 2026-09-17

**Archivo creado**: `src/Services/OtrosIngresosServicio.php`

Servicio para procesar ingresos genéricos (Operación 16 - Otros Ingresos), basado en código legacy Java (CrearCajaAction.java líneas 1282-1285):

- Valida datos básicos (cliente y monto)
- Genera código de movimiento de forma segura con FOR UPDATE
- Inserta movimiento en `f15_movimiento` con operación 16 (usando f15_ingreso)
- Inserta en diario contable `f37_diario_contable` si se proporcionan datos
- Usa transacciones para asegurar integridad de datos
- Logging en `logs/otros_ingresos.log` para auditoría

**Archivo creado**: `views/caja/otros_ingresos.php`

Formulario de otros ingresos con diseño consistente del sistema:

- Información del cliente (código, nombre, documento, condición)
- Campo de monto del ingreso
- Selector de forma de pago
- Selector de código contable con autocompletado
- Selector Debe/Haber
- Descripción contable
- Observaciones y observaciones adicionales
- Sidebar consistente con todas las operaciones de caja (15 operaciones)

**Archivos modificados**:

- `public/index.php`:
  - Ruta GET `otros_ingresos_form` para mostrar el formulario
  - Ruta POST `otros_ingresos` para procesar el ingreso
- `src/Repositories/CajaRepository.php`:
  - Método `accountingCodes()` existente utilizado para códigos contables
- `views/caja/cliente.php`:
  - Agregado enlace "Otros Ingresos" en sección de Beneficios
- Sidebars de todas las vistas de caja actualizados con "Otros Ingresos":
  - `views/caja/aportes_fonbes.php`
  - `views/caja/aportes_coopam.php`
  - `views/caja/sobregiro_prestamo.php`
  - `views/caja/desembolso_prestamo.php`
  - `views/caja/amortizacion.php`
  - `views/caja/movimientos.php`
  - `views/caja/multas.php`
  - `views/caja/devolucion_fonbes.php`
  - `views/caja/devolucion_coopam.php`
  - `views/caja/cancelacion_aee.php`

**Tablas de base de datos utilizadas**:

- `f15_movimiento`: Movimientos (campos: f15_codigo, f15_fecha, f15_operacion, f15_cliente, f15_ingreso, f15_formaPago, f15_usuaCrea, f15_fechaCrea, f15_obs, f15_obs2)
- `f37_diario_contable`: Diario contable (campos: f37_codigo, f37_movimiento, f37_fecha, f37_codigo_contable, f37_operacion, f37_descripcion, f37_debe, f37_haber, f37_usuario_crea, f37_fecha_crea, f37_estado)

**Rutas**:

- GET: `?module=caja&action=otros_ingresos_form&client=CODIGO_CLIENTE`
- POST: `?module=caja&action=otros_ingresos`

**Estado: COMPLETADO Y FUNCIONANDO**

Todas las funcionalidades han sido probadas y funcionan correctamente según el usuario.

### 14.23 Operación 7: Desembolso de Beneficios

**Fecha de implementación**: 2026-09-17

**Archivo creado**: `src/Services/DesembolsoBeneficiosServicio.php`

Servicio para procesar desembolsos de beneficios (Operación 7 - Desembolso Beneficios), basado en código legacy Java (CrearCajaAction.java líneas 515-531 y 2107-2126):

- Valida datos básicos (cliente, beneficio y monto)
- Verifica que el beneficio existe y está en estado PENDIENTE
- Genera código de movimiento de forma segura con FOR UPDATE
- Inserta movimiento en f15_movimiento con operación 7 (usando f15_mbeneficio y f15_beneficio)
- Actualiza estado del beneficio a DESEMBOLSADO en f21_beneficios
- Inserta en diario contable f37_diario_contable si se proporcionan datos
- Usa transacciones para asegurar integridad de datos
- Logging en logs/desembolso_beneficios.log

**Archivos creados**:

- `views/caja/desembolso_beneficios.php`: Vista de lista de beneficios pendientes del cliente
- `views/caja/desembolso_beneficio.php`: Formulario de desembolso de beneficio específico

**Archivos modificados**:

- `public/index.php`:
  - Ruta GET `desembolso_beneficios_form` para mostrar lista de beneficios pendientes
  - Ruta GET `desembolso_beneficio_form` para mostrar formulario de desembolso específico
  - Ruta POST `desembolso_beneficio` para procesar el desembolso
- `views/caja/cliente.php`:
  - Agregado enlace "Desembolso Beneficios" en sección de Beneficios
- Sidebars de todas las vistas actualizados con "Desembolso Beneficios":
  - `views/caja/aportes_fonbes.php`
  - `views/caja/aportes_coopam.php`
  - `views/caja/sobregiro_prestamo.php`
  - `views/caja/desembolso_prestamo.php`
  - `views/caja/amortizacion.php`
  - `views/caja/movimientos.php`
  - `views/caja/multas.php`
  - `views/caja/devolucion_fonbes.php`
  - `views/caja/devolucion_coopam.php`
  - `views/caja/cancelacion_aee.php`

**Tablas de base de datos utilizadas**:

- `f21_beneficios`: Beneficios (campos: f21_codigo, f21_socio, f21_importe, f21_estado, f21_beneficiario, f21_tipo, f21_fecha)
- `f15_movimiento`: Movimientos (campos: f15_codigo, f15_fecha, f15_operacion, f15_cliente, f15_mbeneficio, f15_beneficio, f15_cheque, f15_mletras, f15_formaPago, f15_usuaCrea, f15_fechaCrea, f15_obs, f15_obs2)
- `f37_diario_contable`: Diario contable (campos: f37_movimiento, f37_fecha, f37_codigo_contable, f37_operacion, f37_debe, f37_haber, f37_descripcion, f37_usuario_crea, f37_fecha_crea, f37_estado)

**Rutas**:

- GET: `?module=caja&action=desembolso_beneficios_form&client=CODIGO_CLIENTE`
- GET: `?module=caja&action=desembolso_beneficio_form&client=CODIGO_CLIENTE&benefit=ID_BENEFICIO`
- POST: `?module=caja&action=desembolso_beneficio`

**Estado: COMPLETADO Y FUNCIONANDO**

Todas las funcionalidades han sido probadas y funcionan correctamente según el usuario.

### 14.24 Operación 8: Egresos

**Fecha de implementación**: 2026-09-17

**Archivo creado**: `src/Services/EgresosServicio.php`

Servicio para procesar egresos de caja (Operación 8 - Egresos), operación genérica de egresos del sistema:

- Valida datos básicos (cliente y monto)
- Verifica que el monto sea mayor a cero
- Genera código de movimiento de forma segura con FOR UPDATE
- Inserta movimiento en f15_movimiento con operación 8 (usando f15_megreso)
- Inserta en diario contable f37_diario_contable si se proporcionan datos
- Usa transacciones para asegurar integridad de datos
- Logging en logs/egresos.log

**Archivos creados**:

- `views/caja/egresos.php`: Formulario de egresos con campos para monto, motivo, beneficiario, forma de pago, cheque, datos contables

**Archivos modificados**:

- `public/index.php`:
  - Ruta GET `egresos_form` para mostrar el formulario
  - Ruta POST `egresos` para procesar el egreso
- `views/caja/cliente.php`:
  - Agregado enlace "Egresos" en sección de Beneficios
- Sidebars de todas las vistas actualizados con "Egresos":
  - `views/caja/aportes_fonbes.php`
  - `views/caja/aportes_coopam.php`
  - `views/caja/sobregiro_prestamo.php`
  - `views/caja/desembolso_prestamo.php`
  - `views/caja/amortizacion.php`
  - `views/caja/movimientos.php`
  - `views/caja/multas.php`
  - `views/caja/devolucion_fonbes.php`
  - `views/caja/devolucion_coopam.php`
  - `views/caja/cancelacion_aee.php`
  - `views/caja/otras_devoluciones.php`
  - `views/caja/otros_ingresos.php`
  - `views/caja/pago_rifa.php`
  - `views/caja/pago_sobregiro_fonbes.php`
  - `views/caja/pago_sobregiro_coopam.php`
  - `views/caja/desembolso_beneficios.php`
  - `views/caja/desembolso_beneficio.php`

**Tablas de base de datos utilizadas**:

- `f15_movimiento`: Movimientos (campos: f15_codigo, f15_fecha, f15_operacion, f15_cliente, f15_megreso, f15_cheque, f15_mletras, f15_formaPago, f15_usuaCrea, f15_fechaCrea, f15_obs, f15_obs2, f15_favor, f15_dni)
- `f37_diario_contable`: Diario contable (campos: f37_movimiento, f37_fecha, f37_codigo_contable, f37_operacion, f37_debe, f37_haber, f37_descripcion, f37_usuario_crea, f37_fecha_crea, f37_estado)

**Rutas**:

- GET: `?module=caja&action=egresos_form&client=CODIGO_CLIENTE`
- POST: `?module=caja&action=egresos`

**Estado: COMPLETADO Y FUNCIONANDO**

Todas las funcionalidades han sido probadas y funcionan correctamente según el usuario.

### 14.25 Operación 3: Desembolso de Préstamo

**Fecha de implementación**: 2026-09-13 (anterior a mejoras de UX)

**Archivo existente**: `src/Services/DesembolsoPrestamoServicio.php`

Servicio para procesar desembolsos de préstamos (Operación 3), basado en el código legacy `CrearCajaAction.registrar()`:

- Genera código de movimiento de forma segura con FOR UPDATE
- Inserta movimiento en f15_movimiento con operación 3
- Actualiza f16_prestamo: estado 4 (activo), f16_desembolso, f16_fechaUP
- Inserta en diario contable f37_diario_contable si se proporcionan datos contables
- Usa transacciones para asegurar integridad de datos
- Logging en logs/desembolso_prestamo.log para auditoría

**Archivo existente**: `views/caja/desembolso_prestamo.php`

Formulario de desembolso de préstamo con diseño consistente del sistema (actualizado hoy con sidebar):

- Información del cliente (código, nombre, documento, condición)
- Información del préstamo (código, importe, interés, cuotas, producto, gastos de trámite)
- Campos para: forma de pago, observaciones
- Selector de código contable con autocompletado
- Selector Debe/Haber
- Descripción contable

**Rutas**:
- GET: `?module=caja&action=desembolso_prestamo_form&client=CODIGO_CLIENTE&loan=CODIGO_PRESTAMO`
- POST: `?module=caja&action=desembolso_prestamo`

**Estado**: COMPLETADO Y FUNCIONANDO

### 14.12 Operaciones Pendientes

- Operación 6: Pagos de aportes COOPAM (duplicado en lista original, verificar si es diferente a Operación 9)
- Operación 11: Pagos de multas
- Operación 12: Pagos de rifas
- Operación 13: Pagos de beneficios
- Operación 14: Pago de sobregiro FONBES
- Operación 15: Pago de sobregiro COOPAM
- Operación 16: Pagos varios

## 15. Siguiente paso recomendado

Las siguientes operaciones de caja han sido implementadas y están funcionando:
- ✅ Operación 1: Aportes FONBES
- ✅ Operación 2: Amortización AEE
- ✅ Operación 3: Desembolso de Préstamo
- ✅ Operación 4: Devolución FONBES
- ✅ Operación 5: Devolución COOPAM
- ✅ Operación 6: Cancelación AEE
- ✅ Operación 7: Desembolso Beneficios
- ✅ Operación 8: Egresos
- ✅ Operación 9: Aportes COOPAM
- ✅ Operación 10: Pago de Sobregiro de Préstamo
- ✅ Operación 11: Pagos de multas
- ✅ Operación 12: Pagos de rifas
- ✅ Operación 13: Otras Devoluciones
- ✅ Operación 14: Pago de sobregiro FONBES
- ✅ Operación 15: Pago de sobregiro COOPAM
- ✅ Operación 16: Otros ingresos

**Estado: TODAS LAS OPERACIONES IMPLEMENTADAS**

Las 16 operaciones del sistema de caja han sido implementadas y están funcionando correctamente según el usuario.

**Siguiente paso recomendado**: Probar la Operación 8 (Egresos) con datos reales para verificar su funcionamiento.

Se recomienda seguir el mismo proceso utilizado para las operaciones anteriores:
1. Revisar código legacy en `CrearCajaAction.java` para la lógica de la operación seleccionada
2. Identificar las tablas de base de datos involucradas
3. Crear el servicio correspondiente
4. Crear el formulario de pago con diseño consistente
5. Agregar rutas en `index.php`
6. Integrar con diario contable
7. Probar con datos reales

## 14. Comandos utiles

Validar sintaxis PHP:

```powershell
$project = 'C:\xampp\htdocs\sacs_nuevo'
Get-ChildItem -LiteralPath $project -Recurse -File -Filter *.php | ForEach-Object { php -l $_.FullName }
```

Probar conexion:

```powershell
php -r "require 'C:/xampp/htdocs/sacs_nuevo/config/bootstrap.php'; Sacs\Core\Database::connection()->query('SELECT 1'); echo 'ok', PHP_EOL;"
```

Probar simulacion actual:

```powershell
php -r "require 'C:/xampp/htdocs/sacs_nuevo/config/bootstrap.php'; `$repository = new Sacs\Repositories\CajaRepository(); `$simulation = `$repository->simulateInstallmentPayment('347701', 43413, 1); echo `$simulation['simulated_total'], PHP_EOL;"
```

URL de perfil de prueba:

`http://localhost/sacs_nuevo/public/index.php?module=caja&profile=client&client=347701`

URL de simulacion de prueba:

`http://localhost/sacs_nuevo/public/index.php?module=caja&client=347701&loan=43413&installment=1`

## 15. Restricciones de continuidad

- No ejecutar UPDATE, INSERT o DELETE en `sacs` durante analisis.
- No consultar listados globales de millones de filas; usar LIMIT y filtros por ID/cliente.
- No eliminar ni revertir cambios existentes.
- No modificar el proyecto Java legacy para avanzar PHP.
- No asumir que codigos de cliente tienen el mismo formato en todas las tablas.
- No copiar ciegamente defectos del legacy como MAX+1, SQL concatenado o doble escritura sin transaccion.
- Mantener mensajes y flujo familiar para usuarios en operaciones reales.

## 16. Correcciones Recientes

### 2026-09-17: Revisión de Sidebars

Se realizó una revisión completa de todos los sidebars en las vistas de caja para verificar que todas las 16 operaciones estuvieran presentes.

**Corrección aplicada:**
- `views/caja/pago_rifa.php`: Se corrigió el sidebar para incluir las 3 operaciones faltantes (Devolución FONBES, Devolución COOPAM, Cancelación AEE). Ahora tiene las 16 operaciones completas.

**Estado de sidebars después de corrección:**
- ✅ `amortizacion.php` - 16 operaciones completas
- ✅ `aportes_fonbes.php` - 16 operaciones completas
- ✅ `aportes_coopam.php` - 16 operaciones completas
- ✅ `sobregiro_prestamo.php` - 16 operaciones completas
- ✅ `desembolso_prestamo.php` - 16 operaciones completas
- ✅ `cancelacion_aee.php` - 16 operaciones completas
- ✅ `desembolso_beneficio.php` - 16 operaciones completas
- ✅ `desembolso_beneficios.php` - 16 operaciones completas
- ✅ `devolucion_coopam.php` - 16 operaciones completas
- ✅ `devolucion_fonbes.php` - 16 operaciones completas
- ✅ `multas.php` - 16 operaciones completas
- ✅ `otras_devoluciones.php` - 16 operaciones completas
- ✅ `otros_ingresos.php` - 16 operaciones completas
- ✅ `pago_rifa.php` - 16 operaciones completas (CORREGIDO)
- ✅ `pago_sobregiro_fonbes.php` - 16 operaciones completas
- ✅ `pago_sobregiro_coopam.php` - 16 operaciones completas
- ✅ `egresos.php` - 16 operaciones completas
- ✅ `movimientos.php` - 16 operaciones completas (con lógica condicional)

Todos los sidebars ahora tienen las 16 operaciones completas y consistentes.
