# Guia de defensa - Primera evaluacion parcial

## 1. Presentacion de 90 segundos

El Modulo 29 organiza QA funcional manual y regresion. Permite crear casos
vinculados a requisitos, agruparlos en campanas por version, registrar resultados
con evidencia privada, crear defectos cuando una ejecucion falla, re-probar una
correccion y consultar una matriz consolidada.

La solucion usa arquitectura por capas. HTTP valida y traduce solicitudes;
Application orquesta; Domain protege invariantes; Infrastructure implementa
autorizacion y persistencia. `QaRegressionRepository` desacopla el caso de uso de
Eloquent. Las operaciones estan protegidas por JWT, tenant y el permiso
`qa.regression.manage`. Los datos QA pertenecen al hospital autenticado aunque el
caso evalue una capacidad CENTRAL.

El cliente Vue consume cinco rutas REST. El servidor controla identidad,
hospital, reglas y transacciones. Se conserva un monolito modular porque las
operaciones comparten infraestructura y una base local; extraer un microservicio
agregaria complejidad distribuida sin beneficio demostrado.

## 2. Preguntas y respuestas

### 1. Que aporta UML al modulo?

Permite representar actores, casos de uso, secuencias y relaciones antes de
implementar. En ASII-28 ayudo a delimitar al Analista QA, Revisor, Responsable del
modulo y HIS bajo prueba.

### 2. Cual es el proceso de diseno aplicado?

Se partio del problema y alcance, luego RF/RNF y criterios, arquitectura de alto
nivel, capas y Repository, y finalmente contrato REST e integracion. Cada etapa
refina la anterior sin inventar capacidades.

### 3. Que estilo arquitectonico se usa?

Cliente-servidor con monolito modular Laravel. La SPA consume JSON; Laravel
centraliza seguridad, reglas y persistencia. Internamente se separan capas.

### 4. Por que usar capas?

Para que HTTP no contenga reglas de negocio y el dominio no dependa de Laravel o
PostgreSQL. Esto mejora prueba, mantenibilidad y claridad de responsabilidades.

### 5. Que es Repository en este caso?

Es un puerto de persistencia orientado al dominio. `ManageQaRegression` solicita
guardar casos, campanas o ejecuciones sin conocer SQL/Eloquent. No es lo mismo que
el repositorio Git.

### 6. Por que existen adaptadores Eloquent e InMemory?

Eloquent persiste en PostgreSQL y controla transacciones. InMemory permite probar
rapidamente el caso de uso. Ambos implementan el mismo contrato.

### 7. Como se aplica REST?

Los recursos tienen URI estables, JSON y estados HTTP. `POST` crea o registra
transiciones; `GET` consulta la matriz. 201 y 200 representan exito; 401, 403,
404, 409 y 422 distinguen fallos.

### 8. Como se protege el tenant?

JWT identifica al usuario, middleware resuelve el tenant, la aplicacion recibe el
hospital desde el contexto y el Repository filtra por `hospital_id`. El cliente no
puede escoger el propietario mediante JSON.

### 9. Por que no se creo un microservicio?

No hay necesidad de despliegue o escalado independiente demostrada. El modulo
comparte identidad, tenant, RBAC y transacciones locales; separarlo exigiria
eventos, idempotencia, observabilidad y manejo de fallos distribuidos.

### 10. Como se demuestra calidad?

Con pruebas de dominio, aplicacion, HTTP y PostgreSQL; matriz de regresion;
defecto y re-prueba documentados; regresion focal de otro modulo; y evidencia sin
datos reales ni secretos.

### 11. Que ocurre cuando una ejecucion falla?

Una sola transaccion crea ejecucion, evidencia y defecto `OPEN`. Si algo falla,
se revierte todo. La re-prueba crea otra ejecucion y cierra o reabre el defecto.

### 12. Que significa `target_scope=CENTRAL`?

Describe la capacidad evaluada. No mueve el registro a CENTRAL ni crea una FK o
transaccion remota; el registro sigue perteneciendo al HOSPITAL.

## 3. Errores conceptuales que deben evitarse

- Decir que Repository es Git.
- Afirmar que la API sube o verifica fisicamente archivos.
- Afirmar que existe UI QA terminada.
- Confundir `target_scope` con propiedad de datos.
- Presentar la matriz como ejecucion automatizada E2E.
- Decir que se implemento microservicio, outbox o idempotencia.
- Omitir JWT, tenant y RBAC al explicar seguridad.

## 4. Recordatorio rapido

```text
5 rutas
4 capas
2 adaptadores Repository
6 tablas QA
3 estados de ejecucion
3 estados de defecto
1 permiso: qa.regression.manage
```
