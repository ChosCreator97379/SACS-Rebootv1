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

### 14.8 Operaciones Pendientes

- Operación 6: Pagos de aportes COOPAM
- Operación 9: Pagos de aportes COOPAM
- Operación 10: Pago de sobregiro de préstamo
- Operación 11: Pagos de multas
- Operación 12: Pagos de rifas
- Operación 13: Pagos de beneficios
- Operación 14: Pago de sobregiro FONBES
- Operación 15: Pago de sobregiro COOPAM
- Operación 16: Pagos varios

## 15. Siguiente paso recomendado

Los pasos 1-6 del analisis han sido completados. Para implementar el pago real se recomienda:

1. Resolver el caso pendiente con sobregiro grande (analisis adicional)
2. Crear interfaz de usuario para capturar datos del formulario
3. Integrar AmortizacionServicio en el flujo de caja
4. Pruebas exhaustivas con datos de produccion
5. Validacion final con usuarios antes de habilitar en produccion

**Nota**: La Operación 1 (Pagos de aportes FONBES) ya ha sido implementada y probada. Se recomienda continuar con las operaciones pendientes en orden.

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
