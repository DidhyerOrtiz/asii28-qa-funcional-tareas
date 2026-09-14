# Modulo 29 - QA funcional, pruebas manuales y matriz de regresion

**Variante:** 29  
**Estudiante:** Didhyer Alexander Ortiz Guevara  
**GitHub:** `DidhyerOrtiz`  
**Antecedente:** ASII-28 - QA funcional, pruebas manuales y matriz de regresion  
**Caracter:** transversal a capacidades CENTRAL/HOSPITAL  
**Propiedad:** registros operativos QA locales al HOSPITAL autenticado

## 1. Problema

El Sistema Hospitalario Integrado necesita demostrar que una version cumple requisitos y criterios de aceptacion y que una correccion resuelve el fallo que la origino. La entrega minima registra casos manuales, los agrupa por campana, conserva resultados y evidencias declaradas, abre defectos reproducibles y registra una nueva ejecucion como re-prueba.

El modulo no ejecuta automaticamente las pruebas ni modifica el sistema evaluado. El Analista QA observa el HIS, redacta los datos sensibles y registra el resultado mediante la API.

## 2. Historia de usuario

> Como Analista QA del hospital, quiero crear casos vinculados a requisitos y criterios de aceptacion, ejecutarlos dentro de una campana y registrar defectos y re-pruebas con evidencia, para consultar una matriz trazable de la version evaluada.

## 3. Actores

| Actor | Participacion real |
|---|---|
| Analista QA | Usa las cinco rutas con el permiso `qa.regression.manage`. |
| Responsable del modulo | Corrige fuera de este modulo y comunica a QA la version que se envia como `fixed_version` durante la re-prueba. |
| Revisor de calidad | Puede consultar la matriz si tambien posee `qa.regression.manage`; no existe un permiso de lectura separado. |
| HIS bajo prueba | Capacidad CENTRAL o HOSPITAL observada manualmente. No es invocada por `ManageQaRegression`. |

## 4. Alcance entregado

### Incluye

- Crear `TestCase` con `target_scope`, `requirement_ref`, `acceptance_criterion_ref`, titulo, precondiciones opcionales, lista de pasos y resultado esperado.
- Crear `RegressionCampaign` con nombre, version objetivo y al menos un caso existente del mismo hospital.
- Registrar `TestExecution` con estado, `actual_result`, ejecutor y exactamente un objeto de evidencia en el contrato HTTP.
- Crear un `Defect` junto con una ejecucion inicial `FAILED` usando `reproduction_steps`, `severity` y `detected_version` planos.
- Re-probar un defecto abierto o reabierto mediante una nueva ejecucion que incluye `fixed_version`.
- Cerrar el defecto con una re-prueba `PASSED` o dejarlo `REOPENED` con una re-prueba `FAILED` o `BLOCKED`.
- Consultar la matriz con cada caso, ultimo estado, ejecuciones y defectos.
- Aislar consultas y escrituras por el `hospital_id` obtenido del tenant autenticado.

### No incluye

- Listar, consultar, editar, versionar o eliminar casos por endpoints independientes.
- Actualizar o eliminar campanas, ni administrar ambiente, estado, criterios de entrada/salida o prioridad.
- Guardar una instantanea del caso en cada ejecucion o una version del caso en `qa_campaign_cases`.
- Un endpoint separado para registrar `fixed_version`.
- Cargar el archivo de evidencia, verificar que exista o recalcular su hash desde contenido fisico.
- Un puerto de almacenamiento de evidencia.
- Automatizacion E2E, CI/CD o correccion del modulo probado.
- Idempotencia HTTP, claves de idempotencia o identificadores de correlacion propios del modulo.
- Outbox/inbox o sincronizacion CENTRAL/HOSPITAL.
- FKs remotas o consulta obligatoria de requisitos en CENTRAL.

## 5. Conceptos que no deben confundirse

| Concepto | Significado |
|---|---|
| Repositorio Git | Guarda archivos, ramas y commits. `target_version`, `detected_version` y `fixed_version` pueden contener una etiqueta provista por el usuario, pero el modulo no consulta Git. |
| Patron Repository | `QaRegressionRepository` es el puerto que usa `ManageQaRegression`; sus adaptadores son `InMemoryQaRegressionRepository` y `EloquentQaRegressionRepository`. |
| Arquitectura federada | Separa nodos CENTRAL/HOSPITAL y su disponibilidad. Los datos QA pertenecen al HOSPITAL aunque un caso tenga `target_scope=CENTRAL`. |

## 6. Modelo entregado

### `TestCase`

| Campo de entidad | Significado |
|---|---|
| `id` | UUID generado por la aplicacion. |
| `hospitalId` | Hospital propietario, derivado del tenant autenticado. |
| `targetScope` | `CENTRAL` o `HOSPITAL`; describe la capacidad probada. |
| `requirementRef` | Referencia logica obligatoria al requisito. |
| `acceptanceCriterionRef` | Referencia logica obligatoria al criterio de aceptacion. |
| `title` | Nombre del caso. |
| `preconditions` | Texto opcional. |
| `steps` | Lista no vacia de cadenas. |
| `expectedResult` | Resultado esperado obligatorio. |
| `createdBy` | Usuario local creador. |

No existen `code`, version, prioridad, datos de prueba estructurados ni instantanea del caso.

### `RegressionCampaign`

Contiene `id`, `hospitalId`, `name`, `targetVersion`, `testCaseIds` y `createdBy`. No contiene ambiente, estado, alcance propio ni criterios de entrada/salida. El alcance se conserva en cada `TestCase` seleccionado.

### `TestExecution` y `Evidence`

`TestExecution` contiene `id`, `hospitalId`, `campaignId`, `testCaseId`, `status`, `actualResult`, `executedBy`, un `Evidence`, y los enlaces opcionales `retestOfExecutionId` y `defectId`.

`Evidence` contiene `id`, `path`, `checksum` y `metadata`. El MVP recibe un solo objeto `evidence` por ejecucion. No existe `executed_version` ni copia del caso en la ejecucion.

### `Defect`

Conserva `id`, `hospitalId`, `initialExecutionId`, `reproductionSteps`, copia de la ruta/checksum/metadatos de la evidencia inicial, `severity`, `status`, `detectedVersion`, `fixedVersion` opcional y `closedAt` opcional.

Estados entregados:

- `OPEN`: creado por la ejecucion inicial `FAILED`.
- `REOPENED`: una re-prueba `FAILED` o `BLOCKED` no verifico la correccion.
- `CLOSED`: una re-prueba `PASSED` verifico la correccion.

## 7. Reglas de negocio

| ID | Regla implementada |
|---|---|
| RN-01 | Cada caso exige `requirement_ref` y `acceptance_criterion_ref` no vacios. Son referencias logicas, no FKs. |
| RN-02 | `target_scope` solo acepta `CENTRAL` o `HOSPITAL`. El registro sigue siendo HOSPITAL-local. |
| RN-03 | Un caso exige titulo, al menos un paso de texto no vacio y resultado esperado. |
| RN-04 | Una campana exige nombre, `target_version` y al menos un `test_case_id`; no admite IDs repetidos. |
| RN-05 | Todos los casos de una campana deben existir dentro del mismo `hospital_id`. |
| RN-06 | Solo se registra una ejecucion para un caso asociado a la campana del hospital autenticado. |
| RN-07 | Los estados de ejecucion son `PASSED`, `FAILED` y `BLOCKED`; todos exigen `actual_result` y evidencia. |
| RN-08 | Una ejecucion inicial `FAILED` exige `reproduction_steps`, `severity` y `detected_version`, y crea un defecto `OPEN` en la misma transaccion. |
| RN-09 | `severity` acepta `LOW`, `MEDIUM`, `HIGH` o `CRITICAL`. |
| RN-10 | Una ejecucion inicial `PASSED` o `BLOCKED` no crea defecto. |
| RN-11 | Toda re-prueba exige `fixed_version`, estado, `actual_result` y nueva evidencia. |
| RN-12 | La re-prueba crea otra ejecucion con `retest_of_execution_id` igual a la ejecucion inicial y `defect_id` igual al defecto. |
| RN-13 | Una re-prueba `PASSED` deja el defecto `CLOSED` y registra `closed_at`; `FAILED` o `BLOCKED` lo deja `REOPENED` y sin cierre. |
| RN-14 | Un defecto `CLOSED` rechaza nuevas re-pruebas con conflicto 409. |
| RN-15 | Las cinco rutas requieren JWT, tenant y permiso `qa.regression.manage`. |
| RN-16 | `hospital_id` se obtiene del tenant resuelto por middleware y no del JSON del cliente. |
| RN-17 | La evidencia exige una ruta que empiece con `private/qa/` y contenga algo despues del prefijo, checksum SHA-256 de 64 hexadecimales y metadatos con `redacted=true`, `mime` no vacio y `size` entero mayor o igual que cero. |
| RN-18 | La validacion recursiva rechaza exactamente estas claves de metadatos sin distinguir mayusculas: `password`, `token`, `authorization`, `cookie` y `secret`. |
| RN-19 | La validacion de evidencia no demuestra que el objeto exista ni que su contenido produzca el checksum declarado. |
| RN-20 | Una caida de CENTRAL no impide persistir registros QA locales; la disponibilidad del sistema probado se describe en `actual_result` y el estado elegido por QA. |

## 8. Criterios de aceptacion

| ID | Criterio verificable |
|---|---|
| CA-01 | Crear un caso sin `requirement_ref` o `acceptance_criterion_ref` responde 422. |
| CA-02 | Crear un caso con `target_scope` diferente de `CENTRAL`/`HOSPITAL`, pasos vacios o resultado esperado vacio responde 422. |
| CA-03 | Un caso valido se crea con 201 y queda asociado al hospital y usuario autenticados. |
| CA-04 | Crear una campana sin casos, con IDs repetidos o con un caso de otro hospital falla sin crear una campana parcial. |
| CA-05 | Una ejecucion valida para un caso no asociado a la campana responde 404. |
| CA-06 | `PASSED` y `BLOCKED` crean ejecucion y evidencia, pero no defecto. |
| CA-07 | `FAILED` sin los tres campos planos de defecto responde 422. |
| CA-08 | `FAILED` valido crea atomicamente ejecucion, evidencia y defecto `OPEN` con la evidencia inicial preservada. |
| CA-09 | Una ruta de evidencia fuera de `private/qa`, checksum no SHA-256, `redacted` distinto de `true`, ausencia de `mime`/`size` o metadatos con claves sensibles responde 422. |
| CA-10 | La re-prueba exige `fixed_version`; crea una ejecucion nueva y no sobrescribe la inicial. |
| CA-11 | Re-prueba `PASSED` actualiza el defecto a `CLOSED`; `FAILED` o `BLOCKED` lo actualiza a `REOPENED`. |
| CA-12 | Re-probar un defecto `CLOSED` responde 409 y no crea ejecucion. |
| CA-13 | La matriz muestra por caso `latest_status`, todas sus ejecuciones con evidencia y sus defectos; un caso no ejecutado tiene `latest_status=null`. |
| CA-14 | Un usuario sin `qa.regression.manage` no accede ni siquiera a la matriz. |
| CA-15 | Un identificador de otro hospital no devuelve ni modifica datos de ese hospital. |
| CA-16 | `target_scope=CENTRAL` no crea FK ni dependencia transaccional hacia CENTRAL. |

## 9. API exacta

Las cinco rutas se publican bajo `/api/v1/qa` y comparten middleware `tenant`, `jwt.auth` y `permission:qa.regression.manage`. El cliente envia `Authorization: Bearer <jwt>` y el encabezado de tenant usado por el proyecto. `hospital_id` y `created_by`/`executed_by` no forman parte de los payloads.

| Metodo | Ruta | Respuesta exitosa |
|---|---|---:|
| POST | `/api/v1/qa/test-cases` | 201 |
| POST | `/api/v1/qa/campaigns` | 201 |
| POST | `/api/v1/qa/campaigns/{campaign}/executions` | 201 |
| POST | `/api/v1/qa/defects/{defect}/retests` | 201 |
| GET | `/api/v1/qa/campaigns/{campaign}/matrix` | 200 |

No se entregaron otras rutas del modulo.

### 9.1 Crear caso

```json
{
  "target_scope": "HOSPITAL",
  "requirement_ref": "MOD-02/RF-07",
  "acceptance_criterion_ref": "MOD-02/CA-03",
  "title": "Rechazar operacion sin permiso",
  "preconditions": "Usuario ficticio autenticado sin el permiso requerido",
  "steps": [
    "Autenticar al usuario ficticio",
    "Invocar la operacion protegida",
    "Observar la respuesta"
  ],
  "expected_result": "La API responde 403 y no modifica datos"
}
```

Respuesta 201: `{ "test_case": { ... } }`.

### 9.2 Crear campana

```json
{
  "name": "Regresion permisos 2026-08-21",
  "target_version": "release-2026.08.21",
  "test_case_ids": [
    "018f1f8a-7c4e-7000-8000-000000000001"
  ]
}
```

Respuesta 201: `{ "campaign": { ... } }`.

### 9.3 Registrar ejecucion aprobada o bloqueada

```json
{
  "test_case_id": "018f1f8a-7c4e-7000-8000-000000000001",
  "status": "PASSED",
  "actual_result": "La API respondio 403 y no modifico datos",
  "evidence": {
    "path": "private/qa/campaign-01/execution-01.json",
    "checksum": "0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef",
    "metadata": {
      "redacted": true,
      "mime": "application/json",
      "size": 824
    }
  }
}
```

`BLOCKED` usa el mismo esquema y explica el bloqueo en `actual_result`.

### 9.4 Registrar ejecucion fallida

Los datos del defecto son campos planos del mismo payload, no un objeto `defect`.

```json
{
  "test_case_id": "018f1f8a-7c4e-7000-8000-000000000001",
  "status": "FAILED",
  "actual_result": "La API respondio 200 y modifico datos",
  "evidence": {
    "path": "private/qa/campaign-01/execution-02.json",
    "checksum": "abcdef0123456789abcdef0123456789abcdef0123456789abcdef0123456789",
    "metadata": {
      "redacted": true,
      "mime": "application/json",
      "size": 961
    }
  },
  "reproduction_steps": "1. Autenticar sin permiso. 2. Invocar la operacion. 3. Observar respuesta 200.",
  "severity": "HIGH",
  "detected_version": "release-2026.08.21"
}
```

Respuesta 201:

```json
{
  "execution": {"status": "FAILED", "defect_id": "<uuid>", "evidence": {"...": "..."}},
  "defect": {"status": "OPEN", "detected_version": "release-2026.08.21", "fixed_version": null}
}
```

### 9.5 Re-probar defecto

`fixed_version` se registra en esta operacion; no existe un endpoint previo para informarla.

```json
{
  "fixed_version": "release-2026.08.22",
  "status": "PASSED",
  "actual_result": "La API respondio 403 y no modifico datos",
  "evidence": {
    "path": "private/qa/campaign-01/retest-01.json",
    "checksum": "1111111111111111111111111111111111111111111111111111111111111111",
    "metadata": {
      "redacted": true,
      "mime": "application/json",
      "size": 790
    }
  }
}
```

Respuesta 201 contiene `execution` y `defect`. El defecto queda `CLOSED` para `PASSED`, y `REOPENED` para `FAILED` o `BLOCKED`.

### 9.6 Consultar matriz

Respuesta 200:

```json
{
  "campaign": {
    "id": "<uuid>",
    "hospital_id": "hospital-01",
    "name": "Regresion permisos 2026-08-21",
    "target_version": "release-2026.08.21",
    "test_case_ids": ["<uuid>"],
    "created_by": 10
  },
  "rows": [
    {
      "test_case": {"id": "<uuid>", "requirement_ref": "MOD-02/RF-07"},
      "latest_status": "PASSED",
      "executions": [{"id": "<uuid>", "status": "FAILED"}, {"id": "<uuid>", "status": "PASSED"}],
      "defects": [{"id": "<uuid>", "status": "CLOSED", "fixed_version": "release-2026.08.22"}]
    }
  ]
}
```

### 9.7 Errores observables

| HTTP | Caso |
|---:|---|
| 401 | JWT ausente o invalido, segun middleware. |
| 403 | Falta el permiso o falla la autorizacion del hospital. |
| 404 | Campana, caso, ejecucion original o defecto no encontrado dentro del hospital. |
| 409 | Duplicado/conflicto de persistencia, enlaces inconsistentes o defecto cerrado. |
| 422 | Validacion HTTP o de dominio. |

Los errores de dominio traducidos por el controller contienen `message`. No se documenta una envoltura con `code`, `errors` o `correlation_id` propia del modulo.

## 10. Evidencia y privacidad

- `path` debe empezar con `private/qa/` y no puede ser solamente ese prefijo; no es una URL publica ni contenido del archivo.
- `checksum` debe tener sintaxis SHA-256: 64 caracteres hexadecimales. Se normaliza a minusculas al serializar/persistir.
- `metadata.redacted` debe ser exactamente `true`.
- `metadata.mime` y `metadata.size` son obligatorios para describir tipo y tamano del artefacto.
- Se buscan de forma recursiva y se rechazan las claves `password`, `token`, `authorization`, `cookie` y `secret`, sin distinguir mayusculas.
- QA debe redactar nombres, identificadores clinicos y cualquier dato personal antes de registrar la referencia.
- El MVP persiste la declaracion de ruta, checksum y metadatos. No abre el archivo, no comprueba su existencia y no recalcula el checksum.
- El defecto duplica ruta, checksum y metadatos de la evidencia inicial para conservar su contexto aun cuando la consulta se construya desde `qa_defects`.

## 11. Persistencia PostgreSQL real

La migracion crea exactamente seis tablas `qa_*`. Usa columnas `json`, no `jsonb`, y no declara `CHECK` de estados, alcance, checksum o estructura JSON. Esas validaciones corresponden a requests, enums y entidades.

### 11.1 Tablas

| Tabla | Columnas principales entregadas |
|---|---|
| `qa_test_cases` | UUID `id`; `hospital_id`; `target_scope`; `requirement_ref`; `acceptance_criterion_ref`; `title`; `preconditions` nullable; `steps` JSON; `expected_result`; `created_by`; timestamps. |
| `qa_regression_campaigns` | UUID `id`; `hospital_id`; `name`; `target_version`; `created_by`; timestamps. |
| `qa_campaign_cases` | `hospital_id`; UUID `campaign_id`; UUID `test_case_id`; timestamps; PK compuesta `(hospital_id, campaign_id, test_case_id)`. |
| `qa_test_executions` | UUID `id`; `hospital_id`; UUID `campaign_id`; UUID `test_case_id`; `status`; `actual_result`; `executed_by`; `retest_of_execution_id` nullable; `defect_id` nullable; timestamps. |
| `qa_evidences` | UUID `id`; `hospital_id`; UUID `execution_id`; `path`; `checksum`; `metadata` JSON; timestamps. |
| `qa_defects` | UUID `id`; `hospital_id`; UUID `initial_execution_id`; `reproduction_steps`; evidencia inicial duplicada; `severity`; `status`; `detected_version`; `fixed_version` nullable; `closed_at` nullable; timestamps. |

### 11.2 FKs y borrado

- Todos los `hospital_id` referencian la tabla compartida local `tenants(id)` con borrado en cascada.
- `created_by` y `executed_by` referencian la tabla compartida local `users(id)` con borrado restringido.
- `qa_campaign_cases (hospital_id, campaign_id)` referencia campanas con cascada.
- `qa_campaign_cases (hospital_id, test_case_id)` referencia casos con restriccion.
- `qa_test_executions (hospital_id, campaign_id, test_case_id)` referencia la membresia exacta de campana y caso con restriccion.
- `qa_test_executions (hospital_id, retest_of_execution_id)` es una FK local autorreferente con restriccion.
- `qa_evidences (hospital_id, execution_id)` referencia ejecuciones con cascada.
- `qa_defects.initial_execution_id` es un enlace logico unico por hospital; no declara FK para evitar un ciclo de insercion con la ejecucion inicial.
- `qa_test_executions (hospital_id, defect_id)` referencia localmente `qa_defects (hospital_id, id)` con restriccion.
- No existen FKs a CENTRAL. Las referencias de requisito/criterio y las versiones son cadenas logicas.

### 11.3 Unicidad e indices

- Unico `qa_test_cases (hospital_id, target_scope, requirement_ref, acceptance_criterion_ref, title)`.
- Indice `qa_test_cases (hospital_id, requirement_ref)`.
- Unico `qa_regression_campaigns (hospital_id, name, target_version)`.
- PK `qa_campaign_cases (hospital_id, campaign_id, test_case_id)`; su prefijo tambien respalda consultas por hospital y campana.
- Indices de ejecucion `(hospital_id, campaign_id, test_case_id)`, `(hospital_id, defect_id)` y `(hospital_id, retest_of_execution_id)`.
- Unico `qa_evidences (hospital_id, execution_id)`, que refuerza una evidencia por ejecucion.
- Unico `qa_defects (hospital_id, initial_execution_id)` e indice `(hospital_id, status, severity)`.

No se documentan indices GIN ni restricciones JSONB porque no fueron entregados.

## 12. Limites transaccionales y fallos parciales

| Operacion | Comportamiento entregado |
|---|---|
| Crear caso | Un `INSERT`; no usa una transaccion explicita propia. |
| Crear campana | Una transaccion crea la campana y todas las filas `qa_campaign_cases`; un error revierte el conjunto. |
| Registrar `PASSED`/`BLOCKED` | Una transaccion crea ejecucion y evidencia. |
| Registrar `FAILED` | La misma transaccion crea ejecucion, evidencia y defecto `OPEN`. |
| Re-probar | Una transaccion bloquea el defecto con `lockForUpdate`, crea ejecucion/evidencia y actualiza `status`, `fixed_version` y `closed_at`. |
| Consultar matriz | Lecturas filtradas por hospital; no se declara una instantanea transaccional de toda la consulta. |

- El adaptador valida coherencia entre IDs de ejecucion y defecto antes de persistir.
- Si falla una escritura dentro de una transaccion, PostgreSQL revierte las filas de esa operacion.
- Una re-prueba concurrente encuentra el defecto bloqueado; si ya esta cerrado, se rechaza sin crear una nueva ejecucion.
- No se promete una respuesta HTTP especial para errores de infraestructura no traducidos por el controller.
- No existe coordinacion distribuida con CENTRAL. Una caida de CENTRAL no revierte ni bloquea una escritura QA local.
- No se implemento idempotencia; un cliente no debe asumir que repetir una solicitud despues de un timeout evita duplicados mas alla de las restricciones unicas reales.

## 13. Trazabilidad disponible

| Eslabon | Persistencia |
|---|---|
| Requisito y criterio | `qa_test_cases.requirement_ref` y `acceptance_criterion_ref`. |
| Caso incluido | `qa_campaign_cases` vincula caso y campana. |
| Version objetivo | `qa_regression_campaigns.target_version`. |
| Resultado observado | `qa_test_executions.status` y `actual_result`. |
| Evidencia | `qa_evidences.path`, `checksum` y `metadata`. |
| Fallo inicial | `qa_defects.initial_execution_id`, reproduccion, severidad, evidencia duplicada y `detected_version`. |
| Correccion y re-prueba | `qa_defects.fixed_version`; nueva ejecucion con `retest_of_execution_id` y `defect_id`. |
| Resultado consolidado | `GET .../matrix` devuelve caso, `latest_status`, ejecuciones y defectos. |

Limitacion deliberada: como no hay versionado ni instantaneas de caso, la trazabilidad se apoya en el registro actual de `qa_test_cases`. La API entregada no ofrece una ruta para editar ese registro.

## 14. Federacion e integracion futura

- Los seis registros QA son HOSPITAL-locales y siempre se filtran por `hospital_id`.
- `target_scope` solo describe si el caso prueba una capacidad CENTRAL o HOSPITAL.
- CENTRAL no participa en las transacciones QA y no se usa para resolver referencias.
- Outbox/inbox no esta implementado. Una eventual consolidacion asincrona de indicadores requeriria otra decision y codigo adicional; no forma parte de este MVP.
