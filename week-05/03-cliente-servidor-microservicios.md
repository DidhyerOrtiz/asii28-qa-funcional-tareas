# Cliente-servidor, REST y decision de despliegue

## 1. Modelo cliente-servidor

El cliente es la SPA Vue del HIS. Presenta formularios y matrices, conserva el
token segun las convenciones del proyecto y envia solicitudes HTTPS/JSON. El
servidor es Laravel: autentica, resuelve tenant, autoriza, valida, ejecuta reglas y
persiste.

| Cliente | Servidor |
|---|---|
| Captura datos y presenta estados. | Es autoridad sobre identidad, tenant y reglas. |
| Envia JWT y cabecera tenant. | Rechaza token, tenant o permiso invalidos. |
| Envia referencias de evidencia. | Valida ruta/checksum/metadatos y persiste. |
| No construye `hospital_id`. | Deriva `hospital_id` del contexto autenticado. |
| No accede a base o filesystem. | Usa Repository, PostgreSQL y almacenamiento privado. |

## 2. Uso de REST

- Los URI representan casos, campanas, ejecuciones, defectos y matriz.
- `POST` crea recursos o registra transiciones con efectos controlados.
- `GET` consulta la representacion consolidada sin modificar estado.
- Los estados HTTP distinguen exito, autenticacion, autorizacion, ausencia,
  conflicto y validacion.
- Cada solicitud contiene el contexto necesario; no se depende de sesion de
  servidor propia del modulo.

## 3. Microservicio o monolito modular

La semana 5 estudia microservicios, pero no obliga a separar cada modulo. Para el
MVP se conserva el Modulo 29 dentro del monolito modular Laravel.

### Razones

1. Las transacciones de ejecucion, evidencia y defecto usan una sola base local.
2. JWT, tenant, RBAC, usuarios y migraciones ya viven en el HIS.
3. Separar ahora exigiria identidad distribuida, contratos de eventos,
   observabilidad, reintentos, idempotencia y operacion adicional.
4. El alcance academico se beneficia mas de limites claros por capas que de una
   separacion fisica prematura.

### Condiciones para extraerlo en el futuro

- Carga o ciclo de despliegue independiente demostrado.
- Propiedad de datos y equipo claramente separada.
- Contrato estable para identidad, tenant y eventos.
- Outbox/inbox, idempotencia, trazas y recuperacion ante fallos definidos.
- Beneficio medible mayor que el costo operativo.

## 4. Integracion federada

`target_scope=CENTRAL` describe la capacidad evaluada; no cambia la propiedad del
registro QA. El dato se conserva en HOSPITAL y no existe transaccion distribuida.
Una futura consolidacion de indicadores en CENTRAL debe ser asincrona y contar con
su propio contrato de privacidad y reintentos.

## 5. Responsabilidades por capa

| Capa | Responsabilidad |
|---|---|
| Presentacion | Rutas, requests, controller y JSON HTTP. |
| Aplicacion | Orquestacion en `ManageQaRegression`. |
| Dominio | Entidades, estados, invariantes y excepciones. |
| Infraestructura | RBAC, Repository Eloquent/InMemory y PostgreSQL. |

El patron Repository no es el repositorio Git ni un microservicio. Es la frontera
de persistencia que permite al caso de uso trabajar sin conocer Eloquent.
