# ASII-28 - Semana 5

**Estudiante:** Didhyer Alexander Ortiz Guevara (`DidhyerOrtiz`)<br>
**Modulo:** QA funcional, pruebas manuales y matriz de regresion<br>
**Entrega:** Contrato API preliminar y plan de integracion

## Proposito

Esta entrega formaliza el limite cliente-servidor del Modulo 29. Documenta las
cinco operaciones REST disponibles, sus payloads, respuestas, errores, permiso,
aislamiento por hospital y plan de integracion con el HIS.

El contrato se contrasto con las rutas, controladores, `FormRequest`, caso de uso,
Repository y pruebas de la rama colaborativa `feat/mod29-qa-regresion`. Por ello,
se distingue entre capacidades implementadas y posibilidades futuras.

## Contenido

1. [Contrato API REST](01-contrato-api.md)
2. [Plan de integracion](02-plan-integracion.md)
3. [Cliente-servidor y decision sobre microservicios](03-cliente-servidor-microservicios.md)
4. [Fuente Mermaid del diagrama de integracion](diagramas/01-cliente-servidor-integracion.mmd)
5. [Evidencia de revision](EVIDENCIA.md)
6. [Texto preparado para Project Odoo](ODOO_ACTUALIZACION.md)
7. [Declaracion de uso de IA](DECLARACION_IA.md)

## Cumplimiento

| Requisito de semana 5 | Evidencia |
|---|---|
| Endpoints | Tabla y detalle de cinco rutas en `01-contrato-api.md`. |
| Payloads y respuestas | Ejemplos JSON y campos obligatorios por operacion. |
| Errores | Matriz HTTP 401, 403, 404, 409 y 422. |
| Permisos | JWT, tenant y `qa.regression.manage`. |
| Cliente-servidor | Flujo SPA/API y fuente Mermaid. |
| Microservicios | Decision explicita de conservar monolito modular. |
| Plan de integracion | Issue, rama, worktree, PR, validacion y riesgos. |
| Actualizacion Odoo | Texto listo para pegar en `ODOO_ACTUALIZACION.md`. |

## Estado del diagrama

La fuente editable esta terminada. El PNG debe exportarse manualmente desde
Mermaid con este nombre:

```text
week-05/diagramas/imagenes/cliente-servidor-integracion.png
```

No se presenta un PNG generado automaticamente como evidencia final.

## Referencias

- Fielding, R. T. *Architectural Styles and the Design of Network-based Software Architectures*.
- OpenAPI Initiative. *OpenAPI Specification 3.1*.
- Laravel. *Routing, Validation and Middleware*.
- Proyecto ASII. `docs/weekly-plan.md`.
