# Plan de integracion tecnica

## 1. Objetivo

Integrar el vertical QA en el HIS sin romper autenticacion, aislamiento tenant ni
modulos existentes, y conservar evidencia revisable en issue, rama, worktree y PR.

## 2. Trazabilidad GitHub

| Elemento | Referencia o plan |
|---|---|
| Issue | `ASII-28`, issue colaborativo `#28`; permanece abierto. |
| Base | `origin/develop`. |
| Worktree | Directorio separado del clon principal para no mezclar modulos. |
| Rama implementada | `feat/mod29-qa-regresion`. |
| Pull request | `#63`, abierto hacia `develop` y sin auto-fusion. |
| Repositorio personal | Evidencia academica separada del codigo completo del HIS. |

No se debe fusionar el PR propio. La revision y la decision de integracion
corresponden al docente o mantenedor.

## 3. Dependencias de integracion

| Dependencia | Contrato de entrada | Resultado esperado |
|---|---|---|
| JWT | `Authorization: Bearer <jwt>` | Usuario autenticado o 401. |
| Tenant | Cabecera tenant del proyecto | Hospital resuelto y coincidente o rechazo. |
| RBAC | `qa.regression.manage` | Acceso a las cinco rutas o 403. |
| PostgreSQL local HOSPITAL | Seis tablas `qa_*` | Escrituras y consultas aisladas por `hospital_id`. |
| Usuarios y tenants | FKs locales compartidas | Autor y hospital validos. |
| Filesystem privado | Referencia `private/qa/...` | Metadatos persistidos; verificacion fisica fuera del MVP. |
| Modulo bajo prueba | Resultado observado manualmente | No existe llamada transaccional desde QA. |
| CENTRAL | Referencias logicas | Una caida CENTRAL no bloquea el registro QA local. |

## 4. Secuencia de integracion

1. Confirmar que la rama parte de `origin/develop` y que el worktree esta limpio.
2. Verificar rutas con `php artisan route:list --path=api/v1/qa`.
3. Ejecutar pruebas unitarias, aplicacion y HTTP sin PostgreSQL.
4. Levantar PostgreSQL 17 aislado y ejecutar las pruebas focales de persistencia.
5. Ejecutar `migrate:fresh --seed --force` solo contra una base terminada en `_test`.
6. Verificar la regresion focal del Modulo 20.
7. Revisar `git diff --check` y formato focal.
8. Actualizar issue y PR con resultados reales, sin credenciales.
9. Solicitar revision; no auto-fusionar.
10. Tras aprobacion, validar nuevamente sobre el `develop` resultante.

## 5. Estrategia cliente-servidor

La futura vista Vue consume JSON mediante Axios. El cliente aporta JWT, tenant,
identificadores y datos funcionales, pero nunca decide el hospital propietario ni
el usuario ejecutor. Laravel aplica middleware, valida el payload y delega en
`ManageQaRegression`. El caso de uso depende de `QaRegressionRepository`; el
adaptador Eloquent encapsula transacciones PostgreSQL.

El navegador no accede directamente a PostgreSQL ni al filesystem privado.

## 6. Pruebas de integracion requeridas

| Escenario | Resultado esperado |
|---|---|
| Crear caso y campana en hospital A | Registros asociados solo a A. |
| Usar caso de hospital A desde B | 404/403 sin revelar datos. |
| Registrar fallo valido | Ejecucion, evidencia y defecto se confirman atomicamente. |
| Forzar membresia invalida | PostgreSQL revierte la operacion. |
| Re-probar y aprobar | Nueva ejecucion y defecto `CLOSED`. |
| Insertar segunda evidencia | Restriccion unica rechaza la duplicacion. |
| Consultar matriz | Ultimo estado, ejecuciones y defectos coherentes. |
| Ejecutar regresion Modulo 20 | Sin regresiones observables. |

## 7. Riesgos y mitigaciones

| Riesgo | Mitigacion |
|---|---|
| Confundir `target_scope=CENTRAL` con propiedad CENTRAL | Mantener todos los registros QA HOSPITAL-locales. |
| Filtrar evidencia sensible | Ruta privada, `redacted=true`, lista de claves prohibidas y revision humana. |
| Re-pruebas concurrentes | `lockForUpdate` y conflicto para defecto cerrado. |
| Duplicados por reintento HTTP | Reconocer que no hay idempotencia y usar restricciones reales. |
| PR extenso o dificil de revisar | Commits tematicos, evidencia focal y checklist verificable. |
| Dependencia de herramienta de defectos | Conservar defectos internos; una integracion externa requiere otro contrato. |

## 8. Criterio de salida

La integracion esta lista para revision cuando las cinco rutas estan registradas,
las pruebas reportadas son reproducibles, no hay secretos, el diagrama coincide
con el codigo y el PR apunta a `develop` sin auto-fusion.
