# ASII-28 - Semana 4: Arquitectura Repository

Documentacion de la entrega final de la variante 29, desarrollada por **Didhyer Alexander Ortiz Guevara** (`DidhyerOrtiz`) a partir del antecedente historico **ASII-28: QA funcional, pruebas manuales y matriz de regresion**. La implementacion completa se revisa en el [PR colaborativo #63](https://github.com/compilations-teams/sistema-hospitalario-integrado-SistenasII-2026/pull/63).

El vertical entregado permite:

1. Crear un caso manual con referencias obligatorias a requisito y criterio de aceptacion.
2. Crear una campana para una version y asociarle casos del mismo hospital.
3. Registrar una ejecucion con un resultado y una evidencia.
4. Crear atomicamente un defecto `OPEN` cuando la ejecucion inicial es `FAILED`.
5. Re-probar un defecto indicando `fixed_version`; la re-prueba lo deja `CLOSED` si aprueba o `REOPENED` si falla o queda bloqueada.
6. Consultar la matriz de una campana.

## Contenido

| Archivo | Proposito |
|---|---|
| [ESPECIFICACION.md](ESPECIFICACION.md) | Contrato funcional, API, persistencia, privacidad y trazabilidad reales. |
| [ADR-001-arquitectura.md](ADR-001-arquitectura.md) | Decision arquitectonica implementada. |
| [casos-de-uso.mmd](casos-de-uso.mmd) | Casos de uso expuestos por las cinco rutas. |
| [diagrama-clases.mmd](diagrama-clases.mmd) | Clases de dominio, `ManageQaRegression` y adaptadores Repository. |
| [secuencia-ejecucion.mmd](secuencia-ejecucion.mmd) | Registro de ejecucion y creacion atomica del defecto si falla. |
| [secuencia-defecto-reprueba.mmd](secuencia-defecto-reprueba.mmd) | Re-prueba con `fixed_version` y cierre o reapertura. |
| [componentes.mmd](componentes.mmd) | Componentes implementados y limites federados. |
| [vista-datos.mmd](vista-datos.mmd) | Seis tablas `qa_*` y sus relaciones locales reales. |
| [MATRIZ_REGRESION.md](MATRIZ_REGRESION.md) | Ocho escenarios ejecutados, defecto y re-prueba documentados. |
| [EVIDENCIA.md](EVIDENCIA.md) | Entorno, comandos, resultados y trazabilidad Git reproducibles. |
| [DECLARACION_IA.md](DECLARACION_IA.md) | Uso, alcance, errores y validación humana de la asistencia con IA. |
| [informe-tarea-repository.md](informe-tarea-repository.md) | Fuente editable del informe formal de la Tarea 4. |
| [informe-tarea-repository.docx](informe-tarea-repository.docx) | Informe final editable con siete imagenes incrustadas. |
| [informe-tarea-repository.pdf](informe-tarea-repository.pdf) | Informe final revisado de 17 paginas. |

## Contrato entregado

- Caso de uso de aplicacion: `ManageQaRegression`.
- Puerto: `QaRegressionRepository`.
- Adaptadores: `InMemoryQaRegressionRepository` y `EloquentQaRegressionRepository`.
- Permiso de las cinco rutas: `qa.regression.manage`.
- Prefijo HTTP: `/api/v1/qa`.
- Persistencia: seis tablas `qa_*` en la base HOSPITAL, con FKs locales a tablas QA y a las tablas compartidas locales `users` y `tenants`.
- Alcance probado: `target_scope` del caso acepta `CENTRAL` o `HOSPITAL`; no cambia la propiedad HOSPITAL-local del registro.

## Limites

- El repositorio Git conserva codigo e historial; no es `QaRegressionRepository`.
- El patron Repository abstrae persistencia; no representa la federacion CENTRAL/HOSPITAL.
- La arquitectura federada define propiedad y disponibilidad de nodos; no implica FKs remotas.
- La entrega no versiona casos, no guarda instantaneas de casos y no gestiona estados de campana.
- La API no carga ni verifica fisicamente archivos de evidencia. Valida y persiste su ruta privada, checksum declarado y metadatos saneados.
- No se implementan outbox/inbox. Esa integracion queda como posibilidad futura.

## Diagramas

Los seis archivos `.mmd` son fuentes Mermaid editables y superaron validación de sintaxis. Los PNG se renderizan manualmente con estos nombres en la misma carpeta:

| Fuente | Salida esperada |
|---|---|
| `casos-de-uso.mmd` | `casos-de-uso.png` |
| `diagrama-clases.mmd` | `diagrama-clases.png` |
| `secuencia-ejecucion.mmd` | `secuencia-ejecucion.png` |
| `secuencia-defecto-reprueba.mmd` | `secuencia-defecto-reprueba.png` |
| `componentes.mmd` | `componentes.png` |
| `vista-datos.mmd` | `vista-datos.png` |

Estado actual: fuentes corregidas y renderizables; los seis PNG finales fueron incorporados y revisados visualmente.
