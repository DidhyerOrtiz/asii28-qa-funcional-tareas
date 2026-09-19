# Componentes y refactorizacion

## 1. Componentes frontend

| Componente | Responsabilidad | Dependencia permitida |
|---|---|---|
| `QaShell` | Navegacion, contexto visual y region principal. | Router y sesion. |
| `QaOverviewView` | Resumen de cobertura, pendientes y defectos. | Store de campanas. |
| `CampaignListView` | Filtros y listado de campanas. | Store y servicio QA. |
| `ExecutionWorkspace` | Caso actual, pasos, resultado y evidencia. | Store de ejecucion. |
| `DefectQueueView` | Defectos abiertos/reabiertos y re-prueba. | Store de defectos. |
| `QaApiClient` | Cinco operaciones HTTP y traduccion de errores. | Axios configurado. |
| Stores Pinia | Estado de pantalla y coordinacion asincrona. | `QaApiClient`. |

Las vistas no construyen cabeceras JWT/tenant ni conocen rutas SQL. El cliente
Axios existente aplica el contexto de transporte.

## 2. Componentes backend

| Componente | Responsabilidad |
|---|---|
| Middleware JWT/tenant/RBAC | Identidad, hospital y permiso. |
| `QaRegressionController` | Traducir HTTP a comandos y respuestas. |
| Cuatro `FormRequest` | Validar forma, campos condicionales y evidencia. |
| `ManageQaRegression` | Orquestar los cinco casos de uso. |
| Entidades de dominio | Invariantes de caso, evidencia, ejecucion y defecto. |
| `QaRegressionRepository` | Puerto de persistencia orientado al dominio. |
| Adaptadores Eloquent/InMemory | PostgreSQL real y pruebas rapidas. |

## 3. Problema antes del refactor

Una vista unica que cargue campanas, registre ejecuciones, transforme errores y
controle defectos concentra demasiadas razones de cambio. Tambien obliga a repetir
estados `loading/error`, rutas y normalizacion de respuestas.

## 4. Mejora propuesta

Separar `QaWorkspaceView` en vistas por tarea, stores por estado y un
`QaApiClient` unico. El backend permanece detras del contrato REST ya definido.

```text
Antes: Vista -> Axios -> cinco rutas + estados + mensajes + transformacion

Despues: Vista -> Store -> QaApiClient -> Axios configurado -> API Laravel
```

## 5. Beneficios medibles

- Una sola definicion de rutas y errores HTTP.
- Componentes de UI probables con clientes simulados.
- Estados de carga y reintento consistentes.
- Menos conocimiento de transporte dentro de componentes visuales.
- Cambio de presentacion sin alterar dominio o persistencia.

## 6. Limites

El refactor es una propuesta de diseno para el frontend futuro. No se presenta
como una SPA integrada. El prototipo de semana 11 usa datos ficticios y no llama a
la API real.
